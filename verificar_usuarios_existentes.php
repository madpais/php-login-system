<?php
/**
 * Script para verificar e atribuir badges de países para usuários existentes
 */

require_once 'config.php';
require_once 'sistema_badges.php';

echo "🔍 VERIFICANDO BADGES DE PAÍSES PARA USUÁRIOS EXISTENTES\n";
echo "===================================================\n\n";

try {
    $pdo = conectarBD();
    
    // Buscar todos os usuários
    $stmt = $pdo->query("SELECT id, nome FROM usuarios");
    $usuarios = $stmt->fetchAll();
    
    if (empty($usuarios)) {
        echo "❌ Nenhum usuário encontrado\n";
        exit;
    }
    
    echo "👥 Total de usuários: " . count($usuarios) . "\n\n";
    
    foreach ($usuarios as $usuario) {
        $usuario_id = $usuario['id'];
        $nome = $usuario['nome'];
        
        echo "👤 Verificando usuário: $nome (ID: $usuario_id)\n";
        
        // Contar países visitados
        $stmt = $pdo->prepare("SELECT COUNT(DISTINCT pais_codigo) as total FROM paises_visitados WHERE usuario_id = ?");
        $stmt->execute([$usuario_id]);
        $resultado = $stmt->fetch();
        $total_paises = $resultado ? $resultado['total'] : 0;
        
        echo "   🌍 Total de países visitados: $total_paises\n";
        
        if ($total_paises == 0) {
            echo "   ℹ️ Nenhum país visitado, pulando...\n\n";
            continue;
        }
        
        // Verificar badges existentes
        $stmt = $pdo->prepare("
            SELECT b.codigo
            FROM usuario_badges ub
            JOIN badges b ON ub.badge_id = b.id
            WHERE ub.usuario_id = ? AND b.codigo LIKE 'paises_%'
        ");
        $stmt->execute([$usuario_id]);
        $badges = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "   🏆 Badges de países já atribuídas: " . (empty($badges) ? "Nenhuma" : implode(", ", $badges)) . "\n";
        
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
        
        if (!$badge_esperada) {
            echo "   ℹ️ Não atingiu o mínimo para nenhuma badge (5 países)\n\n";
            continue;
        }
        
        echo "   🎯 Badge esperada: $badge_esperada\n";
        
        // Verificar se já tem a badge esperada
        if (in_array($badge_esperada, $badges)) {
            echo "   ✅ Já possui a badge esperada\n\n";
            continue;
        }
        
        // Forçar verificação de badges
        echo "   🔄 Forçando verificação de badges...\n";
        $resultado = verificarBadgesPaises($usuario_id);
        
        if ($resultado) {
            echo "   ✅ Badge atribuída com sucesso!\n";
        } else {
            echo "   ❌ Falha ao atribuir badge\n";
        }
        
        // Verificar novamente as badges
        $stmt->execute([$usuario_id]);
        $badges_apos = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "   🏆 Badges após verificação: " . (empty($badges_apos) ? "Nenhuma" : implode(", ", $badges_apos)) . "\n\n";
    }
    
    echo "\n✅ VERIFICAÇÃO CONCLUÍDA!\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>