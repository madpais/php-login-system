<?php
require_once '../config.php';

// Iniciar sessão de forma segura
iniciarSessaoSegura();

// Verificar se o usuário está logado - OBRIGATÓRIO para acessar informações dos países
$usuario_logado = isset($_SESSION['usuario_id']);

// Se não estiver logado, redirecionar para a página de login
if (!$usuario_logado) {
    // Salvar a URL atual para redirecionar após o login
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: ../login.php');
    exit();
}

$usuario_nome = $_SESSION['usuario_nome'] ?? '';

// Registrar visita ao país
require_once '../tracking_paises.php';
$resultado_visita = registrarVisitaPais($_SESSION['usuario_id'], 'franca');

// Verificar se é primeira visita para mostrar notificação
$primeira_visita = false;
if ($resultado_visita && $resultado_visita['primeira_visita']) {
    $primeira_visita = true;
    $_SESSION['primeira_visita_pais'] = $resultado_visita['pais_nome'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>França - Guia Completo para Estudantes - DayDreaming</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../imagens/logo_50px_sem_bgd.png">
    <link rel="shortcut icon" type="image/png" href="../imagens/logo_50px_sem_bgd.png">
    <link rel="apple-touch-icon" href="../imagens/logo_50px_sem_bgd.png">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #03254c;
            --secondary-color: #2a9df4;
            --accent-color: #fd79a8;
            --text-dark: #333;
            --text-light: #666;
            --bg-light: #f8f9fa;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
        }

        .header-container {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 25px 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            position: relative;
            overflow: hidden;
        }

        .header-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .header-content {
            position: relative;
            z-index: 2;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .logo-container img {
            max-height: 70px;
            width: auto;
            filter: drop-shadow(0 2px 8px rgba(0,0,0,0.2));
            transition: transform 0.3s ease;
        }

        .logo-container img:hover {
            transform: scale(1.05);
        }

        .country-flag {
            width: 50px;
            height: 35px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            object-fit: cover;
            border: 2px solid rgba(255,255,255,0.3);
        }

        .country-title {
            color: white;
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            letter-spacing: 1px;
        }

        .breadcrumb-nav {
            background: rgba(255,255,255,0.1);
            border-radius: 25px;
            padding: 8px 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .breadcrumb-nav a {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .breadcrumb-nav a:hover {
            color: white;
            text-shadow: 0 0 10px rgba(255,255,255,0.5);
        }

        .breadcrumb-nav .separator {
            color: rgba(255,255,255,0.6);
            margin: 0 10px;
        }

        .breadcrumb-nav .current {
            color: white;
            font-weight: 600;
        }

        .navbutton {
            color: white;
            font-size: clamp(12px, 2vw, 18px);
            text-align: center;
            width: 100%;
            font-weight: 600;
            margin: 0;
            transition: all 0.3s ease;
        }

        .nav-item-container {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #187bcd 100%);
            min-height: 80px;
            border: 3px solid white;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-item-container:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(42, 157, 244, 0.3);
        }

        .nav-item-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .nav-item-container:hover::before {
            left: 100%;
        }

        .hero-image-container {
            display: flex;
            justify-content: center;
            padding: 40px 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .hero-image {
            width: 85%;
            max-width: 1200px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .hero-image:hover {
            transform: scale(1.02);
        }

        .info-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border-top: 4px solid var(--secondary-color);
            height: 100%;
        }

        .info-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(42, 157, 244, 0.15);
        }

        .info-card .icon {
            width: 50px;
            height: 50px;
            margin-bottom: 15px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }

        .info-card h3 {
            font-weight: 700;
            color: var(--secondary-color);
            margin: 10px 0;
            font-size: 2rem;
        }

        .info-card .badge {
            display: inline-block;
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            color: var(--secondary-color);
            border-radius: 20px;
            padding: 8px 16px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .info-card p {
            color: var(--text-light);
            margin-top: 10px;
            font-size: 0.95rem;
        }

        .section-title {
            text-align: center;
            margin: 60px 0 40px 0;
        }

        .section-title h2 {
            color: var(--primary-color);
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .section-title p {
            color: var(--text-light);
            font-size: 1.2rem;
        }

        .nav-tabs {
            border-bottom: 3px solid var(--secondary-color);
            margin-bottom: 30px;
        }

        .nav-tabs .nav-link {
            border: none;
            color: var(--text-light);
            font-weight: 600;
            padding: 15px 25px;
            margin-right: 5px;
            border-radius: 10px 10px 0 0;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link:hover {
            background: rgba(42, 157, 244, 0.1);
            color: var(--secondary-color);
        }

        .nav-tabs .nav-link.active {
            background: var(--secondary-color);
            color: white;
            border-color: var(--secondary-color);
        }

        .tab-content {
            background: white;
            border-radius: 0 15px 15px 15px;
            padding: 40px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }

        .tab-content h4 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .tab-content h5 {
            color: var(--secondary-color);
            font-weight: 600;
            margin: 25px 0 15px 0;
        }

        .tab-content ul {
            padding-left: 0;
        }

        .tab-content li {
            background: var(--bg-light);
            margin: 10px 0;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid var(--secondary-color);
            list-style: none;
        }

        .tab-content li strong {
            color: var(--primary-color);
        }

        .highlight-box {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            border: 2px solid var(--secondary-color);
            border-radius: 15px;
            padding: 25px;
            margin: 25px 0;
        }

        .highlight-box h5 {
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .btn-custom {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #187bcd 100%);
            border: none;
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            margin: 5px;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(42, 157, 244, 0.3);
            color: white;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .header-container {
                padding: 20px 0;
            }

            .logo-container {
                flex-direction: column;
                gap: 10px;
            }

            .logo-container img {
                max-height: 50px;
            }

            .country-flag {
                width: 40px;
                height: 28px;
            }

            .country-title {
                font-size: 1.5rem;
            }

            .breadcrumb-nav {
                padding: 6px 15px;
                font-size: 14px;
            }
            
            .nav-item-container {
                min-height: 60px;
                padding: 10px 5px;
            }
            
            .navbutton {
                font-size: 14px;
            }
            
            .hero-image {
                width: 95%;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
            
            .tab-content {
                padding: 25px 20px;
            }
        }

        @media (max-width: 576px) {
            .info-card {
                margin-bottom: 20px;
            }
            
            .nav-tabs .nav-link {
                padding: 10px 15px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <?php include 'header_status.php'; ?>
    
    <!-- Header -->
    <div class="container-fluid header-container">
        <div class="header-content">
            <div class="container">
                <div class="row align-items-center">
                    <!-- Logo e Título -->
                    <div class="col-lg-6 col-md-6 col-12 text-center text-md-start mb-3 mb-md-0">
                        <div class="logo-container">
                            <img src="../Imagens/Logo_DayDreaming_trasp 1.png" alt="Logo DayDreaming">
                            <img src="https://flagcdn.com/w80/fr.png" alt="Bandeira da França" class="country-flag">
                            <h1 class="country-title">França</h1>
                        </div>
                    </div>
                    
                    <!-- Navegação Breadcrumb -->
                    <div class="col-lg-6 col-md-6 col-12 text-center text-md-end">
                        <nav class="breadcrumb-nav d-inline-block">
                            <a href="../index.php">
                                <i class="fas fa-home me-1"></i>Início
                            </a>
                            <span class="separator">›</span>
                            <a href="../pesquisa_por_pais.php">
                                <i class="fas fa-globe me-1"></i>Países
                            </a>
                            <span class="separator">›</span>
                            <span class="current">
                                <i class="fas fa-map-marker-alt me-1"></i>França
                            </span>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navegação -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-3 col-lg-3 col-sm-6 col-md-6 col-6">
                <div class="nav-item-container" onclick="scrollToSection('quem-somos')">
                    <p class="navbutton">Quem Somos</p>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-sm-6 col-md-6 col-6">
                <div class="nav-item-container" onclick="alert('Funcionalidade em desenvolvimento!')">
                    <p class="navbutton">Teste Vocacional</p>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-sm-6 col-md-6 col-6">
                <div class="nav-item-container" onclick="<?php echo $usuario_logado ? "location.href='../simulador_provas.php'" : "location.href='../login.php'"; ?>">
                    <p class="navbutton">Simulador Prático</p>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-sm-6 col-md-6 col-6">
                <div class="nav-item-container" onclick="scrollToSection('comunidade')">
                    <p class="navbutton">Comunidade</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Imagem Hero -->
    <div class="container-fluid hero-image-container">
        <img src="../imagens/franca_home.png" alt="França - Paisagem" class="hero-image">
    </div>

    <!-- Cards de Informações Básicas -->
    <div class="container my-5">
        <div class="row g-4">
            <!-- População -->
            <div class="col-md-4">
                <div class="info-card">
                    <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="População" class="icon">
                    <h3>68M</h3>
                    <span class="badge">População</span>
                    <p>Habitantes em todo o país</p>
                </div>
            </div>

            <!-- IDH -->
            <div class="col-md-4">
                <div class="info-card">
                    <img src="https://cdn-icons-png.flaticon.com/512/616/616492.png" alt="IDH" class="icon">
                    <h3>28º</h3>
                    <span class="badge">IDH Mundial</span>
                    <p>Índice de Desenvolvimento Humano</p>
                </div>
            </div>

            <!-- Moeda -->
            <div class="col-md-4">
                <div class="info-card">
                    <img src="https://cdn-icons-png.flaticon.com/512/2331/2331970.png" alt="Moeda" class="icon">
                    <h3>EUR</h3>
                    <span class="badge">Moeda Local</span>
                    <p>Euro</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Título da Seção -->
    <div class="section-title">
        <h2>🇫🇷 Guia Completo: Estudar na França</h2>
        <p>Descubra o país da arte, cultura e excelência acadêmica</p>
    </div>

    <!-- Menu de Navegação por Abas -->
    <div class="container">
        <ul class="nav nav-tabs justify-content-center" id="francaTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="idioma-tab" data-bs-toggle="tab" data-bs-target="#idioma" type="button" role="tab">
                    🗣️ Idioma
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="clima-tab" data-bs-toggle="tab" data-bs-target="#clima" type="button" role="tab">
                    🌡️ Clima
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="custos-tab" data-bs-toggle="tab" data-bs-target="#custos" type="button" role="tab">
                    💰 Custos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="bolsas-tab" data-bs-toggle="tab" data-bs-target="#bolsas" type="button" role="tab">
                    🎓 Bolsas
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="universidades-tab" data-bs-toggle="tab" data-bs-target="#universidades" type="button" role="tab">
                    🏛️ Universidades
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="comunidade-tab" data-bs-toggle="tab" data-bs-target="#comunidade-br" type="button" role="tab">
                    🇧🇷 Brasileiros
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="cultura-tab" data-bs-toggle="tab" data-bs-target="#cultura" type="button" role="tab">
                    🎭 Cultura
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="calendario-tab" data-bs-toggle="tab" data-bs-target="#calendario" type="button" role="tab">
                    📅 Calendário
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="guia-tab" data-bs-toggle="tab" data-bs-target="#guia" type="button" role="tab">
                    📋 Guia Bolsas
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="trabalho-tab" data-bs-toggle="tab" data-bs-target="#trabalho" type="button" role="tab">
                    💼 Trabalho
                </button>
            </li>
        </ul>

        <!-- Conteúdo das Abas -->
        <div class="tab-content" id="francaTabContent">
            <!-- Idioma Oficial -->
            <div class="tab-pane fade show active" id="idioma" role="tabpanel">
                <h4><i class="fas fa-language me-2"></i>Idioma Oficial</h4>
                <p><strong>Francês</strong> é o idioma oficial da França, falado por 280 milhões de pessoas no mundo. É uma das línguas mais importantes para diplomacia, cultura e negócios internacionais.</p>

                <div class="row">
                    <div class="col-md-6">
                        <h5>🇫🇷 Francês</h5>
                        <ul>
                            <li><strong>Falantes:</strong> 68 milhões na França</li>
                            <li><strong>Mundial:</strong> 280 milhões de falantes</li>
                            <li><strong>Família:</strong> Língua românica (latim)</li>
                            <li><strong>Status:</strong> Língua oficial em 29 países</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>🌍 Inglês na Educação</h5>
                        <ul>
                            <li><strong>Programas em inglês:</strong> Limitados, principalmente pós-graduação</li>
                            <li><strong>Universidades top:</strong> Alguns programas bilíngues</li>
                            <li><strong>Testes aceitos:</strong> IELTS, TOEFL, Cambridge</li>
                            <li><strong>Nível exigido:</strong> 6.5-7.0 IELTS</li>
                        </ul>
                    </div>
                </div>

                <div class="highlight-box">
                    <h5>🎯 Dicas para Brasileiros:</h5>
                    <ul>
                        <li><strong>Dificuldade:</strong> Moderada para falantes de português</li>
                        <li><strong>Similaridades:</strong> Muitas palavras cognatas com português</li>
                        <li><strong>Pronúncia:</strong> Sons nasais e "r" francês são desafiadores</li>
                        <li><strong>Preparação:</strong> 1-2 anos de estudo antes da viagem</li>
                    </ul>
                </div>

                <h5>📚 Testes de Proficiência:</h5>
                <ul>
                    <li><strong>DELF/DALF:</strong> Diplomas oficiais de francês</li>
                    <li><strong>TCF:</strong> Teste de Conhecimento de Francês</li>
                    <li><strong>TEF:</strong> Teste de Avaliação de Francês</li>
                    <li><strong>Nível exigido:</strong> B2-C1 para graduação, C1-C2 para pós</li>
                </ul>

                <h5>🏫 Aprendendo Francês:</h5>
                <ul>
                    <li><strong>Alliance Française:</strong> Presente em várias cidades brasileiras</li>
                    <li><strong>Duolingo:</strong> Curso gratuito de francês</li>
                    <li><strong>Apps recomendados:</strong> Babbel, Busuu, FluentU</li>
                    <li><strong>Vantagem:</strong> Essencial para vida acadêmica e social</li>
                </ul>
            </div>

            <!-- Clima -->
            <div class="tab-pane fade" id="clima" role="tabpanel">
                <h4><i class="fas fa-sun me-2"></i>Clima Típico</h4>
                <p>A França possui <strong>clima temperado diversificado</strong>, variando do oceânico no oeste ao continental no leste, com regiões mediterrâneas no sul.</p>

                <div class="row">
                    <div class="col-md-6">
                        <h5>🌡️ Regiões Climáticas:</h5>
                        <ul>
                            <li><strong>Norte/Oeste (Paris):</strong> Oceânico - invernos amenos, verões frescos</li>
                            <li><strong>Leste:</strong> Continental - invernos frios, verões quentes</li>
                            <li><strong>Sul (Nice):</strong> Mediterrâneo - invernos suaves, verões secos</li>
                            <li><strong>Montanhas:</strong> Alpino - frio, muita neve</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>📅 Estações do Ano:</h5>
                        <ul>
                            <li><strong>Primavera (Mar-Mai):</strong> 10-20°C, agradável</li>
                            <li><strong>Verão (Jun-Ago):</strong> 20-30°C, quente</li>
                            <li><strong>Outono (Set-Nov):</strong> 10-20°C, chuvoso</li>
                            <li><strong>Inverno (Dez-Fev):</strong> 0-10°C, frio</li>
                        </ul>
                    </div>
                </div>

                <div class="highlight-box">
                    <h5>🧳 Dicas de Vestuário:</h5>
                    <ul>
                        <li><strong>Inverno:</strong> Casaco quente, cachecol, luvas</li>
                        <li><strong>Verão:</strong> Roupas leves, mas leve casaco para a noite</li>
                        <li><strong>Chuva:</strong> Guarda-chuva essencial, especialmente no outono</li>
                        <li><strong>Estilo:</strong> Franceses valorizam elegância no vestir</li>
                    </ul>
                </div>

                <h5>🌍 Comparação com o Brasil:</h5>
                <ul>
                    <li><strong>Estações opostas:</strong> Inverno francês = verão brasileiro</li>
                    <li><strong>Temperatura:</strong> Mais frio que a maioria do Brasil</li>
                    <li><strong>Chuva:</strong> Mais distribuída ao longo do ano</li>
                    <li><strong>Luz solar:</strong> Dias muito curtos no inverno, longos no verão</li>
                </ul>
            </div>

            <!-- Custo de Vida -->
            <div class="tab-pane fade" id="custos" role="tabpanel">
                <h4><i class="fas fa-euro-sign me-2"></i>Custo de Vida</h4>
                <p>A França tem um <strong>custo de vida moderado a alto</strong>, especialmente em Paris, mas oferece excelente qualidade de vida e muitos benefícios estudantis.</p>

                <div class="row">
                    <div class="col-md-6">
                        <h5>🏠 Acomodação (por mês):</h5>
                        <ul>
                            <li><strong>Residência universitária:</strong> €150-400</li>
                            <li><strong>Quarto compartilhado:</strong> €400-700</li>
                            <li><strong>Apartamento próprio:</strong> €600-1500</li>
                            <li><strong>Homestay:</strong> €500-800</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>🍽️ Alimentação (por mês):</h5>
                        <ul>
                            <li><strong>Supermercado:</strong> €200-350</li>
                            <li><strong>Restaurante universitário:</strong> €3.30 por refeição</li>
                            <li><strong>Restaurante médio:</strong> €15-25 por refeição</li>
                            <li><strong>Boulangerie:</strong> €3-8 por refeição</li>
                        </ul>
                    </div>
                </div>

                <h5>🚌 Transporte:</h5>
                <ul>
                    <li><strong>Passe mensal estudantil:</strong> €35-75 (desconto significativo)</li>
                    <li><strong>Vélib' (Paris):</strong> Bicicletas compartilhadas</li>
                    <li><strong>TGV:</strong> Trem de alta velocidade para viagens</li>
                    <li><strong>Transporte urbano:</strong> €1.90-2.50 por viagem</li>
                </ul>

                <h5>📚 Outros Custos:</h5>
                <ul>
                    <li><strong>Livros e materiais:</strong> €200-500 por ano</li>
                    <li><strong>Seguro saúde:</strong> €200-300 por ano (obrigatório)</li>
                    <li><strong>Atividades culturais:</strong> Muitas gratuitas para estudantes</li>
                    <li><strong>Telefone/Internet:</strong> €20-40 por mês</li>
                </ul>

                <div class="highlight-box">
                    <h5>💡 Dicas para Economizar:</h5>
                    <ul>
                        <li>Use cartão estudantil - descontos em transporte, cultura, restaurantes</li>
                        <li>Aproveite restaurantes universitários - refeições subsidiadas</li>
                        <li>Visite museus gratuitos para estudantes</li>
                        <li>Compre em mercados locais - frescos e baratos</li>
                    </ul>
                </div>

                <p><strong>💰 Orçamento mensal total:</strong> €800-1800 (Paris mais caro, cidades menores mais baratas)</p>
            </div>

            <!-- Bolsas de Estudo -->
            <div class="tab-pane fade" id="bolsas" role="tabpanel">
                <h4><i class="fas fa-graduation-cap me-2"></i>Bolsas de Estudo</h4>
                <p>A França oferece <strong>diversas oportunidades de bolsas</strong> para estudantes internacionais, especialmente através do governo francês e universidades prestigiosas.</p>

                <h5>🏛️ Bolsas Governamentais:</h5>
                <ul>
                    <li><strong>Eiffel Excellence Scholarship:</strong> Para mestrado e doutorado</li>
                    <li><strong>Charpak Scholarship:</strong> Específica para estudantes indianos</li>
                    <li><strong>BGF (Bourses du Gouvernement Français):</strong> Bolsas governamentais</li>
                    <li><strong>Erasmus+:</strong> Para estudantes europeus</li>
                </ul>

                <h5>🎓 Bolsas Universitárias Principais:</h5>
                <ul>
                    <li><strong>Sorbonne University:</strong> International Master's Scholarships
                        <br><a href="https://www.sorbonne-universite.fr/en/admissions/scholarships" target="_blank" class="btn-custom">🔗 Sorbonne Scholarships</a>
                    </li>
                    <li><strong>École Normale Supérieure:</strong> International Selection
                        <br><a href="https://www.ens.psl.eu/en/admissions/international-selection" target="_blank" class="btn-custom">🔗 ENS Internacional</a>
                    </li>
                    <li><strong>Sciences Po:</strong> Emile Boutmy Scholarships
                        <br><a href="https://www.sciencespo.fr/students/en/finance/scholarships" target="_blank" class="btn-custom">🔗 Sciences Po Scholarships</a>
                    </li>
                    <li><strong>HEC Paris:</strong> MBA and Master's Scholarships
                        <br><a href="https://www.hec.edu/en/master-programs/specialized-masters/admissions-aid/scholarships-financial-aid" target="_blank" class="btn-custom">🔗 HEC Scholarships</a>
                    </li>
                </ul>

                <h5>🌍 Programas Especiais:</h5>
                <ul>
                    <li><strong>Campus France:</strong> Agência oficial para estudos na França</li>
                    <li><strong>CAPES/CNPq:</strong> Bolsas brasileiras para estudos no exterior</li>
                    <li><strong>Fulbright France:</strong> Para cidadãos americanos</li>
                    <li><strong>Marie Curie Fellowships:</strong> Para pesquisadores</li>
                </ul>

                <div class="highlight-box">
                    <h5>📋 Requisitos Gerais:</h5>
                    <ul>
                        <li>Excelência acadêmica (média 8.5+ ou equivalente)</li>
                        <li>Proficiência em francês (B2-C1) ou inglês</li>
                        <li>Carta de motivação bem estruturada</li>
                        <li>Cartas de recomendação acadêmica</li>
                        <li>Projeto de pesquisa (para pós-graduação)</li>
                        <li>Demonstração de interesse pela França</li>
                    </ul>
                </div>

                <p><strong>💰 Valores típicos:</strong> Bolsas parciais cobrem 25-100% das taxas. Bolsas integrais podem incluir €600-1200/mês para subsistência.</p>
            </div>

            <!-- Universidades -->
            <div class="tab-pane fade" id="universidades" role="tabpanel">
                <h4><i class="fas fa-university me-2"></i>Principais Universidades</h4>
                <p>A França possui algumas das <strong>universidades mais prestigiosas do mundo</strong>, com tradição centenária em excelência acadêmica, pesquisa e formação de líderes globais.</p>

                <h5>🏆 Top Universidades Francesas:</h5>
                <ul>
                    <li><strong>Sorbonne University:</strong> #44 mundial, fusão de Paris-Sorbonne e UPMC
                        <br><a href="https://www.sorbonne-universite.fr/en" target="_blank" class="btn-custom">🔗 Sorbonne Internacional</a>
                    </li>
                    <li><strong>École Normale Supérieure (ENS):</strong> #50 mundial, elite acadêmica
                        <br><a href="https://www.ens.psl.eu/en" target="_blank" class="btn-custom">🔗 ENS Internacional</a>
                    </li>
                    <li><strong>École Polytechnique:</strong> #61 mundial, engenharia de elite
                        <br><a href="https://www.polytechnique.edu/en" target="_blank" class="btn-custom">🔗 Polytechnique Internacional</a>
                    </li>
                    <li><strong>Sciences Po:</strong> #220 mundial, ciências políticas e sociais
                        <br><a href="https://www.sciencespo.fr/en" target="_blank" class="btn-custom">🔗 Sciences Po Internacional</a>
                    </li>
                    <li><strong>University of Paris-Saclay:</strong> #69 mundial, ciências e tecnologia
                        <br><a href="https://www.universite-paris-saclay.fr/en" target="_blank" class="btn-custom">🔗 Paris-Saclay Internacional</a>
                    </li>
                </ul>

                <h5>🌟 Grandes Écoles:</h5>
                <ul>
                    <li><strong>HEC Paris:</strong> Top 2 MBA na Europa</li>
                    <li><strong>INSEAD:</strong> Top 3 MBA mundial</li>
                    <li><strong>CentraleSupélec:</strong> Engenharia de elite</li>
                    <li><strong>ESSEC:</strong> Business school de prestígio</li>
                </ul>

                <h5>🎓 Áreas de Excelência:</h5>
                <ul>
                    <li><strong>Humanidades e Artes:</strong> Sorbonne, ENS</li>
                    <li><strong>Engenharia:</strong> Polytechnique, CentraleSupélec</li>
                    <li><strong>Negócios:</strong> HEC, INSEAD, ESSEC</li>
                    <li><strong>Ciências Políticas:</strong> Sciences Po, ENA</li>
                    <li><strong>Medicina:</strong> Universidades públicas</li>
                </ul>

                <div class="highlight-box">
                    <h5>📊 Dados sobre Intercambistas:</h5>
                    <ul>
                        <li>Mais de <strong>370.000 estudantes internacionais</strong> na França</li>
                        <li><strong>Brasil</strong> está entre os top 10 países de origem</li>
                        <li>Cerca de <strong>8.000 brasileiros</strong> estudam na França anualmente</li>
                        <li>Áreas mais populares: Negócios, Engenharia, Artes, Ciências Sociais</li>
                    </ul>
                </div>
            </div>

            <!-- Comunidade Brasileira -->
            <div class="tab-pane fade" id="comunidade-br" role="tabpanel">
                <h4><i class="fas fa-flag me-2"></i>Comunidade de Brasileiros</h4>
                <p>A França possui uma das <strong>maiores comunidades brasileiras da Europa</strong> com aproximadamente 120.000 pessoas, concentrada principalmente em Paris e região.</p>

                <h5>🏙️ Principais Cidades:</h5>
                <ul>
                    <li><strong>Paris e Île-de-France:</strong> Maior comunidade (~80.000), centro cultural e econômico</li>
                    <li><strong>Lyon:</strong> Segunda maior (~15.000), cidade universitária</li>
                    <li><strong>Toulouse:</strong> Comunidade crescente (~8.000), centro aeroespacial</li>
                    <li><strong>Nice/Côte d'Azur:</strong> Comunidade menor (~5.000), qualidade de vida</li>
                </ul>

                <h5>🤝 Organizações e Grupos:</h5>
                <ul>
                    <li><strong>Casa do Brasil em Paris:</strong> Centro cultural brasileiro</li>
                    <li><strong>Câmara de Comércio Brasil-França:</strong> Networking empresarial</li>
                    <li><strong>Associação dos Brasileiros na França:</strong> Eventos e apoio</li>
                    <li><strong>Grupos no Facebook:</strong> "Brasileiros em Paris", "BR na França"</li>
                </ul>

                <h5>🎉 Eventos e Festivais:</h5>
                <ul>
                    <li><strong>Festival do Brasil:</strong> Evento anual em Paris</li>
                    <li><strong>Carnaval de Paris:</strong> Celebração brasileira tradicional</li>
                    <li><strong>Festa Junina:</strong> Eventos em várias cidades</li>
                    <li><strong>Copa do Mundo:</strong> Grandes encontros para assistir jogos</li>
                </ul>

                <h5>🍽️ Vida Brasileira na França:</h5>
                <ul>
                    <li><strong>Restaurantes:</strong> Mais de 200 restaurantes brasileiros</li>
                    <li><strong>Produtos brasileiros:</strong> Lojas especializadas em Paris</li>
                    <li><strong>Capoeira:</strong> Grupos muito ativos em toda França</li>
                    <li><strong>Música:</strong> Shows regulares de artistas brasileiros</li>
                </ul>

                <div class="highlight-box">
                    <h5>📱 Recursos Úteis:</h5>
                    <ul>
                        <li><strong>Consulados:</strong> Paris, Lyon, Marseille</li>
                        <li><strong>Apps:</strong> Grupos no WhatsApp e Facebook</li>
                        <li><strong>Igreja:</strong> Missas em português em várias cidades</li>
                        <li><strong>Mídia:</strong> Rádio e TV brasileira disponível</li>
                    </ul>
                </div>
            </div>

            <!-- Cultura e Costumes -->
            <div class="tab-pane fade" id="cultura" role="tabpanel">
                <h4><i class="fas fa-wine-glass me-2"></i>Cultura e Costumes Locais</h4>
                <p>A cultura francesa é conhecida pela <strong>arte de viver</strong>, gastronomia refinada, arte, literatura e um forte senso de identidade nacional e elegância.</p>

                <h5>🤝 Características Culturais:</h5>
                <ul>
                    <li><strong>Savoir-vivre:</strong> Arte de viver bem e com elegância</li>
                    <li><strong>Intelectualismo:</strong> Valorização da cultura e debate intelectual</li>
                    <li><strong>Formalidade:</strong> Protocolo social mais formal que no Brasil</li>
                    <li><strong>Laicidade:</strong> Separação rigorosa entre religião e estado</li>
                    <li><strong>Greves:</strong> Manifestações são parte da cultura política</li>
                </ul>

                <h5>🍷 Gastronomia e Etiqueta:</h5>
                <ul>
                    <li><strong>Refeições:</strong> Rituais importantes, nunca apressadas</li>
                    <li><strong>Vinho:</strong> Parte integral da cultura gastronômica</li>
                    <li><strong>Pão:</strong> Baguette fresca diariamente</li>
                    <li><strong>Queijos:</strong> Mais de 400 variedades</li>
                </ul>

                <h5>🎭 Arte e Cultura:</h5>
                <ul>
                    <li><strong>Museus:</strong> Louvre, Orsay, Centre Pompidou</li>
                    <li><strong>Cinema:</strong> Berço da sétima arte</li>
                    <li><strong>Literatura:</strong> Tradição literária riquíssima</li>
                    <li><strong>Moda:</strong> Capital mundial da moda</li>
                </ul>

                <h5>🏛️ Tradições e Festivais:</h5>
                <ul>
                    <li><strong>14 de julho:</strong> Festa Nacional (Bastilha)</li>
                    <li><strong>Nuit Blanche:</strong> Noite dos museus</li>
                    <li><strong>Fête de la Musique:</strong> 21 de junho</li>
                    <li><strong>Beaujolais Nouveau:</strong> Terceira quinta-feira de novembro</li>
                </ul>

                <div class="highlight-box">
                    <h5>⚠️ Dicas Importantes:</h5>
                    <ul>
                        <li>Sempre cumprimente com "Bonjour/Bonsoir" ao entrar em lojas</li>
                        <li>Use "Vous" (formal) até ser convidado a usar "Tu"</li>
                        <li>Evite falar alto em transportes públicos</li>
                        <li>Vista-se bem - aparência é importante</li>
                        <li>Aprenda sobre vinho e queijo - são temas de conversa</li>
                    </ul>
                </div>
            </div>

            <!-- Calendário Acadêmico -->
            <div class="tab-pane fade" id="calendario" role="tabpanel">
                <h4><i class="fas fa-calendar-alt me-2"></i>Calendário Acadêmico</h4>
                <p>O ano acadêmico francês segue o <strong>sistema europeu tradicional</strong>, iniciando em setembro e terminando em junho, dividido em dois semestres com férias bem definidas.</p>

                <div class="row">
                    <div class="col-md-6">
                        <h5>📅 Primeiro Semestre:</h5>
                        <ul>
                            <li><strong>Início:</strong> Início de setembro</li>
                            <li><strong>Término:</strong> Final de janeiro</li>
                            <li><strong>Exames:</strong> Janeiro</li>
                            <li><strong>Férias de inverno:</strong> 2 semanas em fevereiro</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>📅 Segundo Semestre:</h5>
                        <ul>
                            <li><strong>Início:</strong> Fevereiro</li>
                            <li><strong>Término:</strong> Junho</li>
                            <li><strong>Exames:</strong> Maio/junho</li>
                            <li><strong>Férias de verão:</strong> Julho-agosto</li>
                        </ul>
                    </div>
                </div>

                <h5>🎓 Períodos de Aplicação:</h5>
                <ul>
                    <li><strong>Universidades públicas:</strong> Via Parcoursup até março</li>
                    <li><strong>Grandes Écoles:</strong> Concursos específicos</li>
                    <li><strong>Programas internacionais:</strong> Até abril-maio</li>
                    <li><strong>Visto:</strong> Aplicar 2-3 meses antes do início</li>
                </ul>

                <h5>🏖️ Feriados e Pausas:</h5>
                <ul>
                    <li><strong>Toussaint:</strong> 1 semana em novembro</li>
                    <li><strong>Natal:</strong> 2 semanas em dezembro/janeiro</li>
                    <li><strong>Páscoa:</strong> 2 semanas em abril</li>
                    <li><strong>Pentecostes:</strong> Feriado longo em maio</li>
                </ul>

                <div class="highlight-box">
                    <h5>⏰ Cronograma Recomendado:</h5>
                    <ul>
                        <li><strong>18 meses antes:</strong> Começar a aprender francês</li>
                        <li><strong>12 meses antes:</strong> Pesquisar universidades e programas</li>
                        <li><strong>10 meses antes:</strong> Preparar documentos e DELF/DALF</li>
                        <li><strong>8 meses antes:</strong> Aplicar para universidades e bolsas</li>
                        <li><strong>6 meses antes:</strong> Receber ofertas e aplicar para visto</li>
                        <li><strong>3 meses antes:</strong> Organizar acomodação e chegada</li>
                    </ul>
                </div>
            </div>

            <!-- Guia para Bolsas -->
            <div class="tab-pane fade" id="guia" role="tabpanel">
                <h4><i class="fas fa-map me-2"></i>Guia para Conseguir Bolsas</h4>
                <p>Conseguir uma bolsa na França requer <strong>excelência acadêmica, proficiência em francês e demonstração clara de motivação</strong> para estudar no país da arte e cultura.</p>

                <h5>📚 O que Estudar:</h5>
                <ul>
                    <li><strong>Francês:</strong> DELF B2/DALF C1+ para competitividade</li>
                    <li><strong>Área acadêmica:</strong> Mantenha média alta (8.5+ no Brasil)</li>
                    <li><strong>Cultura francesa:</strong> História, arte, literatura, política</li>
                    <li><strong>Inglês:</strong> IELTS 6.5+ como complemento</li>
                </ul>

                <h5>📋 Documentação Necessária:</h5>
                <ul>
                    <li><strong>Histórico acadêmico:</strong> Traduzido e certificado</li>
                    <li><strong>Diploma:</strong> Traduzido e certificado</li>
                    <li><strong>Teste de francês:</strong> DELF/DALF ou TCF</li>
                    <li><strong>CV francês:</strong> Formato específico francês</li>
                    <li><strong>Lettre de motivation:</strong> Carta de motivação em francês</li>
                    <li><strong>Lettres de recommandation:</strong> 2-3 professores</li>
                    <li><strong>Projet d'études:</strong> Projeto de estudos detalhado</li>
                </ul>

                <h5>✍️ Como Fazer CV Francês:</h5>
                <ul>
                    <li><strong>Formato:</strong> Cronológico, máximo 2 páginas</li>
                    <li><strong>Seções:</strong> État civil, Formation, Expérience, Compétences</li>
                    <li><strong>Foto:</strong> Opcional, mas comum</li>
                    <li><strong>Idiomas:</strong> Especificar nível (A1-C2)</li>
                </ul>

                <h5>💌 Lettre de Motivation:</h5>
                <ul>
                    <li><strong>Estrutura:</strong> Vous (empresa/universidade), Moi (candidato), Nous (juntos)</li>
                    <li><strong>Conteúdo:</strong> Por que França, por que este programa</li>
                    <li><strong>Tom:</strong> Formal, mas pessoal</li>
                    <li><strong>Tamanho:</strong> 1 página, máximo 400 palavras</li>
                </ul>

                <div class="highlight-box">
                    <h5>🎯 Preparação para Entrevistas:</h5>
                    <ul>
                        <li><strong>Pesquise:</strong> Universidade, programa, professores, história</li>
                        <li><strong>Pratique:</strong> Entrevista em francês</li>
                        <li><strong>Demonstre:</strong> Conhecimento profundo da cultura francesa</li>
                        <li><strong>Prepare:</strong> Perguntas sobre arte, política, sociedade francesa</li>
                    </ul>
                </div>

                <p><strong>🔗 Links Úteis:</strong></p>
                <a href="https://www.campusfrance.org/" target="_blank" class="btn-custom">🎓 Campus France</a>
                <a href="https://www.diplomatie.gouv.fr/fr/services-aux-francais/bourses/" target="_blank" class="btn-custom">🏆 Bolsas Governamentais</a>
            </div>

            <!-- Trabalho com Visto de Estudante -->
            <div class="tab-pane fade" id="trabalho" role="tabpanel">
                <h4><i class="fas fa-briefcase me-2"></i>Trabalho com Visto de Estudante</h4>
                <p><strong>Sim!</strong> Estudantes internacionais na França podem trabalhar com visto de estudante, e o país oferece <strong>boas oportunidades</strong> de trabalho e experiência profissional.</p>

                <h5>⏰ Permissões de Trabalho:</h5>
                <ul>
                    <li><strong>Durante estudos:</strong> Máximo 20 horas por semana (964h/ano)</li>
                    <li><strong>Férias de verão:</strong> Tempo integral durante férias</li>
                    <li><strong>Estágios:</strong> Tempo integral se parte do currículo</li>
                    <li><strong>Autorização:</strong> Automática com visto de estudante</li>
                </ul>

                <h5>📄 Documentos Necessários:</h5>
                <ul>
                    <li><strong>Titre de séjour:</strong> Cartão de residência estudantil</li>
                    <li><strong>Numéro de sécurité sociale:</strong> Número da previdência social</li>
                    <li><strong>Compte bancaire:</strong> Conta bancária francesa</li>
                    <li><strong>Contrat de travail:</strong> Contrato de trabalho</li>
                </ul>

                <h5>💼 Tipos de Trabalho Disponíveis:</h5>
                <ul>
                    <li><strong>Hospitality:</strong> Restaurantes, cafés, hotéis</li>
                    <li><strong>Retail:</strong> Lojas, grandes magazines</li>
                    <li><strong>Tutoring:</strong> Aulas particulares de português/inglês</li>
                    <li><strong>Assistente de pesquisa:</strong> Projetos universitários</li>
                    <li><strong>Au pair:</strong> Cuidado de crianças</li>
                </ul>

                <h5>🏢 Oportunidades por Setor:</h5>
                <ul>
                    <li><strong>Hospitality:</strong> €10-12/hora</li>
                    <li><strong>Retail:</strong> €10.25/hora (salário mínimo)</li>
                    <li><strong>Tutoring:</strong> €15-25/hora</li>
                    <li><strong>Assistente de pesquisa:</strong> €12-18/hora</li>
                    <li><strong>Au pair:</strong> €280-320/mês + acomodação</li>
                </ul>

                <h5>🎓 Pós-Graduação - Oportunidades:</h5>
                <ul>
                    <li><strong>APS (Autorisation Provisoire de Séjour):</strong> 2 anos para procurar trabalho</li>
                    <li><strong>Talent Passport:</strong> Para profissionais qualificados</li>
                    <li><strong>Startup Scene:</strong> Ecossistema empreendedor ativo</li>
                    <li><strong>Multinacionais:</strong> Muitas empresas globais em Paris</li>
                </ul>

                <div class="highlight-box">
                    <h5>💰 Benefícios Financeiros:</h5>
                    <ul>
                        <li><strong>Salário mínimo:</strong> €10.25/hora (SMIC)</li>
                        <li><strong>Renda mensal:</strong> €800-1200 (20h/semana)</li>
                        <li><strong>Cobertura de custos:</strong> 50-70% das despesas de vida</li>
                        <li><strong>Benefícios:</strong> Férias pagas, seguro saúde</li>
                    </ul>
                </div>

                <p><strong>🎯 Dica:</strong> A França valoriza muito a experiência profissional durante os estudos e oferece excelentes oportunidades de carreira pós-graduação.</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="container-fluid text-center" style="background-color: #03254c; color: white; padding: 40px 0; margin-top: 60px;">
        <img src="../Imagens/Logo_DayDreaming_trasp 1.png" alt="Logo DayDreaming" class="img-fluid" style="max-width: 200px;">
        <p class="mt-3">© 2024 DayDreaming - Sua jornada para educação internacional começa aqui!</p>
        <p>Todos os direitos reservados</p>

        <?php if ($usuario_logado): ?>
            <div class="mt-3">
                <small>Logado como: <?php echo htmlspecialchars($usuario_nome); ?> | <a href="../logout.php" style="color: #2a9df4;">Sair</a></small>
            </div>
        <?php endif; ?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Função para scroll suave para seções
        function scrollToSection(sectionId) {
            // Para futuras implementações de seções específicas
            alert('Funcionalidade em desenvolvimento!');
        }
    </script>
<?php require_once __DIR__ . '/../footer.php'; ?>
</body>
</html>
