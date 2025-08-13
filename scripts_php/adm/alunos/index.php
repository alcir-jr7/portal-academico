<?php
require_once __DIR__ . '/../../../public/includes/header_admin.php';

$busca = $_GET['busca'] ?? '';

if ($busca) {
    // Consulta com filtro
    $stmt = $pdo->prepare("
        SELECT a.id, u.nome, u.matricula, a.email, a.periodo_entrada, c.nome AS curso, u.ativo
        FROM alunos a
        JOIN usuarios u ON a.id = u.id
        JOIN cursos c ON a.curso_id = c.id
        WHERE u.nome LIKE ? OR u.matricula LIKE ?
    ");
    $param = "%$busca%";
    $stmt->execute([$param, $param]);
} else {
    // Consulta normal sem filtro
    $stmt = $pdo->query("
        SELECT a.id, u.nome, u.matricula, a.email, a.periodo_entrada, c.nome AS curso, u.ativo
        FROM alunos a
        JOIN usuarios u ON a.id = u.id
        JOIN cursos c ON a.curso_id = c.id
    ");
}

$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="page-main">
    <h1 class="page-title">Alunos Cadastrados</h1>

    <form method="get" action="index.php" class="form-search">
        <input type="text" name="busca" placeholder="Buscar por nome ou matrícula" value="<?= htmlspecialchars($busca) ?>" class="input-search">
        <button type="submit" class="btn-primary">Buscar</button>
        <?php if ($busca): ?>
            <a href="index.php" class="link-clear">Limpar</a>
        <?php endif; ?>
    </form>

    <br>

    <a href="criar.php" class="btn-primary btn-new">+ Novo Aluno</a>
    <br><br>

    <table border="1" cellpadding="8" class="table-admin">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Matrícula</th>
                <th>Email</th>
                <th>Período de Entrada</th>
                <th>Curso</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($alunos as $aluno): ?>
                <tr class="table-row <?= $aluno['ativo'] ? 'available' : 'used' ?>">
                    <td><?= htmlspecialchars($aluno['nome']) ?></td>
                    <td><?= htmlspecialchars($aluno['matricula']) ?></td>
                    <td><?= htmlspecialchars($aluno['email']) ?></td>
                    <td><?= htmlspecialchars($aluno['periodo_entrada']) ?></td>
                    <td><?= htmlspecialchars($aluno['curso']) ?></td>
                    <td class="status <?= $aluno['ativo'] ? 'status-active' : 'status-inactive' ?>">
                        <?= $aluno['ativo'] ? 'Ativo' : 'Inativo' ?>
                    </td>
                    <td>
                        <a href="visualizar.php?id=<?= $aluno['id'] ?>" class="action-link">Visualizar</a> | 
                        <a href="editar.php?id=<?= $aluno['id'] ?>" class="action-link">Editar</a> | 
                        <a href="deletar.php?id=<?= $aluno['id'] ?>" class="action-link" onclick="return confirm('Tem certeza que deseja excluir este aluno?')">Excluir</a> | 
                        <?php if ($aluno['ativo']): ?>
                            <a href="status.php?id=<?= $aluno['id'] ?>&status=0" class="action-link" onclick="return confirm('Deseja desativar este aluno?')">Desativar</a>
                        <?php else: ?>
                            <a href="status.php?id=<?= $aluno['id'] ?>&status=1" class="action-link" onclick="return confirm('Deseja ativar este aluno?')">Ativar</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <a href="/public/php/painel_admin.php" class="btn-secondary">Voltar</a>

    <script src="/../../../public/recursos/js/painel_admin.js"></script>
</main>


</body>
</html>
