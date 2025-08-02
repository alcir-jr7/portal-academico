
<?php
session_start();
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

$turma_id = $_GET['id'] ?? null;

if (!$turma_id) {
    echo "Turma não especificada.";
    exit;
}

// Buscar turma
$stmt = $pdo->prepare("
    SELECT t.id, d.nome AS disciplina
    FROM turmas t
    JOIN disciplinas d ON t.disciplina_id = d.id
    WHERE t.id = ?
");
$stmt->execute([$turma_id]);
$turma = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$turma) {
    echo "Turma não encontrada.";
    exit;
}

// Buscar alunos disponíveis (opcional: só os que ainda não estão nessa turma)
$alunos = $pdo->query("SELECT a.id, u.nome FROM alunos a JOIN usuarios u ON a.id = u.id")->fetchAll(PDO::FETCH_ASSOC);

// Se enviou o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aluno_id = $_POST['aluno_id'] ?? null;

    if ($aluno_id) {
        // Verificar se já está matriculado
        $check = $pdo->prepare("SELECT id FROM matriculas WHERE aluno_id = ? AND turma_id = ?");
        $check->execute([$aluno_id, $turma_id]);

        if ($check->rowCount() === 0) {
            $stmt = $pdo->prepare("INSERT INTO matriculas (aluno_id, turma_id) VALUES (?, ?)");
            $stmt->execute([$aluno_id, $turma_id]);
            header("Location: visualizar.php?id=$turma_id");
            exit;
        } else {
            $erro = "Aluno já está matriculado nesta turma.";
        }
    } else {
        $erro = "Selecione um aluno.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Aluno à Turma</title>
</head>
<body>
    <h1>Adicionar Aluno à Turma: <?= htmlspecialchars($turma['disciplina']) ?></h1>

    <?php if (!empty($erro)): ?>
        <p style="color: red;"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="post">
        <label>
            Aluno:<br>
            <select name="aluno_id" required>
                <option value="">Selecione...</option>
                <?php foreach ($alunos as $aluno): ?>
                    <option value="<?= $aluno['id'] ?>">
                        <?= htmlspecialchars($aluno['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <button type="submit">Adicionar</button>
        <a href="visualizar.php?id=<?= $turma_id ?>">Cancelar</a>
    </form>
</body>
</html>



