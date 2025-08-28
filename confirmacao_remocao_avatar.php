<?php
require_once "config.php";
iniciarSessaoSegura();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor de Avatar Removido - DayDreaming</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }
        .confirmation-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            margin: 50px 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
        }
        .btn-action {
            margin: 10px;
            padding: 15px 30px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include "header_status.php"; ?>
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="confirmation-card">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h1 class="h2 text-primary mb-4">Editor de Avatar Removido</h1>
                    
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle me-2"></i>Funcionalidade Removida</h5>
                        <p class="mb-0">O Editor de Avatar foi removido das funcionalidades do sistema conforme solicitado.</p>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5>✅ Removido:</h5>
                            <ul class="list-unstyled text-start">
                                <li><i class="fas fa-times text-danger me-2"></i>Arquivo editor_avatar.php</li>
                                <li><i class="fas fa-times text-danger me-2"></i>Link no dropdown do header</li>
                                <li><i class="fas fa-times text-danger me-2"></i>Clique no avatar da página do usuário</li>
                                <li><i class="fas fa-times text-danger me-2"></i>Referências em páginas de teste</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>✅ Mantido:</h5>
                            <ul class="list-unstyled text-start">
                                <li><i class="fas fa-check text-success me-2"></i>Exibição do avatar na página do usuário</li>
                                <li><i class="fas fa-check text-success me-2"></i>Configuração padrão do avatar</li>
                                <li><i class="fas fa-check text-success me-2"></i>Sistema de edição de perfil</li>
                                <li><i class="fas fa-check text-success me-2"></i>Todas as outras funcionalidades</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h5>🔗 Funcionalidades Disponíveis:</h5>
                        <div class="d-flex justify-content-center flex-wrap">
                            <a href="pagina_usuario.php" class="btn btn-primary btn-action">
                                <i class="fas fa-user me-2"></i>Meu Perfil
                            </a>
                            <a href="editar_perfil.php" class="btn btn-success btn-action">
                                <i class="fas fa-edit me-2"></i>Editar Perfil
                            </a>
                            <a href="index.php" class="btn btn-secondary btn-action">
                                <i class="fas fa-home me-2"></i>Página Inicial
                            </a>
                            <a href="forum.php" class="btn btn-warning btn-action">
                                <i class="fas fa-comments me-2"></i>Fórum
                            </a>
                        </div>
                    </div>
                    
                    <div class="alert alert-success mt-4">
                        <h6><i class="fas fa-thumbs-up me-2"></i>Sistema Atualizado</h6>
                        <p class="mb-0">O sistema foi atualizado e todas as referências ao Editor de Avatar foram removidas. 
                        O avatar continuará sendo exibido na página do usuário com a configuração padrão.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        console.log("Editor de Avatar removido do sistema");
        console.log("Funcionalidades disponíveis: Editar Perfil, Visualizar Perfil");
    </script>
</body>
</html>