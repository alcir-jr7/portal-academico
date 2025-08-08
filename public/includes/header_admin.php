<?php
// Exibe erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Verifica login e tipo de usuário
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header('Location: /public/php/login.php?tipo=admin');
    exit;
}

// Conexão com banco
require_once(__DIR__ . '/../../aplicacao/config/conexao.php');

try {
    // Busca nome do admin
    $stmt = $pdo->prepare("SELECT nome FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        session_destroy();
        header('Location: /public/php/login.php?tipo=admin');
        exit;
    }

    // Estatísticas
    $stats = [];
    try {
        $stats['usuarios']     = $pdo->query("SELECT COUNT(*) as total FROM usuarios WHERE ativo = 1")->fetch(PDO::FETCH_ASSOC)['total'];
        $stats['cursos']       = $pdo->query("SELECT COUNT(*) as total FROM cursos")->fetch(PDO::FETCH_ASSOC)['total'];
        $stats['alunos']       = $pdo->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo = 'aluno' AND ativo = 1")->fetch(PDO::FETCH_ASSOC)['total'];
        $stats['professores']  = $pdo->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo = 'professor' AND ativo = 1")->fetch(PDO::FETCH_ASSOC)['total'];
        $stats['disciplinas']  = $pdo->query("SELECT COUNT(*) as total FROM disciplinas")->fetch(PDO::FETCH_ASSOC)['total'];
        $stats['turmas']       = $pdo->query("SELECT COUNT(*) as total FROM turmas")->fetch(PDO::FETCH_ASSOC)['total'];
    } catch (Exception $e) {
        $stats = [
            'usuarios' => 0,
            'cursos' => 0,
            'alunos' => 0,
            'professores' => 0,
            'disciplinas' => 0,
            'turmas' => 0
        ];
    }
} catch (Exception $e) {
    die("Erro ao acessar dados do usuário: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Painel do Administrador - iCampus</title>
    <link rel="icon" href="/public/recursos/images/icon.png" type="image/x-icon">
    <link rel="stylesheet" href="/public/recursos/css/painel_admin.css" />
</head>
<body>
    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar">
        <h1 class="Admin">Administração</h1>
        <button class="close-btn" onclick="toggleSidebar()">✖</button>

        <a href="/scripts_php/adm/usuarios/index.php">
            <img src="/public/recursos/images/user.png" class="icon"> Usuários
        </a>
        <a href="/scripts_php/adm/alunos/index.php">
            <img src="/public/recursos/images/aluno.png" class="icon"> Alunos
        </a>
        <a href="/scripts_php/adm/professor/index.php">
            <img src="/public/recursos/images/professor.png" class="icon"> Professores
        </a>
        <a href="/scripts_php/adm/cursos/index.php">
            <img src="/public/recursos/images/cursos.png" class="icon"> Cursos
        </a>
        <a href="/scripts_php/adm/disciplinas/index.php">
            <img src="/public/recursos/images/disciplinas.png" class="icon"> Disciplinas
        </a>
        <a href="/scripts_php/adm/turmas/index.php">
            <img src="/public/recursos/images/turma.png" class="icon"> Turmas
        </a>
        <a href="/scripts_php/adm/matriculas/index.php">
            <img src="/public/recursos/images/matriculas.png" class="icon"> Matrículas
        </a>
        
    </aside>

    <!-- Header -->
    <header>
        <button class="menu-btn" onclick="toggleSidebar()">☰</button>
        <nav>
            <ul>
                <li><a href="/public/php/painel_admin.php">Home</a></li>
                <li><a href="/scripts_php/logout.php">Sair</a></li>
            </ul>
        </nav>
        <h1>Administrador - <?php echo htmlspecialchars($usuario['nome']); ?></h1>
    </header>


