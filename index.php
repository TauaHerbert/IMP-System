<?php
// Importações
require_once 'modelsLibrary/Imp.php';
require_once 'daoLibrary/ImpDAO.php';
require_once 'modelsLibrary/Leitura.php';
require_once 'daoLibrary/LeituraDAO.php';


$mensagem_painel = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao']) && $_POST['acao'] == 'atualizar_leitura') {
    $leitura = new Leitura();
    $leitura->setIdImpressora($_POST['modal_id_impressora']);
    $leitura->setQuantidadeImpressoes($_POST['modal_quantidade']);

    $leituraDAO = new LeituraDAO();
    if ($leituraDAO->cadastrar($leitura)) {
    
        header("Location: index.php?sucesso=1");
        exit();
    } else {
        $mensagem_painel = "<div style='color: red; font-weight: bold; margin-bottom: 15px;'>❌ Erro ao atualizar o contador.</div>";
    }
}

if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1) {
    $mensagem_painel = "<div style='color: green; font-weight: bold; margin-bottom: 15px;'>✅ Contador atualizado com sucesso!</div>";
}

$dao = new ImpDAO();
$listaImpressoras = $dao->listarTodas();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - Monitoramento de Impressoras</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <?php include 'menu.php'; ?>

    <div class="conteudo-principal">
        
        <div class="cabecalho-tabela">
            <h3>Visão Geral das Impressoras</h3>
        </div>

        <?= $mensagem_painel ?>

        <div class="tabela-container">
            <table>
                <thead>
                    <tr>
                        <th>Setor</th>
                        <th>IP</th>
                        <th>Serial</th>
                        <th>Cor</th>
                        <th>Última Leitura</th>
                        <th>Data Leitura</th> 
                        <th>Data Troca Tuner</th>   
                        <th class="text-center">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaImpressoras)): ?>
                        <tr>
                            <td colspan="8" class="text-center" style="padding: 30px; color: #7f8c8d;">
                                Nenhuma impressora cadastrada ainda. Utilize o menu ao lado para iniciar o mapeamento.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listaImpressoras as $imp): ?>
                            <tr>
                                <td><?= htmlspecialchars($imp['setor']) ?></td>
                                <td><?= htmlspecialchars($imp['ip']) ?></td>
                                <td><?= htmlspecialchars($imp['serial']) ?></td>
                                <td>
                                    <?php if ($imp['tipo_cor'] == 'COLORIDA'): ?>
                                        <span class="badge badge-colorida">Colorida</span>
                                    <?php else: ?>
                                        <span class="badge badge-pb">P&B</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold">
                                    <?= $imp['ultima_leitura'] ? number_format($imp['ultima_leitura'], 0, ',', '.') : '<span style="color: #95a5a6; font-size: 0.9em;">Sem leitura</span>' ?>
                                </td>

                                <td class="text-center">
                                    <?= !empty($imp['ultima_data_leitura']) ? date('d/m/Y', strtotime($imp['ultima_data_leitura'])) : '<span style="color: #95a5a6; font-size: 0.85em;">Sem registro</span>' ?>
                                </td>

                                <td class="text-center">
                                    <?= !empty($imp['ultima_data_troca']) ? date('d/m/Y', strtotime($imp['ultima_data_troca'])) : '<span style="color: #95a5a6; font-size: 0.85em;">Sem registro</span>' ?>
                                </td>

                                <td class="text-center">
                                    <a href="dashboard_imp.php?id=<?= $imp['id'] ?>" class="btn btn-primario" style="font-size: 0.8rem; padding: 5px 10px;"> ⚙️ Detalhes </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="meuModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Atualizar Contador</h3>
            </div>
            
            <p id="info-maquina" style="margin-bottom: 15px; color: #7f8c8d; font-size: 0.9rem;"></p>

            <form action="index.php" method="POST">
                <input type="hidden" name="acao" value="atualizar_leitura">
                <input type="hidden" name="modal_id_impressora" id="modal_id_impressora">

                <div class="form-group">
                    <label for="modal_quantidade">Novo número de páginas:</label>
                    <input type="number" id="modal_quantidade" name="modal_quantidade" class="form-control" placeholder="Ex: 15500" min="0" required>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-perigo" onclick="fecharModal()">Cancelar</button>
                    <button type="submit" class="btn btn-sucesso">Salvar Leitura</button>
                </div>
            </form>
        </div>
    </div>

    <script>
      
        function abrirModal(id, modelo, setor) {
           
            document.getElementById('modal_id_impressora').value = id;
            
            document.getElementById('info-maquina').innerHTML = "<strong>Equipamento:</strong> " + modelo + " <br> <strong>Setor:</strong> " + setor;
           
            document.getElementById('modal_quantidade').value = "";
            
            document.getElementById('meuModal').style.display = 'flex';
        }
        
        function fecharModal() {
            document.getElementById('meuModal').style.display = 'none';
        }
    </script>

</body>
</html>