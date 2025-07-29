<?php
session_start();
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "ID do professor não informado.";
    exit;
}

// Busca dados atuais
$stmt = $pdo->prepare("SELECT p.*, u.nome, u.matricula FROM professores p JOIN usuarios u ON p.id = u.id WHERE p.id = ?");
$stmt->execute([$id]);
$professor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$professor) {
    echo "Professor não encontrado.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $departamento = $_POST['departamento'] ?? '';
    $email = $_POST['email'] ?? '';

    $stmt = $pdo->prepare("UPDATE professores SET departamento = ?, email = ? WHERE id = ?");
    $stmt->execute([$departamento, $email, $id]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Editar Professor</title>
</head>
<body>
    <h1>Editar Professor</h1>

    <form method="post">
        <label>
            Nome:<br>
            <input type="text" value="<?= htmlspecialchars($professor['nome']) ?>">
        </label><br><br>

        <label>
            Matrícula:<br>
            <input type="text" value="<?= htmlspecialchars($professor['matricula']) ?>" disabled>
        </label><br><br>

        <label>
            Departamento:<br>
            <input type="text" name="departamento" value="<?= htmlspecialchars($professor['departamento']) ?>">
        </label><br><br>

        <label>
            Email:<br>
            <input type="email" name="email" value="<?= htmlspecialchars($professor['email']) ?>" required>
        </label><br><br>

        <button type="submit">Salvar</button>
        <a href="index.php">Cancelar</a>
    </form>
</body>
</html>
