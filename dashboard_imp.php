<?php
require_once 'daoLibrary/ImpDAO.php';
require_once 'daoLibrary/H_trocaDAO.php';
require_once 'daoLibrary/LeituraDAO.php';

$mensagem = "";
$id_impressora = $_GET['id'] ?? null;

if (!$id_impressora) die("ID da impressora não fornecido.");

$impDAO = new ImpDAO();
$trocaDAO = new H_trocaDAO();
$leituraDAO = new LeituraDAO();

$impressora = $impDAO->buscarId($id_impressora); 
if (!$impressora) die("Impressora não encontrada.");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao'])) {
 
    if ($_POST['acao'] == 'trocar_toner') {
        require_once 'modelsLibrary/H_troca.php';
        $novaTroca = new Troca();
        $novaTroca->setIdImpressora($id_impressora);
        $novaTroca->setLeituraNaTroca($_POST['leitura_atual_troca']);
        $novaTroca->setObservacao($_POST['observacao'] ?? '');

        if ($trocaDAO->registrarTroca($novaTroca)) {
            $mensagem = "<div style='color: #27ae60; background: #e8f8f5; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>✅ Troca de toner registrada com sucesso!</div>";
        } else {
            $mensagem = "<div style='color: #e74c3c; background: #fadbd8; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>❌ Erro ao registrar troca de toner.</div>";
        }
    }
 
    elseif ($_POST['acao'] == 'atualizar_leitura') {
        $nova_leitura = $_POST['nova_leitura'];
        
        if ($leituraDAO->registrarLeitura($id_impressora, $nova_leitura)) {
            $mensagem = "<div style='color: #27ae60; background: #e8f8f5; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>✅ Leitura atualizada com sucesso!</div>";
        } else {
            $mensagem = "<div style='color: #e74c3c; background: #fadbd8; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>❌ Erro ao atualizar leitura no banco.</div>";
        }
    }
}

$ultimaTroca = $trocaDAO->buscarUltimaTroca($id_impressora);
$leitura_atual = $leituraDAO->buscarUltimaLeitura($id_impressora);
$historicoLeituras = $leituraDAO->listarHistorico($id_impressora);
$historicoTrocas = $trocaDAO->listarHistorico($id_impressora);

$limite_toner = 17000;
$consumo_toner = 0;
$porcentagem = 0;
$cor_barra = "#bdc3c7";
$status_texto = "Aguardando 1ª Troca (Calibração)";
$previsao_texto = "Aguardando calibração...";

if ($ultimaTroca) {
    $leitura_marco_zero = $ultimaTroca['leitura_na_troca'];
    $consumo_toner = $leitura_atual - $leitura_marco_zero;
    if ($consumo_toner < 0) $consumo_toner = 0; 
    
    $porcentagem = ($consumo_toner / $limite_toner) * 100;
    if ($porcentagem > 100) $porcentagem = 100;

    if ($porcentagem <= 60) { $cor_barra = "#27ae60"; $status_texto = "Nível Adequado"; }
    elseif ($porcentagem <= 85) { $cor_barra = "#f39c12"; $status_texto = "Providenciar Estoque"; }
    else { $cor_barra = "#e74c3c"; $status_texto = "Troca Iminente"; }

    if (count($historicoLeituras) >= 2) {
        $leitura_recente = $historicoLeituras[0]; 
        $leitura_antiga = end($historicoLeituras); 
        
        $dias_corridos = (strtotime($leitura_recente['data_verificacao']) - strtotime($leitura_antiga['data_verificacao'])) / 86400; // 86400 seg = 1 dia
        
        if ($dias_corridos > 0) {
            $gasto_total_periodo = $leitura_recente['quantidade'] - $leitura_antiga['quantidade'];
            $media_diaria = $gasto_total_periodo / $dias_corridos;
            
            if ($media_diaria > 0) {
                $impressoes_restantes = $limite_toner - $consumo_toner;
                if($impressoes_restantes < 0) $impressoes_restantes = 0;
                
                $dias_restantes = ceil($impressoes_restantes / $media_diaria);
                $data_prevista = date('d/m/Y', strtotime("+$dias_restantes days"));
                
                $previsao_texto = "<span style='color: #d35400; font-weight: bold;'>$data_prevista</span> (Ritmo: " . number_format($media_diaria, 0) . " págs/dia)";
            }
        } else {
            $previsao_texto = "Mais tempo necessário para cálculo exato.";
        }
    } else {
         $previsao_texto = "Registros insuficientes para calcular a média.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Impressora - Sistema IMP</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <style>
        .modal { 
            display: none; 
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            background: rgba(0,0,0,0.5); 
            z-index: 1000; 
            align-items: center; 
            justify-content: center; 
        }
        
        .modal-content { 
            background: white; 
            padding: 25px; 
            border-radius: 8px; 
            width: 90%; 
            max-width: 400px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.3); 
        }

        .close-btn { 
            float: right; 
            cursor: pointer; 
            font-size: 1.2rem; 
            color: #7f8c8d; 
        }

        .close-btn:hover { 
            color: #e74c3c; 
        }
    </style>
</head>
<body>

    <?php include 'menu.php'; ?>

    <div class="conteudo-principal">
        <div class="cabecalho-tabela" style="display:flex; justify-content: space-between; align-items: center;">
            <h3>Dashboard: <?= htmlspecialchars($impressora['modelo'] ?? '') ?> (<?= htmlspecialchars($impressora['setor'] ?? '') ?>)</h3>
          
            <div>
                <button class="btn btn-primario" onclick="abrirModal('modalLeitura')">📊 Atualizar Leitura</button>
                <button class="btn btn-perigo" onclick="abrirModal('modalTroca')">🚨 Trocar Toner</button>
                <a href="index.php" class="btn btn-primario" style="background-color: #7f8c8d; margin-left:10px;">Voltar</a>
            </div>
        </div>

        <?= $mensagem ?>

        <div class="grid-info-rapida">
            <div class="card-mini">
                <h5>Impressões (Absoluto)</h5>
                <div class="valor"><?= number_format($leitura_atual, 0, ',', '.') ?></div>
                <small style="color: #95a5a6;">Vida útil total da máquina</small>
            </div>
            
            <div class="card-mini" style="border-left-color: <?= $cor_barra ?>;">
                <h5>Consumo do Toner (Relativo)</h5>
                <div class="valor"><?= number_format($consumo_toner, 0, ',', '.') ?> <span style="font-size:1rem; color:#7f8c8d;">/ 17.000</span></div>
                <small style="color: #95a5a6;">Status: <?= $status_texto ?></small>
            </div>

            <div class="card-mini" style="border-left-color: #e67e22;">
                <h5>Previsão de Troca</h5>
                <div class="valor" style="font-size: 1.2rem; margin-top:5px;"><?= $previsao_texto ?></div>
            </div>
        </div>

        <div class="card" style="margin-bottom: 20px;">
            <h4 style="color: #2c3e50; margin-bottom: 5px;">Progresso do Toner Atual</h4>
            <div class="barra-fundo" style="height: 35px; border-radius: 20px; background-color: #ecf0f1; overflow: hidden; width: 100%;">
                <div class="barra-progresso" style="width: <?= $porcentagem ?>%; background-color: <?= $cor_barra ?>; border-radius: 20px; display:flex; align-items:center; justify-content: flex-end; padding-right: 15px; color: white; font-weight: bold; transition: width 0.5s ease; height: 100%;">
                    <?php if($porcentagem > 5) echo number_format($porcentagem, 1) . '%'; ?>
                </div>
            </div>
        </div>

        <div class="dashboard-grid">

            <div class="card">
                <h4 style="color: #2c3e50; margin-bottom: 15px;">📊 Histórico de Leituras</h4>
                <div style="max-height: 300px; overflow-y: auto;">
                    <table class="tabela-padrao" style="width: 100%; text-align: left;">
                        <thead>
                            <tr style="border-bottom: 2px solid #eee;">
                                <th style="padding: 10px;">Data</th>
                                <th style="padding: 10px;">Quantidade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($historicoLeituras)): ?>
                                <tr><td colspan="2" style="text-align: center; padding: 15px;">Nenhuma leitura registrada</td></tr>
                            <?php else: ?>
                                <?php foreach($historicoLeituras as $leitura): ?>
                                    <tr style="border-bottom: 1px solid #f9f9f9;">
                                        <td style="padding: 10px;"><?= date('d/m/Y', strtotime($leitura['data_verificacao'])) ?></td>
                                        <td style="padding: 10px;"><?= number_format($leitura['quantidade'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <h4 style="color: #2c3e50; margin-bottom: 15px;">🚨 Histórico de Trocas</h4>
                <div style="max-height: 300px; overflow-y: auto;">
                    <table class="tabela-padrao" style="width: 100%; text-align: left;">
                        <thead>
                            <tr style="border-bottom: 2px solid #eee;">
                                <th style="padding: 10px;">Data</th>
                                <th style="padding: 10px;">Marco Zero</th>
                                <th style="padding: 10px;">Obs</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($historicoTrocas)): ?>
                                <tr><td colspan="3" style="text-align: center; padding: 15px;">Nenhuma troca registrada</td></tr>
                            <?php else: ?>
                                <?php foreach($historicoTrocas as $troca): ?>
                                    <tr style="border-bottom: 1px solid #f9f9f9;">
                                        <td style="padding: 10px;"><?= date('d/m/Y', strtotime($troca['data_troca'])) ?></td>
                                        <td style="padding: 10px;"><?= number_format($troca['leitura_na_troca'], 0, ',', '.') ?></td>
                                        <td style="padding: 10px;"><?= htmlspecialchars($troca['observacao'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

   
    <div id="modalLeitura" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="fecharModal('modalLeitura')">✖</span>
            <h3 style="margin-bottom: 20px; color: #2c3e50;">Atualizar Leitura</h3>
            <form action="dashboard_imp.php?id=<?= $id_impressora ?>" method="POST">
                <input type="hidden" name="acao" value="atualizar_leitura">
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px;">Quantidade no Visor:</label>
                    <input type="number" name="nova_leitura" class="form-control" style="width: 100%; padding: 8px;" required placeholder="Ex: 50500" value="<?= $leitura_atual ?>">
                </div>
                
                <button type="submit" class="btn btn-primario" style="width: 100%; padding: 10px;">Salvar Leitura</button>
            </form>
        </div>
    </div>

    <div id="modalTroca" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="fecharModal('modalTroca')">✖</span>
            <h3 style="margin-bottom: 20px; color: #e74c3c;">Registrar Troca</h3>
            <form action="dashboard_imp.php?id=<?= $id_impressora ?>" method="POST">
                <input type="hidden" name="acao" value="trocar_toner">
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px;">Leitura atual (Marco Zero):</label>
                    <input type="number" name="leitura_atual_troca" class="form-control" style="width: 100%; padding: 8px;" required placeholder="Ex: 45000" value="<?= $leitura_atual ?>">
                </div>
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px;">Observação (Opcional):</label>
                    <input type="text" name="observacao" class="form-control" style="width: 100%; padding: 8px;" placeholder="Ex: Cilindro trocado junto">
                </div>
                
                <button type="submit" class="btn btn-perigo" style="width: 100%; padding: 10px;">Confirmar Troca</button>
            </form>
        </div>
    </div>

    <script>
        function abrirModal(id) {
            document.getElementById(id).style.display = 'flex';
        }
        function fecharModal(id) {
            document.getElementById(id).style.display = 'none';
        }
        
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = "none";
            }
        }

        function ativarModoResenha() {
            document.body.classList.toggle('tema-coral');
        }
        
    </script>
</body>
</html>