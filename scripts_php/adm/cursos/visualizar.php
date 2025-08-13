<?php
require_once __DIR__ . '/../../../public/includes/header_admin.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "ID do curso não informado.";
    exit;
}

$stmt = $pdo->prepare("SELECT cursos.*, professores.email AS coordenador_email FROM cursos LEFT JOIN professores ON cursos.coordenador_id = professores.id WHERE cursos.id = ?");
$stmt->execute([$id]);
$curso = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$curso) {
    echo "Curso não encontrado.";
    exit;
}
?>

<main class="form-create-main">
    <h1 class="form-create-title">Detalhes do Curso</h1>

    <ul class="form-create-info-list">
        <li><strong class="form-create-label">Nome:</strong> <span class="form-create-value"><?= htmlspecialchars($curso['nome']) ?></span></li>
        <li><strong class="form-create-label">Código:</strong> <span class="form-create-value"><?= htmlspecialchars($curso['codigo']) ?></span></li>
        <li><strong class="form-create-label">Turno:</strong> <span class="form-create-value"><?= htmlspecialchars($curso['turno']) ?></span></li>
        <li><strong class="form-create-label">Duração:</strong> <span class="form-create-value"><?= $curso['duracao_semestres'] ?> semestres</span></li>
        <li><strong class="form-create-label">Coordenador:</strong> <span class="form-create-value"><?= htmlspecialchars($curso['coordenador_email'] ?? '—') ?></span></li>
    </ul>

    <p class="form-create-actions">
        <a href="index.php" class="form-create-btn-secondary">Voltar à lista</a>
    </p>

    <script src="/../../../public/recursos/js/painel_admin.js"></script>
</main>


</body>
</html>
