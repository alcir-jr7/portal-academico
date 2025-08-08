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

<main>
    <h1>Editar Disciplina</h1>

    <?php if ($erro): ?>
        <p style="color:red;"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="post">
        <label>
            Nome:<br>
            <input type="text" name="nome" value="<?= htmlspecialchars($disciplina['nome']) ?>" required>
        </label><br><br>

        <label>
            Código:<br>
            <input type="text" name="codigo" value="<?= htmlspecialchars($disciplina['codigo']) ?>" required>
        </label><br><br>

        <label>
            Carga Horária:<br>
            <input type="number" name="carga_horaria" value="<?= htmlspecialchars($disciplina['carga_horaria']) ?>" required>
        </label><br><br>

        <label>
            Curso:<br>
            <select name="curso_id" required>
                <option value="">Selecione um curso</option>
                <?php foreach ($cursos as $curso): ?>
                    <option value="<?= $curso['id'] ?>" <?= $curso['id'] == $disciplina['curso_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($curso['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <button type="submit">Salvar</button>
        <a href="index.php">Cancelar</a>
    </form>

    <script src="/../../../public/recursos/js/painel_admin.js"></script>
</main>

</body>
</html>
