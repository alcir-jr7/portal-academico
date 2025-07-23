<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// seu código aqui...

session_start();

// Verifica se o usuário está logado e é do tipo aluno
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'aluno') {
    header('Location: ../publico/login.php?tipo=aluno');
    exit;
}

require_once(__DIR__ . '/../../aplicacao/config/conexao.php');

try {
    // Busca o nome do aluno no banco usando o id da sessão
    $stmt = $pdo->prepare("SELECT nome FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        // Usuário não encontrado - desloga
        session_destroy();
        header('Location: ../publico/login.php?tipo=aluno');
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
    <link rel="stylesheet" href="/public/recursos/css/painel_aluno.css" />
</head>
<body>
    <header>
        <h1>Bem-vindo, <?php echo htmlspecialchars($usuario['nome']); ?>!</h1>
        <nav>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="/scripts_php/logout.php">Sair</a></li>
            </ul>
        </nav>
    </header>

    <main>
    <h2>Menu do Aluno</h2>
    <div class="painel-opcoes">
        <a href="boletim.php" class="card-opcao">
            <img src="/public/recursos/images/boletim.png" alt="Boletim">
            <span>Boletim</span>
        </a>
        <a href="frequencia.php" class="card-opcao">
            <img src="/public/recursos/images/frequencia.png" alt="Frequência">
            <span>Frequência</span>
        </a>
        <a href="horario.php" class="card-opcao">
            <img src="/public/recursos/images/horario.png" alt="Horário">
            <span>Horário</span>
        </a>
    </div>
</main>

</body>
</html>
