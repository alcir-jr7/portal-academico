<?php
session_start();
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "ID do professor não informado.";
    exit;
}

// Busca dados do professor e do usuário associado
$stmt = $pdo->prepare("
    SELECT p.*, u.nome, u.matricula 
    FROM professores p
    JOIN usuarios u ON p.id = u.id
    WHERE p.id = ?
");
$stmt->execute([$id]);
$professor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$professor) {
    echo "Professor não encontrado.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Professor</title>
</head>
<body>
    <h1>Detalhes do Professor</h1>
    <ul>
        <li><strong>Nome:</strong> <?= htmlspecialchars($professor['nome']) ?></li>
        <li><strong>Matrícula:</strong> <?= htmlspecialchars($professor['matricula']) ?></li>
        <li><strong>Departamento:</strong> <?= htmlspecialchars($professor['departamento']) ?></li>
        <li><strong>Email:</strong> <?= htmlspecialchars($professor['email']) ?></li>
    </ul>
    <a href="index.php">Voltar à lista</a>
</body>
</html>
