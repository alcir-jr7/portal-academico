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

<main>
    <h1>Criar Novo Administrador</h1>

    <?php if ($erro): ?>
        <p style="color: red;"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>
    <?php if ($sucesso): ?>
        <p style="color: green;"><?= htmlspecialchars($sucesso) ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="nome">Nome:</label><br>
        <input type="text" id="nome" name="nome" required value="<?= htmlspecialchars($nome ?? '') ?>"><br><br>

        <label for="senha">Senha:</label><br>
        <input type="password" id="senha" name="senha" required><br><br>

        <label for="setor">Setor:</label><br>
        <input type="text" id="setor" name="setor" required value="<?= htmlspecialchars($setor ?? '') ?>"><br><br>

        <button type="submit">Adicionar</button>
        <a href="index.php">Cancelar</a>
    </form>
</main>

<script src="/../../../public/recursos/js/painel_admin.js"></script>

</body>
</html>
