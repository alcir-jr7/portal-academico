<?php
session_start();
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricula = trim($_POST['matricula'] ?? '');
    $tipo = $_POST['tipo'] ?? '';

    if (!$matricula || !$tipo) {
        $erro = "Matrícula e tipo são obrigatórios.";
    } else {
        // Verificar se matrícula já existe
        $stmt = $pdo->prepare("SELECT id FROM matriculas_academicas WHERE matricula = ?");
        $stmt->execute([$matricula]);
        if ($stmt->fetch()) {
            $erro = "Esta matrícula já está cadastrada.";
        } else {
            // Inserir no banco
            $stmt = $pdo->prepare("INSERT INTO matriculas_academicas (matricula, tipo) VALUES (?, ?)");
            if ($stmt->execute([$matricula, $tipo])) {
                $sucesso = "Matrícula cadastrada com sucesso!";
                // Limpar campos
                $matricula = '';
                $tipo = '';
            } else {
                $erro = "Erro ao cadastrar matrícula.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Cadastrar Matrícula Acadêmica</title>
</head>
<body>
    <h1>Nova Matrícula Acadêmica</h1>

    <?php if ($erro): ?>
        <p style="color: red;"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <?php if ($sucesso): ?>
        <p style="color: green;"><?= htmlspecialchars($sucesso) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="matricula">Matrícula:</label><br>
        <input type="text" id="matricula" name="matricula" required value="<?= htmlspecialchars($matricula ?? '') ?>"><br><br>

        <label for="tipo">Tipo:</label><br>
        <select id="tipo" name="tipo" required>
            <option value="">Selecione</option>
            <option value="aluno" <?= (isset($tipo) && $tipo === 'aluno') ? 'selected' : '' ?>>Aluno</option>
            <option value="professor" <?= (isset($tipo) && $tipo === 'professor') ? 'selected' : '' ?>>Professor</option>
            <option value="admin" <?= (isset($tipo) && $tipo === 'admin') ? 'selected' : '' ?>>Admin</option>
        </select><br><br>

        <button type="submit">Cadastrar</button>
        <a href="index.php">Cancelar</a>
    </form>
</body>
</html>
