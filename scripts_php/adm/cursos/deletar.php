<?php
session_start();
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "ID do curso não informado.";
    exit;
}

// Deleta o curso pelo ID
$stmt = $pdo->prepare("DELETE FROM cursos WHERE id = ?");
$stmt->execute([$id]);

// Redireciona para a lista após excluir
header("Location: index.php");
exit;
