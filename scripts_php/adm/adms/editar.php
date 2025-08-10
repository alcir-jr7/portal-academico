<?php
require_once __DIR__ . '/../../../public/includes/header_admin.php';

$erro = '';
$sucesso = '';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

// Buscar dados do admin (usuario + administrador)
$stmt = $pdo->prepare("
    SELECT u.*, a.setor
    FROM usuarios u
    JOIN administradores a ON a.id = u.id
    WHERE u.id = ? AND u.tipo = 'admin'
");
$stmt->execute([$id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    echo "Administrador não encontrado.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $setor = trim($_POST['setor'] ?? '');

    if (!$nome || !$setor) {
        $erro = "Nome e setor são obrigatórios.";
    } else {
        try {
            $pdo->beginTransaction();

            if ($senha) {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, senha = ? WHERE id = ?");
                $stmt->execute([$nome, $senhaHash, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE usuarios SET nome = ? WHERE id = ?");
                $stmt->execute([$nome, $id]);
            }

            $stmt2 = $pdo->prepare("UPDATE administradores SET setor = ? WHERE id = ?");
            $stmt2->execute([$setor, $id]);

            $pdo->commit();

            $sucesso = "Administrador atualizado com sucesso!";
            $admin['nome'] = $nome;
            $admin['setor'] = $setor;
        } catch (Exception $e) {
            $pdo->rollBack();
            $erro = "Erro ao atualizar administrador: " . $e->getMessage();
        }
    }
}
?>

<main>
    <h1>Editar Administrador</h1>

    <?php if ($erro): ?>
        <p style="color: red;"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>
    <?php if ($sucesso): ?>
        <p style="color: green;"><?= htmlspecialchars($sucesso) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Matrícula (não editável):</label><br>
        <input type="text" value="<?= htmlspecialchars($admin['matricula']) ?>" readonly><br><br>

        <label for="nome">Nome:</label><br>
        <input type="text" id="nome" name="nome" required value="<?= htmlspecialchars($admin['nome']) ?>"><br><br>

        <label for="senha">Senha (deixe vazio para não alterar):</label><br>
        <input type="password" id="senha" name="senha" autocomplete="new-password"><br><br>

        <label for="setor">Setor:</label><br>
        <input type="text" id="setor" name="setor" required value="<?= htmlspecialchars($admin['setor']) ?>"><br><br>

        <button type="submit">Salvar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</main>

<script src="/../../../public/recursos/js/painel_admin.js"></script>
</body>
</html>
