<?php
session_start();

header('Content-Type: application/json');

// Verifica se o professor está logado
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'professor') {
    echo json_encode(['erro' => 'Acesso não autorizado']);
    exit;
}

$professorId = $_SESSION['usuario_id']; // O mesmo que está na tabela `usuarios` e também é FK na tabela `professores`

try {
    $pdo = new PDO('mysql:host=localhost;dbname=portal_academico', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para obter nome da disciplina (via turma) e média dos alunos
    $sql = "
        SELECT d.nome AS turma_nome, ROUND(AVG(n.media), 2) AS media_geral
        FROM turmas t
        JOIN disciplinas d ON t.disciplina_id = d.id
        JOIN matriculas m ON m.turma_id = t.id
        JOIN notas n ON n.matricula_id = m.id
        WHERE t.professor_id = :professor_id
        GROUP BY d.nome
        ORDER BY d.nome
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':professor_id', $professorId, PDO::PARAM_INT);
    $stmt->execute();

    $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($dados);

} catch (PDOException $e) {
    echo json_encode(['erro' => 'Erro ao conectar ou consultar o banco de dados.']);
}
