<?php
session_start();
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

$id = $_GET['id'] ?? null;

if ($id) {
    // Delete disciplina pelo id
    $stmt = $pdo->prepare("DELETE FROM disciplinas WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: index.php');
exit;
