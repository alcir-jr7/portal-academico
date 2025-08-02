<?php
session_start();
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $matricula_id = $_POST['matricula_id'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $departamento = trim($_POST['departamento'] ?? '');

    if (!$nome || !$matricula_id || !$senha || !$email) {
        $erro = "Preencha todos os campos obrigatórios.";
    } else {
        // Verifica se matrícula existe e está disponível
        $stmt = $pdo->prepare("SELECT * FROM matriculas_academicas WHERE id = ? AND usada = FALSE AND tipo = 'professor'");
        $stmt->execute([$matricula_id]);
        $matricula = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$matricula) {
            $erro = "Matrícula inválida ou já usada.";
        } else {
            try {
                $pdo->beginTransaction();

                // Inserir usuário
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt1 = $pdo->prepare("INSERT INTO usuarios (nome, matricula, senha, tipo) VALUES (?, ?, ?, 'professor')");
                $stmt1->execute([$nome, $matricula['matricula'], $senhaHash]);
                $usuario_id = $pdo->lastInsertId();

                // Inserir professor
                $stmt2 = $pdo->prepare("INSERT INTO professores (id, matricula, departamento, email) VALUES (?, ?, ?, ?)");
                $stmt2->execute([$usuario_id, $matricula['matricula'], $departamento, $email]);

                // Atualizar matrícula para usada
                $stmt3 = $pdo->prepare("UPDATE matriculas_academicas SET usada = TRUE WHERE id = ?");
                $stmt3->execute([$matricula_id]);

                $pdo->commit();

                $sucesso = "Professor cadastrado com sucesso!";
                // Limpar campos
                $nome = $email = $departamento = '';
                $matricula_id = '';
                $senha = '';
            } catch (Exception $e) {
                $pdo->rollBack();
                $erro = "Erro ao cadastrar professor: " . $e->getMessage();
            }
        }
    }
}

// Pega matrículas acadêmicas disponíveis para professores
$stmt = $pdo->query("SELECT id, matricula FROM matriculas_academicas WHERE usada = FALSE AND tipo = 'professor' ORDER BY matricula");
$matriculas_disponiveis = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Professor</title>
</head>
<body>
    <h1>Criar Novo Professor</h1>

    <?php if ($erro): ?>
        <p style="color: red;"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>
    <?php if ($sucesso): ?>
        <p style="color: green;"><?= htmlspecialchars($sucesso) ?></p>
    <?php endif; ?>

    <form method="post">
        <label>
            Nome:<br>
            <input type="text" name="nome" required value="<?= htmlspecialchars($nome ?? '') ?>">
        </label><br><br>

        <label>
            Matrícula Acadêmica:<br>
            <select name="matricula_id" required>
                <option value="">Selecione uma matrícula disponível</option>
                <?php foreach ($matriculas_disponiveis as $matricula_disp): ?>
                    <option value="<?= $matricula_disp['id'] ?>" <?= (isset($matricula_id) && $matricula_id == $matricula_disp['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($matricula_disp['matricula']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <label>
            Senha:<br>
            <input type="password" name="senha" required>
        </label><br><br>

        <label>
            Email:<br>
            <input type="email" name="email" required value="<?= htmlspecialchars($email ?? '') ?>">
        </label><br><br>

        <label>
            Departamento:<br>
            <input type="text" name="departamento" value="<?= htmlspecialchars($departamento ?? '') ?>">
        </label><br><br>

        <button type="submit">Salvar</button>
        <a href="index.php">Cancelar</a>
    </form>
</body>
</html>
