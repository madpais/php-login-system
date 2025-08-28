<?php
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
                <div class="col-md-6 text-center">
                    <h5>📝 Editar Perfil</h5>
                    <a href="editar_perfil.php" class="btn btn-primary btn-test">
                        <i class="fas fa-user-edit"></i><br>
                        Editar Perfil Completo
                    </a>
                    <p class="mt-2"><small>Edita nome, email, escola, biografia, etc.</small></p>
                </div>
            </div>
        </div>
        
        <div class="test-card">
            <h3>📋 Verificações de Funcionalidade</h3>
            <div class="row">
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
            <div class="row">
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
</html>