-- Script para resetar e popular o banco de dados com novos dados de teste
USE db_daydreamming_project;

-- Drop das tabelas (ordem importante devido às foreign keys)
DROP TABLE IF EXISTS logs_acesso;
DROP TABLE IF EXISTS usuarios;

-- Recriar tabela de usuários
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acesso TIMESTAMP NULL,
    ativo BOOLEAN DEFAULT TRUE
);

-- Recriar tabela de logs de acesso
CREATE TABLE logs_acesso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    data_acesso TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip VARCHAR(45),
    sucesso BOOLEAN,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Inserir novos dados de teste (senha para todos: 123456)
INSERT INTO usuarios (nome, usuario, senha, email, ultimo_acesso, ativo) VALUES
('João Silva', 'joao.silva', '$2y$10$8tGmGPvk6wFv.z3yfHnAXO1tWlUPTl2K9VlYnJVRUAUiKYy7ekGgC', 'joao.silva@email.com', '2025-01-15 10:30:00', TRUE),
('Maria Santos', 'maria.santos', '$2y$10$8tGmGPvk6wFv.z3yfHnAXO1tWlUPTl2K9VlYnJVRUAUiKYy7ekGgC', 'maria.santos@email.com', '2025-01-14 16:45:00', TRUE),
('Pedro Costa', 'pedro.costa', '$2y$10$8tGmGPvk6wFv.z3yfHnAXO1tWlUPTl2K9VlYnJVRUAUiKYy7ekGgC', 'pedro.costa@email.com', '2025-01-13 09:15:00', TRUE),
('Ana Oliveira', 'ana.oliveira', '$2y$10$8tGmGPvk6wFv.z3yfHnAXO1tWlUPTl2K9VlYnJVRUAUiKYy7ekGgC', 'ana.oliveira@email.com', NULL, TRUE),
('Carlos Ferreira', 'carlos.ferreira', '$2y$10$8tGmGPvk6wFv.z3yfHnAXO1tWlUPTl2K9VlYnJVRUAUiKYy7ekGgC', 'carlos.ferreira@email.com', '2025-01-12 14:20:00', FALSE),
('Lucia Rodrigues', 'lucia.rodrigues', '$2y$10$8tGmGPvk6wFv.z3yfHnAXO1tWlUPTl2K9VlYnJVRUAUiKYy7ekGgC', 'lucia.rodrigues@email.com', '2025-01-16 11:00:00', TRUE),
('Roberto Lima', 'roberto.lima', '$2y$10$8tGmGPvk6wFv.z3yfHnAXO1tWlUPTl2K9VlYnJVRUAUiKYy7ekGgC', 'roberto.lima@email.com', '2025-01-15 08:30:00', TRUE),
('Fernanda Alves', 'fernanda.alves', '$2y$10$8tGmGPvk6wFv.z3yfHnAXO1tWlUPTl2K9VlYnJVRUAUiKYy7ekGgC', 'fernanda.alves@email.com', NULL, TRUE);

-- Inserir logs de acesso de exemplo
INSERT INTO logs_acesso (usuario_id, data_acesso, ip, sucesso) VALUES
(1, '2025-01-15 10:30:00', '192.168.1.100', TRUE),
(1, '2025-01-15 10:25:00', '192.168.1.100', FALSE),
(2, '2025-01-14 16:45:00', '192.168.1.101', TRUE),
(2, '2025-01-14 16:40:00', '192.168.1.101', FALSE),
(3, '2025-01-13 09:15:00', '192.168.1.102', TRUE),
(3, '2025-01-13 09:10:00', '192.168.1.102', TRUE),
(5, '2025-01-12 14:20:00', '192.168.1.104', TRUE),
(5, '2025-01-12 14:15:00', '192.168.1.104', FALSE),
(6, '2025-01-16 11:00:00', '192.168.1.105', TRUE),
(7, '2025-01-15 08:30:00', '192.168.1.106', TRUE),
(1, '2025-01-14 15:20:00', '192.168.1.100', TRUE),
(2, '2025-01-13 12:30:00', '192.168.1.101', TRUE);

-- Mostrar dados inseridos
SELECT 'Usuários criados:' as info;
SELECT id, nome, usuario, email, ultimo_acesso, ativo FROM usuarios;

SELECT 'Logs de acesso criados:' as info;
SELECT la.id, u.nome, u.usuario, la.data_acesso, la.ip, la.sucesso 
FROM logs_acesso la 
JOIN usuarios u ON la.usuario_id = u.id 
ORDER BY la.data_acesso DESC;