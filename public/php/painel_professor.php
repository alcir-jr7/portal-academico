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
    <!-- O CSS e o link para Tailwind CSS foram removidos conforme solicitado. -->
</head>
<body>
    <header>
        <h1>Bem-vindo, <?php echo htmlspecialchars($professor['nome']); ?>!</h1>
        <nav>
            <ul>
                <li><a href="painel_professor.php">Home</a></li>
                <li><a href="/scripts_php/logout.php">Sair</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Menu do Professor</h2>
        <div class="painel-opcoes">
            <a href="gerenciar_notas.php" class="card-opcao">
                <!-- Ícone para Gerenciar Notas -->
                <img src="https://placehold.co/64x64/cccccc/333333?text=Notas" alt="Gerenciar Notas">
                <span>Gerenciar Notas</span>
            </a>
            <a href="gerenciar_frequencia.php" class="card-opcao">
                <!-- Ícone para Gerenciar Frequência -->
                <img src="https://placehold.co/64x64/cccccc/333333?text=Freq" alt="Gerenciar Frequência">
                <span>Gerenciar Frequência</span>
            </a>
            <a href="minhas_turmas.php" class="card-opcao">
                <!-- Ícone para Minhas Turmas -->
                <img src="https://placehold.co/64x64/cccccc/333333?text=Turmas" alt="Minhas Turmas">
                <span>Minhas Turmas</span>
            </a>
            <a href="meu_horario.php" class="card-opcao">
                <!-- Ícone para Meu Horário -->
                <img src="https://placehold.co/64x64/cccccc/333333?text=Horário" alt="Meu Horário">
                <span>Meu Horário</span>
            </a>
        </div>
    </main>

</body>
</html>
