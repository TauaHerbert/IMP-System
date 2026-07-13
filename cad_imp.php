<?php
require_once 'modelsLibrary/Imp.php';
require_once 'daoLibrary/ImpDAO.php';
require_once 'daoLibrary/DepDAO.php'; 

$mensagem = "";
$dao = new ImpDAO();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['acao']) && $_POST['acao'] == 'arquivar') {
        $id_arquivar = $_POST['id_impressora'];
        if ($dao->arquivar($id_arquivar)) {
            $mensagem = "<div style='color: #e67e22; font-weight: bold; margin-bottom: 15px;'>⚠️ Equipamento movido para Inativos com sucesso!</div>";
        } else {
            $mensagem = "<div style='color: #e74c3c; font-weight: bold; margin-bottom: 15px;'>❌ Erro ao desativar o equipamento.</div>";
        }
    } 

    elseif (isset($_POST['modelo'])) {
        $impressora = new Imp();
        $impressora->setIdDepartamento($_POST['id_departamento']);
        $impressora->setModelo($_POST['modelo']);
        $impressora->setIp($_POST['ip']);
        $impressora->setSerial($_POST['serial']);
        $impressora->setTipoCor($_POST['tipo_cor']);

        if ($dao->cadastrar($impressora)) {
            $mensagem = "<div style='color: #27ae60; font-weight: bold; margin-bottom: 15px;'>✅ Impressora cadastrada com sucesso!</div>";
        } else {
            $mensagem = "<div style='color: #e74c3c; font-weight: bold; margin-bottom: 15px;'>❌ Erro ao cadastrar. Verifique se o Serial ou IP já existem.</div>";
        }
    }
}

$depDAO = new DepDAO();
$listaDepartamentos = $depDAO->listarTodos();
$listaImpressoras = $dao->listarTodasComStatus();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Impressora - Sistema IMP</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <?php include 'menu.php'; ?>

    <div class="conteudo-principal">
        <div class="cabecalho-tabela">
            <h2>Gerenciar Impressoras</h2>
        </div>

        <?= $mensagem ?>
        
        <div class="formulario-container" style="max-width: 100%; margin-bottom: 40px; margin-top: 0;">
            <h3 style="margin-bottom: 20px; color: #2c3e50; border-bottom: 1px solid #eee; padding-bottom: 10px;">Adicionar Novo Equipamento</h3>
            
            <form action="cad_imp.php" method="POST" class="form-layout-horizontal">
                
                <div class="form-group col-full">
                    <label for="id_departamento">Departamento / Setor de Alocação:</label>
                    <select id="id_departamento" name="id_departamento" class="form-control" required>
                        <option value="">-- Selecione um Departamento --</option>
                        <?php foreach ($listaDepartamentos as $dep): ?>
                            <option value="<?= $dep['id'] ?>"><?= htmlspecialchars($dep['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="modelo">Modelo da Máquina:</label>
                    <input type="text" id="modelo" name="modelo" class="form-control" placeholder="Ex: Canon G3110" required>
                </div>

                <div class="form-group">
                    <label for="ip">Endereço IP (Rede):</label>
                    <input type="text" id="ip" name="ip" class="form-control" placeholder="Ex: 10.0.0.230" required>
                </div>

                <div class="form-group">
                    <label for="serial">Número de Série (Patrimônio):</label>
                    <input type="text" id="serial" name="serial" class="form-control" placeholder="Ex: 35C69707" required>
                </div>

                <div class="form-group">
                    <label for="tipo_cor">Tipo de Impressão:</label>
                    <select id="tipo_cor" name="tipo_cor" class="form-control" required>
                        <option value="PRETA E BRANCA">Preta e Branca</option>
                        <option value="COLORIDA">Colorida</option>
                    </select>
                </div>

                <div class="col-full" style="text-align: right; margin-top: 10px;">
                    <button type="submit" class="btn btn-sucesso">Salvar Impressora</button>
                </div>

            </form>
        </div>

        <div class="tabela-container">
            <h3 style="padding: 20px 20px 20px; color: #2c3e50;">Equipamentos Cadastrados</h3>
            <table>
                <thead>
                    <tr>
                        <th>Modelo</th>
                        <th>Setor</th>
                        <th>IP</th>
                        <th>Serial</th>
                        <th>Tipo</th>
                        <th style="text-align: center;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaImpressoras)): ?>
                        <tr>
                            <td colspan="6" class="text-center" style="padding: 30px; color: #7f8c8d;">
                                Nenhuma impressora ativa cadastrada no banco de dados.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listaImpressoras as $imp): ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($imp['modelo'] ?? '') ?></td>
                                <td><?= htmlspecialchars($imp['setor'] ?? '') ?></td>
                                <td><?= htmlspecialchars($imp['ip'] ?? '') ?></td>
                                <td style="font-family: monospace; font-size: 1.1em; color: #555;">
                                    <?= htmlspecialchars($imp['serial'] ?? '') ?>
                                </td>
                                <td>
                                    <?php if ($imp['tipo_cor'] == 'COLORIDA'): ?>
                                        <span class="badge badge-colorida">Colorida</span>
                                    <?php else: ?>
                                        <span class="badge badge-pb">P&B</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <form action="cad_imp.php" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja DESATIVAR a impressora <?= htmlspecialchars($imp['modelo']) ?>? Ela será enviada para o depósito.');">
                                        <input type="hidden" name="acao" value="arquivar">
                                        <input type="hidden" name="id_impressora" value="<?= $imp['id'] ?>">
                                        <button type="submit" class="btn btn-perigo" style="padding: 5px 12px; font-size: 0.85rem;">Desativar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>

            </table>

         </div>

          <div class="col-full" style="text-align: right; margin-top: 20px;">
                <a href="imp_inativas.php" class="btn btn-primario" style="text-decoration: none !important; text-transform: uppercase;">Impressoras Inativas</a>
            </div>

    </div>
    
    <script>
        function ativarModoResenha() {
            document.body.classList.toggle('tema-coral');
        }
    </script>

</body>
</html>