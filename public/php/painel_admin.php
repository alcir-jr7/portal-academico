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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Sidebar expansiva -->
    <aside id="sidebar" class="sidebar">
    <h1 class="Admin">Administração</h1>
    <button class="close-btn" onclick="toggleSidebar()">✖</button>

    <a href="/scripts_php/adm/usuarios/index.php">
        <img src="/public/recursos/images/user.png"  class="icon"> Usuários
    </a>
    <a href="/scripts_php/adm/alunos/index.php">
        <img src="/public/recursos/images/aluno.png" class="icon"> Alunos
    </a>
    <a href="/scripts_php/adm/professor/index.php">
        <img src="/public/recursos/images/professor.png" class="icon"> Professores
    </a>
    <a href="/scripts_php/adm/cursos/index.php">
        <img src="/public/recursos/images/cursos.png"class="icon"> Cursos
    </a>
    <a href="/scripts_php/adm/disciplinas/index.php">
        <img src="/public/recursos/images/disciplinas.png" class="icon"> Disciplinas
    </a>
    <a href="/scripts_php/adm/turmas/index.php">
        <img src="/public/recursos/images/turma.png" class="icon"> Turmas
    </a>
    <a href="/scripts_php/adm/matriculas/index.php">
        <img src="/public/recursos/images/matriculas.png"class="icon"> Matrículas
    </a>
</aside>

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

    <main>
        

        <h2>Gerenciamento do Sistema</h2>
        <div class="painel-opcoes">
            <!-- Gerenciamento de Usuários -->
            <a href="/scripts_php/adm/usuarios/index.php" class="card-opcao">
                <img src="/public/recursos/images/user-p.png" alt="Usuários">
                <span>Usuários</span>
                <div class="description">Gerenciar contas de usuários do sistema</div>
            </a>
           
            <!-- Gerenciamento de Disciplinas -->
            <a href="/scripts_php/adm/disciplinas/index.php" class="card-opcao">
                <img src="/public/recursos/images/disciplina-p.png" alt="Disciplinas">  
                <span>Disciplinas</span>
                <div class="description">Cadastro e gestão de disciplinas</div>
            </a>

            <!-- Gerenciamento de Cursos -->
            <a href="/scripts_php/adm/cursos/index.php" class="card-opcao">
                <img src="/public/recursos/images/curso-p.png" alt="Cursos">
                <span>Cursos</span>
                <div class="description">Cadastro e gestão de cursos</div>
            </a>
            
            <!-- Gerenciamento de Matrículas -->
            <a href="/scripts_php/adm/matriculas/index.php" class="card-opcao">
                <img src="/public/recursos/images/matricular-p.png" alt="Matrículas">
                <span>Matrículas</span>
                <div class="description">Controle de matrículas em turmas</div>
            </a>

        </div>
        <div class="dashboard-linha">
            <div class="calendario-widget">
                 <table id="calendario">
                    <thead>
                         <tr>
                            <th colspan="7" id="mesAno"></th>   
                        </tr>
                        <tr>
                            <th>Dom</th><th>Seg</th><th>Ter</th><th>Qua</th><th>Qui</th><th>Sex</th><th>Sáb</th>
                        </tr>
                    </thead>
                    <tbody id="diasCalendario"></tbody>
                </table>
            </div>

            <div class="grafico-container">
                <h2>Estatísticas Rápidas</h2>
                <canvas id="statsChart" width="400" height="250"></canvas>
            </div>
        </div>
     
    </main>
<script src="../recursos/js/painel_admin.js"></script>
<!-- Biblioteca Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Passa os dados PHP para o JavaScript -->
<script>
    const statsData = {
        usuarios: <?php echo $stats['usuarios']; ?>,
        alunos: <?php echo $stats['alunos']; ?>,
        professores: <?php echo $stats['professores']; ?>,
        cursos: <?php echo $stats['cursos']; ?>
    };
</script>

<!-- Script separado -->
<script src="/public/js/statsChart.js"></script>


</body>
</html>