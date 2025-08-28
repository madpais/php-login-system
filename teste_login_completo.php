<?php
/**
 * Teste Completo do Sistema de Login
 * Verifica todas as funcionalidades de autenticação
 */

echo "🔐 TESTE COMPLETO DO SISTEMA DE LOGIN\n";
echo "=====================================\n\n";

// 1. Configurar sessão corretamente
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0);
    ini_set('session.gc_maxlifetime', 3600);
    session_name('DAYDREAMING_SESSION');
    session_start();
}

require_once 'config.php';

echo "📋 1. CONFIGURAÇÃO DE SESSÃO:\n";
echo "==============================\n";
echo "Session status: " . session_status() . " (3=active)\n";
echo "Session name: " . session_name() . "\n";
echo "Session ID: " . session_id() . "\n";

// 2. Limpar sessão anterior
echo "\n📋 2. LIMPANDO SESSÃO ANTERIOR:\n";
echo "================================\n";
$_SESSION = array();
echo "✅ Sessão limpa\n";

// 3. Testar login com credenciais corretas
echo "\n📋 3. TESTE DE LOGIN - ADMIN:\n";
echo "==============================\n";

$usuario = 'admin';
$senha = 'admin123';

try {
    $pdo = conectarBD();
    echo "✅ Conectado ao banco de dados\n";
    
    // Buscar usuário
    $stmt = $pdo->prepare("SELECT id, usuario, senha, nome, is_admin, ativo FROM usuarios WHERE usuario = :usuario");
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $usuario_dados = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "✅ Usuário encontrado: " . $usuario_dados['nome'] . "\n";
        
        // Verificar senha
        if (password_verify($senha, $usuario_dados['senha'])) {
            echo "✅ Senha correta\n";
            
            // Verificar se está ativo
            if ($usuario_dados['ativo']) {
                echo "✅ Usuário ativo\n";
                
                // Criar sessão
                $_SESSION['usuario_id'] = $usuario_dados['id'];
                $_SESSION['usuario_nome'] = $usuario_dados['nome'];
                $_SESSION['usuario_login'] = $usuario_dados['usuario'];
                $_SESSION['is_admin'] = (bool)$usuario_dados['is_admin'];
                $_SESSION['login_time'] = time();
                
                echo "✅ Sessão criada com sucesso\n";
                echo "  - ID: " . $_SESSION['usuario_id'] . "\n";
                echo "  - Nome: " . $_SESSION['usuario_nome'] . "\n";
                echo "  - Login: " . $_SESSION['usuario_login'] . "\n";
                echo "  - Admin: " . ($_SESSION['is_admin'] ? 'Sim' : 'Não') . "\n";
                
            } else {
                echo "❌ Usuário inativo/banido\n";
            }
        } else {
            echo "❌ Senha incorreta\n";
        }
    } else {
        echo "❌ Usuário não encontrado\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

// 4. Testar verificação de login
echo "\n📋 4. TESTE DE VERIFICAÇÃO DE LOGIN:\n";
echo "=====================================\n";

$usuario_logado = isset($_SESSION['usuario_id']);
echo "Usuário logado: " . ($usuario_logado ? "✅ Sim" : "❌ Não") . "\n";

if ($usuario_logado) {
    echo "Dados da sessão:\n";
    echo "  - usuario_id: " . ($_SESSION['usuario_id'] ?? 'N/A') . "\n";
    echo "  - usuario_nome: " . ($_SESSION['usuario_nome'] ?? 'N/A') . "\n";
    echo "  - usuario_login: " . ($_SESSION['usuario_login'] ?? 'N/A') . "\n";
    echo "  - is_admin: " . (isset($_SESSION['is_admin']) ? ($_SESSION['is_admin'] ? 'true' : 'false') : 'N/A') . "\n";
}

// 5. Testar persistência da sessão
echo "\n📋 5. TESTE DE PERSISTÊNCIA:\n";
echo "=============================\n";

// Simular nova requisição
$session_id_antes = session_id();
echo "Session ID antes: $session_id_antes\n";

// Verificar se dados persistem
if (isset($_SESSION['usuario_id'])) {
    echo "✅ Dados persistem na sessão\n";
    
    // Testar acesso a página protegida (simulação)
    echo "✅ Acesso a página protegida: PERMITIDO\n";
} else {
    echo "❌ Dados não persistem na sessão\n";
    echo "❌ Acesso a página protegida: NEGADO\n";
}

// 6. Testar logout
echo "\n📋 6. TESTE DE LOGOUT:\n";
echo "=======================\n";

if (isset($_SESSION['usuario_id'])) {
    $usuario_id_logout = $_SESSION['usuario_id'];
    
    // Registrar logout no banco
    try {
        $stmt = $pdo->prepare("UPDATE usuarios SET ultimo_logout = NOW() WHERE id = ?");
        $stmt->execute([$usuario_id_logout]);
        echo "✅ Logout registrado no banco\n";
    } catch (Exception $e) {
        echo "⚠️ Erro ao registrar logout: " . $e->getMessage() . "\n";
    }
    
    // Limpar sessão
    $_SESSION = array();
    
    // Destruir cookie de sessão
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    echo "✅ Sessão limpa\n";
    echo "✅ Cookie de sessão removido\n";
    
    // Verificar se logout foi efetivo
    $usuario_logado_apos = isset($_SESSION['usuario_id']);
    echo "Usuário logado após logout: " . ($usuario_logado_apos ? "❌ Ainda logado" : "✅ Deslogado") . "\n";
    
} else {
    echo "❌ Nenhum usuário logado para fazer logout\n";
}

// 7. Testar login novamente após logout
echo "\n📋 7. TESTE DE NOVO LOGIN APÓS LOGOUT:\n";
echo "=======================================\n";

// Tentar login novamente
$stmt = $pdo->prepare("SELECT id, usuario, senha, nome, is_admin, ativo FROM usuarios WHERE usuario = :usuario");
$stmt->bindParam(':usuario', $usuario);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $usuario_dados = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (password_verify($senha, $usuario_dados['senha']) && $usuario_dados['ativo']) {
        $_SESSION['usuario_id'] = $usuario_dados['id'];
        $_SESSION['usuario_nome'] = $usuario_dados['nome'];
        $_SESSION['usuario_login'] = $usuario_dados['usuario'];
        $_SESSION['is_admin'] = (bool)$usuario_dados['is_admin'];
        $_SESSION['login_time'] = time();
        
        echo "✅ Novo login realizado com sucesso\n";
        echo "✅ Sessão recriada\n";
    } else {
        echo "❌ Falha no novo login\n";
    }
}

// 8. Resumo final
echo "\n📊 RESUMO FINAL:\n";
echo "=================\n";

$testes_passaram = 0;
$total_testes = 7;

// Verificar cada teste
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "✅ Configuração de sessão: OK\n";
    $testes_passaram++;
} else {
    echo "❌ Configuração de sessão: FALHOU\n";
}

if (isset($_SESSION['usuario_id'])) {
    echo "✅ Login funcional: OK\n";
    $testes_passaram++;
} else {
    echo "❌ Login funcional: FALHOU\n";
}

if (isset($_SESSION['usuario_nome'])) {
    echo "✅ Dados de sessão: OK\n";
    $testes_passaram++;
} else {
    echo "❌ Dados de sessão: FALHOU\n";
}

echo "\n🎯 RESULTADO: $testes_passaram/$total_testes testes passaram\n";

if ($testes_passaram === $total_testes) {
    echo "🎉 SISTEMA DE LOGIN FUNCIONANDO PERFEITAMENTE!\n";
} else {
    echo "⚠️ SISTEMA DE LOGIN PRECISA DE CORREÇÕES\n";
}

echo "\n🔗 PRÓXIMOS PASSOS:\n";
echo "===================\n";
echo "1. Acesse: http://localhost:8080/login.php\n";
echo "2. Use: admin / admin123\n";
echo "3. Verifique se permanece logado ao navegar\n";
echo "4. Teste logout e novo login\n";

?>
