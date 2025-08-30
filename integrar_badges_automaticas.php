<?php
/**
 * Script para integrar verificação automática de badges
 */

require_once 'config.php';

echo "🔄 INTEGRANDO VERIFICAÇÃO AUTOMÁTICA DE BADGES\n";
echo "===============================================\n\n";

// 1. Atualizar tracking de países para verificar badges
echo "1. Atualizando tracking de países...\n";

$tracking_content = file_get_contents('tracking_paises.php');

// Adicionar verificação de badges no final da função registrarVisitaPais
$new_tracking = str_replace(
    'return [
                "primeira_visita" => true,
                "total_visitas" => 1,
                "pais_nome" => $pais_nome
            ];',
    'return [
                "primeira_visita" => true,
                "total_visitas" => 1,
                "pais_nome" => $pais_nome
            ];',
    $tracking_content
);

// Adicionar include do sistema de badges no início
if (strpos($new_tracking, 'require_once "sistema_badges.php";') === false) {
    $new_tracking = str_replace(
        'require_once "config.php";',
        'require_once "config.php";
require_once "sistema_badges.php";',
        $new_tracking
    );
}

// Adicionar verificação de badges após registrar visita
$new_tracking = str_replace(
    'return [
                "primeira_visita" => true,
                "total_visitas" => 1,
                "pais_nome" => $pais_nome
            ];
        }
        
    } catch (Exception $e) {',
    'return [
                "primeira_visita" => true,
                "total_visitas" => 1,
                "pais_nome" => $pais_nome
            ];
        }
        
        // Verificar badges de países após registrar visita
        verificarBadgesPaises($usuario_id);
        
    } catch (Exception $e) {',
    $new_tracking
);

file_put_contents('tracking_paises.php', $new_tracking);
echo "✅ Tracking de países atualizado\n";

// 2. Criar arquivo para verificar badges após completar teste
echo "\n2. Criando verificação para testes...\n";

$verificar_teste = '<?php
/**
 * Verificar badges após completar teste
 * Incluir este código após salvar resultado do teste
 */

// Adicionar no final do arquivo que salva resultados de testes
function verificarBadgesAposTest($usuario_id) {
    require_once "sistema_badges.php";
    verificarBadgesProvas($usuario_id);
}

// Exemplo de uso:
// Após inserir em resultados_testes, chamar:
// verificarBadgesAposTest($usuario_id);
?>';

file_put_contents('verificar_badges_teste.php', $verificar_teste);
echo "✅ Verificação de testes criada\n";

// 3. Criar arquivo para verificar badges após participação no fórum
echo "\n3. Criando verificação para fórum...\n";

$verificar_forum = '<?php
/**
 * Verificar badges após participação no fórum
 * Incluir este código após criar tópico ou resposta
 */

// Adicionar no final do arquivo que salva tópicos/respostas
function verificarBadgesAposForum($usuario_id) {
    require_once "sistema_badges.php";
    verificarBadgesForum($usuario_id);
}

// Exemplo de uso:
// Após inserir em forum_topicos ou forum_respostas, chamar:
// verificarBadgesAposForum($usuario_id);
?>';

file_put_contents('verificar_badges_forum.php', $verificar_forum);
echo "✅ Verificação de fórum criada\n";

// 4. Criar script de teste para verificar se tudo está funcionando
echo "\n4. Criando script de teste...\n";

$teste_badges = '<?php
/**
 * Script de teste para verificar sistema de badges
 */

require_once "config.php";
require_once "sistema_badges.php";

echo "🧪 TESTANDO SISTEMA DE BADGES\n";
echo "=============================\n\n";

try {
    $pdo = conectarBD();
    
    // Buscar um usuário para teste
    $stmt = $pdo->query("SELECT id FROM usuarios LIMIT 1");
    $usuario = $stmt->fetch();
    
    if (!$usuario) {
        echo "❌ Nenhum usuário encontrado para teste\n";
        exit;
    }
    
    $usuario_id = $usuario["id"];
    echo "👤 Testando com usuário ID: $usuario_id\n\n";
    
    // Testar verificação de badges
    echo "🔍 Verificando badges de provas...\n";
    $resultado_provas = verificarBadgesProvas($usuario_id);
    echo $resultado_provas ? "✅ Badge de prova verificada\n" : "ℹ️ Nenhuma badge de prova conquistada\n";
    
    echo "\n🔍 Verificando badges de fórum...\n";
    $resultado_forum = verificarBadgesForum($usuario_id);
    echo $resultado_forum ? "✅ Badge de fórum verificada\n" : "ℹ️ Nenhuma badge de fórum conquistada\n";
    
    echo "\n🔍 Verificando badges de GPA...\n";
    $resultado_gpa = verificarBadgesGPA($usuario_id);
    echo $resultado_gpa ? "✅ Badge de GPA verificada\n" : "ℹ️ Nenhuma badge de GPA conquistada\n";
    
    echo "\n🔍 Verificando badges de países...\n";
    $resultado_paises = verificarBadgesPaises($usuario_id);
    echo $resultado_paises ? "✅ Badge de países verificada\n" : "ℹ️ Nenhuma badge de países conquistada\n";
    
    // Listar badges conquistadas
    echo "\n🏆 BADGES CONQUISTADAS:\n";
    echo "======================\n";
    $badges = obterBadgesUsuario($usuario_id);
    
    if (empty($badges)) {
        echo "❌ Nenhuma badge conquistada ainda\n";
    } else {
        foreach ($badges as $badge) {
            echo "🏅 {$badge[\"nome\"]} - {$badge[\"descricao\"]}\n";
            echo "   Conquistada em: {$badge[\"data_conquista\"]}\n";
            if ($badge[\"contexto\"]) {
                echo "   Contexto: {$badge[\"contexto\"]}\n";
            }
            echo "\n";
        }
    }
    
    echo "🎉 TESTE CONCLUÍDO!\n";
    
} catch (Exception $e) {
    echo "❌ Erro no teste: " . $e->getMessage() . "\n";
}
?>';

file_put_contents('testar_badges.php', $teste_badges);
echo "✅ Script de teste criado\n";

echo "\n📋 RESUMO DA INTEGRAÇÃO:\n";
echo "========================\n";
echo "✅ tracking_paises.php - Atualizado com verificação automática\n";
echo "✅ verificar_badges_teste.php - Criado para testes\n";
echo "✅ verificar_badges_forum.php - Criado para fórum\n";
echo "✅ testar_badges.php - Script de teste criado\n";

echo "\n🔗 PRÓXIMOS PASSOS:\n";
echo "===================\n";
echo "1. Executar: php testar_badges.php\n";
echo "2. Integrar verificar_badges_teste.php nos arquivos de teste\n";
echo "3. Integrar verificar_badges_forum.php nos arquivos do fórum\n";
echo "4. Testar na página do usuário\n";

echo "\n🎉 INTEGRAÇÃO AUTOMÁTICA CONCLUÍDA!\n";
?>
