<?php
require_once 'config.php';
require_once 'verificar_auth.php';

// Iniciar sessão de forma segura
iniciarSessaoSegura();

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
    
    // Buscar badges conquistadas
    $stmt = $pdo->prepare("
        SELECT b.nome, b.descricao, b.icone, ub.data_conquista
        FROM usuario_badges ub
        JOIN badges b ON ub.badge_id = b.id
        WHERE ub.usuario_id = ?
        ORDER BY ub.data_conquista DESC
    ");
    $stmt->execute([$usuario_id]);
    $badges = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Buscar histórico de atividades recentes
    $stmt = $pdo->prepare("
        SELECT tipo_atividade, descricao, pontos_ganhos, data_atividade
        FROM historico_atividades
        WHERE usuario_id = ?
        ORDER BY data_atividade DESC
        LIMIT 10
    ");
    $stmt->execute([$usuario_id]);
    $atividades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Buscar país de maior interesse baseado nos testes
    $stmt = $pdo->prepare("
        SELECT 
            CASE 
                WHEN tipo_prova = 'toefl' OR tipo_prova = 'sat' THEN 'Estados Unidos'
                WHEN tipo_prova = 'ielts' THEN 'Reino Unido'
                WHEN tipo_prova = 'dele' THEN 'Espanha'
                WHEN tipo_prova = 'delf' THEN 'França'
                WHEN tipo_prova = 'testdaf' THEN 'Alemanha'
                WHEN tipo_prova = 'jlpt' THEN 'Japão'
                WHEN tipo_prova = 'hsk' THEN 'China'
                ELSE 'Não definido'
            END as pais,
            COUNT(*) as total_testes
        FROM sessoes_teste
        WHERE usuario_id = ? AND status = 'finalizada'
        GROUP BY tipo_prova
        ORDER BY total_testes DESC
        LIMIT 1
    ");
    $stmt->execute([$usuario_id]);
    $pais_interesse = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Erro na página de usuário: " . $e->getMessage());
    $erro = "Erro ao carregar dados do usuário.";
}

// Configurações padrão do avatar se não existir
$avatar_config = $usuario['avatar_personagem'] ? json_decode($usuario['avatar_personagem'], true) : [
    'cabelo_cor' => '#8B4513',
    'cabelo_estilo' => 'curto',
    'pele_cor' => '#FDBCB4',
    'olhos_cor' => '#654321',
    'roupa_cor' => '#4CAF50',
    'roupa_estilo' => 'casual'
];

$background_cor = $usuario['background_cor'] ?? '#4CAF50';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($usuario['nome']) ?> - DayDreaming</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #87CEEB 0%, #98FB98 50%, #90EE90 100%);
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 400"><defs><linearGradient id="sky" x1="0%" y1="0%" x2="0%" y2="100%"><stop offset="0%" style="stop-color:%2387CEEB;stop-opacity:1" /><stop offset="100%" style="stop-color:%23E0F6FF;stop-opacity:1" /></linearGradient></defs><rect width="1200" height="400" fill="url(%23sky)"/><g><circle cx="100" cy="80" r="25" fill="%23FFF" opacity="0.8"/><circle cx="200" cy="60" r="30" fill="%23FFF" opacity="0.6"/><circle cx="300" cy="90" r="20" fill="%23FFF" opacity="0.7"/><circle cx="500" cy="70" r="35" fill="%23FFF" opacity="0.5"/><circle cx="700" cy="85" r="25" fill="%23FFF" opacity="0.8"/><circle cx="900" cy="65" r="28" fill="%23FFF" opacity="0.6"/><circle cx="1100" cy="75" r="22" fill="%23FFF" opacity="0.7"/></g></svg>');
            background-size: cover;
            background-position: center;
            min-height: 350px;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 400"><path d="M0,250 Q200,200 400,240 T800,230 Q1000,220 1200,240 L1200,400 L0,400 Z" fill="%2332CD32" opacity="0.8"/><path d="M0,280 Q300,250 600,270 T1200,260 L1200,400 L0,400 Z" fill="%2328A745" opacity="0.9"/><g><ellipse cx="150" cy="320" rx="40" ry="15" fill="%23228B22" opacity="0.6"/><ellipse cx="350" cy="310" rx="35" ry="12" fill="%23228B22" opacity="0.5"/><ellipse cx="550" cy="325" rx="45" ry="18" fill="%23228B22" opacity="0.7"/><ellipse cx="750" cy="315" rx="38" ry="14" fill="%23228B22" opacity="0.6"/><ellipse cx="950" cy="320" rx="42" ry="16" fill="%23228B22" opacity="0.5"/></g><path d="M100,350 Q150,340 200,350 Q250,360 300,350 Q350,340 400,350" stroke="%23DAA520" stroke-width="3" fill="none" opacity="0.8"/></svg>');
            background-size: cover;
            z-index: 1;
        }
        
        .profile-content {
            position: relative;
            z-index: 2;
            padding: 2rem 0;
        }
        
        .avatar-container {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: linear-gradient(135deg, #FFF 0%, #F8F9FA 100%);
            border: 5px solid rgba(255,255,255,0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 15px 35px rgba(0,0,0,0.15), 0 5px 15px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .avatar-container:hover {
            transform: scale(1.05) translateY(-5px);
            box-shadow: 0 20px 45px rgba(0,0,0,0.2), 0 10px 25px rgba(0,0,0,0.15);
        }

        .avatar-container::before {
            content: '';
            position: absolute;
            top: -3px;
            left: -3px;
            right: -3px;
            bottom: -3px;
            background: linear-gradient(45deg, #4CAF50, #81C784, #A5D6A7, #4CAF50);
            border-radius: 50%;
            z-index: -1;
            animation: rotate 3s linear infinite;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .user-name {
            color: white;
            font-size: 2.2rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .user-level {
            background: linear-gradient(135deg, #FFD700, #FFA500);
            color: #333;
            padding: 0.6rem 1.5rem;
            border-radius: 25px;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
            border: 2px solid rgba(255,255,255,0.8);
        }
        
        .progress-section {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            margin: 1rem 0;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        
        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .progress-bar-custom {
            height: 12px;
            border-radius: 10px;
            background: #e9ecef;
            overflow: hidden;
            margin: 0.5rem 0;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4CAF50, #81C784);
            border-radius: 10px;
            transition: width 0.8s ease;
        }
        
        .badge-item {
            background: linear-gradient(135deg, #FFD700, #FFA500);
            border-radius: 15px;
            padding: 1rem;
            text-align: center;
            color: white;
            margin: 0.5rem;
            box-shadow: 0 4px 15px rgba(255,215,0,0.3);
            transition: transform 0.3s ease;
        }
        
        .badge-item:hover {
            transform: translateY(-5px);
        }
        
        .badge-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 1.5rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .activity-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin: 0.5rem 0;
            border-left: 4px solid #4CAF50;
        }
        
        .navbar-custom {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        
        .btn-edit-profile {
            background: linear-gradient(135deg, #FF6B6B, #FF8E8E);
            border: 2px solid rgba(255,255,255,0.8);
            color: white;
            padding: 0.8rem 2.5rem;
            border-radius: 30px;
            font-weight: 700;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.3);
            font-size: 1rem;
        }

        .btn-edit-profile:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 107, 107, 0.4);
            color: white;
            background: linear-gradient(135deg, #FF5252, #FF7979);
        }
    </style>
</head>
<body>
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
                <a class="nav-link me-3 active" href="pagina_usuario.php">
                    <i class="fas fa-user me-1"></i>Perfil
                </a>
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i>Sair
                </a>
            </div>
        </div>
    </nav>

    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-content">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 text-center">
                        <div class="avatar-container">
                            <div id="avatar-display">
                                <!-- Avatar SVG estilo chibi -->
                                <svg width="120" height="120" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                    <!-- Cabeça (estilo chibi - maior e mais redonda) -->
                                    <ellipse cx="50" cy="35" rx="22" ry="20" fill="<?= $avatar_config['pele_cor'] ?>" stroke="#000" stroke-width="0.8"/>

                                    <!-- Cabelo -->
                                    <?php if ($avatar_config['cabelo_estilo'] === 'curto'): ?>
                                        <path d="M25 20 Q35 12 50 15 Q65 12 75 20 Q75 25 70 28 Q60 25 50 25 Q40 25 30 28 Q25 25 25 20" fill="<?= $avatar_config['cabelo_cor'] ?>" stroke="#000" stroke-width="0.5"/>
                                        <path d="M30 18 Q40 14 50 16 Q60 14 70 18" fill="<?= $avatar_config['cabelo_cor'] ?>" opacity="0.8"/>
                                    <?php elseif ($avatar_config['cabelo_estilo'] === 'longo'): ?>
                                        <path d="M20 20 Q30 10 50 12 Q70 10 80 20 Q80 35 75 45 Q70 50 65 48 Q60 45 55 48 Q50 50 45 48 Q40 45 35 48 Q30 50 25 45 Q20 35 20 20" fill="<?= $avatar_config['cabelo_cor'] ?>" stroke="#000" stroke-width="0.5"/>
                                        <path d="M25 18 Q35 12 50 14 Q65 12 75 18" fill="<?= $avatar_config['cabelo_cor'] ?>" opacity="0.8"/>
                                        <path d="M22 35 Q27 40 32 38 Q37 35 42 38" fill="<?= $avatar_config['cabelo_cor'] ?>" opacity="0.7"/>
                                        <path d="M58 38 Q63 35 68 38 Q73 40 78 35" fill="<?= $avatar_config['cabelo_cor'] ?>" opacity="0.7"/>
                                    <?php else: ?>
                                        <path d="M22 20 Q32 12 50 14 Q68 12 78 20 Q78 30 73 35 Q65 32 50 32 Q35 32 27 35 Q22 30 22 20" fill="<?= $avatar_config['cabelo_cor'] ?>" stroke="#000" stroke-width="0.5"/>
                                        <path d="M27 18 Q37 14 50 16 Q63 14 73 18" fill="<?= $avatar_config['cabelo_cor'] ?>" opacity="0.8"/>
                                    <?php endif; ?>

                                    <!-- Olhos grandes estilo chibi -->
                                    <ellipse cx="42" cy="32" rx="4" ry="5" fill="#FFF" stroke="#000" stroke-width="0.5"/>
                                    <ellipse cx="58" cy="32" rx="4" ry="5" fill="#FFF" stroke="#000" stroke-width="0.5"/>
                                    <ellipse cx="42" cy="33" rx="3" ry="4" fill="<?= $avatar_config['olhos_cor'] ?>"/>
                                    <ellipse cx="58" cy="33" rx="3" ry="4" fill="<?= $avatar_config['olhos_cor'] ?>"/>
                                    <circle cx="43" cy="31" r="1.5" fill="#FFF" opacity="0.8"/>
                                    <circle cx="59" cy="31" r="1.5" fill="#FFF" opacity="0.8"/>

                                    <!-- Sobrancelhas -->
                                    <path d="M38 28 Q42 26 46 28" stroke="#000" stroke-width="0.8" fill="none"/>
                                    <path d="M54 28 Q58 26 62 28" stroke="#000" stroke-width="0.8" fill="none"/>

                                    <!-- Nariz pequeno -->
                                    <circle cx="50" cy="38" r="0.8" fill="#000" opacity="0.4"/>

                                    <!-- Boca sorridente estilo chibi -->
                                    <path d="M46 42 Q50 46 54 42" stroke="#000" stroke-width="1" fill="none"/>
                                    <path d="M47 43 Q50 45 53 43" stroke="#FF69B4" stroke-width="0.5" fill="none" opacity="0.6"/>

                                    <!-- Bochechas rosadas -->
                                    <circle cx="35" cy="40" r="3" fill="#FFB6C1" opacity="0.6"/>
                                    <circle cx="65" cy="40" r="3" fill="#FFB6C1" opacity="0.6"/>

                                    <!-- Corpo chibi (menor proporcionalmente) -->
                                    <ellipse cx="50" cy="70" rx="18" ry="22" fill="<?= $avatar_config['roupa_cor'] ?>" stroke="#000" stroke-width="0.8"/>

                                    <!-- Detalhes da roupa -->
                                    <rect x="44" y="58" width="12" height="8" rx="2" fill="#FFF" opacity="0.8" stroke="#000" stroke-width="0.5"/>
                                    <circle cx="47" cy="62" r="1" fill="#000" opacity="0.6"/>
                                    <circle cx="53" cy="62" r="1" fill="#000" opacity="0.6"/>

                                    <!-- Braços -->
                                    <ellipse cx="30" cy="65" rx="6" ry="12" fill="<?= $avatar_config['pele_cor'] ?>" stroke="#000" stroke-width="0.8"/>
                                    <ellipse cx="70" cy="65" rx="6" ry="12" fill="<?= $avatar_config['pele_cor'] ?>" stroke="#000" stroke-width="0.8"/>

                                    <!-- Mãos -->
                                    <circle cx="30" cy="75" r="4" fill="<?= $avatar_config['pele_cor'] ?>" stroke="#000" stroke-width="0.5"/>
                                    <circle cx="70" cy="75" r="4" fill="<?= $avatar_config['pele_cor'] ?>" stroke="#000" stroke-width="0.5"/>

                                    <!-- Pernas -->
                                    <ellipse cx="42" cy="88" rx="5" ry="8" fill="<?= $avatar_config['pele_cor'] ?>" stroke="#000" stroke-width="0.8"/>
                                    <ellipse cx="58" cy="88" rx="5" ry="8" fill="<?= $avatar_config['pele_cor'] ?>" stroke="#000" stroke-width="0.8"/>

                                    <!-- Sapatos -->
                                    <ellipse cx="42" cy="95" rx="6" ry="3" fill="#8B4513" stroke="#000" stroke-width="0.5"/>
                                    <ellipse cx="58" cy="95" rx="6" ry="3" fill="#8B4513" stroke="#000" stroke-width="0.5"/>
                                </svg>
                            </div>
                        </div>
                        <h1 class="user-name"><?= htmlspecialchars($usuario['nome']) ?></h1>
                        <div class="user-level">
                            <i class="fas fa-star me-2"></i>Nível <?= $usuario['nivel_atual'] ?? 1 ?>
                        </div>
                        <button class="btn btn-edit-profile" onclick="openProfileEditor()">
                            <i class="fas fa-edit me-2"></i>Editar Perfil
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container my-5">
        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-4">
                <!-- Informações Pessoais -->
                <div class="progress-section">
                    <h3 class="section-title">
                        <i class="fas fa-user-circle text-primary"></i>
                        Informações Pessoais
                    </h3>
                    <div class="mb-3">
                        <strong>Email:</strong><br>
                        <span class="text-muted"><?= htmlspecialchars($usuario['email']) ?></span>
                    </div>
                    <div class="mb-3">
                        <strong>Escola:</strong><br>
                        <span class="text-muted"><?= htmlspecialchars($usuario['escola'] ?? 'Não informado') ?></span>
                    </div>
                    <div class="mb-3">
                        <strong>Série/Ano:</strong><br>
                        <span class="text-muted"><?= htmlspecialchars($usuario['serie_ano'] ?? 'Não informado') ?></span>
                    </div>
                    <div class="mb-3">
                        <strong>Cidade/Estado:</strong><br>
                        <span class="text-muted"><?= htmlspecialchars($usuario['cidade_estado'] ?? 'Não informado') ?></span>
                    </div>
                </div>

                <!-- Dados Acadêmicos -->
                <div class="progress-section">
                    <h3 class="section-title">
                        <i class="fas fa-graduation-cap text-success"></i>
                        Dados Acadêmicos
                    </h3>
                    <div class="mb-3">
                        <strong>GPA:</strong><br>
                        <span class="text-muted"><?= $usuario['gpa'] ? number_format($usuario['gpa'], 2) : 'Não informado' ?></span>
                    </div>
                    <div class="mb-3">
                        <strong>Idiomas:</strong><br>
                        <span class="text-muted"><?= $usuario['idiomas'] ? implode(', ', json_decode($usuario['idiomas'], true)) : 'Não informado' ?></span>
                    </div>
                    <div class="mb-3">
                        <strong>Exames:</strong><br>
                        <span class="text-muted"><?= $usuario['exames_realizados'] ? implode(', ', json_decode($usuario['exames_realizados'], true)) : 'Nenhum exame realizado' ?></span>
                    </div>
                </div>
            </div>

            <!-- Middle Column -->
            <div class="col-lg-4">
                <!-- Progresso -->
                <div class="progress-section">
                    <h3 class="section-title">
                        <i class="fas fa-chart-line text-info"></i>
                        Progresso
                    </h3>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Nível <?= $usuario['nivel_atual'] ?? 1 ?></span>
                            <span><?= $usuario['experiencia_nivel'] ?? 0 ?>/<?= $usuario['experiencia_necessaria'] ?? 100 ?> XP</span>
                        </div>
                        <div class="progress-bar-custom">
                            <div class="progress-fill" style="width: <?= (($usuario['experiencia_nivel'] ?? 0) / ($usuario['experiencia_necessaria'] ?? 100)) * 100 ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-number"><?= $usuario['testes_completados'] ?? 0 ?></div>
                            <div>Testes Realizados</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?= $usuario['melhor_pontuacao'] ? number_format($usuario['melhor_pontuacao'], 1) . '%' : '0%' ?></div>
                            <div>Melhor Pontuação</div>
                        </div>
                    </div>
                </div>

                <!-- Metas de Intercâmbio -->
                <div class="progress-section">
                    <h3 class="section-title">
                        <i class="fas fa-target text-warning"></i>
                        Metas de Intercâmbio
                    </h3>
                    <div class="mb-3">
                        <strong>País de Interesse:</strong><br>
                        <span class="text-muted">
                            <?= $usuario['pais_interesse'] ?? ($pais_interesse['pais'] ?? 'Não definido') ?>
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong>Tipo de Intercâmbio:</strong><br>
                        <span class="text-muted"><?= ucfirst(str_replace('_', ' ', $usuario['meta_intercambio'] ?? 'Não definido')) ?></span>
                    </div>
                    <div class="mb-3">
                        <strong>Prazo:</strong><br>
                        <span class="text-muted"><?= ucfirst(str_replace('_', ' ', $usuario['meta_prazo'] ?? 'Não definido')) ?></span>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">
                <!-- Badges Conquistadas -->
                <div class="progress-section">
                    <h3 class="section-title">
                        <i class="fas fa-trophy text-warning"></i>
                        Badges Conquistadas
                    </h3>
                    <div class="row">
                        <?php if (empty($badges)): ?>
                            <div class="col-12 text-center text-muted">
                                <i class="fas fa-medal fa-3x mb-3 opacity-50"></i>
                                <p>Nenhuma badge conquistada ainda.<br>Complete testes para ganhar suas primeiras conquistas!</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($badges as $badge): ?>
                                <div class="col-6">
                                    <div class="badge-item">
                                        <div class="badge-icon"><?= $badge['icone'] ?></div>
                                        <div class="fw-bold"><?= htmlspecialchars($badge['nome']) ?></div>
                                        <small><?= date('d/m/Y', strtotime($badge['data_conquista'])) ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Histórico de Atividades -->
                <div class="progress-section">
                    <h3 class="section-title">
                        <i class="fas fa-history text-secondary"></i>
                        Histórico de Atividades
                    </h3>
                    <?php if (empty($atividades)): ?>
                        <div class="text-center text-muted">
                            <i class="fas fa-clock fa-2x mb-3 opacity-50"></i>
                            <p>Nenhuma atividade registrada ainda.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($atividades as $atividade): ?>
                            <div class="activity-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($atividade['descricao']) ?></div>
                                        <small class="text-muted"><?= date('d/m/Y H:i', strtotime($atividade['data_atividade'])) ?></small>
                                    </div>
                                    <?php if ($atividade['pontos_ganhos'] > 0): ?>
                                        <span class="badge bg-success">+<?= $atividade['pontos_ganhos'] ?> XP</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>


        function openProfileEditor() {
            window.location.href = 'editar_perfil.php';
        }

        // Animação da barra de progresso
        document.addEventListener('DOMContentLoaded', function() {
            const progressBars = document.querySelectorAll('.progress-fill');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 500);
            });
        });
    </script>
</body>
</html>
