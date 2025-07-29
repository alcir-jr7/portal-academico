<?php
session_start();
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

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
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Curso</title>
</head>
<body>
    <h1>Detalhes do Curso</h1>
    <ul>
        <li><strong>Nome:</strong> <?= htmlspecialchars($curso['nome']) ?></li>
        <li><strong>Código:</strong> <?= htmlspecialchars($curso['codigo']) ?></li>
        <li><strong>Turno:</strong> <?= htmlspecialchars($curso['turno']) ?></li>
        <li><strong>Duração:</strong> <?= $curso['duracao_semestres'] ?> semestres</li>
        <li><strong>Coordenador:</strong> <?= htmlspecialchars($curso['coordenador_email'] ?? '—') ?></li>
    </ul>
    <a href="index.php">Voltar à lista</a>
</body>
</html>
