<?php
session_start();
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

// Deleta a turma pelo id
$stmt = $pdo->prepare("DELETE FROM turmas WHERE id = ?");
$stmt->execute([$id]);

header('Location: index.php');
exit;
