<?php
require_once "config.php";
iniciarSessaoSegura();

// Headers anti-cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$mensagem = "";
$usuario_logado = isset($_SESSION["usuario_id"]);

// Processar login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["acao"])) {
    if ($_POST["acao"] == "login") {
        $usuario = trim($_POST["usuario"] ?? "");
        $senha = trim($_POST["senha"] ?? "");
        
        if (!empty($usuario) && !empty($senha)) {
            try {
                $pdo = conectarBD();
                $stmt = $pdo->prepare("SELECT id, usuario, senha, nome, is_admin, ativo FROM usuarios WHERE usuario = ?");
                $stmt->execute([$usuario]);
                $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user_data && password_verify($senha, $user_data["senha"]) && $user_data["ativo"]) {
                    $_SESSION["usuario_id"] = $user_data["id"];
                    $_SESSION["usuario_nome"] = $user_data["nome"];
                    $_SESSION["usuario_login"] = $user_data["usuario"];
                    $_SESSION["is_admin"] = (bool)$user_data["is_admin"];
                    $_SESSION["login_time"] = time();
                    
                    $mensagem = "✅ Login realizado com sucesso!";
                    $usuario_logado = true;
                    
                    // Registrar login
                    $stmt = $pdo->prepare("INSERT INTO logs_acesso (usuario_id, ip, sucesso) VALUES (?, ?, TRUE)");
                    $stmt->execute([$user_data["id"], "127.0.0.1"]);
                } else {
                    $mensagem = "❌ Usuário ou senha incorretos!";
                }
            } catch (Exception $e) {
                $mensagem = "❌ Erro: " . $e->getMessage();
            }
        } else {
            $mensagem = "⚠️ Preencha todos os campos!";
        }
    } elseif ($_POST["acao"] == "logout") {
        $_SESSION = array();
        session_destroy();
        session_name("DAYDREAMING_SESSION");
        session_start();
        $mensagem = "✅ Logout realizado com sucesso!";
        $usuario_logado = false;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Login Funcional - DayDreaming</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }
        .test-container {
            padding: 20px 0;
        }
        .test-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin: 20px 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .status-logado { background: #d4edda; color: #155724; }
        .status-deslogado { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <!-- HEADER ORIGINAL -->
    <?php include "header_status.php"; ?>
    
    <div class="container test-container">
        <div class="test-card text-center">
            <h1>🧪 Teste de Login Funcional</h1>
            
            <?php if ($mensagem): ?>
                <div class="alert alert-info"><?= $mensagem ?></div>
            <?php endif; ?>
            
            <div class="alert <?= $usuario_logado ? "status-logado" : "status-deslogado" ?>">
                <h4>
                    Status: <?= $usuario_logado ? "✅ LOGADO" : "❌ DESLOGADO" ?>
                </h4>
                <?php if ($usuario_logado): ?>
                    <p><strong>Usuário:</strong> <?= $_SESSION["usuario_nome"] ?? "N/A" ?></p>
                    <p><strong>Login:</strong> <?= $_SESSION["usuario_login"] ?? "N/A" ?></p>
                    <p><strong>Session ID:</strong> <?= session_id() ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (!$usuario_logado): ?>
            <div class="test-card">
                <h3>🔐 Fazer Login</h3>
                <form method="POST">
                    <input type="hidden" name="acao" value="login">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Usuário:</label>
                                <input type="text" name="usuario" class="form-control" value="teste" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Senha:</label>
                                <input type="password" name="senha" class="form-control" value="teste123" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt"></i> Fazer Login
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="test-card">
                <h3>🚪 Fazer Logout</h3>
                <form method="POST">
                    <input type="hidden" name="acao" value="logout">
                    <button type="submit" class="btn btn-danger btn-lg">
                        <i class="fas fa-sign-out-alt"></i> Fazer Logout
                    </button>
                </form>
            </div>
        <?php endif; ?>
        
        <div class="test-card">
            <h3>🔗 Teste de Navegação</h3>
            <div class="row">
                <div class="col-md-3">
                    <a href="index.php" class="btn btn-success w-100 mb-2">
                        🏠 Página Inicial
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="pagina_usuario.php" class="btn btn-info w-100 mb-2">
                        👤 Meu Perfil
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="forum.php" class="btn btn-warning w-100 mb-2">
                        💬 Fórum
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="paises/eua.php" class="btn btn-secondary w-100 mb-2">
                        🌍 EUA
                    </a>
                </div>
            </div>
        </div>
        
        <div class="test-card">
            <h3>📋 Verificações do Header</h3>
            <div class="alert alert-warning">
                <h5>O header acima deveria mostrar:</h5>
                <?php if ($usuario_logado): ?>
                    <ul>
                        <li>✅ "Você está logado"</li>
                        <li>✅ Nome: "<?= $_SESSION["usuario_nome"] ?>"</li>
                        <li>✅ Botão "🚪 Deslogar"</li>
                        <li>✅ Dropdown com opções</li>
                    </ul>
                <?php else: ?>
                    <ul>
                        <li>❌ "Você não está logado"</li>
                        <li>❌ Botão "🔑 Fazer Login"</li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        // Log do estado
        console.log("Estado atual:", {
            logado: <?= $usuario_logado ? "true" : "false" ?>,
            session_id: "<?= session_id() ?>",
            timestamp: "<?= date("Y-m-d H:i:s") ?>"
        });
        
        // Verificar header após 1 segundo
        setTimeout(function() {
            const header = document.querySelector("header, nav, .header, .navbar");
            if (header) {
                console.log("Header encontrado:", header.textContent.substring(0, 100));
            } else {
                console.log("Header não encontrado");
            }
        }, 1000);
    </script>
<?php require_once __DIR__ . '/footer.php'; ?>
</body>
</html>