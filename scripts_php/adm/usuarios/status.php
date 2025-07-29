<?php
session_start();
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

$id = $_GET['id'] ?? null;
$status = $_GET['status'] ?? null;

if (!$id || !isset($status)) {
    echo "Parâmetros inválidos.";
    exit;
}

// Atualiza o campo ativo do usuário
$stmt = $pdo->prepare("UPDATE usuarios SET ativo = ? WHERE id = ?");
$stmt->execute([$status, $id]);

header("Location: index.php");
exit;
