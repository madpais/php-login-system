-- Criação do banco de dados (se não existir)
CREATE DATABASE IF NOT EXISTS db_daydreamming_project;
USE db_daydreamming_project;

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acesso TIMESTAMP NULL,
    ultimo_logout TIMESTAMP NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    ativo BOOLEAN DEFAULT TRUE
);

-- Tabela de logs de acesso
CREATE TABLE IF NOT EXISTS logs_acesso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    data_acesso TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip VARCHAR(45),
    sucesso BOOLEAN,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabela de logs do sistema
CREATE TABLE IF NOT EXISTS logs_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    acao VARCHAR(50) NOT NULL,
    detalhes TEXT,
    data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip VARCHAR(45),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Inserir alguns usuários de exemplo (senha: 123456)
INSERT INTO usuarios (nome, usuario, senha, email, is_admin) VALUES
('Administrador', 'admin', '$2y$10$8tGmGPvk6wFv.z3yfHnAXO1tWlUPTl2K9VlYnJVRUAUiKYy7ekGgC', 'admin@exemplo.com', TRUE),
('Usuário Teste', 'teste', '$2y$10$8tGmGPvk6wFv.z3yfHnAXO1tWlUPTl2K9VlYnJVRUAUiKYy7ekGgC', 'teste@exemplo.com', FALSE);

-- Adicionar colunas se não existirem (para bancos existentes)
ALTER TABLE usuarios 
ADD COLUMN IF NOT EXISTS ultimo_logout TIMESTAMP NULL,
ADD COLUMN IF NOT EXISTS is_admin BOOLEAN DEFAULT FALSE;