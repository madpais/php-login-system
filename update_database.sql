-- Script para atualizar banco de dados existente
USE db_daydreamming_project;

-- Adicionar colunas na tabela usuarios se não existirem
ALTER TABLE usuarios 
ADD COLUMN IF NOT EXISTS ultimo_logout TIMESTAMP NULL,
ADD COLUMN IF NOT EXISTS is_admin BOOLEAN DEFAULT FALSE;

-- Criar tabela de logs do sistema se não existir
CREATE TABLE IF NOT EXISTS logs_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    acao VARCHAR(50) NOT NULL,
    detalhes TEXT,
    data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip VARCHAR(45),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Atualizar usuário admin para ter privilégios de administrador
UPDATE usuarios SET is_admin = TRUE WHERE usuario = 'admin';

-- Verificar estrutura atualizada
DESCRIBE usuarios;
DESCRIBE logs_sistema;

SELECT 'Banco de dados atualizado com sucesso!' as status;