function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            const main = document.querySelector("main");

            if (sidebar.style.width === "250px") {
                sidebar.style.width = "0";
                main.style.marginLeft = "0";
            } else {
                sidebar.style.width = "250px";
                main.style.marginLeft = "250px";
            }
}


//gráfico de estatísticas
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('statsChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Usuários', 'Alunos', 'Professores', 'Cursos'],
            datasets: [{
                label: 'Quantidade',
                data: [
                    statsData.usuarios,
                    statsData.alunos,
                    statsData.professores,
                    statsData.cursos
                ],
                backgroundColor: [
                    '#151616ff',
                    '#474e4cff',
                    '#070808ff',
                    '#464440ff'
                ],
                borderColor: '#ccc',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
});

// Função para montar o calendário
const nomesDosMeses = [
  "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
  "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
];

const eventos = {
  "2025-08-13": "Demo Week",
  "2025-08-14": "Demo Week",
  "2025-08-15": "Demo Week",
  "2025-09-07": "Feriado da Independência",
  "2025-11-15": "Proclamação da República",
  "2025-12-20": "Fim do semestre"
};

function montarCalendario(mes, ano) {
  const tbody = document.getElementById("diasCalendario");
  const mesAno = document.getElementById("mesAno");
  const totalEventos = document.getElementById("totalEventos");

  tbody.innerHTML = ""; // Limpa os dias anteriores

  const primeiroDia = new Date(ano, mes, 1).getDay();
  const diasDoMes = new Date(ano, mes + 1, 0).getDate();

  mesAno.textContent = `${nomesDosMeses[mes]} de ${ano}`;

  let linha = document.createElement("tr");
  let contador = 0;

  // Pega a data atual para destacar o dia de hoje
  const hoje = new Date();
  const hojeDia = hoje.getDate();
  const hojeMes = hoje.getMonth();
  const hojeAno = hoje.getFullYear();

  for (let i = 0; i < primeiroDia; i++) {
    linha.appendChild(document.createElement("td"));
  }

  for (let dia = 1; dia <= diasDoMes; dia++) {
    const dataStr = `${ano}-${(mes + 1).toString().padStart(2, '0')}-${dia.toString().padStart(2, '0')}`;
    const td = document.createElement("td");
    td.textContent = dia;

    if (eventos[dataStr]) {
      td.classList.add("evento");
      td.title = eventos[dataStr];
      contador++;
    }

    // Destaca o dia atual, mantendo evento se houver
    if (dia === hojeDia && mes === hojeMes && ano === hojeAno) {
      td.classList.add("hoje");
    }

    linha.appendChild(td);

    if (linha.children.length % 7 === 0) {
      tbody.appendChild(linha);
      linha = document.createElement("tr");
    }
  }

  if (linha.children.length > 0) {
    while (linha.children.length < 7) {
      linha.appendChild(document.createElement("td"));
    }
    tbody.appendChild(linha);
  }

  // Atualiza contador
  totalEventos.textContent = `Eventos no mês: ${contador}`;
}

document.addEventListener("DOMContentLoaded", () => {
  const hoje = new Date();
  montarCalendario(hoje.getMonth(), hoje.getFullYear());
});
