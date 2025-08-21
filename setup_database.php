<?php
/**
 * Script de configuração completa do banco de dados
 * Execute este arquivo após fazer git clone do projeto
 */

echo "🚀 CONFIGURAÇÃO DO BANCO DE DADOS - DAYDREAMING PROJECT\n";
echo "=======================================================\n\n";

// Configurações do banco
$config = [
    'host' => 'localhost',
    'user' => 'root',
    'password' => '',
    'database' => 'db_daydreamming_project',
    'charset' => 'utf8mb4'
];

try {
    // 1. Conectar ao MySQL (sem especificar database)
    echo "📡 CONECTANDO AO MYSQL...\n";
    $dsn = "mysql:host={$config['host']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['user'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "✅ Conectado ao MySQL\n\n";
    
    // 2. Criar database se não existir
    echo "🗄️ CRIANDO DATABASE...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS {$config['database']} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Database '{$config['database']}' criado/verificado\n\n";
    
    // 3. Conectar ao database específico
    $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['user'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // 4. Criar tabelas
    echo "📋 CRIANDO TABELAS...\n";
    
    // Tabela usuarios
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            usuario VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            senha VARCHAR(255) NOT NULL,
            is_admin BOOLEAN DEFAULT FALSE,
            ativo BOOLEAN DEFAULT TRUE,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            ultimo_acesso TIMESTAMP NULL,
            ultimo_logout TIMESTAMP NULL,
            INDEX idx_usuario (usuario),
            INDEX idx_ativo (ativo)
        )
    ");
    echo "✅ Tabela 'usuarios' criada\n";
    
    // Tabela questoes
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS questoes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            numero_questao INT NOT NULL,
            tipo_prova VARCHAR(20) NOT NULL,
            enunciado TEXT NOT NULL,
            alternativa_a TEXT,
            alternativa_b TEXT,
            alternativa_c TEXT,
            alternativa_d TEXT,
            alternativa_e TEXT,
            resposta_correta VARCHAR(10),
            tipo_questao ENUM('multipla_escolha', 'dissertativa') DEFAULT 'multipla_escolha',
            resposta_dissertativa TEXT,
            materia VARCHAR(50),
            assunto VARCHAR(100),
            dificuldade ENUM('facil', 'medio', 'dificil') DEFAULT 'medio',
            explicacao TEXT,
            ativa BOOLEAN DEFAULT TRUE,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_tipo_prova (tipo_prova),
            INDEX idx_numero_questao (numero_questao),
            INDEX idx_ativa (ativa)
        )
    ");
    echo "✅ Tabela 'questoes' criada\n";
    
    // Tabela sessoes_teste
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sessoes_teste (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            tipo_prova VARCHAR(20) NOT NULL,
            inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            fim TIMESTAMP NULL,
            duracao_minutos INT DEFAULT 180,
            status ENUM('ativo', 'finalizado', 'cancelado') DEFAULT 'ativo',
            pontuacao_final DECIMAL(5,2) DEFAULT 0.00,
            acertos INT DEFAULT 0,
            questoes_respondidas INT DEFAULT 0,
            tempo_gasto INT DEFAULT 0,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            INDEX idx_usuario_status (usuario_id, status)
        )
    ");
    echo "✅ Tabela 'sessoes_teste' criada\n";
    
    // Tabela respostas_usuario
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS respostas_usuario (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sessao_id INT NOT NULL,
            questao_id INT NOT NULL,
            questao_numero INT NOT NULL,
            resposta_usuario VARCHAR(10),
            resposta_dissertativa_usuario TEXT,
            resposta_correta VARCHAR(10),
            esta_correta BOOLEAN DEFAULT FALSE,
            data_resposta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (sessao_id) REFERENCES sessoes_teste(id) ON DELETE CASCADE,
            FOREIGN KEY (questao_id) REFERENCES questoes(id) ON DELETE CASCADE,
            UNIQUE KEY unique_sessao_questao (sessao_id, questao_id)
        )
    ");
    echo "✅ Tabela 'respostas_usuario' criada\n";
    
    // Tabela resultados_testes
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS resultados_testes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            sessao_id INT NOT NULL,
            tipo_prova VARCHAR(20) NOT NULL,
            pontuacao DECIMAL(5,2) DEFAULT 0.00,
            acertos INT DEFAULT 0,
            total_questoes INT DEFAULT 0,
            questoes_respondidas INT DEFAULT 0,
            tempo_gasto INT DEFAULT 0,
            data_realizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            FOREIGN KEY (sessao_id) REFERENCES sessoes_teste(id) ON DELETE CASCADE
        )
    ");
    echo "✅ Tabela 'resultados_testes' criada\n";
    
    // Tabela badges (se necessário)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS badges (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            descricao TEXT,
            icone VARCHAR(50),
            condicao_tipo VARCHAR(50),
            condicao_valor INT,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "✅ Tabela 'badges' criada\n";
    
    // Tabela usuario_badges
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS usuario_badges (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            badge_id INT NOT NULL,
            data_conquista TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE CASCADE,
            UNIQUE KEY unique_usuario_badge (usuario_id, badge_id)
        )
    ");
    echo "✅ Tabela 'usuario_badges' criada\n";
    
    // Tabela forum_categorias
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS forum_categorias (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            descricao TEXT,
            cor VARCHAR(7) DEFAULT '#007bff',
            icone VARCHAR(10) DEFAULT '📝',
            ativo BOOLEAN DEFAULT TRUE,
            ordem INT DEFAULT 0,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_ativo (ativo),
            INDEX idx_ordem (ordem)
        )
    ");
    echo "✅ Tabela 'forum_categorias' criada\n";

    // Tabela forum_topicos
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS forum_topicos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            categoria_id INT NOT NULL,
            titulo VARCHAR(200) NOT NULL,
            conteudo TEXT NOT NULL,
            autor_id INT NOT NULL,
            aprovado BOOLEAN DEFAULT FALSE,
            fixado BOOLEAN DEFAULT FALSE,
            fechado BOOLEAN DEFAULT FALSE,
            visualizacoes INT DEFAULT 0,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (categoria_id) REFERENCES forum_categorias(id) ON DELETE CASCADE,
            FOREIGN KEY (autor_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            INDEX idx_categoria (categoria_id),
            INDEX idx_autor (autor_id),
            INDEX idx_aprovado (aprovado)
        )
    ");
    echo "✅ Tabela 'forum_topicos' criada\n";

    // Tabela forum_respostas
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS forum_respostas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            topico_id INT NOT NULL,
            conteudo TEXT NOT NULL,
            autor_id INT NOT NULL,
            aprovado BOOLEAN DEFAULT FALSE,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (topico_id) REFERENCES forum_topicos(id) ON DELETE CASCADE,
            FOREIGN KEY (autor_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            INDEX idx_topico (topico_id),
            INDEX idx_autor (autor_id),
            INDEX idx_aprovado (aprovado)
        )
    ");
    echo "✅ Tabela 'forum_respostas' criada\n";

    // Tabela niveis_usuario
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
        )
    ");
    echo "✅ Tabela 'niveis_usuario' criada\n";

    // Tabela configuracoes_sistema
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS configuracoes_sistema (
            id INT AUTO_INCREMENT PRIMARY KEY,
            chave VARCHAR(100) UNIQUE NOT NULL,
            valor TEXT NOT NULL,
            tipo ENUM('string', 'integer', 'boolean', 'json') DEFAULT 'string',
            categoria VARCHAR(50) DEFAULT 'geral',
            descricao TEXT,
            editavel BOOLEAN DEFAULT TRUE,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_chave (chave),
            INDEX idx_categoria (categoria)
        )
    ");
    echo "✅ Tabela 'configuracoes_sistema' criada\n";

    // Tabela logs_sistema
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS logs_sistema (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NULL,
            acao VARCHAR(100) NOT NULL,
            detalhes TEXT,
            ip_address VARCHAR(45),
            user_agent TEXT,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
            INDEX idx_usuario (usuario_id),
            INDEX idx_acao (acao),
            INDEX idx_data (data_criacao)
        )
    ");
    echo "✅ Tabela 'logs_sistema' criada\n";

    // Tabela logs_acesso
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS logs_acesso (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NULL,
            tipo_evento ENUM('login', 'logout', 'tentativa_login') NOT NULL,
            sucesso BOOLEAN DEFAULT TRUE,
            ip_address VARCHAR(45),
            user_agent TEXT,
            data_evento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
            INDEX idx_usuario (usuario_id),
            INDEX idx_tipo (tipo_evento),
            INDEX idx_data (data_evento)
        )
    ");
    echo "✅ Tabela 'logs_acesso' criada\n";

    // Tabela notificacoes
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notificacoes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            titulo VARCHAR(200) NOT NULL,
            mensagem TEXT NOT NULL,
            tipo ENUM('info', 'sucesso', 'aviso', 'erro') DEFAULT 'info',
            lida BOOLEAN DEFAULT FALSE,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            data_leitura TIMESTAMP NULL,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            INDEX idx_usuario (usuario_id),
            INDEX idx_lida (lida),
            INDEX idx_tipo (tipo)
        )
    ");
    echo "✅ Tabela 'notificacoes' criada\n";

    // Tabela historico_experiencia
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS historico_experiencia (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            acao VARCHAR(100) NOT NULL,
            xp_ganho INT NOT NULL,
            xp_total_anterior INT NOT NULL,
            xp_total_novo INT NOT NULL,
            nivel_anterior INT NOT NULL,
            nivel_novo INT NOT NULL,
            detalhes TEXT,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            INDEX idx_usuario (usuario_id),
            INDEX idx_acao (acao),
            INDEX idx_data (data_criacao)
        )
    ");
    echo "✅ Tabela 'historico_experiencia' criada\n";

    // Tabela forum_curtidas
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS forum_curtidas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            topico_id INT NULL,
            resposta_id INT NULL,
            tipo_curtida ENUM('like', 'dislike') DEFAULT 'like',
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            FOREIGN KEY (topico_id) REFERENCES forum_topicos(id) ON DELETE CASCADE,
            FOREIGN KEY (resposta_id) REFERENCES forum_respostas(id) ON DELETE CASCADE,
            UNIQUE KEY unique_curtida_topico (usuario_id, topico_id),
            UNIQUE KEY unique_curtida_resposta (usuario_id, resposta_id),
            INDEX idx_usuario (usuario_id),
            INDEX idx_topico (topico_id),
            INDEX idx_resposta (resposta_id)
        )
    ");
    echo "✅ Tabela 'forum_curtidas' criada\n";

    // Tabela forum_moderacao
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS forum_moderacao (
            id INT AUTO_INCREMENT PRIMARY KEY,
            moderador_id INT NOT NULL,
            topico_id INT NULL,
            resposta_id INT NULL,
            acao ENUM('aprovar', 'rejeitar', 'editar', 'deletar', 'fixar', 'fechar') NOT NULL,
            motivo TEXT,
            data_acao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (moderador_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            FOREIGN KEY (topico_id) REFERENCES forum_topicos(id) ON DELETE CASCADE,
            FOREIGN KEY (resposta_id) REFERENCES forum_respostas(id) ON DELETE CASCADE,
            INDEX idx_moderador (moderador_id),
            INDEX idx_topico (topico_id),
            INDEX idx_acao (acao)
        )
    ");
    echo "✅ Tabela 'forum_moderacao' criada\n";

    echo "\n🎉 TODAS AS TABELAS CRIADAS COM SUCESSO!\n\n";
    
    // 5. Inserir dados iniciais
    echo "📝 INSERINDO DADOS INICIAIS...\n";
    
    // Verificar se já existem usuários
    $stmt = $pdo->query("SELECT COUNT(*) FROM usuarios");
    $usuarios_existentes = $stmt->fetchColumn();
    
    if ($usuarios_existentes == 0) {
        echo "👤 Criando usuário administrador...\n";
        
        // Criar usuário admin padrão
        $senha_admin = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->exec("
            INSERT INTO usuarios (nome, login, senha, email, is_admin) 
            VALUES ('Administrador', 'admin', '$senha_admin', 'admin@daydreaming.com', TRUE)
        ");
        
        // Criar usuário de teste
        $senha_teste = password_hash('teste123', PASSWORD_DEFAULT);
        $pdo->exec("
            INSERT INTO usuarios (nome, login, senha, email, is_admin) 
            VALUES ('Usuário Teste', 'teste', '$senha_teste', 'teste@daydreaming.com', FALSE)
        ");
        
        echo "✅ Usuários criados:\n";
        echo "   👨‍💼 Admin: login='admin', senha='admin123'\n";
        echo "   👤 Teste: login='teste', senha='teste123'\n";
    } else {
        echo "ℹ️ Usuários já existem no banco\n";
    }
    
    // Verificar se já existem badges
    $stmt = $pdo->query("SELECT COUNT(*) FROM badges");
    $badges_existentes = $stmt->fetchColumn();
    
    if ($badges_existentes == 0) {
        echo "\n🏆 Criando badges padrão...\n";
        
        $badges = [
            ['Primeiro Teste', 'Complete seu primeiro teste', '🎯', 'testes_realizados', 1],
            ['Estudioso', 'Complete 5 testes', '📚', 'testes_realizados', 5],
            ['Dedicado', 'Complete 10 testes', '🎓', 'testes_realizados', 10],
            ['Expert SAT', 'Obtenha 80% ou mais no SAT', '🏆', 'pontuacao_sat', 80],
            ['Expert TOEFL', 'Obtenha 80% ou mais no TOEFL', '🇺🇸', 'pontuacao_toefl', 80],
            ['Expert IELTS', 'Obtenha 80% ou mais no IELTS', '🇬🇧', 'pontuacao_ielts', 80],
            ['Expert GRE', 'Obtenha 80% ou mais no GRE', '🎯', 'pontuacao_gre', 80]
        ];
        
        foreach ($badges as $badge) {
            $pdo->prepare("INSERT INTO badges (nome, descricao, icone, condicao_tipo, condicao_valor) VALUES (?, ?, ?, ?, ?)")
                ->execute($badge);
        }
        
        echo "✅ " . count($badges) . " badges criadas\n";
    } else {
        echo "ℹ️ Badges já existem no banco\n";
    }

    // Verificar se já existem categorias do fórum
    $stmt = $pdo->query("SELECT COUNT(*) FROM forum_categorias");
    $categorias_existentes = $stmt->fetchColumn();

    if ($categorias_existentes == 0) {
        echo "\n💬 Criando categorias do fórum...\n";

        $categorias = [
            ['Geral', 'Discussões gerais sobre estudar no exterior', '#007bff', '💬', TRUE, 1],
            ['Testes Internacionais', 'Dúvidas e dicas sobre TOEFL, IELTS, SAT, etc.', '#28a745', '📝', TRUE, 2],
            ['Universidades', 'Informações sobre universidades no exterior', '#17a2b8', '🎓', TRUE, 3],
            ['Bolsas de Estudo', 'Oportunidades de bolsas e financiamento', '#ffc107', '💰', TRUE, 4],
            ['Experiências', 'Relatos de quem já estudou fora', '#6f42c1', '✈️', TRUE, 5],
            ['Dúvidas Técnicas', 'Problemas com o sistema e suporte', '#dc3545', '🔧', TRUE, 6]
        ];

        foreach ($categorias as $categoria) {
            $pdo->prepare("INSERT INTO forum_categorias (nome, descricao, cor, icone, ativo, ordem) VALUES (?, ?, ?, ?, ?, ?)")
                ->execute($categoria);
        }

        echo "✅ " . count($categorias) . " categorias do fórum criadas\n";
    } else {
        echo "ℹ️ Categorias do fórum já existem no banco\n";
    }

    // Verificar se já existem configurações do sistema
    $stmt = $pdo->query("SELECT COUNT(*) FROM configuracoes_sistema");
    $configs_existentes = $stmt->fetchColumn();

    if ($configs_existentes == 0) {
        echo "\n⚙️ Inserindo configurações do sistema...\n";

        $configuracoes = [
            ['sistema_nome', 'DayDreaming - Sistema de Simulados', 'string', 'geral', 'Nome do sistema'],
            ['sistema_versao', '2.0.0', 'string', 'geral', 'Versão atual do sistema'],
            ['manutencao_ativa', 'false', 'boolean', 'sistema', 'Se o sistema está em manutenção'],
            ['registro_aberto', 'true', 'boolean', 'usuarios', 'Se novos registros estão permitidos'],
            ['max_tentativas_login', '5', 'integer', 'seguranca', 'Máximo de tentativas de login'],
            ['tempo_bloqueio_minutos', '30', 'integer', 'seguranca', 'Tempo de bloqueio em minutos'],
            ['duracao_sessao_horas', '8', 'integer', 'seguranca', 'Duração da sessão em horas'],
            ['xp_base_teste', '20', 'integer', 'gamificacao', 'XP base por completar um teste'],
            ['xp_bonus_badge', '50', 'integer', 'gamificacao', 'XP bônus por conquistar uma badge'],
            ['forum_moderacao_ativa', 'true', 'boolean', 'forum', 'Se a moderação do fórum está ativa']
        ];

        foreach ($configuracoes as $config) {
            $pdo->prepare("INSERT INTO configuracoes_sistema (chave, valor, tipo, categoria, descricao) VALUES (?, ?, ?, ?, ?)")
                ->execute($config);
        }

        echo "✅ " . count($configuracoes) . " configurações do sistema inseridas\n";
    } else {
        echo "ℹ️ Configurações do sistema já existem no banco\n";
    }
    
    echo "\n📊 RESUMO DA CONFIGURAÇÃO:\n";
    echo "==========================\n";
    echo "✅ Database: {$config['database']}\n";
    echo "✅ Tabelas: 18 tabelas criadas\n";
    echo "   • usuarios (sistema de login)\n";
    echo "   • questoes (banco de questões)\n";
    echo "   • sessoes_teste (controle de testes)\n";
    echo "   • respostas_usuario (respostas detalhadas)\n";
    echo "   • resultados_testes (resultados finais)\n";
    echo "   • badges (sistema de conquistas)\n";
    echo "   • usuario_badges (badges conquistadas)\n";
    echo "   • forum_categorias (categorias do fórum)\n";
    echo "   • forum_topicos (tópicos do fórum)\n";
    echo "   • forum_respostas (respostas do fórum)\n";
    echo "   • forum_curtidas (curtidas do fórum)\n";
    echo "   • forum_moderacao (moderação do fórum)\n";
    echo "   • niveis_usuario (sistema de níveis)\n";
    echo "   • configuracoes_sistema (configurações)\n";
    echo "   • logs_sistema (logs de ações)\n";
    echo "   • logs_acesso (logs de login/logout)\n";
    echo "   • notificacoes (sistema de notificações)\n";
    echo "   • historico_experiencia (histórico de XP)\n";
    echo "✅ Usuários: Admin e Teste\n";
    echo "✅ Badges: Sistema de conquistas\n";
    echo "✅ Fórum: Categorias padrão\n";
    echo "✅ Configurações: Sistema configurado\n";
    echo "✅ Logs: Sistema de auditoria\n";
    echo "✅ Gamificação: Níveis e experiência\n";
    echo "✅ Estrutura: Pronta para uso\n\n";
    
    echo "🔑 CREDENCIAIS DE ACESSO:\n";
    echo "=========================\n";
    echo "👨‍💼 Administrador:\n";
    echo "   Login: admin\n";
    echo "   Senha: admin123\n\n";
    echo "👤 Usuário Teste:\n";
    echo "   Login: teste\n";
    echo "   Senha: teste123\n\n";
    
    echo "🌐 PRÓXIMOS PASSOS:\n";
    echo "===================\n";
    echo "1. Execute: php seed_questoes.php (para carregar questões)\n";
    echo "2. Acesse: http://localhost:8080/\n";
    echo "3. Faça login com as credenciais acima\n";
    echo "4. Teste o sistema de simulados\n\n";
    
    echo "🎉 CONFIGURAÇÃO CONCLUÍDA COM SUCESSO!\n";
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
    echo "Linha: " . $e->getLine() . "\n\n";
    
    echo "🔧 POSSÍVEIS SOLUÇÕES:\n";
    echo "======================\n";
    echo "1. Verifique se o MySQL está rodando\n";
    echo "2. Confirme as credenciais no arquivo config.php\n";
    echo "3. Verifique se o usuário tem permissões para criar databases\n";
    echo "4. Execute: GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost';\n";
}
?>
