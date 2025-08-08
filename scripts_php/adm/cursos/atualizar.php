<?php
require_once __DIR__ . '/../../../public/includes/header_admin.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "ID do curso não informado.";
    exit;
}

// Busca dados atuais do curso
$stmt = $pdo->prepare("SELECT * FROM cursos WHERE id = ?");
$stmt->execute([$id]);
$curso = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$curso) {
    echo "Curso não encontrado.";
    exit;
}

// Se formulário enviado, atualiza os dados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $codigo = $_POST['codigo'] ?? '';
    $turno = $_POST['turno'] ?? '';
    $duracao = $_POST['duracao'] ?? 0;
    $coordenador_id = $_POST['coordenador_id'] ?: null;

    $stmt = $pdo->prepare("UPDATE cursos SET nome = ?, codigo = ?, turno = ?, duracao_semestres = ?, coordenador_id = ? WHERE id = ?");
    $stmt->execute([$nome, $codigo, $turno, $duracao, $coordenador_id, $id]);

    header("Location:index.php");
    exit;
}

// Pega lista de professores para select
$professores = $pdo->query("SELECT id, email FROM professores")->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <h1>Editar Curso</h1>
    <form method="post">
        <label>
            Nome:<br>
            <input type="text" name="nome" value="<?= htmlspecialchars($curso['nome']) ?>" required>
        </label>
        <br><br>

        <label>
            Código:<br>
            <input type="text" name="codigo" value="<?= htmlspecialchars($curso['codigo']) ?>" required>
        </label>
        <br><br>

        <label>
            Turno:<br>
            <select name="turno" required>
                <?php
                $turnos = ['matutino', 'vespertino', 'noturno', 'integral'];
                foreach ($turnos as $t) {
                    $selected = ($curso['turno'] === $t) ? 'selected' : '';
                    echo "<option value=\"$t\" $selected>" . ucfirst($t) . "</option>";
                }
                ?>
            </select>
        </label>
        <br><br>

        <label>
            Duração (semestres):<br>
            <input type="number" name="duracao" value="<?= $curso['duracao_semestres'] ?>" min="1" required>
        </label>
        <br><br>

        <label>
            Coordenador (opcional):<br>
            <select name="coordenador_id">
                <option value="">Nenhum</option>
                <?php foreach ($professores as $prof): 
                    $sel = ($prof['id'] == $curso['coordenador_id']) ? 'selected' : '';
                ?>
                    <option value="<?= $prof['id'] ?>" <?= $sel ?>>
                        <?= htmlspecialchars($prof['email']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <br><br>

        <button type="submit">Salvar</button>
        <a href="index.php">Cancelar</a>
    </form>

</main>
    <script src="/../../../public/recursos/js/painel_admin.js"></script>
</body>
</html>
