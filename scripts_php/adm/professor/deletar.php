<?php
session_start();
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

// Verifica se foi passado um ID pela URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Exclui da tabela professores
    $stmt1 = $pdo->prepare("DELETE FROM professores WHERE id = ?");
    $stmt1->execute([$id]);

    // Exclui da tabela usuarios
    $stmt2 = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt2->execute([$id]);

    header("Location: index.php");
    exit;
} else {
    echo "ID inválido.";
}
