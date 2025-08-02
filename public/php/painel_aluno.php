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
    <!-- Sidebar expansiva -->
    <aside id="sidebar" class="sidebar">
    <h1 class="menu">Menu</h1>
    <button class="close-btn" onclick="toggleSidebar()">✖</button>

    <a href="/scripts_php/adm/usuarios/index.php">
        <img src="/public/recursos/images/boletim.png"  class="icon"> Boletim
    </a>
    <a href="/scripts_php/adm/alunos/index.php">
        <img src="/public/recursos/images/frequencia.png" class="icon"> Frequência
    </a>
    <a href="/scripts_php/adm/professor/index.php">
        <img src="/public/recursos/images/horario.png" class="icon"> Horários
    </a>
</aside>
    <header>
        <button class="menu-btn" onclick="toggleSidebar()">☰</button>
        <nav>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="/scripts_php/logout.php">Sair</a></li>
            </ul>
        </nav>
         <h1>Bem-vindo, <?php echo htmlspecialchars($usuario['nome']); ?>!</h1>
    </header>

    <main>
    <h2>Menu do Aluno</h2>
    <div class="painel-opcoes">
        <a href="boletim.php" class="card-opcao">
            <img src="/public/recursos/images/boletim-p.png" alt="Boletim">
            <span>Boletim</span>
        </a>
        <a href="frequencia.php" class="card-opcao">
            <img src="/public/recursos/images/frequencia-p.png" alt="Frequência">
            <span>Frequência</span>
        </a>
        <a href="horario.php" class="card-opcao">
            <img src="/public/recursos/images/horario-p.png" alt="Horário">
            <span>Horário</span>
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
                <h2>Gráficos de Notas</h2>
                <canvas id="statsChart" width="400" height="250"></canvas>
            </div>
        </div>

    </main>
        <script src="../recursos/js/painel_aluno.js"></script>
    </body>
</html>
