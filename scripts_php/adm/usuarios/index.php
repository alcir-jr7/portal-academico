<?php
session_start();
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

$tipo = $_GET['tipo'] ?? '';

// Consulta com ou sem filtro por tipo
$sql = "SELECT * FROM usuarios";
$params = [];

if (in_array($tipo, ['aluno', 'professor', 'admin'])) {
    $sql .= " WHERE tipo = ?";
    $params[] = $tipo;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Usuários</title>
</head>
<body>
    <h1>Lista de Usuários</h1>

    <form method="get">
        <label for="tipo">Filtrar por tipo:</label>
        <select name="tipo" onchange="this.form.submit()">
            <option value="">Todos</option>
            <option value="aluno" <?= $tipo === 'aluno' ? 'selected' : '' ?>>Aluno</option>
            <option value="professor" <?= $tipo === 'professor' ? 'selected' : '' ?>>Professor</option>
            <option value="admin" <?= $tipo === 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>
    </form>

    <br><br>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Matrícula</th>
                <th>Tipo</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr style="background-color: <?= $usuario['ativo'] ? '#e0ffe0' : '#ffe0e0' ?>;">
                    <td><?= htmlspecialchars($usuario['nome']) ?></td>
                    <td><?= htmlspecialchars($usuario['matricula']) ?></td>
                    <td><?= htmlspecialchars($usuario['tipo']) ?></td>
                    <td><?= $usuario['ativo'] ? 'Ativo' : 'Inativo' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="/public/php/painel_admin.php">Voltar</a>
</body>
</html>
