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

-- Inserir alguns usuários de exemplo (senha: 123456)
INSERT INTO usuarios (nome, usuario, senha, email) VALUES
('Administrador', 'admin', '$2y$10$8tGmGPvk6wFv.z3yfHnAXO1tWlUPTl2K9VlYnJVRUAUiKYy7ekGgC', 'admin@exemplo.com'),
('Usuário Teste', 'teste', '$2y$10$8tGmGPvk6wFv.z3yfHnAXO1tWlUPTl2K9VlYnJVRUAUiKYy7ekGgC', 'teste@exemplo.com');