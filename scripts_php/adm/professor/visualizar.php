<?php
require_once __DIR__ . '/../../../public/includes/header_admin.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "ID do professor não informado.";
    exit;
}

// Busca dados do professor e do usuário associado
$stmt = $pdo->prepare("
    SELECT p.*, u.nome, u.matricula, i.path AS imagem_path
    FROM professores p
    JOIN usuarios u ON p.id = u.id
    LEFT JOIN imagens i ON p.imagem_id = i.id
    WHERE p.id = ?
");
$stmt->execute([$id]);
$professor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$professor) {
    echo "Professor não encontrado.";
    exit;
}

// Definir caminho da imagem
if (!empty($professor['imagem_path'])) {
    $imagemPath = '/../../../public/recursos/storage/' . $professor['imagem_path'];
} else {
    $imagemPath = '/../../../public/recursos/storage/profile.jpg'; // Imagem padrão
}
?>

<main class="form-create-main">
    <h1 class="form-create-title">Detalhes do Professor</h1>

    <div class="form-create-img-container" style="margin-bottom: 20px;">
        <img src="<?= htmlspecialchars($imagemPath) ?>" alt="Imagem de Perfil" class="form-create-img">
    </div>

    <ul class="form-create-list">
        <li><strong class="form-create-label">Nome:</strong> <span class="form-create-value"><?= htmlspecialchars($professor['nome']) ?></span></li>
        <li><strong class="form-create-label">Matrícula:</strong> <span class="form-create-value"><?= htmlspecialchars($professor['matricula']) ?></span></li>
        <li><strong class="form-create-label">Departamento:</strong> <span class="form-create-value"><?= htmlspecialchars($professor['departamento']) ?></span></li>
        <li><strong class="form-create-label">Email:</strong> <span class="form-create-value"><?= htmlspecialchars($professor['email']) ?></span></li>
    </ul>

    <p class="form-create-actions">
        <a href="index.php" class="form-create-btn-secondary">Voltar à lista</a>
    </p>
</main>

<script src="/../../../public/recursos/js/painel_admin.js"></script>

</body>
</html>