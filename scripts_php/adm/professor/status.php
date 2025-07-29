<?php
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $novoStatus = $_GET['status'] == '1' ? 1 : 0;

    $stmt = $pdo->prepare("UPDATE usuarios SET ativo = ? WHERE id = ?");
    if ($stmt->execute([$novoStatus, $id])) {
        header("Location: index.php");
        exit;
    } else {
        echo "Erro ao atualizar status.";
    }
} else {
    echo "Parâmetros inválidos.";
}
