<?php
/**
 * Sistema de Notificações
 * Gerencia notificações de interações no fórum e outras atividades
 */

require_once 'config.php';

class SistemaNotificacoes {
    private $pdo;
    
    public function __construct() {
        $this->pdo = conectarBD();
    }
    
    /**
     * Criar notificação para resposta no fórum
     */
    public function notificarRespostaForum($topico_id, $autor_resposta_id, $conteudo_resposta) {
        try {
            // Buscar o autor do tópico
            $stmt = $this->pdo->prepare("
                SELECT usuario_id, titulo 
                FROM forum_topicos 
                WHERE id = ?
            ");
            $stmt->execute([$topico_id]);
            $topico = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($topico && $topico['usuario_id'] != $autor_resposta_id) {
                // Buscar nome do autor da resposta
                $stmt = $this->pdo->prepare("SELECT nome FROM usuarios WHERE id = ?");
                $stmt->execute([$autor_resposta_id]);
                $autor_resposta = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $titulo = "Nova resposta no seu tópico";
                $mensagem = $autor_resposta['nome'] . " respondeu ao seu tópico \"" . $topico['titulo'] . "\"";
                $link = "forum.php?topico=" . $topico_id;
                
                $this->criarNotificacao(
                    $topico['usuario_id'],
                    'forum_resposta',
                    $titulo,
                    $mensagem,
                    $link
                );
            }
        } catch (Exception $e) {
            error_log("Erro ao criar notificação de resposta: " . $e->getMessage());
        }
    }
    
    /**
     * Criar notificação para menção no fórum
     */
    public function notificarMencaoForum($usuario_mencionado_id, $autor_mencao_id, $topico_id, $conteudo) {
        try {
            if ($usuario_mencionado_id != $autor_mencao_id) {
                // Buscar nome do autor da menção
                $stmt = $this->pdo->prepare("SELECT nome FROM usuarios WHERE id = ?");
                $stmt->execute([$autor_mencao_id]);
                $autor_mencao = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Buscar título do tópico
                $stmt = $this->pdo->prepare("SELECT titulo FROM forum_topicos WHERE id = ?");
                $stmt->execute([$topico_id]);
                $topico = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $titulo = "Você foi mencionado no fórum";
                $mensagem = $autor_mencao['nome'] . " mencionou você no tópico \"" . $topico['titulo'] . "\"";
                $link = "forum.php?topico=" . $topico_id;
                
                $this->criarNotificacao(
                    $usuario_mencionado_id,
                    'forum_mencao',
                    $titulo,
                    $mensagem,
                    $link
                );
            }
        } catch (Exception $e) {
            error_log("Erro ao criar notificação de menção: " . $e->getMessage());
        }
    }
    
    /**
     * Criar notificação para badge conquistada
     */
    public function notificarBadgeConquistada($usuario_id, $badge_nome, $badge_descricao) {
        try {
            $titulo = "Nova conquista desbloqueada!";
            $mensagem = "Parabéns! Você conquistou a badge \"" . $badge_nome . "\": " . $badge_descricao;
            $link = "pagina_usuario.php#badges";
            
            $this->criarNotificacao(
                $usuario_id,
                'badge_conquistada',
                $titulo,
                $mensagem,
                $link
            );
        } catch (Exception $e) {
            error_log("Erro ao criar notificação de badge: " . $e->getMessage());
        }
    }
    
    /**
     * Criar notificação para subida de nível
     */
    public function notificarSubidaNivel($usuario_id, $nivel_novo) {
        try {
            $titulo = "Nível aumentado!";
            $mensagem = "Parabéns! Você subiu para o nível " . $nivel_novo . "!";
            $link = "pagina_usuario.php#progresso";
            
            $this->criarNotificacao(
                $usuario_id,
                'nivel_subiu',
                $titulo,
                $mensagem,
                $link
            );
        } catch (Exception $e) {
            error_log("Erro ao criar notificação de nível: " . $e->getMessage());
        }
    }
    
    /**
     * Criar notificação genérica
     */
    private function criarNotificacao($usuario_id, $tipo, $titulo, $mensagem, $link = null) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO notificacoes_usuario (usuario_id, tipo, titulo, mensagem, link)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$usuario_id, $tipo, $titulo, $mensagem, $link]);
        } catch (Exception $e) {
            error_log("Erro ao criar notificação: " . $e->getMessage());
        }
    }
    
    /**
     * Buscar notificações não lidas do usuário
     */
    public function buscarNotificacoesNaoLidas($usuario_id, $limite = 10) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, tipo, titulo, mensagem, link, data_criacao
                FROM notificacoes_usuario
                WHERE usuario_id = ? AND lida = FALSE
                ORDER BY data_criacao DESC
                LIMIT ?
            ");
            $stmt->execute([$usuario_id, $limite]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao buscar notificações: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Contar notificações não lidas
     */
    public function contarNotificacoesNaoLidas($usuario_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as total
                FROM notificacoes_usuario
                WHERE usuario_id = ? AND lida = FALSE
            ");
            $stmt->execute([$usuario_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (Exception $e) {
            error_log("Erro ao contar notificações: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Marcar notificação como lida
     */
    public function marcarComoLida($notificacao_id, $usuario_id) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE notificacoes_usuario 
                SET lida = TRUE 
                WHERE id = ? AND usuario_id = ?
            ");
            $stmt->execute([$notificacao_id, $usuario_id]);
            return true;
        } catch (Exception $e) {
            error_log("Erro ao marcar notificação como lida: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Marcar todas as notificações como lidas
     */
    public function marcarTodasComoLidas($usuario_id) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE notificacoes_usuario 
                SET lida = TRUE 
                WHERE usuario_id = ? AND lida = FALSE
            ");
            $stmt->execute([$usuario_id]);
            return true;
        } catch (Exception $e) {
            error_log("Erro ao marcar todas as notificações como lidas: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Buscar todas as notificações do usuário (lidas e não lidas)
     */
    public function buscarTodasNotificacoes($usuario_id, $limite = 50) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, tipo, titulo, mensagem, link, lida, data_criacao
                FROM notificacoes_usuario
                WHERE usuario_id = ?
                ORDER BY data_criacao DESC
                LIMIT ?
            ");
            $stmt->execute([$usuario_id, $limite]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao buscar todas as notificações: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Processar menções em texto (@usuario)
     */
    public function processarMencoes($texto, $topico_id, $autor_id) {
        // Encontrar menções no formato @usuario
        preg_match_all('/@([a-zA-Z0-9_]+)/', $texto, $matches);
        
        if (!empty($matches[1])) {
            foreach ($matches[1] as $usuario_mencionado) {
                // Buscar ID do usuário mencionado
                $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE usuario = ?");
                $stmt->execute([$usuario_mencionado]);
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($usuario) {
                    $this->notificarMencaoForum($usuario['id'], $autor_id, $topico_id, $texto);
                }
            }
        }
    }
}

// Função helper para usar o sistema de notificações
function obterSistemaNotificacoes() {
    return new SistemaNotificacoes();
}
?>
