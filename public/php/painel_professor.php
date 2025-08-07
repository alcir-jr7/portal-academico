<?php
// Ativa a exibição de erros para depuração (remover em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inicia a sessão PHP
session_start();

// --- Verificação de Autenticação e Tipo de Usuário ---
// Redireciona se o usuário não estiver logado ou não for do tipo 'professor'
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'professor') {
    // Redireciona para a página de login, indicando o tipo esperado
    header('Location: ../publico/login.php?tipo=professor');
    exit; // Garante que o script pare de executar após o redirecionamento
}

// --- Inclusão do Arquivo de Conexão com o Banco de Dados ---
// Assume que 'conexao.php' está no caminho correto e retorna uma instância de PDO
require_once(__DIR__ . '/../../aplicacao/config/conexao.php');

try {
    // --- Busca o nome e departamento do professor no banco de dados ---
    // Usa o ID do usuário armazenado na sessão
    $stmt = $pdo->prepare("
        SELECT u.nome, p.departamento
        FROM usuarios u
        JOIN professores p ON u.id = p.id
        WHERE u.id = ? AND u.tipo = 'professor'
    ");
    $stmt->execute([$_SESSION['usuario_id']]);
    $professor = $stmt->fetch(PDO::FETCH_ASSOC);

    // --- Verifica se o professor foi encontrado ---
    if (!$professor) {
        // Se o professor não for encontrado (dados inconsistentes), destrói a sessão e redireciona para o login
        session_destroy();
        header('Location: ../publico/login.php?tipo=professor');
        exit;
    }
} catch (Exception $e) {
    // Em caso de erro na consulta ao banco de dados, exibe uma mensagem de erro
    die("Erro ao acessar dados do professor: " . $e->getMessage());
}

// --- HTML da Página do Painel do Professor ---
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="/public/recursos/css/painel_professor.css" />
   
    <title>Painel do Professor - iCampus</title>
</head>
    <body>
    <!-- Sidebar expansiva -->
    <aside id="sidebar" class="sidebar">
        <h1 class="menu">Menu</h1>
        <button class="close-btn" onclick="toggleSidebar()">✖</button>

        <a href="/scripts_php/adm/usuarios/index.php">
            <img src="/public/recursos/images/boletim.png"  class="icon"> Gerenciar Notas
        </a>
        <a href="/scripts_php/adm/alunos/index.php">
            <img src="/public/recursos/images/frequencia.png" class="icon"> Gerenciar Frequências
        </a>
        <a href="/scripts_php/adm/professor/index.php">
            <img src="/public/recursos/images/turma.png" class="icon"> Minhas Turmas
        </a>
        <a href="/scripts_php/adm/professor/index.php">
            <img src="/public/recursos/images/horario.png" class="icon"> Meus Horários
        </a>
    </aside>
        <header>
            <button class="menu-btn" onclick="toggleSidebar()">☰</button>
            <nav>
                <ul>
                    <li><a href="painel_professor.php">Home</a></li>
                    <li><a href="/scripts_php/logout.php">Sair</a></li>
                </ul>
            </nav>
            <h1>Bem-vindo, <?php echo htmlspecialchars($professor['nome']); ?>!</h1>
        </header>

        <main>
            <h2>Menu do Professor</h2>
            <div class="painel-opcoes">
                <a href="/scripts_php/prof/notas/index.php" class="card-opcao">
                    <!-- Ícone para Gerenciar Notas -->
                    <img src="/public/recursos/images/boletim-p.png" alt=" Notas">
                    <span>Notas</span>
                    <div class="description">Gerenciamento das notas dos Alunos</div>
                </a>
                <a href="/scripts_php/prof/frequencia/index.php" class="card-opcao">
                    <!-- Ícone para Gerenciar Frequência -->
                    <img src="/public/recursos/images/frequencia-p.png" alt=" Frequência">
                    <span>Frequência</span>
                    <div class="description">Gerenciamento das Frequência</div>
                </a>
                <a href="minhas_turmas.php" class="card-opcao">
                    <!-- Ícone para Minhas Turmas -->
                    <img src="/public/recursos/images/turma-p.png" alt=" Turmas">
                    <span> Turmas</span>
                    <div class="description">Gerenciamento das minhas turmas</div>
                </a>
                <a href="meu_horario.php" class="card-opcao">
                    <!-- Ícone para Meu Horário -->
                    <img src="/public/recursos/images/horario-p.png" alt=" Horário">
                    <span> Horário</span>
                    <div class="description">Gerenciamento dos meus Horários</div>
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
                    <h2>Médias das Turmas</h2>
                    <canvas id="statsChart" width="400" height="250"></canvas>
                </div>
            </div>
        </main>
        <script src="/public/recursos/js/painel_professor.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </body>
</html>
