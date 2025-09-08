<?php
/**
 * Script para verificar a tabela de badges
 */

require_once 'config.php';

echo "🔍 VERIFICANDO TABELA DE BADGES\n";
echo "============================\n\n";

try {
    $pdo = conectarBD();
    
    // Verificar se a tabela existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'badges'");
    if ($stmt->rowCount() == 0) {
        echo "❌ Tabela 'badges' não existe\n";
        exit;
    }
    
    // Contar registros
    $stmt = $pdo->query("SELECT COUNT(*) FROM badges");
    $total = $stmt->fetchColumn();
    echo "📊 Total de badges na tabela: $total\n\n";
    
    // Verificar badges de países
    $stmt = $pdo->query("SELECT * FROM badges WHERE codigo LIKE 'paises_%' ORDER BY condicao_valor");
    $badges_paises = $stmt->fetchAll();
    
    if (empty($badges_paises)) {
        echo "❌ Nenhuma badge de países encontrada na tabela\n";
        echo "\n🔧 SOLUÇÃO: Execute o script inserir_badges.php para criar as badges\n";
        exit;
    }
    
    echo "📋 BADGES DE PAÍSES ENCONTRADAS:\n";
    echo "-------------------------------\n";
    foreach ($badges_paises as $b) {
        echo "ID: {$b['id']} | Código: {$b['codigo']} | Nome: {$b['nome']} | "
           . "Condição: {$b['condicao_valor']} países | Ativa: {$b['ativa']}\n";
    }
    
    // Verificar se as badges estão ativas
    $inativas = false;
    foreach ($badges_paises as $b) {
        if ($b['ativa'] != 1) {
            $inativas = true;
            echo "\n❌ A badge {$b['codigo']} está inativa (ativa = {$b['ativa']})\n";
        }
    }
    
    if ($inativas) {
        echo "\n🔧 SOLUÇÃO: Ative as badges com o seguinte comando SQL:\n";
        echo "UPDATE badges SET ativa = 1 WHERE codigo LIKE 'paises_%';\n";
    }
    
    // Verificar se a função atribuirBadge está funcionando
    echo "\n🔧 TESTANDO ATRIBUIÇÃO MANUAL DE BADGE:\n";
    echo "-------------------------------------\n";
    
    // Obter um usuário para teste
    $stmt = $pdo->query("SELECT id, nome FROM usuarios LIMIT 1");
    $usuario = $stmt->fetch();
    
    if (!$usuario) {
        echo "❌ Nenhum usuário encontrado para teste\n";
        exit;
    }
    
    echo "Testando com usuário: {$usuario['nome']} (ID: {$usuario['id']})\n";
    
    // Incluir o arquivo com a função
    require_once 'sistema_badges.php';
    
    // Testar atribuição manual
    $resultado = atribuirBadge($usuario['id'], 'paises_bronze', 'Teste manual');
    
    if ($resultado) {
        echo "✅ Badge atribuída com sucesso!\n";
    } else {
        echo "❌ Falha ao atribuir badge manualmente\n";
        
        // Verificar se já tem a badge
        $stmt = $pdo->prepare("
            SELECT ub.id 
            FROM usuario_badges ub
            JOIN badges b ON ub.badge_id = b.id
            WHERE ub.usuario_id = ? AND b.codigo = ?
        ");
        $stmt->execute([$usuario['id'], 'paises_bronze']);
        
        if ($stmt->rowCount() > 0) {
            echo "ℹ️ O usuário já possui esta badge (isso é normal)\n";
        } else {
            echo "❌ PROBLEMA ENCONTRADO: O usuário não tem a badge e não foi possível atribuí-la\n";
            
            // Verificar erros no log
            if (file_exists('error_log')) {
                $log = file_get_contents('error_log');
                $linhas = explode("\n", $log);
                $ultimas_linhas = array_slice($linhas, -10);
                
                echo "\n📋 Últimas linhas do log de erros:\n";
                foreach ($ultimas_linhas as $linha) {
                    echo $linha . "\n";
                }
            }
        }
    }
    
    echo "\n🔍 VERIFICANDO CHAMADA DA FUNÇÃO verificarBadgesPaises():\n";
    echo "------------------------------------------------------\n";
    
    // Verificar se a função está sendo chamada no arquivo tracking_paises.php
    $tracking_content = file_get_contents('tracking_paises.php');
    
    if (strpos($tracking_content, 'verificarBadgesPaises($usuario_id)') !== false) {
        echo "✅ Chamada à função verificarBadgesPaises() encontrada no arquivo tracking_paises.php\n";
        
        // Verificar se está no lugar correto
        $pos_chamada = strpos($tracking_content, 'verificarBadgesPaises($usuario_id)');
        $pos_primeira_visita = strpos($tracking_content, '"primeira_visita" => true');
        $pos_visita_existente = strpos($tracking_content, '"primeira_visita" => false');
        
        if ($pos_chamada > $pos_primeira_visita && $pos_chamada > $pos_visita_existente) {
            echo "✅ A função está sendo chamada após registrar a visita\n";
        } else {
            echo "❌ A função NÃO está sendo chamada após registrar a visita (PROBLEMA ENCONTRADO)\n";
        }
    } else {
        echo "❌ Chamada à função verificarBadgesPaises() NÃO encontrada no arquivo tracking_paises.php\n";
    }
    
    echo "\n🔧 DIAGNÓSTICO FINAL:\n";
    echo "====================\n";
    
    if (empty($badges_paises)) {
        echo "❌ Não há badges de países cadastradas. Execute o script inserir_badges.php\n";
    } elseif ($inativas) {
        echo "❌ Há badges de países inativas. Ative-as com o comando SQL sugerido acima\n";
    } elseif (strpos($tracking_content, 'verificarBadgesPaises($usuario_id)') === false) {
        echo "❌ A função verificarBadgesPaises() não está sendo chamada no arquivo tracking_paises.php\n";
        echo "   Solução: Edite o arquivo tracking_paises.php e adicione a chamada à função após registrar a visita\n";
    } else {
        echo "✅ A configuração das badges de países parece estar correta.\n";
        echo "   Possíveis problemas:\n";
        echo "   1. A função atribuirBadge() pode estar com erro\n";
        echo "   2. Pode haver um problema na consulta SQL dentro da função verificarBadgesPaises()\n";
        echo "   3. Verifique se há erros no log do PHP\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>