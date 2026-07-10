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
            $mensagem = "<div style='color: #27ae60; background: rgba(39, 174, 96, 0.1); border: 1px solid #27ae60; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>✅ Troca de toner registrada com sucesso!</div>";
        } else {
            $mensagem = "<div style='color: #e74c3c; background: rgba(231, 76, 60, 0.1); border: 1px solid #e74c3c; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>❌ Erro ao registrar troca de toner.</div>";
        }
    }
 
    elseif ($_POST['acao'] == 'atualizar_leitura') {
        $nova_leitura = $_POST['nova_leitura'];
        
        if ($leituraDAO->registrarLeitura($id_impressora, $nova_leitura)) {
            $mensagem = "<div style='color: #27ae60; background: rgba(39, 174, 96, 0.1); border: 1px solid #27ae60; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>✅ Leitura atualizada com sucesso!</div>";
        } else {
            $mensagem = "<div style='color: #e74c3c; background: rgba(231, 76, 60, 0.1); border: 1px solid #e74c3c; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>❌ Erro ao atualizar leitura no banco.</div>";
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
$previsao_historica = "";

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
        $leitura_hoje = $historicoLeituras[0]; 
        $leitura_anterior = $historicoLeituras[1]; 
        
        $impressoes_restantes = $limite_toner - $consumo_toner;
        if($impressoes_restantes < 0) $impressoes_restantes = 0;

        $dias_desde_troca = (strtotime($leitura_hoje['data_verificacao']) - strtotime($ultimaTroca['data_troca'])) / 86400;
        if ($dias_desde_troca <= 0) $dias_desde_troca = 1; 
        
        $media_diaria_global = $consumo_toner / $dias_desde_troca;
        
        if ($media_diaria_global > 0) {
            $dias_restantes_global = ceil($impressoes_restantes / $media_diaria_global);
            $data_prevista_global = date('d/m/Y', strtotime("+$dias_restantes_global days"));
            
            $previsao_historica = "<div style='margin-top: 10px; padding-top: 10px; border-top: 1px dashed var(--cor-borda);'>";
            $previsao_historica .= "<span style='font-size: 0.85rem; color: var(--cor-texto-secundario);'>Média Global: <strong>$data_prevista_global</strong></span>";
            $previsao_historica .= "</div>";
        } else {
            $media_diaria_global = 1; 
        }

        $dias_recentes = (strtotime($leitura_hoje['data_verificacao']) - strtotime($leitura_anterior['data_verificacao'])) / 86400;
        
        if ($dias_recentes > 0) {
            $gasto_recente = $leitura_hoje['quantidade'] - $leitura_anterior['quantidade'];
            $media_diaria_recente = $gasto_recente / $dias_recentes;
            
            if ($media_diaria_recente > 0) {
                $dias_restantes_atual = ceil($impressoes_restantes / $media_diaria_recente);
                $data_prevista_atual = date('d/m/Y', strtotime("+$dias_restantes_atual days"));
 
                $status_tendencia = "";
                $cor_tendencia = "";
                
                if ($media_diaria_recente > ($media_diaria_global * 1.15)) {
                    $status_tendencia = "📈 Acelerou o uso";
                    $cor_tendencia = "#e74c3c"; 
                } elseif ($media_diaria_recente < ($media_diaria_global * 0.85)) {
                    $status_tendencia = "📉 Diminuiu o uso";
                    $cor_tendencia = "#27ae60"; 
                } else {
                    $status_tendencia = "⚖️ Manteve o padrão";
                    $cor_tendencia = "#f39c12"; 
                }
                
                $previsao_texto = "<span style='color: var(--cor-azul-destaque); font-weight: bold; font-size: 1.3rem;'>$data_prevista_atual</span> <br>";
                $previsao_texto .= "<span style='font-size: 0.85rem; color: var(--cor-texto-secundario); line-height: 1.6;'>Ritmo Atual: " . number_format($media_diaria_recente, 0) . " págs/dia</span><br>";
                $previsao_texto .= "<span style='font-size: 0.85rem; color: $cor_tendencia; font-weight: bold;'>$status_tendencia</span>";
            }
        } else {
            $previsao_texto = "Aguardando próxima leitura para ritmo atual.";
        }
    } else {
         $previsao_texto = "Mínimo de 2 leituras para prever.";
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
</head>
<body>

    <div class="conteudo-principal">
        <div class="cabecalho-tabela" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3>Dashboard do Equipamento</h3>
          
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">

                <?php if(!empty($impressora['ip'])): ?>
                    <a href="http://<?= htmlspecialchars($impressora['ip']) ?>" target="_blank" class="btn btn-sucesso">🌐 Acessar Painel Web</a>
                <?php endif; ?>
                
                <button class="btn btn-primario" onclick="abrirModal('modalLeitura')">📊 Atualizar Leitura</button>
                <button class="btn btn-perigo" onclick="abrirModal('modalTroca')">🚨 Trocar Toner</button>
                <a href="index.php" class="btn btn-primario" style="background-color: var(--cor-menu-lateral); border: 1px solid var(--cor-borda);">Voltar</a>
            </div>
        </div>

        <div class="card" style="margin-bottom: 25px; padding: 15px 25px;">
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
                <div>
                    <span style="color: var(--cor-texto-secundario); font-size: 0.85rem; text-transform: uppercase;">Modelo</span><br>
                    <strong style="font-size: 1.1rem; color: var(--cor-texto-principal);"><?= htmlspecialchars($impressora['modelo'] ?? '-') ?></strong>
                </div>
                <div>
                    <span style="color: var(--cor-texto-secundario); font-size: 0.85rem; text-transform: uppercase;">Setor / Departamento</span><br>
                    <strong style="font-size: 1.1rem; color: var(--cor-texto-principal);"><?= htmlspecialchars($impressora['setor'] ?? '-') ?></strong>
                </div>
                <div>
                    <span style="color: var(--cor-texto-secundario); font-size: 0.85rem; text-transform: uppercase;">Endereço IP</span><br>
                    <strong style="font-size: 1.1rem; color: var(--cor-texto-principal);"><?= htmlspecialchars($impressora['ip'] ?? '-') ?></strong>
                </div>
                <div>
                    <span style="color: var(--cor-texto-secundario); font-size: 0.85rem; text-transform: uppercase;">Número de Série</span><br>
                    <strong style="font-size: 1.1rem; color: var(--cor-texto-principal); font-family: monospace;"><?= htmlspecialchars($impressora['serial'] ?? '-') ?></strong>
                </div>
                <div>
                    <span style="color: var(--cor-texto-secundario); font-size: 0.85rem; text-transform: uppercase;">Tipo</span><br>
                    <div style="margin-top: 3px;">
                        <?php if (($impressora['tipo_cor'] ?? '') == 'COLORIDA'): ?>
                            <span class="badge badge-colorida">Colorida</span>
                        <?php else: ?>
                            <span class="badge badge-pb">P&B</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?= $mensagem ?>

        <div class="grid-info-rapida">
            <div class="card-mini">
                <h5>Impressões (Absoluto)</h5>
                <div class="valor"><?= number_format($leitura_atual, 0, ',', '.') ?></div>
                <small style="color: var(--cor-texto-secundario);">Vida útil total da máquina</small>
            </div>
            
            <div class="card-mini" style="border-left-color: <?= $cor_barra ?>;">
                <h5>Consumo do Toner (Relativo)</h5>
                <div class="valor"><?= number_format($consumo_toner, 0, ',', '.') ?> <span style="font-size:1rem; color:var(--cor-texto-secundario);">/ 17.000</span></div>
                <small style="color: var(--cor-texto-secundario);">Status: <?= $status_texto ?></small>
            </div>

            <div class="card-mini" style="border-left-color: #e67e22;">
                <h5>Previsão de Troca</h5>
                <div class="valor" style="font-size: 1.1rem; margin-top:5px; margin-bottom: 5px;">
                    <?= $previsao_texto ?>
                </div>
                <?= $previsao_historica ?>
            </div>
        </div>

        <div class="card" style="margin-bottom: 20px;">
            <h4 style="color: var(--cor-texto-secundario); margin-bottom: 5px;">Progresso do Toner Atual</h4>
            <div class="barra-fundo">
                <div class="barra-progresso" style="width: <?= $porcentagem ?>%; background-color: <?= $cor_barra ?>; display:flex; align-items:center; justify-content: flex-end; padding-right: 15px; color: white; font-weight: bold;">
                    <?php if($porcentagem > 5) echo number_format($porcentagem, 1) . '%'; ?>
                </div>
            </div>
        </div>

        <div class="dashboard-grid">

            <div class="card">
                <h4 style="color: var(--cor-texto-secundario); margin-bottom: 15px;">📊 Histórico de Leituras</h4>
                <div class="historico-container">
                    <table class="tabela-historico">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Quantidade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($historicoLeituras)): ?>
                                <tr><td colspan="2" class="text-center" style="padding: 15px;">Nenhuma leitura registrada</td></tr>
                            <?php else: ?>
                                <?php foreach($historicoLeituras as $leitura): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($leitura['data_verificacao'])) ?></td>
                                        <td><?= number_format($leitura['quantidade'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <h4 style="color: var(--cor-texto-secundario); margin-bottom: 15px;">🚨 Histórico de Trocas</h4>
                <div class="historico-container">
                    <table class="tabela-historico">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Marco Zero</th>
                                <th>Obs</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($historicoTrocas)): ?>
                                <tr><td colspan="3" class="text-center" style="padding: 15px;">Nenhuma troca registrada</td></tr>
                            <?php else: ?>
                                <?php foreach($historicoTrocas as $troca): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($troca['data_troca'])) ?></td>
                                        <td><?= number_format($troca['leitura_na_troca'], 0, ',', '.') ?></td>
                                        <td><?= htmlspecialchars($troca['observacao'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <div id="modalLeitura" class="modal-overlay">
        <div class="modal-content">
            <span class="btn-fechar-modal" onclick="fecharModal('modalLeitura')">✖</span>
            <h3 style="margin-bottom: 20px;">Atualizar Leitura</h3>
            <form action="dashboard_imp.php?id=<?= $id_impressora ?>" method="POST">
                <input type="hidden" name="acao" value="atualizar_leitura">
                
                <div class="form-group">
                    <label>Quantidade no Visor:</label>
                    <input type="number" name="nova_leitura" class="form-control" required placeholder="Ex: 50500" value="<?= $leitura_atual ?>">
                </div>
                
                <button type="submit" class="btn btn-primario" style="width: 100%; margin-top: 10px;">Salvar Leitura</button>
            </form>
        </div>
    </div>

    <div id="modalTroca" class="modal-overlay">
        <div class="modal-content">
            <span class="btn-fechar-modal" onclick="fecharModal('modalTroca')">✖</span>
            <h3 style="margin-bottom: 20px; color: #e74c3c;">Registrar Troca</h3>
            <form action="dashboard_imp.php?id=<?= $id_impressora ?>" method="POST">
                <input type="hidden" name="acao" value="trocar_toner">
                
                <div class="form-group">
                    <label>Leitura atual (Marco Zero):</label>
                    <input type="number" name="leitura_atual_troca" class="form-control" required placeholder="Ex: 45000" value="<?= $leitura_atual ?>">
                </div>
                
                <div class="form-group">
                    <label>Observação (Opcional):</label>
                    <input type="text" name="observacao" class="form-control" placeholder="Ex: Cilindro trocado junto">
                </div>
                
                <button type="submit" class="btn btn-perigo" style="width: 100%; margin-top: 10px;">Confirmar Troca</button>
            </form>
        </div>
    </div>

    <script>
        function abrirModal(id) {
            document.getElementById(id).classList.add('ativo');
        }
        function fecharModal(id) {
            document.getElementById(id).classList.remove('ativo');
        }
        
        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.classList.remove('ativo');
            }
        }
    </script>
</body>
</html>