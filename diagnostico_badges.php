<?php
/**
 * Diagnóstico do Sistema de Badges
 * Verifica por que o sistema de badges não funciona em outros computadores
 */

require_once 'config.php';
iniciarSessaoSegura();

echo "🔍 DIAGNÓSTICO DO SISTEMA DE BADGES\n";
echo "===================================\n\n";

// 1. Verificar conexão com banco de dados
echo "📋 1. VERIFICAÇÃO DE CONEXÃO:\n";
echo "=============================\n";

try {
    $pdo = conectarBD();
    echo "✅ Conexão com banco de dados: OK\n";
    echo "Database: " . DB_NAME . "\n";
    echo "Host: " . DB_HOST . "\n";
} catch (Exception $e) {
    echo "❌ Erro de conexão: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. Verificar se as tabelas existem
echo "\n📋 2. VERIFICAÇÃO DE TABELAS:\n";
echo "=============================\n";

$tabelas_necessarias = ['badges', 'usuario_badges', 'usuarios'];

foreach ($tabelas_necessarias as $tabela) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE '$tabela'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Tabela '$tabela': Existe\n";
            
            // Verificar estrutura da tabela
            $stmt = $pdo->query("DESCRIBE $tabela");
            $colunas = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "   Colunas: " . implode(', ', $colunas) . "\n";
            
            // Contar registros
            $stmt = $pdo->query("SELECT COUNT(*) FROM $tabela");
            $count = $stmt->fetchColumn();
            echo "   Registros: $count\n";
        } else {
            echo "❌ Tabela '$tabela': NÃO EXISTE\n";
        }
    } catch (Exception $e) {
        echo "❌ Erro ao verificar tabela '$tabela': " . $e->getMessage() . "\n";
    }
}

// 3. Verificar se há badges cadastradas
echo "\n📋 3. VERIFICAÇÃO DE BADGES:\n";
echo "===========================\n";

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM badges WHERE ativa = 1");
    $badges_ativas = $stmt->fetchColumn();
    
    if ($badges_ativas > 0) {
        echo "✅ Badges ativas: $badges_ativas\n";
        
        // Listar algumas badges
        $stmt = $pdo->query("SELECT codigo, nome, tipo, categoria FROM badges WHERE ativa = 1 LIMIT 5");
        $badges = $stmt->fetchAll();
        
        echo "\n📝 Exemplos de badges:\n";
        foreach ($badges as $badge) {
            echo "   - {$badge['codigo']}: {$badge['nome']} ({$badge['tipo']}/{$badge['categoria']})\n";
        }
    } else {
        echo "❌ Nenhuma badge ativa encontrada\n";
        echo "\n🔧 SOLUÇÃO: Execute o script de inserção de badges:\n";
        echo "   php inserir_badges.php\n";
    }
} catch (Exception $e) {
    echo "❌ Erro ao verificar badges: " . $e->getMessage() . "\n";
}

// 4. Verificar arquivos do sistema de badges
echo "\n📋 4. VERIFICAÇÃO DE ARQUIVOS:\n";
echo "==============================\n";

$arquivos_badges = [
    'badges_manager.php' => 'Gerenciador principal de badges',
    'sistema_badges.php' => 'Sistema de badges auxiliar',
    'inserir_badges.php' => 'Script de inserção de badges'
];

foreach ($arquivos_badges as $arquivo => $descricao) {
    if (file_exists($arquivo)) {
        echo "✅ $arquivo: Existe ($descricao)\n";
        
        // Verificar se o arquivo tem conteúdo
        $tamanho = filesize($arquivo);
        echo "   Tamanho: " . number_format($tamanho) . " bytes\n";
    } else {
        echo "❌ $arquivo: NÃO EXISTE ($descricao)\n";
    }
}

// 5. Testar funcionalidades básicas
echo "\n📋 5. TESTE DE FUNCIONALIDADES:\n";
echo "===============================\n";

// Verificar se as classes/funções existem
if (class_exists('BadgesManager')) {
    echo "✅ Classe BadgesManager: Disponível\n";
    
    try {
        $badges_manager = new BadgesManager();
        echo "✅ Instância BadgesManager: Criada com sucesso\n";
    } catch (Exception $e) {
        echo "❌ Erro ao criar BadgesManager: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Classe BadgesManager: NÃO DISPONÍVEL\n";
    echo "   Verifique se badges_manager.php está sendo incluído\n";
}

// Verificar funções do sistema_badges.php
if (function_exists('verificarBadgesProvas')) {
    echo "✅ Função verificarBadgesProvas: Disponível\n";
} else {
    echo "❌ Função verificarBadgesProvas: NÃO DISPONÍVEL\n";
    echo "   Verifique se sistema_badges.php está sendo incluído\n";
}

// 6. Verificar usuários para teste
echo "\n📋 6. VERIFICAÇÃO DE USUÁRIOS:\n";
echo "==============================\n";

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE ativo = 1");
    $usuarios_ativos = $stmt->fetchColumn();
    
    if ($usuarios_ativos > 0) {
        echo "✅ Usuários ativos: $usuarios_ativos\n";
        
        // Verificar se algum usuário tem badges
        $stmt = $pdo->query("SELECT COUNT(*) FROM usuario_badges");
        $badges_conquistadas = $stmt->fetchColumn();
        echo "✅ Badges conquistadas no total: $badges_conquistadas\n";
        
        if ($badges_conquistadas == 0) {
            echo "\n⚠️ AVISO: Nenhuma badge foi conquistada ainda\n";
            echo "   Isso é normal em instalações novas\n";
        }
    } else {
        echo "❌ Nenhum usuário ativo encontrado\n";
    }
} catch (Exception $e) {
    echo "❌ Erro ao verificar usuários: " . $e->getMessage() . "\n";
}

// 7. Verificar configurações específicas
echo "\n📋 7. VERIFICAÇÕES ESPECÍFICAS:\n";
echo "===============================\n";

// Verificar se DEBUG_MODE está ativo
if (defined('DEBUG_MODE') && DEBUG_MODE) {
    echo "✅ DEBUG_MODE: Ativo (bom para diagnóstico)\n";
} else {
    echo "⚠️ DEBUG_MODE: Inativo\n";
}

// Verificar permissões de escrita (para logs)
if (is_writable('.')) {
    echo "✅ Permissões de escrita: OK\n";
} else {
    echo "❌ Permissões de escrita: Limitadas\n";
}

// 8. Resumo e recomendações
echo "\n📋 8. RESUMO E RECOMENDAÇÕES:\n";
echo "==============================\n";

echo "\n🔧 PASSOS PARA CORRIGIR PROBLEMAS:\n";
echo "\n1. Se as tabelas não existem:\n";
echo "   php criar_tabelas.php\n";
echo "\n2. Se não há badges cadastradas:\n";
echo "   php inserir_badges.php\n";
echo "\n3. Se há erros de conexão:\n";
echo "   - Verifique config.php\n";
echo "   - Confirme que o MySQL está rodando\n";
echo "   - Verifique credenciais do banco\n";
echo "\n4. Para instalação completa:\n";
echo "   php instalar_completo.php\n";

echo "\n✅ Diagnóstico concluído!\n";
?>