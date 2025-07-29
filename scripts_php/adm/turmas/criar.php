<?php
session_start();
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

// Pega disciplinas e professores para os selects
$disciplinas = $pdo->query("SELECT id, nome FROM disciplinas ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$professores = $pdo->query("
    SELECT p.id, u.nome 
    FROM professores p 
    JOIN usuarios u ON p.id = u.id 
    ORDER BY u.nome
")->fetchAll(PDO::FETCH_ASSOC);

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $disciplina_id = $_POST['disciplina_id'] ?? null;
    $professor_id = $_POST['professor_id'] ?? null;
    $semestre = $_POST['semestre'] ?? '';
    $horario = $_POST['horario'] ?? '';

    if ($disciplina_id && $professor_id && $semestre) {
        $stmt = $pdo->prepare("INSERT INTO turmas (disciplina_id, professor_id, semestre, horario) VALUES (?, ?, ?, ?)");
        $stmt->execute([$disciplina_id, $professor_id, $semestre, $horario]);
        header('Location: index.php');
        exit;
    } else {
        $erro = "Preencha os campos disciplina, professor e semestre.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Criar Turma</title>
</head>
<body>
    <h1>Criar Nova Turma</h1>

    <?php if ($erro): ?>
        <p style="color:red;"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="post">
        <label>
            Disciplina:<br>
            <select name="disciplina_id" required>
                <option value="">Selecione uma disciplina</option>
                <?php foreach ($disciplinas as $disciplina): ?>
                    <option value="<?= $disciplina['id'] ?>"><?= htmlspecialchars($disciplina['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <label>
            Professor:<br>
            <select name="professor_id" required>
                <option value="">Selecione um professor</option>
                <?php foreach ($professores as $prof): ?>
                    <option value="<?= $prof['id'] ?>"><?= htmlspecialchars($prof['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <label>
            Semestre:<br>
            <input type="text" name="semestre" placeholder="Ex: 2023.1" required>
        </label><br><br>

        <label>
            Horário:<br>
            <input type="text" name="horario" placeholder="Ex: Seg 8h-10h">
        </label><br><br>

        <button type="submit">Salvar</button>
        <a href="index.php">Cancelar</a>
    </form>
</body>
</html>
