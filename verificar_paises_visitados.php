<?php
/**
 * Script para verificar registros de países visitados
 */

require_once 'config.php';

echo "🔍 VERIFICANDO REGISTROS DE PAÍSES VISITADOS\n";
echo "========================================\n\n";

try {
    $pdo = conectarBD();
    
    // Verificar se a tabela existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'paises_visitados'");
    if ($stmt->rowCount() == 0) {
        echo "❌ Tabela 'paises_visitados' não existe\n";
        exit;
    }
    
    // Contar registros
    $stmt = $pdo->query("SELECT COUNT(*) FROM paises_visitados");
    $total = $stmt->fetchColumn();
    echo "📊 Total de registros na tabela: $total\n\n";
    
    if ($total == 0) {
        echo "❌ Nenhum registro encontrado na tabela paises_visitados\n";
        echo "\n🔧 POSSÍVEL CAUSA DO PROBLEMA:\n";
        echo "A badge de países não está sendo atribuída porque não há registros de países visitados.\n";
        exit;
    }
    
    // Listar registros
    $stmt = $pdo->query("SELECT * FROM paises_visitados ORDER BY usuario_id, pais_codigo");
    $registros = $stmt->fetchAll();
    
    echo "📋 REGISTROS ENCONTRADOS:\n";
    echo "------------------------\n";
    foreach ($registros as $r) {
        echo "ID: {$r['id']} | Usuário: {$r['usuario_id']} | País: {$r['pais_codigo']} | "
           . "Nome: {$r['pais_nome']} | Visitas: {$r['total_visitas']} | "
           . "Primeira visita: {$r['data_primeira_visita']} | Última visita: {$r['ultima_visita']}\n";
    }
    
    echo "\n🔍 VERIFICANDO BADGES DE PAÍSES ATRIBUÍDAS:\n";
    echo "------------------------------------------\n";
    
    // Verificar badges de países atribuídas
    $stmt = $pdo->query("
        SELECT ub.usuario_id, u.nome, b.codigo, b.nome as badge_nome, ub.data_conquista, ub.contexto
        FROM usuario_badges ub
        JOIN badges b ON ub.badge_id = b.id
        JOIN usuarios u ON ub.usuario_id = u.id
        WHERE b.codigo LIKE 'paises_%'
        ORDER BY ub.usuario_id, ub.data_conquista
    ");
    
    $badges = $stmt->fetchAll();
    
    if (empty($badges)) {
        echo "❌ Nenhuma badge de países atribuída a nenhum usuário\n";
    } else {
        foreach ($badges as $b) {
            echo "Usuário: {$b['nome']} (ID: {$b['usuario_id']}) | Badge: {$b['badge_nome']} ({$b['codigo']}) | "
                . "Data: {$b['data_conquista']} | Contexto: {$b['contexto']}\n";
        }
    }
    
    echo "\n🔧 VERIFICANDO FUNÇÃO DE ATRIBUIÇÃO DE BADGES:\n";
    echo "----------------------------------------------\n";
    
    // Verificar se a função está sendo chamada
    $stmt = $pdo->query("SELECT DISTINCT usuario_id FROM paises_visitados");
    $usuarios = $stmt->fetchAll();
    
    foreach ($usuarios as $u) {
        $usuario_id = $u['usuario_id'];
        
        // Contar países visitados por este usuário
        $stmt = $pdo->prepare("SELECT COUNT(DISTINCT pais_codigo) as total FROM paises_visitados WHERE usuario_id = ?");
        $stmt->execute([$usuario_id]);
        $total_paises = $stmt->fetch()['total'];
        
        // Obter nome do usuário
        $stmt = $pdo->prepare("SELECT nome FROM usuarios WHERE id = ?");
        $stmt->execute([$usuario_id]);
        $nome_usuario = $stmt->fetch()['nome'];
        
        echo "Usuário: $nome_usuario (ID: $usuario_id) | Países visitados: $total_paises\n";
        
        // Verificar qual badge deveria ter
        $badge_esperada = null;
        if ($total_paises >= 28) {
            $badge_esperada = 'paises_diamante';
        } elseif ($total_paises >= 20) {
            $badge_esperada = 'paises_rubi';
        } elseif ($total_paises >= 15) {
            $badge_esperada = 'paises_ouro';
        } elseif ($total_paises >= 10) {
            $badge_esperada = 'paises_prata';
        } elseif ($total_paises >= 5) {
            $badge_esperada = 'paises_bronze';
        }
        
        if ($badge_esperada) {
            echo "  ✓ Deveria ter a badge: $badge_esperada\n";
            
            // Verificar se tem a badge
            $stmt = $pdo->prepare("
                SELECT ub.id 
                FROM usuario_badges ub
                JOIN badges b ON ub.badge_id = b.id
                WHERE ub.usuario_id = ? AND b.codigo = ?
            ");
            $stmt->execute([$usuario_id, $badge_esperada]);
            
            if ($stmt->rowCount() > 0) {
                echo "  ✅ Badge $badge_esperada já atribuída corretamente\n";
            } else {
                echo "  ❌ Badge $badge_esperada NÃO atribuída (PROBLEMA ENCONTRADO)\n";
            }
        } else {
            echo "  ℹ️ Não tem países suficientes para receber uma badge\n";
        }
    }
    
    echo "\n🔍 VERIFICANDO CÓDIGO DA FUNÇÃO verificarBadgesPaises():\n";
    echo "------------------------------------------------------\n";
    
    // Verificar se a função está sendo chamada após registrar visita
    $tracking_content = file_get_contents('tracking_paises.php');
    
    if (strpos($tracking_content, 'verificarBadgesPaises') !== false) {
        echo "✅ Função verificarBadgesPaises() está sendo chamada no arquivo tracking_paises.php\n";
        
        // Verificar se está sendo chamada após registrar visita
        if (strpos($tracking_content, '// Verificar badges de países após registrar visita') !== false) {
            echo "✅ Função está sendo chamada após registrar visita\n";
        } else {
            echo "❌ Função NÃO está sendo chamada após registrar visita (PROBLEMA ENCONTRADO)\n";
        }
    } else {
        echo "❌ Função verificarBadgesPaises() NÃO está sendo chamada no arquivo tracking_paises.php (PROBLEMA ENCONTRADO)\n";
    }
    
    echo "\n🔧 DIAGNÓSTICO FINAL:\n";
    echo "====================\n";
    
    if ($total == 0) {
        echo "❌ Não há registros de países visitados. Visite alguns países primeiro.\n";
    } elseif (empty($badges)) {
        echo "❌ Há países visitados, mas nenhuma badge foi atribuída.\n";
        echo "   Possíveis causas:\n";
        echo "   1. A função verificarBadgesPaises() não está sendo chamada após registrar visita\n";
        echo "   2. A função atribuirBadge() não está funcionando corretamente\n";
    } else {
        echo "✅ O sistema de badges de países parece estar funcionando corretamente.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>