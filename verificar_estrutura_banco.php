<?php
/**
 * Verificar estrutura completa do banco de dados
 */

require_once 'config.php';

echo "🔍 VERIFICANDO ESTRUTURA COMPLETA DO BANCO DE DADOS\n";
echo "===================================================\n\n";

try {
    $pdo = conectarBD();
    
    // 1. Listar todas as tabelas
    echo "📋 1. TABELAS EXISTENTES:\n";
    echo "=========================\n";
    
    $stmt = $pdo->query("SHOW TABLES");
    $tabelas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tabelas as $tabela) {
        echo "✅ $tabela\n";
    }
    
    echo "\n📊 Total de tabelas: " . count($tabelas) . "\n\n";
    
    // 2. Estrutura de cada tabela
    foreach ($tabelas as $tabela) {
        echo "📋 ESTRUTURA DA TABELA: $tabela\n";
        echo str_repeat("=", 30 + strlen($tabela)) . "\n";
        
        // Estrutura
        $stmt = $pdo->query("DESCRIBE $tabela");
        $campos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($campos as $campo) {
            echo sprintf("  %-20s %-20s %-10s %-10s %-10s %s\n", 
                $campo['Field'], 
                $campo['Type'], 
                $campo['Null'], 
                $campo['Key'], 
                $campo['Default'], 
                $campo['Extra']
            );
        }
        
        // Contar registros
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM $tabela");
        $total = $stmt->fetch();
        echo "\n📊 Total de registros: " . $total['total'] . "\n";
        
        // Mostrar alguns dados de exemplo
        if ($total['total'] > 0) {
            echo "📄 Dados de exemplo:\n";
            $stmt = $pdo->query("SELECT * FROM $tabela LIMIT 3");
            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($dados as $i => $linha) {
                echo "  Registro " . ($i + 1) . ":\n";
                foreach ($linha as $campo => $valor) {
                    $valor_exibir = strlen($valor) > 50 ? substr($valor, 0, 50) . '...' : $valor;
                    echo "    $campo: $valor_exibir\n";
                }
                echo "\n";
            }
        }
        
        echo "\n" . str_repeat("-", 80) . "\n\n";
    }
    
    // 3. Chaves estrangeiras
    echo "🔗 3. CHAVES ESTRANGEIRAS:\n";
    echo "==========================\n";
    
    $stmt = $pdo->query("
        SELECT 
            TABLE_NAME,
            COLUMN_NAME,
            CONSTRAINT_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM 
            INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE 
            REFERENCED_TABLE_SCHEMA = DATABASE()
            AND REFERENCED_TABLE_NAME IS NOT NULL
    ");
    
    $fks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($fks as $fk) {
        echo "  {$fk['TABLE_NAME']}.{$fk['COLUMN_NAME']} -> {$fk['REFERENCED_TABLE_NAME']}.{$fk['REFERENCED_COLUMN_NAME']}\n";
    }
    
    if (empty($fks)) {
        echo "  Nenhuma chave estrangeira encontrada\n";
    }
    
    // 4. Índices
    echo "\n📇 4. ÍNDICES:\n";
    echo "==============\n";
    
    foreach ($tabelas as $tabela) {
        $stmt = $pdo->query("SHOW INDEX FROM $tabela");
        $indices = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($indices)) {
            echo "\n$tabela:\n";
            foreach ($indices as $indice) {
                echo "  {$indice['Key_name']} ({$indice['Column_name']}) - " . 
                     ($indice['Non_unique'] ? 'INDEX' : 'UNIQUE') . "\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n🎯 RESUMO PARA SCRIPT DE INSTALAÇÃO:\n";
echo "====================================\n";
echo "📋 Tabelas a incluir: " . count($tabelas) . "\n";
echo "🔗 Relacionamentos a preservar\n";
echo "📊 Dados a migrar\n";
echo "📇 Índices a recriar\n";

?>
