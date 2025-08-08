<?php
require_once __DIR__ . '/../../../public/includes/header_admin.php';

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

// Buscar alunos disponíveis (ainda não matriculados nessa turma)
$stmt = $pdo->prepare("
    SELECT a.id, u.nome 
    FROM alunos a 
    JOIN usuarios u ON a.id = u.id
    WHERE a.id NOT IN (
        SELECT aluno_id FROM matriculas WHERE turma_id = ?
    )
    ORDER BY u.nome
");
$stmt->execute([$turma_id]);
$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alunos_ids = $_POST['aluno_id'] ?? [];

    if (!empty($alunos_ids) && is_array($alunos_ids)) {
        // Inserir cada aluno selecionado, evitando duplicatas
        $inseridos = 0;
        foreach ($alunos_ids as $aluno_id) {
            // Verificar se já está matriculado (por segurança)
            $check = $pdo->prepare("SELECT id FROM matriculas WHERE aluno_id = ? AND turma_id = ?");
            $check->execute([$aluno_id, $turma_id]);
            if ($check->rowCount() === 0) {
                $stmt = $pdo->prepare("INSERT INTO matriculas (aluno_id, turma_id) VALUES (?, ?)");
                $stmt->execute([$aluno_id, $turma_id]);
                $inseridos++;
            }
        }
        header("Location: visualizar.php?id=$turma_id");
        exit;
    } else {
        $erro = "Selecione ao menos um aluno.";
    }
}
?>

<main>
    <h1>Adicionar Aluno(s) à Turma: <?= htmlspecialchars($turma['disciplina']) ?></h1>

    <?php if (!empty($erro)): ?>
        <p style="color: red;"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="post">
        <label>
            Alunos:<br>
            <select name="aluno_id[]" multiple size="10" required>
                <?php foreach ($alunos as $aluno): ?>
                    <option value="<?= $aluno['id'] ?>">
                        <?= htmlspecialchars($aluno['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <button type="submit">Adicionar Selecionados</button>
        <a href="visualizar.php?id=<?= $turma_id ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</main>

<script src="/../../../public/recursos/js/painel_admin.js"></script>

</body>
</html>
