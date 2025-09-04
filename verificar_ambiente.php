<?php
/**
 * Script de Verificação de Ambiente
 * 
 * Este script verifica se o ambiente está pronto para executar
 * o Sistema DayDreamming, identificando possíveis problemas
 * antes da instalação.
 * 
 * Versão: 1.0.0
 * Data: 2025-01-13
 * Autor: Sistema DayDreamming
 */

echo "\n🔍 VERIFICAÇÃO DE AMBIENTE - SISTEMA DAYDREAMMING\n";
echo "================================================\n\n";

$problemas = [];
$avisos = [];
$sucesso = [];

// Verificar versão do PHP
echo "📋 VERIFICANDO PHP...\n";
echo "-------------------\n";

$phpVersion = phpversion();
echo "✓ Versão do PHP: $phpVersion\n";

if (version_compare($phpVersion, '7.4.0', '<')) {
    $problemas[] = "PHP 7.4+ é necessário. Versão atual: $phpVersion";
} else {
    $sucesso[] = "Versão do PHP compatível";
}

// Verificar extensões necessárias
echo "\n📦 VERIFICANDO EXTENSÕES PHP...\n";
echo "-------------------------------\n";

$extensoes_necessarias = ['pdo', 'pdo_mysql', 'mbstring', 'json', 'session'];

foreach ($extensoes_necessarias as $extensao) {
    if (extension_loaded($extensao)) {
        echo "✓ $extensao: Instalada\n";
        $sucesso[] = "Extensão $extensao disponível";
    } else {
        echo "❌ $extensao: NÃO INSTALADA\n";
        $problemas[] = "Extensão PHP '$extensao' não está instalada";
    }
}

// Verificar configurações PHP importantes
echo "\n⚙️ VERIFICANDO CONFIGURAÇÕES PHP...\n";
echo "-----------------------------------\n";

$upload_max = ini_get('upload_max_filesize');
echo "✓ Upload máximo: $upload_max\n";

$post_max = ini_get('post_max_size');
echo "✓ POST máximo: $post_max\n";

$memory_limit = ini_get('memory_limit');
echo "✓ Limite de memória: $memory_limit\n";

$max_execution = ini_get('max_execution_time');
echo "✓ Tempo máximo execução: {$max_execution}s\n";

// Verificar se o arquivo config.php existe
echo "\n📄 VERIFICANDO ARQUIVOS DE CONFIGURAÇÃO...\n";
echo "------------------------------------------\n";

if (file_exists('config.php')) {
    echo "✓ config.php: Encontrado\n";
    $sucesso[] = "Arquivo config.php existe";
    
    // Tentar incluir o config.php para verificar sintaxe
    try {
        include_once 'config.php';
        echo "✓ config.php: Sintaxe válida\n";
        $sucesso[] = "Arquivo config.php tem sintaxe válida";
        
        // Verificar se as constantes estão definidas
        $constantes_necessarias = ['DB_HOST', 'DB_USER', 'DB_PASS', 'DB_NAME'];
        foreach ($constantes_necessarias as $constante) {
            if (defined($constante)) {
                echo "✓ $constante: Definida\n";
            } else {
                echo "❌ $constante: NÃO DEFINIDA\n";
                $problemas[] = "Constante '$constante' não está definida em config.php";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ config.php: Erro de sintaxe - " . $e->getMessage() . "\n";
        $problemas[] = "Erro de sintaxe em config.php: " . $e->getMessage();
    }
    
} else {
    echo "❌ config.php: NÃO ENCONTRADO\n";
    $problemas[] = "Arquivo config.php não encontrado";
    
    if (file_exists('config.exemplo.php')) {
        echo "💡 config.exemplo.php: Encontrado (copie para config.php)\n";
        $avisos[] = "Copie config.exemplo.php para config.php e configure suas credenciais";
    } else {
        echo "❌ config.exemplo.php: NÃO ENCONTRADO\n";
        $problemas[] = "Arquivo config.exemplo.php também não encontrado";
    }
}

// Verificar conexão com MySQL (se config.php existir)
if (file_exists('config.php') && defined('DB_HOST')) {
    echo "\n🗄️ VERIFICANDO CONEXÃO COM MYSQL...\n";
    echo "-----------------------------------\n";
    
    try {
        // Tentar conectar sem especificar banco
        $dsn = "mysql:host=" . DB_HOST . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        echo "✓ Conexão MySQL: Sucesso\n";
        $sucesso[] = "Conexão com MySQL estabelecida";
        
        // Verificar se o banco existe
        $stmt = $pdo->query("SHOW DATABASES LIKE '" . DB_NAME . "'");
        if ($stmt->rowCount() > 0) {
            echo "✓ Banco '" . DB_NAME . "': Existe\n";
            $sucesso[] = "Banco de dados existe";
            
            // Conectar ao banco específico
            $dsn_db = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo_db = new PDO($dsn_db, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            // Contar tabelas
            $stmt = $pdo_db->query("SHOW TABLES");
            $num_tabelas = $stmt->rowCount();
            echo "✓ Tabelas encontradas: $num_tabelas\n";
            
            if ($num_tabelas >= 23) {
                $sucesso[] = "Sistema já instalado com $num_tabelas tabelas";
            } else if ($num_tabelas > 0) {
                $avisos[] = "Banco parcialmente instalado ($num_tabelas tabelas). Execute setup_database.php";
            } else {
                $avisos[] = "Banco vazio. Execute setup_database.php para instalar";
            }
            
        } else {
            echo "❌ Banco '" . DB_NAME . "': NÃO EXISTE\n";
            $avisos[] = "Banco '" . DB_NAME . "' não existe. Será criado durante a instalação";
        }
        
    } catch (PDOException $e) {
        echo "❌ Conexão MySQL: FALHOU - " . $e->getMessage() . "\n";
        $problemas[] = "Não foi possível conectar ao MySQL: " . $e->getMessage();
    }
}

// Verificar arquivos essenciais
echo "\n📁 VERIFICANDO ARQUIVOS ESSENCIAIS...\n";
echo "------------------------------------\n";

$arquivos_essenciais = [
    'index.php' => 'Página inicial',
    'login.php' => 'Sistema de login',
    'setup_database.php' => 'Script de instalação',
    'instalar_sistema_limpo.php' => 'Instalação limpa',
    'config.exemplo.php' => 'Exemplo de configuração'
];

foreach ($arquivos_essenciais as $arquivo => $descricao) {
    if (file_exists($arquivo)) {
        echo "✓ $arquivo: Encontrado ($descricao)\n";
        $sucesso[] = "Arquivo $arquivo disponível";
    } else {
        echo "❌ $arquivo: NÃO ENCONTRADO ($descricao)\n";
        $problemas[] = "Arquivo essencial '$arquivo' não encontrado";
    }
}

// Verificar permissões de escrita (se necessário)
echo "\n🔐 VERIFICANDO PERMISSÕES...\n";
echo "---------------------------\n";

$diretorios_escrita = ['logs', 'uploads', 'cache'];
foreach ($diretorios_escrita as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "✓ $dir/: Permissão de escrita OK\n";
            $sucesso[] = "Diretório $dir tem permissão de escrita";
        } else {
            echo "⚠️ $dir/: SEM permissão de escrita\n";
            $avisos[] = "Diretório $dir sem permissão de escrita (pode causar problemas futuros)";
        }
    } else {
        echo "ℹ️ $dir/: Diretório não existe (será criado se necessário)\n";
    }
}

// Resumo final
echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 RESUMO DA VERIFICAÇÃO\n";
echo str_repeat("=", 50) . "\n\n";

if (empty($problemas)) {
    echo "🎉 AMBIENTE PRONTO!\n";
    echo "✅ Nenhum problema crítico encontrado\n\n";
    
    if (!empty($avisos)) {
        echo "⚠️ AVISOS (" . count($avisos) . "):";
        foreach ($avisos as $i => $aviso) {
            echo "\n   " . ($i + 1) . ". $aviso";
        }
        echo "\n\n";
    }
    
    echo "🚀 PRÓXIMOS PASSOS:\n";
    echo "   1. Execute: php setup_database.php\n";
    echo "   2. Inicie o servidor: php -S localhost:8080\n";
    echo "   3. Acesse: http://localhost:8080\n";
    echo "   4. Login: admin / admin123\n\n";
    
} else {
    echo "❌ PROBLEMAS ENCONTRADOS (" . count($problemas) . "):";
    foreach ($problemas as $i => $problema) {
        echo "\n   " . ($i + 1) . ". $problema";
    }
    echo "\n\n";
    
    if (!empty($avisos)) {
        echo "⚠️ AVISOS ADICIONAIS (" . count($avisos) . "):";
        foreach ($avisos as $i => $aviso) {
            echo "\n   " . ($i + 1) . ". $aviso";
        }
        echo "\n\n";
    }
    
    echo "🔧 CORRIJA OS PROBLEMAS ANTES DE CONTINUAR\n\n";
}

echo "✅ ITENS OK (" . count($sucesso) . "):";
foreach ($sucesso as $i => $item) {
    echo "\n   " . ($i + 1) . ". $item";
}

echo "\n\n📚 Para mais informações, consulte README_INSTALACAO.md\n";
echo "🌐 Sistema DayDreamming - Verificação de Ambiente Concluída\n\n";

?>