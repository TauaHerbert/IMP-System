<?php
require_once 'modelsLibrary/Imp.php';
require_once 'daoLibrary/ImpDAO.php';

$mensagem = "";
$dao = new ImpDAO();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao'])) {
    $id_alvo = $_POST['id_impressora'];

    if ($_POST['acao'] == 'reativar') {
        if ($dao->reativar($id_alvo)) {
            $mensagem = "<div style='color: #27ae60; background: rgba(39, 174, 96, 0.1); border: 1px solid #27ae60; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-weight: bold;'>✅ Equipamento reativado e retornado ao painel principal!</div>";
        } else {
            $mensagem = "<div style='color: #e74c3c; font-weight: bold; margin-bottom: 15px;'>❌ Erro ao reativar o equipamento.</div>";
        }
    } 
    elseif ($_POST['acao'] == 'excluir') {
        if ($dao->excluirDefinitivo($id_alvo)) {
            $mensagem = "<div style='color: #27ae60; background: rgba(39, 174, 96, 0.1); border: 1px solid #27ae60; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-weight: bold;'>🗑️ Equipamento excluído permanentemente do sistema!</div>";
        } else {
            $mensagem = "<div style='color: #e74c3c; background: rgba(231, 76, 60, 0.1); border: 1px solid #e74c3c; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-weight: bold;'>❌ Erro ao excluir. Pode haver histórico de leituras vinculado a ela.</div>";
        }
    }
}

$listaImpressoras = $dao->listarInativas();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Depósito de Impressoras - Sistema IMP</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

    <div class="conteudo-principal" style="margin: 0 auto; max-width: 1200px; padding: 40px 20px;">
     
        <div class="cabecalho-tabela" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <div>
                <h2 style="color: var(--cor-texto-principal); margin: 0;">Depósito (Inativas)</h2>
                <small style="color: var(--cor-texto-secundario);">Equipamentos desativados ou em manutenção</small>
            </div>
            
            <a href="cad_imp.php" class="btn btn-primario" style="background-color: var(--cor-menu-lateral); border: 1px solid var(--cor-borda); text-decoration: none !important;">Voltar</a>
        </div>

        <?= $mensagem ?>

        <div class="tabela-container" style="background: var(--cor-fundo-tabela); border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); padding-bottom: 10px;">
            <h3 style="padding: 20px; color: var(--cor-texto-secundario); border-bottom: 1px solid var(--cor-borda); margin-top: 0;">Equipamentos Arquivados</h3>
            
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: var(--cor-fundo-principal);">
                        <th style="padding: 15px; text-align: left; color: var(--cor-texto-secundario);">Modelo</th>
                        <th style="padding: 15px; text-align: left; color: var(--cor-texto-secundario);">Setor</th>
                        <th style="padding: 15px; text-align: left; color: var(--cor-texto-secundario);">IP</th>
                        <th style="padding: 15px; text-align: left; color: var(--cor-texto-secundario);">Serial</th>
                        <th style="padding: 15px; text-align: left; color: var(--cor-texto-secundario);">Tipo</th>
                        <th style="padding: 15px; text-align: center; color: var(--cor-texto-secundario);">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaImpressoras)): ?>
                        <tr>
                            <td colspan="6" class="text-center" style="padding: 40px; color: var(--cor-texto-secundario); font-size: 1.1rem;">
                                Nenhuma impressora inativa no depósito.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listaImpressoras as $imp): ?>
                            <tr style="border-bottom: 1px solid var(--cor-borda);">
                                <td style="padding: 15px; color: var(--cor-texto-principal); font-weight: bold;"><?= htmlspecialchars($imp['modelo'] ?? '') ?></td>
                                <td style="padding: 15px; color: var(--cor-texto-principal);"><?= htmlspecialchars($imp['setor'] ?? '') ?></td>
                                <td style="padding: 15px; color: var(--cor-texto-principal);"><?= htmlspecialchars($imp['ip'] ?? '') ?></td>
                                <td style="padding: 15px; font-family: monospace; font-size: 1.1em; color: var(--cor-texto-secundario);">
                                    <?= htmlspecialchars($imp['serial'] ?? '') ?>
                                </td>
                                <td style="padding: 15px;">
                                    <?php if ($imp['tipo_cor'] == 'COLORIDA'): ?>
                                        <span class="badge badge-colorida">Colorida</span>
                                    <?php else: ?>
                                        <span class="badge badge-pb">P&B</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 15px; text-align: center; white-space: nowrap;">
                                    <form action="imp_inativas.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="acao" value="reativar">
                                        <input type="hidden" name="id_impressora" value="<?= $imp['id'] ?>">
                                        <button type="submit" class="btn btn-sucesso" style="padding: 6px 15px; font-size: 0.85rem; margin-right: 5px;">Ativar</button>
                                    </form>
                                    
                                    <form action="imp_inativas.php" method="POST" style="display:inline;" onsubmit="return confirm('PERIGO: Tem certeza que deseja excluir a impressora <?= htmlspecialchars($imp['modelo']) ?> definitivamente? Esta ação não pode ser desfeita.');">
                                        <input type="hidden" name="acao" value="excluir">
                                        <input type="hidden" name="id_impressora" value="<?= $imp['id'] ?>">
                                        <button type="submit" class="btn btn-perigo" style="padding: 6px 15px; font-size: 0.85rem;">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
         </div>
    </div>

</body>
</html>