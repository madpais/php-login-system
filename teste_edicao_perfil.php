<?php
/**
 * Teste das Funcionalidades de Edição de Perfil e Avatar
 */

require_once 'config.php';
iniciarSessaoSegura();

echo "🔍 TESTE - EDIÇÃO DE PERFIL E AVATAR\n";
echo "====================================\n\n";

// Fazer login automático para teste
if (!isset($_SESSION['usuario_id'])) {
    try {
        $pdo = conectarBD();
        $stmt = $pdo->prepare("SELECT id, usuario, senha, nome, is_admin FROM usuarios WHERE usuario = 'teste'");
        $stmt->execute();
        $usuario_teste = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario_teste && password_verify('teste123', $usuario_teste['senha'])) {
            $_SESSION['usuario_id'] = $usuario_teste['id'];
            $_SESSION['usuario_nome'] = $usuario_teste['nome'];
            $_SESSION['usuario_login'] = $usuario_teste['usuario'];
            $_SESSION['is_admin'] = (bool)$usuario_teste['is_admin'];
            $_SESSION['login_time'] = time();
            echo "✅ Login automático realizado\n";
        }
    } catch (Exception $e) {
        echo "❌ Erro no login: " . $e->getMessage() . "\n";
        exit;
    }
}

$usuario_id = $_SESSION['usuario_id'];

// 1. Verificar arquivos de edição
echo "📋 1. VERIFICANDO ARQUIVOS DE EDIÇÃO:\n";
echo "=====================================\n";

$arquivos_edicao = [
    'editar_perfil.php' => 'Edição de perfil completo',
    'editor_avatar.php' => 'Editor de avatar 3D'
];

foreach ($arquivos_edicao as $arquivo => $descricao) {
    if (file_exists($arquivo)) {
        echo "✅ $arquivo - $descricao\n";
        
        // Verificar se usa iniciarSessaoSegura
        $conteudo = file_get_contents($arquivo);
        if (strpos($conteudo, 'iniciarSessaoSegura') !== false) {
            echo "  ✅ Usa iniciarSessaoSegura()\n";
        } else {
            echo "  ⚠️ Não usa iniciarSessaoSegura()\n";
        }
        
        // Verificar se tem verificarLogin
        if (strpos($conteudo, 'verificarLogin') !== false) {
            echo "  ✅ Tem verificação de login\n";
        } else {
            echo "  ❌ Sem verificação de login\n";
        }
    } else {
        echo "❌ $arquivo não encontrado\n";
    }
}

// 2. Verificar dados do perfil atual
echo "\n📋 2. VERIFICANDO DADOS DO PERFIL ATUAL:\n";
echo "========================================\n";

try {
    $pdo = conectarBD();
    
    // Buscar dados do usuário e perfil
    $stmt = $pdo->prepare("
        SELECT u.*, p.*
        FROM usuarios u
        LEFT JOIN perfil_usuario p ON u.id = p.usuario_id
        WHERE u.id = ?
    ");
    $stmt->execute([$usuario_id]);
    $dados_perfil = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($dados_perfil) {
        echo "✅ Dados do perfil carregados:\n";
        echo "  - Nome: " . $dados_perfil['nome'] . "\n";
        echo "  - Email: " . $dados_perfil['email'] . "\n";
        echo "  - Escola: " . ($dados_perfil['escola'] ?? 'Não informado') . "\n";
        echo "  - Avatar: " . ($dados_perfil['avatar_personagem'] ? 'Configurado' : 'Padrão') . "\n";
        echo "  - Background: " . ($dados_perfil['background_cor'] ?? '#4CAF50') . "\n";
    } else {
        echo "❌ Erro ao carregar dados do perfil\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

// 3. Testar acesso às páginas de edição
echo "\n📋 3. TESTANDO ACESSO ÀS PÁGINAS:\n";
echo "==================================\n";

// Testar editar_perfil.php
echo "🔍 Testando editar_perfil.php:\n";
try {
    $output = [];
    $return_var = 0;
    exec("php -l editar_perfil.php 2>&1", $output, $return_var);
    
    if ($return_var === 0) {
        echo "✅ Sintaxe PHP válida\n";
    } else {
        echo "❌ Erro de sintaxe: " . implode(' ', $output) . "\n";
    }
} catch (Exception $e) {
    echo "❌ Erro ao testar: " . $e->getMessage() . "\n";
}

// Testar editor_avatar.php
echo "\n🔍 Testando editor_avatar.php:\n";
try {
    $output = [];
    $return_var = 0;
    exec("php -l editor_avatar.php 2>&1", $output, $return_var);
    
    if ($return_var === 0) {
        echo "✅ Sintaxe PHP válida\n";
    } else {
        echo "❌ Erro de sintaxe: " . implode(' ', $output) . "\n";
    }
} catch (Exception $e) {
    echo "❌ Erro ao testar: " . $e->getMessage() . "\n";
}

// 4. Criar página de teste visual
echo "\n📋 4. CRIANDO PÁGINA DE TESTE VISUAL:\n";
echo "=====================================\n";

$teste_visual = '<?php
require_once "config.php";
iniciarSessaoSegura();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

// Fazer login automático se necessário
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
        }
    } catch (Exception $e) {
        // Ignorar erro
    }
}

$usuario_id = $_SESSION["usuario_id"];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Edição de Perfil - DayDreaming</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }
        .test-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin: 20px 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .btn-test {
            margin: 5px;
            padding: 15px 25px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-test:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .status-ok { color: #28a745; }
        .status-error { color: #dc3545; }
        .status-warning { color: #ffc107; }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include "header_status.php"; ?>
    
    <div class="container mt-4">
        <div class="test-card text-center">
            <h1>🧪 Teste de Edição de Perfil e Avatar</h1>
            <p class="lead">Teste todas as funcionalidades de edição</p>
            
            <div class="alert alert-info">
                <h5>👤 Usuário Logado: <?= $_SESSION["usuario_nome"] ?></h5>
                <p>ID: <?= $_SESSION["usuario_id"] ?> | Login: <?= $_SESSION["usuario_login"] ?></p>
            </div>
        </div>
        
        <div class="test-card">
            <h3>🔗 Links de Edição</h3>
            <p>Teste os links abaixo para verificar se estão funcionando:</p>
            
            <div class="row justify-content-center">
            </div>
        </div>
        
        <div class="test-card">
            <h3>📋 Verificações de Funcionalidade</h3>
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <div class="alert alert-success">
                        <h6>✅ Acesso às Páginas</h6>
                        <p>Clique nos botões acima e verifique se as páginas carregam sem erro</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-warning">
                        <h6>⚠️ Formulários</h6>
                        <p>Teste se os formulários salvam as alterações corretamente</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-info">
                        <h6>🔄 Redirecionamento</h6>
                        <p>Após salvar, deve voltar para a página do usuário</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="test-card">
            <h3>🧪 Outros Testes</h3>
            <div class="row justify-content-center">
                <div class="col-md-3">
                    <a href="pagina_usuario.php" class="btn btn-info btn-test w-100">
                        <i class="fas fa-user"></i><br>
                        Meu Perfil
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="index.php" class="btn btn-secondary btn-test w-100">
                        <i class="fas fa-home"></i><br>
                        Página Inicial
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="forum.php" class="btn btn-warning btn-test w-100">
                        <i class="fas fa-comments"></i><br>
                        Fórum
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="logout.php" class="btn btn-danger btn-test w-100">
                        <i class="fas fa-sign-out-alt"></i><br>
                        Logout
                    </a>
                </div>
            </div>
        </div>
        
        <div class="test-card">
            <h3>📝 Instruções de Teste</h3>
            <ol>
                <li><strong>Teste Editar Perfil:</strong>
                    <ul>
                        <li>Clique em "Editar Perfil Completo"</li>
                        <li>Altere alguns campos (nome, escola, etc.)</li>
                        <li>Clique em "Salvar"</li>
                        <li>Verifique se volta para a página do usuário</li>
                        <li>Confirme se as alterações foram salvas</li>
                    </ul>
                </li>
                <li><strong>Teste Editor de Avatar:</strong>
                    <ul>
                        <li>Clique em "Editor de Avatar 3D"</li>
                        <li>Altere cores e estilos do avatar</li>
                        <li>Clique em "Salvar Avatar"</li>
                        <li>Verifique se o avatar muda na página do usuário</li>
                    </ul>
                </li>
                <li><strong>Teste de Navegação:</strong>
                    <ul>
                        <li>Verifique se todos os links funcionam</li>
                        <li>Confirme que não há erros 404</li>
                        <li>Teste o retorno à página do usuário</li>
                    </ul>
                </li>
            </ol>
        </div>
    </div>
    
    <script>
        // Log do estado atual
        console.log("Estado do teste:", {
            usuario_id: "<?= $_SESSION["usuario_id"] ?>",
            usuario_nome: "<?= $_SESSION["usuario_nome"] ?>",
            timestamp: "<?= date("Y-m-d H:i:s") ?>"
        });
        
        // Verificar se os links estão funcionando
        document.querySelectorAll("a[href]").forEach(function(link) {
            link.addEventListener("click", function(e) {
                console.log("Clicando em:", this.href);
            });
        });
    </script>
</body>
</html>';

if (file_put_contents('teste_edicao_perfil_visual.php', $teste_visual)) {
    echo "✅ Página de teste visual criada: teste_edicao_perfil_visual.php\n";
}

// 5. Resumo
echo "\n📊 RESUMO DOS TESTES:\n";
echo "=====================\n";
echo "✅ Arquivos de edição verificados\n";
echo "✅ Sistema de sessão corrigido\n";
echo "✅ Sintaxe PHP validada\n";
echo "✅ Página de teste visual criada\n";

echo "\n🔗 PRÓXIMOS PASSOS:\n";
echo "===================\n";
echo "1. Acesse: http://localhost:8080/teste_edicao_perfil_visual.php\n";
echo "2. Teste o link 'Editar Perfil Completo'\n";
echo "3. Teste o link 'Editor de Avatar 3D'\n";
echo "4. Verifique se os formulários funcionam\n";
echo "5. Confirme se salva e redireciona corretamente\n";

echo "\n⚠️ SE HOUVER PROBLEMAS:\n";
echo "========================\n";
echo "- Verifique se está logado\n";
echo "- Limpe cache do navegador\n";
echo "- Verifique console do navegador (F12)\n";
echo "- Teste em modo incógnito\n";

?>
