<?php
session_start();
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

// Buscar turmas com dados da disciplina e do professor
$stmt = $pdo->query("
    SELECT t.id, d.nome AS disciplina, p.id AS professor_id, u.nome AS professor, t.semestre, t.horario
    FROM turmas t
    JOIN disciplinas d ON t.disciplina_id = d.id
    JOIN professores p ON t.professor_id = p.id
    JOIN usuarios u ON p.id = u.id
");
$turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Lista de Turmas</title>
</head>
<body>
    <h1>Turmas Cadastradas</h1>

    <a href="criar.php">+ Nova Turma</a>
    <br><br>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Disciplina</th>
                <th>Professor</th>
                <th>Semestre</th>
                <th>Horário</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($turmas as $turma): ?>
                <tr>
                    <td><?= htmlspecialchars($turma['disciplina']) ?></td>
                    <td><?= htmlspecialchars($turma['professor']) ?></td>
                    <td><?= htmlspecialchars($turma['semestre']) ?></td>
                    <td><?= htmlspecialchars($turma['horario']) ?></td>
                    <td>
                        <a href="visualizar.php?id=<?= $turma['id'] ?>">Visualizar</a> |
                        <a href="editar.php?id=<?= $turma['id'] ?>">Editar</a> |
                        <a href="deletar.php?id=<?= $turma['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir esta turma?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
     <a href="/public/php/painel_admin.php">Voltar</a>
</body>
</html>
