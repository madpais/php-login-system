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
        header("Location: todas_notificacoes.php");
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
        
        .navbar-custom {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        
        .notifications-container {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            margin: 2rem 0;
        }
        
        .notification-item {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .notification-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .notification-item.unread {
            background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%);
            border-color: #2196F3;
        }
        
        .notification-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 1rem;
        }
        
        .notification-icon.forum {
            background: linear-gradient(135deg, #4CAF50, #81C784);
            color: white;
        }
        
        .notification-icon.badge {
            background: linear-gradient(135deg, #FFD700, #FFA500);
            color: white;
        }
        
        .notification-icon.level {
            background: linear-gradient(135deg, #9C27B0, #E1BEE7);
            color: white;
        }
        
        .notification-icon.system {
            background: linear-gradient(135deg, #607D8B, #90A4AE);
            color: white;
        }
        
        .notification-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .notification-message {
            color: #666;
            line-height: 1.5;
            margin-bottom: 0.5rem;
        }
        
        .notification-time {
            color: #999;
            font-size: 0.875rem;
        }
        
        .unread-badge {
            background: #2196F3;
            color: white;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 10px;
            font-weight: 500;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4CAF50, #81C784);
            border: none;
            border-radius: 25px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <?php include 'header_status.php'; ?>
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="imagens/Logo_DayDreaming_trasp 1.png" alt="DayDreaming" height="40" class="me-2">
                <span class="fw-bold text-primary">DayDreaming</span>
            </a>
            
            <div class="navbar-nav ms-auto d-flex flex-row align-items-center">
                <a class="nav-link me-3" href="pesquisa_por_pais.php">
                    <i class="fas fa-globe me-1"></i>Países
                </a>
                <a class="nav-link me-3" href="testes_internacionais.php">
                    <i class="fas fa-graduation-cap me-1"></i>Testes
                </a>
                <a class="nav-link me-3" href="simulador_provas.php">
                    <i class="fas fa-laptop me-1"></i>Simuladores
                </a>
                <a class="nav-link me-3" href="forum.php">
                    <i class="fas fa-comments me-1"></i>Fórum
                </a>
                <a class="nav-link me-3" href="pagina_usuario.php">
                    <i class="fas fa-user me-1"></i>Perfil
                </a>
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i>Sair
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="notifications-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1 class="h2 text-primary mb-2">
                                <i class="fas fa-bell me-2"></i>
                                Notificações
                            </h1>
                            <p class="text-muted mb-0">
                                <?php if ($total_nao_lidas > 0): ?>
                                    Você tem <?= $total_nao_lidas ?> notificação<?= $total_nao_lidas > 1 ? 'ões' : '' ?> não lida<?= $total_nao_lidas > 1 ? 's' : '' ?>
                                <?php else: ?>
                                    Todas as notificações foram lidas
                                <?php endif; ?>
                            </p>
                        </div>
                        
                        <?php if ($total_nao_lidas > 0): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="acao" value="marcar_todas_lidas">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check-double me-2"></i>
                                    Marcar todas como lidas
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <?php if (empty($notificacoes)): ?>
                        <div class="empty-state">
                            <i class="fas fa-bell-slash"></i>
                            <h3>Nenhuma notificação</h3>
                            <p>Você não possui notificações ainda. Quando houver atividades relevantes, elas aparecerão aqui.</p>
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
                                        
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="notification-title">
                                                    <?= htmlspecialchars($notificacao['titulo']) ?>
                                                </div>
                                                <?php if (!$notificacao['lida']): ?>
                                                    <span class="unread-badge">Nova</span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="notification-message">
                                                <?= htmlspecialchars($notificacao['mensagem']) ?>
                                            </div>
                                            
                                            <div class="notification-time">
                                                <i class="fas fa-clock me-1"></i>
                                                <?= date('d/m/Y H:i', strtotime($notificacao['data_criacao'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
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
            });
        }
    </script>
</body>
</html>
