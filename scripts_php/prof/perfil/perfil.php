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

<main>
    <h1>Meu Perfil</h1>

    <div>
        <img src="<?= htmlspecialchars($imagemPath) ?>" alt="Foto de perfil" style="width:150px; height:150px; border-radius:50%; object-fit:cover; border:2px solid #ddd;">
    </div>

    <h2>Dados Pessoais</h2>
    <ul>
        <li><strong>Nome:</strong> <?= htmlspecialchars($professor['nome']) ?></li>
        <li><strong>Matrícula:</strong> <?= htmlspecialchars($professor['usuario_matricula']) ?></li>
        <li><strong>Email:</strong> <?= htmlspecialchars($professor['email']) ?></li>
        <li><strong>Status:</strong> <?= $professor['ativo'] ? 'Ativo' : 'Inativo' ?></li>
    </ul>

    <h2>Dados Profissionais</h2>
    <ul>
        <li><strong>Departamento:</strong> <?= htmlspecialchars($professor['departamento'] ?? 'Não informado') ?></li>
    </ul>

    <button type="button" onclick="window.location.href='editar_perfil.php'">
        Editar Perfil
    </button>

    <button type="button" onclick="window.location.href='../../../public/php/painel_professor.php'">
        Voltar
    </button>
</main>

<script src="/../../../../public/recursos/js/painel_professor.js"></script>

</body>
</html>
