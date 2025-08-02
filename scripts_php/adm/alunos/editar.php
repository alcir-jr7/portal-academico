<?php
session_start();
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "ID do aluno não informado.";
    exit;
}

// Busca dados atuais do aluno
$stmt = $pdo->prepare("
    SELECT u.nome, u.matricula, a.email, a.periodo_entrada, a.curso_id
    FROM alunos a
    JOIN usuarios u ON a.id = u.id
    WHERE a.id = ?
");
$stmt->execute([$id]);
$aluno = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$aluno) {
    echo "Aluno não encontrado.";
    exit;
}

// Lista de cursos para o <select>
$cursos = $pdo->query("SELECT id, nome FROM cursos")->fetchAll(PDO::FETCH_ASSOC);

// Atualiza se enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    // matrícula não será alterada
    $email = $_POST['email'] ?? '';
    $periodo = $_POST['periodo_entrada'] ?? '';
    $curso_id = $_POST['curso_id'] ?? null;

    // Atualiza na tabela usuarios (sem alterar matrícula)
    $stmt1 = $pdo->prepare("UPDATE usuarios SET nome = ? WHERE id = ?");
    $stmt1->execute([$nome, $id]);

    // Atualiza na tabela alunos
    $stmt2 = $pdo->prepare("UPDATE alunos SET email = ?, periodo_entrada = ?, curso_id = ? WHERE id = ?");
    $stmt2->execute([$email, $periodo, $curso_id, $id]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Aluno</title>
</head>
<body>
    <h1>Editar Aluno</h1>

    <form method="post">
        <label>
            Nome:<br>
            <input type="text" name="nome" value="<?= htmlspecialchars($aluno['nome']) ?>" required>
        </label>
        <br><br>

        <label>
            Matrícula:<br>
            <input type="text" name="matricula" value="<?= htmlspecialchars($aluno['matricula']) ?>" readonly style="background-color: #eee; border: 1px solid #ccc; cursor: not-allowed;">
        </label>
        <br>
        <small style="color: #555;">A matrícula não pode ser alterada.</small>
        <br><br>

        <label>
            Email:<br>
            <input type="email" name="email" value="<?= htmlspecialchars($aluno['email']) ?>" required>
        </label>
        <br><br>

        <label>
            Período de Entrada:<br>
            <input type="text" name="periodo_entrada" value="<?= htmlspecialchars($aluno['periodo_entrada']) ?>">
        </label>
        <br><br>

        <label>
            Curso:<br>
            <select name="curso_id" required>
                <?php foreach ($cursos as $curso): 
                    $selected = $curso['id'] == $aluno['curso_id'] ? 'selected' : '';
                ?>
                    <option value="<?= $curso['id'] ?>" <?= $selected ?>>
                        <?= htmlspecialchars($curso['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <br><br>

        <button type="submit">Salvar</button>
        <a href="index.php">Cancelar</a>
    </form>
</body>
</html>
