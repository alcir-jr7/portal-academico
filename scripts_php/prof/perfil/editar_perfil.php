<?php
// Incluir header que já inicia a sessão
require_once __DIR__ . '/../../../public/includes/header_professor.php';

// Verificação do usuário logado
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_tipo'])) {
    header('Location: ../../../public/php/login.php');
    exit;
}

$tipo_usuario = strtolower(trim($_SESSION['usuario_tipo']));

if ($tipo_usuario !== 'professor') {
    header('Location: ../../../public/php/login.php');
    exit;
}

$id = $_SESSION['usuario_id'];

// Verificar se PDO está disponível
if (!isset($pdo)) {
    $conexao_path = __DIR__ . '/../../../bancoDados/banco.php';
    if (file_exists($conexao_path)) {
        require_once $conexao_path;
    } else {
        die("Erro: Conexão com banco de dados não disponível.");
    }
}

// Busca dados atuais do professor
try {
    $stmt = $pdo->prepare("
        SELECT 
            u.nome, u.matricula, p.email, p.departamento,
            p.imagem_id, i.path AS imagem_path
        FROM professores p
        JOIN usuarios u ON p.id = u.id
        LEFT JOIN imagens i ON p.imagem_id = i.id
        WHERE p.id = ?
    ");
    $stmt->execute([$id]);
    $professor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$professor) {
        echo "<main><p>❌ Dados do professor não encontrados.</p>";
        echo '<p><a href="perfil.php">Voltar ao Perfil</a></p></main></body></html>';
        exit;
    }
} catch (PDOException $e) {
    error_log("Erro ao buscar dados do professor: " . $e->getMessage());
    echo "<main><p>❌ Erro ao carregar dados. Tente novamente.</p>";
    echo '<p><a href="perfil.php">Voltar ao Perfil</a></p></main></body></html>';
    exit;
}

$mensagem = '';
$erro = '';

// Processar upload da imagem e atualização dos dados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Departamento NÃO será editável, portanto não atualiza
    $departamento = $professor['departamento'];

    // Processar upload da imagem (se tiver arquivo)
    if (!empty($_FILES['imagem']['name'])) {
        $arquivo = $_FILES['imagem'];

        $tipos_permitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $tipo_arquivo = $arquivo['type'];

        if (!in_array($tipo_arquivo, $tipos_permitidos)) {
            $erro = "Tipo de arquivo não permitido. Use apenas JPG, PNG ou GIF.";
        } 
        else if ($arquivo['size'] > 5 * 1024 * 1024) {
            $erro = "Arquivo muito grande. Tamanho máximo: 5MB.";
        }
        else if ($arquivo['error'] !== UPLOAD_ERR_OK) {
            $erro = "Erro ao fazer upload do arquivo.";
        }
        else {
            $extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
            $novoNome = uniqid('perfil_' . $id . '_') . '.' . $extensao;
            $caminho_destino = __DIR__ . '/../../../public/recursos/storage/' . $novoNome;

            $diretorio = dirname($caminho_destino);
            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0755, true);
            }

            if (move_uploaded_file($arquivo['tmp_name'], $caminho_destino)) {
                try {
                    // Inserir nova imagem na tabela imagens
                    $stmt = $pdo->prepare("INSERT INTO imagens (path) VALUES (?)");
                    $stmt->execute([$novoNome]);
                    $nova_imagem_id = $pdo->lastInsertId();

                    // Atualizar o professor com a nova imagem
                    $stmt = $pdo->prepare("UPDATE professores SET imagem_id = ? WHERE id = ?");
                    $stmt->execute([$nova_imagem_id, $id]);

                    // Remover imagem antiga se existir e não for padrão
                    if (!empty($professor['imagem_path']) && $professor['imagem_path'] !== 'profile.jpg') {
                        $caminho_antigo = __DIR__ . '/../../../public/recursos/storage/' . $professor['imagem_path'];
                        if (file_exists($caminho_antigo)) {
                            unlink($caminho_antigo);
                        }
                    }

                    // Redirecionar para perfil.php após sucesso
                    header("Location: perfil.php");
                    exit;

                } catch (PDOException $e) {
                    error_log("Erro ao salvar imagem no banco: " . $e->getMessage());
                    if (file_exists($caminho_destino)) {
                        unlink($caminho_destino);
                    }
                    $erro = "Erro ao salvar imagem no banco de dados.";
                }
            } else {
                $erro = "Erro ao salvar o arquivo no servidor.";
            }
        }
    }
}

// Definir caminho da imagem atual
if (!empty($professor['imagem_path'])) {
    $imagemPath = '../../../public/recursos/storage/' . $professor['imagem_path'];
} else {
    $imagemPath = '../../../public/recursos/storage/profile.jpg';
}
?>

<main>
    <h1>Editar Perfil</h1>
    
    <?php if ($mensagem): ?>
        <div style="color: green;">
            <?= $mensagem ?>
        </div>
    <?php endif; ?>
    
    <?php if ($erro): ?>
        <div style="color: red;">
            ❌ <?= htmlspecialchars($erro) ?>
        </div>
    <?php endif; ?>

    <div>
        <h3>Foto Atual</h3>
        <img src="<?= htmlspecialchars($imagemPath) ?>" alt="Foto atual" width="150" height="150" style="border-radius: 50%; object-fit: cover; border: 2px solid #ddd;">
    </div>

    <form method="post" enctype="multipart/form-data">

        <fieldset>
            <legend><strong>Dados Pessoais</strong></legend>

            <label>Nome:</label><br>
            <input type="text" value="<?= htmlspecialchars($professor['nome']) ?>" disabled><br><br>

            <label>Matrícula:</label><br>
            <input type="text" value="<?= htmlspecialchars($professor['matricula']) ?>" disabled><br><br>

            <label>Email:</label><br>
            <input type="email" value="<?= htmlspecialchars($professor['email']) ?>" disabled><br><br>

            <label>Departamento:</label><br>
            <input type="text" value="<?= htmlspecialchars($professor['departamento'] ?? '') ?>" disabled><br><br>
        </fieldset>

        <fieldset>
            <legend><strong>Alterar Foto de Perfil</strong></legend>

            <label for="imagem">Escolher Nova Foto:</label><br>
            <input type="file" id="imagem" name="imagem" accept="image/*"><br><br>

            <p>
                <strong>Requisitos:</strong><br>
                • Tipos permitidos: JPG, PNG, GIF<br>
                • Tamanho máximo: 5MB<br>
                • Recomendado: Imagem quadrada para melhor visualização
            </p>
        </fieldset>

        <button type="submit">Salvar Alterações</button>
        <button type="button" onclick="window.location.href='perfil.php'">Voltar</button>
    </form>
</main>

<script src="../../../public/recursos/js/painel_professor.js"></script>

</body>
</html>
