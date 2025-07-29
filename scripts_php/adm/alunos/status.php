<?php
session_start();
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

$id = $_GET['id'] ?? null;
$status = $_GET['status'] ?? null;

if (!$id || !isset($status)) {
    die("ID ou status inválido.");
}

// Atualiza o campo ativo na tabela usuarios
$stmt = $pdo->prepare("UPDATE usuarios SET ativo = ? WHERE id = ?");
$stmt->execute([$status, $id]);

header("Location: index.php"); // Redireciona de volta para a lista
exit;
