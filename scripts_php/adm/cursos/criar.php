<?php
session_start();
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $codigo = $_POST['codigo'] ?? '';
    $turno = $_POST['turno'] ?? '';
    $duracao = $_POST['duracao_semestres'] ?? '';
    $coordenador_id = $_POST['coordenador_id'] ?? null;

    // Validação básica
    if (!$nome) $erros[] = "Nome é obrigatório.";
    if (!$codigo) $erros[] = "Código é obrigatório.";
    if (!$turno) $erros[] = "Turno é obrigatório.";
    if (!$duracao || !is_numeric($duracao)) $erros[] = "Duração deve ser um número.";

    if (empty($erros)) {
        $stmt = $pdo->prepare("INSERT INTO cursos (nome, codigo, turno, duracao_semestres, coordenador_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $codigo, $turno, $duracao, $coordenador_id ?: null]);

        header("Location: index.php");
        exit;
    }
}

// Pega professores para o select
$professores = $pdo->query("SELECT id, email FROM professores")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Curso</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Criar Novo Curso</h1>

    <?php if ($erros): ?>
        <ul style="color: red;">
            <?php foreach ($erros as $erro): ?>
                <li><?= htmlspecialchars($erro) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST">
        <label>Nome:</label><br>
        <input type="text" name="nome" required><br><br>

        <label>Código:</label><br>
        <input type="text" name="codigo" required><br><br>

        <label>Turno:</label><br>
        <select name="turno" required>
            <option value="">Selecione</option>
            <option value="matutino">Matutino</option>
            <option value="vespertino">Vespertino</option>
            <option value="noturno">Noturno</option>
            <option value="integral">Integral</option>
        </select><br><br>

        <label>Duração (em semestres):</label><br>
        <input type="number" name="duracao_semestres" required><br><br>

        <label>Coordenador (opcional):</label><br>
        <select name="coordenador_id">
            <option value="">Nenhum</option>
            <?php foreach ($professores as $prof): ?>
                <option value="<?= $prof['id'] ?>"><?= htmlspecialchars($prof['email']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Salvar</button>
        <a href="index.php">Cancelar</a>
    </form>
</body>
</html>
