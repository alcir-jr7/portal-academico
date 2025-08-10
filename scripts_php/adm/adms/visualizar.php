<?php
require_once __DIR__ . '/../../../public/includes/header_admin.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

// Buscar dados do admin (usuario + administrador)
$stmt = $pdo->prepare("
    SELECT u.id, u.nome, u.matricula, u.tipo, a.setor
    FROM usuarios u
    JOIN administradores a ON a.id = u.id
    WHERE u.id = ? AND u.tipo = 'admin'
");
$stmt->execute([$id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    echo "Administrador não encontrado.";
    exit;
}
?>

<main>
    <h1>Detalhes do Administrador</h1>

    <p><strong>ID:</strong> <?= $admin['id'] ?></p>
    <p><strong>Nome:</strong> <?= htmlspecialchars($admin['nome']) ?></p>
    <p><strong>Matrícula:</strong> <?= htmlspecialchars($admin['matricula']) ?></p>
    <p><strong>Tipo:</strong> <?= ucfirst($admin['tipo']) ?></p>
    <p><strong>Setor:</strong> <?= htmlspecialchars($admin['setor']) ?></p>

    <p>
        <a href="editar.php?id=<?= $admin['id'] ?>">Editar</a> | 
        <a href="deletar.php?id=<?= $admin['id'] ?>" onclick="return confirm('Deseja excluir este administrador?')">Excluir</a> | 
        <a href="index.php">Voltar</a>
    </p>

    <script src="/../../../public/recursos/js/painel_admin.js"></script>
</main>

</body>
</html>
