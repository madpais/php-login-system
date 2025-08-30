<?php
/**
 * Script de Instalação Limpa do Sistema DayDreamming
 * 
 * Este script remove o banco existente e cria tudo do zero
 * com todas as 22 tabelas e dados iniciais
 */

echo "🧹 INSTALAÇÃO LIMPA DO SISTEMA DAYDREAMMING\n";
echo "===========================================\n\n";

// Configurações do banco de dados
$host = 'localhost';
$dbname = 'daydreamming_db';
$username = 'root';
$password = '';

try {
    // Conectar ao MySQL
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conectado ao MySQL\n";
    
    // Remover banco existente
    echo "🗑️ Removendo banco existente...\n";
    $pdo->exec("DROP DATABASE IF EXISTS $dbname");
    echo "✅ Banco '$dbname' removido\n";
    
    // Criar banco novo
    $pdo->exec("CREATE DATABASE $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Banco '$dbname' criado\n";
    
    // Conectar ao banco específico
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conectado ao banco '$dbname'\n\n";
    
    echo "📋 Executando script de instalação completa...\n";
    echo "===============================================\n";
    
    // Incluir e executar o script de instalação
    include 'setup_database_completo.php';
    
} catch(PDOException $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    exit(1);
}
?>
