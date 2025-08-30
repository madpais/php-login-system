<?php
require_once 'config.php';

echo "🗑️ REMOÇÃO DE PAÍSES VISITADOS\n";
echo "==============================\n\n";

try {
    $pdo = conectarBD();
    
    // Primeiro, verificar estrutura da tabela
    echo "🔍 VERIFICANDO ESTRUTURA DA TABELA:\n";
    echo "====================================\n";
    $stmt = $pdo->query("DESCRIBE paises_visitados");
    $colunas = $stmt->fetchAll();
    foreach ($colunas as $col) {
        echo "Coluna: {$col['Field']} - Tipo: {$col['Type']}\n";
    }

    // Verificar registros existentes
    echo "\n📊 REGISTROS ATUAIS:\n";
    echo "====================\n";
    $stmt = $pdo->query("
        SELECT *
        FROM paises_visitados
        ORDER BY usuario_id, pais_codigo
    ");
    $registros = $stmt->fetchAll();

    if (empty($registros)) {
        echo "❌ Nenhum registro encontrado na tabela paises_visitados\n";
        exit;
    }

    foreach ($registros as $r) {
        $data_campo = isset($r['data_visita']) ? $r['data_visita'] : (isset($r['data_criacao']) ? $r['data_criacao'] : 'N/A');
        echo "ID: {$r['id']} | Usuário: {$r['usuario_id']} | País: {$r['pais_codigo']} | Data: {$data_campo}\n";
    }
    
    echo "\n🎯 REMOVENDO PAÍSES ESPECÍFICOS:\n";
    echo "=================================\n";
    
    // Remover registros da Índia
    $stmt = $pdo->prepare("DELETE FROM paises_visitados WHERE pais_codigo = 'india'");
    $stmt->execute();
    $removidos_india = $stmt->rowCount();
    echo "🇮🇳 Índia: $removidos_india registros removidos\n";
    
    // Remover registros da Austrália
    $stmt = $pdo->prepare("DELETE FROM paises_visitados WHERE pais_codigo = 'australia'");
    $stmt->execute();
    $removidos_australia = $stmt->rowCount();
    echo "🇦🇺 Austrália: $removidos_australia registros removidos\n";
    
    // Verificar registros restantes
    echo "\n📊 REGISTROS RESTANTES:\n";
    echo "=======================\n";
    $stmt = $pdo->query("
        SELECT *
        FROM paises_visitados
        ORDER BY usuario_id, pais_codigo
    ");
    $registros_restantes = $stmt->fetchAll();

    if (empty($registros_restantes)) {
        echo "✅ Nenhum registro restante - tabela limpa\n";
    } else {
        foreach ($registros_restantes as $r) {
            $data_campo = isset($r['data_visita']) ? $r['data_visita'] : (isset($r['data_criacao']) ? $r['data_criacao'] : 'N/A');
            echo "ID: {$r['id']} | Usuário: {$r['usuario_id']} | País: {$r['pais_codigo']} | Data: {$data_campo}\n";
        }
    }
    
    echo "\n✅ REMOÇÃO CONCLUÍDA!\n";
    echo "=====================\n";
    echo "🗑️ Total removido: " . ($removidos_india + $removidos_australia) . " registros\n";
    echo "📊 Registros restantes: " . count($registros_restantes) . "\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>
