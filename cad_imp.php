<?php
require_once 'modelsLibrary/Imp.php';
require_once 'daoLibrary/ImpDAO.php';
require_once 'daoLibrary/DepDAO.php';

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $impressora = new Imp();
    $impressora->setIdDepartamento($_POST['id_departamento']);
    $impressora->setModelo($_POST['modelo']);
    $impressora->setIp($_POST['ip']);
    $impressora->setSerial($_POST['serial']);
    $impressora->setTipoCor($_POST['tipo_cor']);

    $dao = new ImpDAO();
    if ($dao->cadastrar($impressora)) {
        $mensagem = "<div style='color: green; font-weight: bold; margin-bottom: 15px;'>✅ Impressora cadastrada com sucesso!</div>";
    } else {
        $mensagem = "<div style='color: red; font-weight: bold; margin-bottom: 15px;'>❌ Erro ao cadastrar. Verifique se o Serial já existe.</div>";
    }
}

$depDAO = new DepDAO();
$listaDepartamentos = $depDAO->listarTodos();
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
            <h3>Adicionar Nova Impressora</h3>
            <a href="index.php" class="btn btn-primario">Voltar ao Painel</a>
        </div>

        <div class="formulario-container">
            <?= $mensagem ?>
            
            <form action="cad_imp.php" method="POST">
                
                <div class="form-group">
                    <label for="id_departamento">Departamento / Setor:</label>
                    <select id="id_departamento" name="id_departamento" class="form-control" required>
                        <option value="">-- Selecione um Departamento --</option>
                        
                        <?php foreach ($listaDepartamentos as $dep): ?>
                            <option value="<?= $dep['id'] ?>">
                                <?= htmlspecialchars($dep['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                        
                    </select>
                </div>

                <div class="form-group">
                    <label for="modelo">Modelo da Impressora:</label>
                    <input type="text" id="modelo" name="modelo" class="form-control" placeholder="Ex: Canon G3110" required>
                </div>

                <div class="form-group">
                    <label for="ip">Endereço IP:</label>
                    <input type="text" id="ip" name="ip" class="form-control" placeholder="Ex: 10.0.0.230" required>
                </div>

                <div class="form-group">
                    <label for="serial">Número de Série:</label>
                    <input type="text" id="serial" name="serial" class="form-control" placeholder="Ex: 35C69707" required>
                </div>

                <div class="form-group">
                    <label for="tipo_cor">Tipo de Impressão:</label>
                    <select id="tipo_cor" name="tipo_cor" class="form-control" required>
                        <option value="PRETA E BRANCA">Preta e Branca</option>
                        <option value="COLORIDA">Colorida</option>
                    </select>
                </div>

                <div style="text-align: right;">
                    <button type="submit" class="btn btn-sucesso">Salvar Impressora</button>
                </div>

            </form>
        </div>
    </div>
</body>
</html>