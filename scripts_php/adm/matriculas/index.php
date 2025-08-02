z<?php
session_start();
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

$busca = $_GET['busca'] ?? '';

if ($busca) {
    $stmt = $pdo->prepare("
        SELECT * FROM matriculas_academicas
        WHERE matricula LIKE ? OR tipo LIKE ?
        ORDER BY id DESC
    ");
    $param = "%$busca%";
    $stmt->execute([$param, $param]);
} else {
    $stmt = $pdo->query("
        SELECT * FROM matriculas_academicas
        ORDER BY id DESC
    ");
}

$matriculas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Matrículas Acadêmicas</title>
</head>
<body>
    <h1>Matrículas Acadêmicas</h1>

    <form method="get" action="index.php">
        <input type="text" name="busca" placeholder="Buscar por matrícula ou tipo" value="<?= htmlspecialchars($busca) ?>">
        <button type="submit">Buscar</button>
        <?php if ($busca): ?>
            <a href="index.php">Limpar</a>
        <?php endif; ?>
    </form>

    <br>

    <a href="criar.php">+ Nova Matrícula Acadêmica</a>
    <br><br>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Matrícula</th>
                <th>Tipo</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($matriculas as $m): ?>
                <?php if ($m['usada']): ?>
                    <tr style="background-color: #fce4ec;">
                <?php else: ?>
                    <tr style="background-color: #e8f5e9;">
                <?php endif; ?>
                    <td><?= $m['id'] ?></td>
                    <td><?= htmlspecialchars($m['matricula']) ?></td>
                    <td><?= ucfirst($m['tipo']) ?></td>
                    <td><?= $m['usada'] ? 'Usada' : 'Disponível' ?></td>
                    <td>
                        <a href="visualizar.php?id=<?= $m['id'] ?>">Visualizar</a> |
                        <a href="editar.php?id=<?= $m['id'] ?>">Editar</a> |
                        <a href="deletar.php?id=<?= $m['id'] ?>" onclick="return confirm('Deseja excluir esta matrícula?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <a href="/public/php/painel_admin.php">Voltar</a>
</body>
</html>
