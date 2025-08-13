<?php
require_once __DIR__ . '/../../../public/includes/header_admin.php';

$erro = '';
$sucesso = '';
$nome = '';
$senha = '';
$setor = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $setor = trim($_POST['setor'] ?? '');

    if (!$nome || !$senha || !$setor) {
        $erro = "Preencha todos os campos obrigatórios.";
    } else {
        try {
            // Buscar a maior matrícula ADM já cadastrada
            $stmt = $pdo->query("SELECT matricula FROM matriculas_academicas WHERE tipo = 'admin' AND matricula LIKE 'ADM%' ORDER BY id DESC LIMIT 1");
            $ultimaMatricula = $stmt->fetchColumn();

            if ($ultimaMatricula) {
                $numero = (int)substr($ultimaMatricula, 3);
                $novoNumero = $numero + 1;
            } else {
                $novoNumero = 1;
            }

            $novaMatricula = 'ADM' . $novoNumero;

            // Inserir nova matrícula acadêmica
            $stmt = $pdo->prepare("INSERT INTO matriculas_academicas (matricula, tipo, usada) VALUES (?, 'admin', FALSE)");
            $stmt->execute([$novaMatricula]);
            $matricula_id = $pdo->lastInsertId();

            $pdo->beginTransaction();

            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            // Inserir usuário com matrícula gerada automaticamente
            $stmt1 = $pdo->prepare("INSERT INTO usuarios (nome, matricula, senha, tipo) VALUES (?, ?, ?, 'admin')");
            $stmt1->execute([$nome, $novaMatricula, $senhaHash]);
            $usuario_id = $pdo->lastInsertId();

            // Inserir administrador (sem email e imagem_id)
            $stmt2 = $pdo->prepare("INSERT INTO administradores (id, setor) VALUES (?, ?)");
            $stmt2->execute([$usuario_id, $setor]);

            // Atualizar matrícula para usada
            $stmt3 = $pdo->prepare("UPDATE matriculas_academicas SET usada = TRUE WHERE id = ?");
            $stmt3->execute([$matricula_id]);

            $pdo->commit();

            $sucesso = "Administrador cadastrado com sucesso! Matrícula: $novaMatricula";
            $nome = $senha = $setor = '';

        } catch (Exception $e) {
            $pdo->rollBack();
            $erro = "Erro ao cadastrar administrador: " . $e->getMessage();
        }
    }
}
?>

<main class="form-create-container">
    <h1 class="form-create-title">Criar Novo Administrador</h1>

    <?php if ($erro): ?>
        <p class="form-create-error"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>
    <?php if ($sucesso): ?>
        <p class="form-create-success"><?= htmlspecialchars($sucesso) ?></p>
    <?php endif; ?>

    <form method="post" class="form-create-form">
        <label for="nome" class="form-create-label">Nome:</label><br>
        <input type="text" id="nome" name="nome" required class="form-create-input" value="<?= htmlspecialchars($nome ?? '') ?>"><br><br>

        <label for="senha" class="form-create-label">Senha:</label><br>
        <input type="password" id="senha" name="senha" required class="form-create-input"><br><br>

        <label for="setor" class="form-create-label">Setor:</label><br>
        <input type="text" id="setor" name="setor" required class="form-create-input" value="<?= htmlspecialchars($setor ?? '') ?>"><br><br>

        <button type="submit" class="form-create-btn-primary">Adicionar</button>
        <a href="index.php" class="form-create-link-secondary">Cancelar</a>
    </form>
</main>

<script src="/../../../public/recursos/js/painel_admin.js"></script>


</body>
</html>
