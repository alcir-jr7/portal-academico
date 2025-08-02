<?php
session_start();
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit;
}

// Verifica se existe
$stmt = $pdo->prepare("SELECT * FROM matriculas_academicas WHERE id = ?");
$stmt->execute([$id]);
$matricula = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$matricula) {
    echo "Matrícula não encontrada.";
    exit;
}

// Confirma exclusão
if (isset($_GET['confirm']) && $_GET['confirm'] === 'sim') {
    $stmt = $pdo->prepare("DELETE FROM matriculas_academicas WHERE id = ?");
    if ($stmt->execute([$id])) {
        header('Location: index.php');
        exit;
    } else {
        echo "Erro ao excluir.";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Excluir Matrícula Acadêmica</title>
</head>
<body>
    <h1>Excluir Matrícula Acadêmica</h1>
    <p>Tem certeza que deseja excluir a matrícula <strong><?= htmlspecialchars($matricula['matricula']) ?></strong> do tipo <strong><?= htmlspecialchars($matricula['tipo']) ?></strong>?</p>

    <a href="?id=<?= $id ?>&confirm=sim">Sim, excluir</a> |
    <a href="index.php">Cancelar</a>
</body>
</html>
