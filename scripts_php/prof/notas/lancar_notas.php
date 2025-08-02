<?php
session_start();

// Verifica se está logado e é professor
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'professor') {
    header('Location: ../../publico/login.php?tipo=professor');
    exit;
}

require_once(__DIR__ . '/../../../aplicacao/config/conexao.php');

// Pega turma e disciplina da URL (GET)
$turma_id = filter_input(INPUT_GET, 'turma_id', FILTER_VALIDATE_INT);
$disciplina_id = filter_input(INPUT_GET, 'disciplina_id', FILTER_VALIDATE_INT);

if (!$turma_id || !$disciplina_id) {
    die("Turma ou disciplina inválida.");
}

// Busca nome da disciplina para mostrar no topo
$stmt = $pdo->prepare("SELECT nome FROM disciplinas WHERE id = ?");
$stmt->execute([$disciplina_id]);
$disciplina = $stmt->fetchColumn();

if (!$disciplina) {
    die("Disciplina não encontrada.");
}

// Busca os alunos matriculados na turma (status ativa)
$stmt = $pdo->prepare("
    SELECT
        a.id AS aluno_id,
        u.nome AS aluno_nome,
        m.id AS matricula_id,
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

// Se foi enviado formulário para salvar notas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notas'])) {
    $notasPost = $_POST['notas']; // Array [matricula_id => [nota1, nota2, observacao]]

    // Para cada matrícula, atualiza ou insere a nota
    $pdo->beginTransaction();

    try {
        $sqlInsert = "INSERT INTO notas (matricula_id, nota1, nota2, media, observacao) VALUES (?, ?, ?, ?, ?)";
        $sqlUpdate = "UPDATE notas SET nota1 = ?, nota2 = ?, media = ?, observacao = ? WHERE matricula_id = ?";

        $stmtInsert = $pdo->prepare($sqlInsert);
        $stmtUpdate = $pdo->prepare($sqlUpdate);

        foreach ($notasPost as $matricula_id => $dados) {
            // Limpa e formata as notas
            $nota1 = isset($dados['nota1']) && is_numeric($dados['nota1']) ? floatval($dados['nota1']) : null;
            $nota2 = isset($dados['nota2']) && is_numeric($dados['nota2']) ? floatval($dados['nota2']) : null;
            $media = null;

            if ($nota1 !== null && $nota2 !== null) {
                $media = ($nota1 + $nota2) / 2;
            } elseif ($nota1 !== null) {
                $media = $nota1;
            } elseif ($nota2 !== null) {
                $media = $nota2;
            }

            $observacao = isset($dados['observacao']) ? trim($dados['observacao']) : null;

            // Verifica se já existe nota para essa matrícula
            $stmtCheck = $pdo->prepare("SELECT id FROM notas WHERE matricula_id = ?");
            $stmtCheck->execute([$matricula_id]);
            $existe = $stmtCheck->fetchColumn();

            if ($existe) {
                // Atualiza
                $stmtUpdate->execute([$nota1, $nota2, $media, $observacao, $matricula_id]);
            } else {
                // Insere
                $stmtInsert->execute([$matricula_id, $nota1, $nota2, $media, $observacao]);
            }
        }

        $pdo->commit();
        $msg_sucesso = "Notas salvas com sucesso.";
        
        // Recarregar as notas para mostrar atualizado
        $stmt->execute([$turma_id]);
        $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        $pdo->rollBack();
        $msg_erro = "Erro ao salvar notas: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Lançar Notas - <?= htmlspecialchars($disciplina) ?></title>
    <link rel="stylesheet" href="../../../public/css/admin-style.css" />
</head>
<body>
    <header>
        <h1>Lançar Notas - <?= htmlspecialchars($disciplina) ?></h1>
        <nav>
            <a href="index.php">Voltar às turmas</a> |
            <a href="/scripts_php/logout.php">Sair</a>
        </nav>
    </header>

    <main>
        <?php if (!empty($msg_sucesso)): ?>
            <div class="alert success"><?= htmlspecialchars($msg_sucesso) ?></div>
        <?php endif; ?>
        <?php if (!empty($msg_erro)): ?>
            <div class="alert error"><?= htmlspecialchars($msg_erro) ?></div>
        <?php endif; ?>

        <?php if (empty($alunos)): ?>
            <p>Nenhum aluno matriculado nesta turma.</p>
        <?php else: ?>
            <form method="POST" action="">
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
                                <td>
                                    <input 
                                        type="number" 
                                        step="0.01" min="0" max="10" 
                                        name="notas[<?= $aluno['matricula_id'] ?>][nota1]" 
                                        value="<?= htmlspecialchars($aluno['nota1']) ?>" />
                                </td>
                                <td>
                                    <input 
                                        type="number" 
                                        step="0.01" min="0" max="10" 
                                        name="notas[<?= $aluno['matricula_id'] ?>][nota2]" 
                                        value="<?= htmlspecialchars($aluno['nota2']) ?>" />
                                </td>
                                <td>
                                    <?= is_null($aluno['media']) ? '-' : number_format($aluno['media'], 2) ?>
                                </td>
                                <td>
                                    <input 
                                        type="text" 
                                        name="notas[<?= $aluno['matricula_id'] ?>][observacao]" 
                                        value="<?= htmlspecialchars($aluno['observacao']) ?>" />
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <button type="submit" class="btn btn-primary">Salvar Notas</button>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>
