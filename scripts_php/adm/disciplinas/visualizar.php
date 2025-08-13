<?php
require_once __DIR__ . '/../../../public/includes/header_admin.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

// Buscar disciplina com o nome do curso
$stmt = $pdo->prepare("
    SELECT d.*, c.nome AS nome_curso
    FROM disciplinas d
    JOIN cursos c ON d.curso_id = c.id
    WHERE d.id = ?
");
$stmt->execute([$id]);
$disciplina = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$disciplina) {
    echo "Disciplina não encontrada.";
    exit;
}
?>
<main class="form-create-main">
    <h1 class="form-create-title">Detalhes da Disciplina</h1>

    <ul class="form-create-info-list">
        <li>
            <strong class="form-create-label">Nome:</strong> 
            <span class="form-create-value"><?= htmlspecialchars($disciplina['nome']) ?></span>
        </li>
        <li>
            <strong class="form-create-label">Código:</strong> 
            <span class="form-create-value"><?= htmlspecialchars($disciplina['codigo']) ?></span>
        </li>
        <li>
            <strong class="form-create-label">Carga Horária:</strong> 
            <span class="form-create-value"><?= htmlspecialchars($disciplina['carga_horaria']) ?> horas</span>
        </li>
        <li>
            <strong class="form-create-label">Curso:</strong> 
            <span class="form-create-value"><?= htmlspecialchars($disciplina['nome_curso']) ?></span>
        </li>
    </ul>

    <p class="form-create-actions">
        <a href="index.php" class="form-create-btn-secondary">Voltar</a>
    </p>

    <script src="/../../../public/recursos/js/painel_admin.js"></script>
</main>



</body>
</html>
