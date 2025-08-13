<?php 
require_once __DIR__ . '/../../../public/includes/header_admin.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<main><p>ID do aluno não informado.</p></main></body></html>";
    exit;
}

// Busca dados do aluno, usuário, curso e imagem
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
    echo "<main><p>Aluno não encontrado.</p></main></body></html>";
    exit;
}


if ($aluno['imagem_path']) {
    $imagemPath = '/../../../public/recursos/storage/' . $aluno['imagem_path'];
} else {
    $imagemPath = '/../../../public/recursos/storage/profile.jpg'; // Imagem padrão
}

?>

<main class="form-create-main">
    <h1 class="form-create-title">Detalhes do Aluno</h1>

    <div class="form-create-img-container">
        <img src="<?= htmlspecialchars($imagemPath) ?>" alt="Imagem de Perfil" class="form-create-img">
    </div>

    <ul class="form-create-info-list">
        <li><strong class="form-create-label">Nome:</strong> <span class="form-create-value"><?= htmlspecialchars($aluno['nome']) ?></span></li>
        <li><strong class="form-create-label">Matrícula:</strong> <span class="form-create-value"><?= htmlspecialchars($aluno['matricula']) ?></span></li>
        <li><strong class="form-create-label">Email:</strong> <span class="form-create-value"><?= htmlspecialchars($aluno['email']) ?></span></li>
        <li><strong class="form-create-label">Período de Entrada:</strong> <span class="form-create-value"><?= htmlspecialchars($aluno['periodo_entrada']) ?></span></li>
        <li><strong class="form-create-label">Curso:</strong> <span class="form-create-value"><?= htmlspecialchars($aluno['curso_nome']) ?> (<?= htmlspecialchars($aluno['curso_codigo']) ?>)</span></li>
        <li><strong class="form-create-label">Turno do Curso:</strong> <span class="form-create-value"><?= htmlspecialchars($aluno['curso_turno']) ?></span></li>
        <li><strong class="form-create-label">Coordenador do Curso:</strong> <span class="form-create-value"><?= htmlspecialchars($aluno['coordenador_email'] ?? '—') ?></span></li>
        <li><strong class="form-create-label">Status do Usuário:</strong> <span class="form-create-value"><?= $aluno['ativo'] ? 'Ativo' : 'Inativo' ?></span></li>
        <li><strong class="form-create-label">Tipo de Usuário:</strong> <span class="form-create-value"><?= htmlspecialchars($aluno['tipo']) ?></span></li>
    </ul>

    <p class="form-create-actions">
        <a href="index.php" class="form-create-btn-secondary">Voltar à lista</a>
    </p>
</main>

<script src="/../../../public/recursos/js/painel_admin.js"></script>

</body>
</html>