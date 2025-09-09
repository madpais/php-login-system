<?php
/**
 * Verificar estrutura das tabelas de badges e identificar inconsistências
 */

require_once 'config.php';

echo "🔍 VERIFICAÇÃO DA ESTRUTURA DAS TABELAS DE BADGES\n";
echo "=================================================\n\n";

try {
    $pdo = conectarBD();
    
    // Verificar se as tabelas existem
    echo "📋 1. VERIFICANDO EXISTÊNCIA DAS TABELAS:\n";
    echo "=========================================\n";
    
    $tabelas = ['badges', 'usuario_badges'];
    foreach ($tabelas as $tabela) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$tabela'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Tabela '$tabela': Existe\n";
        } else {
            echo "❌ Tabela '$tabela': NÃO EXISTE\n";
        }
    }
    
    // Verificar estrutura da tabela badges
    echo "\n📋 2. ESTRUTURA DA TABELA 'badges':\n";
    echo "===================================\n";
    
    try {
        $stmt = $pdo->query("DESCRIBE badges");
        $colunas_badges = $stmt->fetchAll();
        
        echo "Colunas encontradas:\n";
        foreach ($colunas_badges as $coluna) {
            echo "- {$coluna['Field']}: {$coluna['Type']} (Default: {$coluna['Default']})\n";
        }
        
        // Verificar se tem coluna 'ativa' ou 'ativo'
        $tem_ativa = false;
        $tem_ativo = false;
        foreach ($colunas_badges as $coluna) {
            if ($coluna['Field'] === 'ativa') $tem_ativa = true;
            if ($coluna['Field'] === 'ativo') $tem_ativo = true;
        }
        
        echo "\n🔍 Verificação de colunas de status:\n";
        echo "- Coluna 'ativa': " . ($tem_ativa ? "✅ Existe" : "❌ Não existe") . "\n";
        echo "- Coluna 'ativo': " . ($tem_ativo ? "✅ Existe" : "❌ Não existe") . "\n";
        
        if ($tem_ativa && $tem_ativo) {
            echo "⚠️ PROBLEMA: Ambas as colunas 'ativa' e 'ativo' existem!\n";
        } elseif (!$tem_ativa && !$tem_ativo) {
            echo "❌ PROBLEMA: Nenhuma coluna de status encontrada!\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Erro ao verificar estrutura da tabela badges: " . $e->getMessage() . "\n";
    }
    
    // Verificar estrutura da tabela usuario_badges
    echo "\n📋 3. ESTRUTURA DA TABELA 'usuario_badges':\n";
    echo "==========================================\n";
    
    try {
        $stmt = $pdo->query("DESCRIBE usuario_badges");
        $colunas_usuario_badges = $stmt->fetchAll();
        
        echo "Colunas encontradas:\n";
        foreach ($colunas_usuario_badges as $coluna) {
            echo "- {$coluna['Field']}: {$coluna['Type']} (Default: {$coluna['Default']})\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Erro ao verificar estrutura da tabela usuario_badges: " . $e->getMessage() . "\n";
    }
    
    // Verificar dados existentes
    echo "\n📋 4. DADOS EXISTENTES:\n";
    echo "======================\n";
    
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM badges");
        $total_badges = $stmt->fetchColumn();
        echo "📊 Total de badges: $total_badges\n";
        
        if ($total_badges > 0) {
            // Tentar com 'ativa'
            try {
                $stmt = $pdo->query("SELECT COUNT(*) FROM badges WHERE ativa = 1");
                $badges_ativas = $stmt->fetchColumn();
                echo "✅ Badges ativas (coluna 'ativa'): $badges_ativas\n";
            } catch (Exception $e) {
                echo "❌ Erro ao consultar coluna 'ativa': " . $e->getMessage() . "\n";
            }
            
            // Tentar com 'ativo'
            try {
                $stmt = $pdo->query("SELECT COUNT(*) FROM badges WHERE ativo = 1");
                $badges_ativo = $stmt->fetchColumn();
                echo "✅ Badges ativas (coluna 'ativo'): $badges_ativo\n";
            } catch (Exception $e) {
                echo "❌ Erro ao consultar coluna 'ativo': " . $e->getMessage() . "\n";
            }
        }
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM usuario_badges");
        $total_usuario_badges = $stmt->fetchColumn();
        echo "📊 Total de badges de usuários: $total_usuario_badges\n";
        
    } catch (Exception $e) {
        echo "❌ Erro ao verificar dados: " . $e->getMessage() . "\n";
    }
    
    // Verificar inconsistências nas funções
    echo "\n📋 5. VERIFICANDO INCONSISTÊNCIAS NAS FUNÇÕES:\n";
    echo "==============================================\n";
    
    // Verificar sistema_badges.php
    if (file_exists('sistema_badges.php')) {
        $conteudo = file_get_contents('sistema_badges.php');
        $usa_ativa = strpos($conteudo, 'ativa = 1') !== false;
        $usa_ativo = strpos($conteudo, 'ativo = 1') !== false;
        
        echo "📄 sistema_badges.php:\n";
        echo "- Usa 'ativa': " . ($usa_ativa ? "✅ Sim" : "❌ Não") . "\n";
        echo "- Usa 'ativo': " . ($usa_ativo ? "✅ Sim" : "❌ Não") . "\n";
    }
    
    // Verificar inserir_badges.php
    if (file_exists('inserir_badges.php')) {
        $conteudo = file_get_contents('inserir_badges.php');
        $usa_ativa = strpos($conteudo, 'ativa') !== false;
        $usa_ativo = strpos($conteudo, 'ativo') !== false;
        
        echo "📄 inserir_badges.php:\n";
        echo "- Usa 'ativa': " . ($usa_ativa ? "✅ Sim" : "❌ Não") . "\n";
        echo "- Usa 'ativo': " . ($usa_ativo ? "✅ Sim" : "❌ Não") . "\n";
    }
    
    echo "\n📋 6. RECOMENDAÇÕES:\n";
    echo "===================\n";
    
    if ($tem_ativa && !$tem_ativo) {
        echo "✅ Estrutura correta: usar 'ativa' em todas as funções\n";
    } elseif (!$tem_ativa && $tem_ativo) {
        echo "⚠️ Estrutura usa 'ativo': atualizar funções para usar 'ativo'\n";
    } elseif ($tem_ativa && $tem_ativo) {
        echo "❌ Problema: duas colunas de status. Remover uma delas.\n";
    } else {
        echo "❌ Problema: nenhuma coluna de status. Adicionar coluna 'ativa'.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro geral: " . $e->getMessage() . "\n";
}

echo "\n🎯 VERIFICAÇÃO CONCLUÍDA!\n";
?>
