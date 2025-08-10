<?php
require_once __DIR__ . '/../includes/header_professor.php'; 
?>

<main>
    <h2>Menu do Professor</h2>
    <div class="painel-opcoes">
        <a href="/scripts_php/prof/notas/index.php" class="card-opcao">
            <img src="/public/recursos/images/boletim-p.png" alt="Notas">
            <span>Notas</span>
            <div class="description">Gerenciamento das notas dos Alunos</div>
        </a>
        <a href="/scripts_php/prof/frequencia/index.php" class="card-opcao">
            <img src="/public/recursos/images/frequencia-p.png" alt="Frequência">
            <span>Frequência</span>
            <div class="description">Gerenciamento das Frequência</div>
        </a>
        <a href="minhas_turmas.php" class="card-opcao">
            <img src="/public/recursos/images/turma-p.png" alt="Turmas">
            <span>Turmas</span>
            <div class="description">Gerenciamento das minhas turmas</div>
        </a>
        <a href="meu_horario.php" class="card-opcao">
            <img src="/public/recursos/images/horario-p.png" alt="Horário">
            <span>Horário</span>
            <div class="description">Gerenciamento dos meus Horários</div>
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
            <h2>Médias das Turmas</h2>
            <canvas id="statsChart" width="400" height="250"></canvas>
        </div>
    </div>
</main>

<script src="/public/recursos/js/painel_professor.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>
