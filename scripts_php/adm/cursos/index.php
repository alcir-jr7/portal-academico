<?php
session_start();

// Incluir conexão com o caminho correto
require_once __DIR__ . '/../../../aplicacao/config/conexao.php';

// Seleciona todos os cursos com nome do coordenador
$stmt = $pdo->query("
    SELECT cursos.*, professores.email AS coordenador_email
    FROM cursos
    LEFT JOIN professores ON cursos.coordenador_id = professores.id
");
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gerenciamento de Cursos - Painel Administrativo</title>
    <link rel="stylesheet" href="../../../public/css/admin-style.css" />
</head>
<body>
    <header>
        <div class="header-content">
            <h1>Gerenciamento de Cursos</h1>
            <div class="admin-info">
                <span>Painel Administrativo</span>
            </div>
        </div>
        <nav class="admin-nav">
            <ul>
                <li><a href="criar.php" class="btn-create">➕ Novo Curso</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="content-header">
            <h2>Cursos Cadastrados</h2>
            <a href="criar.php" class="btn btn-primary">➕ Adicionar Novo Curso</a>
        </div>
        
        <?php if (empty($cursos)): ?>
            <div class="empty-state">
                <h3>Nenhum curso cadastrado</h3>
                <p>Comece adicionando o primeiro curso ao sistema.</p>
                <a href="criar.php" class="btn btn-primary">Cadastrar Primeiro Curso</a>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome do Curso</th>
                            <th>Código</th>
                            <th>Turno</th>
                            <th>Duração</th>
                            <th>Coordenador</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cursos as $curso): ?>
                            <tr>
                                <td><?= $curso['id'] ?></td>
                                <td><strong><?= htmlspecialchars($curso['nome']) ?></strong></td>
                                <td><code><?= htmlspecialchars($curso['codigo']) ?></code></td>
                                <td><?= htmlspecialchars($curso['turno']) ?></td>
                                <td><?= $curso['duracao_semestres'] ?> semestres</td>
                                <td><?= htmlspecialchars($curso['coordenador_email'] ?? 'Não definido') ?></td>
                                <td class="actions">
                                    <a href="visualizar.php?id=<?= $curso['id'] ?>" class="btn btn-info" title="Visualizar">👁️</a>
                                    <a href="atualizar.php?id=<?= $curso['id'] ?>" class="btn btn-warning" title="Editar">✏️</a>
                                    <a href="deletar.php?id=<?= $curso['id'] ?>" 
                                       class="btn btn-danger" 
                                       title="Excluir"
                                       onclick="return confirm('⚠️ Tem certeza que deseja excluir o curso \'<?= htmlspecialchars($curso['nome']) ?>\'?\n\nEsta ação não pode ser desfeita!');">🗑️</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="table-info">
                <p>Total de cursos cadastrados: <strong><?= count($cursos) ?></strong></p>
            </div>
            <a href="/public/php/painel_admin.php">Voltar</a>
        <?php endif; ?>
    </main>
</body>
</html>