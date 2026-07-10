<?php
require_once 'modelsLibrary/Dep.php';
require_once 'daoLibrary/DepDAO.php';

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dep = new Dep();
    $dep->setNome($_POST['nome_departamento']);

    $dao = new DepDAO();
    $resultado = $dao->cadastrar($dep);

    if ($resultado === true) {
        $mensagem = "<div style='color: #27ae60; font-weight: bold; margin-bottom: 15px;'>✅ Departamento cadastrado com sucesso!</div>";
    } elseif ($resultado === "duplicado") {
        $mensagem = "<div style='color: #f39c12; font-weight: bold; margin-bottom: 15px;'>⚠️ Atenção: Este departamento já existe.</div>";
    } else {
        $mensagem = "<div style='color: #e74c3c; font-weight: bold; margin-bottom: 15px;'>❌ Erro ao cadastrar o departamento.</div>";
    }
}

$depDAO_listagem = new DepDAO();
$listaDepartamentos = $depDAO_listagem->listarTodos();
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
            <h3>Gerenciar Departamentos</h3>
        </div>

        <?= $mensagem ?> 

        <div class="grid-duas-colunas">
            
            <div class="formulario-container">
                <h4 style="margin-bottom: 20px; color: #9ca3af; border-bottom: 1px solid #eee; padding-bottom: 10px;">Adicionar Novo</h4>
                
                <form action="cad_dep.php" method="POST">
                    <div class="form-group">
                        <label for="nome_departamento">Nome do Setor / Departamento:</label>
                        <input type="text" id="nome_departamento" name="nome_departamento" class="form-control" placeholder="Ex: PLANEJAMENTO, RH, DIRETORIA..." required>
                    </div>

                    <div style="text-align: right;">
                        <button type="submit" class="btn btn-sucesso">Salvar</button>
                    </div>
                </form>
            </div>

            <div class="tabela-container">
                <h4 style="padding: 20px 20px 17px; color: #9ca3af;">Setores Cadastrados</h4>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Nome do Departamento</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($listaDepartamentos)): ?>
                            <tr>
                                <td colspan="2" class="text-center" style="padding: 20px; color: #7f8c8d;">
                                    Nenhum departamento cadastrado.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($listaDepartamentos as $dep): ?>
                                <tr>
                                    <td class="fw-bold text-center"><?= htmlspecialchars($dep['id']) ?></td>
                                    <td><?= htmlspecialchars($dep['nome']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div> 
    </div>

    <script>
        function ativarModoResenha() {
            document.body.classList.toggle('tema-coral');
        }
    </script>

</body>
</html>