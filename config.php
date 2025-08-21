<?php
/**
 * StudyAbroad - Configuration File
 * 
 * This file contains all the configuration settings for the application.
 * Modify these settings according to your environment and requirements.
 * 
 * @author StudyAbroad Team
 * @version 2.0
 * @since 2025
 */

// Prevent direct access
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__);
}

// Environment Configuration
define('ENVIRONMENT', 'development'); // development, staging, production
define('DEBUG', true); // Set to false in production

// Database Configuration (Legacy compatibility maintained)
define('DB_HOST', 'localhost'); // Host do banco de dados
define('DB_USER', 'root');      // Usuário do MySQL
define('DB_PASS', '');          // Senha do MySQL (deixe vazio se não tiver senha)
define('DB_NAME', 'db_daydreamming_project');  // Nome do banco de dados
define('DB_CHARSET', 'utf8mb4');

// Application Configuration
define('APP_NAME', 'StudyAbroad');
define('APP_VERSION', '2.0.0');
define('APP_DESCRIPTION', 'Plataforma completa para estudar no exterior com testes internacionais, universidades e bolsas de estudo');
define('APP_KEYWORDS', 'estudar no exterior, testes internacionais, TOEFL, IELTS, SAT, GRE, GMAT, universidades, bolsas de estudo');
define('APP_AUTHOR', 'StudyAbroad Team');
define('APP_EMAIL', 'contato@studyabroad.com');
define('APP_PHONE', '+55 11 99999-9999');

// URL Configuration
define('SITE_URL', 'http://localhost:8080');
define('ADMIN_URL', SITE_URL . '/admin');
define('API_URL', SITE_URL . '/api');

// Path Configuration
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('CACHE_PATH', ROOT_PATH . '/cache');
define('LOG_PATH', ROOT_PATH . '/logs');



// Security Configuration
define('ENCRYPTION_KEY', 'your-secret-encryption-key-here');
define('SESSION_LIFETIME', 3600); // 1 hour
define('CSRF_TOKEN_LIFETIME', 1800); // 30 minutes

// Language Configuration
define('DEFAULT_LANGUAGE', 'pt-BR');
define('DEFAULT_TIMEZONE', 'America/Sao_Paulo');
date_default_timezone_set(DEFAULT_TIMEZONE);

// Error Reporting
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
}

// Função para conectar ao banco de dados (Legacy compatibility maintained)
function conectarBD() {
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
        // Configurar o PDO para lançar exceções em caso de erros
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Configurar o charset para utf8mb4
        $conn->exec("SET NAMES utf8mb4");
        return $conn;
    } catch(PDOException $e) {
        // Em ambiente de produção, você deve registrar o erro em um log
        // e mostrar uma mensagem genérica para o usuário
        if (ENVIRONMENT === 'development') {
            die("Erro na conexão com o banco de dados: " . $e->getMessage());
        } else {
            error_log("Database connection error: " . $e->getMessage());
            die("Erro interno do servidor. Tente novamente mais tarde.");
        }
    }
}

// Helper Functions
function site_url($path = '') {
    return SITE_URL . '/' . ltrim($path, '/');
}

function asset_url($path = '') {
    return SITE_URL . '/public/' . ltrim($path, '/');
}

function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function get_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function sanitize_input($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
        return false;
    }
    
    if (time() - $_SESSION['csrf_token_time'] > CSRF_TOKEN_LIFETIME) {
        unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Session Configuration (must be set before session_start())
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Strict');
}

// Auto-create directories if they don't exist
$directories = [UPLOAD_PATH, CACHE_PATH, LOG_PATH];
foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

?>