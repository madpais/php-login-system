<?php
// Configurações de conexão com o banco de dados
define('DB_HOST', '127.0.0.1:3306'); // Host do banco de dados
define('DB_USER', 'root');      // Usuário do MySQL (altere conforme necessário)
define('DB_PASS', '');          // Senha do MySQL (altere conforme necessário)
define('DB_NAME', 'db_daydreamming_project');  // Nome do banco de dados

// Função para conectar ao banco de dados
function conectarBD() {
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        // Configurar o PDO para lançar exceções em caso de erros
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Configurar o charset para utf8
        $conn->exec("SET NAMES utf8");
        return $conn;
    } catch(PDOException $e) {
        // Em ambiente de produção, você deve registrar o erro em um log
        // e mostrar uma mensagem genérica para o usuário
        die("Erro na conexão com o banco de dados: " . $e->getMessage());
    }
}