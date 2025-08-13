<?php
require_once __DIR__ . '/../../../public/includes/header_admin.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $curso_id = $_POST['curso_id'] ?? null;
    $periodo = trim($_POST['periodo'] ?? '');

    if (!$nome || !$senha || !$email || !$curso_id) {
        $erro = "Preencha todos os campos obrigatórios.";
    } else {
        // Gerar matrícula automática no padrão ALN<number>
        try {
            // Buscar a maior matrícula ALN já cadastrada
            $stmt = $pdo->query("SELECT matricula FROM matriculas_academicas WHERE tipo = 'aluno' AND matricula LIKE 'ALN%' ORDER BY id DESC LIMIT 1");
            $ultimaMatricula = $stmt->fetchColumn();

            if ($ultimaMatricula) {
                // Extrair o número da matrícula (remover 'ALN' e converter para inteiro)
                $numero = (int)substr($ultimaMatricula, 3);
                $novoNumero = $numero + 1;
            } else {
                // Se não existir nenhuma matrícula ALN ainda, começar pelo 1
                $novoNumero = 1;
            }

            $novaMatricula = 'ALN' . $novoNumero;

            // Inserir nova matrícula acadêmica
            $stmt = $pdo->prepare("INSERT INTO matriculas_academicas (matricula, tipo, usada) VALUES (?, 'aluno', FALSE)");
            $stmt->execute([$novaMatricula]);
            $matricula_id = $pdo->lastInsertId();

            // Verificar se foi enviada uma imagem
            if (!empty($_FILES['imagem']['name'])) {
                $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
                $novoNome = uniqid() . '.' . $extensao;
                $caminho = __DIR__ . '/../../../public/recursos/storage/' . $novoNome;

                if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho)) {
                    $stmtImg = $pdo->prepare("INSERT INTO imagens (path) VALUES (?)");
                    $stmtImg->execute([$novoNome]);
                    $imagem_id = $pdo->lastInsertId();
                } else {
                    $imagem_id = null;
                }
            } else {
                $imagem_id = null;
            }

            $pdo->beginTransaction();

            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            // Inserir usuário com a matrícula nova gerada
            $stmt1 = $pdo->prepare("INSERT INTO usuarios (nome, matricula, senha, tipo) VALUES (?, ?, ?, 'aluno')");
            $stmt1->execute([$nome, $novaMatricula, $senhaHash]);
            $usuario_id = $pdo->lastInsertId();

            $stmt2 = $pdo->prepare("INSERT INTO alunos (id, curso_id, periodo_entrada, email, imagem_id) VALUES (?, ?, ?, ?, ?)");
            $stmt2->execute([$usuario_id, $curso_id, $periodo, $email, $imagem_id]);

            // Atualizar a matrícula como usada
            $stmt3 = $pdo->prepare("UPDATE matriculas_academicas SET usada = TRUE WHERE id = ?");
            $stmt3->execute([$matricula_id]);

            $pdo->commit();

            $sucesso = "Aluno cadastrado com sucesso! Matrícula: $novaMatricula";
            $nome = $email = $periodo = '';
            $curso_id = '';
            $senha = '';

        } catch (Exception $e) {
            $pdo->rollBack();
            $erro = "Erro ao cadastrar aluno: " . $e->getMessage();
        }
    }
}

$cursos = $pdo->query("SELECT id, nome FROM cursos ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
?>
<main class="form-create-container">
    <h1 class="form-create-title">Criar Novo Aluno</h1>

    <?php if ($erro): ?>
        <p class="form-create-error"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>
    <?php if ($sucesso): ?>
        <p class="form-create-success"><?= htmlspecialchars($sucesso) ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="form-create-form">
        <label for="nome" class="form-create-label">Nome:</label><br>
        <input type="text" id="nome" name="nome" required class="form-create-input" value="<?= htmlspecialchars($nome ?? '') ?>"><br><br>

        <label for="senha" class="form-create-label">Senha:</label><br>
        <input type="password" id="senha" name="senha" required class="form-create-input"><br><br>

        <label for="email" class="form-create-label">Email:</label><br>
        <input type="email" id="email" name="email" required class="form-create-input" value="<?= htmlspecialchars($email ?? '') ?>"><br><br>

        <label for="curso_id" class="form-create-label">Curso:</label><br>
        <select id="curso_id" name="curso_id" required class="form-create-select">
            <option value="">Selecione um curso</option>
            <?php foreach ($cursos as $curso): ?>
                <option value="<?= $curso['id'] ?>" <?= (isset($curso_id) && $curso_id == $curso['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($curso['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="periodo" class="form-create-label">Período de Entrada:</label><br>
        <input type="text" id="periodo" name="periodo" placeholder="Ex: 2023.1" class="form-create-input" value="<?= htmlspecialchars($periodo ?? '') ?>"><br><br>

        <label for="imagem" class="form-create-label">Imagem de Perfil:</label><br>
        <input type="file" id="imagem" name="imagem" accept="image/*" class="form-create-file"><br><br>

        <button type="submit" class="form-create-btn-primary">Adicionar</button>
        <a href="index.php" class="form-create-link-secondary">Cancelar</a>
    </form>
</main>

<script src="/../../../public/recursos/js/painel_admin.js"></script>


</body>
</html>
