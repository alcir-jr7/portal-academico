<?php
session_start();
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

if (!isset($_GET['id'])) {
    echo "Turma não especificada.";
    exit;
}

$turma_id = (int) $_GET['id'];

// Buscar dados da turma, professor e disciplina
$stmt = $pdo->prepare("
    SELECT t.id, d.nome AS disciplina, d.codigo, 
           u.nome AS professor, t.semestre, t.horario
    FROM turmas t
    JOIN disciplinas d ON t.disciplina_id = d.id
    JOIN professores p ON t.professor_id = p.id
    JOIN usuarios u ON p.id = u.id
    WHERE t.id = ?
");
$stmt->execute([$turma_id]);
$turma = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$turma) {
    echo "Turma não encontrada.";
    exit;
}

// Buscar alunos da turma com nome e matrícula
$stmt = $pdo->prepare("
    SELECT u.nome AS nome_aluno, u.matricula
    FROM matriculas m
    JOIN alunos a ON m.aluno_id = a.id
    JOIN usuarios u ON a.id = u.id
    WHERE m.turma_id = ?
");
$stmt->execute([$turma_id]);
$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Detalhes da Turma</title>
</head>
<body>
    <h1>Detalhes da Turma</h1>
    
    <h2>Informações</h2>
    <p><strong>Disciplina:</strong> <?= htmlspecialchars($turma['disciplina']) ?> (<?= htmlspecialchars($turma['codigo']) ?>)</p>
    <p><strong>Professor:</strong> <?= htmlspecialchars($turma['professor']) ?></p>
    <p><strong>Semestre:</strong> <?= htmlspecialchars($turma['semestre']) ?></p>
    <p><strong>Horário:</strong> <?= htmlspecialchars($turma['horario']) ?></p>

    <h2>Alunos Matriculados</h2>
    <?php if (count($alunos) > 0): ?>
        <ul>
            <?php foreach ($alunos as $aluno): ?>
                <li><?= htmlspecialchars($aluno['nome_aluno']) ?> (<?= htmlspecialchars($aluno['matricula']) ?>)</li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Nenhum aluno matriculado nesta turma.</p>
    <?php endif; ?>

    <a href="index.php">Voltar à lista de turmas</a>
</body>
</html>
