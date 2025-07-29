<?php
session_start();
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

// Pega cursos para o select
$cursos = $pdo->query("SELECT id, nome FROM cursos ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $codigo = $_POST['codigo'] ?? '';
    $carga_horaria = $_POST['carga_horaria'] ?? '';
    $curso_id = $_POST['curso_id'] ?? '';

    if ($nome && $codigo && $carga_horaria && $curso_id) {
        // Inserir disciplina
        $stmt = $pdo->prepare("INSERT INTO disciplinas (nome, codigo, carga_horaria, curso_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nome, $codigo, $carga_horaria, $curso_id]);

        header("Location: index.php");
        exit;
    } else {
        $erro = "Por favor, preencha todos os campos obrigatórios.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Criar Disciplina</title>
</head>
<body>
    <h1>Criar Nova Disciplina</h1>

    <?php if (!empty($erro)): ?>
        <p style="color:red;"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="post">
        <label>
            Nome:<br>
            <input type="text" name="nome" required>
        </label><br><br>

        <label>
            Código:<br>
            <input type="text" name="codigo" required>
        </label><br><br>

        <label>
            Carga Horária:<br>
            <input type="number" name="carga_horaria" required min="1">
        </label><br><br>

        <label>
            Curso:<br>
            <select name="curso_id" required>
                <option value="">Selecione um curso</option>
                <?php foreach ($cursos as $curso): ?>
                    <option value="<?= $curso['id'] ?>"><?= htmlspecialchars($curso['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <button type="submit">Salvar</button>
        <a href="index.php">Cancelar</a>
    </form>
</body>
</html>
