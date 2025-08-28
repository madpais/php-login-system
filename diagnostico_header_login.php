<?php
/**
 * Diagnóstico do Header e Sistema de Login
 * Verifica por que o header não está mostrando o usuário logado
 */

require_once 'config.php';
iniciarSessaoSegura();

echo "🔍 DIAGNÓSTICO - HEADER E SISTEMA DE LOGIN\n";
echo "==========================================\n\n";

// 1. Verificar estado da sessão
echo "📋 1. ESTADO ATUAL DA SESSÃO:\n";
echo "==============================\n";
echo "Session ID: " . session_id() . "\n";
echo "Session status: " . session_status() . " (3=active)\n";

if (!empty($_SESSION)) {
    echo "\n📊 Dados na sessão:\n";
    foreach ($_SESSION as $key => $value) {
        if (is_string($value) || is_numeric($value) || is_bool($value)) {
            echo "  - $key: " . (is_bool($value) ? ($value ? 'true' : 'false') : $value) . "\n";
        } else {
            echo "  - $key: " . gettype($value) . "\n";
        }
    }
} else {
    echo "\n⚠️ Sessão vazia - usuário não está logado\n";
}

// 2. Fazer login de teste
echo "\n📋 2. FAZENDO LOGIN DE TESTE:\n";
echo "=============================\n";

try {
    $pdo = conectarBD();
    
    // Buscar usuário teste
    $stmt = $pdo->prepare("SELECT id, usuario, senha, nome, is_admin, ativo FROM usuarios WHERE usuario = 'teste'");
    $stmt->execute();
    $usuario_teste = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario_teste && password_verify('teste123', $usuario_teste['senha'])) {
        $_SESSION['usuario_id'] = $usuario_teste['id'];
        $_SESSION['usuario_nome'] = $usuario_teste['nome'];
        $_SESSION['usuario_login'] = $usuario_teste['usuario'];
        $_SESSION['is_admin'] = (bool)$usuario_teste['is_admin'];
        $_SESSION['login_time'] = time();
        
        echo "✅ Login realizado com sucesso\n";
        echo "✅ Dados salvos na sessão:\n";
        echo "  - usuario_id: " . $_SESSION['usuario_id'] . "\n";
        echo "  - usuario_nome: " . $_SESSION['usuario_nome'] . "\n";
        echo "  - usuario_login: " . $_SESSION['usuario_login'] . "\n";
        echo "  - is_admin: " . ($_SESSION['is_admin'] ? 'true' : 'false') . "\n";
        
        // Registrar login
        $stmt = $pdo->prepare("INSERT INTO logs_acesso (usuario_id, ip, sucesso) VALUES (?, ?, TRUE)");
        $stmt->execute([$usuario_teste['id'], '127.0.0.1']);
        echo "✅ Login registrado no banco\n";
        
    } else {
        echo "❌ Erro no login do usuário teste\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

// 3. Testar header_status.php
echo "\n📋 3. TESTANDO HEADER_STATUS.PHP:\n";
echo "==================================\n";

// Capturar output do header
ob_start();
include 'header_status.php';
$header_output = ob_get_clean();

echo "✅ Header incluído sem erros\n";
echo "📏 Tamanho do output: " . strlen($header_output) . " bytes\n";

// Verificar se contém elementos esperados
if (strpos($header_output, 'Você está logado') !== false) {
    echo "✅ Header mostra 'Você está logado'\n";
} else {
    echo "❌ Header NÃO mostra 'Você está logado'\n";
}

if (strpos($header_output, 'Fazer Login') !== false) {
    echo "⚠️ Header ainda mostra 'Fazer Login' (problema!)\n";
} else {
    echo "✅ Header não mostra 'Fazer Login'\n";
}

if (strpos($header_output, 'Deslogar') !== false) {
    echo "✅ Header mostra botão 'Deslogar'\n";
} else {
    echo "❌ Header NÃO mostra botão 'Deslogar'\n";
}

// 4. Verificar variáveis do header
echo "\n📋 4. VERIFICANDO VARIÁVEIS DO HEADER:\n";
echo "======================================\n";

// Simular o que o header faz
$usuario_logado = isset($_SESSION['usuario_id']);
$usuario_nome = '';
$usuario_login = '';
$is_admin = false;

echo "usuario_logado (inicial): " . ($usuario_logado ? 'true' : 'false') . "\n";

if ($usuario_logado) {
    try {
        $stmt = $pdo->prepare("SELECT nome, usuario, is_admin, ativo FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['usuario_id']]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user_data && $user_data['ativo']) {
            $usuario_nome = $user_data['nome'];
            $usuario_login = $user_data['usuario'];
            $is_admin = (bool)$user_data['is_admin'];
            
            echo "✅ Dados do banco recuperados:\n";
            echo "  - nome: $usuario_nome\n";
            echo "  - login: $usuario_login\n";
            echo "  - admin: " . ($is_admin ? 'true' : 'false') . "\n";
            echo "  - ativo: " . ($user_data['ativo'] ? 'true' : 'false') . "\n";
        } else {
            echo "❌ Usuário não encontrado ou inativo no banco\n";
            $usuario_logado = false;
        }
    } catch (Exception $e) {
        echo "❌ Erro ao buscar dados do banco: " . $e->getMessage() . "\n";
    }
}

echo "usuario_logado (final): " . ($usuario_logado ? 'true' : 'false') . "\n";

// 5. Criar página de teste do header
echo "\n📋 5. CRIANDO PÁGINA DE TESTE DO HEADER:\n";
echo "========================================\n";

$teste_header = '<?php
require_once "config.php";
iniciarSessaoSegura();

// Fazer login automático para teste
if (!isset($_SESSION["usuario_id"])) {
    try {
        $pdo = conectarBD();
        $stmt = $pdo->prepare("SELECT id, usuario, senha, nome, is_admin FROM usuarios WHERE usuario = \"teste\"");
        $stmt->execute();
        $usuario_teste = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario_teste && password_verify("teste123", $usuario_teste["senha"])) {
            $_SESSION["usuario_id"] = $usuario_teste["id"];
            $_SESSION["usuario_nome"] = $usuario_teste["nome"];
            $_SESSION["usuario_login"] = $usuario_teste["usuario"];
            $_SESSION["is_admin"] = (bool)$usuario_teste["is_admin"];
            $_SESSION["login_time"] = time();
        }
    } catch (Exception $e) {
        // Ignorar erro
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste do Header - DayDreaming</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        .content {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .debug-info {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .status-ok { color: #28a745; }
        .status-error { color: #dc3545; }
        .status-warning { color: #ffc107; }
    </style>
</head>
<body>
    <!-- Header de Status -->
    <?php include "header_status.php"; ?>
    
    <div class="content">
        <div class="debug-info">
            <h1>🔍 Teste do Header de Login</h1>
            
            <h3>📊 Estado da Sessão:</h3>
            <ul>
                <li><strong>Session ID:</strong> <?= session_id() ?></li>
                <li><strong>Usuário logado:</strong> 
                    <span class="<?= isset($_SESSION["usuario_id"]) ? "status-ok" : "status-error" ?>">
                        <?= isset($_SESSION["usuario_id"]) ? "✅ Sim" : "❌ Não" ?>
                    </span>
                </li>
                <?php if (isset($_SESSION["usuario_id"])): ?>
                    <li><strong>ID:</strong> <?= $_SESSION["usuario_id"] ?></li>
                    <li><strong>Nome:</strong> <?= $_SESSION["usuario_nome"] ?? "N/A" ?></li>
                    <li><strong>Login:</strong> <?= $_SESSION["usuario_login"] ?? "N/A" ?></li>
                    <li><strong>Admin:</strong> <?= isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] ? "Sim" : "Não" ?></li>
                <?php endif; ?>
            </ul>
            
            <h3>🧪 Testes de Funcionalidade:</h3>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="login.php" style="padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;">
                    🔐 Ir para Login
                </a>
                <a href="logout.php" style="padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; border-radius: 5px;">
                    🚪 Fazer Logout
                </a>
                <a href="pagina_usuario.php" style="padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;">
                    👤 Meu Perfil
                </a>
                <a href="index.php" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">
                    🏠 Página Inicial
                </a>
            </div>
            
            <h3>📝 Instruções de Teste:</h3>
            <ol>
                <li>Verifique se o header mostra "✅ Você está logado"</li>
                <li>Verifique se aparece o nome do usuário no dropdown</li>
                <li>Verifique se o botão direito mostra "🚪 Deslogar" (não "🔑 Fazer Login")</li>
                <li>Teste o dropdown do usuário clicando no nome</li>
                <li>Teste os links do dropdown</li>
                <li>Teste o logout e verifique se volta ao estado deslogado</li>
            </ol>
            
            <div style="background: #e3f2fd; padding: 15px; border-radius: 5px; margin-top: 20px;">
                <h4>🔧 Se o header não estiver funcionando:</h4>
                <ul>
                    <li>Limpe o cache do navegador (Ctrl+Shift+Del)</li>
                    <li>Use modo incógnito</li>
                    <li>Verifique se não há erros no console do navegador (F12)</li>
                    <li>Recarregue a página (F5)</li>
                </ul>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-refresh a cada 30 segundos para monitorar mudanças
        setTimeout(function() {
            console.log("Auto-refresh em 30 segundos...");
            location.reload();
        }, 30000);
        
        // Log do estado atual
        console.log("Estado da sessão:", {
            logado: <?= isset($_SESSION["usuario_id"]) ? "true" : "false" ?>,
            usuario_id: "<?= $_SESSION["usuario_id"] ?? "N/A" ?>",
            usuario_nome: "<?= $_SESSION["usuario_nome"] ?? "N/A" ?>",
            session_id: "<?= session_id() ?>"
        });
    </script>
</body>
</html>';

if (file_put_contents('teste_header_login.php', $teste_header)) {
    echo "✅ Página de teste criada: teste_header_login.php\n";
}

// 6. Verificar se há conflitos de sessão
echo "\n📋 6. VERIFICANDO CONFLITOS DE SESSÃO:\n";
echo "=======================================\n";

// Verificar se há múltiplas chamadas de session_start
$arquivos_verificar = ['index.php', 'login.php', 'header_status.php', 'config.php'];

foreach ($arquivos_verificar as $arquivo) {
    if (file_exists($arquivo)) {
        $conteudo = file_get_contents($arquivo);
        $count_session_start = substr_count($conteudo, 'session_start');
        $count_iniciar_sessao = substr_count($conteudo, 'iniciarSessaoSegura');
        
        echo "📄 $arquivo:\n";
        echo "  - session_start(): $count_session_start\n";
        echo "  - iniciarSessaoSegura(): $count_iniciar_sessao\n";
        
        if ($count_session_start > 0) {
            echo "  ⚠️ Contém session_start() - pode causar conflito\n";
        }
    }
}

} catch (Exception $e) {
    echo "❌ Erro geral: " . $e->getMessage() . "\n";
}

// 7. Resumo e soluções
echo "\n📊 RESUMO DO DIAGNÓSTICO:\n";
echo "==========================\n";

if (isset($_SESSION['usuario_id'])) {
    echo "✅ Usuário está logado na sessão\n";
    echo "✅ Dados da sessão parecem corretos\n";
    echo "⚠️ Se o header não mostra o usuário logado, pode ser:\n";
    echo "  1. Cache do navegador\n";
    echo "  2. JavaScript não carregando\n";
    echo "  3. CSS sobrescrevendo elementos\n";
    echo "  4. Erro na inclusão do header\n";
} else {
    echo "❌ Usuário não está logado na sessão\n";
    echo "🔧 Faça login primeiro em: http://localhost:8080/login.php\n";
}

echo "\n🔗 PRÓXIMOS PASSOS:\n";
echo "===================\n";
echo "1. Acesse: http://localhost:8080/teste_header_login.php\n";
echo "2. Verifique se o header mostra o usuário logado\n";
echo "3. Se não funcionar, limpe cache do navegador\n";
echo "4. Teste em modo incógnito\n";
echo "5. Verifique console do navegador (F12)\n";

?>
