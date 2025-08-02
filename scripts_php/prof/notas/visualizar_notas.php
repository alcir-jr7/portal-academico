<?php
session_start();

// Verifica se está logado e é professor
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'professor') {
    header('Location: ../../publico/login.php?tipo=professor');
    exit;
}

require_once(__DIR__ . '/../../../aplicacao/config/conexao.php');

// Pega turma e disciplina da URL
$turma_id = filter_input(INPUT_GET, 'turma_id', FILTER_VALIDATE_INT);
$disciplina_id = filter_input(INPUT_GET, 'disciplina_id', FILTER_VALIDATE_INT);

if (!$turma_id || !$disciplina_id) {
    die("Turma ou disciplina inválida.");
}

// Busca nome da disciplina
$stmt = $pdo->prepare("SELECT nome FROM disciplinas WHERE id = ?");
$stmt->execute([$disciplina_id]);
$disciplina = $stmt->fetchColumn();
if (!$disciplina) {
    die("Disciplina não encontrada.");
}

// Busca os alunos da turma com as notas
$stmt = $pdo->prepare("
    SELECT 
        u.nome AS aluno_nome,
        n.nota1,
        n.nota2,
        n.media,
        n.observacao
    FROM matriculas m
    JOIN alunos a ON m.aluno_id = a.id
    JOIN usuarios u ON a.id = u.id
    LEFT JOIN notas n ON n.matricula_id = m.id
    WHERE m.turma_id = ? AND m.status = 'ativa'
    ORDER BY u.nome
");
$stmt->execute([$turma_id]);
$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Visualizar Notas - <?= htmlspecialchars($disciplina) ?></title>
    <link rel="stylesheet" href="../../../public/css/admin-style.css" />
</head>
<body>
    <header>
        <h1>Notas da Turma - <?= htmlspecialchars($disciplina) ?></h1>
        <nav>
            <a href="index.php">Voltar às turmas</a> |
            <a href="/scripts_php/logout.php">Sair</a>
        </nav>
    </header>

    <main>
        <?php if (empty($alunos)): ?>
            <p>Nenhum aluno matriculado nesta turma.</p>
        <?php else: ?>
            <table border="1" class="admin-table">
                <thead>
                    <tr>
                        <th>Aluno</th>
                        <th>Nota 1</th>
                        <th>Nota 2</th>
                        <th>Média</th>
                        <th>Observação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alunos as $aluno): ?>
                        <tr>
                            <td><?= htmlspecialchars($aluno['aluno_nome']) ?></td>
                            <td><?= is_null($aluno['nota1']) ? '-' : number_format($aluno['nota1'], 2) ?></td>
                            <td><?= is_null($aluno['nota2']) ? '-' : number_format($aluno['nota2'], 2) ?></td>
                            <td><?= is_null($aluno['media']) ? '-' : number_format($aluno['media'], 2) ?></td>
                            <td><?= htmlspecialchars($aluno['observacao'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</body>
</html>
