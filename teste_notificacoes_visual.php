<?php
require_once "config.php";
require_once "sistema_notificacoes.php";
iniciarSessaoSegura();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$sistema = new SistemaNotificacoes();
$usuario_id = $_SESSION["usuario_id"];
$total_nao_lidas = $sistema->contarNotificacoesNaoLidas($usuario_id);
$notificacoes = $sistema->buscarNotificacoesNaoLidas($usuario_id, 5);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste Visual - Sistema de Notificações</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .notification-badge {
            background: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            position: absolute;
            top: -5px;
            right: -5px;
        }
        .notification-item {
            border-left: 4px solid #007bff;
            background: #f8f9fa;
            margin-bottom: 10px;
            padding: 15px;
            border-radius: 5px;
        }
        .notification-item.unread {
            background: #e3f2fd;
            border-left-color: #2196f3;
        }
        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        .badge-icon { background: #ffd700; color: #333; }
        .level-icon { background: #4caf50; color: white; }
        .forum-icon { background: #2196f3; color: white; }
        .system-icon { background: #9c27b0; color: white; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4><i class="fas fa-bell"></i> Sistema de Notificações</h4>
                        <div class="position-relative">
                            <i class="fas fa-bell fa-2x"></i>
                            <?php if ($total_nao_lidas > 0): ?>
                                <span class="notification-badge"><?= $total_nao_lidas ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <strong>Total não lidas:</strong> <?= $total_nao_lidas ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-success">
                                    <strong>Usuário:</strong> <?= $_SESSION["usuario_nome"] ?>
                                </div>
                            </div>
                        </div>
                        
                        <h5>Notificações Recentes:</h5>
                        
                        <?php if (empty($notificacoes)): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-bell-slash"></i> Nenhuma notificação não lida
                            </div>
                        <?php else: ?>
                            <?php foreach ($notificacoes as $notif): ?>
                                <div class="notification-item unread d-flex">
                                    <div class="notification-icon <?php
                                        switch ($notif["tipo"]) {
                                            case "badge_conquistada": echo "badge-icon"; break;
                                            case "nivel_subiu": echo "level-icon"; break;
                                            case "forum_resposta":
                                            case "forum_mencao": echo "forum-icon"; break;
                                            default: echo "system-icon";
                                        }
                                    ?>">
                                        <?php
                                        switch ($notif["tipo"]) {
                                            case "badge_conquistada": echo "🏆"; break;
                                            case "nivel_subiu": echo "📈"; break;
                                            case "forum_resposta": echo "💬"; break;
                                            case "forum_mencao": echo "👤"; break;
                                            default: echo "🔔";
                                        }
                                        ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?= htmlspecialchars($notif["titulo"]) ?></h6>
                                        <p class="mb-1"><?= htmlspecialchars($notif["mensagem"]) ?></p>
                                        <small class="text-muted">
                                            <?= date("d/m/Y H:i", strtotime($notif["data_criacao"])) ?>
                                            <?php if ($notif["link"]): ?>
                                                | <a href="<?= htmlspecialchars($notif["link"]) ?>">Ver mais</a>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <div class="mt-4">
                            <a href="todas_notificacoes.php" class="btn btn-primary">
                                <i class="fas fa-list"></i> Ver Todas as Notificações
                            </a>
                            <a href="pagina_usuario.php" class="btn btn-secondary">
                                <i class="fas fa-user"></i> Meu Perfil
                            </a>
                            <a href="index.php" class="btn btn-success">
                                <i class="fas fa-home"></i> Início
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php require_once __DIR__ . '/footer.php'; ?>
</body>
</html>