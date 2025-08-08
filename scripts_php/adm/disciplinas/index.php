<?php
require_once __DIR__ . '/../../../public/includes/header_admin.php';

// Busca disciplinas com nome do curso
$stmt = $pdo->query("
    SELECT d.id, d.nome, d.codigo, d.carga_horaria, c.nome AS curso
    FROM disciplinas d
    JOIN cursos c ON d.curso_id = c.id
    ORDER BY c.nome, d.nome
");
$disciplinas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <h1>Disciplinas Cadastradas</h1>

    <a href="criar.php" class="btn btn-primary">+ Nova Disciplina</a>
    <br><br>

    <?php if (empty($disciplinas)): ?>
        <p>Nenhuma disciplina cadastrada.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" class="admin-table">
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
                        <td class="actions">
                            <a href="visualizar.php?id=<?= $d['id'] ?>" class="btn btn-info">Visualizar</a> |
                            <a href="editar.php?id=<?= $d['id'] ?>" class="btn btn-warning">Editar</a> |
                            <a href="deletar.php?id=<?= $d['id'] ?>" class="btn btn-danger"
                               onclick="return confirm('Tem certeza que deseja excluir esta disciplina?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <br>
    <a href="/public/php/painel_admin.php" class="btn btn-secondary">Voltar</a>

    <script src="/../../../public/recursos/js/painel_admin.js"></script>
</main>

</body>
</html>
