<?php
require_once "config.php";
iniciarSessaoSegura();

if (!isset($_SESSION["usuario_id"])) {
    echo "<h1>❌ Usuário não logado</h1>";
    echo "<a href=\"login.php\">Fazer Login</a>";
    exit;
}

try {
    require_once "sistema_notificacoes.php";
    $sistema = new SistemaNotificacoes();
    $usuario_id = $_SESSION["usuario_id"];
    $total_nao_lidas = $sistema->contarNotificacoesNaoLidas($usuario_id);
    $notificacoes = $sistema->buscarTodasNotificacoes($usuario_id, 20);
} catch (Exception $e) {
    echo "<h1>❌ Erro: " . htmlspecialchars($e->getMessage()) . "</h1>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste Simples - Notificações</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; padding: 20px; }
        .notification-card { margin-bottom: 15px; }
        .unread { border-left: 4px solid #007bff; background: #e3f2fd; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>🔔 Notificações Simples</h4>
                        <small>Total não lidas: <span class="badge bg-primary"><?= $total_nao_lidas ?></span></small>
                    </div>
                    <div class="card-body">
                        <?php if (empty($notificacoes)): ?>
                            <div class="alert alert-info">
                                <h5>📭 Nenhuma notificação</h5>
                                <p>Você não possui notificações no momento.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($notificacoes as $notif): ?>
                                <div class="card notification-card <?= !$notif[\"lida\"] ? \"unread\" : \"\" ?>">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <?= htmlspecialchars($notif[\"titulo\"]) ?>
                                            <?php if (!$notif[\"lida\"]): ?>
                                                <span class="badge bg-primary">Nova</span>
                                            <?php endif; ?>
                                        </h6>
                                        <p class="card-text"><?= htmlspecialchars($notif[\"mensagem\"]) ?></p>
                                        <small class="text-muted">
                                            <?= date(\"d/m/Y H:i\", strtotime($notif[\"data_criacao\"])) ?>
                                            <?php if ($notif[\"link\"]): ?>
                                                | <a href=\"<?= htmlspecialchars($notif[\"link\"]) ?>\">Ver mais</a>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <div class="mt-3">
                            <a href="todas_notificacoes.php" class="btn btn-primary">Página Original</a>
                            <a href="todas_notificacoes_corrigida.php" class="btn btn-success">Versão Corrigida</a>
                            <a href="pagina_usuario.php" class="btn btn-secondary">Meu Perfil</a>
                            <a href="index.php" class="btn btn-outline-primary">Início</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php require_once __DIR__ . '/footer.php'; ?>
</body>
</html>