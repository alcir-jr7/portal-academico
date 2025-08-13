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

<main class="form-create-main">
    <h1 class="form-create-title">Detalhes do Administrador</h1>

    <div class="form-create-container">
        <p class="form-create-info-item"><strong class="form-create-label">ID:</strong> <span class="form-create-value"><?= $admin['id'] ?></span></p>
        <p class="form-create-info-item"><strong class="form-create-label">Nome:</strong> <span class="form-create-value"><?= htmlspecialchars($admin['nome']) ?></span></p>
        <p class="form-create-info-item"><strong class="form-create-label">Matrícula:</strong> <span class="form-create-value"><?= htmlspecialchars($admin['matricula']) ?></span></p>
        <p class="form-create-info-item"><strong class="form-create-label">Tipo:</strong> <span class="form-create-value"><?= ucfirst($admin['tipo']) ?></span></p>
        <p class="form-create-info-item"><strong class="form-create-label">Setor:</strong> <span class="form-create-value"><?= htmlspecialchars($admin['setor']) ?></span></p>
    </div>

    <p class="form-create-actions">
        <a href="index.php" class="form-create-btn-secondary">Voltar</a>
    </p>

    <script src="/../../../public/recursos/js/painel_admin.js"></script>
</main>

</body>
</html>
