<?php
require_once __DIR__ . '/../../../public/includes/header_professor.php';

// Pega turma da URL (GET)
$turma_id = isset($_GET['turma_id']) ? (int)$_GET['turma_id'] : null;

if (!$turma_id) {
    echo "<p style='color: red;'>Turma inválida. Certifique-se de que o link contém o parâmetro turma_id.</p>";
    exit;
}

// Verifica se a turma existe e pega informações da disciplina
$stmt = $pdo->prepare("
    SELECT t.id, d.nome AS disciplina_nome, d.id AS disciplina_id, t.semestre, t.horario
    FROM turmas t 
    JOIN disciplinas d ON t.disciplina_id = d.id 
    WHERE t.id = ?
");
$stmt->execute([$turma_id]);
$turma_info = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$turma_info) {
    echo "<p style='color: red;'>Turma não encontrada.</p>";
    exit;
}

// Verifica se o professor logado é responsável por essa turma
$stmt = $pdo->prepare("SELECT id FROM turmas WHERE id = ? AND professor_id = ?");
$stmt->execute([$turma_id, $_SESSION['usuario_id']]);
$professor_autorizado = $stmt->fetchColumn();

if (!$professor_autorizado) {
    echo "<p style='color: red;'>Você não tem permissão para registrar frequência nesta turma.</p>";
    exit;
}

// Busca os alunos matriculados na turma
$stmt = $pdo->prepare("
    SELECT a.id AS aluno_id, u.nome AS aluno_nome, u.matricula, m.id AS matricula_id 
    FROM matriculas m 
    JOIN alunos a ON m.aluno_id = a.id 
    JOIN usuarios u ON a.id = u.id 
    WHERE m.turma_id = ? AND m.status = 'ativa' 
    ORDER BY u.nome
");
$stmt->execute([$turma_id]);
$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Se foi enviado o formulário de frequência
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['presencas'])) {
    $data = date('Y-m-d');
    $presencasPost = $_POST['presencas'];

    $pdo->beginTransaction();

    try {
        foreach ($presencasPost as $matricula_id => $presente) {
            $presente = ($presente === '1') ? 1 : 0;

            // Verifica se já foi registrada frequência nessa data para esta matrícula
            $stmtCheck = $pdo->prepare("SELECT id FROM frequencias WHERE matricula_id = ? AND data = ?");
            $stmtCheck->execute([$matricula_id, $data]);
            $existe = $stmtCheck->fetchColumn();

            if ($existe) {
                $stmtUpdate = $pdo->prepare("UPDATE frequencias SET presente = ? WHERE matricula_id = ? AND data = ?");
                $stmtUpdate->execute([$presente, $matricula_id, $data]);
            } else {
                $stmtInsert = $pdo->prepare("INSERT INTO frequencias (matricula_id, data, presente) VALUES (?, ?, ?)");
                $stmtInsert->execute([$matricula_id, $data, $presente]);
            }
        }
        $pdo->commit();
        $msg_sucesso = "Frequência registrada com sucesso para o dia " . date('d/m/Y') . ".";
    } catch (Exception $e) {
        $pdo->rollBack();
        $msg_erro = "Erro ao registrar frequência: " . $e->getMessage();
    }
}

// Verifica se já existe frequência registrada para hoje e busca os dados
$data_hoje = date('Y-m-d');
$stmt = $pdo->prepare("
    SELECT f.matricula_id, f.presente
    FROM frequencias f 
    JOIN matriculas m ON f.matricula_id = m.id 
    WHERE m.turma_id = ? AND f.data = ?
");
$stmt->execute([$turma_id, $data_hoje]);
$frequencias_hoje = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$tem_frequencia_hoje = !empty($frequencias_hoje);
?>

<main>
    <h1>Registrar Frequência</h1>
    <h2><?= htmlspecialchars($turma_info['disciplina_nome']) ?> - <?= htmlspecialchars($turma_info['semestre']) ?></h2>
    <p><strong>Data:</strong> <?= date('d/m/Y') ?> | <strong>Horário:</strong> <?= htmlspecialchars($turma_info['horario']) ?></p>


    <?php if (!empty($msg_sucesso)): ?>
        <div class="alert success"><?= htmlspecialchars($msg_sucesso) ?></div>
    <?php endif; ?>
    <?php if (!empty($msg_erro)): ?>
        <div class="alert error"><?= htmlspecialchars($msg_erro) ?></div>
    <?php endif; ?>

    <?php if ($tem_frequencia_hoje): ?>
        <div class="alert info">
            <strong>Atenção:</strong> Já existe frequência registrada para hoje. 
            Se você continuar, os registros serão atualizados.
        </div>
    <?php endif; ?>

    <?php if (empty($alunos)): ?>
        <p>Nenhum aluno matriculado nesta turma.</p>
    <?php else: ?>
        <form method="POST" action="">
            <table border="1" class="admin-table">
                <thead>
                    <tr>
                        <th>Matrícula</th>
                        <th>Aluno</th>
                        <th>Presença</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alunos as $aluno): ?>
                        <?php 
                        $presente_atual = isset($frequencias_hoje[$aluno['matricula_id']]) 
                                       ? $frequencias_hoje[$aluno['matricula_id']] 
                                       : 1; 
                        ?>
                        <tr data-matricula="<?= $aluno['matricula_id'] ?>" class="linha-aluno <?= $presente_atual ? 'presente' : 'ausente' ?>">
                            <td><?= htmlspecialchars($aluno['matricula']) ?></td>
                            <td><?= htmlspecialchars($aluno['aluno_nome']) ?></td>
                            <td>
                                <label class="opcao-presenca">
                                    <input type="radio" 
                                           name="presencas[<?= $aluno['matricula_id'] ?>]" 
                                           value="1" 
                                           <?= $presente_atual ? 'checked' : '' ?>
                                           onchange="atualizarLinha(<?= $aluno['matricula_id'] ?>, true)"> 
                                    Presente
                                </label>
                                <label class="opcao-presenca">
                                    <input type="radio" 
                                           name="presencas[<?= $aluno['matricula_id'] ?>]" 
                                           value="0"
                                           <?= !$presente_atual ? 'checked' : '' ?>
                                           onchange="atualizarLinha(<?= $aluno['matricula_id'] ?>, false)"> 
                                    Ausente
                                </label>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="acoes-form">
                <button type="submit" class="btn btn-primary">Salvar Frequência</button>
                <button type="button" onclick="marcarTodos(true)" class="btn btn-secondary">
                    Marcar Todos Presentes
                </button>
                <button type="button" onclick="marcarTodos(false)" class="btn btn-secondary">
                    Marcar Todos Ausentes
                </button>
                <a href="index.php">Voltar às turmas</a>
            </div>
        </form>
    <?php endif; ?>
</main>

<script src="/../../../public/recursos/js/painel_professor.js"></script>

</body>
</html>
