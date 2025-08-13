<?php
require_once __DIR__ . '/../../../public/includes/header_professor.php';

// Pega turma da URL
$turma_id = filter_input(INPUT_GET, 'turma_id', FILTER_VALIDATE_INT);

if (!$turma_id) {
    die("Turma inválida.");
}

// Verifica se a turma existe e pega informações da disciplina
$stmt = $pdo->prepare("
    SELECT t.id, d.nome AS disciplina_nome, t.semestre
    FROM turmas t 
    JOIN disciplinas d ON t.disciplina_id = d.id 
    WHERE t.id = ?
");
$stmt->execute([$turma_id]);
$turma_info = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$turma_info) {
    die("Turma não encontrada.");
}

// Verifica se o professor logado é responsável por essa turma
$stmt = $pdo->prepare("SELECT id FROM turmas WHERE id = ? AND professor_id = ?");
$stmt->execute([$turma_id, $_SESSION['usuario_id']]);
$professor_autorizado = $stmt->fetchColumn();

if (!$professor_autorizado) {
    die("Você não tem permissão para visualizar frequência desta turma.");
}

// Busca os alunos da turma com suas frequências
$stmt = $pdo->prepare("
    SELECT 
        u.nome AS aluno_nome,
        u.matricula,
        m.id AS matricula_id,
        COUNT(f.id) AS total_aulas,
        SUM(CASE WHEN f.presente = 1 THEN 1 ELSE 0 END) AS presencas,
        SUM(CASE WHEN f.presente = 0 THEN 1 ELSE 0 END) AS faltas
    FROM matriculas m
    JOIN alunos a ON m.aluno_id = a.id
    JOIN usuarios u ON a.id = u.id
    LEFT JOIN frequencias f ON f.matricula_id = m.id
    WHERE m.turma_id = ? AND m.status = 'ativa'
    GROUP BY m.id, u.nome, u.matricula
    ORDER BY u.nome
");
$stmt->execute([$turma_id]);
$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <h1>Frequência da Turma - <?= htmlspecialchars($turma_info['disciplina_nome']) ?></h1>

    <?php if (empty($alunos)): ?>
        <p>Nenhum aluno matriculado nesta turma.</p>
    <?php else: ?>
        <table border="1" class="admin-table">
            <thead>
                <tr>
                    <th>Matrícula</th>
                    <th>Aluno</th>
                    <th>Total de Aulas</th>
                    <th>Presenças</th>
                    <th>Faltas</th>
                    <th>% Presença</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alunos as $aluno): ?>
                    <?php 
                    $total_aulas = (int)$aluno['total_aulas'];
                    $presencas = (int)$aluno['presencas'];
                    $faltas = (int)$aluno['faltas'];
                    $percentual = $total_aulas > 0 ? round(($presencas / $total_aulas) * 100, 1) : 0;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($aluno['matricula']) ?></td>
                        <td><?= htmlspecialchars($aluno['aluno_nome']) ?></td>
                        <td><?= $total_aulas ?></td>
                        <td><?= $presencas ?></td>
                        <td><?= $faltas ?></td>
                        <td><?= $percentual ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <div class="botao-atalho">
        <button type="button" onclick="window.location.href='index.php'">
            Voltar
        </button>
    </div>
</main>

<script src="/../../../public/recursos/js/painel_professor.js"></script>

</body>
</html>
