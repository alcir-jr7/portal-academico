<?php
require_once __DIR__ . '/../../../public/includes/header_admin.php';

$busca = $_GET['busca'] ?? '';

if ($busca) {
    $stmt = $pdo->prepare("
        SELECT m.*, u.nome 
        FROM matriculas_academicas m
        LEFT JOIN usuarios u ON u.matricula = m.matricula AND u.tipo = 'admin'
        WHERE (m.matricula LIKE ? OR u.nome LIKE ?)
        AND m.tipo = 'admin'
        ORDER BY m.id DESC
    ");
    $param = "%$busca%";
    $stmt->execute([$param, $param]);
} else {
    $stmt = $pdo->prepare("
        SELECT m.*, u.nome 
        FROM matriculas_academicas m
        LEFT JOIN usuarios u ON u.matricula = m.matricula AND u.tipo = 'admin'
        WHERE m.tipo = 'admin'
        ORDER BY m.id DESC
    ");
    $stmt->execute();
}

$matriculas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="page-main">
    <h1 class="page-title">Gerenciar Administradores</h1>

    <form method="get" action="index.php" class="form-search">
        <input type="text" name="busca" placeholder="Buscar por matrícula ou nome" value="<?= htmlspecialchars($busca) ?>" class="input-search">
        <button type="submit" class="btn-primary">Buscar</button>
        <?php if ($busca): ?>
            <a href="index.php" class="link-clear">Limpar</a>
        <?php endif; ?>
    </form>

    <a href="criar.php" class="btn-primary btn-new">+ Novo Administrador</a>

    <br><br>

    <table border="1" cellpadding="8" class="table-admin">
        <thead>
            <tr>
                <th>ID</th>
                <th>Matrícula</th>
                <th>Nome</th>
                <th>Tipo</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($matriculas as $m): ?>
                <tr class="table-row <?= $m['usada'] ? 'used' : 'available' ?>">
                    <td><?= $m['id'] ?></td>
                    <td><?= htmlspecialchars($m['matricula']) ?></td>
                    <td><?= htmlspecialchars($m['nome'] ?? '-') ?></td>
                    <td><?= ucfirst($m['tipo']) ?></td>
                    <td><?= $m['usada'] ? 'Usada' : 'Disponível' ?></td>
                    <td>
                        <a href="visualizar.php?id=<?= $m['id'] ?>" class="action-link">Visualizar</a> |
                        <a href="editar.php?id=<?= $m['id'] ?>" class="action-link">Editar</a> |
                        <a href="deletar.php?id=<?= $m['id'] ?>" class="action-link" onclick="return confirm('Deseja excluir esta matrícula?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <a href="/public/php/painel_admin.php" class="btn-secondary">Voltar</a>

    <script src="/../../../public/recursos/js/painel_admin.js"></script>
</main>

