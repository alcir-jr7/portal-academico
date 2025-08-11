<?php
// Incluir header primeiro (que já inicia a sessão)
require_once __DIR__ . '/../../../public/includes/header_aluno.php';

// Verificação do usuário logado - NOMES CORRETOS DA SESSÃO
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_tipo'])) {
    header('Location: ../../../public/php/login.php');
    exit;
}

// Limpar espaços e converter para minúsculo para comparação robusta
$tipo_usuario = strtolower(trim($_SESSION['usuario_tipo']));

if ($tipo_usuario !== 'aluno') {
    header('Location: ../../../public/php/login.php');
    exit;
}

$id = $_SESSION['usuario_id'];

// Verificar se PDO está disponível
if (!isset($pdo)) {
    $conexao_path = __DIR__ . '/../../../bancoDados/banco.php';
    if (file_exists($conexao_path)) {
        require_once $conexao_path;
    } else {
        die("Erro: Conexão com banco de dados não disponível.");
    }
}

// Busca dados do aluno com tratamento de erro
try {
    $stmt = $pdo->prepare("
        SELECT 
            a.*, u.nome, u.matricula, u.tipo, u.ativo,
            c.nome AS curso_nome, c.codigo AS curso_codigo, c.turno AS curso_turno,
            p.email AS coordenador_email, up.nome AS coordenador_nome,
            i.path AS imagem_path
        FROM alunos a
        JOIN usuarios u ON a.id = u.id
        JOIN cursos c ON a.curso_id = c.id
        LEFT JOIN professores p ON c.coordenador_id = p.id
        LEFT JOIN usuarios up ON p.id = up.id
        LEFT JOIN imagens i ON a.imagem_id = i.id
        WHERE a.id = ?
    ");
    
    $stmt->execute([$id]);
    $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$aluno) {
        echo "<main><p>❌ Dados do aluno não encontrados. Entre em contato com o suporte.</p>";
        echo '<p><a href="../../../public/php/login.php">Voltar ao Login</a></p></main></body></html>';
        exit;
    }
    
} catch (PDOException $e) {
    error_log("Erro na consulta do perfil do aluno: " . $e->getMessage());
    
    echo "<main><p>❌ Erro ao carregar dados do perfil. Tente novamente.</p>";
    echo '<p><a href="../../aluno/dashboard.php">Voltar ao Dashboard</a></p></main></body></html>';
    exit;
}

// Definir caminho da imagem
if (!empty($aluno['imagem_path'])) {
    $imagemPath = '../../../../public/recursos/storage/' . $aluno['imagem_path'];
} else {
    $imagemPath = '../../../../public/recursos/storage/profile.jpg';
}
?>

<main class="perfil-main">
    <div class="perfil-container">
        <!-- Header do Perfil -->
        <div class="perfil-header">
            <div class="perfil-foto-container">
                <img src="<?= htmlspecialchars($imagemPath) ?>" alt="Foto de perfil" class="perfil-foto-principal">
                <div class="perfil-status <?= $aluno['ativo'] ? 'ativo' : 'inativo' ?>">
                    <?= $aluno['ativo'] ? 'Ativo' : 'Inativo' ?>
                </div>
            </div>
            
            <div class="perfil-info-principal">
                <h1 class="perfil-nome"><?= htmlspecialchars($aluno['nome']) ?></h1>
                <p class="perfil-matricula">Matrícula: <?= htmlspecialchars($aluno['matricula']) ?></p>
                <p class="perfil-curso"><?= htmlspecialchars($aluno['curso_nome']) ?></p>
            </div>
        </div>

        <!-- Cards de Informações -->
        <div class="perfil-cards-grid">
            <!-- Card Dados Pessoais -->
            <div class="perfil-card">
                <div class="perfil-card-header">
                    <h2>👤 Dados Pessoais</h2>
                </div>
                <div class="perfil-card-content">
                    <div class="perfil-info-item">
                        <span class="perfil-label">Nome Completo:</span>
                        <span class="perfil-value"><?= htmlspecialchars($aluno['nome']) ?></span>
                    </div>
                    <div class="perfil-info-item">
                        <span class="perfil-label">Email:</span>
                        <span class="perfil-value"><?= htmlspecialchars($aluno['email']) ?></span>
                    </div>
                    <div class="perfil-info-item">
                        <span class="perfil-label">Matrícula:</span>
                        <span class="perfil-value"><?= htmlspecialchars($aluno['matricula']) ?></span>
                    </div>
                    <div class="perfil-info-item">
                        <span class="perfil-label">Status da Conta:</span>
                        <span class="perfil-value status-badge <?= $aluno['ativo'] ? 'ativo' : 'inativo' ?>">
                            <?= $aluno['ativo'] ? 'Ativo' : 'Inativo' ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Card Dados Acadêmicos -->
            <div class="perfil-card">
                <div class="perfil-card-header">
                    <h2>🎓 Dados Acadêmicos</h2>
                </div>
                <div class="perfil-card-content">
                    <div class="perfil-info-item">
                        <span class="perfil-label">Curso:</span>
                        <span class="perfil-value"><?= htmlspecialchars($aluno['curso_nome']) ?></span>
                    </div>
                    <div class="perfil-info-item">
                        <span class="perfil-label">Código do Curso:</span>
                        <span class="perfil-value"><?= htmlspecialchars($aluno['curso_codigo']) ?></span>
                    </div>
                    <div class="perfil-info-item">
                        <span class="perfil-label">Turno:</span>
                        <span class="perfil-value"><?= ucfirst(htmlspecialchars($aluno['curso_turno'])) ?></span>
                    </div>
                    <div class="perfil-info-item">
                        <span class="perfil-label">Período de Entrada:</span>
                        <span class="perfil-value"><?= htmlspecialchars($aluno['periodo_entrada'] ?? 'Não informado') ?></span>
                    </div>
                </div>
            </div>

            <!-- Card Coordenação -->
            <div class="perfil-card">
                <div class="perfil-card-header">
                    <h2>📧 Coordenação</h2>
                </div>
                <div class="perfil-card-content">
                    <div class="perfil-info-item">
                        <span class="perfil-label">Nome do Coordenador:</span>
                        <span class="perfil-value"><?= htmlspecialchars($aluno['coordenador_nome'] ?? 'Não informado') ?></span>
                    </div>
                    <div class="perfil-info-item">
                        <span class="perfil-label">Email do Coordenador:</span>
                        <span class="perfil-value">
                            <?php if (!empty($aluno['coordenador_email'])): ?>
                                <a href="mailto:<?= htmlspecialchars($aluno['coordenador_email']) ?>" class="perfil-email-link">
                                    <?= htmlspecialchars($aluno['coordenador_email']) ?>
                                </a>
                            <?php else: ?>
                                Não informado
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="perfil-acoes">
            <button type="button" class="perfil-btn" onclick="window.location.href='editar_perfil.php'">
                Editar Perfil
            </button>
            <button type="button" class="perfil-btn" onclick="window.location.href='../../../public/php/painel_aluno.php'">
                Voltar
            </button>
        </div>
    </div>
</main>

<script src="/../../../../public/recursos/js/painel_aluno.js"></script>

</body>
</html>