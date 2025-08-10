<?php
require_once __DIR__ . '/../../../public/includes/header_admin.php';

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
        $stmt = $pdo->prepare("SELECT * FROM matriculas_academicas WHERE id = ? AND usada = FALSE AND tipo = 'aluno'");
        $stmt->execute([$matricula_id]);
        $matricula = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$matricula) {
            $erro = "Matrícula inválida ou já usada.";
        } else {
            // Verificar se foi enviada uma imagem
            if (!empty($_FILES['imagem']['name'])) {
                $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
                $novoNome = uniqid() . '.' . $extensao;
                $caminho = __DIR__ . '/../../../public/recursos/storage/' . $novoNome;

                // Mover o arquivo para a pasta storage
                if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho)) {
                    // Inserir o caminho da imagem na tabela imagens
                    $stmt = $pdo->prepare("INSERT INTO imagens (path) VALUES (?)");
                    $stmt->execute([$novoNome]);
                    $imagem_id = $pdo->lastInsertId();
                }
            } else {
                $imagem_id = null;
            }

            try {
                $pdo->beginTransaction();

                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt1 = $pdo->prepare("INSERT INTO usuarios (nome, matricula, senha, tipo) VALUES (?, ?, ?, 'aluno')");
                $stmt1->execute([$nome, $matricula['matricula'], $senhaHash]);
                $usuario_id = $pdo->lastInsertId();

                $stmt2 = $pdo->prepare("INSERT INTO alunos (id, curso_id, periodo_entrada, email, imagem_id) VALUES (?, ?, ?, ?, ?)");
                $stmt2->execute([$usuario_id, $curso_id, $periodo, $email, $imagem_id]);

                $stmt3 = $pdo->prepare("UPDATE matriculas_academicas SET usada = TRUE WHERE id = ?");
                $stmt3->execute([$matricula_id]);

                $pdo->commit();

                $sucesso = "Aluno cadastrado com sucesso!";
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

$cursos = $pdo->query("SELECT id, nome FROM cursos ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$stmt = $pdo->query("SELECT id, matricula FROM matriculas_academicas WHERE usada = FALSE AND tipo = 'aluno' ORDER BY matricula");
$matriculas_disponiveis = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <h1>Criar Novo Aluno</h1>

    <?php if ($erro): ?>
        <p style="color: red;"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>
    <?php if ($sucesso): ?>
        <p style="color: green;"><?= htmlspecialchars($sucesso) ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label for="nome">Nome:</label><br>
        <input type="text" id="nome" name="nome" required value="<?= htmlspecialchars($nome ?? '') ?>"><br><br>

        <label for="matricula_id">Matrícula Acadêmica:</label><br>
        <select id="matricula_id" name="matricula_id" required>
            <option value="">Selecione uma matrícula disponível</option>
            <?php foreach ($matriculas_disponiveis as $matricula_disp): ?>
                <option value="<?= $matricula_disp['id'] ?>" <?= (isset($matricula_id) && $matricula_id == $matricula_disp['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($matricula_disp['matricula']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="senha">Senha:</label><br>
        <input type="password" id="senha" name="senha" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($email ?? '') ?>"><br><br>

        <label for="curso_id">Curso:</label><br>
        <select id="curso_id" name="curso_id" required>
            <option value="">Selecione um curso</option>
            <?php foreach ($cursos as $curso): ?>
                <option value="<?= $curso['id'] ?>" <?= (isset($curso_id) && $curso_id == $curso['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($curso['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="periodo">Período de Entrada:</label><br>
        <input type="text" id="periodo" name="periodo" placeholder="Ex: 2023.1" value="<?= htmlspecialchars($periodo ?? '') ?>"><br><br>

        <label for="imagem">Imagem de Perfil:</label><br>
        <input type="file" id="imagem" name="imagem" accept="image/*"><br><br>

        <button type="submit">Adicionar</button>
        <a href="index.php">Cancelar</a>
    </form>
</main>

<script src="/../../../public/recursos/js/painel_admin.js"></script>

</body>
</html>