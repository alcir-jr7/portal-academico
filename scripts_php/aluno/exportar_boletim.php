<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once __DIR__ . '/../../aplicacao/config/conexao.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'aluno') {
    die("Acesso negado.");
}

$aluno_id = $_SESSION['usuario_id'];

// Buscar informações do aluno
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

// Buscar disciplinas e notas do aluno
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

// Montar HTML para PDF
$html = '
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<title>Boletim do Aluno</title>
<style>
    body { font-family: Arial, sans-serif; font-size: 12px; color: #000; }
    h1 { text-align: center; margin-bottom: 20px; }
    .info-aluno p { margin: 4px 0; font-weight: bold; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { border: 1px solid #000; padding: 6px; text-align: left; }
    th { background-color: #f0f0f0; }
    .situacao.aprovado { color: green; font-weight: bold; }
    .situacao.recuperacao { color: orange; font-weight: bold; }
    .situacao.reprovado { color: red; font-weight: bold; }
    .situacao.pendente { color: gray; font-weight: bold; }
    .status.ativa { color: green; }
    .status.trancada { color: orange; }
    .status.dispensada { color: blue; }
    .status.concluida { color: purple; }
    .observacoes { margin-top: 20px; }
    .observacoes ul { margin-left: 20px; }
    .resumo-desempenho { margin-top: 20px; font-weight: bold; }
    .linha-resumo { margin-bottom: 8px; }
</style>
</head>
<body>
<h1>Boletim</h1>
<div class="info-aluno">
    <p>Aluno: ' . htmlspecialchars($aluno_info['nome']) . '</p>
    <p>Matrícula: ' . htmlspecialchars($aluno_info['matricula']) . '</p>
    <p>Curso: ' . htmlspecialchars($aluno_info['curso_nome']) . '</p>
</div>
';

if (empty($disciplinas)) {
    $html .= '<p>Você ainda não está matriculado em nenhuma disciplina.</p>';
} else {
    $html .= '
    <table>
        <thead>
            <tr>
                <th>Semestre</th>
                <th>Código</th>
                <th>Disciplina</th>
                <th>Professor</th>
                <th>Nota 1</th>
                <th>Nota 2</th>
                <th>Média</th>
                <th>Situação</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
    ';

    foreach ($disciplinas as $disciplina) {
        // Situação
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

        // Status matrícula
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
            default:
                $status_texto = '';
                $status_classe = '';
                break;
        }

        $html .= '<tr>
            <td>' . htmlspecialchars($disciplina['semestre']) . '</td>
            <td>' . htmlspecialchars($disciplina['disciplina_codigo']) . '</td>
            <td>' . htmlspecialchars($disciplina['disciplina_nome']) . '</td>
            <td>' . htmlspecialchars($disciplina['professor_nome']) . '</td>
            <td>' . (is_null($disciplina['nota1']) ? '-' : number_format($disciplina['nota1'], 1)) . '</td>
            <td>' . (is_null($disciplina['nota2']) ? '-' : number_format($disciplina['nota2'], 1)) . '</td>
            <td>' . (is_null($disciplina['media']) ? '-' : number_format($disciplina['media'], 1)) . '</td>
            <td class="situacao ' . $situacao_classe . '">' . $situacao . '</td>
            <td class="status ' . $status_classe . '">' . $status_texto . '</td>
        </tr>';
    }

    $html .= '</tbody></table>';

    // Observações
    $observacoes = array_filter(array_column($disciplinas, 'observacao'));
    $observacoes = array_unique($observacoes);

    if (!empty($observacoes)) {
        $html .= '<div class="observacoes"><h3>Observações:</h3><ul>';
        foreach ($observacoes as $obs) {
            $html .= '<li>' . htmlspecialchars($obs) . '</li>';
        }
        $html .= '</ul></div>';
    }

    // Resumo acadêmico
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

    $html .= '<div class="resumo-desempenho">
        <div class="linha-resumo"><strong>Média Global:</strong> ' . number_format($mediaGlobal, 2, ',', '.') . '</div>
        <div class="linha-resumo"><strong>Situação do Aluno:</strong> ' . $situacaoGeral . '</div>
    </div>';
}

$html .= '
</body>
</html>
';

// Gerar PDF com dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('boletim_' . $aluno_info['matricula'] . '.pdf', ['Attachment' => false]);
exit;
