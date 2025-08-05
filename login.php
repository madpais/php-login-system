<?php
session_start();
require_once 'config.php';

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obter os dados do formulário
    $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
    $senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';
    
    // Validação básica
    if (empty($usuario) || empty($senha)) {
        $erro = "Por favor, preencha todos os campos.";
    } else {
        try {
            // Conectar ao banco de dados
            $conn = conectarBD();
            
            // Preparar a consulta SQL
            $stmt = $conn->prepare("SELECT id, usuario, senha, nome FROM usuarios WHERE usuario = :usuario AND ativo = TRUE");
            $stmt->bindParam(':usuario', $usuario);
            $stmt->execute();
            
            // Verificar se o usuário existe
            if ($stmt->rowCount() > 0) {
                $usuario_dados = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Verificar a senha
                if (password_verify($senha, $usuario_dados['senha'])) {
                    // Senha correta, iniciar a sessão
                    $_SESSION['usuario_id'] = $usuario_dados['id'];
                    $_SESSION['usuario_nome'] = $usuario_dados['nome'];
                    $_SESSION['usuario_login'] = $usuario_dados['usuario'];
                    
                    // Registrar o login bem-sucedido
                    $ip = $_SERVER['REMOTE_ADDR'];
                    $stmt = $conn->prepare("INSERT INTO logs_acesso (usuario_id, ip, sucesso) VALUES (:usuario_id, :ip, TRUE)");
                    $stmt->bindParam(':usuario_id', $usuario_dados['id']);
                    $stmt->bindParam(':ip', $ip);
                    $stmt->execute();
                    
                    // Atualizar o último acesso
                    $stmt = $conn->prepare("UPDATE usuarios SET ultimo_acesso = NOW() WHERE id = :id");
                    $stmt->bindParam(':id', $usuario_dados['id']);
                    $stmt->execute();
                    
                    // Redirecionar para a página principal
                    header("Location: dashboard.php");
                    exit;
                } else {
                    // Senha incorreta
                    $erro = "Usuário ou senha incorretos.";
                    
                    // Registrar a tentativa de login mal-sucedida
                    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE usuario = :usuario");
                    $stmt->bindParam(':usuario', $usuario);
                    $stmt->execute();
                    $usuario_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
                    
                    $ip = $_SERVER['REMOTE_ADDR'];
                    $stmt = $conn->prepare("INSERT INTO logs_acesso (usuario_id, ip, sucesso) VALUES (:usuario_id, :ip, FALSE)");
                    $stmt->bindParam(':usuario_id', $usuario_id);
                    $stmt->bindParam(':ip', $ip);
                    $stmt->execute();
                }
            } else {
                // Usuário não encontrado
                $erro = "Usuário ou senha incorretos.";
            }
        } catch(PDOException $e) {
            $erro = "Erro ao processar o login. Por favor, tente novamente mais tarde.";
            // Em ambiente de produção, você deve registrar o erro em um log
            // error_log("Erro no login: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Formulário de Login</title>
    <script src="script.js?v=1"></script>
</head>
<body>
    <section class="container">
        <div class="login-container">
            <div class="circle circle-one"></div>
            <div class="form-container">
                <h1 class="opacity">LOGIN</h1>
                
                <?php if (isset($_SESSION['mensagem'])): ?>
                <div class="success-message">
                    <?php 
                    echo $_SESSION['mensagem']; 
                    unset($_SESSION['mensagem']); // Limpar a mensagem após exibir
                    ?>
                </div>
                <?php endif; ?>
                
                <?php if (isset($erro)): ?>
                <div class="error-message">
                    <?php echo $erro; ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <input type="text" name="usuario" placeholder="USUÁRIO" value="<?php echo isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : ''; ?>" />
                    <input type="password" name="senha" placeholder="SENHA" />
                    <button type="submit" class="opacity">ENTRAR</button>
                </form>
                <div class="register-forget opacity">
                    <a href="cadastro.php">CADASTRAR</a>
                    <a href="recuperar_senha.php">ESQUECI A SENHA</a>
                </div>
            </div>
            <div class="circle circle-two"></div>
        </div>
        <div class="theme-btn-container"></div>
    </section>
</body>
</html>