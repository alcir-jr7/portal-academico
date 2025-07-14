<?php
session_start();

// Verifica se o usuário está logado e é do tipo aluno
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'aluno') {
    header('Location: ../publico/login.php?tipo=aluno');
    exit;
}

require_once(__DIR__ . '/../config/conexao.php');

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
    <link rel="stylesheet" href="../public/recursos/css/painel_aluno.css" />
</head>
<body>
    <header>
        <h1>Bem-vindo, <?php echo htmlspecialchars($usuario['nome']); ?>!</h1>
        <nav>
            <ul>
                <li><a href="boletim.php">Boletim</a></li>
                <li><a href="frequencia.php">Frequência</a></li>
                <li><a href="horario.php">Horário</a></li>
                <li><a href="renovacao_matricula.php">Renovação de Matrícula</a></li>
                <li><a href="../scripts_php/logout.php">Sair</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <p>Escolha uma opção no menu para começar.</p>
    </main>
</body>
</html>
