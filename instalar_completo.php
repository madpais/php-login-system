<?php
/**
 * Script de Instalação Automatizada - Sistema DayDreamming
 * 
 * Este script automatiza completamente a instalação do sistema
 * para novos colaboradores, incluindo verificações, configuração
 * e inicialização do ambiente.
 * 
 * Versão: 1.0.0
 * Data: 2025-01-13
 * Autor: Sistema DayDreamming
 */

echo "\n🚀 INSTALAÇÃO AUTOMATIZADA - SISTEMA DAYDREAMMING\n";
echo "=================================================\n\n";

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
echo "📋 PASSO 1/7: Verificando ambiente...\n";
exibirProgresso(1, 7, "Verificando PHP e extensões");

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
echo "\n📋 PASSO 2/7: Configurando arquivo de configuração...\n";
exibirProgresso(2, 7, "Verificando config.php");

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
echo "\n📋 PASSO 3/7: Testando conexão com banco de dados...\n";
exibirProgresso(3, 7, "Conectando ao MySQL");

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
echo "\n📋 PASSO 4/7: Instalando estrutura do banco...\n";
exibirProgresso(4, 7, "Executando setup_database.php");

if (file_exists('setup_database.php')) {
    echo "\n🗄️ Executando instalação do banco...\n";
    
    // Capturar output do setup_database.php
    ob_start();
    try {
        include 'setup_database.php';
        $output = ob_get_contents();
        ob_end_clean();
        
        // Verificar se houve erros
        if (strpos($output, 'ERRO') !== false || strpos($output, 'ERROR') !== false) {
            echo "⚠️ Possíveis avisos durante instalação:\n";
            echo $output;
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

// PASSO 5: Verificar instalação
echo "\n📋 PASSO 5/7: Verificando instalação...\n";
exibirProgresso(5, 7, "Contando tabelas e dados");

try {
    $dsn_db = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo_db = new PDO($dsn_db, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Contar tabelas
    $stmt = $pdo_db->query("SHOW TABLES");
    $num_tabelas = $stmt->rowCount();
    
    echo "\n📊 Tabelas encontradas: $num_tabelas\n";
    
    if ($num_tabelas >= 23) {
        echo "✅ Todas as tabelas instaladas corretamente\n";
        
        // Verificar dados essenciais
        $stmt = $pdo_db->query("SELECT COUNT(*) FROM usuarios");
        $usuarios = $stmt->fetchColumn();
        
        $stmt = $pdo_db->query("SELECT COUNT(*) FROM badges");
        $badges = $stmt->fetchColumn();
        
        // Verificar questões SAT
        $stmt = $pdo_db->query("SELECT COUNT(*) FROM questoes WHERE tipo_prova = 'sat'");
        $questoes_sat = $stmt->fetchColumn();
        
        echo "👥 Usuários: $usuarios\n";
        echo "🏆 Badges: $badges\n";
        echo "📝 Questões SAT: $questoes_sat\n";
        
        if ($usuarios >= 2 && $badges >= 10) {
            echo "✅ Dados iniciais carregados\n";
            $passos_concluidos[] = "Sistema completamente instalado";
        } else {
            $avisos[] = "Poucos dados iniciais encontrados";
        }
        
    } else {
        $avisos[] = "Apenas $num_tabelas tabelas encontradas (esperado: 23+)";
    }
    
} catch (PDOException $e) {
    $erros[] = "Erro na verificação: " . $e->getMessage();
}

sleep(1);

// PASSO 6: Carregar questões do simulador
echo "\n📋 PASSO 6/7: Carregando questões do simulador...\n";
exibirProgresso(6, 7, "Carregando questões SAT");

echo "\n📚 Carregando questões do simulador SAT...\n";

if (file_exists('carregar_questoes_sat.php')) {
    // Capturar output do carregamento de questões
    ob_start();
    try {
        include 'carregar_questoes_sat.php';
        $output = ob_get_contents();
        ob_end_clean();
        
        // Verificar se houve erros
        if (strpos($output, 'ERRO') !== false || strpos($output, 'ERROR') !== false) {
            echo "⚠️ Possíveis avisos durante carregamento de questões:\n";
            echo $output;
            $avisos[] = "Verifique os logs de carregamento de questões";
        } else {
            echo "✅ Questões do simulador SAT carregadas com sucesso\n";
        }
        
        // Verificar questões carregadas
        if (file_exists('verificar_questoes_carregadas.php')) {
            echo "\n📊 Verificando questões carregadas...\n";
            ob_start();
            include 'verificar_questoes_carregadas.php';
            $output_verificacao = ob_get_contents();
            ob_end_clean();
            
            // Extrair apenas as informações essenciais
            if (preg_match('/Total de questões no banco: (\d+)/', $output_verificacao, $matches)) {
                echo "📝 Total de questões no banco: {$matches[1]}\n";
            }
            
            if (preg_match('/🎯 SAT: (\d+) questões/', $output_verificacao, $matches)) {
                echo "📝 Questões SAT carregadas: {$matches[1]}\n";
            }
        }
        
    } catch (Exception $e) {
        ob_end_clean();
        echo "⚠️ Aviso durante carregamento de questões: " . $e->getMessage() . "\n";
        $avisos[] = "Aviso no carregamento de questões: " . $e->getMessage();
    }
    
    $passos_concluidos[] = "Questões do simulador carregadas";
} else {
    echo "⚠️ Arquivo carregar_questoes_sat.php não encontrado\n";
    $avisos[] = "Arquivo carregar_questoes_sat.php não encontrado";
}

sleep(1);

// PASSO 7: Finalização
echo "\n📋 PASSO 7/7: Finalizando instalação...\n";
exibirProgresso(7, 7, "Preparando sistema");

// Verificar arquivos essenciais
$arquivos_essenciais = [
    'index.php' => 'Página inicial',
    'login.php' => 'Sistema de login',
    'forum.php' => 'Fórum',
    'pagina_usuario.php' => 'Dashboard do usuário'
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
    echo "4. 🧪 Execute verificar_ambiente.php para diagnósticos\n";
    echo "5. 📝 Acesse o simulador SAT: http://localhost:8080/simulador_provas.php\n";
    echo "6. 📚 Consulte README_INSTALACAO.md para mais detalhes\n\n";
    
    echo "🎯 FUNCIONALIDADES DISPONÍVEIS:\n";
    echo "===============================\n";
    echo "✅ Sistema de usuários e autenticação\n";
    echo "✅ Dashboard personalizado\n";
    echo "✅ Sistema de testes e simulador\n";
    echo "✅ Simulador SAT com questões carregadas\n";
    echo "✅ Fórum com categorias e moderação\n";
    echo "✅ Sistema de badges e gamificação\n";
    echo "✅ Páginas de países (28 países)\n";
    echo "✅ Sistema de notificações\n";
    echo "✅ Tracking de países visitados\n\n";
    
    echo "🔧 FERRAMENTAS DE DEBUG:\n";
    echo "========================\n";
    echo "• verificar_ambiente.php - Diagnóstico completo\n";
    echo "• teste_sistema_completo.php - Teste de funcionalidades\n";
    echo "• status_projeto.php - Status do projeto\n\n";
    
} else {
    echo "❌ INSTALAÇÃO COM PROBLEMAS\n\n";
    echo "🚨 ERROS ENCONTRADOS (" . count($erros) . "):";
    foreach ($erros as $i => $erro) {
        echo "\n   " . ($i + 1) . ". $erro";
    }
    echo "\n\n🔧 CORRIJA OS ERROS E EXECUTE NOVAMENTE\n";
}

echo "📞 SUPORTE:\n";
echo "===========\n";
echo "📖 README_INSTALACAO.md - Guia detalhado\n";
echo "🔍 verificar_ambiente.php - Diagnóstico\n";
echo "🌐 Sistema DayDreamming - Instalação Automatizada\n\n";

// Criar arquivo de status da instalação
file_put_contents('instalacao_status.txt', 
    "Instalação executada em: " . date('Y-m-d H:i:s') . "\n" .
    "Passos concluídos: " . count($passos_concluidos) . "\n" .
    "Erros: " . count($erros) . "\n" .
    "Avisos: " . count($avisos) . "\n" .
    "Status: " . (empty($erros) ? 'SUCESSO' : 'COM PROBLEMAS') . "\n"
);

echo "📄 Status salvo em: instalacao_status.txt\n\n";

?>