<?php
session_start();
require_once 'config.php';

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obter os dados do formulário
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';
    $confirmar_senha = isset($_POST['confirmar_senha']) ? trim($_POST['confirmar_senha']) : '';
    
    // Validação básica
    $erros = [];
    
    if (empty($nome)) {
        $erros[] = "O nome é obrigatório.";
    }
    
    if (empty($usuario)) {
        $erros[] = "O nome de usuário é obrigatório.";
    } elseif (strlen($usuario) < 4) {
        $erros[] = "O nome de usuário deve ter pelo menos 4 caracteres.";
    }
    
    if (empty($email)) {
        $erros[] = "O e-mail é obrigatório.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "Por favor, insira um e-mail válido.";
    }
    
    if (empty($senha)) {
        $erros[] = "A senha é obrigatória.";
    } elseif (strlen($senha) < 6) {
        $erros[] = "A senha deve ter pelo menos 6 caracteres.";
    }
    
    if ($senha !== $confirmar_senha) {
        $erros[] = "As senhas não coincidem.";
    }
    
    // Se não houver erros, prosseguir com o cadastro
    if (empty($erros)) {
        try {
            // Conectar ao banco de dados
            $conn = conectarBD();
            
            // Verificar se o usuário já existe
            $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE usuario = :usuario OR email = :email");
            $stmt->bindParam(':usuario', $usuario);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->fetchColumn() > 0) {
                // Verificar qual campo está duplicado
                $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE usuario = :usuario");
                $stmt->bindParam(':usuario', $usuario);
                $stmt->execute();
                
                if ($stmt->fetchColumn() > 0) {
                    $erros[] = "Este nome de usuário já está em uso.";
                } else {
                    $erros[] = "Este e-mail já está cadastrado.";
                }
            } else {
                // Criptografar a senha
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                
                // Inserir o novo usuário
                $stmt = $conn->prepare("INSERT INTO usuarios (nome, usuario, senha, email) VALUES (:nome, :usuario, :senha, :email)");
                $stmt->bindParam(':nome', $nome);
                $stmt->bindParam(':usuario', $usuario);
                $stmt->bindParam(':senha', $senha_hash);
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                
                // Redirecionar para a página de login com mensagem de sucesso
                $_SESSION['mensagem'] = "Cadastro realizado com sucesso! Faça login para continuar.";
                header("Location: login.php");
                exit;
            }
        } catch(PDOException $e) {
            $erros[] = "Erro ao processar o cadastro. Por favor, tente novamente mais tarde.";
            // Em ambiente de produção, você deve registrar o erro em um log
            // error_log("Erro no cadastro: " . $e->getMessage());
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
    <title>Cadastro de Usuário</title>
    <script src="script.js?v=1"></script>
    <style>
        .error-list {
            background-color: rgba(231, 76, 60, 0.2);
            border-left: 4px solid #e74c3c;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            color: #fff;
        }
        
        .error-list ul {
            margin: 5px 0 5px 20px;
            padding: 0;
        }
        
        .form-container {
            max-width: 400px;
        }
        
        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <section class="container">
        <div class="login-container">
            <div class="circle circle-one"></div>
            <div class="form-container">
                <h1 class="opacity">CADASTRO</h1>
                
                <?php if (!empty($erros)): ?>
                <div class="error-list">
                    <ul>
                        <?php foreach ($erros as $erro): ?>
                            <li><?php echo $erro; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <input type="text" name="nome" placeholder="NOME COMPLETO" value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>" />
                    <input type="text" name="usuario" placeholder="NOME DE USUÁRIO" value="<?php echo isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : ''; ?>" />
                    <input type="email" name="email" placeholder="E-MAIL" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" />
                    <input type="password" name="senha" placeholder="SENHA" />
                    <input type="password" name="confirmar_senha" placeholder="CONFIRMAR SENHA" />
                    <button type="submit" class="opacity">CADASTRAR</button>
                </form>
                <a href="login.php" class="back-link opacity">VOLTAR PARA LOGIN</a>
            </div>
            <div class="circle circle-two"></div>
        </div>
        <div class="theme-btn-container"></div>
    </section>
</body>
</html>