<?php
require_once __DIR__ . '/../../../public/includes/header_admin.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM matriculas_academicas WHERE id = ?");
$stmt->execute([$id]);
$matricula = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$matricula) {
    echo "Matrícula não encontrada.";
    exit;
}
?>

<main>
    <h1>Detalhes da Matrícula Acadêmica</h1>

    <p><strong>ID:</strong> <?= $matricula['id'] ?></p>
    <p><strong>Matrícula:</strong> <?= htmlspecialchars($matricula['matricula']) ?></p>
    <p><strong>Tipo:</strong> <?= ucfirst($matricula['tipo']) ?></p>
    <p><strong>Status:</strong> <?= $matricula['usada'] ? 'Usada' : 'Disponível' ?></p>

    <p>
        <a href="editar.php?id=<?= $matricula['id'] ?>">Editar</a> | 
        <a href="deletar.php?id=<?= $matricula['id'] ?>">Excluir</a> | 
        <a href="index.php">Voltar</a>
    </p>

    <script src="/../../../public/recursos/js/painel_admin.js"></script>
</main>

</body>
</html>
