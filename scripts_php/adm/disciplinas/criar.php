<?php
require_once __DIR__ . '/../../../public/includes/header_admin.php';

$cursos = $pdo->query("SELECT id, nome FROM cursos ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $codigo = $_POST['codigo'] ?? '';
    $carga_horaria = $_POST['carga_horaria'] ?? '';
    $curso_id = $_POST['curso_id'] ?? '';

    if ($nome && $codigo && $carga_horaria && $curso_id) {
        $stmt = $pdo->prepare("INSERT INTO disciplinas (nome, codigo, carga_horaria, curso_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nome, $codigo, $carga_horaria, $curso_id]);

        header("Location: index.php");
        exit;
    } else {
        $erro = "Por favor, preencha todos os campos obrigatórios.";
    }
}
?>
<main class="form-create-container">
    <h1 class="form-create-title">Criar Nova Disciplina</h1>

    <?php if ($erro): ?>
        <p class="form-create-error-msg"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="post" class="form-create-form">
        <label class="form-create-label" for="nome">
            Nome:
        </label><br>
        <input type="text" name="nome" id="nome" required class="form-create-input"><br><br>

        <label class="form-create-label" for="codigo">
            Código:
        </label><br>
        <input type="text" name="codigo" id="codigo" required class="form-create-input"><br><br>

        <label class="form-create-label" for="carga_horaria">
            Carga Horária:
        </label><br>
        <input type="number" name="carga_horaria" id="carga_horaria" required min="1" class="form-create-input"><br><br>

        <label class="form-create-label" for="curso_id">
            Curso:
        </label><br>
        <select name="curso_id" id="curso_id" required class="form-create-select">
            <option value="">Selecione um curso</option>
            <?php foreach ($cursos as $curso): ?>
                <option value="<?= $curso['id'] ?>"><?= htmlspecialchars($curso['nome']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit" class="form-create-btn-primary">Salvar</button>
        <a href="index.php" class="form-create-link-secondary">Cancelar</a>
    </form>

    <script src="/../../../public/recursos/js/painel_admin.js"></script>
</main>


</body>
</html>
