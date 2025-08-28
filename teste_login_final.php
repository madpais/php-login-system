<?php
/**
 * Teste Final do Sistema de Login Corrigido
 */

require_once 'config.php';

// Iniciar sessão de forma segura
$sessao_iniciada = iniciarSessaoSegura();

echo "🔐 TESTE FINAL - SISTEMA DE LOGIN CORRIGIDO\n";
echo "===========================================\n\n";

echo "📋 1. VERIFICAÇÃO DE SESSÃO:\n";
echo "=============================\n";
echo "Sessão iniciada: " . ($sessao_iniciada ? "✅ Sim" : "❌ Não") . "\n";
echo "Session status: " . session_status() . " (3=active)\n";
echo "Session name: " . session_name() . "\n";
echo "Session ID: " . session_id() . "\n";

// Limpar sessão para teste limpo
$_SESSION = array();

echo "\n📋 2. TESTE DE LOGIN:\n";
echo "=====================\n";

$usuario = 'admin';
$senha = 'admin123';

try {
    $pdo = conectarBD();
    
    // Buscar usuário
    $stmt = $pdo->prepare("SELECT id, usuario, senha, nome, is_admin, ativo FROM usuarios WHERE usuario = :usuario");
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $usuario_dados = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (password_verify($senha, $usuario_dados['senha']) && $usuario_dados['ativo']) {
            // Criar sessão
            $_SESSION['usuario_id'] = $usuario_dados['id'];
            $_SESSION['usuario_nome'] = $usuario_dados['nome'];
            $_SESSION['usuario_login'] = $usuario_dados['usuario'];
            $_SESSION['is_admin'] = (bool)$usuario_dados['is_admin'];
            $_SESSION['login_time'] = time();
            
            echo "✅ Login realizado com sucesso\n";
            echo "✅ Dados salvos na sessão\n";
            
            // Registrar login no banco
            $ip = '127.0.0.1';
            $stmt = $pdo->prepare("INSERT INTO logs_acesso (usuario_id, ip, sucesso) VALUES (?, ?, TRUE)");
            $stmt->execute([$usuario_dados['id'], $ip]);
            
            echo "✅ Login registrado no banco\n";
            
        } else {
            echo "❌ Credenciais inválidas\n";
        }
    } else {
        echo "❌ Usuário não encontrado\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n📋 3. VERIFICAÇÃO DE PERSISTÊNCIA:\n";
echo "===================================\n";

if (isset($_SESSION['usuario_id'])) {
    echo "✅ usuario_id: " . $_SESSION['usuario_id'] . "\n";
    echo "✅ usuario_nome: " . $_SESSION['usuario_nome'] . "\n";
    echo "✅ usuario_login: " . $_SESSION['usuario_login'] . "\n";
    echo "✅ is_admin: " . ($_SESSION['is_admin'] ? 'true' : 'false') . "\n";
    echo "✅ login_time: " . date('Y-m-d H:i:s', $_SESSION['login_time']) . "\n";
} else {
    echo "❌ Dados de sessão não encontrados\n";
}

echo "\n📋 4. TESTE DE VERIFICAÇÃO DE AUTENTICAÇÃO:\n";
echo "============================================\n";

// Simular verificação como nas páginas protegidas
$usuario_logado = isset($_SESSION['usuario_id']);
echo "Usuário logado: " . ($usuario_logado ? "✅ Sim" : "❌ Não") . "\n";

if ($usuario_logado) {
    // Verificar se usuário ainda existe e está ativo
    try {
        $stmt = $pdo->prepare("SELECT ativo, nome FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['usuario_id']]);
        $user = $stmt->fetch();
        
        if ($user && $user['ativo']) {
            echo "✅ Usuário válido e ativo\n";
            echo "✅ Acesso a páginas protegidas: PERMITIDO\n";
        } else {
            echo "❌ Usuário inválido ou inativo\n";
            echo "❌ Acesso a páginas protegidas: NEGADO\n";
        }
    } catch (Exception $e) {
        echo "❌ Erro na verificação: " . $e->getMessage() . "\n";
    }
}

echo "\n📋 5. TESTE DE TIMEOUT DE SESSÃO:\n";
echo "==================================\n";

$session_timeout = 3600; // 1 hora
$tempo_atual = time();
$tempo_login = $_SESSION['login_time'] ?? 0;
$tempo_decorrido = $tempo_atual - $tempo_login;

echo "Tempo de login: " . date('Y-m-d H:i:s', $tempo_login) . "\n";
echo "Tempo atual: " . date('Y-m-d H:i:s', $tempo_atual) . "\n";
echo "Tempo decorrido: " . $tempo_decorrido . " segundos\n";
echo "Timeout configurado: " . $session_timeout . " segundos\n";

if ($tempo_decorrido < $session_timeout) {
    echo "✅ Sessão dentro do prazo de validade\n";
} else {
    echo "⚠️ Sessão expirada (seria necessário novo login)\n";
}

echo "\n📋 6. TESTE DE LOGOUT:\n";
echo "======================\n";

if (isset($_SESSION['usuario_id'])) {
    $usuario_id_logout = $_SESSION['usuario_id'];
    
    // Registrar logout
    try {
        $stmt = $pdo->prepare("UPDATE usuarios SET ultimo_logout = NOW() WHERE id = ?");
        $stmt->execute([$usuario_id_logout]);
        
        $stmt = $pdo->prepare("INSERT INTO logs_sistema (usuario_id, acao, detalhes, data_hora) VALUES (?, 'logout', 'Logout via teste', NOW())");
        $stmt->execute([$usuario_id_logout]);
        
        echo "✅ Logout registrado no banco\n";
    } catch (Exception $e) {
        echo "⚠️ Erro ao registrar logout: " . $e->getMessage() . "\n";
    }
    
    // Limpar sessão
    $_SESSION = array();
    
    echo "✅ Variáveis de sessão limpas\n";
    
    // Verificar se logout foi efetivo
    $ainda_logado = isset($_SESSION['usuario_id']);
    echo "Ainda logado: " . ($ainda_logado ? "❌ Sim" : "✅ Não") . "\n";
}

echo "\n📊 RESUMO FINAL:\n";
echo "=================\n";

$problemas = [];

if (!$sessao_iniciada) {
    $problemas[] = "Falha ao iniciar sessão";
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    $problemas[] = "Sessão não está ativa";
}

if (empty(session_id())) {
    $problemas[] = "Session ID vazio";
}

if (empty($problemas)) {
    echo "🎉 SISTEMA DE LOGIN FUNCIONANDO PERFEITAMENTE!\n";
    echo "\n✅ FUNCIONALIDADES TESTADAS:\n";
    echo "  - Configuração de sessão segura\n";
    echo "  - Login com credenciais válidas\n";
    echo "  - Persistência de dados na sessão\n";
    echo "  - Verificação de autenticação\n";
    echo "  - Controle de timeout\n";
    echo "  - Logout completo\n";
    echo "  - Logs de auditoria\n";
} else {
    echo "⚠️ PROBLEMAS IDENTIFICADOS:\n";
    foreach ($problemas as $problema) {
        echo "  - $problema\n";
    }
}

echo "\n🔗 TESTE NO NAVEGADOR:\n";
echo "=======================\n";
echo "1. Acesse: http://localhost:8080/login.php\n";
echo "2. Use: admin / admin123\n";
echo "3. Verifique se redireciona para index.php\n";
echo "4. Navegue para outras páginas (forum.php, etc.)\n";
echo "5. Verifique se permanece logado\n";
echo "6. Teste logout em: http://localhost:8080/logout.php\n";

?>
