-- Estrutura do banco de dados para o fórum
USE db_daydreamming_project;

-- Adicionar campo de perfil de administrador na tabela usuarios
ALTER TABLE usuarios ADD COLUMN is_admin BOOLEAN DEFAULT FALSE;

-- Atualizar o usuário admin para ter privilégios de administrador
UPDATE usuarios SET is_admin = TRUE WHERE usuario = 'admin';

-- Tabela de categorias do fórum
CREATE TABLE IF NOT EXISTS forum_categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    cor VARCHAR(7) DEFAULT '#187bcd',
    icone VARCHAR(50) DEFAULT '💬',
    ativo BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de tópicos do fórum
CREATE TABLE IF NOT EXISTS forum_topicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    usuario_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    conteudo TEXT NOT NULL,
    visualizacoes INT DEFAULT 0,
    fixado BOOLEAN DEFAULT FALSE,
    fechado BOOLEAN DEFAULT FALSE,
    aprovado BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES forum_categorias(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabela de respostas do fórum
CREATE TABLE IF NOT EXISTS forum_respostas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    topico_id INT NOT NULL,
    usuario_id INT NOT NULL,
    conteudo TEXT NOT NULL,
    aprovado BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (topico_id) REFERENCES forum_topicos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabela de curtidas/likes
CREATE TABLE IF NOT EXISTS forum_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    topico_id INT NULL,
    resposta_id INT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (topico_id) REFERENCES forum_topicos(id) ON DELETE CASCADE,
    FOREIGN KEY (resposta_id) REFERENCES forum_respostas(id) ON DELETE CASCADE,
    UNIQUE KEY unique_like_topico (usuario_id, topico_id),
    UNIQUE KEY unique_like_resposta (usuario_id, resposta_id)
);

-- Tabela de moderação
CREATE TABLE IF NOT EXISTS forum_moderacao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    moderador_id INT NOT NULL,
    tipo_conteudo ENUM('topico', 'resposta') NOT NULL,
    conteudo_id INT NOT NULL,
    acao ENUM('aprovar', 'rejeitar', 'editar', 'deletar', 'fixar', 'fechar') NOT NULL,
    motivo TEXT,
    data_acao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (moderador_id) REFERENCES usuarios(id)
);

-- Inserir categorias padrão
INSERT INTO forum_categorias (nome, descricao, cor, icone) VALUES
('Geral', 'Discussões gerais sobre diversos tópicos', '#187bcd', '💬'),
('Educação', 'Tópicos relacionados a educação e aprendizado', '#f59e0b', '📚'),
('Suporte', 'Ajuda e suporte técnico', '#ef4444', '🆘'),
('Anúncios', 'Anúncios e novidades importantes', '#8b5cf6', '📢');

-- Inserir alguns tópicos de exemplo
INSERT INTO forum_topicos (categoria_id, usuario_id, titulo, conteudo) VALUES
(1, 1, 'Bem-vindos ao nosso fórum!', 'Este é o primeiro tópico do nosso fórum. Sintam-se à vontade para participar e compartilhar suas ideias!'),
(2, 1, 'Dicas de estudo eficiente', 'Compartilhem aqui suas melhores dicas para estudar de forma mais eficiente e produtiva.');

-- Inserir algumas respostas de exemplo
INSERT INTO forum_respostas (topico_id, usuario_id, conteudo) VALUES
(1, 2, 'Obrigado pela boas-vindas! Estou animado para participar das discussões.'),
(2, 2, 'Uma dica importante é fazer pausas regulares durante o estudo. A técnica Pomodoro funciona muito bem!');