<?php
session_start();
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

// Busca disciplinas com nome do curso
$stmt = $pdo->query("
    SELECT d.id, d.nome, d.codigo, d.carga_horaria, c.nome AS curso
    FROM disciplinas d
    JOIN cursos c ON d.curso_id = c.id
    ORDER BY c.nome, d.nome
");
$disciplinas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Lista de Disciplinas</title>
</head>
<body>
    <h1>Disciplinas Cadastradas</h1>

    <a href="criar.php">+ Nova Disciplina</a>
    <br><br>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Código</th>
                <th>Carga Horária</th>
                <th>Curso</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($disciplinas as $d): ?>
                <tr>
                    <td><?= htmlspecialchars($d['nome']) ?></td>
                    <td><?= htmlspecialchars($d['codigo']) ?></td>
                    <td><?= htmlspecialchars($d['carga_horaria']) ?></td>
                    <td><?= htmlspecialchars($d['curso']) ?></td>
                    <td>
                        <a href="visualizar.php?id=<?= $d['id'] ?>">Visualizar</a> |
                        <a href="editar.php?id=<?= $d['id'] ?>">Editar</a> |
                        <a href="deletar.php?id=<?= $d['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir esta disciplina?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <a href="/public/php/painel_admin.php">Voltar</a>
</body>
</html>
