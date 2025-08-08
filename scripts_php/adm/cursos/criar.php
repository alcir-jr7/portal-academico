<?php
require_once __DIR__ . '/../../../public/includes/header_admin.php';

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

<main>
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
        <input type="text" name="nome" required value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"><br><br>

        <label>Código:</label><br>
        <input type="text" name="codigo" required value="<?= htmlspecialchars($_POST['codigo'] ?? '') ?>"><br><br>

        <label>Turno:</label><br>
        <select name="turno" required>
            <option value="">Selecione</option>
            <?php
            $turnos = ['matutino', 'vespertino', 'noturno', 'integral'];
            $selected_turno = $_POST['turno'] ?? '';
            foreach ($turnos as $t) {
                $sel = ($selected_turno === $t) ? 'selected' : '';
                echo "<option value=\"$t\" $sel>" . ucfirst($t) . "</option>";
            }
            ?>
        </select><br><br>

        <label>Duração (em semestres):</label><br>
        <input type="number" name="duracao_semestres" required value="<?= htmlspecialchars($_POST['duracao_semestres'] ?? '') ?>"><br><br>

        <label>Coordenador (opcional):</label><br>
        <select name="coordenador_id">
            <option value="">Nenhum</option>
            <?php 
            $selected_coord = $_POST['coordenador_id'] ?? '';
            foreach ($professores as $prof): 
                $sel = ($prof['id'] == $selected_coord) ? 'selected' : '';
            ?>
                <option value="<?= $prof['id'] ?>" <?= $sel ?>>
                    <?= htmlspecialchars($prof['email']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Salvar</button>
        <a href="index.php">Cancelar</a>
    </form>

    <script src="/../../../public/recursos/js/painel_admin.js"></script>
</main>

</body>
</html>
