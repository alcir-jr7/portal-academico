<?php
session_start();
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $matricula = $_POST['matricula'] ?? '';
    $senha = password_hash($_POST['senha'] ?? '', PASSWORD_DEFAULT);
    $email = $_POST['email'] ?? '';
    $departamento = $_POST['departamento'] ?? '';

    if ($nome && $matricula && $senha && $email) {
        try {
            $pdo->beginTransaction();

            // Inserir usuário
            $stmt1 = $pdo->prepare("INSERT INTO usuarios (nome, matricula, senha, tipo) VALUES (?, ?, ?, 'professor')");
            $stmt1->execute([$nome, $matricula, $senha]);

            $usuario_id = $pdo->lastInsertId();

            // Inserir professor
            $stmt2 = $pdo->prepare("INSERT INTO professores (id, matricula, departamento, email) VALUES (?, ?, ?, ?)");
            $stmt2->execute([$usuario_id, $matricula, $departamento, $email]);

            $pdo->commit();

            header("Location: index.php");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "Erro ao cadastrar professor: " . $e->getMessage();
        }
    } else {
        echo "Preencha todos os campos obrigatórios.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Professor</title>
</head>
<body>
    <h1>Criar Novo Professor</h1>

    <form method="post">
        <label>
            Nome:<br>
            <input type="text" name="nome" required>
        </label><br><br>

        <label>
            Matrícula:<br>
            <input type="text" name="matricula" required>
        </label><br><br>

        <label>
            Senha:<br>
            <input type="password" name="senha" required>
        </label><br><br>

        <label>
            Email:<br>
            <input type="email" name="email" required>
        </label><br><br>

        <label>
            Departamento:<br>
            <input type="text" name="departamento">
        </label><br><br>

        <button type="submit">Salvar</button>
        <a href="index.php">Cancelar</a>
    </form>
</body>
</html>
