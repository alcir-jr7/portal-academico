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

// Busca as disciplinas e notas do aluno
$stmt = $pdo->prepare("
    SELECT 
        d.nome AS disciplina_nome,
        d.codigo AS disciplina_codigo,
        t.semestre,
        p.nome AS professor_nome,
        n.nota1,
        n.nota2,
        n.media,
        n.observacao,
        m.status AS status_matricula
    FROM matriculas m
    JOIN turmas t ON m.turma_id = t.id
    JOIN disciplinas d ON t.disciplina_id = d.id
    JOIN professores pr ON t.professor_id = pr.id
    JOIN usuarios p ON pr.id = p.id
    LEFT JOIN notas n ON n.matricula_id = m.id
    WHERE m.aluno_id = ?
    ORDER BY t.semestre DESC, d.nome
");
$stmt->execute([$aluno_id]);
$disciplinas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <h1>Boletim</h1>
    <div class="boletim-container">
          <div class="info-aluno">
        <p><strong>Aluno:</strong> <?= htmlspecialchars($aluno_info['nome']) ?></p>
        <p><strong>Matrícula:</strong> <?= htmlspecialchars($aluno_info['matricula']) ?></p>
        <p><strong>Curso:</strong> <?= htmlspecialchars($aluno_info['curso_nome']) ?></p>
    </div>

    <section>
        <?php if (empty($disciplinas)): ?>
            <div class="alert info">
                <p>Você ainda não está matriculado em nenhuma disciplina.</p>
            </div>
        <?php else: ?>
            <table border="1" class="admin-table">
                <thead>
                        <th>Semestre</th>
                        <th>Código</th>
                        <th>Disciplina</th>
                        <th>Professor</th>
                        <th>Nota 1</th>
                        <th>Nota 2</th>
                        <th>Média</th>
                        <th>Situação</th>
                        <th>Status</th>
                  
                </thead>
                <tbody>
                    <?php foreach ($disciplinas as $disciplina): ?>
                        <?php 
                        $situacao = '';
                        $situacao_classe = '';
                        if (!is_null($disciplina['media'])) {
                            if ($disciplina['media'] >= 7.0) {
                                $situacao = 'Aprovado';
                                $situacao_classe = 'aprovado';
                            } elseif ($disciplina['media'] >= 5.0) {
                                $situacao = 'Recuperação';
                                $situacao_classe = 'recuperacao';
                            } else {
                                $situacao = 'Reprovado';
                                $situacao_classe = 'reprovado';
                            }
                        } else {
                            $situacao = 'Pendente';
                            $situacao_classe = 'pendente';
                        }

                        $status_texto = '';
                        $status_classe = '';
                        switch ($disciplina['status_matricula']) {
                            case 'ativa':
                                $status_texto = 'Ativa';
                                $status_classe = 'ativa';
                                break;
                            case 'trancada':
                                $status_texto = 'Trancada';
                                $status_classe = 'trancada';
                                break;
                            case 'dispensada':
                                $status_texto = 'Dispensada';
                                $status_classe = 'dispensada';
                                break;
                            case 'concluida':
                                $status_texto = 'Concluída';
                                $status_classe = 'concluida';
                                break;
                        }
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($disciplina['semestre']) ?></td>
                            <td><?= htmlspecialchars($disciplina['disciplina_codigo']) ?></td>
                            <td><?= htmlspecialchars($disciplina['disciplina_nome']) ?></td>
                            <td><?= htmlspecialchars($disciplina['professor_nome']) ?></td>
                            <td><?= is_null($disciplina['nota1']) ? '-' : number_format($disciplina['nota1'], 1) ?></td>
                            <td><?= is_null($disciplina['nota2']) ? '-' : number_format($disciplina['nota2'], 1) ?></td>
                            <td class="media"><?= is_null($disciplina['media']) ? '-' : number_format($disciplina['media'], 1) ?></td>
                            <td><span class="situacao <?= $situacao_classe ?>"><?= $situacao ?></span></td>
                            <td><span class="status <?= $status_classe ?>"><?= $status_texto ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if (!empty($disciplinas[0]['observacao'])): ?>
                <div class="observacoes">
                    <h3>Observações:</h3>
                    <?php 
                    $observacoes = array_filter(array_column($disciplinas, 'observacao'));
                    if (!empty($observacoes)):
                    ?>
                        <ul>
                            <?php foreach (array_unique($observacoes) as $obs): ?>
                                <li><?= htmlspecialchars($obs) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?> 
    </section>
    </div>
  
    <div class="resumo-desempenho">
                <h3>Resumo Acadêmico: </h3>
                <?php
                $totalMedias = 0;
                $somaMedias = 0;
                $disciplinasAprovadas = 0;

                foreach ($disciplinas as $disc) {
                    if (!is_null($disc['media'])) {
                        $totalMedias++;
                        $somaMedias += $disc['media'];
                        if ($disc['media'] >= 7.0) {
                            $disciplinasAprovadas++;
                        }
                    }
                }

                $mediaGlobal = $totalMedias > 0 ? $somaMedias / $totalMedias : 0;
                $situacaoGeral = $disciplinasAprovadas >= 2 ? "Aprovado" : "Matriculado";
                ?>
                <div class="linha-resumo">
                    <strong>Média Global:</strong>
                    <span><?= number_format($mediaGlobal, 2, ',', '.') ?></span>
                </div>
                <div class="linha-resumo">
                    <strong>Situação do Aluno:</strong>
                    <span><?= $situacaoGeral ?></span>
                </div>
            </div>
        <?php endif; ?>

</main>



<script src="/../../../public/recursos/js/painel_aluno.js"></script>

</body>
</html>
