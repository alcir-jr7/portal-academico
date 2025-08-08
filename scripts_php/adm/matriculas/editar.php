<?php
require_once __DIR__ . '/../../../public/includes/header_admin.php';

$erro = '';
$sucesso = '';

// Pega o ID da matrícula a editar
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

// Busca os dados atuais da matrícula
$stmt = $pdo->prepare("SELECT * FROM matriculas_academicas WHERE id = ?");
$stmt->execute([$id]);
$matricula = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$matricula) {
    echo "Matrícula não encontrada.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nova_matricula = trim($_POST['matricula'] ?? '');
    $novo_tipo = $_POST['tipo'] ?? '';
    $nova_usada = isset($_POST['usada']) ? 1 : 0;

    if (!$nova_matricula || !$novo_tipo) {
        $erro = "Matrícula e tipo são obrigatórios.";
    } else {
        // Verifica se matrícula nova já existe (diferente do atual)
        $stmt = $pdo->prepare("SELECT id FROM matriculas_academicas WHERE matricula = ? AND id != ?");
        $stmt->execute([$nova_matricula, $id]);
        if ($stmt->fetch()) {
            $erro = "Esta matrícula já está cadastrada para outro registro.";
        } else {
            // Atualiza
            $stmt = $pdo->prepare("UPDATE matriculas_academicas SET matricula = ?, tipo = ?, usada = ? WHERE id = ?");
            if ($stmt->execute([$nova_matricula, $novo_tipo, $nova_usada, $id])) {
                $sucesso = "Matrícula atualizada com sucesso!";
                // Atualiza variável para refletir no formulário
                $matricula['matricula'] = $nova_matricula;
                $matricula['tipo'] = $novo_tipo;
                $matricula['usada'] = $nova_usada;
            } else {
                $erro = "Erro ao atualizar matrícula.";
            }
        }
    }
}
?>

<main>
    <h1>Editar Matrícula Acadêmica</h1>

    <?php if ($erro): ?>
        <p style="color: red;"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <?php if ($sucesso): ?>
        <p style="color: green;"><?= htmlspecialchars($sucesso) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="matricula">Matrícula:</label><br>
        <input type="text" id="matricula" name="matricula" required value="<?= htmlspecialchars($matricula['matricula']) ?>"><br><br>

        <label for="tipo">Tipo:</label><br>
        <select id="tipo" name="tipo" required>
            <option value="">Selecione</option>
            <option value="aluno" <?= ($matricula['tipo'] === 'aluno') ? 'selected' : '' ?>>Aluno</option>
            <option value="professor" <?= ($matricula['tipo'] === 'professor') ? 'selected' : '' ?>>Professor</option>
            <option value="admin" <?= ($matricula['tipo'] === 'admin') ? 'selected' : '' ?>>Admin</option>
        </select><br><br>

        <label>
            <input type="checkbox" name="usada" value="1" <?= $matricula['usada'] ? 'checked' : '' ?>>
            Matrícula usada
        </label><br><br>

        <button type="submit">Salvar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>

    <script src="/../../../public/recursos/js/painel_admin.js"></script>
</main>

</body>
</html>
