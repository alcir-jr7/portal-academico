<?php
// aplicacao/visoes/painel_aluno.php

session_start();

// Verifica se o usuário está logado e é do tipo aluno
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'aluno') {
    header('Location: /public/php/login.php?tipo=aluno');
    exit;
}

require_once __DIR__ . '/../config/conexao.php';

// Buscar dados do aluno no banco
try {
    $stmt = $pdo->prepare("SELECT nome FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        session_destroy();
        header('Location: /public/recursos/php/login.php?tipo=aluno');
        exit;
    }
} catch (Exception $e) {
    die("Erro ao carregar dados do usuário: " . $e->getMessage());
}

?>

<?php include_once __DIR__ . '/inclui/cabecalho.php'; ?>

<h2>Bem-vindo, <?php echo htmlspecialchars($usuario['nome']); ?>!</h2>

<nav>
    <ul>
        <li><a href="/public/recursos/php/boletim.php">Boletim</a></li>
        <li><a href="/public/recursos/php/frequencia.php">Frequência</a></li>
        <li><a href="/public/recursos/php/horario.php">Horário</a></li>
        <li><a href="/public/recursos/php/renovacao_matricula.php">Renovação de Matrícula</a></li>
        <li><a href="/scripts_php/logout.php">Sair</a></li>
    </ul>
</nav>

<?php include_once __DIR__ . '/inclui/rodape.php'; ?>
