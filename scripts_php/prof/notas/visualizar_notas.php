<?php
require_once __DIR__ . '/../../../public/includes/header_professor.php';

try {
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

} catch (Exception $e) {
    die("Erro ao buscar notas: " . $e->getMessage());
}
?>

<main>
    <h2>Notas da Turma - <?= htmlspecialchars($disciplina) ?></h2>

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
    <a href="index.php">Voltar às turmas</a>
</main>

<script src="/../../../public/recursos/js/painel_professor.js"></script>

</body>
</html>
