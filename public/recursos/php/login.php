<?php
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
</head>
<body>
    <main class="login-container">
        <h2>Login - <?php echo $tipo_titulo; ?></h2>

        <form action="/scripts_php/validar_login.php" method="POST">
            <input type="hidden" name="tipo" value="<?php echo htmlspecialchars($tipo); ?>" />

            <label for="matricula">Matrícula:</label>
            <input type="text" id="matricula" name="matricula" required />

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required />

            <button type="submit">Entrar</button>
        </form>
    </main>
</body>
</html>
