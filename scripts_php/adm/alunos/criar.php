<?php
session_start();
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $matricula = $_POST['matricula'] ?? '';
    $senha = password_hash($_POST['senha'] ?? '', PASSWORD_DEFAULT);
    $email = $_POST['email'] ?? '';
    $curso_id = $_POST['curso_id'] ?? null;
    $periodo = $_POST['periodo'] ?? '';

    if ($nome && $matricula && $senha && $email && $curso_id) {
        // Insere na tabela usuarios
        $stmt1 = $pdo->prepare("INSERT INTO usuarios (nome, matricula, senha, tipo) VALUES (?, ?, ?, 'aluno')");
        $stmt1->execute([$nome, $matricula, $senha]);

        $usuario_id = $pdo->lastInsertId();

        // Insere na tabela alunos
        $stmt2 = $pdo->prepare("INSERT INTO alunos (id, curso_id, periodo_entrada, email) VALUES (?, ?, ?, ?)");
        $stmt2->execute([$usuario_id, $curso_id, $periodo, $email]);

        header("Location: index.php");
        exit;
    } else {
        echo "Preencha todos os campos obrigatórios.";
    }
}

// Pega os cursos para o select
$cursos = $pdo->query("SELECT id, nome FROM cursos")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Aluno</title>
</head>
<body>
    <h1>Criar Novo Aluno</h1>

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
            Curso:<br>
            <select name="curso_id" required>
                <option value="">Selecione um curso</option>
                <?php foreach ($cursos as $curso): ?>
                    <option value="<?= $curso['id'] ?>"><?= htmlspecialchars($curso['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <label>
            Período de Entrada:<br>
            <input type="text" name="periodo" placeholder="Ex: 2023.1">
        </label><br><br>

        <button type="submit">Salvar</button>
        <a href="index.php">Cancelar</a>
    </form>
</body>
</html>
