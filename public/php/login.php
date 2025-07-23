<?php
session_start();

$tipo = $_GET['tipo'] ?? '';
$tipos_validos = ['aluno', 'professor', 'admin'];

if (!in_array($tipo, $tipos_validos)) {
    http_response_code(400);
    exit('Tipo de usuário inválido.');
}

$tipo_titulo = ucfirst($tipo);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Login do Sistema Acadêmico iCampus" />
    <link rel="icon" href="/public/recursos/images/icon.png" type="image/x-icon">
    <link rel="stylesheet" href="/public/recursos/css/login.css">
    <title>Login - <?php echo $tipo_titulo; ?></title>
    <style>
        .error-message {
            background-color: #fee2e2;
            border: 1px solid #fca5a5;
            color: #dc2626;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
            text-align: center;
        }
        
        .success-message {
            background-color: #dcfce7;
            border: 1px solid #86efac;
            color: #16a34a;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
            text-align: center;
        }
    </style>
</head>
<body>
    <main class="login-container">
        <h2>Login - <?php echo $tipo_titulo; ?></h2>

        <?php
        // Exibe mensagem de erro se existir
        if (isset($_SESSION['login_error'])) {
            echo '<div class="error-message">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
            unset($_SESSION['login_error']); // Remove a mensagem após exibir
        }
        
        // Exibe mensagem de sucesso se existir (para casos como logout bem-sucedido)
        if (isset($_SESSION['login_success'])) {
            echo '<div class="success-message">' . htmlspecialchars($_SESSION['login_success']) . '</div>';
            unset($_SESSION['login_success']);
        }
        ?>

        <form action="/scripts_php/validar_login.php" method="POST">
            <input type="hidden" name="tipo" value="<?php echo htmlspecialchars($tipo); ?>" />

            <label for="matricula">Matrícula:</label>
            <input type="text" id="matricula" name="matricula" 
                   value="<?php echo htmlspecialchars($_POST['matricula'] ?? ''); ?>" 
                   required />

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required />

            <button type="submit">Entrar</button>
        </form>
    </main>
</body>
</html>