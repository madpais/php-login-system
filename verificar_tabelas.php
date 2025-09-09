<?php
require_once 'config.php';

try {
    $pdo = conectarBD();
    
    echo "📋 TABELAS NO BANCO DE DADOS:\n";
    echo "=============================\n";
    
    $stmt = $pdo->query("SHOW TABLES");
    $tabelas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tabelas as $tabela) {
        echo "- $tabela\n";
    }
    
    echo "\n🔍 PROCURANDO TABELAS DE RESULTADOS:\n";
    echo "====================================\n";
    
    $tabelas_resultado = array_filter($tabelas, function($tabela) {
        return strpos(strtolower($tabela), 'resultado') !== false || 
               strpos(strtolower($tabela), 'teste') !== false ||
               strpos(strtolower($tabela), 'prova') !== false;
    });
    
    if (!empty($tabelas_resultado)) {
        foreach ($tabelas_resultado as $tabela) {
            echo "✅ $tabela\n";
            
            // Mostrar estrutura
            $stmt = $pdo->query("DESCRIBE $tabela");
            $colunas = $stmt->fetchAll();
            
            echo "   Colunas:\n";
            foreach ($colunas as $coluna) {
                echo "   - {$coluna['Field']} ({$coluna['Type']})\n";
            }
            echo "\n";
        }
    } else {
        echo "❌ Nenhuma tabela de resultados encontrada\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>
