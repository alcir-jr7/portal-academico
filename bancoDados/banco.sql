
-- Criar banco
CREATE DATABASE IF NOT EXISTS portal_academico;
USE portal_academico;


-- Tabela de usuários
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    matricula VARCHAR(20) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('aluno', 'professor', 'admin') NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de professores
CREATE TABLE professores (
    id INT PRIMARY KEY,
    matricula VARCHAR(20) UNIQUE NOT NULL,
    departamento VARCHAR(100),
    email VARCHAR(100) NOT NULL UNIQUE,
    FOREIGN KEY (id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de cursos
CREATE TABLE cursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    codigo VARCHAR(20) UNIQUE NOT NULL,
    turno ENUM('matutino', 'vespertino', 'noturno', 'integral') NOT NULL,
    duracao_semestres INT NOT NULL,
    coordenador_id INT,
    FOREIGN KEY (coordenador_id) REFERENCES professores(id)
);

-- Tabela de alunos
CREATE TABLE alunos (
    id INT PRIMARY KEY,
    curso_id INT NOT NULL,
    periodo_entrada VARCHAR(20),
    email VARCHAR(100) NOT NULL UNIQUE,
    FOREIGN KEY (id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (curso_id) REFERENCES cursos(id)
);


-- Tabela de administradores
CREATE TABLE administradores (
    id INT PRIMARY KEY,
    setor VARCHAR(100),
    FOREIGN KEY (id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de disciplinas
CREATE TABLE disciplinas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    codigo VARCHAR(20) UNIQUE NOT NULL,
    carga_horaria INT NOT NULL,
    curso_id INT NOT NULL,
    FOREIGN KEY (curso_id) REFERENCES cursos(id)
);

-- Tabela de turmas
CREATE TABLE turmas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    disciplina_id INT NOT NULL,
    professor_id INT NOT NULL,
    semestre VARCHAR(10) NOT NULL,
    horario VARCHAR(100),
    FOREIGN KEY (disciplina_id) REFERENCES disciplinas(id),
    FOREIGN KEY (professor_id) REFERENCES professores(id)
);

-- Tabela de matrículas
CREATE TABLE matriculas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_id INT NOT NULL,
    turma_id INT NOT NULL,
    data_matricula DATE DEFAULT (CURRENT_DATE),
    status ENUM('ativa', 'trancada', 'dispensada', 'concluida') DEFAULT 'ativa',
    FOREIGN KEY (aluno_id) REFERENCES alunos(id),
    FOREIGN KEY (turma_id) REFERENCES turmas(id)
);

 -- Tabela de matrículas_academicas (aluno,professor,admin) 
CREATE TABLE matriculas_academicas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    matricula VARCHAR(20) UNIQUE NOT NULL,
    tipo ENUM('aluno', 'professor', 'admin') NOT NULL,
    usada BOOLEAN DEFAULT FALSE
);


-- Tabela de notas
CREATE TABLE notas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    matricula_id INT NOT NULL,
    nota1 DECIMAL(5,2),
    nota2 DECIMAL(5,2),
    media DECIMAL(5,2),
    observacao TEXT,
    FOREIGN KEY (matricula_id) REFERENCES matriculas(id) ON DELETE CASCADE
);

-- Tabela de frequência
CREATE TABLE frequencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    matricula_id INT NOT NULL,
    data DATE NOT NULL,
    presente BOOLEAN,
    FOREIGN KEY (matricula_id) REFERENCES matriculas(id) ON DELETE CASCADE
);

-- Tabela de calendário acadêmico
CREATE TABLE calendario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descricao TEXT,
    data_inicio DATE NOT NULL,
    data_fim DATE
);

-- Tabela de pedidos de dispensa de disciplina
CREATE TABLE dispensas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_id INT NOT NULL,
    disciplina_id INT NOT NULL,
    justificativa TEXT,
    status ENUM('pendente', 'aprovado', 'rejeitado') DEFAULT 'pendente',
    data_solicitacao DATE DEFAULT (CURRENT_DATE),
    FOREIGN KEY (aluno_id) REFERENCES alunos(id),
    FOREIGN KEY (disciplina_id) REFERENCES disciplinas(id)
);

-- Tabela de horários dos professores
CREATE TABLE horarios_professores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    professor_id INT NOT NULL,
    dia_semana ENUM('segunda','terca','quarta','quinta','sexta','sabado'),
    horario_inicio TIME,
    horario_fim TIME,
    turma_id INT NOT NULL,
    FOREIGN KEY (professor_id) REFERENCES professores(id),
    FOREIGN KEY (turma_id) REFERENCES turmas(id)
);

-- View de horários dos alunos
CREATE VIEW horarios_alunos AS
SELECT 
    m.aluno_id, 
    t.id AS turma_id, 
    d.nome AS disciplina,
    t.horario, 
    t.semestre
FROM matriculas m
JOIN turmas t ON m.turma_id = t.id
JOIN disciplinas d ON t.disciplina_id = d.id
WHERE m.status = 'ativa';

-- Índices para otimização de performance em JOINs
CREATE INDEX idx_matriculas_aluno_id ON matriculas(aluno_id);
CREATE INDEX idx_matriculas_turma_id ON matriculas(turma_id);
CREATE INDEX idx_notas_matricula_id ON notas(matricula_id);
CREATE INDEX idx_frequencias_matricula_id ON frequencias(matricula_id);
CREATE INDEX idx_disciplinas_curso_id ON disciplinas(curso_id);
CREATE INDEX idx_turmas_professor_id ON turmas(professor_id);
CREATE INDEX idx_turmas_disciplina_id ON turmas(disciplina_id);


