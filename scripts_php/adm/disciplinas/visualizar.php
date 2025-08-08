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

<main>
    <h1>Detalhes da Disciplina</h1>

    <p><strong>Nome:</strong> <?= htmlspecialchars($disciplina['nome']) ?></p>
    <p><strong>Código:</strong> <?= htmlspecialchars($disciplina['codigo']) ?></p>
    <p><strong>Carga Horária:</strong> <?= htmlspecialchars($disciplina['carga_horaria']) ?> horas</p>
    <p><strong>Curso:</strong> <?= htmlspecialchars($disciplina['nome_curso']) ?></p>

    <p><a href="index.php" class="btn btn-secondary">Voltar</a></p>

    <script src="/../../../public/recursos/js/painel_admin.js"></script>
</main>

</body>
</html>
