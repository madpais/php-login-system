<?php
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
<?php require_once __DIR__ . '/footer.php'; ?>
</body>
</html>