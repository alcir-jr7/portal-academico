<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once(__DIR__ . '/../aplicacao/config/conexao.php');

// Pega dados do formulário
$matricula = $_POST['matricula'] ?? '';
$senha = $_POST['senha'] ?? '';
$tipo = $_POST['tipo'] ?? '';

// Validação básica dos campos
if (empty($matricula) || empty($senha) || empty($tipo)) {
    $_SESSION['login_error'] = 'Preencha todos os campos.';
    header('Location: ../public/php/login.php?tipo=' . htmlspecialchars($tipo));
    exit;
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
        // Login bem sucedido - limpa possíveis mensagens de erro
        unset($_SESSION['login_error']);
        
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_tipo'] = $usuario['tipo'];

        // Redireciona para o painel respectivo
        switch ($usuario['tipo']) {
            case 'aluno':
                header('Location: ../public/php/painel_aluno.php');
                break;
            case 'professor':
                header('Location: ../public/php/painel_professor.php');
                break;
            case 'admin':
                header('Location: ../public/php/painel_admin.php');
                break;
            default:
                session_destroy();
                $_SESSION['login_error'] = 'Tipo de usuário inválido.';
                header('Location: ../public/php/login.php?tipo=' . htmlspecialchars($tipo));
                exit;
        }
        exit;
    } else {
        // Senha incorreta
        $_SESSION['login_error'] = 'Senha incorreta. Verifique suas credenciais e tente novamente.';
        header('Location: ../public/php/login.php?tipo=' . htmlspecialchars($tipo));
        exit;
    }
} else {
    // Usuário não encontrado
    $_SESSION['login_error'] = 'Usuário não encontrado ou inativo. Verifique seus dados e tente novamente.';
    header('Location: ../public/php/login.php?tipo=' . htmlspecialchars($tipo));
    exit;
}
?>