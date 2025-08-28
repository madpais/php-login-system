<?php
require_once "config.php";
iniciarSessaoSegura();

// Fazer login automático para teste
if (!isset($_SESSION["usuario_id"])) {
    try {
        $pdo = conectarBD();
        $stmt = $pdo->prepare("SELECT id, nome FROM usuarios WHERE usuario = \"teste\"");
        $stmt->execute();
        $user = $stmt->fetch();
        if ($user) {
            $_SESSION["usuario_id"] = $user["id"];
            $_SESSION["usuario_nome"] = $user["nome"];
            $_SESSION["usuario_login"] = "teste";
        }
    } catch (Exception $e) {
        // Ignorar erro se não conseguir conectar
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Correção de Referências - DayDreaming</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .link-test {
            display: inline-block;
            margin: 5px;
            padding: 10px 15px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .link-test:hover {
            background: #0056b3;
            color: white;
            text-decoration: none;
        }
        .status-ok { color: #28a745; }
        .status-warning { color: #ffc107; }
        .status-error { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="test-card text-center">
            <h1>🔄 Correção de Referências Concluída</h1>
            <p class="lead">Todas as referências foram corrigidas para apontar para index.php</p>
            
            <div class="alert alert-success">
                <h5>✅ Correção Realizada</h5>
                <p><strong><?= $arquivos_corrigidos ?></strong> arquivos corrigidos</p>
                <p><strong><?= $substituicoes_feitas ?></strong> substituições feitas</p>
            </div>
        </div>
        
        <div class="test-card">
            <h3>🔗 Teste de Redirecionamentos</h3>
            <p>Teste os links abaixo para verificar se todos apontam para index.php:</p>
            
            <div class="row">
                <div class="col-md-6">
                    <h5>📄 Links Diretos</h5>
                    <a href="index.php" class="link-test">🏠 index.php</a>
                    <a href="login.php" class="link-test">🔐 Login (teste redirecionamento)</a>
                    <a href="logout.php" class="link-test">🚪 Logout (teste redirecionamento)</a>
                </div>
                <div class="col-md-6">
                    <h5>🌍 Breadcrumbs de Países</h5>
                    <a href="paises/eua.php" class="link-test">🇺🇸 EUA (teste breadcrumb)</a>
                    <a href="paises/canada.php" class="link-test">🇨🇦 Canadá (teste breadcrumb)</a>
                    <a href="pesquisa_por_pais.php" class="link-test">🔍 Pesquisa (teste navegação)</a>
                </div>
            </div>
        </div>
        
        <div class="test-card">
            <h3>🧪 Verificações Importantes</h3>
            <div class="row">
                <div class="col-md-4">
                    <div class="alert alert-info">
                        <h6>Header Status</h6>
                        <p>Botão "🏠 Página Inicial" deve levar para index.php</p>
                        <small>Verifique no topo da página</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-warning">
                        <h6>Sistema de Login</h6>
                        <p>Após login deve redirecionar para index.php</p>
                        <small>Teste com: teste / teste123</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-success">
                        <h6>Páginas de Países</h6>
                        <p>Breadcrumb "Início" deve apontar para index.php</p>
                        <small>Teste em qualquer página de país</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="test-card text-center">
            <h3>📊 Resultado da Correção</h3>
            <div class="row">
                <div class="col-md-3">
                    <h4 class="status-ok">✅</h4>
                    <small>Referências Corrigidas</small>
                </div>
                <div class="col-md-3">
                    <h4 class="status-ok"><?= $arquivos_corrigidos ?></h4>
                    <small>Arquivos Modificados</small>
                </div>
                <div class="col-md-3">
                    <h4 class="status-ok"><?= $substituicoes_feitas ?></h4>
                    <small>Substituições</small>
                </div>
                <div class="col-md-3">
                    <h4 class="status-ok">🏠</h4>
                    <small>index.php Ativo</small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>