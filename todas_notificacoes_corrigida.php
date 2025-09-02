<?php
require_once 'config.php';
require_once 'verificar_auth.php';
require_once 'sistema_notificacoes.php';

// Iniciar sessão de forma segura
iniciarSessaoSegura();

// Verificar se o usuário está logado
verificarLogin();

$usuario_id = $_SESSION['usuario_id'];
$sistema_notificacoes = new SistemaNotificacoes();

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    if ($acao === 'marcar_todas_lidas') {
        $sistema_notificacoes->marcarTodasComoLidas($usuario_id);
        header("Location: todas_notificacoes_corrigida.php");
        exit;
    }
}

// Buscar todas as notificações
$notificacoes = $sistema_notificacoes->buscarTodasNotificacoes($usuario_id, 100);
$total_nao_lidas = $sistema_notificacoes->contarNotificacoesNaoLidas($usuario_id);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificações - DayDreaming</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .main-container {
            padding: 20px 0;
        }
        
        .notifications-header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .notifications-content {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .notification-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #dee2e6;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .notification-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .notification-item.unread {
            background: #e3f2fd;
            border-left-color: #2196f3;
        }
        
        .notification-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 20px;
        }
        
        .notification-icon.forum { background: #2196f3; color: white; }
        .notification-icon.badge { background: #ffd700; color: #333; }
        .notification-icon.level { background: #4caf50; color: white; }
        .notification-icon.system { background: #9c27b0; color: white; }
        
        .notification-content h6 {
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .notification-content p {
            margin-bottom: 8px;
            color: #666;
        }
        
        .notification-meta {
            font-size: 12px;
            color: #999;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .empty-state i {
            font-size: 4em;
            margin-bottom: 20px;
            color: #ddd;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
        }
        
        .btn-custom {
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <!-- Header de Status -->
    <?php include 'header_status.php'; ?>
    
    <div class="container main-container">
        <!-- Header da Página -->
        <div class="notifications-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0">
                        <i class="fas fa-bell text-primary"></i>
                        Notificações
                    </h1>
                    <p class="text-muted mb-0">Acompanhe suas atividades e interações</p>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <h3 class="mb-1"><?= $total_nao_lidas ?></h3>
                        <small>Não lidas</small>
                    </div>
                </div>
            </div>
            
            <?php if ($total_nao_lidas > 0): ?>
                <div class="mt-3">
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="acao" value="marcar_todas_lidas">
                        <button type="submit" class="btn btn-outline-primary btn-custom">
                            <i class="fas fa-check-double"></i>
                            Marcar todas como lidas
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Conteúdo das Notificações -->
        <div class="notifications-content">
            <?php if (empty($notificacoes)): ?>
                <div class="empty-state">
                    <i class="fas fa-bell-slash"></i>
                    <h3>Nenhuma notificação</h3>
                    <p>Você não possui notificações ainda. Quando houver atividades relevantes, elas aparecerão aqui.</p>
                    <a href="index.php" class="btn btn-primary btn-custom mt-3">
                        <i class="fas fa-home"></i>
                        Voltar ao Início
                    </a>
                </div>
            <?php else: ?>
                <div class="notifications-list">
                    <?php foreach ($notificacoes as $notificacao): ?>
                        <div class="notification-item <?= !$notificacao['lida'] ? 'unread' : '' ?>" 
                             onclick="abrirNotificacao(<?= $notificacao['id'] ?>, '<?= htmlspecialchars($notificacao['link'] ?? '') ?>')">
                            <div class="d-flex align-items-start">
                                <div class="notification-icon <?php
                                    switch ($notificacao['tipo']) {
                                        case 'forum_resposta':
                                        case 'forum_mencao':
                                            echo 'forum';
                                            break;
                                        case 'badge_conquistada':
                                            echo 'badge';
                                            break;
                                        case 'nivel_subiu':
                                            echo 'level';
                                            break;
                                        default:
                                            echo 'system';
                                    }
                                ?>">
                                    <?php
                                    switch ($notificacao['tipo']) {
                                        case 'forum_resposta':
                                            echo '<i class="fas fa-comment"></i>';
                                            break;
                                        case 'forum_mencao':
                                            echo '<i class="fas fa-at"></i>';
                                            break;
                                        case 'badge_conquistada':
                                            echo '<i class="fas fa-trophy"></i>';
                                            break;
                                        case 'nivel_subiu':
                                            echo '<i class="fas fa-star"></i>';
                                            break;
                                        default:
                                            echo '<i class="fas fa-bell"></i>';
                                    }
                                    ?>
                                </div>
                                
                                <div class="notification-content flex-grow-1">
                                    <h6>
                                        <?= htmlspecialchars($notificacao['titulo']) ?>
                                        <?php if (!$notificacao['lida']): ?>
                                            <span class="badge bg-primary ms-2">Nova</span>
                                        <?php endif; ?>
                                    </h6>
                                    <p><?= htmlspecialchars($notificacao['mensagem']) ?></p>
                                    <div class="notification-meta">
                                        <i class="fas fa-clock"></i>
                                        <?= date('d/m/Y H:i', strtotime($notificacao['data_criacao'])) ?>
                                        <?php if ($notificacao['link']): ?>
                                            | <i class="fas fa-external-link-alt"></i> Clique para ver mais
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="text-center mt-4">
                    <a href="pagina_usuario.php" class="btn btn-secondary btn-custom me-2">
                        <i class="fas fa-user"></i>
                        Meu Perfil
                    </a>
                    <a href="index.php" class="btn btn-primary btn-custom">
                        <i class="fas fa-home"></i>
                        Página Inicial
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
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
                    // Apenas recarregar para atualizar o status
                    location.reload();
                }
            }).catch(error => {
                console.error('Erro ao marcar notificação como lida:', error);
                // Redirecionar mesmo com erro
                if (link && link.trim() !== '') {
                    window.location.href = link;
                }
            });
        }
    </script>
<?php require_once __DIR__ . '/footer.php'; ?>
</body>
</html>
