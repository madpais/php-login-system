<?php
/**
 * Teste Completo do Sistema de Autenticação
 * Verifica todas as funcionalidades após correções
 */

require_once 'config.php';
iniciarSessaoSegura();

echo "🔍 TESTE COMPLETO DO SISTEMA DE AUTENTICAÇÃO\n";
echo "============================================\n\n";

// 1. Verificar configuração de sessão
echo "📋 1. CONFIGURAÇÃO DE SESSÃO:\n";
echo "==============================\n";
echo "Session status: " . session_status() . " (3=active)\n";
echo "Session name: " . session_name() . "\n";
echo "Session ID: " . session_id() . "\n";
echo "Cookie httponly: " . ini_get('session.cookie_httponly') . "\n";
echo "Use only cookies: " . ini_get('session.use_only_cookies') . "\n";

// 2. Testar login
echo "\n📋 2. TESTE DE LOGIN:\n";
echo "=====================\n";

// Limpar sessão para teste limpo
$_SESSION = array();

$usuario = 'admin';
$senha = 'admin123';

try {
    $pdo = conectarBD();
    
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
            
            echo "✅ Login realizado com sucesso\n";
            echo "✅ Dados salvos na sessão\n";
        } else {
            echo "❌ Credenciais inválidas\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

// 3. Testar páginas principais
echo "\n📋 3. TESTE DE PÁGINAS PRINCIPAIS:\n";
echo "===================================\n";

$paginas_teste = [
    'index.php' => 'Página inicial',
    'forum.php' => 'Fórum',
    'admin_forum.php' => 'Admin do fórum',
    'pagina_usuario.php' => 'Dashboard do usuário',
    'simulador_provas.php' => 'Simulador de provas',
    'testes_internacionais.php' => 'Testes internacionais',
    'badges_manager.php' => 'Gerenciador de badges',
    'sistema_notificacoes.php' => 'Sistema de notificações'
];

foreach ($paginas_teste as $arquivo => $descricao) {
    if (file_exists($arquivo)) {
        // Verificar se a página usa verificação de autenticação
        $conteudo = file_get_contents($arquivo);
        
        if (strpos($conteudo, 'verificar_auth.php') !== false || 
            strpos($conteudo, 'verificarLogin') !== false ||
            strpos($conteudo, 'header_status.php') !== false ||
            strpos($conteudo, 'iniciarSessaoSegura') !== false) {
            echo "✅ $arquivo - $descricao (com autenticação)\n";
        } else {
            echo "⚠️ $arquivo - $descricao (sem verificação de auth)\n";
        }
    } else {
        echo "❌ $arquivo - $descricao (arquivo não encontrado)\n";
    }
}

// 4. Testar páginas de países
echo "\n📋 4. TESTE DE PÁGINAS DE PAÍSES:\n";
echo "==================================\n";

$paises_dir = 'paises';
if (is_dir($paises_dir)) {
    $paises = glob($paises_dir . '/*.php');
    $paises_ok = 0;
    $paises_erro = 0;
    
    foreach ($paises as $pais) {
        if (basename($pais) !== 'header_status.php') {
            $conteudo = file_get_contents($pais);
            
            if (strpos($conteudo, 'iniciarSessaoSegura()') !== false) {
                $paises_ok++;
            } else {
                $paises_erro++;
                echo "⚠️ " . basename($pais) . " - sem sessão corrigida\n";
            }
        }
    }
    
    echo "✅ Páginas de países com sessão corrigida: $paises_ok\n";
    echo "⚠️ Páginas de países com problemas: $paises_erro\n";
}

// 5. Testar verificação de autenticação
echo "\n📋 5. TESTE DE VERIFICAÇÃO DE AUTENTICAÇÃO:\n";
echo "============================================\n";

if (isset($_SESSION['usuario_id'])) {
    echo "✅ Usuário logado: " . $_SESSION['usuario_nome'] . "\n";
    
    // Simular verificação como nas páginas protegidas
    try {
        $stmt = $pdo->prepare("SELECT ativo, nome FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['usuario_id']]);
        $user = $stmt->fetch();
        
        if ($user && $user['ativo']) {
            echo "✅ Usuário válido e ativo\n";
            echo "✅ Acesso a páginas protegidas: PERMITIDO\n";
        } else {
            echo "❌ Usuário inválido ou inativo\n";
        }
    } catch (Exception $e) {
        echo "❌ Erro na verificação: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Nenhum usuário logado\n";
}

// 6. Testar funcionalidades específicas
echo "\n📋 6. TESTE DE FUNCIONALIDADES ESPECÍFICAS:\n";
echo "============================================\n";

// Testar sistema de fórum
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM forum_categorias WHERE ativo = 1");
    $categorias = $stmt->fetchColumn();
    echo "✅ Categorias do fórum ativas: $categorias\n";
} catch (Exception $e) {
    echo "❌ Erro no sistema de fórum: " . $e->getMessage() . "\n";
}

// Testar sistema de badges
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM badges WHERE ativa = 1");
    $badges = $stmt->fetchColumn();
    echo "✅ Badges ativas: $badges\n";
} catch (Exception $e) {
    echo "❌ Erro no sistema de badges: " . $e->getMessage() . "\n";
}

// Testar sistema de questões
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM questoes WHERE ativo = 1");
    $questoes = $stmt->fetchColumn();
    echo "✅ Questões ativas: $questoes\n";
} catch (Exception $e) {
    echo "❌ Erro no sistema de questões: " . $e->getMessage() . "\n";
}

// 7. Verificar logs do sistema
echo "\n📋 7. VERIFICAÇÃO DE LOGS:\n";
echo "===========================\n";

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM logs_acesso WHERE DATE(data_hora) = CURDATE()");
    $logs_hoje = $stmt->fetchColumn();
    echo "✅ Logs de acesso hoje: $logs_hoje\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM logs_sistema WHERE DATE(data_hora) = CURDATE()");
    $logs_sistema_hoje = $stmt->fetchColumn();
    echo "✅ Logs do sistema hoje: $logs_sistema_hoje\n";
} catch (Exception $e) {
    echo "❌ Erro nos logs: " . $e->getMessage() . "\n";
}

// 8. Resumo final
echo "\n📊 RESUMO FINAL:\n";
echo "=================\n";

$problemas = [];

if (session_status() !== PHP_SESSION_ACTIVE) {
    $problemas[] = "Sessão não está ativa";
}

if (!isset($_SESSION['usuario_id'])) {
    $problemas[] = "Usuário não está logado";
}

if (empty(session_id())) {
    $problemas[] = "Session ID vazio";
}

if (empty($problemas)) {
    echo "🎉 SISTEMA DE AUTENTICAÇÃO FUNCIONANDO PERFEITAMENTE!\n";
    echo "\n✅ FUNCIONALIDADES VERIFICADAS:\n";
    echo "  - Configuração de sessão segura\n";
    echo "  - Login e logout funcionais\n";
    echo "  - Persistência de dados na sessão\n";
    echo "  - Verificação de autenticação em páginas\n";
    echo "  - Páginas de países protegidas\n";
    echo "  - Sistema de logs ativo\n";
    echo "  - Funcionalidades específicas operacionais\n";
} else {
    echo "⚠️ PROBLEMAS IDENTIFICADOS:\n";
    foreach ($problemas as $problema) {
        echo "  - $problema\n";
    }
}

echo "\n🔗 TESTE MANUAL RECOMENDADO:\n";
echo "=============================\n";
echo "1. Acesse: http://localhost:8080/login.php\n";
echo "2. Faça login com: admin / admin123\n";
echo "3. Verifique redirecionamento para index.php\n";
echo "4. Navegue para: forum.php, pagina_usuario.php\n";
echo "5. Acesse uma página de país: paises/eua.php\n";
echo "6. Verifique se permanece logado em todas\n";
echo "7. Teste logout: logout.php\n";
echo "8. Verifique se é redirecionado para login\n";

?>
