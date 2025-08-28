<?php
/**
 * Diagnóstico do Header - Verificar problema de usuário incorreto
 */

require_once 'config.php';
iniciarSessaoSegura();

echo "🔍 DIAGNÓSTICO DO HEADER - USUÁRIO INCORRETO\n";
echo "============================================\n\n";

// 1. Verificar estado atual da sessão
echo "📋 1. ESTADO ATUAL DA SESSÃO:\n";
echo "==============================\n";
echo "Session ID: " . session_id() . "\n";
echo "Session status: " . session_status() . "\n";

if (isset($_SESSION['usuario_id'])) {
    echo "✅ usuario_id: " . $_SESSION['usuario_id'] . "\n";
    echo "✅ usuario_nome: " . ($_SESSION['usuario_nome'] ?? 'N/A') . "\n";
    echo "✅ usuario_login: " . ($_SESSION['usuario_login'] ?? 'N/A') . "\n";
    echo "✅ is_admin: " . (isset($_SESSION['is_admin']) ? ($_SESSION['is_admin'] ? 'true' : 'false') : 'N/A') . "\n";
} else {
    echo "❌ Nenhum usuário logado na sessão\n";
}

// 2. Verificar dados no banco para comparação
echo "\n📋 2. VERIFICAÇÃO NO BANCO DE DADOS:\n";
echo "====================================\n";

try {
    $pdo = conectarBD();
    
    // Buscar todos os usuários para comparação
    $stmt = $pdo->query("SELECT id, usuario, nome, is_admin, ativo FROM usuarios ORDER BY id");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Usuários no banco:\n";
    foreach ($usuarios as $user) {
        $status = $user['ativo'] ? 'Ativo' : 'Inativo';
        $admin = $user['is_admin'] ? 'Admin' : 'User';
        echo "  - ID: {$user['id']} | Login: {$user['usuario']} | Nome: {$user['nome']} | {$admin} | {$status}\n";
    }
    
    // Verificar se o usuário da sessão existe no banco
    if (isset($_SESSION['usuario_id'])) {
        $stmt = $pdo->prepare("SELECT id, usuario, nome, is_admin, ativo FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['usuario_id']]);
        $usuario_banco = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario_banco) {
            echo "\n✅ Usuário da sessão encontrado no banco:\n";
            echo "  - ID: {$usuario_banco['id']}\n";
            echo "  - Login: {$usuario_banco['usuario']}\n";
            echo "  - Nome: {$usuario_banco['nome']}\n";
            echo "  - Admin: " . ($usuario_banco['is_admin'] ? 'Sim' : 'Não') . "\n";
            echo "  - Ativo: " . ($usuario_banco['ativo'] ? 'Sim' : 'Não') . "\n";
            
            // Comparar dados da sessão com o banco
            echo "\n🔍 COMPARAÇÃO SESSÃO vs BANCO:\n";
            if ($_SESSION['usuario_nome'] === $usuario_banco['nome']) {
                echo "✅ Nome: Consistente\n";
            } else {
                echo "❌ Nome: INCONSISTENTE!\n";
                echo "  - Sessão: " . $_SESSION['usuario_nome'] . "\n";
                echo "  - Banco: " . $usuario_banco['nome'] . "\n";
            }
            
            if ($_SESSION['usuario_login'] === $usuario_banco['usuario']) {
                echo "✅ Login: Consistente\n";
            } else {
                echo "❌ Login: INCONSISTENTE!\n";
                echo "  - Sessão: " . $_SESSION['usuario_login'] . "\n";
                echo "  - Banco: " . $usuario_banco['usuario'] . "\n";
            }
            
            if ($_SESSION['is_admin'] == $usuario_banco['is_admin']) {
                echo "✅ Admin: Consistente\n";
            } else {
                echo "❌ Admin: INCONSISTENTE!\n";
                echo "  - Sessão: " . ($_SESSION['is_admin'] ? 'true' : 'false') . "\n";
                echo "  - Banco: " . ($usuario_banco['is_admin'] ? 'true' : 'false') . "\n";
            }
            
        } else {
            echo "❌ Usuário da sessão NÃO encontrado no banco!\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erro ao verificar banco: " . $e->getMessage() . "\n";
}

// 3. Simular login com usuário teste
echo "\n📋 3. TESTE DE LOGIN COM USUÁRIO 'teste':\n";
echo "==========================================\n";

try {
    // Buscar usuário teste
    $stmt = $pdo->prepare("SELECT id, usuario, senha, nome, is_admin, ativo FROM usuarios WHERE usuario = 'teste'");
    $stmt->execute();
    $usuario_teste = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario_teste) {
        echo "✅ Usuário 'teste' encontrado:\n";
        echo "  - ID: {$usuario_teste['id']}\n";
        echo "  - Nome: {$usuario_teste['nome']}\n";
        echo "  - Admin: " . ($usuario_teste['is_admin'] ? 'Sim' : 'Não') . "\n";
        
        // Verificar senha
        if (password_verify('teste123', $usuario_teste['senha'])) {
            echo "✅ Senha 'teste123' está correta\n";
            
            // Simular login
            $_SESSION['usuario_id'] = $usuario_teste['id'];
            $_SESSION['usuario_nome'] = $usuario_teste['nome'];
            $_SESSION['usuario_login'] = $usuario_teste['usuario'];
            $_SESSION['is_admin'] = (bool)$usuario_teste['is_admin'];
            $_SESSION['login_time'] = time();
            
            echo "✅ Login simulado realizado\n";
            echo "✅ Dados atualizados na sessão:\n";
            echo "  - usuario_id: " . $_SESSION['usuario_id'] . "\n";
            echo "  - usuario_nome: " . $_SESSION['usuario_nome'] . "\n";
            echo "  - usuario_login: " . $_SESSION['usuario_login'] . "\n";
            echo "  - is_admin: " . ($_SESSION['is_admin'] ? 'true' : 'false') . "\n";
            
        } else {
            echo "❌ Senha 'teste123' está incorreta\n";
        }
    } else {
        echo "❌ Usuário 'teste' não encontrado\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro no teste de login: " . $e->getMessage() . "\n";
}

// 4. Testar header após login
echo "\n📋 4. TESTE DO HEADER APÓS LOGIN:\n";
echo "==================================\n";

// Simular o que o header faz
$usuario_logado = isset($_SESSION['usuario_id']);
$usuario_nome = '';
$usuario_login = '';

if ($usuario_logado) {
    $usuario_nome = $_SESSION['usuario_nome'] ?? '';
    $usuario_login = $_SESSION['usuario_login'] ?? '';
}

echo "Resultado do header:\n";
echo "  - Usuário logado: " . ($usuario_logado ? 'Sim' : 'Não') . "\n";
echo "  - Nome exibido: " . $usuario_nome . "\n";
echo "  - Login exibido: " . $usuario_login . "\n";

// 5. Verificar logs de acesso recentes
echo "\n📋 5. LOGS DE ACESSO RECENTES:\n";
echo "===============================\n";

try {
    $stmt = $pdo->query("
        SELECT la.*, u.usuario, u.nome 
        FROM logs_acesso la 
        JOIN usuarios u ON la.usuario_id = u.id 
        ORDER BY la.data_hora DESC 
        LIMIT 10
    ");
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Últimos 10 logins:\n";
    foreach ($logs as $log) {
        $sucesso = $log['sucesso'] ? 'Sucesso' : 'Falha';
        echo "  - {$log['data_hora']} | {$log['usuario']} ({$log['nome']}) | {$sucesso} | IP: {$log['ip']}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro ao verificar logs: " . $e->getMessage() . "\n";
}

// 6. Diagnóstico final
echo "\n📊 DIAGNÓSTICO FINAL:\n";
echo "======================\n";

$problemas = [];

if (!isset($_SESSION['usuario_id'])) {
    $problemas[] = "Nenhum usuário logado";
} else {
    // Verificar consistência dos dados
    try {
        $stmt = $pdo->prepare("SELECT nome, usuario, is_admin FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['usuario_id']]);
        $dados_banco = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$dados_banco) {
            $problemas[] = "Usuário da sessão não existe no banco";
        } else {
            if ($_SESSION['usuario_nome'] !== $dados_banco['nome']) {
                $problemas[] = "Nome na sessão não confere com o banco";
            }
            if ($_SESSION['usuario_login'] !== $dados_banco['usuario']) {
                $problemas[] = "Login na sessão não confere com o banco";
            }
            if ($_SESSION['is_admin'] != $dados_banco['is_admin']) {
                $problemas[] = "Status admin na sessão não confere com o banco";
            }
        }
    } catch (Exception $e) {
        $problemas[] = "Erro ao verificar consistência: " . $e->getMessage();
    }
}

if (empty($problemas)) {
    echo "🎉 HEADER FUNCIONANDO CORRETAMENTE!\n";
    echo "✅ Dados da sessão consistentes com o banco\n";
    echo "✅ Usuário correto sendo exibido\n";
} else {
    echo "⚠️ PROBLEMAS IDENTIFICADOS:\n";
    foreach ($problemas as $problema) {
        echo "  - $problema\n";
    }
    
    echo "\n🔧 SOLUÇÕES RECOMENDADAS:\n";
    echo "  1. Fazer logout completo\n";
    echo "  2. Limpar cookies do navegador\n";
    echo "  3. Fazer novo login\n";
    echo "  4. Verificar se há múltiplas sessões ativas\n";
}

echo "\n🔗 TESTE MANUAL:\n";
echo "=================\n";
echo "1. Acesse: http://localhost:8080/logout.php\n";
echo "2. Limpe cookies do navegador\n";
echo "3. Acesse: http://localhost:8080/login.php\n";
echo "4. Faça login com: teste / teste123\n";
echo "5. Verifique se o header mostra 'Usuário Teste'\n";
echo "6. Navegue para outras páginas e verifique consistência\n";

?>
