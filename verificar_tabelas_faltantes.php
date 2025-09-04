<?php
/**
 * Verificar tabelas que estão faltando no sistema
 */

require_once 'config.php';

echo "🔍 VERIFICAÇÃO DE TABELAS FALTANTES\n";
echo "===================================\n\n";

try {
    $pdo = conectarBD();
    
    $tabelas_verificar = [
        'forum_topicos' => 'Tópicos do fórum',
        'forum_posts' => 'Posts do fórum (pode ser forum_respostas)',
        'forum_categorias' => 'Categorias do fórum',
        'forum_respostas' => 'Respostas do fórum',
        'notificacoes' => 'Sistema de notificações',
        'paises' => 'Lista de países',
        'usuario_paises' => 'Países visitados pelos usuários',
        'simulados' => 'Sistema de simulados',
        'questoes' => 'Banco de questões'
    ];
    
    $tabelas_existentes = [];
    $tabelas_faltantes = [];
    
    foreach ($tabelas_verificar as $tabela => $descricao) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$tabela'");
        if ($stmt->rowCount() > 0) {
            echo "✅ $tabela: Existe - $descricao\n";
            $tabelas_existentes[] = $tabela;
            
            // Contar registros
            $stmt = $pdo->query("SELECT COUNT(*) FROM $tabela");
            $count = $stmt->fetchColumn();
            echo "   Registros: $count\n";
        } else {
            echo "❌ $tabela: NÃO EXISTE - $descricao\n";
            $tabelas_faltantes[] = $tabela;
        }
    }
    
    echo "\n📊 RESUMO:\n";
    echo "==========\n";
    echo "Tabelas existentes: " . count($tabelas_existentes) . "\n";
    echo "Tabelas faltantes: " . count($tabelas_faltantes) . "\n";
    
    if (count($tabelas_faltantes) > 0) {
        echo "\n❌ TABELAS FALTANTES:\n";
        foreach ($tabelas_faltantes as $tabela) {
            echo "   - $tabela\n";
        }
        
        echo "\n🔧 SOLUÇÕES:\n";
        echo "1. Execute: php instalar_completo.php\n";
        echo "2. Execute: php setup_database.php\n";
        echo "3. Verifique se há scripts específicos para criar essas tabelas\n";
    }
    
    // Verificar se há tabelas com nomes similares
    echo "\n🔍 VERIFICANDO TABELAS SIMILARES:\n";
    echo "=================================\n";
    
    $stmt = $pdo->query("SHOW TABLES");
    $todas_tabelas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tabelas_faltantes as $tabela_faltante) {
        echo "\nProcurando por '$tabela_faltante':\n";
        foreach ($todas_tabelas as $tabela_existente) {
            if (stripos($tabela_existente, str_replace('_', '', $tabela_faltante)) !== false ||
                stripos($tabela_faltante, str_replace('_', '', $tabela_existente)) !== false) {
                echo "   Possível equivalente: $tabela_existente\n";
            }
        }
    }
    
    echo "\n📋 TODAS AS TABELAS NO BANCO:\n";
    echo "=============================\n";
    foreach ($todas_tabelas as $tabela) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM $tabela");
        $count = $stmt->fetchColumn();
        echo "   $tabela ($count registros)\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n✅ Verificação concluída!\n";
?>