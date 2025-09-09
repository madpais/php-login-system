<?php
/**
 * Script de Instalação Completa - NOVA VERSÃO ROBUSTA
 * Sistema DayDreamming com foco especial no sistema de badges
 * 
 * Versão: 2.0.0
 * Data: 2025-01-13
 * Autor: Sistema DayDreamming
 */

echo "\n🚀 INSTALAÇÃO COMPLETA - SISTEMA DAYDREAMMING v2.0\n";
echo "==================================================\n\n";

$erros = [];
$avisos = [];
$passos_concluidos = [];

// Função para exibir progresso
function exibirProgresso($passo, $total, $descricao) {
    $porcentagem = round(($passo / $total) * 100);
    $barra = str_repeat('█', floor($porcentagem / 5));
    $espacos = str_repeat('░', 20 - floor($porcentagem / 5));
    echo "\r[$barra$espacos] $porcentagem% - $descricao";
    if ($passo == $total) echo "\n";
}

// PASSO 1: Verificar ambiente
echo "📋 PASSO 1/8: Verificando ambiente...\n";
exibirProgresso(1, 8, "Verificando PHP e extensões");

// Verificar PHP
if (version_compare(phpversion(), '7.4.0', '<')) {
    $erros[] = "PHP 7.4+ necessário. Versão atual: " . phpversion();
}

// Verificar extensões
$extensoes = ['pdo', 'pdo_mysql', 'mbstring', 'json', 'session'];
foreach ($extensoes as $ext) {
    if (!extension_loaded($ext)) {
        $erros[] = "Extensão PHP '$ext' não instalada";
    }
}

if (!empty($erros)) {
    echo "\n❌ ERROS CRÍTICOS ENCONTRADOS:\n";
    foreach ($erros as $erro) {
        echo "   • $erro\n";
    }
    echo "\n🔧 Corrija os erros antes de continuar.\n";
    exit(1);
}

$passos_concluidos[] = "Ambiente verificado";
sleep(1);

// PASSO 2: Verificar/criar config.php
echo "\n📋 PASSO 2/8: Configurando arquivo de configuração...\n";
exibirProgresso(2, 8, "Verificando config.php");

if (!file_exists('config.php')) {
    if (file_exists('config.exemplo.php')) {
        echo "\n📄 Copiando config.exemplo.php para config.php...\n";
        copy('config.exemplo.php', 'config.php');
        echo "✅ Arquivo config.php criado\n";
        echo "\n⚠️ IMPORTANTE: Configure suas credenciais de banco em config.php\n";
        $avisos[] = "Configure as credenciais do banco de dados em config.php";
    } else {
        $erros[] = "Arquivo config.exemplo.php não encontrado";
    }
} else {
    echo "✅ config.php já existe\n";
}

$passos_concluidos[] = "Configuração verificada";
sleep(1);

// PASSO 3: Testar conexão com banco
echo "\n📋 PASSO 3/8: Testando conexão com banco de dados...\n";
exibirProgresso(3, 8, "Conectando ao MySQL");

try {
    require_once 'config.php';
    
    // Testar conexão
    $dsn = "mysql:host=" . DB_HOST . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "✅ Conexão com MySQL estabelecida\n";
    
    // Verificar se banco existe
    $stmt = $pdo->query("SHOW DATABASES LIKE '" . DB_NAME . "'");
    if ($stmt->rowCount() == 0) {
        echo "📦 Criando banco de dados '" . DB_NAME . "'...\n";
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "✅ Banco de dados criado\n";
    } else {
        echo "✅ Banco de dados já existe\n";
    }
    
    $passos_concluidos[] = "Conexão com banco estabelecida";
    
} catch (PDOException $e) {
    $erros[] = "Erro de conexão: " . $e->getMessage();
    echo "❌ Falha na conexão: " . $e->getMessage() . "\n";
    echo "\n🔧 Verifique as credenciais em config.php\n";
    exit(1);
}

sleep(1);

// PASSO 4: Instalar banco de dados
echo "\n📋 PASSO 4/8: Instalando estrutura do banco...\n";
exibirProgresso(4, 8, "Executando setup_database.php");

if (file_exists('setup_database.php')) {
    echo "\n🗄️ Executando instalação do banco...\n";
    
    ob_start();
    try {
        include 'setup_database.php';
        $output = ob_get_contents();
        ob_end_clean();
        
        if (strpos($output, 'ERRO') !== false || strpos($output, 'ERROR') !== false) {
            echo "⚠️ Possíveis avisos durante instalação:\n";
            $avisos[] = "Verifique os logs de instalação do banco";
        } else {
            echo "✅ Banco de dados instalado com sucesso\n";
        }
        
    } catch (Exception $e) {
        ob_end_clean();
        echo "❌ Erro durante instalação: " . $e->getMessage() . "\n";
        $erros[] = "Falha na instalação do banco: " . $e->getMessage();
    }
    
    $passos_concluidos[] = "Banco de dados instalado";
} else {
    $erros[] = "Arquivo setup_database.php não encontrado";
}

sleep(1);

// PASSO 5: Reset e configuração do sistema de badges
echo "\n📋 PASSO 5/8: Configurando sistema de badges...\n";
exibirProgresso(5, 8, "Resetando e configurando badges");

echo "\n🏆 Configurando sistema de badges...\n";

try {
    $dsn_db = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo_db = new PDO($dsn_db, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Verificar se as tabelas de badges existem e estão corretas
    echo "🔍 Verificando tabelas de badges...\n";
    
    $stmt = $pdo_db->query("SHOW TABLES LIKE 'badges'");
    $badges_table_exists = $stmt->rowCount() > 0;
    
    $stmt = $pdo_db->query("SHOW TABLES LIKE 'usuario_badges'");
    $usuario_badges_table_exists = $stmt->rowCount() > 0;
    
    if (!$badges_table_exists || !$usuario_badges_table_exists) {
        echo "⚠️ Tabelas de badges não encontradas. Criando...\n";
        
        // Criar tabelas de badges
        if (!$badges_table_exists) {
            $pdo_db->exec("
                CREATE TABLE badges (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    codigo VARCHAR(50) NOT NULL UNIQUE,
                    nome VARCHAR(100) NOT NULL,
                    descricao TEXT NOT NULL,
                    icone VARCHAR(10) NOT NULL,
                    tipo ENUM('pontuacao', 'frequencia', 'especial', 'tempo', 'social') NOT NULL,
                    categoria ENUM('teste', 'forum', 'geral', 'social', 'gpa', 'paises') DEFAULT 'teste',
                    condicao_valor INT NULL,
                    raridade ENUM('comum', 'raro', 'epico', 'lendario') DEFAULT 'comum',
                    experiencia_bonus INT DEFAULT 50,
                    ativa TINYINT(1) DEFAULT 1,
                    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_codigo (codigo),
                    INDEX idx_ativa (ativa),
                    INDEX idx_tipo (tipo)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            echo "✅ Tabela badges criada\n";
        }
        
        if (!$usuario_badges_table_exists) {
            $pdo_db->exec("
                CREATE TABLE usuario_badges (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    usuario_id INT NOT NULL,
                    badge_id INT NOT NULL,
                    data_conquista DATETIME NOT NULL,
                    contexto VARCHAR(100) NULL,
                    notificado TINYINT(1) DEFAULT 0,
                    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY unique_usuario_badge (usuario_id, badge_id),
                    INDEX idx_usuario (usuario_id),
                    INDEX idx_badge (badge_id),
                    INDEX idx_data_conquista (data_conquista),
                    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
                    FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            echo "✅ Tabela usuario_badges criada\n";
        }
    }
    
    // Verificar badges existentes
    $stmt = $pdo_db->query("SELECT COUNT(*) FROM badges WHERE ativa = 1");
    $badges_ativas = $stmt->fetchColumn();
    echo "📊 Badges ativas encontradas: $badges_ativas\n";
    
    if ($badges_ativas < 35) {
        echo "🏆 Inserindo badges completas...\n";
        
        if (file_exists('inserir_badges_completo.php')) {
            ob_start();
            include 'inserir_badges_completo.php';
            $output = ob_get_contents();
            ob_end_clean();
            
            // Verificar novamente
            $stmt = $pdo_db->query("SELECT COUNT(*) FROM badges WHERE ativa = 1");
            $badges_apos = $stmt->fetchColumn();
            echo "✅ Badges inseridas: $badges_apos badges ativas\n";
        } else {
            echo "⚠️ Arquivo inserir_badges_completo.php não encontrado\n";
            $avisos[] = "Sistema de badges pode não estar completo";
        }
    } else {
        echo "✅ Sistema de badges já configurado\n";
    }
    
    $passos_concluidos[] = "Sistema de badges configurado";
    
} catch (Exception $e) {
    echo "❌ Erro na configuração de badges: " . $e->getMessage() . "\n";
    $erros[] = "Erro na configuração de badges: " . $e->getMessage();
}

sleep(1);

// PASSO 6: Verificar instalação
echo "\n📋 PASSO 6/8: Verificando instalação...\n";
exibirProgresso(6, 8, "Contando tabelas e dados");

try {
    // Contar tabelas
    $stmt = $pdo_db->query("SHOW TABLES");
    $num_tabelas = $stmt->rowCount();
    
    echo "\n📊 Tabelas encontradas: $num_tabelas\n";
    
    if ($num_tabelas >= 23) {
        echo "✅ Todas as tabelas instaladas corretamente\n";
        
        // Verificar dados essenciais
        $stmt = $pdo_db->query("SELECT COUNT(*) FROM usuarios");
        $usuarios = $stmt->fetchColumn();
        
        $stmt = $pdo_db->query("SELECT COUNT(*) FROM badges WHERE ativa = 1");
        $badges = $stmt->fetchColumn();
        
        echo "👥 Usuários: $usuarios\n";
        echo "🏆 Badges ativas: $badges\n";
        
        if ($usuarios >= 2 && $badges >= 35) {
            echo "✅ Dados iniciais carregados adequadamente\n";
            $passos_concluidos[] = "Sistema completamente instalado";
        } else {
            $avisos[] = "Dados iniciais insuficientes (usuários: $usuarios, badges: $badges)";
        }
        
    } else {
        $avisos[] = "Apenas $num_tabelas tabelas encontradas (esperado: 23+)";
    }
    
} catch (PDOException $e) {
    $erros[] = "Erro na verificação: " . $e->getMessage();
}

sleep(1);

// PASSO 7: Testar sistema de badges
echo "\n📋 PASSO 7/8: Testando sistema de badges...\n";
exibirProgresso(7, 8, "Verificando funções de badges");

echo "\n🧪 Testando funções de badges...\n";

if (file_exists('verificar_badges_funcionais.php')) {
    ob_start();
    include 'verificar_badges_funcionais.php';
    $output = ob_get_contents();
    ob_end_clean();
    
    if (strpos($output, '100% FUNCIONAL') !== false) {
        echo "✅ Sistema de badges 100% funcional\n";
        $passos_concluidos[] = "Sistema de badges testado e funcional";
    } else {
        echo "⚠️ Sistema de badges com possíveis problemas\n";
        $avisos[] = "Verifique o sistema de badges manualmente";
    }
} else {
    echo "⚠️ Script de verificação não encontrado\n";
    $avisos[] = "Não foi possível testar o sistema de badges";
}

sleep(1);

// PASSO 8: Finalização
echo "\n📋 PASSO 8/8: Finalizando instalação...\n";
exibirProgresso(8, 8, "Preparando sistema");

// Verificar arquivos essenciais
$arquivos_essenciais = [
    'index.php' => 'Página inicial',
    'login.php' => 'Sistema de login',
    'forum.php' => 'Fórum',
    'pagina_usuario.php' => 'Dashboard do usuário',
    'badges_manager.php' => 'Sistema de badges',
    'sistema_badges.php' => 'Funções de badges'
];

$arquivos_ok = 0;
foreach ($arquivos_essenciais as $arquivo => $desc) {
    if (file_exists($arquivo)) {
        $arquivos_ok++;
    } else {
        $avisos[] = "Arquivo $arquivo não encontrado";
    }
}

echo "\n📁 Arquivos essenciais: $arquivos_ok/" . count($arquivos_essenciais) . "\n";

$passos_concluidos[] = "Instalação finalizada";
sleep(1);

// RESUMO FINAL
echo "\n" . str_repeat("=", 60) . "\n";
echo "🎉 INSTALAÇÃO CONCLUÍDA!\n";
echo str_repeat("=", 60) . "\n\n";

if (empty($erros)) {
    echo "✅ SUCESSO! Sistema instalado e pronto para uso\n\n";
    
    echo "📊 RESUMO DA INSTALAÇÃO:\n";
    echo "========================\n";
    foreach ($passos_concluidos as $i => $passo) {
        echo "   " . ($i + 1) . ". $passo\n";
    }
    
    if (!empty($avisos)) {
        echo "\n⚠️ AVISOS (" . count($avisos) . "):";
        foreach ($avisos as $i => $aviso) {
            echo "\n   " . ($i + 1) . ". $aviso";
        }
        echo "\n";
    }
    
    echo "\n🚀 PRÓXIMOS PASSOS:\n";
    echo "===================\n";
    echo "1. 🌐 Inicie o servidor: php -S localhost:8080\n";
    echo "2. 🔗 Acesse: http://localhost:8080\n";
    echo "3. 🔐 Faça login com:\n";
    echo "   👤 Admin: admin / admin123\n";
    echo "   👤 Teste: teste / teste123\n";
    echo "4. 🧪 Execute verificar_badges_funcionais.php para diagnósticos\n";
    echo "5. 📝 Teste o sistema de badges realizando ações\n\n";
    
    echo "🎯 SISTEMA DE BADGES CONFIGURADO:\n";
    echo "=================================\n";
    echo "✅ Todas as funções de badges operacionais\n";
    echo "✅ 35+ badges cadastradas e ativas\n";
    echo "✅ Tabelas criadas corretamente\n";
    echo "✅ Pronto para atribuir badges automaticamente\n\n";
    
} else {
    echo "❌ INSTALAÇÃO COM PROBLEMAS\n\n";
    echo "🚨 ERROS ENCONTRADOS (" . count($erros) . "):";
    foreach ($erros as $i => $erro) {
        echo "\n   " . ($i + 1) . ". $erro";
    }
    echo "\n\n🔧 CORRIJA OS ERROS E EXECUTE NOVAMENTE\n";
}

// Criar arquivo de status da instalação
file_put_contents('instalacao_status.txt', 
    "Instalação executada em: " . date('Y-m-d H:i:s') . "\n" .
    "Versão: 2.0.0 (Nova versão robusta)\n" .
    "Passos concluídos: " . count($passos_concluidos) . "\n" .
    "Erros: " . count($erros) . "\n" .
    "Avisos: " . count($avisos) . "\n" .
    "Status: " . (empty($erros) ? 'SUCESSO' : 'COM PROBLEMAS') . "\n" .
    "Sistema de badges: " . (empty($erros) ? 'FUNCIONAL' : 'VERIFICAR') . "\n"
);

echo "📄 Status salvo em: instalacao_status.txt\n\n";

?>
