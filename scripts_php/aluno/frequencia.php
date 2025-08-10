<?php
require_once __DIR__ . '/../../public/includes/header_aluno.php';

$aluno_id = $_SESSION['usuario_id'];

// Busca informações do aluno
$stmt = $pdo->prepare("
    SELECT u.nome, u.matricula, c.nome AS curso_nome
    FROM usuarios u
    JOIN alunos a ON u.id = a.id
    JOIN cursos c ON a.curso_id = c.id
    WHERE u.id = ?
");
$stmt->execute([$aluno_id]);
$aluno_info = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$aluno_info) {
    die("Informações do aluno não encontradas.");
}

// Busca disciplinas, professor, total aulas e faltas
$stmt = $pdo->prepare("
    SELECT 
        d.nome AS disciplina_nome,
        d.codigo AS disciplina_codigo,
        t.semestre,
        p.nome AS professor_nome,
        m.id AS matricula_id,
        COUNT(f.id) AS total_aulas,
        COALESCE(SUM(CASE WHEN f.presente = 0 THEN 1 ELSE 0 END), 0) AS faltas
    FROM matriculas m
    JOIN turmas t ON m.turma_id = t.id
    JOIN disciplinas d ON t.disciplina_id = d.id
    JOIN professores pr ON t.professor_id = pr.id
    JOIN usuarios p ON pr.id = p.id
    LEFT JOIN frequencias f ON f.matricula_id = m.id
    WHERE m.aluno_id = ?
    GROUP BY m.id, d.nome, d.codigo, t.semestre, p.nome
    ORDER BY t.semestre DESC, d.nome
");
$stmt->execute([$aluno_id]);
$disciplinas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <h1>Frequência</h1>

    <div class="info-aluno">
        <p><strong>Aluno:</strong> <?= htmlspecialchars($aluno_info['nome']) ?></p>
        <p><strong>Matrícula:</strong> <?= htmlspecialchars($aluno_info['matricula']) ?></p>
        <p><strong>Curso:</strong> <?= htmlspecialchars($aluno_info['curso_nome']) ?></p>
    </div>

   <section>
         <?php if (empty($disciplinas)): ?>
        <div class="alert info">
            <p>Você ainda não está matriculado em nenhuma disciplina ou não há registros de frequência.</p>
        </div>
    <?php else: ?>
        <table border="1" class="admin-table">
            <thead>
                <tr>
                    <th>Semestre</th>
                    <th>Código</th>
                    <th>Disciplina</th>
                    <th>Professor</th>
                    <th>Total de Aulas</th>
                    <th>Faltas</th>
                    <th>% de Faltas</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($disciplinas as $disciplina): ?>
                    <?php
                        $total = (int)$disciplina['total_aulas'];
                        $faltas = (int)$disciplina['faltas'];
                        $percentual = $total > 0 ? ($faltas / $total) * 100 : 0;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($disciplina['semestre']) ?></td>
                        <td><?= htmlspecialchars($disciplina['disciplina_codigo']) ?></td>
                        <td><?= htmlspecialchars($disciplina['disciplina_nome']) ?></td>
                        <td><?= htmlspecialchars($disciplina['professor_nome']) ?></td>
                        <td><?= $total ?></td>
                        <td><?= $faltas ?></td>
                        <td><?= number_format($percentual, 2, ',', '.') ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="button" onclick="window.location.href='/../../public/php/painel_aluno.php'">
            Voltar
        </button>
    <?php endif; ?>
   </section>
</main>

<script src="/../../../public/recursos/js/painel_aluno.js"></script>

</body>
</html>
