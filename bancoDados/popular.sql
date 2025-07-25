-- Inserir curso
INSERT INTO cursos (nome, codigo, turno, duracao_semestres)
VALUES ('Sistemas para Internet', 'TSI2025', 'noturno', 6);

-- Inserir usuário aluno
INSERT INTO usuarios (nome, matricula, senha, tipo)
VALUES ('João da Silva', '20250001', '$2y$10$67umZXmuA8Lm9gliNgfsTuU2vA/5GIx5Da27Op6RTO8p4PsJK3SRi', 'aluno');

-- Inserir aluno (assumindo que o ID do usuário inserido acima foi 1)
INSERT INTO alunos (id, curso_id, periodo_entrada, email)
VALUES (1, 1, '2025.1', 'joao.silva@if.edu.br');

-- Inserir usuário professor
INSERT INTO usuarios (nome, matricula, senha, tipo)
VALUES ('Maria Oliveira', 'PROF001', '$2y$10$67umZXmuA8Lm9gliNgfsTuU2vA/5GIx5Da27Op6RTO8p4PsJK3SRi', 'professor');

-- Inserir professor (assumindo que o ID do usuário inserido acima foi 2)
INSERT INTO professores (id, matricula, departamento, email)
VALUES (2, 'PROF001', 'Departamento de Computação', 'maria.oliveira@if.edu.br');

-- Atualizar curso com o coordenador (professor de ID 2)
UPDATE cursos SET coordenador_id = 2 WHERE id = 1;

-- Inserir usuário administrador
INSERT INTO usuarios (nome, matricula, senha, tipo)
VALUES ('Carlos Souza', 'ADMIN001', '$2a$12$E0HX6gYYmXMdK0kWYcEyzuukOpvIoCwhQMWmanRrdpoZkCM6UH6PG', 'admin');

-- Inserir administrador (assumindo que o ID do usuário inserido acima foi 3)
INSERT INTO administradores (id, setor)
VALUES (3, 'Coordenação Acadêmica');
