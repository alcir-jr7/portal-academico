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

<main class="page-main">
    <div class="page-header">
        <h1 class="page-title">Disciplinas Cadastradas</h1>
    </div>
        <a href="criar.php" class="btn-primary btn-new">+ Nova Disciplina</a>

    <?php if (empty($disciplinas)): ?>
        <p>Nenhuma disciplina cadastrada.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" class="table-admin">
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
                            <a href="visualizar.php?id=<?= $d['id'] ?>" class="action-link">Visualizar</a> |
                            <a href="editar.php?id=<?= $d['id'] ?>" class="action-link">Editar</a> |
                            <a href="deletar.php?id=<?= $d['id'] ?>" class="action-link"
                               onclick="return confirm('Tem certeza que deseja excluir esta disciplina?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <br>
    <a href="/public/php/painel_admin.php" class="btn-secondary">Voltar</a>

    <script src="/../../../public/recursos/js/painel_admin.js"></script>
</main>


</body>
</html>
