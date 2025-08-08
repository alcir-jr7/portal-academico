<?php
// header_professor.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'professor') {
    header('Location: ../publico/login.php?tipo=professor');
    exit;
}

require_once(__DIR__ . '/../../aplicacao/config/conexao.php');

try {
    $stmt = $pdo->prepare("
        SELECT u.nome, p.departamento
        FROM usuarios u
        JOIN professores p ON u.id = p.id
        WHERE u.id = ? AND u.tipo = 'professor'
    ");
    $stmt->execute([$_SESSION['usuario_id']]);
    $professor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$professor) {
        session_destroy();
        header('Location: ../publico/login.php?tipo=professor');
        exit;
    }
} catch (Exception $e) {
    die("Erro ao acessar dados do professor: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Painel do Professor - iCampus</title>
    <link rel="icon" href="/public/recursos/images/icon.png" type="image/x-icon">
    <link rel="stylesheet" href="/public/recursos/css/painel_professor.css" />
</head>
<body>
    <aside id="sidebar" class="sidebar">
        <h1 class="menu">Menu</h1>
        <button class="close-btn" onclick="toggleSidebar()">✖</button>

        <a href="/scripts_php/prof/notas/index.php">
            <img src="/public/recursos/images/boletim.png" class="icon"> Gerenciar Notas
        </a>
        <a href="/scripts_php/prof/frequencia/index.php">
            <img src="/public/recursos/images/frequencia.png" class="icon"> Gerenciar Frequências
        </a>
        <a href="/scripts_php/prof//index.php">
            <img src="/public/recursos/images/turma.png" class="icon"> Minhas Turmas
        </a>
        <a href="/scripts_php/prof//index.php">
            <img src="/public/recursos/images/horario.png" class="icon"> Meus Horários
        </a>
    </aside>

    <header>
        <button class="menu-btn" onclick="toggleSidebar()">☰</button>
        <nav>
            <ul>
                <li><a href="/public/php/painel_professor.php">Home</a></li>
                <li><a href="/scripts_php/logout.php">Sair</a></li>
            </ul>
        </nav>
        <h1>Bem-vindo, <?php echo htmlspecialchars($professor['nome']); ?>!</h1>
    </header>
