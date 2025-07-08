<?php
session_start();

// Proteção da página
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Administrativo</title>
</head>
<body>
    <h1>Bem-vindo, Administrador!</h1>
    <p><a href="../logout.php">Sair</a></p>
</body>
</html>
