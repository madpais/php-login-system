<?php
session_start();
require_once 'config.php';
require_once 'verificar_auth.php';

// Verificar se o usuário está logado
verificarLogin();

$usuario_id = $_SESSION['usuario_id'];

try {
    $pdo = conectarBD();
    
    // Buscar dados do usuário
    $stmt = $pdo->prepare("
        SELECT u.*, p.*, n.nivel_atual, n.experiencia_total, n.experiencia_nivel, 
               n.experiencia_necessaria, n.testes_completados, n.melhor_pontuacao, n.media_pontuacao
        FROM usuarios u
        LEFT JOIN perfil_usuario p ON u.id = p.usuario_id
        LEFT JOIN niveis_usuario n ON u.id = n.usuario_id
        WHERE u.id = ?
    ");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        header("Location: login.php");
        exit;
    }
    
    // Buscar configuração do avatar
    $stmt = $pdo->prepare("SELECT * FROM avatar_config WHERE usuario_id = ?");
    $stmt->execute([$usuario_id]);
    $avatar_config = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Configuração padrão do avatar se não existir
    if (!$avatar_config) {
        $avatar_config = [
            'pele_cor' => '#FDBCB4',
            'cabelo_cor' => '#8B4513',
            'cabelo_estilo' => 'curto',
            'olhos_cor' => '#4169E1',
            'roupa_cor' => '#FF6B6B'
        ];
    }
    
} catch (PDOException $e) {
    error_log("Erro na página do usuário: " . $e->getMessage());
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Usuário - Sistema de Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(180deg, #87CEEB 0%, #E0F6FF 100%);
            min-height: 100vh;
        }
        
        /* Header com paisagem */
        .landscape-header {
            height: 280px;
            background: linear-gradient(180deg, #87CEEB 0%, #98FB98 50%, #90EE90 100%);
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 280"><defs><linearGradient id="sky" x1="0%" y1="0%" x2="0%" y2="100%"><stop offset="0%" style="stop-color:%2387CEEB;stop-opacity:1" /><stop offset="50%" style="stop-color:%2398FB98;stop-opacity:1" /><stop offset="100%" style="stop-color:%2390EE90;stop-opacity:1" /></linearGradient></defs><rect width="1200" height="280" fill="url(%23sky)"/><g><circle cx="100" cy="40" r="20" fill="%23FFF" opacity="0.8"/><circle cx="200" cy="30" r="25" fill="%23FFF" opacity="0.6"/><circle cx="300" cy="50" r="15" fill="%23FFF" opacity="0.7"/><circle cx="500" cy="35" r="30" fill="%23FFF" opacity="0.5"/><circle cx="700" cy="45" r="20" fill="%23FFF" opacity="0.8"/><circle cx="900" cy="25" r="22" fill="%23FFF" opacity="0.6"/><circle cx="1100" cy="40" r="18" fill="%23FFF" opacity="0.7"/></g><g><ellipse cx="50" cy="120" rx="25" ry="35" fill="%2332CD32"/><ellipse cx="150" cy="110" rx="20" ry="30" fill="%2328A745"/><ellipse cx="250" cy="130" rx="30" ry="40" fill="%2332CD32"/><ellipse cx="350" cy="115" rx="23" ry="33" fill="%2328A745"/><ellipse cx="450" cy="125" rx="27" ry="37" fill="%2332CD32"/><ellipse cx="550" cy="120" rx="25" ry="35" fill="%2328A745"/><ellipse cx="650" cy="110" rx="20" ry="30" fill="%2332CD32"/><ellipse cx="750" cy="130" rx="30" ry="40" fill="%2328A745"/><ellipse cx="850" cy="115" rx="23" ry="33" fill="%2332CD32"/><ellipse cx="950" cy="125" rx="27" ry="37" fill="%2328A745"/><ellipse cx="1050" cy="120" rx="25" ry="35" fill="%2332CD32"/><ellipse cx="1150" cy="110" rx="20" ry="30" fill="%2328A745"/></g><path d="M0,180 Q200,160 400,170 T800,165 Q1000,160 1200,170 L1200,280 L0,280 Z" fill="%2332CD32" opacity="0.9"/><path d="M0,200 Q300,180 600,190 T1200,185 L1200,280 L0,280 Z" fill="%2328A745" opacity="0.95"/><path d="M100,230 Q200,220 300,230 Q400,240 500,230 Q600,220 700,230 Q800,240 900,230 Q1000,220 1100,230" stroke="%23DAA520" stroke-width="4" fill="none" opacity="0.8"/></svg>');
            background-size: cover;
            background-position: center;
            position: relative;
            overflow: hidden;
        }
        
        /* Navbar no topo */
        .top-navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.1);
            padding: 8px 0;
        }
        
        .nav-tabs {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            padding: 5px;
            margin: 10px 0;
            border: none;
        }
        
        .nav-tabs .nav-link {
            border: none;
            color: #666;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 6px;
            margin: 0 2px;
        }
        
        .nav-tabs .nav-link.active {
            background: #4CAF50;
            color: white;
            border: none;
        }
        
        /* Avatar e nome do usuário */
        .user-profile {
            position: absolute;
            bottom: -50px;
            left: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            z-index: 10;
        }
        
        .avatar-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: white;
            border: 4px solid white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .user-name {
            background: rgba(255, 255, 255, 0.95);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        /* Container principal */
        .main-container {
            margin-top: 80px;
            padding: 20px;
        }
        
        /* Card principal */
        .profile-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        /* Seção de informações pessoais */
        .personal-info {
            padding: 30px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #333;
        }
        
        .info-value {
            color: #666;
        }
        
        /* Seção de dados acadêmicos */
        .academic-data {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        
        .academic-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }
        
        /* Seção de progresso */
        .progress-section {
            padding: 20px;
        }
        
        .progress-item {
            margin-bottom: 15px;
        }
        
        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
        
        .progress-bar-custom {
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4CAF50, #81C784);
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        
        /* Badges */
        .badges-section {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .badge-item {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
        }
        
        .badge-green { background: #4CAF50; }
        .badge-orange { background: #FF9800; }
        .badge-blue { background: #2196F3; }
        .badge-red { background: #F44336; }
        
        /* Metas de intercâmbio */
        .goals-section {
            margin-top: 20px;
        }
        
        .goal-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .goal-item:last-child {
            border-bottom: none;
        }
        
        .goal-flag {
            width: 24px;
            height: 16px;
            margin-right: 8px;
        }
        
        /* Histórico de atividades */
        .activity-history {
            margin-top: 20px;
        }
        
        .activity-item {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.9rem;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Navbar superior -->
    <nav class="top-navbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 40 40'><circle cx='20' cy='20' r='18' fill='%234CAF50'/><path d='M15 20l5 5 10-10' stroke='white' stroke-width='2' fill='none'/></svg>" width="32" height="32" alt="Logo">
                    <span class="fw-bold">Testes de Perfil</span>
                </div>
                <div class="d-flex gap-3">
                    <a href="#" class="text-decoration-none">Guia por Países</a>
                    <a href="#" class="text-decoration-none">Simuladores</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Header com paisagem -->
    <div class="landscape-header">
        <!-- Perfil do usuário -->
        <div class="user-profile">
            <div class="avatar-circle" onclick="openAvatarEditor()">
                <!-- Avatar SVG estilo chibi -->
                <svg width="80" height="80" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <!-- Cabeça -->
                    <ellipse cx="50" cy="35" rx="22" ry="20" fill="<?= $avatar_config['pele_cor'] ?>" stroke="#000" stroke-width="0.8"/>
                    
                    <!-- Cabelo -->
                    <?php if ($avatar_config['cabelo_estilo'] === 'curto'): ?>
                        <path d="M25 20 Q35 12 50 15 Q65 12 75 20 Q75 25 70 28 Q60 25 50 25 Q40 25 30 28 Q25 25 25 20" fill="<?= $avatar_config['cabelo_cor'] ?>" stroke="#000" stroke-width="0.5"/>
                    <?php elseif ($avatar_config['cabelo_estilo'] === 'longo'): ?>
                        <path d="M20 20 Q30 10 50 12 Q70 10 80 20 Q80 35 75 45 Q70 50 65 48 Q60 45 55 48 Q50 50 45 48 Q40 45 35 48 Q30 50 25 45 Q20 35 20 20" fill="<?= $avatar_config['cabelo_cor'] ?>" stroke="#000" stroke-width="0.5"/>
                    <?php else: ?>
                        <path d="M22 20 Q32 12 50 14 Q68 12 78 20 Q78 30 73 35 Q65 32 50 32 Q35 32 27 35 Q22 30 22 20" fill="<?= $avatar_config['cabelo_cor'] ?>" stroke="#000" stroke-width="0.5"/>
                    <?php endif; ?>

                    <!-- Olhos -->
                    <ellipse cx="42" cy="32" rx="4" ry="5" fill="#FFF" stroke="#000" stroke-width="0.5"/>
                    <ellipse cx="58" cy="32" rx="4" ry="5" fill="#FFF" stroke="#000" stroke-width="0.5"/>
                    <ellipse cx="42" cy="33" rx="3" ry="4" fill="<?= $avatar_config['olhos_cor'] ?>"/>
                    <ellipse cx="58" cy="33" rx="3" ry="4" fill="<?= $avatar_config['olhos_cor'] ?>"/>
                    <circle cx="43" cy="31" r="1.5" fill="#FFF" opacity="0.8"/>
                    <circle cx="59" cy="31" r="1.5" fill="#FFF" opacity="0.8"/>
                    
                    <!-- Boca -->
                    <path d="M46 42 Q50 46 54 42" stroke="#000" stroke-width="1" fill="none"/>
                    
                    <!-- Corpo -->
                    <ellipse cx="50" cy="70" rx="18" ry="22" fill="<?= $avatar_config['roupa_cor'] ?>" stroke="#000" stroke-width="0.8"/>
                </svg>
            </div>
            <div class="user-name"><?= htmlspecialchars($usuario['nome']) ?></div>
        </div>
    </div>

    <!-- Container principal -->
    <div class="container main-container">
        <div class="row">
            <div class="col-12">
                <!-- Tabs de navegação -->
                <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">
                            Informações Pessoais
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="academic-tab" data-bs-toggle="tab" data-bs-target="#academic" type="button" role="tab">
                            Dados Acadêmicos
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="goals-tab" data-bs-toggle="tab" data-bs-target="#goals" type="button" role="tab">
                            Metas de Intercâmbio
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="progress-tab" data-bs-toggle="tab" data-bs-target="#progress" type="button" role="tab">
                            Progresso e Gamificação
                        </button>
                    </li>
                </ul>

                <!-- Conteúdo das tabs -->
                <div class="tab-content profile-card" id="profileTabsContent">
                    <!-- Informações Pessoais -->
                    <div class="tab-pane fade show active" id="info" role="tabpanel">
                        <div class="personal-info">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-row">
                                        <span class="info-label">Email:</span>
                                        <span class="info-value"><?= htmlspecialchars($usuario['email']) ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Escola:</span>
                                        <span class="info-value"><?= htmlspecialchars($usuario['escola'] ?? 'Não informado') ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Série / Ano:</span>
                                        <span class="info-value"><?= htmlspecialchars($usuario['serie_ano'] ?? 'Não informado') ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Cidade / Estado:</span>
                                        <span class="info-value"><?= htmlspecialchars($usuario['cidade'] ?? 'Não informado') ?> / <?= htmlspecialchars($usuario['estado'] ?? 'Não informado') ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <!-- Dados Acadêmicos -->
                                    <div class="academic-data">
                                        <div class="academic-title">Dados Acadêmicos</div>
                                        <div class="info-row">
                                            <span class="info-label">GPA:</span>
                                            <span class="info-value"><?= $usuario['gpa'] ?? '3.8' ?></span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Idiomas:</span>
                                            <span class="info-value">Português, Inglês</span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Exames:</span>
                                            <span class="info-value">SAT, TOEFL</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Outras tabs serão adicionadas aqui -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Função removida - Editor de Avatar não está mais disponível
    </script>
<?php require_once __DIR__ . '/footer.php'; ?>
</body>
</html>
