<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclui o header_admin.php que já faz toda a verificação de sessão,
// busca do nome e estatísticas, além do sidebar e header.
require_once __DIR__ . '/../includes/header_admin.php';
?>

<main>
    <h2>Gerenciamento do Sistema</h2>
    <div class="painel-opcoes">
        <!-- Gerenciamento de Usuários -->
        <a href="/scripts_php/adm/usuarios/index.php" class="card-opcao">
            <img src="/public/recursos/images/user-p.png" alt="Usuários">
            <span>Usuários</span>
            <div class="description">Gerenciar contas de usuários do sistema</div>
        </a>

        <!-- Gerenciamento de Disciplinas -->
        <a href="/scripts_php/adm/disciplinas/index.php" class="card-opcao">
            <img src="/public/recursos/images/disciplina-p.png" alt="Disciplinas">  
            <span>Disciplinas</span>
            <div class="description">Cadastro e gestão de disciplinas</div>
        </a>

        <!-- Gerenciamento de Cursos -->
        <a href="/scripts_php/adm/cursos/index.php" class="card-opcao">
            <img src="/public/recursos/images/curso-p.png" alt="Cursos">
            <span>Cursos</span>
            <div class="description">Cadastro e gestão de cursos</div>
        </a>

        <!-- Gerenciamento de Matrículas -->
        <a href="/scripts_php/adm/matriculas/index.php" class="card-opcao">
            <img src="/public/recursos/images/matricular-p.png" alt="Matrículas">
            <span>Matrículas</span>
            <div class="description">Controle de matrículas em turmas</div>
        </a>
    </div>

    <div class="dashboard-linha">
        <div class="calendario-widget">
            <table id="calendario">
                <thead>
                    <tr>
                        <th colspan="7" id="mesAno"></th>   
                    </tr>
                    <tr>
                        <th>Dom</th><th>Seg</th><th>Ter</th><th>Qua</th><th>Qui</th><th>Sex</th><th>Sáb</th>
                    </tr>
                </thead>
                <tbody id="diasCalendario"></tbody>
            </table>
        </div>

        <div class="grafico-container">
            <h2>Estatísticas Rápidas</h2>
            <canvas id="statsChart" width="400" height="250"></canvas>
        </div>
    </div>
</main>

<script src="../recursos/js/painel_admin.js"></script>
<!-- Biblioteca Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Passa os dados PHP para o JavaScript -->
<script>
    const statsData = {
        usuarios: <?php echo $stats['usuarios']; ?>,
        alunos: <?php echo $stats['alunos']; ?>,
        professores: <?php echo $stats['professores']; ?>,
        cursos: <?php echo $stats['cursos']; ?>
    };
</script>

</body>
</html>
