<?php
// Incluir header que já inicia a sessão e verifica se professor está logado
require_once __DIR__ . '/../../../public/includes/header_professor.php';

// Verificação do usuário logado - nomes corretos da sessão
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_tipo'])) {
    header('Location: ../../../public/php/login.php');
    exit;
}

$tipo_usuario = strtolower(trim($_SESSION['usuario_tipo']));
if ($tipo_usuario !== 'professor') {
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

// Ativar modo erro para debug (comente ou remova em produção)
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Busca dados do professor com tratamento de erro
try {
    $stmt = $pdo->prepare("
        SELECT 
            p.id,
            p.matricula AS professor_matricula,
            p.departamento,
            p.email,
            p.imagem_id,
            u.nome,
            u.matricula AS usuario_matricula,
            u.ativo,
            i.path AS imagem_path
        FROM professores p
        JOIN usuarios u ON p.id = u.id
        LEFT JOIN imagens i ON p.imagem_id = i.id
        WHERE p.id = ?
    ");
    $stmt->execute([$id]);
    $professor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$professor) {
        echo "<main><p>❌ Dados do professor não encontrados. Entre em contato com o suporte.</p>";
        echo '<p><a href="../../../public/php/login.php">Voltar ao Login</a></p></main></body></html>';
        exit;
    }
} catch (PDOException $e) {
    echo "<main><p>❌ Erro ao carregar dados do perfil: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo '<p><a href="../../../public/php/painel_professor.php">Voltar ao Painel</a></p></main></body></html>';
    exit;
}

// Definir caminho da imagem
if (!empty($professor['imagem_path'])) {
    $imagemPath = '../../../../public/recursos/storage/' . $professor['imagem_path'];
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
                <div class="perfil-status <?= $professor['ativo'] ? 'ativo' : 'inativo' ?>">
                    <?= $professor['ativo'] ? 'Ativo' : 'Inativo' ?>
                </div>
            </div>
            
            <div class="perfil-info-principal">
                <h1 class="perfil-nome"><?= htmlspecialchars($professor['nome']) ?></h1>
                <p class="perfil-matricula">Matrícula: <?= htmlspecialchars($professor['usuario_matricula']) ?></p>
                <p class="perfil-departamento"><?= htmlspecialchars($professor['departamento'] ?? 'Departamento não informado') ?></p>
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
                        <span class="perfil-value"><?= htmlspecialchars($professor['nome']) ?></span>
                    </div>
                    <div class="perfil-info-item">
                        <span class="perfil-label">Email:</span>
                        <span class="perfil-value"><?= htmlspecialchars($professor['email']) ?></span>
                    </div>
                    <div class="perfil-info-item">
                        <span class="perfil-label">Matrícula:</span>
                        <span class="perfil-value"><?= htmlspecialchars($professor['usuario_matricula']) ?></span>
                    </div>
                    <div class="perfil-info-item">
                        <span class="perfil-label">Status da Conta:</span>
                        <span class="perfil-value status-badge <?= $professor['ativo'] ? 'ativo' : 'inativo' ?>">
                            <?= $professor['ativo'] ? 'Ativo' : 'Inativo' ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Card Dados Profissionais -->
            <div class="perfil-card">
                <div class="perfil-card-header">
                    <h2>🎓 Dados Profissionais</h2>
                </div>
                <div class="perfil-card-content">
                    <div class="perfil-info-item">
                        <span class="perfil-label">Departamento:</span>
                        <span class="perfil-value"><?= htmlspecialchars($professor['departamento'] ?? 'Não informado') ?></span>
                    </div>
                    <div class="perfil-info-item">
                        <span class="perfil-label">Matrícula de Professor:</span>
                        <span class="perfil-value"><?= htmlspecialchars($professor['professor_matricula'] ?? 'Não informado') ?></span>
                    </div>
                    <div class="perfil-info-item">
                        <span class="perfil-label">Tipo de Usuário:</span>
                        <span class="perfil-value">Professor</span>
                    </div>
                    <div class="perfil-info-item">
                        <span class="perfil-label">Situação:</span>
                        <span class="perfil-value status-badge <?= $professor['ativo'] ? 'ativo' : 'inativo' ?>">
                            <?= $professor['ativo'] ? 'Ativo no Sistema' : 'Inativo no Sistema' ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Card Informações de Contato -->
            <div class="perfil-card">
                <div class="perfil-card-header">
                    <h2>📧 Informações de Contato</h2>
                </div>
                <div class="perfil-card-content">
                    <div class="perfil-info-item">
                        <span class="perfil-label">Email Institucional:</span>
                        <span class="perfil-value">
                            <a href="mailto:<?= htmlspecialchars($professor['email']) ?>" class="perfil-email-link">
                                <?= htmlspecialchars($professor['email']) ?>
                            </a>
                        </span>
                    </div>
                    <div class="perfil-info-item">
                        <span class="perfil-label">Departamento de Lotação:</span>
                        <span class="perfil-value"><?= htmlspecialchars($professor['departamento'] ?? 'Não informado') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="perfil-acoes">
            <button type="button" class="perfil-btn" onclick="window.location.href='editar_perfil.php'">
                Editar Perfil
            </button>
            <button type="button" class="perfil-btn" onclick="window.location.href='../../../public/php/painel_professor.php'">
                Voltar
            </button>
        </div>
    </div>
</main>

<script src="/../../../../public/recursos/js/painel_professor.js"></script>

</body>
</html>