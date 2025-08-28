<?php
require_once "header_status.php";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teste Header Corrigido</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .info { background: #e8f5e8; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .warning { background: #fff3cd; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .error { background: #f8d7da; padding: 15px; margin: 10px 0; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>🔍 Teste do Header Corrigido</h1>
    
    <div class="info">
        <h2>Dados Atuais do Header:</h2>
        <p><strong>Usuário Logado:</strong> <?php echo $usuario_logado ? "Sim" : "Não"; ?></p>
        <p><strong>Nome Exibido:</strong> <?php echo htmlspecialchars($usuario_nome); ?></p>
        <p><strong>Login:</strong> <?php echo htmlspecialchars($usuario_login); ?></p>
        <p><strong>Admin:</strong> <?php echo $is_admin ? "Sim" : "Não"; ?></p>
        <p><strong>Session ID:</strong> <?php echo session_id(); ?></p>
        <p><strong>Timestamp:</strong> <?php echo date("Y-m-d H:i:s"); ?></p>
    </div>
    
    <div class="warning">
        <h2>Teste de Navegação:</h2>
        <p>Clique nos links abaixo e verifique se o nome do usuário permanece correto:</p>
        <ul>
            <li><a href="index.php">Página Inicial</a></li>
            <li><a href="forum.php">Fórum</a></li>
            <li><a href="pagina_usuario.php">Página do Usuário</a></li>
            <li><a href="paises/eua.php">EUA</a></li>
            <li><a href="paises/canada.php">Canadá</a></li>
            <li><a href="teste_header_corrigido_navegador.php">Recarregar esta página</a></li>
        </ul>
    </div>
    
    <div class="error">
        <h2>Ações de Teste:</h2>
        <p><a href="logout.php">Fazer Logout</a></p>
        <p><a href="login.php">Ir para Login</a></p>
    </div>
    
    <script>
        // Auto-refresh a cada 10 segundos para monitorar mudanças
        setTimeout(function() {
            location.reload();
        }, 10000);
    </script>
</body>
</html>