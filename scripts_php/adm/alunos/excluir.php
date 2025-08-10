<?php
session_start();
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "ID do aluno não informado.";
    exit;
}

// Verifica se o aluno existe
$stmt = $pdo->prepare("SELECT * FROM alunos WHERE id = ?");
$stmt->execute([$id]);
$aluno = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$aluno) {
    echo "Aluno não encontrado.";
    exit;
}

// Deleta o usuário (vai deletar o aluno junto, pois há ON DELETE CASCADE)
$stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php");
exit;
