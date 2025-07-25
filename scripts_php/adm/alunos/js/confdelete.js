

        // Confirmação para deletar
        document.querySelector('[data-action="delete"]').addEventListener('click', function(e) {
            e.stopPropagation();
            if (confirm('⚠️ ATENÇÃO: Tem certeza que deseja acessar a área de exclusão de alunos?\n\nEsta ação pode remover dados permanentemente!')) {
                window.location.href = 'js/deletar_aluno.js';
            }
        });

        // Animação de entrada dos cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.menu-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 150);
            });
        });

        // Efeito de clique nos cards
        document.querySelectorAll('.menu-card').forEach(card => {
            card.addEventListener('mousedown', function() {
                this.style.transform = 'translateY(-8px) scale(0.98)';
            });
            
            card.addEventListener('mouseup', function() {
                this.style.transform = 'translateY(-10px) scale(1)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
