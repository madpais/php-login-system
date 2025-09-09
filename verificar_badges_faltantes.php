<?php
/**
 * Verificar quais badges estão sendo procuradas pelas funções mas não existem no banco
 */

require_once 'config.php';

echo "🔍 VERIFICAÇÃO DE BADGES FALTANTES\n";
echo "==================================\n\n";

try {
    $pdo = conectarBD();
    
    // Badges que as funções procuram
    $badges_esperadas = [
        // Badges de provas (sistema_badges.php)
        'prova_bronze', 'prova_prata', 'prova_ouro', 'prova_rubi', 'prova_diamante',
        
        // Badges de fórum (sistema_badges.php)
        'forum_bronze', 'forum_prata', 'forum_ouro', 'forum_rubi', 'forum_diamante',
        
        // Badges de GPA (sistema_badges.php)
        'gpa_bronze', 'gpa_prata', 'gpa_ouro', 'gpa_rubi', 'gpa_diamante',
        
        // Badges de países (sistema_badges.php)
        'paises_bronze', 'paises_prata', 'paises_ouro', 'paises_rubi', 'paises_diamante',
        
        // Badges do BadgesManager
        'iniciante', 'experiente', 'veterano', 'mestre', 'lenda',
        'especialista_sat', 'especialista_enem', 'especialista_vestibular',
        'consistente', 'dedicado', 'maratonista', 'persistente',
        'rapido', 'eficiente', 'perfeccionista'
    ];
    
    echo "📋 BADGES ESPERADAS PELAS FUNÇÕES:\n";
    echo "==================================\n";
    
    $badges_existentes = [];
    $badges_faltantes = [];
    
    // Verificar quais badges existem
    $stmt = $pdo->query("SELECT codigo FROM badges WHERE ativa = 1");
    $badges_db = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($badges_esperadas as $badge_codigo) {
        if (in_array($badge_codigo, $badges_db)) {
            $badges_existentes[] = $badge_codigo;
            echo "✅ $badge_codigo: Existe\n";
        } else {
            $badges_faltantes[] = $badge_codigo;
            echo "❌ $badge_codigo: FALTANDO\n";
        }
    }
    
    echo "\n📊 RESUMO:\n";
    echo "==========\n";
    echo "✅ Badges existentes: " . count($badges_existentes) . "\n";
    echo "❌ Badges faltantes: " . count($badges_faltantes) . "\n";
    
    if (!empty($badges_faltantes)) {
        echo "\n🔧 BADGES QUE PRECISAM SER CRIADAS:\n";
        echo "===================================\n";
        foreach ($badges_faltantes as $badge) {
            echo "   - $badge\n";
        }
        
        echo "\n💡 SOLUÇÃO: Execute o script de inserção completa de badges\n";
        echo "   php inserir_badges_completas.php\n";
    }
    
    // Verificar badges extras no banco que não são usadas pelas funções
    echo "\n📋 BADGES NO BANCO NÃO USADAS PELAS FUNÇÕES:\n";
    echo "============================================\n";
    
    $badges_extras = array_diff($badges_db, $badges_esperadas);
    if (!empty($badges_extras)) {
        foreach ($badges_extras as $badge) {
            echo "⚠️ $badge: Existe no banco mas não é usada pelas funções\n";
        }
    } else {
        echo "✅ Todas as badges no banco são usadas pelas funções\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n🎯 VERIFICAÇÃO CONCLUÍDA!\n";
?>
