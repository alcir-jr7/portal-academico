USE portal_academico;

-- Inserir usuários (alunos, professores, administrador)
INSERT INTO usuarios (nome, matricula, senha, tipo, ativo) VALUES
('João Silva', 'ALN1', '$2y$10$yzg7okswAFGkLSrVhpDcaebEWHvUkHDbvKaXXPCx4J9VsXF52zgvG', 'aluno', TRUE),       -- id = 1
('Maria Souza', 'ALN2', '$2y$10$yzg7okswAFGkLSrVhpDcaebEWHvUkHDbvKaXXPCx4J9VsXF52zgvG', 'aluno', TRUE),      -- id = 2
('Allan Lima', 'PROF1', '$2y$10$yzg7okswAFGkLSrVhpDcaebEWHvUkHDbvKaXXPCx4J9VsXF52zgvG', 'professor', TRUE), -- id = 3
('Emaur Florêncio', 'PROF2', '$2y$10$yzg7okswAFGkLSrVhpDcaebEWHvUkHDbvKaXXPCx4J9VsXF52zgvG', 'professor', TRUE), -- id = 4
('Hugo Dantas', 'PROF3', '$2y$10$yzg7okswAFGkLSrVhpDcaebEWHvUkHDbvKaXXPCx4J9VsXF52zgvG', 'professor', TRUE),    -- id = 5
('Nami', 'ADM1', '$2y$10$yzg7okswAFGkLSrVhpDcaebEWHvUkHDbvKaXXPCx4J9VsXF52zgvG', 'admin', TRUE);          -- id = 6

-- Inserir professores (id deve ser igual ao do usuário correspondente)
INSERT INTO professores (id, matricula, departamento, email) VALUES
(3, 'PROF1', 'Ciência da Computação', 'allan.lima@icampus.edu'),
(4, 'PROF2', 'Engenharia de Software', 'emaur.florencio@icampus.edu'),
(5, 'PROF3', 'Matemática Aplicada', 'hugo.dantas@icampus.edu');

-- Inserir cursos
INSERT INTO cursos (nome, codigo, turno, duracao_semestres, coordenador_id) VALUES
('Informática para Internet', 'INF1', 'matutino', 6, 3),
('Tecnologia em Sistemas', 'TEC1', 'noturno', 8, 4);

-- Inserir alunos (id = id do usuário correspondente)
INSERT INTO alunos (id, curso_id, periodo_entrada, email) VALUES
(1, 1, '2025.1', 'joao.silva@icampus.edu'),
(2, 2, '2025.1', 'maria.souza@icampus.edu');

-- Inserir administrador (id = id do usuário correspondente)
INSERT INTO administradores (id, setor) VALUES
(6, 'Secretaria Acadêmica');

-- Inserir disciplinas
INSERT INTO disciplinas (nome, codigo, carga_horaria, curso_id) VALUES
('Lógica de Programação', 'LP101', 60, 1),
('Projeto I', 'PJ101', 60, 2),
('Banco de Dados', 'BD101', 60, 1),
('Cálculo I', 'CAL101', 60, 2),
('Desenvolvimento Web I', 'DW101', 60, 1);

-- Inserir turmas
INSERT INTO turmas (disciplina_id, professor_id, semestre, horario) VALUES
(1, 3, '2025.1', 'Seg e Qua 08:00-10:00'), -- Allan - Lógica de Programação
(4, 5, '2025.1', 'Ter e Qui 19:00-21:00'), -- Hugo - Cálculo I
(2, 4, '2025.1', 'Qua e Sex 14:00-16:00'); -- Emaur - Projeto I

-- Inserir matrículas dos alunos nas turmas
INSERT INTO matriculas (aluno_id, turma_id, data_matricula, status) VALUES
(1, 1, CURRENT_DATE, 'ativa'), -- João na turma de Lógica
(1, 2, CURRENT_DATE, 'ativa'), -- João em Cálculo
(2, 1, CURRENT_DATE, 'ativa'), -- Maria em Lógica
(2, 3, CURRENT_DATE, 'ativa'); -- Maria em Projeto I

-- Inserir matrículas acadêmicas
INSERT INTO matriculas_academicas (matricula, tipo, usada) VALUES
('ALN1', 'aluno', TRUE),
('ALN2', 'aluno', TRUE),
('PROF1', 'professor', TRUE),
('PROF2', 'professor', TRUE),
('PROF3', 'professor', TRUE),
('ADM1', 'admin', TRUE);
