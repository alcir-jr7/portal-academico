<?php
require_once __DIR__ . '/../../../public/includes/header_admin.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

// Busca turma com os dados da disciplina e do professor
$stmt = $pdo->prepare("
    SELECT t.id, t.semestre, t.horario,
           d.nome AS disciplina_nome, d.codigo AS disciplina_codigo,
           u.nome AS professor_nome, p.matricula AS professor_matricula
    FROM turmas t
    JOIN disciplinas d ON t.disciplina_id = d.id
    JOIN professores p ON t.professor_id = p.id
    JOIN usuarios u ON p.id = u.id
    WHERE t.id = ?
");
$stmt->execute([$id]);
$turma = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$turma) {
    echo "Turma não encontrada.";
    exit;
}
?>

<main class="form-create-main">
    <h1 class="form-create-title">Detalhes da Turma</h1>

    <div class="form-create-info">
        <p><strong class="form-create-label">Disciplina:</strong> <span class="form-create-value"><?= htmlspecialchars($turma['disciplina_nome']) ?> (<?= htmlspecialchars($turma['disciplina_codigo']) ?>)</span></p>
        <p><strong class="form-create-label">Professor:</strong> <span class="form-create-value"><?= htmlspecialchars($turma['professor_nome']) ?> (Matrícula: <?= htmlspecialchars($turma['professor_matricula']) ?>)</span></p>
        <p><strong class="form-create-label">Semestre:</strong> <span class="form-create-value"><?= htmlspecialchars($turma['semestre']) ?></span></p>
        <p><strong class="form-create-label">Horário:</strong> <span class="form-create-value"><?= htmlspecialchars($turma['horario']) ?></span></p>
    </div>

    <p class="form-create-actions">
        <a href="index.php" class="form-create-btn-secondary">Voltar</a>
    </p>
</main>

<script src="/../../../public/recursos/js/painel_admin.js"></script>

</body>
</html>
