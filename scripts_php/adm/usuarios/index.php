<?php
require_once __DIR__ . '/../../../public/includes/header_admin.php';
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

<main class="page-main">
    <h1 class="page-title">Lista de Usuários</h1>

    <form method="get" class="form-search">
        <label for="tipo">Filtrar por tipo:</label>
        <select name="tipo" onchange="this.form.submit()" class="input-search" style="max-width: 200px;">
            <option value="">Todos</option>
            <option value="aluno" <?= $tipo === 'aluno' ? 'selected' : '' ?>>Aluno</option>
            <option value="professor" <?= $tipo === 'professor' ? 'selected' : '' ?>>Professor</option>
            <option value="admin" <?= $tipo === 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>
    </form>

    <br><br>

    <table border="1" cellpadding="8" class="table-admin">
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
                <tr class="table-row <?= $usuario['ativo'] ? 'available' : 'used' ?>">
                    <td><?= htmlspecialchars($usuario['nome']) ?></td>
                    <td><?= htmlspecialchars($usuario['matricula']) ?></td>
                    <td><?= htmlspecialchars($usuario['tipo']) ?></td>
                    <td class="status <?= $usuario['ativo'] ? 'status-active' : 'status-inactive' ?>">
                        <?= $usuario['ativo'] ? 'Ativo' : 'Inativo' ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <a href="/public/php/painel_admin.php" class="btn-secondary">Voltar</a>
</main>

<script src="/../../../public/recursos/js/painel_admin.js"></script>


</body>
</html>
