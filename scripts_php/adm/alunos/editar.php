<?php
require_once __DIR__ . '/../../../public/includes/header_admin.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<main><p>ID do aluno não informado.</p></main></body></html>";
    exit;
}

// Busca dados atuais do aluno
$stmt = $pdo->prepare("
    SELECT u.nome, u.matricula, a.email, a.periodo_entrada, a.curso_id, a.imagem_id
    FROM alunos a
    JOIN usuarios u ON a.id = u.id
    WHERE a.id = ?
");
$stmt->execute([$id]);
$aluno = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$aluno) {
    echo "<main><p>Aluno não encontrado.</p></main></body></html>";
    exit;
}

// Lista de cursos para o <select>
$cursos = $pdo->query("SELECT id, nome FROM cursos")->fetchAll(PDO::FETCH_ASSOC);

// Atualiza se enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $periodo = $_POST['periodo_entrada'] ?? '';
    $curso_id = $_POST['curso_id'] ?? null;
    $imagem_id = $aluno['imagem_id'];

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

    // Atualiza na tabela usuarios (sem alterar matrícula)
    $stmt1 = $pdo->prepare("UPDATE usuarios SET nome = ? WHERE id = ?");
    $stmt1->execute([$nome, $id]);

    // Atualiza na tabela alunos
    $stmt2 = $pdo->prepare("UPDATE alunos SET email = ?, periodo_entrada = ?, curso_id = ?, imagem_id = ? WHERE id = ?");
    $stmt2->execute([$email, $periodo, $curso_id, $imagem_id, $id]);

    header("Location: visualizar.php?id=" . $id);
    exit;
}
?>

<main class="form-edit-main">
    <h1 class="form-edit-title">Editar Aluno</h1>

    <form method="post" enctype="multipart/form-data" class="form-edit-form">
        <label for="nome" class="form-edit-label">Nome:</label><br>
        <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($aluno['nome']) ?>" required class="form-edit-input-text"><br><br>

        <label for="matricula" class="form-edit-label">Matrícula:</label><br>
        <input type="text" id="matricula" name="matricula" value="<?= htmlspecialchars($aluno['matricula']) ?>" readonly class="form-edit-input-text-disabled"><br><br>
        <small class="form-edit-note">A matrícula não pode ser alterada.</small>

        <label for="email" class="form-edit-label">Email:</label><br>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($aluno['email']) ?>" required class="form-edit-input-text"><br><br>

        <label for="periodo_entrada" class="form-edit-label">Período de Entrada:</label><br>
        <input type="text" id="periodo_entrada" name="periodo_entrada" value="<?= htmlspecialchars($aluno['periodo_entrada']) ?>" class="form-edit-input-text"><br><br>

        <label for="curso_id" class="form-edit-label">Curso:</label><br>
        <select id="curso_id" name="curso_id" required class="form-edit-select">
            <?php foreach ($cursos as $curso): 
                $selected = $curso['id'] == $aluno['curso_id'] ? 'selected' : '';
            ?>
                <option value="<?= $curso['id'] ?>" <?= $selected ?>>
                    <?= htmlspecialchars($curso['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="imagem" class="form-edit-label">Imagem de Perfil:</label><br>
        <input type="file" id="imagem" name="imagem" accept="image/*" class="form-edit-file"><br><br>

        <button type="submit" class="form-edit-btn-primary">Atualizar</button>
        <a href="index.php" class="form-edit-link-secondary">Cancelar</a>
    </form>
</main>


<script src="/../../../public/recursos/js/painel_admin.js"></script>

</body>
</html>