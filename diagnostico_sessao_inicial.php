<?php
/**
 * Diagnóstico da Sessão Inicial
 * Verifica por que o projeto abriu já logado
 */

require_once 'config.php';
iniciarSessaoSegura();

echo "🔍 DIAGNÓSTICO - SESSÃO INICIAL\n";
echo "===============================\n\n";

// 1. Verificar estado da sessão
echo "📋 1. ESTADO ATUAL DA SESSÃO:\n";
echo "==============================\n";
echo "Session ID: " . session_id() . "\n";
echo "Session status: " . session_status() . " (3=active)\n";
echo "Session name: " . session_name() . "\n";

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
    echo "\n✅ Sessão vazia (como deveria estar)\n";
}

// 2. Verificar cookies
echo "\n📋 2. COOKIES ATIVOS:\n";
echo "=====================\n";

if (!empty($_COOKIE)) {
    foreach ($_COOKIE as $name => $value) {
        if (strpos($name, 'session') !== false || strpos($name, 'PHPSESSID') !== false || strpos($name, 'DAYDREAMING') !== false) {
            echo "🍪 $name: " . substr($value, 0, 20) . "...\n";
        }
    }
} else {
    echo "✅ Nenhum cookie relevante encontrado\n";
}

// 3. Verificar se há login automático
echo "\n📋 3. VERIFICANDO LOGIN AUTOMÁTICO:\n";
echo "===================================\n";

// Verificar se há algum mecanismo de login automático
$arquivos_verificar = ['index.php', 'header_status.php', 'config.php'];

foreach ($arquivos_verificar as $arquivo) {
    if (file_exists($arquivo)) {
        $conteudo = file_get_contents($arquivo);
        
        // Procurar por login automático
        if (strpos($conteudo, 'auto_login') !== false || 
            strpos($conteudo, 'login_automatico') !== false ||
            strpos($conteudo, '$_SESSION[\'usuario_id\'] =') !== false) {
            echo "⚠️ $arquivo pode conter login automático\n";
        } else {
            echo "✅ $arquivo sem login automático\n";
        }
    }
}

// 4. Limpar sessão completamente
echo "\n📋 4. LIMPANDO SESSÃO PARA TESTE LIMPO:\n";
echo "=======================================\n";

// Salvar dados atuais antes de limpar
$dados_anteriores = $_SESSION;

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

// Destruir sessão
session_destroy();

echo "✅ Sessão limpa completamente\n";
echo "✅ Cookie de sessão removido\n";

// Iniciar nova sessão limpa
session_name('DAYDREAMING_SESSION');
session_start();

echo "✅ Nova sessão iniciada\n";
echo "Novo Session ID: " . session_id() . "\n";

if (empty($_SESSION)) {
    echo "✅ Sessão está vazia (correto)\n";
} else {
    echo "⚠️ Sessão ainda contém dados:\n";
    foreach ($_SESSION as $key => $value) {
        echo "  - $key: $value\n";
    }
}

// 5. Criar página de teste de estado limpo
echo "\n📋 5. CRIANDO PÁGINA DE TESTE LIMPO:\n";
echo "====================================\n";

$teste_limpo = '<?php
require_once "config.php";
iniciarSessaoSegura();

// Headers anti-cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$usuario_logado = isset($_SESSION["usuario_id"]);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DayDreaming - Estado Limpo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }
        .main-container {
            padding: 50px 0;
        }
        .status-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .status-indicator {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
        }
        .status-logado { background: #28a745; }
        .status-deslogado { background: #dc3545; }
        .btn-custom {
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 500;
            margin: 5px;
        }
    </style>
</head>
<body>
    <div class="container main-container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="status-card text-center">
                    <h1 class="mb-4">🚀 DayDreaming - Estado Inicial</h1>
                    
                    <div class="alert <?= $usuario_logado ? "alert-warning" : "alert-success" ?>">
                        <h4>
                            <span class="status-indicator <?= $usuario_logado ? "status-logado" : "status-deslogado" ?>"></span>
                            Status: <?= $usuario_logado ? "LOGADO" : "DESLOGADO" ?>
                        </h4>
                        <?php if ($usuario_logado): ?>
                            <p><strong>Usuário:</strong> <?= $_SESSION["usuario_nome"] ?? "N/A" ?></p>
                            <p><strong>Login:</strong> <?= $_SESSION["usuario_login"] ?? "N/A" ?></p>
                            <p><strong>Session ID:</strong> <?= session_id() ?></p>
                        <?php else: ?>
                            <p>Sistema iniciado corretamente sem usuário logado</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5>🔐 Autenticação</h5>
                            <?php if ($usuario_logado): ?>
                                <a href="logout.php" class="btn btn-danger btn-custom">
                                    <i class="fas fa-sign-out-alt"></i> Fazer Logout
                                </a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-primary btn-custom">
                                    <i class="fas fa-sign-in-alt"></i> Fazer Login
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <h5>🏠 Navegação</h5>
                            <a href="index.php" class="btn btn-success btn-custom">
                                <i class="fas fa-home"></i> Página Inicial
                            </a>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h5>🧪 Testes de Funcionalidade</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <a href="forum.php" class="btn btn-outline-primary btn-custom">
                                    💬 Fórum
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="paises/eua.php" class="btn btn-outline-primary btn-custom">
                                    🌍 Países
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="simulador_provas.php" class="btn btn-outline-primary btn-custom">
                                    📚 Simulador
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <small class="text-muted">
                            Timestamp: <?= date("Y-m-d H:i:s") ?><br>
                            Session ID: <?= session_id() ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-refresh a cada 30 segundos para monitorar mudanças
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>';

if (file_put_contents('estado_inicial_limpo.php', $teste_limpo)) {
    echo "✅ Página de teste criada: estado_inicial_limpo.php\n";
}

// 6. Resumo
echo "\n📊 RESUMO:\n";
echo "==========\n";

if (!empty($dados_anteriores)) {
    echo "⚠️ PROBLEMA IDENTIFICADO:\n";
    echo "O sistema estava iniciando com usuário já logado:\n";
    foreach ($dados_anteriores as $key => $value) {
        if (is_string($value) || is_numeric($value)) {
            echo "  - $key: $value\n";
        }
    }
    echo "\n🔧 SOLUÇÃO APLICADA:\n";
    echo "✅ Sessão limpa completamente\n";
    echo "✅ Cookies removidos\n";
    echo "✅ Nova sessão iniciada\n";
} else {
    echo "✅ Sistema estava correto (sem usuário logado)\n";
}

echo "\n🔗 PRÓXIMOS PASSOS:\n";
echo "===================\n";
echo "1. Acesse: http://localhost:8080/estado_inicial_limpo.php\n";
echo "2. Verifique se mostra 'DESLOGADO'\n";
echo "3. Teste login: http://localhost:8080/login.php\n";
echo "4. Use: teste / teste123 ou admin / admin123\n";
echo "5. Verifique funcionalidades após login\n";

echo "\n⚠️ IMPORTANTE:\n";
echo "===============\n";
echo "- Feche TODOS os navegadores antes de testar\n";
echo "- Use modo incógnito para testes isolados\n";
echo "- Limpe cache e cookies se necessário\n";

?>
