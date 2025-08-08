<?php
require_once __DIR__ . '/../../../public/includes/header_admin.php';

// Seleciona todos os cursos com nome do coordenador
$stmt = $pdo->query("
    SELECT cursos.*, professores.email AS coordenador_email
    FROM cursos
    LEFT JOIN professores ON cursos.coordenador_id = professores.id
");
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <div class="content-header">
        <h1>Gerenciamento de Cursos</h1>
        <nav class="admin-nav">
            <ul>
                <li><a href="criar.php" class="btn-create">➕ Novo Curso</a></li>
            </ul>
        </nav>
    </div>

    <h2>Cursos Cadastrados</h2>

    <?php if (empty($cursos)): ?>
        <div class="empty-state">
            <h3>Nenhum curso cadastrado</h3>
            <p>Comece adicionando o primeiro curso ao sistema.</p>
            <a href="criar.php" class="btn btn-primary">Cadastrar Primeiro Curso</a>
        </div>
    <?php else: ?>
        <div class="table-container">
            <table border="1" class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome do Curso</th>
                        <th>Código</th>
                        <th>Turno</th>
                        <th>Duração</th>
                        <th>Coordenador</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cursos as $curso): ?>
                        <tr>
                            <td><?= $curso['id'] ?></td>
                            <td><strong><?= htmlspecialchars($curso['nome']) ?></strong></td>
                            <td><code><?= htmlspecialchars($curso['codigo']) ?></code></td>
                            <td><?= htmlspecialchars($curso['turno']) ?></td>
                            <td><?= $curso['duracao_semestres'] ?> semestres</td>
                            <td><?= htmlspecialchars($curso['coordenador_email'] ?? 'Não definido') ?></td>
                            <td class="actions">
                                <a href="visualizar.php?id=<?= $curso['id'] ?>" class="btn btn-info" title="Visualizar">Visualizar</a>
                                <a href="atualizar.php?id=<?= $curso['id'] ?>" class="btn btn-warning" title="Editar">Editar</a>
                                <a href="deletar.php?id=<?= $curso['id'] ?>" 
                                   class="btn btn-danger" 
                                   title="Excluir"
                                   onclick="return confirm('⚠️ Tem certeza que deseja excluir o curso \'<?= htmlspecialchars($curso['nome']) ?>\'?\n\nEsta ação não pode ser desfeita!');">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="table-info">
            <p>Total de cursos cadastrados: <strong><?= count($cursos) ?></strong></p>
        </div>
        <a href="/public/php/painel_admin.php">Voltar</a>
    <?php endif; ?>

    <script src="/../../../public/recursos/js/painel_admin.js"></script>
</main>

</body>
</html>
