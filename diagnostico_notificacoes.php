<?php
/**
 * Diagnóstico Completo do Sistema de Notificações
 * Verifica tabelas, funcionalidades e testa o sistema
 */

require_once 'config.php';
iniciarSessaoSegura();

echo "🔔 DIAGNÓSTICO COMPLETO - SISTEMA DE NOTIFICAÇÕES\n";
echo "=================================================\n\n";

try {
    $pdo = conectarBD();
    echo "✅ Conectado ao banco de dados\n\n";
    
    // 1. Verificar tabela de notificações
    echo "📋 1. VERIFICANDO TABELA DE NOTIFICAÇÕES:\n";
    echo "==========================================\n";
    
    $stmt = $pdo->query("SHOW TABLES LIKE 'notificacoes_usuario'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabela notificacoes_usuario existe\n";
        
        // Verificar estrutura
        $stmt = $pdo->query("DESCRIBE notificacoes_usuario");
        $colunas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "📊 Estrutura da tabela:\n";
        foreach ($colunas as $coluna) {
            echo "  - {$coluna['Field']} ({$coluna['Type']})\n";
        }
        
        // Verificar dados
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM notificacoes_usuario");
        $total = $stmt->fetchColumn();
        echo "\n📈 Total de notificações: $total\n";
        
    } else {
        echo "❌ Tabela notificacoes_usuario NÃO existe\n";
        echo "🔧 Solução: Execute php criar_tabela_perfil_usuario.php\n";
    }
    
    // 2. Verificar arquivos do sistema
    echo "\n📋 2. VERIFICANDO ARQUIVOS DO SISTEMA:\n";
    echo "======================================\n";
    
    $arquivos_necessarios = [
        'sistema_notificacoes.php' => 'Classe principal do sistema',
        'ajax_notificacoes.php' => 'Handler AJAX para ações',
        'componente_notificacoes.php' => 'Componente para header',
        'todas_notificacoes.php' => 'Página de todas as notificações'
    ];
    
    foreach ($arquivos_necessarios as $arquivo => $descricao) {
        if (file_exists($arquivo)) {
            echo "✅ $arquivo - $descricao\n";
        } else {
            echo "❌ $arquivo - $descricao (FALTANDO)\n";
        }
    }
    
    // 3. Testar classe SistemaNotificacoes
    echo "\n📋 3. TESTANDO CLASSE SISTEMANOTIFICACOES:\n";
    echo "==========================================\n";
    
    if (file_exists('sistema_notificacoes.php')) {
        require_once 'sistema_notificacoes.php';
        
        try {
            $sistema = new SistemaNotificacoes();
            echo "✅ Classe SistemaNotificacoes instanciada\n";
            
            // Simular usuário logado para testes
            if (!isset($_SESSION['usuario_id'])) {
                $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE usuario = 'teste' LIMIT 1");
                $stmt->execute();
                $user = $stmt->fetch();
                if ($user) {
                    $_SESSION['usuario_id'] = $user['id'];
                    echo "✅ Usuário teste simulado para testes\n";
                }
            }
            
            $usuario_id = $_SESSION['usuario_id'] ?? 1;
            
            // Testar métodos principais
            echo "\n🔍 Testando métodos:\n";
            
            // Contar notificações não lidas
            $nao_lidas = $sistema->contarNotificacoesNaoLidas($usuario_id);
            echo "✅ contarNotificacoesNaoLidas(): $nao_lidas notificações\n";
            
            // Buscar notificações não lidas
            $notificacoes = $sistema->buscarNotificacoesNaoLidas($usuario_id, 5);
            echo "✅ buscarNotificacoesNaoLidas(): " . count($notificacoes) . " notificações\n";
            
            // Buscar todas as notificações
            $todas = $sistema->buscarTodasNotificacoes($usuario_id, 10);
            echo "✅ buscarTodasNotificacoes(): " . count($todas) . " notificações\n";
            
        } catch (Exception $e) {
            echo "❌ Erro ao testar classe: " . $e->getMessage() . "\n";
        }
    } else {
        echo "❌ Arquivo sistema_notificacoes.php não encontrado\n";
    }
    
    // 4. Criar notificações de exemplo
    echo "\n📋 4. CRIANDO NOTIFICAÇÕES DE EXEMPLO:\n";
    echo "======================================\n";
    
    if (isset($sistema) && isset($usuario_id)) {
        // Criar notificação de badge
        try {
            $stmt = $pdo->prepare("
                INSERT INTO notificacoes_usuario (usuario_id, tipo, titulo, mensagem, link, lida)
                VALUES (?, 'badge_conquistada', 'Nova Badge Conquistada!', 'Parabéns! Você conquistou a badge \"Primeiro Teste\"', 'pagina_usuario.php', FALSE)
            ");
            $stmt->execute([$usuario_id]);
            echo "✅ Notificação de badge criada\n";
        } catch (Exception $e) {
            echo "⚠️ Notificação de badge já existe ou erro: " . $e->getMessage() . "\n";
        }
        
        // Criar notificação de nível
        try {
            $stmt = $pdo->prepare("
                INSERT INTO notificacoes_usuario (usuario_id, tipo, titulo, mensagem, link, lida)
                VALUES (?, 'nivel_subiu', 'Nível Aumentado!', 'Você subiu para o nível 3! Continue assim!', 'pagina_usuario.php', FALSE)
            ");
            $stmt->execute([$usuario_id]);
            echo "✅ Notificação de nível criada\n";
        } catch (Exception $e) {
            echo "⚠️ Notificação de nível já existe ou erro: " . $e->getMessage() . "\n";
        }
        
        // Criar notificação de fórum
        try {
            $stmt = $pdo->prepare("
                INSERT INTO notificacoes_usuario (usuario_id, tipo, titulo, mensagem, link, lida)
                VALUES (?, 'forum_resposta', 'Nova Resposta no Fórum', 'Alguém respondeu ao seu tópico \"Dúvidas sobre SAT\"', 'forum.php', FALSE)
            ");
            $stmt->execute([$usuario_id]);
            echo "✅ Notificação de fórum criada\n";
        } catch (Exception $e) {
            echo "⚠️ Notificação de fórum já existe ou erro: " . $e->getMessage() . "\n";
        }
        
        // Criar notificação de sistema
        try {
            $stmt = $pdo->prepare("
                INSERT INTO notificacoes_usuario (usuario_id, tipo, titulo, mensagem, link, lida)
                VALUES (?, 'sistema', 'Bem-vindo ao DayDreaming!', 'Explore todas as funcionalidades da plataforma', 'index.php', FALSE)
            ");
            $stmt->execute([$usuario_id]);
            echo "✅ Notificação de sistema criada\n";
        } catch (Exception $e) {
            echo "⚠️ Notificação de sistema já existe ou erro: " . $e->getMessage() . "\n";
        }
    }
    
    // 5. Testar funcionalidades após criação
    echo "\n📋 5. TESTANDO APÓS CRIAÇÃO DE EXEMPLOS:\n";
    echo "========================================\n";
    
    if (isset($sistema) && isset($usuario_id)) {
        $nao_lidas_apos = $sistema->contarNotificacoesNaoLidas($usuario_id);
        echo "✅ Notificações não lidas após criação: $nao_lidas_apos\n";
        
        $notificacoes_apos = $sistema->buscarNotificacoesNaoLidas($usuario_id, 10);
        echo "✅ Lista de notificações não lidas:\n";
        
        foreach ($notificacoes_apos as $notif) {
            echo "  - [{$notif['tipo']}] {$notif['titulo']}\n";
            echo "    {$notif['mensagem']}\n";
            echo "    Link: " . ($notif['link'] ?? 'Nenhum') . "\n\n";
        }
    }
    
    // 6. Testar AJAX
    echo "\n📋 6. TESTANDO FUNCIONALIDADE AJAX:\n";
    echo "===================================\n";
    
    if (file_exists('ajax_notificacoes.php')) {
        echo "✅ Arquivo ajax_notificacoes.php existe\n";
        echo "✅ Endpoints disponíveis:\n";
        echo "  - marcar_lida: Marcar notificação como lida\n";
        echo "  - marcar_todas_lidas: Marcar todas como lidas\n";
        echo "  - buscar_notificacoes: Buscar notificações\n";
        echo "  - contar_nao_lidas: Contar não lidas\n";
    } else {
        echo "❌ Arquivo ajax_notificacoes.php não encontrado\n";
    }
    
    // 7. Verificar integração com header
    echo "\n📋 7. VERIFICANDO INTEGRAÇÃO COM HEADER:\n";
    echo "=========================================\n";
    
    if (file_exists('header_status.php')) {
        $header_content = file_get_contents('header_status.php');
        
        if (strpos($header_content, 'componente_notificacoes.php') !== false) {
            echo "✅ Header integrado com componente de notificações\n";
        } else {
            echo "⚠️ Header NÃO integrado com componente de notificações\n";
            echo "🔧 Solução: Adicionar include do componente_notificacoes.php\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erro geral: " . $e->getMessage() . "\n";
}

// 8. Resumo e próximos passos
echo "\n📊 RESUMO DO DIAGNÓSTICO:\n";
echo "==========================\n";

$problemas = [];
$sucessos = [];

// Verificar se tabela existe
$stmt = $pdo->query("SHOW TABLES LIKE 'notificacoes_usuario'");
if ($stmt->rowCount() > 0) {
    $sucessos[] = "Tabela notificacoes_usuario existe";
} else {
    $problemas[] = "Tabela notificacoes_usuario não existe";
}

// Verificar arquivos
$arquivos_sistema = ['sistema_notificacoes.php', 'ajax_notificacoes.php', 'componente_notificacoes.php'];
foreach ($arquivos_sistema as $arquivo) {
    if (file_exists($arquivo)) {
        $sucessos[] = "Arquivo $arquivo existe";
    } else {
        $problemas[] = "Arquivo $arquivo não existe";
    }
}

echo "✅ SUCESSOS (" . count($sucessos) . "):\n";
foreach ($sucessos as $sucesso) {
    echo "  - $sucesso\n";
}

if (!empty($problemas)) {
    echo "\n⚠️ PROBLEMAS (" . count($problemas) . "):\n";
    foreach ($problemas as $problema) {
        echo "  - $problema\n";
    }
}

echo "\n🔗 PRÓXIMOS PASSOS:\n";
echo "===================\n";
echo "1. Acesse: http://localhost:8080/todas_notificacoes.php\n";
echo "2. Verifique se as notificações aparecem\n";
echo "3. Teste marcar como lida\n";
echo "4. Verifique contador no header\n";
echo "5. Teste funcionalidades AJAX\n";

echo "\n🧪 PÁGINAS PARA TESTAR:\n";
echo "========================\n";
echo "- http://localhost:8080/todas_notificacoes.php (Todas as notificações)\n";
echo "- http://localhost:8080/pagina_usuario.php (Verificar header)\n";
echo "- http://localhost:8080/forum.php (Testar notificações de fórum)\n";

?>
