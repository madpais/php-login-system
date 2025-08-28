<?php
// Componente de header para mostrar status de login - Versão para pasta paises
require_once '../config.php';

// Iniciar sessão de forma segura
iniciarSessaoSegura();

// Headers anti-cache
if (!headers_sent()) {
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
}

// Verificar se o usuário está logado
$usuario_logado = isset($_SESSION['usuario_id']);
$usuario_nome = '';
$usuario_login = '';
$is_admin = false;

if ($usuario_logado) {
    // SEMPRE buscar dados atualizados do banco para evitar inconsistências
    try {
        $pdo = conectarBD();
        $stmt = $pdo->prepare("SELECT nome, usuario, is_admin, ativo FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['usuario_id']]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user_data && $user_data['ativo']) {
            // Usar dados do banco (sempre atualizados)
            $usuario_nome = $user_data['nome'];
            $usuario_login = $user_data['usuario'];
            $is_admin = (bool)$user_data['is_admin'];

            // Atualizar sessão com dados corretos do banco
            $_SESSION['usuario_nome'] = $usuario_nome;
            $_SESSION['usuario_login'] = $usuario_login;
            $_SESSION['is_admin'] = $is_admin;
        } else {
            // Usuário não encontrado ou inativo - limpar sessão
            $_SESSION = array();
            $usuario_logado = false;
        }
    } catch (Exception $e) {
        // Em caso de erro, usar dados da sessão como fallback
        $usuario_nome = $_SESSION['usuario_nome'] ?? '';
        $usuario_login = $_SESSION['usuario_login'] ?? '';
        $is_admin = $_SESSION['is_admin'] ?? false;
    }
}
?>