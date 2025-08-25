<?php
/**
 * Script para criar tabela de perfil de usuário
 * Adiciona campos para personalização e dados acadêmicos
 */

require_once 'config.php';

try {
    $pdo = conectarBD();
    
    echo "🔧 Criando tabela de perfil de usuário...\n";
    
    // Criar tabela perfil_usuario
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS perfil_usuario (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            
            -- Dados Acadêmicos (obrigatórios)
            escola VARCHAR(200) NULL,
            serie_ano VARCHAR(100) NULL,
            cidade_estado VARCHAR(200) NULL,
            gpa DECIMAL(3,2) NULL,
            idiomas TEXT NULL, -- JSON array
            exames_realizados TEXT NULL, -- JSON array
            
            -- Personalização do Avatar
            avatar_tipo ENUM('foto', 'personagem') DEFAULT 'personagem',
            avatar_foto VARCHAR(255) NULL, -- caminho para foto
            avatar_personagem JSON NULL, -- configurações do personagem
            
            -- Preferências e Metas
            pais_interesse VARCHAR(100) NULL,
            meta_intercambio ENUM('graduacao', 'pos_graduacao', 'mestrado', 'doutorado', 'curso_idioma', 'trabalho') NULL,
            meta_prazo ENUM('6_meses', '1_ano', '2_anos', '3_anos', 'mais_3_anos') NULL,
            
            -- Configurações de Fundo
            background_tipo ENUM('padrao', 'personalizado') DEFAULT 'padrao',
            background_imagem VARCHAR(255) NULL,
            background_cor VARCHAR(7) DEFAULT '#4CAF50',
            
            -- Informações Pessoais (opcionais)
            biografia TEXT NULL,
            data_nascimento DATE NULL,
            telefone VARCHAR(20) NULL,
            linkedin VARCHAR(255) NULL,
            instagram VARCHAR(255) NULL,
            
            -- Configurações de Privacidade
            perfil_publico BOOLEAN DEFAULT TRUE,
            mostrar_progresso BOOLEAN DEFAULT TRUE,
            mostrar_badges BOOLEAN DEFAULT TRUE,
            mostrar_historico BOOLEAN DEFAULT TRUE,
            
            -- Timestamps
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            -- Chaves
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            UNIQUE KEY unique_usuario (usuario_id),
            INDEX idx_pais_interesse (pais_interesse),
            INDEX idx_meta_intercambio (meta_intercambio)
        ) ENGINE=InnoDB
    ");
    echo "✅ Tabela perfil_usuario criada!\n";
    
    // Criar tabela para histórico de atividades
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS historico_atividades (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            tipo_atividade ENUM('teste_realizado', 'badge_conquistada', 'nivel_subiu', 'topico_criado', 'resposta_forum', 'login', 'perfil_atualizado') NOT NULL,
            descricao VARCHAR(255) NOT NULL,
            detalhes JSON NULL,
            pontos_ganhos INT DEFAULT 0,
            data_atividade TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            INDEX idx_usuario_data (usuario_id, data_atividade),
            INDEX idx_tipo (tipo_atividade),
            INDEX idx_data (data_atividade)
        ) ENGINE=InnoDB
    ");
    echo "✅ Tabela historico_atividades criada!\n";
    
    // Criar tabela para notificações de usuário
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notificacoes_usuario (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            tipo ENUM('forum_resposta', 'forum_mencao', 'badge_conquistada', 'nivel_subiu', 'sistema') NOT NULL,
            titulo VARCHAR(200) NOT NULL,
            mensagem TEXT NOT NULL,
            link VARCHAR(255) NULL,
            lida BOOLEAN DEFAULT FALSE,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            INDEX idx_usuario_lida (usuario_id, lida),
            INDEX idx_data (data_criacao)
        ) ENGINE=InnoDB
    ");
    echo "✅ Tabela notificacoes_usuario criada!\n";
    
    // Inserir perfis padrão para usuários existentes
    echo "👤 Criando perfis padrão para usuários existentes...\n";
    $pdo->exec("
        INSERT INTO perfil_usuario (usuario_id, avatar_personagem, background_cor)
        SELECT id, 
               JSON_OBJECT(
                   'cabelo_cor', '#8B4513',
                   'cabelo_estilo', 'curto',
                   'pele_cor', '#FDBCB4',
                   'olhos_cor', '#654321',
                   'roupa_cor', '#4CAF50',
                   'roupa_estilo', 'casual'
               ),
               '#4CAF50'
        FROM usuarios 
        WHERE id NOT IN (SELECT usuario_id FROM perfil_usuario)
    ");
    echo "✅ Perfis padrão criados!\n";
    
    echo "\n🎉 Estrutura de perfil de usuário criada com sucesso!\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>
