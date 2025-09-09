<?php
/**
 * Teste das funções de badges para identificar problemas
 */

require_once 'config.php';

echo "🧪 TESTE DAS FUNÇÕES DE BADGES\n";
echo "==============================\n\n";

// Teste 1: Verificar se as funções estão disponíveis
echo "📋 1. VERIFICAÇÃO DE FUNÇÕES:\n";
echo "=============================\n";

$funcoes_badges = [
    'verificarBadgesProvas',
    'verificarBadgesForum', 
    'verificarBadgesGPA',
    'verificarBadgesPaises',
    'atribuirBadge',
    'verificarTodasBadges'
];

foreach ($funcoes_badges as $funcao) {
    if (function_exists($funcao)) {
        echo "✅ $funcao: Disponível\n";
    } else {
        echo "❌ $funcao: NÃO DISPONÍVEL\n";
    }
}

// Teste 2: Verificar se a classe BadgesManager está funcionando
echo "\n📋 2. VERIFICAÇÃO DA CLASSE BADGESMANAGER:\n";
echo "==========================================\n";

try {
    if (class_exists('BadgesManager')) {
        echo "✅ Classe BadgesManager: Disponível\n";
        
        $manager = new BadgesManager();
        echo "✅ Instância criada: Sucesso\n";
        
        // Testar método da classe
        if (method_exists($manager, 'verificarBadgesResultado')) {
            echo "✅ Método verificarBadgesResultado: Disponível\n";
        } else {
            echo "❌ Método verificarBadgesResultado: NÃO DISPONÍVEL\n";
        }
        
    } else {
        echo "❌ Classe BadgesManager: NÃO DISPONÍVEL\n";
    }
} catch (Exception $e) {
    echo "❌ Erro ao testar BadgesManager: " . $e->getMessage() . "\n";
}

// Teste 3: Testar conexão e buscar usuário para teste
echo "\n📋 3. TESTE COM USUÁRIO REAL:\n";
echo "=============================\n";

try {
    $pdo = conectarBD();
    echo "✅ Conexão com banco: OK\n";
    
    // Buscar um usuário para teste
    $stmt = $pdo->query("SELECT id, nome FROM usuarios LIMIT 1");
    $usuario = $stmt->fetch();
    
    if ($usuario) {
        $usuario_id = $usuario['id'];
        echo "✅ Usuário de teste encontrado: {$usuario['nome']} (ID: $usuario_id)\n";
        
        // Teste 4: Testar função verificarBadgesProvas
        echo "\n📋 4. TESTE DA FUNÇÃO verificarBadgesProvas:\n";
        echo "===========================================\n";
        
        if (function_exists('verificarBadgesProvas')) {
            try {
                $resultado = verificarBadgesProvas($usuario_id);
                echo "✅ Função executada: " . ($resultado ? "Badge atribuída" : "Nenhuma badge atribuída") . "\n";
            } catch (Exception $e) {
                echo "❌ Erro ao executar verificarBadgesProvas: " . $e->getMessage() . "\n";
            }
        }
        
        // Teste 5: Testar função verificarBadgesForum
        echo "\n📋 5. TESTE DA FUNÇÃO verificarBadgesForum:\n";
        echo "==========================================\n";
        
        if (function_exists('verificarBadgesForum')) {
            try {
                $resultado = verificarBadgesForum($usuario_id);
                echo "✅ Função executada: " . ($resultado ? "Badge atribuída" : "Nenhuma badge atribuída") . "\n";
            } catch (Exception $e) {
                echo "❌ Erro ao executar verificarBadgesForum: " . $e->getMessage() . "\n";
            }
        }
        
        // Teste 6: Testar função verificarBadgesGPA
        echo "\n📋 6. TESTE DA FUNÇÃO verificarBadgesGPA:\n";
        echo "========================================\n";
        
        if (function_exists('verificarBadgesGPA')) {
            try {
                $resultado = verificarBadgesGPA($usuario_id);
                echo "✅ Função executada: " . ($resultado ? "Badge atribuída" : "Nenhuma badge atribuída") . "\n";
            } catch (Exception $e) {
                echo "❌ Erro ao executar verificarBadgesGPA: " . $e->getMessage() . "\n";
            }
        }
        
        // Teste 7: Testar função verificarBadgesPaises
        echo "\n📋 7. TESTE DA FUNÇÃO verificarBadgesPaises:\n";
        echo "===========================================\n";
        
        if (function_exists('verificarBadgesPaises')) {
            try {
                $resultado = verificarBadgesPaises($usuario_id);
                echo "✅ Função executada: " . ($resultado ? "Badge atribuída" : "Nenhuma badge atribuída") . "\n";
            } catch (Exception $e) {
                echo "❌ Erro ao executar verificarBadgesPaises: " . $e->getMessage() . "\n";
            }
        }
        
        // Teste 8: Testar função verificarTodasBadges
        echo "\n📋 8. TESTE DA FUNÇÃO verificarTodasBadges:\n";
        echo "==========================================\n";
        
        if (function_exists('verificarTodasBadges')) {
            try {
                $resultado = verificarTodasBadges($usuario_id);
                echo "✅ Função executada: " . count($resultado) . " tipos de badges verificados\n";
                if (!empty($resultado)) {
                    echo "   Badges conquistadas: " . implode(', ', $resultado) . "\n";
                }
            } catch (Exception $e) {
                echo "❌ Erro ao executar verificarTodasBadges: " . $e->getMessage() . "\n";
            }
        }
        
    } else {
        echo "❌ Nenhum usuário encontrado para teste\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro na conexão: " . $e->getMessage() . "\n";
}

echo "\n📋 9. VERIFICAÇÃO DE BADGES EXISTENTES:\n";
echo "=======================================\n";

try {
    $stmt = $pdo->query("SELECT codigo, nome, tipo, categoria FROM badges WHERE ativa = 1");
    $badges = $stmt->fetchAll();
    
    echo "✅ Total de badges ativas: " . count($badges) . "\n";
    
    if (count($badges) > 0) {
        echo "\n📝 Badges disponíveis:\n";
        foreach ($badges as $badge) {
            echo "   - {$badge['codigo']}: {$badge['nome']} ({$badge['tipo']}/{$badge['categoria']})\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erro ao buscar badges: " . $e->getMessage() . "\n";
}

echo "\n🎯 TESTE CONCLUÍDO!\n";
echo "===================\n";
?>
