<?php
/**
 * Teste Simples do Header de Login
 */

require_once 'config.php';
iniciarSessaoSegura();

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
        .btn {
            padding: 10px 20px;
            margin: 5px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            color: white;
        }
        .btn-primary { background: #007bff; }
        .btn-danger { background: #dc3545; }
        .btn-success { background: #28a745; }
        .btn-secondary { background: #6c757d; }
    </style>
</head>
<body>
    <!-- Header de Status -->
    <?php include 'header_status.php'; ?>
    
    <div class="content">
        <div class="debug-info">
            <h1>🔍 Teste do Header de Login</h1>
            
            <h3>📊 Estado da Sessão:</h3>
            <ul>
                <li><strong>Session ID:</strong> <?= session_id() ?></li>
                <li><strong>Usuário logado:</strong> 
                    <span class="<?= isset($_SESSION['usuario_id']) ? 'status-ok' : 'status-error' ?>">
                        <?= isset($_SESSION['usuario_id']) ? '✅ Sim' : '❌ Não' ?>
                    </span>
                </li>
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <li><strong>ID:</strong> <?= $_SESSION['usuario_id'] ?></li>
                    <li><strong>Nome:</strong> <?= $_SESSION['usuario_nome'] ?? 'N/A' ?></li>
                    <li><strong>Login:</strong> <?= $_SESSION['usuario_login'] ?? 'N/A' ?></li>
                    <li><strong>Admin:</strong> <?= isset($_SESSION['is_admin']) && $_SESSION['is_admin'] ? 'Sim' : 'Não' ?></li>
                <?php endif; ?>
            </ul>
            
            <h3>🧪 Verificações do Header:</h3>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
                <?php
                // Verificar o que o header deveria mostrar
                $usuario_logado = isset($_SESSION['usuario_id']);
                echo "<p><strong>Variável \$usuario_logado:</strong> " . ($usuario_logado ? 'true' : 'false') . "</p>";
                
                if ($usuario_logado) {
                    echo "<p><strong>Nome a ser exibido:</strong> " . ($_SESSION['usuario_nome'] ?? 'N/A') . "</p>";
                    echo "<p class='status-ok'>✅ Header deveria mostrar: 'Você está logado' e botão 'Deslogar'</p>";
                } else {
                    echo "<p class='status-error'>❌ Header deveria mostrar: 'Você não está logado' e botão 'Fazer Login'</p>";
                }
                ?>
            </div>
            
            <h3>🔗 Testes de Funcionalidade:</h3>
            <div>
                <a href="login.php" class="btn btn-primary">🔐 Ir para Login</a>
                <a href="logout.php" class="btn btn-danger">🚪 Fazer Logout</a>
                <a href="pagina_usuario.php" class="btn btn-success">👤 Meu Perfil</a>
                <a href="index.php" class="btn btn-secondary">🏠 Página Inicial</a>
            </div>
            
            <h3>📝 O que verificar no header acima:</h3>
            <ol>
                <li><strong>Status de login:</strong> Deve mostrar "✅ Você está logado"</li>
                <li><strong>Nome do usuário:</strong> Deve aparecer "👤 Usuário Teste" clicável</li>
                <li><strong>Botão direito:</strong> Deve mostrar "🚪 Deslogar" (não "🔑 Fazer Login")</li>
                <li><strong>Dropdown:</strong> Clique no nome para ver opções do usuário</li>
                <li><strong>Notificações:</strong> Deve aparecer ícone de sino com contador</li>
            </ol>
            
            <div style="background: #fff3cd; padding: 15px; border-radius: 5px; margin-top: 20px;">
                <h4>🔧 Se o header não estiver correto:</h4>
                <ul>
                    <li>Recarregue a página (F5)</li>
                    <li>Limpe o cache do navegador (Ctrl+Shift+Del)</li>
                    <li>Abra em modo incógnito</li>
                    <li>Verifique o console do navegador (F12)</li>
                </ul>
            </div>
            
            <div style="background: #d4edda; padding: 15px; border-radius: 5px; margin-top: 20px;">
                <h4>✅ Teste Manual:</h4>
                <ol>
                    <li>Clique em "🚪 Fazer Logout" acima</li>
                    <li>Verifique se o header muda para "❌ Você não está logado"</li>
                    <li>Clique em "🔐 Ir para Login"</li>
                    <li>Faça login com: teste / teste123</li>
                    <li>Verifique se volta a mostrar "✅ Você está logado"</li>
                </ol>
            </div>
        </div>
    </div>
    
    <script>
        // Log do estado atual no console
        console.log('Estado da sessão:', {
            logado: <?= isset($_SESSION['usuario_id']) ? 'true' : 'false' ?>,
            usuario_id: '<?= $_SESSION['usuario_id'] ?? 'N/A' ?>',
            usuario_nome: '<?= $_SESSION['usuario_nome'] ?? 'N/A' ?>',
            session_id: '<?= session_id() ?>'
        });
        
        // Verificar se o header foi carregado corretamente
        setTimeout(function() {
            const header = document.getElementById('login-status-header');
            if (header) {
                console.log('✅ Header carregado');
                const loginStatus = header.textContent;
                console.log('Conteúdo do header:', loginStatus);
                
                if (loginStatus.includes('Você está logado')) {
                    console.log('✅ Header mostra usuário logado');
                } else if (loginStatus.includes('Você não está logado')) {
                    console.log('⚠️ Header mostra usuário deslogado');
                } else {
                    console.log('❌ Header com conteúdo inesperado');
                }
            } else {
                console.log('❌ Header não encontrado');
            }
        }, 1000);
    </script>
<?php require_once __DIR__ . '/footer.php'; ?>
</body>
</html>
