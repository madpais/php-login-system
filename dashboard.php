<?php
// Verificação de segurança para dashboard
require_once 'verificar_auth.php';

// Verificar se o usuário está logado e ativo
$user_data = verificarUsuarioAtivo();

// Obter informações do usuário da sessão
$nome = $_SESSION['usuario_nome'];
$usuario = $_SESSION['usuario_login'];

// Gerar token CSRF para formulários
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Dashboard</title>
    <script src="public/js/main.js?v=1"></script>
    <style>
        .dashboard-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }
        
        .welcome-message {
            margin-bottom: 30px;
            text-align: center;
        }
        
        .dashboard-menu {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }
        
        .menu-item {
            background-color: var(--primary-color);
            color: var(--color);
            padding: 15px 25px;
            border-radius: 5px;
            margin: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .logout-btn {
            display: block;
            width: 150px;
            margin: 20px auto;
            padding: 10px;
            background-color: #e74c3c;
            color: white;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .logout-btn:hover {
            background-color: #c0392b;
            transform: scale(1.05);
        }
        
        @media screen and (max-width: 768px) {
            .dashboard-container {
                width: 90%;
                padding: 15px;
            }
            
            .menu-item {
                width: 45%;
                padding: 12px 15px;
            }
        }
        
        @media screen and (max-width: 480px) {
            .dashboard-menu {
                flex-direction: column;
                align-items: center;
            }
            
            .menu-item {
                width: 80%;
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>
    <?php include 'header_status.php'; ?>
    
    <section class="container">
        <div class="dashboard-container">
            <div class="welcome-message">
                <h1>Bem-vindo, <?php echo htmlspecialchars($nome); ?>!</h1>
                <p>Usuário: <?php echo htmlspecialchars($usuario); ?></p>
            </div>
            
            <div class="dashboard-menu">
                <div class="menu-item">
                    <h3>Meu Perfil</h3>
                    <p>Gerencie suas informações pessoais</p>
                </div>
                
                <div class="menu-item">
                    <h3>Configurações</h3>
                    <p>Ajuste as configurações da sua conta</p>
                </div>
                
                <div class="menu-item">
                    <h3>Mensagens</h3>
                    <p>Veja suas mensagens recentes</p>
                </div>
                
                <div class="menu-item">
                    <h3>Atividades</h3>
                    <p>Histórico de atividades recentes</p>
                </div>
            </div>
            
            <a href="logout.php" class="logout-btn">Sair</a>
        </div>
    </section>
</body>
</html>