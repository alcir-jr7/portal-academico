<?php
session_start();
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

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

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Visualizar Turma</title>
</head>
<body>
    <h1>Detalhes da Turma</h1>
    <p><strong>Disciplina:</strong> <?= htmlspecialchars($turma['disciplina_nome']) ?> (<?= htmlspecialchars($turma['disciplina_codigo']) ?>)</p>
    <p><strong>Professor:</strong> <?= htmlspecialchars($turma['professor_nome']) ?> (Matricula: <?= htmlspecialchars($turma['professor_matricula']) ?>)</p>
    <p><strong>Semestre:</strong> <?= htmlspecialchars($turma['semestre']) ?></p>
    <p><strong>Horário:</strong> <?= htmlspecialchars($turma['horario']) ?></p>

    <a href="index.php">Voltar</a>
</body>
</html>
