<?php
/**
 * Script para limpar completamente todas as sessões e cache
 * Resolve problemas de usuário incorreto no header
 */

echo "🧹 LIMPEZA COMPLETA DE SESSÕES E CACHE\n";
echo "======================================\n\n";

// 1. Iniciar sessão para poder limpá-la
require_once 'config.php';

echo "📋 1. INICIANDO LIMPEZA DE SESSÃO:\n";
echo "===================================\n";

// Verificar se há sessão ativa
if (session_status() == PHP_SESSION_NONE) {
    session_name('DAYDREAMING_SESSION');
    session_start();
    echo "✅ Sessão iniciada para limpeza\n";
} else {
    echo "✅ Sessão já estava ativa\n";
}

echo "Session ID antes da limpeza: " . session_id() . "\n";

// Mostrar dados atuais da sessão
if (!empty($_SESSION)) {
    echo "Dados atuais na sessão:\n";
    foreach ($_SESSION as $key => $value) {
        if (is_string($value) || is_numeric($value)) {
            echo "  - $key: $value\n";
        } else {
            echo "  - $key: " . gettype($value) . "\n";
        }
    }
} else {
    echo "Sessão vazia\n";
}

// 2. Limpar completamente a sessão
echo "\n📋 2. LIMPANDO SESSÃO COMPLETAMENTE:\n";
echo "====================================\n";

// Destruir todas as variáveis de sessão
$_SESSION = array();

// Limpar cookie de sessão
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
    echo "✅ Cookie de sessão removido\n";
}

// Destruir a sessão
session_destroy();
echo "✅ Sessão destruída\n";

// 3. Criar nova sessão limpa
echo "\n📋 3. CRIANDO NOVA SESSÃO LIMPA:\n";
echo "=================================\n";

// Iniciar nova sessão
session_name('DAYDREAMING_SESSION');
session_start();
echo "✅ Nova sessão criada\n";
echo "Novo Session ID: " . session_id() . "\n";

// Verificar se está realmente limpa
if (empty($_SESSION)) {
    echo "✅ Sessão está completamente limpa\n";
} else {
    echo "⚠️ Ainda há dados na sessão:\n";
    foreach ($_SESSION as $key => $value) {
        echo "  - $key: $value\n";
    }
}

// 4. Testar login com usuário teste
echo "\n📋 4. TESTE DE LOGIN LIMPO - USUÁRIO TESTE:\n";
echo "============================================\n";

try {
    $pdo = conectarBD();
    
    // Buscar usuário teste
    $stmt = $pdo->prepare("SELECT id, usuario, senha, nome, is_admin, ativo FROM usuarios WHERE usuario = 'teste'");
    $stmt->execute();
    $usuario_teste = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario_teste && password_verify('teste123', $usuario_teste['senha'])) {
        // Fazer login limpo
        $_SESSION['usuario_id'] = $usuario_teste['id'];
        $_SESSION['usuario_nome'] = $usuario_teste['nome'];
        $_SESSION['usuario_login'] = $usuario_teste['usuario'];
        $_SESSION['is_admin'] = (bool)$usuario_teste['is_admin'];
        $_SESSION['login_time'] = time();
        
        echo "✅ Login realizado com sucesso\n";
        echo "✅ Dados salvos na sessão:\n";
        echo "  - ID: " . $_SESSION['usuario_id'] . "\n";
        echo "  - Nome: " . $_SESSION['usuario_nome'] . "\n";
        echo "  - Login: " . $_SESSION['usuario_login'] . "\n";
        echo "  - Admin: " . ($_SESSION['is_admin'] ? 'Sim' : 'Não') . "\n";
        
        // Registrar login no banco
        $ip = '127.0.0.1';
        $stmt = $pdo->prepare("INSERT INTO logs_acesso (usuario_id, ip, sucesso) VALUES (?, ?, TRUE)");
        $stmt->execute([$usuario_teste['id'], $ip]);
        echo "✅ Login registrado no banco\n";
        
    } else {
        echo "❌ Erro no login do usuário teste\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

// 5. Verificar header após login limpo
echo "\n📋 5. VERIFICAÇÃO DO HEADER APÓS LOGIN LIMPO:\n";
echo "==============================================\n";

// Simular exatamente o que o header faz
$usuario_logado = isset($_SESSION['usuario_id']);
$usuario_nome = '';
$usuario_login = '';

if ($usuario_logado) {
    $usuario_nome = $_SESSION['usuario_nome'] ?? '';
    $usuario_login = $_SESSION['usuario_login'] ?? '';
}

echo "Status do header:\n";
echo "  - Usuário logado: " . ($usuario_logado ? 'Sim' : 'Não') . "\n";
echo "  - Nome que será exibido: '$usuario_nome'\n";
echo "  - Login que será exibido: '$usuario_login'\n";

// Verificar se os dados estão corretos
if ($usuario_nome === 'Usuário Teste' && $usuario_login === 'teste') {
    echo "✅ Header exibirá o usuário correto\n";
} else {
    echo "❌ Header ainda está incorreto\n";
}

// 6. Gerar headers para evitar cache
echo "\n📋 6. CONFIGURANDO HEADERS ANTI-CACHE:\n";
echo "=======================================\n";

// Headers para evitar cache do navegador
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
echo "✅ Headers anti-cache configurados\n";

// 7. Criar arquivo de teste para verificação no navegador
echo "\n📋 7. CRIANDO ARQUIVO DE TESTE:\n";
echo "================================\n";

$teste_header_content = '<?php
require_once "config.php";
iniciarSessaoSegura();

// Headers anti-cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$usuario_logado = isset($_SESSION["usuario_id"]);
$usuario_nome = $_SESSION["usuario_nome"] ?? "";
$usuario_login = $_SESSION["usuario_login"] ?? "";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teste de Header</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
</head>
<body>
    <h1>Teste de Header - Verificação de Usuário</h1>
    
    <div style="background: #f0f0f0; padding: 20px; margin: 20px 0; border-radius: 5px;">
        <h2>Dados da Sessão:</h2>
        <p><strong>Usuário logado:</strong> ' . ($usuario_logado ? 'Sim' : 'Não') . '</p>
        <p><strong>Nome:</strong> ' . htmlspecialchars($usuario_nome) . '</p>
        <p><strong>Login:</strong> ' . htmlspecialchars($usuario_login) . '</p>
        <p><strong>Session ID:</strong> ' . session_id() . '</p>
        <p><strong>Timestamp:</strong> ' . date('Y-m-d H:i:s') . '</p>
    </div>
    
    <div style="background: #e8f5e8; padding: 20px; margin: 20px 0; border-radius: 5px;">
        <h2>Teste de Navegação:</h2>
        <p><a href="index.php">Ir para Index</a></p>
        <p><a href="forum.php">Ir para Fórum</a></p>
        <p><a href="paises/eua.php">Ir para EUA</a></p>
        <p><a href="logout.php">Fazer Logout</a></p>
    </div>
    
    <script>
        // Recarregar a página a cada 5 segundos para verificar mudanças
        setTimeout(function() {
            location.reload();
        }, 5000);
    </script>
</body>
</html>';

if (file_put_contents('teste_header_usuario.php', $teste_header_content)) {
    echo "✅ Arquivo de teste criado: teste_header_usuario.php\n";
} else {
    echo "❌ Erro ao criar arquivo de teste\n";
}

// 8. Resumo final
echo "\n📊 RESUMO DA LIMPEZA:\n";
echo "======================\n";
echo "✅ Sessão anterior destruída completamente\n";
echo "✅ Nova sessão criada\n";
echo "✅ Login teste realizado\n";
echo "✅ Headers anti-cache configurados\n";
echo "✅ Arquivo de teste criado\n";

echo "\n🔗 PRÓXIMOS PASSOS:\n";
echo "====================\n";
echo "1. Feche TODOS os navegadores\n";
echo "2. Limpe cache e cookies do navegador\n";
echo "3. Abra novo navegador\n";
echo "4. Acesse: http://localhost:8080/teste_header_usuario.php\n";
echo "5. Verifique se mostra 'Usuário Teste'\n";
echo "6. Navegue pelas páginas e observe se muda\n";
echo "7. Se ainda houver problema, execute este script novamente\n";

echo "\n⚠️ IMPORTANTE:\n";
echo "===============\n";
echo "- Sempre feche o navegador completamente antes de testar\n";
echo "- Limpe cache e cookies entre testes\n";
echo "- Use modo incógnito para testes isolados\n";
echo "- Verifique se não há múltiplas abas abertas\n";

?>
