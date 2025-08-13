<?php
require_once __DIR__ . '/../../../public/includes/header_admin.php';

$stmt = $pdo->query("
    SELECT p.id, u.nome, p.matricula, p.departamento, p.email, u.ativo
    FROM professores p
    JOIN usuarios u ON p.id = u.id
");
$professores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="page-main">
    <h1 class="page-title">Professores Cadastrados</h1>

    <a href="criar.php" class="btn-primary btn-new">+ Novo Professor</a>
    <br><br>

    <table border="1" cellpadding="8" class="table-admin">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Matrícula</th>
                <th>Departamento</th>
                <th>Email</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($professores as $prof): ?>
                <tr class="<?= $prof['ativo'] ? 'table-row available' : 'table-row used' ?>">
                    <td><?= htmlspecialchars($prof['nome']) ?></td>
                    <td><?= htmlspecialchars($prof['matricula']) ?></td>
                    <td><?= htmlspecialchars($prof['departamento']) ?></td>
                    <td><?= htmlspecialchars($prof['email']) ?></td>
                    <td class="status <?= $prof['ativo'] ? 'status-active' : 'status-inactive' ?>">
                        <?= $prof['ativo'] ? 'Ativo' : 'Inativo' ?>
                    </td>
                    <td>
                        <a href="visualizar.php?id=<?= $prof['id'] ?>" class="action-link">Visualizar</a> | 
                        <a href="editar.php?id=<?= $prof['id'] ?>" class="action-link">Editar</a> | 
                        <a href="deletar.php?id=<?= $prof['id'] ?>" class="action-link" onclick="return confirm('Tem certeza que deseja excluir este professor?')">Excluir</a> |
                        <?php if ($prof['ativo']): ?>
                            <a href="status.php?id=<?= $prof['id'] ?>&status=0" class="action-link" onclick="return confirm('Deseja desativar este professor?')">Desativar</a>
                        <?php else: ?>
                            <a href="status.php?id=<?= $prof['id'] ?>&status=1" class="action-link" onclick="return confirm('Deseja ativar este professor?')">Ativar</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <a href="/public/php/painel_admin.php" class="btn-secondary">Voltar</a>
</main>

<script src="/../../../public/recursos/js/painel_admin.js"></script>


</body>
</html>
