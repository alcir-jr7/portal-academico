<?php
session_start();

$pdo = new PDO("mysql:host=localhost;dbname=portal_academico", "root", "");

// Pega dados do formulário
$usuario = $_POST['usuario'] ?? '';
$senha = $_POST['senha'] ?? '';

// Validação simples
if ($usuario === $adminUsuario && $senha === $adminSenha) {
    $_SESSION['admin_logado'] = true;
    header('Location: ../publico/painel_admin.php');
    exit;
} else {
    echo "Acesso negado. <a href='../publico/login.php'>Tente novamente</a>";
}
?>
