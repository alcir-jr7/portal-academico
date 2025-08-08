<?php
require_once __DIR__ . '/../includes/header_aluno.php'; // ajuste o caminho conforme sua estrutura
?>

<main>
    <h2>Menu do Aluno</h2>
    <div class="painel-opcoes">
        <a href="/scripts_php/aluno/boletim.php" class="card-opcao">
            <img src="/public/recursos/images/boletim-p.png" alt="Boletim">
            <span>Boletim</span>
        </a>
        <a href="/scripts_php/aluno/frequencia.php" class="card-opcao">
            <img src="/public/recursos/images/frequencia-p.png" alt="Frequência">
            <span>Frequência</span>
        </a>
        <a href="horario.php" class="card-opcao">
            <img src="/public/recursos/images/horario-p.png" alt="Horário">
            <span>Horário</span>
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
            <h2>Gráficos de Notas</h2>
            <canvas id="statsChart" width="400" height="250"></canvas>
        </div>
    </div>
</main>

<script src="../recursos/js/painel_aluno.js"></script>
</body>
</html>
