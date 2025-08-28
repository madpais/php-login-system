<?php
require_once "config.php";
iniciarSessaoSegura();

// Headers anti-cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$usuario_logado = isset($_SESSION["usuario_id"]);
$usuario_nome = $_SESSION["usuario_nome"] ?? "";
$usuario_login = $_SESSION["usuario_login"] ?? "";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teste de Header</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
</head>
<body>
    <h1>Teste de Header - Verificação de Usuário</h1>
    
    <div style="background: #f0f0f0; padding: 20px; margin: 20px 0; border-radius: 5px;">
        <h2>Dados da Sessão:</h2>
        <p><strong>Usuário logado:</strong> Sim</p>
        <p><strong>Nome:</strong> Usuário Teste</p>
        <p><strong>Login:</strong> teste</p>
        <p><strong>Session ID:</strong> </p>
        <p><strong>Timestamp:</strong> 2025-08-26 20:33:03</p>
    </div>
    
    <div style="background: #e8f5e8; padding: 20px; margin: 20px 0; border-radius: 5px;">
        <h2>Teste de Navegação:</h2>
        <p><a href="index.php">Ir para Index</a></p>
        <p><a href="forum.php">Ir para Fórum</a></p>
        <p><a href="paises/eua.php">Ir para EUA</a></p>
        <p><a href="logout.php">Fazer Logout</a></p>
    </div>
    
    <script>
        // Recarregar a página a cada 5 segundos para verificar mudanças
        setTimeout(function() {
            location.reload();
        }, 5000);
    </script>
</body>
</html>