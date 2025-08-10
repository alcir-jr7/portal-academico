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
            p.email AS coordenador_email,
            i.path AS imagem_path
        FROM alunos a
        JOIN usuarios u ON a.id = u.id
        JOIN cursos c ON a.curso_id = c.id
        LEFT JOIN professores p ON c.coordenador_id = p.id
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
    $imagemPath = '../../../public/recursos/storage/' . $aluno['imagem_path'];
} else {
    $imagemPath = '../../../public/recursos/storage/profile.jpg';
}
?>

<main>
    <h1>Meu Perfil</h1>
    
    <div>
        <img src="<?= htmlspecialchars($imagemPath) ?>" alt="Minha foto de perfil" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd;">
    </div>
    
    <h2>Dados Pessoais</h2>
    <ul>
        <li><strong>Nome:</strong> <?= htmlspecialchars($aluno['nome']) ?></li>
        <li><strong>Matrícula:</strong> <?= htmlspecialchars($aluno['matricula']) ?></li>
        <li><strong>Email:</strong> <?= htmlspecialchars($aluno['email']) ?></li>
        <li><strong>Status:</strong> <?= $aluno['ativo'] ? 'Ativo' : 'Inativo' ?></li>
    </ul>

    <h2>Dados Acadêmicos</h2>
    <ul>
        <li><strong>Curso:</strong> <?= htmlspecialchars($aluno['curso_nome']) ?> (<?= htmlspecialchars($aluno['curso_codigo']) ?>)</li>
        <li><strong>Turno:</strong> <?= ucfirst(htmlspecialchars($aluno['curso_turno'])) ?></li>
        <li><strong>Período de Entrada:</strong> <?= htmlspecialchars($aluno['periodo_entrada'] ?? 'Não informado') ?></li>
        <li><strong>Coordenador do Curso:</strong> <?= htmlspecialchars($aluno['coordenador_email'] ?? 'Não informado') ?></li>
    </ul>

    <p>
        <a href="editar_perfil.php">Editar Perfil</a> |
        <a href="/../../../public/php/painel_aluno.php">Voltar ao Dashboard</a>
    </p>
</main>

<script src="/../../../../public/recursos/js/painel_aluno.js"></script>

</body>
</html>