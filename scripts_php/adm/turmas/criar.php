<?php
require_once __DIR__ . '/../../../public/includes/header_admin.php';

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
    $dia = $_POST['dia'] ?? '';
    $horario_time = $_POST['horario'] ?? '';

    if ($disciplina_id && $professor_id && $semestre) {
        // Combinar dia e horário se preenchidos
        $horario = '';
        if ($dia && $horario_time) {
            $horario = $dia . ' às ' . $horario_time;
        } elseif ($dia) {
            $horario = $dia;
        } elseif ($horario_time) {
            $horario = 'às ' . $horario_time;
        }

        $stmt = $pdo->prepare("INSERT INTO turmas (disciplina_id, professor_id, semestre, horario) VALUES (?, ?, ?, ?)");
        $stmt->execute([$disciplina_id, $professor_id, $semestre, $horario]);
        header('Location: index.php');
        exit;
    } else {
        $erro = "Preencha os campos disciplina, professor e semestre.";
    }
}
?>

<main class="form-create-main">
    <h1 class="form-create-title">Criar Nova Turma</h1>

    <?php if ($erro): ?>
        <p class="form-create-msg-erro"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="post" class="form-create-form">
        <label class="form-create-label">
            Disciplina:<br>
            <select name="disciplina_id" required class="form-create-select">
                <option value="">Selecione uma disciplina</option>
                <?php foreach ($disciplinas as $disciplina): ?>
                    <option value="<?= $disciplina['id'] ?>"><?= htmlspecialchars($disciplina['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <label class="form-create-label">
            Professor:<br>
            <select name="professor_id" required class="form-create-select">
                <option value="">Selecione um professor</option>
                <?php foreach ($professores as $prof): ?>
                    <option value="<?= $prof['id'] ?>"><?= htmlspecialchars($prof['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <label class="form-create-label">
            Semestre:<br>
            <input type="text" name="semestre" placeholder="Ex: 2024.1, Primeiro Semestre 2024" required class="form-create-select">
        </label><br><br>

        <label class="form-create-label">
            Dia:<br>
            <select name="dia" class="form-create-select">
                <option value="">Selecione um dia</option>
                <option value="Segunda-feira">Segunda-feira</option>
                <option value="Terça-feira">Terça-feira</option>
                <option value="Quarta-feira">Quarta-feira</option>
                <option value="Quinta-feira">Quinta-feira</option>
                <option value="Sexta-feira">Sexta-feira</option>
                <option value="Sábado">Sábado</option>
            </select>
        </label><br><br>

        <label class="form-create-label">
            Horário:<br>
            <input type="time" name="horario" class="form-create-input-text">
        </label><br><br>

        <button type="submit" class="form-create-btn-primary">Salvar</button>
        <a href="index.php" class="form-create-link-secondary">Cancelar</a>
    </form>
</main>

<script src="/../../../public/recursos/js/painel_admin.js"></script>

</body>
</html>