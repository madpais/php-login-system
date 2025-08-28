<?php
/**
 * Correção rápida da tabela sessoes_teste
 */

require_once 'config.php';

echo "🔧 CORRIGINDO TABELA SESSOES_TESTE\n";
echo "==================================\n\n";

try {
    $pdo = conectarBD();
    
    // Verificar estrutura atual
    $stmt = $pdo->query("DESCRIBE sessoes_teste");
    $colunas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Colunas atuais: " . implode(', ', $colunas) . "\n\n";
    
    // Adicionar campo data_inicio se não existir
    if (!in_array('data_inicio', $colunas)) {
        echo "➕ Adicionando campo data_inicio...\n";
        $pdo->exec("ALTER TABLE sessoes_teste ADD COLUMN data_inicio DATETIME DEFAULT CURRENT_TIMESTAMP");
        echo "✅ Campo data_inicio adicionado\n";
    } else {
        echo "✅ Campo data_inicio já existe\n";
    }
    
    // Atualizar registros existentes
    $pdo->exec("UPDATE sessoes_teste SET data_inicio = data_fim WHERE data_inicio IS NULL");
    echo "✅ Registros existentes atualizados\n";
    
    // Testar query corrigida
    echo "\n🧪 Testando query corrigida:\n";
    $stmt = $pdo->query("
        SELECT tipo_prova, status, pontuacao_final, data_inicio
        FROM sessoes_teste
        ORDER BY data_inicio DESC
        LIMIT 3
    ");
    $sessoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "✅ Query funcionando - " . count($sessoes) . " sessões encontradas:\n";
    foreach ($sessoes as $sessao) {
        echo "  - {$sessao['tipo_prova']}: {$sessao['status']} - " . 
             ($sessao['pontuacao_final'] ?? 'N/A') . "% - " . 
             date('d/m/Y H:i', strtotime($sessao['data_inicio'])) . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n✅ CORREÇÃO CONCLUÍDA!\n";

?>
