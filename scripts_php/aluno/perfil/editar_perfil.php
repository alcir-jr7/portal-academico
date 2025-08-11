<?php
// Incluir header primeiro (que já inicia a sessão)
require_once __DIR__ . '/../../../public/includes/header_aluno.php';

// Verificação do usuário logado
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_tipo'])) {
    header('Location: ../../../public/php/login.php');
    exit;
}

$tipo_usuario = strtolower(trim($_SESSION['usuario_tipo']));

if ($tipo_usuario !== 'aluno') {
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

// Busca dados atuais do aluno
try {
    $stmt = $pdo->prepare("
        SELECT 
            u.nome, u.matricula, a.email, a.periodo_entrada, 
            c.nome AS curso_nome, a.imagem_id, i.path AS imagem_path
        FROM alunos a
        JOIN usuarios u ON a.id = u.id
        JOIN cursos c ON a.curso_id = c.id
        LEFT JOIN imagens i ON a.imagem_id = i.id
        WHERE a.id = ?
    ");
    $stmt->execute([$id]);
    $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$aluno) {
        echo "<main><p>❌ Dados do aluno não encontrados.</p>";
        echo '<p><a href="perfil.php">Voltar ao Perfil</a></p></main></body></html>';
        exit;
    }
} catch (PDOException $e) {
    error_log("Erro ao buscar dados do aluno: " . $e->getMessage());
    echo "<main><p>❌ Erro ao carregar dados. Tente novamente.</p>";
    echo '<p><a href="perfil.php">Voltar ao Perfil</a></p></main></body></html>';
    exit;
}

$mensagem = '';
$erro = '';

// Processar upload da imagem
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (!empty($_FILES['imagem']['name'])) {
        $arquivo = $_FILES['imagem'];
        
        // Validar tipo de arquivo
        $tipos_permitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $tipo_arquivo = $arquivo['type'];
        
        if (!in_array($tipo_arquivo, $tipos_permitidos)) {
            $erro = "Tipo de arquivo não permitido. Use apenas JPG, PNG ou GIF.";
        } 
        // Validar tamanho (máximo 5MB)
        else if ($arquivo['size'] > 5 * 1024 * 1024) {
            $erro = "Arquivo muito grande. Tamanho máximo: 5MB.";
        }
        // Verificar se houve erro no upload
        else if ($arquivo['error'] !== UPLOAD_ERR_OK) {
            $erro = "Erro ao fazer upload do arquivo.";
        }
        else {
            // Gerar nome único para o arquivo
            $extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
            $novoNome = uniqid('perfil_' . $id . '_') . '.' . $extensao;
            $caminho_destino = __DIR__ . '/../../../public/recursos/storage/' . $novoNome;
            
            // Criar diretório se não existir
            $diretorio = dirname($caminho_destino);
            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0755, true);
            }
            
            // Mover arquivo para pasta storage
            if (move_uploaded_file($arquivo['tmp_name'], $caminho_destino)) {
                try {
                    // Inserir nova imagem na tabela imagens
                    $stmt = $pdo->prepare("INSERT INTO imagens (path) VALUES (?)");
                    $stmt->execute([$novoNome]);
                    $nova_imagem_id = $pdo->lastInsertId();
                    
                    // Atualizar o aluno com a nova imagem
                    $stmt = $pdo->prepare("UPDATE alunos SET imagem_id = ? WHERE id = ?");
                    $stmt->execute([$nova_imagem_id, $id]);
                    
                    // Opcional: Remover imagem antiga (se existir e não for a padrão)
                    if (!empty($aluno['imagem_path']) && $aluno['imagem_path'] !== 'profile.jpg') {
                        $caminho_antigo = __DIR__ . '/../../../public/recursos/storage/' . $aluno['imagem_path'];
                        if (file_exists($caminho_antigo)) {
                            unlink($caminho_antigo);
                        }
                    }
                    
                    $mensagem = "✅ Foto de perfil atualizada com sucesso!";
                    header("Location: perfil.php?sucesso=foto_atualizada");
                    exit;
                    
                    // Recarregar dados atualizados
                    $stmt = $pdo->prepare("
                        SELECT i.path AS imagem_path
                        FROM alunos a
                        LEFT JOIN imagens i ON a.imagem_id = i.id
                        WHERE a.id = ?
                    ");
                    $stmt->execute([$id]);
                    $nova_imagem = $stmt->fetch(PDO::FETCH_ASSOC);
                    $aluno['imagem_path'] = $nova_imagem['imagem_path'];
                    
                } catch (PDOException $e) {
                    error_log("Erro ao salvar imagem no banco: " . $e->getMessage());
                    // Remover arquivo se deu erro no banco
                    if (file_exists($caminho_destino)) {
                        unlink($caminho_destino);
                    }
                    $erro = "Erro ao salvar imagem no banco de dados.";
                }
            } else {
                $erro = "Erro ao salvar o arquivo no servidor.";
            }
        }
    } else {
        $erro = "Nenhuma imagem foi selecionada.";
    }
}

// Definir caminho da imagem atual
if (!empty($aluno['imagem_path'])) {
    $imagemPath = '../../../public/recursos/storage/' . $aluno['imagem_path'];
} else {
    $imagemPath = '../../../public/recursos/storage/profile.jpg';
}
?>

<main class="editar-perfil">

    <?php if ($mensagem): ?>
        <div class="mensagem-sucesso">
            <?= htmlspecialchars($mensagem) ?>
        </div>
    <?php endif; ?>

    <?php if ($erro): ?>
        <div class="mensagem-erro">
            ❌ <?= htmlspecialchars($erro) ?>
        </div>
    <?php endif; ?>
    <div class="container-perfil">
    <!-- Foto à esquerda -->
    <div class="foto-atual">
        <img src="<?= htmlspecialchars($imagemPath) ?>" alt="Minha foto atual" width="150" height="150"> 
        <h3>Minha Foto Atual</h3>
    </div>

    <!-- Formulário à direita -->
    <form method="post" enctype="multipart/form-data" class="form-perfil">
        <fieldset class="dados-pessoais">
            <legend>Dados Pessoais</legend>
    
            <div class="campo">
                <label>Nome:</label>
                <input type="text" value="<?= htmlspecialchars($aluno['nome']) ?>" disabled>
            </div>
    
            <div class="campo">
                <label>Matrícula:</label>
                <input type="text" value="<?= htmlspecialchars($aluno['matricula']) ?>" disabled>
            </div>
    
            <div class="campo">
                <label>Email:</label>
                <input type="email" value="<?= htmlspecialchars($aluno['email']) ?>" disabled>
            </div>
    
            <div class="campo">
                <label>Curso:</label>
                <input type="text" value="<?= htmlspecialchars($aluno['curso_nome']) ?>" disabled>
            </div>
        </fieldset>

        <fieldset class="alterar-foto">
            <legend>Alterar Foto de Perfil</legend>
            
            <label for="imagem">Escolher Nova Foto:</label>
            <input type="file" id="imagem" name="imagem" accept="image/*" required>
            
            <div class="requisitos">
                <strong>Requisitos:</strong>
                <ul>
                    <li>Tipos permitidos: JPG, PNG, GIF</li>
                    <li>Tamanho máximo: 5MB</li>
                    <li>Recomendado: Imagem quadrada para melhor visualização</li>
                </ul>
            </div>
            
            <div class="botoes">
                <button type="submit">Atualizar Foto</button>
                <button type="button" onclick="window.location.href='perfil.php'">Voltar</button>
            </div>
        </fieldset>
    </form>
</div>

</main>


<script src="../../../public/recursos/js/painel_aluno.js"></script>
<script src="perfil.js"></script>

</body>
</html>
