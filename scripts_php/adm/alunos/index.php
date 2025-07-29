<?php
session_start();
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

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

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Alunos</title>
</head>
<body>
    <h1>Alunos Cadastrados</h1>

    <form method="get" action="index.php">
        <input type="text" name="busca" placeholder="Buscar por nome ou matrícula" value="<?= htmlspecialchars($busca) ?>">
        <button type="submit">Buscar</button>
        <?php if ($busca): ?>
            <a href="index.php">Limpar</a>
        <?php endif; ?>
    </form>

    <br>

    <a href="criar.php">+ Novo Aluno</a>
    <br><br>

    <table border="1" cellpadding="8">
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
                <?php if ($aluno['ativo']): ?>
                    <tr style="background-color: #e0ffe0;">
                <?php else: ?>
                    <tr style="background-color: #ffe0e0;">
                <?php endif; ?>
                    <td><?= htmlspecialchars($aluno['nome']) ?></td>
                    <td><?= htmlspecialchars($aluno['matricula']) ?></td>
                    <td><?= htmlspecialchars($aluno['email']) ?></td>
                    <td><?= htmlspecialchars($aluno['periodo_entrada']) ?></td>
                    <td><?= htmlspecialchars($aluno['curso']) ?></td>
                    <td><?= $aluno['ativo'] ? 'Ativo' : 'Inativo' ?></td>
                    <td>
                        <a href="visualizar.php?id=<?= $aluno['id'] ?>">Visualizar</a> | 
                        <a href="editar.php?id=<?= $aluno['id'] ?>">Editar</a> | 
                        <a href="deletar.php?id=<?= $aluno['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir este aluno?')">Excluir</a> | 
                        <?php if ($aluno['ativo']): ?>
                            <a href="status.php?id=<?= $aluno['id'] ?>&status=0" onclick="return confirm('Deseja desativar este aluno?')">Desativar</a>
                        <?php else: ?>
                            <a href="status.php?id=<?= $aluno['id'] ?>&status=1" onclick="return confirm('Deseja ativar este aluno?')">Ativar</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
        <a href="/public/php/painel_admin.php">Voltar</a>
</body>
</html>
