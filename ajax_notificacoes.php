<?php
/**
 * AJAX Handler para Notificações
 * Processa ações relacionadas às notificações
 */

session_start();
require_once 'config.php';
require_once 'sistema_notificacoes.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Usuário não autenticado']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$sistema_notificacoes = new SistemaNotificacoes();

// Verificar ação solicitada
$acao = $_POST['acao'] ?? '';

switch ($acao) {
    case 'marcar_lida':
        $notificacao_id = intval($_POST['id'] ?? 0);
        
        if ($notificacao_id > 0) {
            $sucesso = $sistema_notificacoes->marcarComoLida($notificacao_id, $usuario_id);
            
            if ($sucesso) {
                echo json_encode(['sucesso' => true, 'mensagem' => 'Notificação marcada como lida']);
            } else {
                echo json_encode(['erro' => 'Erro ao marcar notificação como lida']);
            }
        } else {
            echo json_encode(['erro' => 'ID de notificação inválido']);
        }
        break;
        
    case 'marcar_todas_lidas':
        $sucesso = $sistema_notificacoes->marcarTodasComoLidas($usuario_id);
        
        if ($sucesso) {
            echo json_encode(['sucesso' => true, 'mensagem' => 'Todas as notificações foram marcadas como lidas']);
        } else {
            echo json_encode(['erro' => 'Erro ao marcar todas as notificações como lidas']);
        }
        break;
        
    case 'buscar_notificacoes':
        $limite = intval($_POST['limite'] ?? 10);
        $notificacoes = $sistema_notificacoes->buscarNotificacoesNaoLidas($usuario_id, $limite);
        $total_nao_lidas = $sistema_notificacoes->contarNotificacoesNaoLidas($usuario_id);
        
        echo json_encode([
            'sucesso' => true,
            'notificacoes' => $notificacoes,
            'total_nao_lidas' => $total_nao_lidas
        ]);
        break;
        
    case 'contar_nao_lidas':
        $total = $sistema_notificacoes->contarNotificacoesNaoLidas($usuario_id);
        echo json_encode(['sucesso' => true, 'total' => $total]);
        break;
        
    default:
        http_response_code(400);
        echo json_encode(['erro' => 'Ação não reconhecida']);
        break;
}
?>
