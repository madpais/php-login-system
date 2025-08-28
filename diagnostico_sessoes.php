<?php
/**
 * Diagnóstico Completo do Sistema de Sessões
 * Identifica problemas de autenticação e persistência de login
 */

echo "🔍 DIAGNÓSTICO COMPLETO DO SISTEMA DE SESSÕES\n";
echo "=============================================\n\n";

// 1. Verificar configurações de sessão
echo "📋 1. CONFIGURAÇÕES DE SESSÃO:\n";
echo "==============================\n";
echo "Session status: " . session_status() . " (1=disabled, 2=none, 3=active)\n";
echo "Session name: " . session_name() . "\n";
echo "Session ID: " . (session_status() === PHP_SESSION_ACTIVE ? session_id() : 'N/A') . "\n";
echo "Session save path: " . session_save_path() . "\n";
echo "Session cookie lifetime: " . ini_get('session.cookie_lifetime') . "\n";
echo "Session gc maxlifetime: " . ini_get('session.gc_maxlifetime') . "\n";
echo "Session use cookies: " . ini_get('session.use_cookies') . "\n";
echo "Session cookie httponly: " . ini_get('session.cookie_httponly') . "\n";
echo "Session cookie secure: " . ini_get('session.cookie_secure') . "\n";

// 2. Testar início de sessão
echo "\n📋 2. TESTE DE INÍCIO DE SESSÃO:\n";
echo "=================================\n";

if (session_status() === PHP_SESSION_NONE) {
    echo "Iniciando sessão...\n";
    session_start();
    echo "✅ Sessão iniciada com sucesso\n";
} else {
    echo "✅ Sessão já estava ativa\n";
}

echo "Session ID após start: " . session_id() . "\n";

// 3. Testar variáveis de sessão
echo "\n📋 3. VARIÁVEIS DE SESSÃO ATUAIS:\n";
echo "==================================\n";

if (empty($_SESSION)) {
    echo "❌ Nenhuma variável de sessão encontrada\n";
} else {
    echo "✅ Variáveis de sessão encontradas:\n";
    foreach ($_SESSION as $key => $value) {
        if (is_string($value) || is_numeric($value)) {
            echo "  - $key: $value\n";
        } else {
            echo "  - $key: " . gettype($value) . "\n";
        }
    }
}

// 4. Testar login simulado
echo "\n📋 4. TESTE DE LOGIN SIMULADO:\n";
echo "===============================\n";

require_once 'config.php';

try {
    $pdo = conectarBD();
    echo "✅ Conectado ao banco de dados\n";
    
    // Buscar usuário admin
    $stmt = $pdo->prepare("SELECT id, usuario, nome, is_admin, ativo FROM usuarios WHERE usuario = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "✅ Usuário admin encontrado:\n";
        echo "  - ID: " . $admin['id'] . "\n";
        echo "  - Usuário: " . $admin['usuario'] . "\n";
        echo "  - Nome: " . $admin['nome'] . "\n";
        echo "  - Admin: " . ($admin['is_admin'] ? 'Sim' : 'Não') . "\n";
        echo "  - Ativo: " . ($admin['ativo'] ? 'Sim' : 'Não') . "\n";
        
        // Simular login
        $_SESSION['usuario_id'] = $admin['id'];
        $_SESSION['usuario_nome'] = $admin['nome'];
        $_SESSION['usuario_login'] = $admin['usuario'];
        $_SESSION['is_admin'] = (bool)$admin['is_admin'];
        $_SESSION['login_time'] = time();
        
        echo "✅ Sessão de login simulada criada\n";
        
    } else {
        echo "❌ Usuário admin não encontrado\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro ao conectar com banco: " . $e->getMessage() . "\n";
}

// 5. Verificar persistência da sessão
echo "\n📋 5. VERIFICAÇÃO DE PERSISTÊNCIA:\n";
echo "===================================\n";

if (isset($_SESSION['usuario_id'])) {
    echo "✅ usuario_id: " . $_SESSION['usuario_id'] . "\n";
} else {
    echo "❌ usuario_id não encontrado\n";
}

if (isset($_SESSION['usuario_nome'])) {
    echo "✅ usuario_nome: " . $_SESSION['usuario_nome'] . "\n";
} else {
    echo "❌ usuario_nome não encontrado\n";
}

if (isset($_SESSION['usuario_login'])) {
    echo "✅ usuario_login: " . $_SESSION['usuario_login'] . "\n";
} else {
    echo "❌ usuario_login não encontrado\n";
}

if (isset($_SESSION['is_admin'])) {
    echo "✅ is_admin: " . ($_SESSION['is_admin'] ? 'true' : 'false') . "\n";
} else {
    echo "❌ is_admin não encontrado\n";
}

// 6. Testar funções de verificação
echo "\n📋 6. TESTE DAS FUNÇÕES DE VERIFICAÇÃO:\n";
echo "========================================\n";

// Incluir arquivo de verificação
if (file_exists('verificar_auth.php')) {
    echo "✅ Arquivo verificar_auth.php encontrado\n";
    
    // Testar função verificarLogin sem redirecionamento
    $usuario_logado = isset($_SESSION['usuario_id']);
    echo "Resultado verificarLogin: " . ($usuario_logado ? "✅ Logado" : "❌ Não logado") . "\n";
    
} else {
    echo "❌ Arquivo verificar_auth.php não encontrado\n";
}

// 7. Verificar cookies
echo "\n📋 7. VERIFICAÇÃO DE COOKIES:\n";
echo "==============================\n";

if (isset($_COOKIE[session_name()])) {
    echo "✅ Cookie de sessão encontrado: " . $_COOKIE[session_name()] . "\n";
} else {
    echo "❌ Cookie de sessão não encontrado\n";
}

echo "Todos os cookies:\n";
if (empty($_COOKIE)) {
    echo "❌ Nenhum cookie encontrado\n";
} else {
    foreach ($_COOKIE as $name => $value) {
        echo "  - $name: $value\n";
    }
}

// 8. Diagnóstico final
echo "\n📋 8. DIAGNÓSTICO FINAL:\n";
echo "=========================\n";

$problemas = [];

if (session_status() !== PHP_SESSION_ACTIVE) {
    $problemas[] = "Sessão não está ativa";
}

if (empty($_SESSION)) {
    $problemas[] = "Nenhuma variável de sessão definida";
}

if (!isset($_SESSION['usuario_id'])) {
    $problemas[] = "usuario_id não está na sessão";
}

if (!isset($_COOKIE[session_name()])) {
    $problemas[] = "Cookie de sessão não encontrado";
}

if (empty($problemas)) {
    echo "🎉 SISTEMA DE SESSÕES FUNCIONANDO CORRETAMENTE!\n";
} else {
    echo "⚠️ PROBLEMAS IDENTIFICADOS:\n";
    foreach ($problemas as $problema) {
        echo "  - $problema\n";
    }
}

echo "\n📊 RESUMO:\n";
echo "==========\n";
echo "Status da sessão: " . (session_status() === PHP_SESSION_ACTIVE ? "✅ Ativa" : "❌ Inativa") . "\n";
echo "Variáveis de sessão: " . (empty($_SESSION) ? "❌ Vazias" : "✅ Presentes") . "\n";
echo "Cookie de sessão: " . (isset($_COOKIE[session_name()]) ? "✅ Presente" : "❌ Ausente") . "\n";
echo "Login simulado: " . (isset($_SESSION['usuario_id']) ? "✅ Sucesso" : "❌ Falhou") . "\n";

?>
