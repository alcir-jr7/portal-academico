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

<main>
    <h1>Detalhes do Professor</h1>
    <div style="margin-bottom: 20px;">
        <img src="<?= htmlspecialchars($imagemPath) ?>" alt="Imagem de Perfil" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd;">
    </div>
    <ul>
        <li><strong>Nome:</strong> <?= htmlspecialchars($professor['nome']) ?></li>
        <li><strong>Matrícula:</strong> <?= htmlspecialchars($professor['matricula']) ?></li>
        <li><strong>Departamento:</strong> <?= htmlspecialchars($professor['departamento']) ?></li>
        <li><strong>Email:</strong> <?= htmlspecialchars($professor['email']) ?></li>
    </ul>
    <a href="index.php" class="btn btn-secondary">Voltar à lista</a>
</main>

<script src="/../../../public/recursos/js/painel_admin.js"></script>

</body>
</html>