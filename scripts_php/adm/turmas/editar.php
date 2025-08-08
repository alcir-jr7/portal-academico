<?php
require_once __DIR__ . '/../../../public/includes/header_admin.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

// Pega os dados atuais da turma
$stmt = $pdo->prepare("SELECT * FROM turmas WHERE id = ?");
$stmt->execute([$id]);
$turma = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$turma) {
    echo "Turma não encontrada.";
    exit;
}

// Pega todas as disciplinas para o select
$disciplinas = $pdo->query("SELECT id, nome FROM disciplinas ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

// Pega todos os professores para o select
$professores = $pdo->query("
    SELECT p.id, u.nome 
    FROM professores p 
    JOIN usuarios u ON p.id = u.id
    ORDER BY u.nome
")->fetchAll(PDO::FETCH_ASSOC);

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $disciplina_id = $_POST['disciplina_id'] ?? null;
    $professor_id = $_POST['professor_id'] ?? null;
    $semestre = $_POST['semestre'] ?? '';
    $horario = $_POST['horario'] ?? '';

    if ($disciplina_id && $professor_id && $semestre) {
        $stmt = $pdo->prepare("
            UPDATE turmas SET disciplina_id = ?, professor_id = ?, semestre = ?, horario = ? WHERE id = ?
        ");
        $stmt->execute([$disciplina_id, $professor_id, $semestre, $horario, $id]);

        header("Location: index.php");
        exit;
    } else {
        $erro = "Por favor, preencha os campos obrigatórios.";
    }
}
?>

<main>
    <h1>Editar Turma</h1>

    <?php if (!empty($erro)): ?>
        <p style="color:red;"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="post">
        <label>
            Disciplina:<br>
            <select name="disciplina_id" required>
                <option value="">Selecione uma disciplina</option>
                <?php foreach ($disciplinas as $disciplina): ?>
                    <option value="<?= $disciplina['id'] ?>" <?= $disciplina['id'] == $turma['disciplina_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($disciplina['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <label>
            Professor:<br>
            <select name="professor_id" required>
                <option value="">Selecione um professor</option>
                <?php foreach ($professores as $prof): ?>
                    <option value="<?= $prof['id'] ?>" <?= $prof['id'] == $turma['professor_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($prof['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <label>
            Semestre:<br>
            <input type="text" name="semestre" value="<?= htmlspecialchars($turma['semestre']) ?>" required>
        </label><br><br>

        <label>
            Horário:<br>
            <input type="text" name="horario" value="<?= htmlspecialchars($turma['horario']) ?>">
        </label><br><br>

        <button type="submit">Salvar</button>
        <a href="index.php">Cancelar</a>
    </form>
</main>

<script src="/../../../public/recursos/js/painel_admin.js"></script>

</body>
</html>
