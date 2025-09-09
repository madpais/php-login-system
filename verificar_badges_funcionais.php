<?php
/**
 * Verificação completa e funcional do sistema de badges
 * Testa todas as funções e compatibilidade
 */

require_once 'config.php';

echo "🧪 VERIFICAÇÃO COMPLETA DO SISTEMA DE BADGES\n";
echo "============================================\n\n";

$erros = [];
$avisos = [];
$sucessos = [];

try {
    $pdo = conectarBD();
    
    // 1. Verificar estrutura das tabelas
    echo "📋 1. VERIFICANDO ESTRUTURA DAS TABELAS:\n";
    echo "========================================\n";
    
    // Verificar tabela badges
    $stmt = $pdo->query("SHOW TABLES LIKE 'badges'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabela 'badges': Existe\n";
        $sucessos[] = "Tabela badges existe";
        
        // Verificar colunas essenciais
        $stmt = $pdo->query("DESCRIBE badges");
        $colunas = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $colunas_necessarias = ['id', 'codigo', 'nome', 'descricao', 'icone', 'tipo', 'categoria', 'condicao_valor', 'ativa'];
        foreach ($colunas_necessarias as $coluna) {
            if (in_array($coluna, $colunas)) {
                echo "   ✅ Coluna '$coluna': Existe\n";
            } else {
                echo "   ❌ Coluna '$coluna': FALTANDO\n";
                $erros[] = "Coluna '$coluna' faltando na tabela badges";
            }
        }
    } else {
        echo "❌ Tabela 'badges': NÃO EXISTE\n";
        $erros[] = "Tabela badges não existe";
    }
    
    // Verificar tabela usuario_badges
    $stmt = $pdo->query("SHOW TABLES LIKE 'usuario_badges'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabela 'usuario_badges': Existe\n";
        $sucessos[] = "Tabela usuario_badges existe";
    } else {
        echo "❌ Tabela 'usuario_badges': NÃO EXISTE\n";
        $erros[] = "Tabela usuario_badges não existe";
    }
    
    // 2. Verificar badges cadastradas
    echo "\n📋 2. VERIFICANDO BADGES CADASTRADAS:\n";
    echo "====================================\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM badges WHERE ativa = 1");
    $total_badges = $stmt->fetchColumn();
    echo "📊 Total de badges ativas: $total_badges\n";
    
    if ($total_badges >= 35) {
        echo "✅ Quantidade adequada de badges\n";
        $sucessos[] = "Badges suficientes cadastradas";
    } else {
        echo "⚠️ Poucas badges cadastradas (mínimo recomendado: 35)\n";
        $avisos[] = "Poucas badges cadastradas";
    }
    
    // Verificar badges específicas necessárias
    $badges_necessarias = [
        'prova_bronze', 'prova_prata', 'prova_ouro', 'prova_rubi', 'prova_diamante',
        'forum_bronze', 'forum_prata', 'forum_ouro', 'forum_rubi', 'forum_diamante',
        'gpa_bronze', 'gpa_prata', 'gpa_ouro', 'gpa_rubi', 'gpa_diamante',
        'paises_bronze', 'paises_prata', 'paises_ouro', 'paises_rubi', 'paises_diamante'
    ];
    
    $stmt = $pdo->prepare("SELECT codigo FROM badges WHERE codigo = ? AND ativa = 1");
    $badges_faltantes = [];
    
    foreach ($badges_necessarias as $badge_codigo) {
        $stmt->execute([$badge_codigo]);
        if ($stmt->fetch()) {
            echo "   ✅ $badge_codigo: Existe\n";
        } else {
            echo "   ❌ $badge_codigo: FALTANDO\n";
            $badges_faltantes[] = $badge_codigo;
        }
    }
    
    if (empty($badges_faltantes)) {
        $sucessos[] = "Todas as badges essenciais existem";
    } else {
        $erros[] = "Badges faltantes: " . implode(', ', $badges_faltantes);
    }
    
    // 3. Verificar funções de badges
    echo "\n📋 3. VERIFICANDO FUNÇÕES DE BADGES:\n";
    echo "===================================\n";
    
    $funcoes_necessarias = [
        'verificarBadgesProvas',
        'verificarBadgesForum',
        'verificarBadgesGPA',
        'verificarBadgesPaises',
        'atribuirBadge',
        'verificarTodasBadges'
    ];
    
    foreach ($funcoes_necessarias as $funcao) {
        if (function_exists($funcao)) {
            echo "✅ $funcao: Disponível\n";
            $sucessos[] = "Função $funcao disponível";
        } else {
            echo "❌ $funcao: NÃO DISPONÍVEL\n";
            $erros[] = "Função $funcao não disponível";
        }
    }
    
    // 4. Verificar classe BadgesManager
    echo "\n📋 4. VERIFICANDO CLASSE BADGESMANAGER:\n";
    echo "======================================\n";
    
    if (class_exists('BadgesManager')) {
        echo "✅ Classe BadgesManager: Disponível\n";
        $sucessos[] = "Classe BadgesManager disponível";
        
        try {
            $manager = new BadgesManager();
            echo "✅ Instância BadgesManager: Criada com sucesso\n";
            $sucessos[] = "BadgesManager instanciável";
            
            if (method_exists($manager, 'verificarBadgesResultado')) {
                echo "✅ Método verificarBadgesResultado: Disponível\n";
                $sucessos[] = "Método verificarBadgesResultado disponível";
            } else {
                echo "❌ Método verificarBadgesResultado: NÃO DISPONÍVEL\n";
                $erros[] = "Método verificarBadgesResultado não disponível";
            }
        } catch (Exception $e) {
            echo "❌ Erro ao instanciar BadgesManager: " . $e->getMessage() . "\n";
            $erros[] = "Erro ao instanciar BadgesManager";
        }
    } else {
        echo "❌ Classe BadgesManager: NÃO DISPONÍVEL\n";
        $erros[] = "Classe BadgesManager não disponível";
    }
    
    // 5. Teste funcional com usuário
    echo "\n📋 5. TESTE FUNCIONAL COM USUÁRIO:\n";
    echo "=================================\n";
    
    $stmt = $pdo->query("SELECT id, nome FROM usuarios LIMIT 1");
    $usuario = $stmt->fetch();
    
    if ($usuario) {
        $usuario_id = $usuario['id'];
        echo "👤 Testando com usuário: {$usuario['nome']} (ID: $usuario_id)\n";
        
        // Testar cada função
        if (function_exists('verificarBadgesProvas')) {
            try {
                $resultado = verificarBadgesProvas($usuario_id);
                echo "✅ verificarBadgesProvas: Executada (" . ($resultado ? "badge atribuída" : "sem nova badge") . ")\n";
                $sucessos[] = "verificarBadgesProvas funcional";
            } catch (Exception $e) {
                echo "❌ verificarBadgesProvas: Erro - " . $e->getMessage() . "\n";
                $erros[] = "Erro em verificarBadgesProvas";
            }
        }
        
        if (function_exists('verificarBadgesForum')) {
            try {
                $resultado = verificarBadgesForum($usuario_id);
                echo "✅ verificarBadgesForum: Executada (" . ($resultado ? "badge atribuída" : "sem nova badge") . ")\n";
                $sucessos[] = "verificarBadgesForum funcional";
            } catch (Exception $e) {
                echo "❌ verificarBadgesForum: Erro - " . $e->getMessage() . "\n";
                $erros[] = "Erro em verificarBadgesForum";
            }
        }
        
        if (function_exists('verificarBadgesGPA')) {
            try {
                $resultado = verificarBadgesGPA($usuario_id);
                echo "✅ verificarBadgesGPA: Executada (" . ($resultado ? "badge atribuída" : "sem nova badge") . ")\n";
                $sucessos[] = "verificarBadgesGPA funcional";
            } catch (Exception $e) {
                echo "❌ verificarBadgesGPA: Erro - " . $e->getMessage() . "\n";
                $erros[] = "Erro em verificarBadgesGPA";
            }
        }
        
        if (function_exists('verificarBadgesPaises')) {
            try {
                $resultado = verificarBadgesPaises($usuario_id);
                echo "✅ verificarBadgesPaises: Executada (" . ($resultado ? "badge atribuída" : "sem nova badge") . ")\n";
                $sucessos[] = "verificarBadgesPaises funcional";
            } catch (Exception $e) {
                echo "❌ verificarBadgesPaises: Erro - " . $e->getMessage() . "\n";
                $erros[] = "Erro em verificarBadgesPaises";
            }
        }
        
        if (function_exists('verificarTodasBadges')) {
            try {
                $resultado = verificarTodasBadges($usuario_id);
                echo "✅ verificarTodasBadges: Executada (" . count($resultado) . " tipos verificados)\n";
                $sucessos[] = "verificarTodasBadges funcional";
            } catch (Exception $e) {
                echo "❌ verificarTodasBadges: Erro - " . $e->getMessage() . "\n";
                $erros[] = "Erro em verificarTodasBadges";
            }
        }
        
    } else {
        echo "⚠️ Nenhum usuário encontrado para teste\n";
        $avisos[] = "Nenhum usuário para teste";
    }
    
    // 6. Resumo final
    echo "\n📊 RESUMO FINAL:\n";
    echo "================\n";
    
    echo "✅ Sucessos: " . count($sucessos) . "\n";
    foreach ($sucessos as $sucesso) {
        echo "   • $sucesso\n";
    }
    
    if (!empty($avisos)) {
        echo "\n⚠️ Avisos: " . count($avisos) . "\n";
        foreach ($avisos as $aviso) {
            echo "   • $aviso\n";
        }
    }
    
    if (!empty($erros)) {
        echo "\n❌ Erros: " . count($erros) . "\n";
        foreach ($erros as $erro) {
            echo "   • $erro\n";
        }
        
        echo "\n🔧 AÇÕES NECESSÁRIAS:\n";
        echo "====================\n";
        if (in_array("Tabela badges não existe", $erros)) {
            echo "1. Execute: php reset_badges_sistema.php\n";
        }
        if (!empty($badges_faltantes)) {
            echo "2. Execute: php inserir_badges_completo.php\n";
        }
        if (strpos(implode(' ', $erros), 'função') !== false) {
            echo "3. Verifique se config.php inclui sistema_badges.php e badges_manager.php\n";
        }
    } else {
        echo "\n🎉 SISTEMA DE BADGES 100% FUNCIONAL!\n";
        echo "====================================\n";
        echo "✅ Todas as verificações passaram\n";
        echo "✅ Todas as funções estão operacionais\n";
        echo "✅ Sistema pronto para uso\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro crítico durante verificação: " . $e->getMessage() . "\n";
}

echo "\n🎯 VERIFICAÇÃO CONCLUÍDA!\n";
?>
