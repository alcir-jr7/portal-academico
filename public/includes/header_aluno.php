<?php
// header_aluno.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'aluno') {
    header('Location: ../public/php/login.php?tipo=aluno');
    exit;
}

require_once(__DIR__ . '/../../aplicacao/config/conexao.php');

try {
    $stmt = $pdo->prepare("SELECT nome FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        session_destroy();
        header('Location: ../public/php/login.php?tipo=aluno');
        exit;
    }
} catch (Exception $e) {
    die("Erro ao acessar dados do usuário: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Painel do Aluno - iCampus</title>
    <link rel="icon" href="/public/recursos/images/icon.png" type="image/x-icon">
    <link rel="stylesheet" href="/public/recursos/css/painel_aluno.css" />
</head>
<body>
    <aside id="sidebar" class="sidebar">
        <h1 class="menu">Menu</h1>
        <button class="close-btn" onclick="toggleSidebar()">✖</button>

        <a href="/scripts_php/aluno/boletim.php">
            <img src="/public/recursos/images/boletim.png"  class="icon"> Boletim
        </a>
        <a href="/scripts_php/aluno/frequencia.php">
            <img src="/public/recursos/images/frequencia.png" class="icon"> Frequência
        </a>
        <a href="/scripts_php/aluno/">
            <img src="/public/recursos/images/horario.png" class="icon"> Horários
        </a>
    </aside>

    <header>
        <button class="menu-btn" onclick="toggleSidebar()">☰</button>
        <nav>
            <ul>
                <li><a href="/public/php/painel_aluno.php">Home</a></li>
                <li><a href="/scripts_php/logout.php">Sair</a></li>
            </ul>
        </nav>
        <h1>Bem-vindo, <?php echo htmlspecialchars($usuario['nome']); ?>!</h1>
    </header>
