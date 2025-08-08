<?php
require_once __DIR__ . '/../../../public/includes/header_admin.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<main><p>ID do aluno não informado.</p></main></body></html>";
    exit;
}

// Busca dados do aluno, usuário e curso
$stmt = $pdo->prepare("
    SELECT 
        a.*, u.nome, u.matricula, u.tipo, u.ativo,
        c.nome AS curso_nome, c.codigo AS curso_codigo, c.turno AS curso_turno,
        p.email AS coordenador_email
    FROM alunos a
    JOIN usuarios u ON a.id = u.id
    JOIN cursos c ON a.curso_id = c.id
    LEFT JOIN professores p ON c.coordenador_id = p.id
    WHERE a.id = ?
");
$stmt->execute([$id]);
$aluno = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$aluno) {
    echo "<main><p>Aluno não encontrado.</p></main></body></html>";
    exit;
}
?>

<main>
    <h1>Detalhes do Aluno</h1>
    <ul>
        <li><strong>Nome:</strong> <?= htmlspecialchars($aluno['nome']) ?></li>
        <li><strong>Matrícula:</strong> <?= htmlspecialchars($aluno['matricula']) ?></li>
        <li><strong>Email:</strong> <?= htmlspecialchars($aluno['email']) ?></li>
        <li><strong>Período de Entrada:</strong> <?= htmlspecialchars($aluno['periodo_entrada']) ?></li>
        <li><strong>Curso:</strong> <?= htmlspecialchars($aluno['curso_nome']) ?> (<?= htmlspecialchars($aluno['curso_codigo']) ?>)</li>
        <li><strong>Turno do Curso:</strong> <?= htmlspecialchars($aluno['curso_turno']) ?></li>
        <li><strong>Coordenador do Curso:</strong> <?= htmlspecialchars($aluno['coordenador_email'] ?? '—') ?></li>
        <li><strong>Status do Usuário:</strong> <?= $aluno['ativo'] ? 'Ativo' : 'Inativo' ?></li>
        <li><strong>Tipo de Usuário:</strong> <?= htmlspecialchars($aluno['tipo']) ?></li>
    </ul>

    <a href="index.php">Voltar à lista</a>
</main>

    <script src="/../../../public/recursos/js/painel_admin.js"></script>

</body>
</html>
