<?php
// aplicacao/controladores/alunoControl.php

session_start();

require_once __DIR__ . '/../config/conexao.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'aluno') {
    header('Location: ../../public/recursos/php/login.php?tipo=aluno');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT nome FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        session_destroy();
        header('Location: ../../public/recursos/php/login.php?tipo=aluno');
        exit;
    }

    // Passar dados para a view
    include __DIR__ . '/../visoes/painel_aluno.php';

} catch (Exception $e) {
    die("Erro: " . $e->getMessage());
}
?>
