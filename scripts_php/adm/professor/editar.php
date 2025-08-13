<?php
require_once __DIR__ . '/../../../public/includes/header_admin.php';

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
    $nome = $_POST['nome'] ?? '';
    $departamento = $_POST['departamento'] ?? '';
    $email = $_POST['email'] ?? '';
    $imagem_id = $professor['imagem_id'];

     // Verificar se foi enviada uma nova imagem
    if (!empty($_FILES['imagem']['name'])) {
        $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $novoNome = uniqid() . '.' . $extensao;
        $caminho = __DIR__ . '/../../../public/recursos/storage/' . $novoNome;

        // Mover o arquivo para a pasta storage
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho)) {
            // Inserir o caminho da nova imagem na tabela imagens
            $stmt = $pdo->prepare("INSERT INTO imagens (path) VALUES (?)");
            $stmt->execute([$novoNome]);
            $imagem_id = $pdo->lastInsertId();
        }
    }

    // Atualiza o nome no usuarios
    $stmt1 = $pdo->prepare("UPDATE usuarios SET nome = ? WHERE id = ?");
    $stmt1->execute([$nome, $id]);

    // Atualiza departamento, email e imagem no professores
    $stmt2 = $pdo->prepare("UPDATE professores SET departamento = ?, email = ?, imagem_id = ? WHERE id = ?");
    $stmt2->execute([$departamento, $email, $imagem_id, $id]);

    header("Location: visualizar.php?id=" . $id);
    exit;
}
?>
<main class="form-edit-main">
    <h1 class="form-edit-title">Editar Professor</h1>

    <form method="post" enctype="multipart/form-data" class="form-edit-form">
        <label for="nome" class="form-edit-label">Nome:</label><br>
        <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($professor['nome']) ?>" required class="form-edit-input-text"><br><br>

        <label for="matricula" class="form-edit-label">Matrícula:</label><br>
        <input type="text" id="matricula" value="<?= htmlspecialchars($professor['matricula']) ?>" readonly class="form-edit-input-text-readonly"><br>
        <small class="form-edit-note">A matrícula não pode ser alterada.</small><br><br>

        <label for="departamento" class="form-edit-label">Departamento:</label><br>
        <input type="text" id="departamento" name="departamento" value="<?= htmlspecialchars($professor['departamento']) ?>" class="form-edit-input-text"><br><br>

        <label for="email" class="form-edit-label">Email:</label><br>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($professor['email']) ?>" required class="form-edit-input-text"><br><br>

        <label for="imagem" class="form-edit-label">Imagem de Perfil:</label><br>
        <input type="file" id="imagem" name="imagem" accept="image/*" class="form-edit-input-file"><br><br>

        <button type="submit" class="form-edit-btn-primary">Salvar</button>
        <a href="index.php" class="form-edit-link-secondary">Cancelar</a>
    </form>
</main>


<script src="/../../../public/recursos/js/painel_admin.js"></script>

</body>
</html>