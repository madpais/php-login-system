<?php
/**
 * Script de Instalação Completa do Sistema DayDreamming
 * 
 * Este script cria todas as 22 tabelas necessárias para o sistema
 * e insere todos os dados existentes no banco de dados para facilitar
 * a instalação do projeto por novos colaboradores.
 * 
 * Todas as tabelas e dados são preservados exatamente como estão no
 * banco de dados original, garantindo que todos os colaboradores
 * tenham o mesmo ambiente de desenvolvimento.
 * 
 * Versão: 3.1.0
 * Data: 2025-09-02
 * Autor: Sistema DayDreamming
 */

echo "🌍 INSTALAÇÃO COMPLETA DO SISTEMA DAYDREAMMING\n";
echo "==============================================\n\n";

// Configurações do banco de dados
$host = 'localhost';
$dbname = 'daydreamming_db';
$username = 'root';
$password = '';

try {
    // Conectar ao MySQL (sem especificar o banco)
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conectado ao MySQL\n";
    
    // Criar banco de dados se não existir
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Banco de dados '$dbname' criado/verificado\n";
    
    // Conectar ao banco específico
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conectado ao banco '$dbname'\n\n";
    
    echo "📋 CRIANDO TABELAS PRINCIPAIS...\n";
    echo "=================================\n";
    
    // 1. Tabela de usuários (base do sistema)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            usuario VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            senha VARCHAR(255) NOT NULL,
            is_admin TINYINT(1) DEFAULT 0,
            ativo TINYINT(1) DEFAULT 1,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            ultimo_acesso TIMESTAMP NULL,
            ultimo_logout TIMESTAMP NULL
        ) ENGINE=InnoDB
    ");
    echo "✅ Tabela 'usuarios' criada\n";
    
    // 2. Tabela de perfil do usuário
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS perfil_usuario (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            escola VARCHAR(200),
            serie_ano VARCHAR(100),
            cidade_estado VARCHAR(200),
            gpa DECIMAL(3,2),
            idiomas TEXT,
            exames_realizados TEXT,
            avatar_tipo ENUM('foto','personagem') DEFAULT 'personagem',
            avatar_foto VARCHAR(255),
            avatar_personagem LONGTEXT,
            pais_interesse VARCHAR(100),
            meta_intercambio ENUM('graduacao','pos_graduacao','mestrado','doutorado','curso_idioma','trabalho'),
            meta_prazo ENUM('6_meses','1_ano','2_anos','3_anos','mais_3_anos'),
            background_tipo ENUM('padrao','personalizado') DEFAULT 'padrao',
            background_imagem VARCHAR(255),
            background_cor VARCHAR(7) DEFAULT '#4CAF50',
            biografia TEXT,
            data_nascimento DATE,
            telefone VARCHAR(20),
            linkedin VARCHAR(255),
            instagram VARCHAR(255),
            perfil_publico TINYINT(1) DEFAULT 1,
            mostrar_progresso TINYINT(1) DEFAULT 1,
            mostrar_badges TINYINT(1) DEFAULT 1,
            mostrar_historico TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            UNIQUE KEY unique_usuario (usuario_id),
            INDEX idx_pais_interesse (pais_interesse),
            INDEX idx_meta_intercambio (meta_intercambio)
        ) ENGINE=InnoDB
    ");
    echo "✅ Tabela 'perfil_usuario' criada\n";
    
    // 3. Tabela de badges
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS badges (
            id INT AUTO_INCREMENT PRIMARY KEY,
            codigo VARCHAR(50) NOT NULL UNIQUE,
            nome VARCHAR(100) NOT NULL,
            descricao TEXT NOT NULL,
            icone VARCHAR(10) NOT NULL,
            tipo ENUM('pontuacao','frequencia','especial','tempo','social') NOT NULL,
            categoria ENUM('teste','forum','geral','social') DEFAULT 'teste',
            condicao_valor INT,
            raridade ENUM('comum','raro','epico','lendario') DEFAULT 'comum',
            experiencia_bonus INT DEFAULT 50,
            ativa TINYINT(1) DEFAULT 1,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB
    ");
    echo "✅ Tabela 'badges' criada\n";
    
    // 4. Tabela de níveis do usuário
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS niveis_usuario (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            nivel_atual INT DEFAULT 1,
            experiencia_total INT DEFAULT 0,
            experiencia_nivel INT DEFAULT 0,
            experiencia_necessaria INT DEFAULT 100,
            testes_completados INT DEFAULT 0,
            melhor_pontuacao DECIMAL(5,2) DEFAULT 0.00,
            media_pontuacao DECIMAL(5,2) DEFAULT 0.00,
            data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            UNIQUE KEY unique_usuario (usuario_id)
        ) ENGINE=InnoDB
    ");
    echo "✅ Tabela 'niveis_usuario' criada\n";
    
    // 5. Tabela de questões
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS questoes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tipo_prova ENUM('toefl', 'ielts', 'sat', 'gre', 'gmat', 'dele', 'delf', 'testdaf', 'jlpt', 'hsk') NOT NULL,
            numero_questao INT NOT NULL,
            enunciado TEXT NOT NULL,
            alternativa_a VARCHAR(500) NOT NULL,
            alternativa_b VARCHAR(500) NOT NULL,
            alternativa_c VARCHAR(500) NOT NULL,
            alternativa_d VARCHAR(500) NOT NULL,
            alternativa_e VARCHAR(500),
            resposta_correta ENUM('a', 'b', 'c', 'd', 'e') NOT NULL,
            tipo_questao ENUM('multipla_escolha', 'dissertativa') DEFAULT 'multipla_escolha',
            resposta_dissertativa TEXT,
            dificuldade ENUM('facil', 'medio', 'dificil') DEFAULT 'medio',
            materia VARCHAR(100),
            assunto VARCHAR(100),
            explicacao TEXT,
            ativa TINYINT(1) DEFAULT 1,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            ativo TINYINT(1) DEFAULT 1,
            UNIQUE KEY unique_tipo_numero (tipo_prova, numero_questao)
        ) ENGINE=InnoDB
    ");
    echo "✅ Tabela 'questoes' criada\n";
    
    // 6. Tabela de sessões de teste
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sessoes_teste (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            tipo_prova ENUM('toefl', 'ielts', 'sat', 'gre', 'gmat', 'dele', 'delf', 'testdaf', 'jlpt', 'hsk') NOT NULL,
            inicio DATETIME NOT NULL,
            fim DATETIME,
            duracao_minutos INT NOT NULL,
            status ENUM('ativo', 'finalizado', 'expirado', 'cancelado') DEFAULT 'ativo',
            questoes_total INT NOT NULL,
            questoes_respondidas INT DEFAULT 0,
            tempo_gasto INT DEFAULT 0,
            pontuacao DECIMAL(5,2),
            acertos INT,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            pontuacao_final DECIMAL(5,2) DEFAULT 0.00,
            total_questoes INT DEFAULT 20,
            percentual_acerto DECIMAL(5,2) DEFAULT 0.00,
            data_inicio DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            INDEX (usuario_id)
        ) ENGINE=InnoDB
    ");
    echo "✅ Tabela 'sessoes_teste' criada\n";
    
    // 7. Tabela de respostas do usuário
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS respostas_usuario (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sessao_id INT NOT NULL,
            questao_id INT NOT NULL,
            questao_numero INT NOT NULL,
            resposta_usuario ENUM('a', 'b', 'c', 'd', 'e'),
            resposta_dissertativa_usuario TEXT,
            resposta_correta ENUM('a', 'b', 'c', 'd', 'e') NOT NULL,
            esta_correta TINYINT(1) NOT NULL,
            tempo_resposta INT,
            tentativas INT DEFAULT 1,
            marcada_revisao TINYINT(1) DEFAULT 0,
            data_resposta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            acertou TINYINT(1) DEFAULT 0,
            FOREIGN KEY (sessao_id) REFERENCES sessoes_teste(id) ON DELETE CASCADE,
            FOREIGN KEY (questao_id) REFERENCES questoes(id) ON DELETE CASCADE,
            UNIQUE KEY unique_sessao_questao (sessao_id, questao_numero),
            INDEX (sessao_id),
            INDEX (questao_id),
            INDEX (esta_correta)
        ) ENGINE=InnoDB
    ");
    echo "✅ Tabela 'respostas_usuario' criada\n";
    
    // 8. Tabela de resultados dos testes
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS resultados_testes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            sessao_id INT NOT NULL,
            tipo_prova ENUM('toefl', 'ielts', 'sat', 'gre', 'gmat', 'dele', 'delf', 'testdaf', 'jlpt', 'hsk') NOT NULL,
            pontuacao DECIMAL(5,2) NOT NULL,
            acertos INT NOT NULL,
            erros INT NOT NULL,
            nao_respondidas INT NOT NULL,
            total_questoes INT NOT NULL,
            tempo_gasto INT NOT NULL,
            data_realizacao DATETIME NOT NULL,
            questoes_respondidas INT DEFAULT 0,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            FOREIGN KEY (sessao_id) REFERENCES sessoes_teste(id) ON DELETE CASCADE,
            INDEX (usuario_id),
            INDEX (sessao_id)
        ) ENGINE=InnoDB
    ");
    echo "✅ Tabela 'resultados_testes' criada\n";
    
    echo "\n📋 CRIANDO TABELAS DO SISTEMA...\n";
    echo "=================================\n";

    // 9. Tabela de configurações do sistema
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS configuracoes_sistema (
            id INT AUTO_INCREMENT PRIMARY KEY,
            chave VARCHAR(100) NOT NULL UNIQUE,
            valor TEXT NOT NULL,
            tipo ENUM('string','integer','boolean','json') DEFAULT 'string',
            categoria VARCHAR(50) DEFAULT 'geral',
            descricao TEXT,
            editavel TINYINT(1) DEFAULT 1,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_chave (chave),
            INDEX idx_categoria (categoria)
        ) ENGINE=InnoDB
    ");
    echo "✅ Tabela 'configuracoes_sistema' criada\n";

    // 10. Tabela de logs de acesso
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS logs_acesso (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT,
            ip VARCHAR(45) NOT NULL,
            sucesso TINYINT(1) NOT NULL,
            data_tentativa TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
            INDEX (usuario_id)
        ) ENGINE=InnoDB
    ");
    echo "✅ Tabela 'logs_acesso' criada\n";

    // 11. Tabela de logs do sistema
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS logs_sistema (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT,
            acao VARCHAR(100) NOT NULL,
            tabela_afetada VARCHAR(50),
            registro_id INT,
            detalhes TEXT,
            ip VARCHAR(45),
            data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
            INDEX idx_usuario_id (usuario_id),
            INDEX idx_acao (acao),
            INDEX idx_data_hora (data_hora)
        ) ENGINE=InnoDB
    ");
    echo "✅ Tabela 'logs_sistema' criada\n";

    // 12. Tabela de notificações
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notificacoes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            tipo ENUM('badge_conquistada','nivel_subiu','novo_teste','resposta_forum','sistema') NOT NULL,
            titulo VARCHAR(200) NOT NULL,
            mensagem TEXT NOT NULL,
            icone VARCHAR(10),
            url VARCHAR(255),
            lida TINYINT(1) DEFAULT 0,
            data_leitura TIMESTAMP NULL,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            data_expiracao TIMESTAMP NULL,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            INDEX idx_usuario_id (usuario_id),
            INDEX idx_tipo (tipo),
            INDEX idx_lida (lida),
            INDEX idx_data_criacao (data_criacao)
        ) ENGINE=InnoDB
    ");
    echo "✅ Tabela 'notificacoes' criada\n";

    // 13. Tabela de notificações do usuário
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notificacoes_usuario (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            tipo ENUM('forum_resposta','forum_mencao','badge_conquistada','nivel_subiu','sistema') NOT NULL,
            titulo VARCHAR(200) NOT NULL,
            mensagem TEXT NOT NULL,
            link VARCHAR(255),
            lida TINYINT(1) DEFAULT 0,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            INDEX idx_usuario_lida (usuario_id, lida),
            INDEX idx_data (data_criacao)
        ) ENGINE=InnoDB
    ");
    echo "✅ Tabela 'notificacoes_usuario' criada\n";

    // 14. Tabela de histórico de atividades
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS historico_atividades (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            tipo_atividade ENUM('teste_realizado','badge_conquistada','nivel_subiu','topico_criado','resposta_forum','login','perfil_atualizado') NOT NULL,
            descricao VARCHAR(255) NOT NULL,
            detalhes LONGTEXT,
            pontos_ganhos INT DEFAULT 0,
            data_atividade TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            INDEX idx_usuario_data (usuario_id, data_atividade),
            INDEX idx_tipo (tipo_atividade),
            INDEX idx_data (data_atividade)
        ) ENGINE=InnoDB
    ");
    echo "✅ Tabela 'historico_atividades' criada\n";

    // 15. Tabela de histórico de experiência
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS historico_experiencia (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            tipo_acao ENUM('teste_completado','badge_conquistada','primeiro_teste','bonus_velocidade','bonus_pontuacao','participacao_forum','login_diario') NOT NULL,
            experiencia_ganha INT NOT NULL,
            contexto VARCHAR(100),
            nivel_anterior INT NOT NULL,
            nivel_posterior INT NOT NULL,
            detalhes LONGTEXT,
            data_acao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            INDEX idx_usuario_id (usuario_id),
            INDEX idx_tipo_acao (tipo_acao),
            INDEX idx_data_acao (data_acao)
        ) ENGINE=InnoDB
    ");
    echo "✅ Tabela 'historico_experiencia' criada\n";

    // 16. Tabela de países visitados
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS paises_visitados (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            pais_codigo VARCHAR(50) NOT NULL,
            pais_nome VARCHAR(100) NOT NULL,
            data_primeira_visita TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            total_visitas INT DEFAULT 1,
            ultima_visita TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            UNIQUE KEY unique_usuario_pais (usuario_id, pais_codigo),
            INDEX idx_usuario (usuario_id),
            INDEX idx_pais (pais_codigo),
            INDEX idx_data_visita (data_primeira_visita)
        ) ENGINE=InnoDB
    ");
    echo "✅ Tabela 'paises_visitados' criada\n";

    // 17. Tabela de badges do usuário
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS usuario_badges (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            badge_id INT NOT NULL,
            data_conquista DATETIME NOT NULL,
            contexto VARCHAR(100),
            notificado TINYINT(1) DEFAULT 0,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE CASCADE,
            UNIQUE KEY unique_usuario_badge (usuario_id, badge_id),
            INDEX (badge_id)
        ) ENGINE=InnoDB
    ");
    echo "✅ Tabela 'usuario_badges' criada\n";

    echo "\n📋 CRIANDO TABELAS DO FÓRUM...\n";
    echo "===============================\n";

    // 18. Tabela de categorias do fórum
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS forum_categorias (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            descricao TEXT,
            cor VARCHAR(7) DEFAULT '#007bff',
            icone VARCHAR(10) DEFAULT '💬',
            ativo TINYINT(1) DEFAULT 1,
            ordem INT DEFAULT 0,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_ativo (ativo),
            INDEX idx_ordem (ordem)
        ) ENGINE=InnoDB
    ");
    echo "✅ Tabela 'forum_categorias' criada\n";

    // 19. Tabela de tópicos do fórum
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS forum_topicos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            categoria_id INT NOT NULL,
            titulo VARCHAR(200) NOT NULL,
            conteudo TEXT NOT NULL,
            autor_id INT NOT NULL,
            aprovado TINYINT(1) DEFAULT 1,
            fixado TINYINT(1) DEFAULT 0,
            fechado TINYINT(1) DEFAULT 0,
            visualizacoes INT DEFAULT 0,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (categoria_id) REFERENCES forum_categorias(id) ON DELETE CASCADE,
            FOREIGN KEY (autor_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            INDEX idx_categoria (categoria_id),
            INDEX idx_autor (autor_id),
            INDEX idx_aprovado (aprovado)
        ) ENGINE=InnoDB
    ");
    echo "✅ Tabela 'forum_topicos' criada\n";

    // 20. Tabela de respostas do fórum
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS forum_respostas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            topico_id INT NOT NULL,
            conteudo TEXT NOT NULL,
            autor_id INT NOT NULL,
            aprovado TINYINT(1) DEFAULT 1,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (topico_id) REFERENCES forum_topicos(id) ON DELETE CASCADE,
            FOREIGN KEY (autor_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            INDEX idx_topico (topico_id),
            INDEX idx_autor (autor_id),
            INDEX idx_aprovado (aprovado)
        ) ENGINE=InnoDB
    ");
    echo "✅ Tabela 'forum_respostas' criada\n";

    // 21. Tabela de curtidas do fórum
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS forum_curtidas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            topico_id INT,
            resposta_id INT,
            tipo_curtida ENUM('like','dislike') DEFAULT 'like',
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            FOREIGN KEY (topico_id) REFERENCES forum_topicos(id) ON DELETE CASCADE,
            FOREIGN KEY (resposta_id) REFERENCES forum_respostas(id) ON DELETE CASCADE,
            UNIQUE KEY unique_curtida_topico (usuario_id, topico_id),
            UNIQUE KEY unique_curtida_resposta (usuario_id, resposta_id),
            INDEX idx_usuario (usuario_id),
            INDEX idx_topico (topico_id),
            INDEX idx_resposta (resposta_id)
        ) ENGINE=InnoDB
    ");
    echo "✅ Tabela 'forum_curtidas' criada\n";

    // 22. Tabela de moderação do fórum
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS forum_moderacao (
            id INT AUTO_INCREMENT PRIMARY KEY,
            moderador_id INT NOT NULL,
            topico_id INT,
            resposta_id INT,
            acao ENUM('aprovar','rejeitar','editar','deletar','fixar','fechar') NOT NULL,
            motivo TEXT,
            data_acao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (moderador_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            FOREIGN KEY (topico_id) REFERENCES forum_topicos(id) ON DELETE CASCADE,
            FOREIGN KEY (resposta_id) REFERENCES forum_respostas(id) ON DELETE CASCADE,
            INDEX idx_moderador (moderador_id),
            INDEX idx_topico (topico_id),
            INDEX resposta_id (resposta_id),
            INDEX idx_acao (acao)
        ) ENGINE=InnoDB
    ");
    echo "✅ Tabela 'forum_moderacao' criada\n";

    echo "\n📊 INSERINDO DADOS INICIAIS...\n";
    echo "===============================\n";

    // Inserir dados na tabela usuarios
    $pdo->exec("INSERT INTO usuarios (id, nome, usuario, email, senha, is_admin, ativo, data_criacao, ultimo_acesso, ultimo_logout) VALUES (1, 'Administrador', 'admin', 'admin@daydreamming.com', '$2y$10$wj2./v4McroYwA09hlkyQ.n5wKgrGnVy18ulNvf5iqXXdwl7gVahK', 1, 1, '2025-08-28 00:11:13', NULL, NULL)");
    $pdo->exec("INSERT INTO usuarios (id, nome, usuario, email, senha, is_admin, ativo, data_criacao, ultimo_acesso, ultimo_logout) VALUES (2, 'Usuário Teste', 'teste', 'teste@daydreamming.com', '$2y$10$3e/EL3rga.iI61mBzv1UmexvH3SXPJy/HgryMZ1ABCkDseQ8ZXPf6', 0, 1, '2025-08-28 00:11:13', NULL, NULL)");
    echo "✅ Usuários inseridos\n";
    
    // Inserir dados na tabela badges
    $pdo->exec("INSERT INTO badges (id, codigo, nome, descricao, icone, tipo, categoria, condicao_valor, raridade, experiencia_bonus, ativa, data_criacao) VALUES (1, 'primeiro_teste', 'Primeiro Teste', 'Complete seu primeiro teste', '🎯', 'especial', 'teste', 1, 'comum', 100, 1, '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO badges (id, codigo, nome, descricao, icone, tipo, categoria, condicao_valor, raridade, experiencia_bonus, ativa, data_criacao) VALUES (2, 'dez_testes', '10 Testes', 'Complete 10 testes', '🔟', 'frequencia', 'teste', 10, 'comum', 200, 1, '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO badges (id, codigo, nome, descricao, icone, tipo, categoria, condicao_valor, raridade, experiencia_bonus, ativa, data_criacao) VALUES (3, 'cem_testes', '100 Testes', 'Complete 100 testes', '💯', 'frequencia', 'teste', 100, 'raro', 500, 1, '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO badges (id, codigo, nome, descricao, icone, tipo, categoria, condicao_valor, raridade, experiencia_bonus, ativa, data_criacao) VALUES (4, 'pontuacao_alta', 'Pontuação Alta', 'Obtenha mais de 90% em um teste', '⭐', 'pontuacao', 'teste', 90, 'raro', 300, 1, '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO badges (id, codigo, nome, descricao, icone, tipo, categoria, condicao_valor, raridade, experiencia_bonus, ativa, data_criacao) VALUES (5, 'pontuacao_perfeita', 'Pontuação Perfeita', 'Obtenha 100% em um teste', '🏆', 'pontuacao', 'teste', 100, 'epico', 1000, 1, '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO badges (id, codigo, nome, descricao, icone, tipo, categoria, condicao_valor, raridade, experiencia_bonus, ativa, data_criacao) VALUES (6, 'participante_forum', 'Participante do Fórum', 'Crie seu primeiro tópico no fórum', '💬', 'social', 'forum', 1, 'comum', 150, 1, '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO badges (id, codigo, nome, descricao, icone, tipo, categoria, condicao_valor, raridade, experiencia_bonus, ativa, data_criacao) VALUES (7, 'colaborador', 'Colaborador', 'Responda 10 tópicos no fórum', '🤝', 'social', 'forum', 10, 'raro', 400, 1, '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO badges (id, codigo, nome, descricao, icone, tipo, categoria, condicao_valor, raridade, experiencia_bonus, ativa, data_criacao) VALUES (8, 'veterano', 'Veterano', 'Use o sistema por 30 dias', '🎖️', 'tempo', 'geral', 30, 'epico', 800, 1, '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO badges (id, codigo, nome, descricao, icone, tipo, categoria, condicao_valor, raridade, experiencia_bonus, ativa, data_criacao) VALUES (9, 'explorador', 'Explorador', 'Visite 10 países diferentes', '🌍', 'especial', 'geral', 10, 'raro', 350, 1, '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO badges (id, codigo, nome, descricao, icone, tipo, categoria, condicao_valor, raridade, experiencia_bonus, ativa, data_criacao) VALUES (10, 'globetrotter', 'Globetrotter', 'Visite todos os países disponíveis', '✈️', 'especial', 'geral', 28, 'lendario', 2000, 1, '2025-08-28 00:11:13')");
    echo "✅ Badges inseridas\n";
    
    // Inserir dados na tabela configuracoes_sistema
    $pdo->exec("INSERT INTO configuracoes_sistema (id, chave, valor, tipo, categoria, descricao, editavel, data_criacao, data_atualizacao) VALUES (1, 'site_nome', 'DayDreamming', 'string', 'geral', 'Nome do site', 1, '2025-08-28 00:11:13', '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO configuracoes_sistema (id, chave, valor, tipo, categoria, descricao, editavel, data_criacao, data_atualizacao) VALUES (2, 'site_descricao', 'Plataforma de preparação para intercâmbio', 'string', 'geral', 'Descrição do site', 1, '2025-08-28 00:11:13', '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO configuracoes_sistema (id, chave, valor, tipo, categoria, descricao, editavel, data_criacao, data_atualizacao) VALUES (3, 'manutencao_ativa', 0, 'boolean', 'sistema', 'Modo manutenção ativo', 1, '2025-08-28 00:11:13', '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO configuracoes_sistema (id, chave, valor, tipo, categoria, descricao, editavel, data_criacao, data_atualizacao) VALUES (4, 'registro_aberto', 1, 'boolean', 'usuarios', 'Permitir novos registros', 1, '2025-08-28 00:11:13', '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO configuracoes_sistema (id, chave, valor, tipo, categoria, descricao, editavel, data_criacao, data_atualizacao) VALUES (5, 'forum_ativo', 1, 'boolean', 'forum', 'Fórum ativo', 1, '2025-08-28 00:11:13', '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO configuracoes_sistema (id, chave, valor, tipo, categoria, descricao, editavel, data_criacao, data_atualizacao) VALUES (6, 'moderacao_automatica', 0, 'boolean', 'forum', 'Moderação automática do fórum', 1, '2025-08-28 00:11:13', '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO configuracoes_sistema (id, chave, valor, tipo, categoria, descricao, editavel, data_criacao, data_atualizacao) VALUES (7, 'max_tentativas_login', 5, 'integer', 'seguranca', 'Máximo de tentativas de login', 1, '2025-08-28 00:11:13', '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO configuracoes_sistema (id, chave, valor, tipo, categoria, descricao, editavel, data_criacao, data_atualizacao) VALUES (8, 'tempo_bloqueio_login', 15, 'integer', 'seguranca', 'Tempo de bloqueio em minutos', 1, '2025-08-28 00:11:13', '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO configuracoes_sistema (id, chave, valor, tipo, categoria, descricao, editavel, data_criacao, data_atualizacao) VALUES (9, 'duracao_teste_padrao', 60, 'integer', 'testes', 'Duração padrão dos testes em minutos', 1, '2025-08-28 00:11:13', '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO configuracoes_sistema (id, chave, valor, tipo, categoria, descricao, editavel, data_criacao, data_atualizacao) VALUES (10, 'questoes_por_teste', 20, 'integer', 'testes', 'Número de questões por teste', 1, '2025-08-28 00:11:13', '2025-08-28 00:11:13')");
    echo "✅ Configurações do sistema inseridas\n";
    
    // Inserir dados na tabela forum_categorias
    $pdo->exec("INSERT INTO forum_categorias (id, nome, descricao, cor, icone, ativo, ordem, data_criacao) VALUES (1, 'Geral', 'Discussões gerais sobre intercâmbio', '#007bff', '💬', 1, 1, '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO forum_categorias (id, nome, descricao, cor, icone, ativo, ordem, data_criacao) VALUES (2, 'Testes e Preparação', 'Dicas e discussões sobre testes', '#28a745', '📚', 1, 2, '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO forum_categorias (id, nome, descricao, cor, icone, ativo, ordem, data_criacao) VALUES (3, 'Países e Destinos', 'Informações sobre países e destinos', '#17a2b8', '🌍', 1, 3, '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO forum_categorias (id, nome, descricao, cor, icone, ativo, ordem, data_criacao) VALUES (4, 'Experiências', 'Compartilhe suas experiências', '#ffc107', '✨', 1, 4, '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO forum_categorias (id, nome, descricao, cor, icone, ativo, ordem, data_criacao) VALUES (5, 'Dúvidas e Suporte', 'Tire suas dúvidas aqui', '#dc3545', '❓', 1, 5, '2025-08-28 00:11:13')");
    echo "✅ Categorias do fórum inseridas\n";

    // Já inserimos os dados acima, não precisamos inserir novamente


    echo "\n🎉 INSTALAÇÃO CONCLUÍDA COM SUCESSO!\n";
    echo "====================================\n";
    echo "✅ 22 tabelas criadas\n";
    echo "✅ Dados iniciais inseridos\n";
    echo "✅ Sistema pronto para uso\n\n";

    echo "👤 USUÁRIOS CRIADOS:\n";
    echo "====================\n";
    echo "🔑 Admin: admin / admin123\n";
    echo "🧪 Teste: teste / teste123\n\n";

    echo "📊 ESTATÍSTICAS:\n";
    echo "================\n";

    // Contar registros em cada tabela
    $tabelas = [
        'usuarios', 'perfil_usuario', 'badges', 'niveis_usuario', 'questoes',
        'sessoes_teste', 'respostas_usuario', 'resultados_testes', 'configuracoes_sistema',
        'logs_acesso', 'logs_sistema', 'notificacoes', 'notificacoes_usuario',
        'historico_atividades', 'historico_experiencia', 'paises_visitados',
        'usuario_badges', 'forum_categorias', 'forum_topicos', 'forum_respostas',
        'forum_curtidas', 'forum_moderacao'
    ];

    foreach ($tabelas as $tabela) {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM $tabela");
        $total = $stmt->fetch();
        echo "📋 $tabela: {$total['total']} registros\n";
    }

    echo "\n🌐 PRÓXIMOS PASSOS:\n";
    echo "===================\n";
    echo "1. Configure o arquivo config.php com suas credenciais\n";
    echo "2. Acesse o sistema via navegador\n";
    echo "3. Faça login com admin/admin123\n";
    echo "4. Configure as questões dos testes\n";
    echo "5. Personalize as configurações do sistema\n\n";

    echo "🚀 Sistema DayDreamming instalado e pronto para uso!\n";

} catch(PDOException $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    exit(1);
}
?>
