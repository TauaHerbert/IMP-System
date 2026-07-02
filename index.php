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

        <div class="tabela-container">
            <table>
                <thead>
                    <tr>
                        <th>Setor</th>
                        <th>IP</th>
                        <th>Serial</th>
                        <th>Cor</th>
                        <th>Última Leitura</th>
                        <th class="text-center">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 30px; color: #7f8c8d;">
                            Nenhuma impressora cadastrada ainda. Utilize o menu ao lado para iniciar o mapeamento.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>