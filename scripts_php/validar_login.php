<?php
session_start();

require_once(__DIR__ . '/../aplicacao/config/conexao.php');

// Pega dados do formulário
$matricula = $_POST['matricula'] ?? '';
$senha = $_POST['senha'] ?? '';
$tipo = $_POST['tipo'] ?? '';

// Validação básica dos campos
if (empty($matricula) || empty($senha) || empty($tipo)) {
    die('Preencha todos os campos.');
}

// Consulta o usuário pelo tipo e matrícula
$sql = "SELECT * FROM usuarios WHERE matricula = :matricula AND tipo = :tipo AND ativo = 1 LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':matricula', $matricula);
$stmt->bindValue(':tipo', $tipo);
$stmt->execute();

$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario) {
    // Verifica a senha usando password_verify (a senha deve estar hashada no banco)
    if (password_verify($senha, $usuario['senha'])) {
        // Login bem sucedido
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_tipo'] = $usuario['tipo'];

        // Redireciona para o painel respectivo
        switch ($usuario['tipo']) {
            case 'aluno':
                header('Location: ../public/recursos/php/painel_aluno.php');
                break;
            case 'professor':
                header('Location: ../public/recursos/php/painel_professor.php');
                break;
            case 'admin':
                header('Location: ../public/recursos/php/painel_admin.php');
                break;
            default:
                session_destroy();
                die('Tipo de usuário inválido.');
        }
        exit;
    } else {
        // Senha incorreta
        die('Senha incorreta. <a href="../public/recursos/php/login.php?tipo=' . htmlspecialchars($tipo) . '">Tente novamente</a>');
    }
} else {
    // Usuário não encontrado
    die('Usuário não encontrado ou inativo. <a href="../public/recursos/php/login.php?tipo=' . htmlspecialchars($tipo) . '">Tente novamente</a>');
}
?>
