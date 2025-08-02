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
    $curso_id = $_POST['curso_id'] ?? null;
    $periodo = trim($_POST['periodo'] ?? '');

    if (!$nome || !$matricula_id || !$senha || !$email || !$curso_id) {
        $erro = "Preencha todos os campos obrigatórios.";
    } else {
        // Verifica se matrícula existe e está disponível
        $stmt = $pdo->prepare("SELECT * FROM matriculas_academicas WHERE id = ? AND usada = FALSE AND tipo = 'aluno'");
        $stmt->execute([$matricula_id]);
        $matricula = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$matricula) {
            $erro = "Matrícula inválida ou já usada.";
        } else {
            try {
                $pdo->beginTransaction();

                // Inserir usuário
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt1 = $pdo->prepare("INSERT INTO usuarios (nome, matricula, senha, tipo) VALUES (?, ?, ?, 'aluno')");
                $stmt1->execute([$nome, $matricula['matricula'], $senhaHash]);
                $usuario_id = $pdo->lastInsertId();

                // Inserir aluno
                $stmt2 = $pdo->prepare("INSERT INTO alunos (id, curso_id, periodo_entrada, email) VALUES (?, ?, ?, ?)");
                $stmt2->execute([$usuario_id, $curso_id, $periodo, $email]);

                // Atualizar matrícula para usada
                $stmt3 = $pdo->prepare("UPDATE matriculas_academicas SET usada = TRUE WHERE id = ?");
                $stmt3->execute([$matricula_id]);

                $pdo->commit();

                $sucesso = "Aluno cadastrado com sucesso!";
                // Limpar campos
                $nome = $email = $periodo = '';
                $curso_id = $matricula_id = '';
                $senha = '';
            } catch (Exception $e) {
                $pdo->rollBack();
                $erro = "Erro ao cadastrar aluno: " . $e->getMessage();
            }
        }
    }
}

// Pega os cursos para o select
$cursos = $pdo->query("SELECT id, nome FROM cursos ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

// Pega as matrículas acadêmicas disponíveis para alunos
$stmt = $pdo->query("SELECT id, matricula FROM matriculas_academicas WHERE usada = FALSE AND tipo = 'aluno' ORDER BY matricula");
$matriculas_disponiveis = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Aluno</title>
</head>
<body>
    <h1>Criar Novo Aluno</h1>

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
            Curso:<br>
            <select name="curso_id" required>
                <option value="">Selecione um curso</option>
                <?php foreach ($cursos as $curso): ?>
                    <option value="<?= $curso['id'] ?>" <?= (isset($curso_id) && $curso_id == $curso['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($curso['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <label>
            Período de Entrada:<br>
            <input type="text" name="periodo" placeholder="Ex: 2023.1" value="<?= htmlspecialchars($periodo ?? '') ?>">
        </label><br><br>

        <button type="submit">Salvar</button>
        <a href="index.php">Cancelar</a>
    </form>
</body>
</html>



