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
<main class="form-edit-main">
    <h1 class="form-edit-title">Editar Turma</h1>

    <?php if (!empty($erro)): ?>
        <p class="form-edit-error"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="post" class="form-edit-form">
        <label class="form-edit-label">
            Disciplina:<br>
            <select name="disciplina_id" required class="form-edit-select">
                <option value="">Selecione uma disciplina</option>
                <?php foreach ($disciplinas as $disciplina): ?>
                    <option value="<?= $disciplina['id'] ?>" <?= $disciplina['id'] == $turma['disciplina_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($disciplina['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <label class="form-edit-label">
            Professor:<br>
            <select name="professor_id" required class="form-edit-select">
                <option value="">Selecione um professor</option>
                <?php foreach ($professores as $prof): ?>
                    <option value="<?= $prof['id'] ?>" <?= $prof['id'] == $turma['professor_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($prof['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <label class="form-edit-label">
            Semestre:<br>
            <input type="text" name="semestre" value="<?= htmlspecialchars($turma['semestre']) ?>" required class="form-edit-input-text">
        </label><br><br>

        <label class="form-edit-label">
            Horário:<br>
            <input type="text" name="horario" value="<?= htmlspecialchars($turma['horario']) ?>" class="form-edit-input-text">
        </label><br><br>

        <button type="submit" class="form-edit-btn-primary">Salvar</button>
        <a href="index.php" class="form-edit-link-secondary">Cancelar</a>
    </form>
</main>


<script src="/../../../public/recursos/js/painel_admin.js"></script>

</body>
</html>
