<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Verifica se o usuário está logado e é do tipo admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header('Location: /public/php/login.php?tipo=admin');
    exit;
}

require_once(__DIR__ . '/../../aplicacao/config/conexao.php');

try {
    // Busca o nome do administrador no banco usando o id da sessão
    $stmt = $pdo->prepare("SELECT nome FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        // Usuário não encontrado - desloga
        session_destroy();
        header('Location: /public/php/login.php?tipo=admin');
        exit;
    }

    // Busca estatísticas rápidas para o dashboard
    $stats = [];
    
    try {
        // Total de usuários ativos
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios WHERE ativo = 1");
        $stats['usuarios'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Total de cursos
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM cursos");
        $stats['cursos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Total de alunos ativos
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo = 'aluno' AND ativo = 1");
        $stats['alunos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Total de professores ativos
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo = 'professor' AND ativo = 1");
        $stats['professores'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Total de disciplinas
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM disciplinas");
        $stats['disciplinas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Total de turmas
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM turmas");
        $stats['turmas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
    } catch (Exception $e) {
        // Em caso de erro, define valores padrão
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
    <link rel="stylesheet" href="/public/recursos/css/painel_admin.css" />
</head>
<body>
    <header>
        <h1>Painel Administrativo - <?php echo htmlspecialchars($usuario['nome']); ?></h1>
        <nav>
            <ul>
                <li><a href="/public/php/painel_admin.php">Home</a></li>
                <li><a href="/scripts_php/logout.php">Sair</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <!-- Estatísticas rápidas -->
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-number"><?php echo $stats['usuarios']; ?></span>
                <span class="stat-label">Usuários</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?php echo $stats['alunos']; ?></span>
                <span class="stat-label">Alunos</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?php echo $stats['professores']; ?></span>
                <span class="stat-label">Professores</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?php echo $stats['cursos']; ?></span>
                <span class="stat-label">Cursos</span>
            </div>
        </div>

        <h2>Gerenciamento do Sistema</h2>
        <div class="painel-opcoes">
            <!-- Gerenciamento de Usuários -->
            <a href="/public/php/admin/usuarios/listar.php" class="card-opcao">
                <img src="/public/recursos/images/user.png" alt="Usuários">
                <span>Usuários</span>
                <div class="description">Gerenciar contas de usuários do sistema</div>
            </a>

            <!-- Gerenciamento de Alunos -->
            <a href="/scripts_php/adm/alunos/php/index.php" class="card-opcao">
                <img src="/public/recursos/images/aluno.png" alt="Alunos">
                <span>Alunos</span>
                <div class="description">Cadastro e gestão de alunos</div>
            </a>

            <!-- Gerenciamento de Professores -->
            <a href="/public/php/admin/professores/listar.php" class="card-opcao">
                <img src="/public/recursos/images/professor.png" alt="Professores">
                <span>Professores</span>
                <div class="description">Cadastro e gestão de professores</div>
            </a>

            <!-- Gerenciamento de Cursos -->
            <a href="/public/php/admin/cursos/listar.php" class="card-opcao">
                <img src="/public/recursos/images/cursos.png" alt="Cursos">
                <span>Cursos</span>
                <div class="description">Cadastro e gestão de cursos</div>
            </a>

            <!-- Gerenciamento de Disciplinas -->
            <a href="/public/php/admin/disciplinas/listar.php" class="card-opcao">
                <img src="/public/recursos/images/disciplinas.png" alt="Disciplinas">
                <span>Disciplinas</span>
                <div class="description">Cadastro de disciplinas e matérias</div>
            </a>

            <!-- Gerenciamento de Turmas -->
            <a href="/public/php/admin/turmas/listar.php" class="card-opcao">
                <img src="/public/recursos/images/turma.png" alt="Turmas">
                <span>Turmas</span>
                <div class="description">Criação e gestão de turmas</div>
            </a>
        </div>
    </main>

</body>
</html>