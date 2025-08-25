<?php
/**
 * Componente de Notificações para o Header
 * Exibe notificações não lidas do usuário
 */

if (!isset($_SESSION['usuario_id'])) {
    return;
}

require_once 'sistema_notificacoes.php';

$sistema_notificacoes = new SistemaNotificacoes();
$usuario_id = $_SESSION['usuario_id'];
$total_nao_lidas = $sistema_notificacoes->contarNotificacoesNaoLidas($usuario_id);
$notificacoes = $sistema_notificacoes->buscarNotificacoesNaoLidas($usuario_id, 5);
?>

<div class="notifications-container" style="position: relative; display: inline-block;">
    <button class="notification-bell" onclick="toggleNotifications()" style="
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.25);
        color: white;
        padding: 8px 12px;
        border-radius: 20px;
        cursor: pointer;
        font-size: 14px;
        position: relative;
        transition: all 0.3s ease;
    " onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
        🔔 Notificações
        <?php if ($total_nao_lidas > 0): ?>
            <span class="notification-badge" style="
                position: absolute;
                top: -5px;
                right: -5px;
                background: #ff4757;
                color: white;
                border-radius: 50%;
                width: 20px;
                height: 20px;
                font-size: 11px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
            "><?= min($total_nao_lidas, 9) ?><?= $total_nao_lidas > 9 ? '+' : '' ?></span>
        <?php endif; ?>
    </button>
    
    <div id="notifications-dropdown" class="notifications-dropdown" style="
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        width: 350px;
        max-height: 400px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        margin-top: 5px;
    ">
        <div class="notifications-header" style="
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            background: #f8f9fa;
            border-radius: 10px 10px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        ">
            <h6 style="margin: 0; color: #333; font-weight: 600;">Notificações</h6>
            <?php if ($total_nao_lidas > 0): ?>
                <button onclick="marcarTodasComoLidas()" style="
                    background: none;
                    border: none;
                    color: #007bff;
                    font-size: 12px;
                    cursor: pointer;
                    text-decoration: underline;
                ">Marcar todas como lidas</button>
            <?php endif; ?>
        </div>
        
        <div class="notifications-list">
            <?php if (empty($notificacoes)): ?>
                <div style="padding: 30px 20px; text-align: center; color: #666;">
                    <i style="font-size: 2em; margin-bottom: 10px;">🔔</i>
                    <p style="margin: 0;">Nenhuma notificação nova</p>
                </div>
            <?php else: ?>
                <?php foreach ($notificacoes as $notificacao): ?>
                    <div class="notification-item" onclick="abrirNotificacao(<?= $notificacao['id'] ?>, '<?= htmlspecialchars($notificacao['link'] ?? '') ?>')" style="
                        padding: 15px 20px;
                        border-bottom: 1px solid #f0f0f0;
                        cursor: pointer;
                        transition: background 0.2s ease;
                        background: <?= !$notificacao['lida'] ? '#f8f9ff' : 'white' ?>;
                    " onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='<?= !$notificacao['lida'] ? '#f8f9ff' : 'white' ?>'">
                        <div style="display: flex; align-items: flex-start; gap: 10px;">
                            <div style="font-size: 1.2em; margin-top: 2px;">
                                <?php
                                switch ($notificacao['tipo']) {
                                    case 'forum_resposta':
                                        echo '💬';
                                        break;
                                    case 'forum_mencao':
                                        echo '👤';
                                        break;
                                    case 'badge_conquistada':
                                        echo '🏆';
                                        break;
                                    case 'nivel_subiu':
                                        echo '⭐';
                                        break;
                                    default:
                                        echo '📢';
                                }
                                ?>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: #333; font-size: 14px; margin-bottom: 4px;">
                                    <?= htmlspecialchars($notificacao['titulo']) ?>
                                </div>
                                <div style="color: #666; font-size: 13px; line-height: 1.4; margin-bottom: 6px;">
                                    <?= htmlspecialchars($notificacao['mensagem']) ?>
                                </div>
                                <div style="color: #999; font-size: 11px;">
                                    <?= date('d/m/Y H:i', strtotime($notificacao['data_criacao'])) ?>
                                </div>
                            </div>
                            <?php if (!$notificacao['lida']): ?>
                                <div style="
                                    width: 8px;
                                    height: 8px;
                                    background: #007bff;
                                    border-radius: 50%;
                                    margin-top: 6px;
                                "></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($notificacoes)): ?>
            <div style="padding: 10px 20px; text-align: center; border-top: 1px solid #eee;">
                <a href="todas_notificacoes.php" style="
                    color: #007bff;
                    text-decoration: none;
                    font-size: 13px;
                    font-weight: 500;
                ">Ver todas as notificações</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleNotifications() {
    const dropdown = document.getElementById('notifications-dropdown');
    const isVisible = dropdown.style.display === 'block';
    
    // Fechar outros dropdowns se existirem
    document.querySelectorAll('.notifications-dropdown').forEach(d => {
        if (d !== dropdown) d.style.display = 'none';
    });
    
    dropdown.style.display = isVisible ? 'none' : 'block';
    
    // Fechar ao clicar fora
    if (!isVisible) {
        setTimeout(() => {
            document.addEventListener('click', closeNotificationsOnClickOutside);
        }, 100);
    }
}

function closeNotificationsOnClickOutside(event) {
    const container = document.querySelector('.notifications-container');
    if (!container.contains(event.target)) {
        document.getElementById('notifications-dropdown').style.display = 'none';
        document.removeEventListener('click', closeNotificationsOnClickOutside);
    }
}

function abrirNotificacao(notificacaoId, link) {
    // Marcar como lida
    fetch('ajax_notificacoes.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'acao=marcar_lida&id=' + notificacaoId
    }).then(() => {
        // Redirecionar se houver link
        if (link && link.trim() !== '') {
            window.location.href = link;
        } else {
            // Apenas recarregar para atualizar o contador
            location.reload();
        }
    });
}

function marcarTodasComoLidas() {
    fetch('ajax_notificacoes.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'acao=marcar_todas_lidas'
    }).then(() => {
        location.reload();
    });
}

// Fechar dropdown ao pressionar ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        document.getElementById('notifications-dropdown').style.display = 'none';
        document.removeEventListener('click', closeNotificationsOnClickOutside);
    }
});
</script>

<style>
.notifications-dropdown::-webkit-scrollbar {
    width: 6px;
}

.notifications-dropdown::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.notifications-dropdown::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.notifications-dropdown::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

@media (max-width: 768px) {
    .notifications-dropdown {
        width: 300px !important;
        right: -50px !important;
    }
}

@media (max-width: 480px) {
    .notifications-dropdown {
        width: 280px !important;
        right: -100px !important;
    }
}
</style>
