<?php
require_once __DIR__ . '/../../../public/includes/header_admin.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM disciplinas WHERE id = ?");
$stmt->execute([$id]);
$disciplina = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$disciplina) {
    echo "Disciplina não encontrada.";
    exit;
}

$cursos = $pdo->query("SELECT id, nome FROM cursos")->fetchAll(PDO::FETCH_ASSOC);

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $codigo = $_POST['codigo'] ?? '';
    $carga_horaria = $_POST['carga_horaria'] ?? '';
    $curso_id = $_POST['curso_id'] ?? '';

    if ($nome && $codigo && $carga_horaria && $curso_id) {
        $stmt = $pdo->prepare("UPDATE disciplinas SET nome = ?, codigo = ?, carga_horaria = ?, curso_id = ? WHERE id = ?");
        $stmt->execute([$nome, $codigo, $carga_horaria, $curso_id, $id]);

        header("Location: index.php");
        exit;
    } else {
        $erro = "Preencha todos os campos obrigatórios.";
    }
}
?>

<main class="form-edit-main">
    <h1 class="form-edit-title">Editar Disciplina</h1>

    <?php if ($erro): ?>
        <p class="form-edit-msg-error"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="post" class="form-edit-form">
        <label class="form-edit-label">
            Nome:<br>
            <input type="text" name="nome" value="<?= htmlspecialchars($disciplina['nome']) ?>" required class="form-edit-input-text">
        </label><br><br>

        <label class="form-edit-label">
            Código:<br>
            <input type="text" name="codigo" value="<?= htmlspecialchars($disciplina['codigo']) ?>" required class="form-edit-input-text">
        </label><br><br>

        <label class="form-edit-label">
            Carga Horária:<br>
            <input type="number" name="carga_horaria" value="<?= htmlspecialchars($disciplina['carga_horaria']) ?>" required class="form-edit-input-text">
        </label><br><br>

        <label class="form-edit-label">
            Curso:<br>
            <select name="curso_id" required class="form-edit-select">
                <option value="">Selecione um curso</option>
                <?php foreach ($cursos as $curso): ?>
                    <option value="<?= $curso['id'] ?>" <?= $curso['id'] == $disciplina['curso_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($curso['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <button type="submit" class="form-edit-btn-primary">Salvar</button>
        <a href="index.php" class="form-edit-link-secondary">Cancelar</a>
    </form>

    <script src="/../../../public/recursos/js/painel_admin.js"></script>
</main>


</body>
</html>
