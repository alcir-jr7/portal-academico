<?php
require_once __DIR__ . '/../../../public/includes/header_professor.php';

try {
    // Buscar turmas do professor
    $stmt = $pdo->prepare("
        SELECT 
            t.id AS turma_id,
            d.id AS disciplina_id,
            d.nome AS disciplina_nome,
            t.semestre,
            t.horario
        FROM turmas t
        JOIN disciplinas d ON t.disciplina_id = d.id
        WHERE t.professor_id = ?
        ORDER BY d.nome, t.semestre
    ");
    $stmt->execute([$_SESSION['usuario_id']]);
    $turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Erro ao buscar turmas do professor: " . $e->getMessage());
}
?>

<main>
    <div class="content-header">
        <h2>Minhas Turmas</h2>
    </div>

    <?php if (empty($turmas)): ?>
        <div class="empty-state">
            <h3>Você ainda não tem turmas vinculadas.</h3>
            <p>Entre em contato com a coordenação para mais informações.</p>
        </div>
    <?php else: ?>
        <div class="table-container">
            <table class="admin-table" border="1">
                <thead>
                    <tr>
                        <th>Disciplina</th>
                        <th>Semestre</th>
                        <th>Horário</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($turmas as $turma): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($turma['disciplina_nome']) ?></strong></td>
                            <td><?= htmlspecialchars($turma['semestre']) ?></td>
                            <td><?= htmlspecialchars($turma['horario']) ?></td>
                            <td class="actions">
                                <a href="registrar_frequencia.php?turma_id=<?= $turma['turma_id'] ?>" class="btn btn-primary" title="Registrar Frequência">Registrar Frequência</a>
                                <a href="visualizar_frequencia.php?turma_id=<?= $turma['turma_id'] ?>" class="btn btn-info" title="Visualizar Frequência">Visualizar Frequência</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="table-info">
            <p>Total de turmas: <strong><?= count($turmas) ?></strong></p>
        </div>
    <?php endif; ?>
</main>

<script src="/../../../public/recursos/js/painel_professor.js"></script>

</body>
</html>
