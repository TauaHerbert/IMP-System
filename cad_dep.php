<?php
require_once 'modelsLibrary/Dep.php';
require_once 'daoLibrary/DepDAO.php';

$mensagem = "";

// Verifica se o botão de salvar foi clicado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $dep = new Dep();
    $dep->setNome($_POST['nome_departamento']);

    $dao = new DepDAO();
    $resultado = $dao->cadastrar($dep);

    // Tratamento das mensagens de retorno
    if ($resultado === true) {
        $mensagem = "<div style='color: green; font-weight: bold; margin-bottom: 15px;'>✅ Departamento cadastrado com sucesso!</div>";
    } elseif ($resultado === "duplicado") {
        $mensagem = "<div style='color: orange; font-weight: bold; margin-bottom: 15px;'>⚠️ Atenção: Este departamento já existe no banco de dados.</div>";
    } else {
        $mensagem = "<div style='color: red; font-weight: bold; margin-bottom: 15px;'>❌ Erro ao cadastrar o departamento.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Departamento - Sistema IMP</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <?php include 'menu.php'; ?>

    <div class="conteudo-principal">
        
        <div class="cabecalho-tabela">
            <h3>Adicionar Novo Departamento</h3>
            <a href="index.php" class="btn btn-primario">Voltar ao Painel</a>
        </div>

        <div class="formulario-container">
            <?= $mensagem ?> 
            
            <form action="cad_dep.php" method="POST">
                
                <div class="form-group">
                    <label for="nome_departamento">Nome do Setor / Departamento:</label>
                    <input type="text" id="nome_departamento" name="nome_departamento" class="form-control" placeholder="Ex: PLANEJAMENTO, RECURSOS HUMANOS, DIRETORIA..." required>
                </div>

                <div style="text-align: right;">
                    <button type="submit" class="btn btn-sucesso">Salvar Departamento</button>
                </div>

            </form>
        </div>

    </div>

</body>
</html>