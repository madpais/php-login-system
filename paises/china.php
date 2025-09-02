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
$resultado_visita = registrarVisitaPais($_SESSION['usuario_id'], 'china');

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
    <title>China - Guia Completo para Estudantes - DayDreaming</title>
    
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
                            <img src="https://flagcdn.com/w80/cn.png" alt="Bandeira da China" class="country-flag">
                            <h1 class="country-title">China</h1>
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
                                <i class="fas fa-map-marker-alt me-1"></i>China
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
        <img src="../imagens/china_home.png" alt="China - Paisagem" class="hero-image">
    </div>

    <!-- Cards de Informações Básicas -->
    <div class="container my-5">
        <div class="row g-4">
            <!-- População -->
            <div class="col-md-4">
                <div class="info-card">
                    <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="População" class="icon">
                    <h3>1,4B</h3>
                    <span class="badge">População</span>
                    <p>Habitantes em todo o país</p>
                </div>
            </div>

            <!-- IDH -->
            <div class="col-md-4">
                <div class="info-card">
                    <img src="https://cdn-icons-png.flaticon.com/512/616/616492.png" alt="IDH" class="icon">
                    <h3>79º</h3>
                    <span class="badge">IDH Mundial</span>
                    <p>Índice de Desenvolvimento Humano</p>
                </div>
            </div>

            <!-- Moeda -->
            <div class="col-md-4">
                <div class="info-card">
                    <img src="https://cdn-icons-png.flaticon.com/512/2331/2331970.png" alt="Moeda" class="icon">
                    <h3>CNY</h3>
                    <span class="badge">Moeda Local</span>
                    <p>Yuan Chinês</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Título da Seção -->
    <div class="section-title">
        <h2>🇨🇳 Guia Completo: Estudar na China</h2>
        <p>Descubra o império do meio e suas oportunidades educacionais em ascensão</p>
    </div>

    <!-- Menu de Navegação por Abas -->
    <div class="container">
        <ul class="nav nav-tabs justify-content-center" id="chinaTab" role="tablist">
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
        <div class="tab-content" id="chinaTabContent">
            <!-- Idioma Oficial -->
            <div class="tab-pane fade show active" id="idioma" role="tabpanel">
                <h4><i class="fas fa-language me-2"></i>Idioma Oficial</h4>
                <p><strong>Mandarim (Chinês Padrão)</strong> é o idioma oficial da China, falado por mais de 900 milhões de pessoas. É baseado no dialeto de Pequim e usa caracteres chineses simplificados.</p>

                <div class="row">
                    <div class="col-md-6">
                        <h5>🇨🇳 Mandarim (普通话)</h5>
                        <ul>
                            <li><strong>Nome oficial:</strong> Putonghua (língua comum)</li>
                            <li><strong>Falantes:</strong> 900+ milhões nativos</li>
                            <li><strong>Escrita:</strong> Caracteres simplificados</li>
                            <li><strong>Tons:</strong> 4 tons principais + neutro</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>🌍 Inglês na Educação</h5>
                        <ul>
                            <li><strong>Programas internacionais:</strong> Muitos cursos em inglês</li>
                            <li><strong>Universidades top:</strong> Programas bilíngues</li>
                            <li><strong>Testes aceitos:</strong> IELTS, TOEFL</li>
                            <li><strong>Nível exigido:</strong> 6.0-6.5 IELTS</li>
                        </ul>
                    </div>
                </div>

                <div class="highlight-box">
                    <h5>🎯 Dicas para Brasileiros:</h5>
                    <ul>
                        <li><strong>Dificuldade:</strong> Considerado um dos idiomas mais difíceis para falantes de português</li>
                        <li><strong>Tons:</strong> Sistema tonal - mudança de tom altera significado</li>
                        <li><strong>Caracteres:</strong> Sistema de escrita logográfico (não alfabético)</li>
                        <li><strong>Preparação:</strong> Recomenda-se 2-3 anos de estudo antes da viagem</li>
                    </ul>
                </div>

                <h5>📚 Testes de Proficiência:</h5>
                <ul>
                    <li><strong>HSK (Hanyu Shuiping Kaoshi):</strong> Teste oficial de chinês</li>
                    <li><strong>Níveis:</strong> HSK 1-6 (básico ao avançado)</li>
                    <li><strong>Graduação:</strong> HSK 4-5 (intermediário-avançado)</li>
                    <li><strong>Pós-graduação:</strong> HSK 5-6 (avançado)</li>
                </ul>

                <h5>🏫 Cursos de Chinês:</h5>
                <ul>
                    <li><strong>Institutos Confúcio:</strong> Presentes em várias universidades brasileiras</li>
                    <li><strong>Programas intensivos:</strong> 6 meses a 2 anos na China</li>
                    <li><strong>Apps recomendados:</strong> HelloChinese, Pleco, Anki</li>
                </ul>
            </div>

            <!-- Clima -->
            <div class="tab-pane fade" id="clima" role="tabpanel">
                <h4><i class="fas fa-sun me-2"></i>Clima Típico</h4>
                <p>A China possui <strong>grande diversidade climática</strong> devido ao seu tamanho continental. O clima varia de tropical no sul a temperado continental no norte, com monções influenciando as estações.</p>

                <div class="row">
                    <div class="col-md-6">
                        <h5>🌡️ Regiões Climáticas:</h5>
                        <ul>
                            <li><strong>Norte (Pequim):</strong> Continental - invernos frios, verões quentes</li>
                            <li><strong>Sul (Guangzhou):</strong> Subtropical - quente e úmido</li>
                            <li><strong>Oeste (Xinjiang):</strong> Desértico - extremos de temperatura</li>
                            <li><strong>Leste (Xangai):</strong> Subtropical úmido - quatro estações</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>📅 Estações do Ano:</h5>
                        <ul>
                            <li><strong>Primavera (Mar-Mai):</strong> 10-25°C, agradável</li>
                            <li><strong>Verão (Jun-Ago):</strong> 25-35°C, quente e úmido</li>
                            <li><strong>Outono (Set-Nov):</strong> 15-25°C, melhor época</li>
                            <li><strong>Inverno (Dez-Fev):</strong> -10 a 10°C, frio e seco</li>
                        </ul>
                    </div>
                </div>

                <div class="highlight-box">
                    <h5>🧳 Dicas de Vestuário:</h5>
                    <ul>
                        <li><strong>Inverno no Norte:</strong> Roupas muito quentes, temperaturas podem chegar a -20°C</li>
                        <li><strong>Verão:</strong> Roupas leves, mas leve casaco para ar condicionado</li>
                        <li><strong>Máscara:</strong> Comum usar devido à poluição em grandes cidades</li>
                        <li><strong>Chuva:</strong> Guarda-chuva essencial durante monções (jun-ago)</li>
                    </ul>
                </div>

                <h5>🌍 Comparação com o Brasil:</h5>
                <ul>
                    <li><strong>Estações opostas:</strong> Inverno chinês = verão brasileiro</li>
                    <li><strong>Poluição:</strong> Qualidade do ar pode ser desafiadora</li>
                    <li><strong>Umidade:</strong> Verões muito úmidos no sul</li>
                    <li><strong>Aquecimento:</strong> Nem todos os edifícios têm aquecimento central</li>
                </ul>
            </div>

            <!-- Custo de Vida -->
            <div class="tab-pane fade" id="custos" role="tabpanel">
                <h4><i class="fas fa-yen-sign me-2"></i>Custo de Vida</h4>
                <p>A China oferece um <strong>custo de vida relativamente baixo</strong> comparado a países ocidentais, especialmente fora das grandes metrópoles como Pequim e Xangai.</p>

                <div class="row">
                    <div class="col-md-6">
                        <h5>🏠 Acomodação (por mês):</h5>
                        <ul>
                            <li><strong>Dormitório universitário:</strong> ¥800-2000</li>
                            <li><strong>Apartamento compartilhado:</strong> ¥1500-4000</li>
                            <li><strong>Apartamento próprio:</strong> ¥2500-8000</li>
                            <li><strong>Homestay:</strong> ¥2000-3500</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>🍽️ Alimentação (por mês):</h5>
                        <ul>
                            <li><strong>Cantina universitária:</strong> ¥500-800</li>
                            <li><strong>Restaurante local:</strong> ¥15-30 por refeição</li>
                            <li><strong>Restaurante médio:</strong> ¥50-100 por refeição</li>
                            <li><strong>Supermercado:</strong> ¥800-1200</li>
                        </ul>
                    </div>
                </div>

                <h5>🚌 Transporte:</h5>
                <ul>
                    <li><strong>Metrô/Ônibus:</strong> ¥2-6 por viagem</li>
                    <li><strong>Bicicleta compartilhada:</strong> ¥1-2 por viagem</li>
                    <li><strong>Táxi/DiDi:</strong> ¥10-30 por viagem urbana</li>
                    <li><strong>Trem de alta velocidade:</strong> ¥200-800 (viagens longas)</li>
                </ul>

                <h5>📚 Outros Custos:</h5>
                <ul>
                    <li><strong>Livros e materiais:</strong> ¥500-1000 por semestre</li>
                    <li><strong>Seguro saúde:</strong> ¥600-1200 por ano</li>
                    <li><strong>Internet/Telefone:</strong> ¥50-100 por mês</li>
                    <li><strong>Atividades sociais:</strong> ¥300-800 por mês</li>
                </ul>

                <div class="highlight-box">
                    <h5>💡 Dicas para Economizar:</h5>
                    <ul>
                        <li>Use apps de pagamento móvel (WeChat Pay, Alipay) para descontos</li>
                        <li>Coma em cantinas universitárias - muito barato e nutritivo</li>
                        <li>Use bicicletas compartilhadas para transporte local</li>
                        <li>Compre em mercados locais em vez de supermercados ocidentais</li>
                    </ul>
                </div>

                <p><strong>💰 Orçamento mensal total:</strong> ¥3000-8000 (dependendo da cidade e estilo de vida)</p>
            </div>

            <!-- Bolsas de Estudo -->
            <div class="tab-pane fade" id="bolsas" role="tabpanel">
                <h4><i class="fas fa-graduation-cap me-2"></i>Bolsas de Estudo</h4>
                <p>A China oferece <strong>generosas bolsas de estudo</strong> para estudantes internacionais como parte da iniciativa "Belt and Road" para aumentar sua influência educacional global.</p>

                <h5>🏛️ Bolsas Governamentais:</h5>
                <ul>
                    <li><strong>Chinese Government Scholarship (CGS):</strong> Cobertura total + ¥3000-3500/mês</li>
                    <li><strong>Confucius Institute Scholarship:</strong> Para estudos de chinês e cultura</li>
                    <li><strong>Belt and Road Scholarship:</strong> Para países da iniciativa (inclui Brasil)</li>
                    <li><strong>Provincial Government Scholarships:</strong> Oferecidas por governos provinciais</li>
                </ul>

                <h5>🎓 Bolsas Universitárias Principais:</h5>
                <ul>
                    <li><strong>Tsinghua University:</strong> Schwarzman Scholars Program
                        <br><a href="https://www.schwarzmanscholars.org/" target="_blank" class="btn-custom">🔗 Schwarzman Scholars</a>
                    </li>
                    <li><strong>Peking University:</strong> Yenching Academy Scholarship
                        <br><a href="https://yenchingacademy.pku.edu.cn/" target="_blank" class="btn-custom">🔗 Yenching Academy</a>
                    </li>
                    <li><strong>Fudan University:</strong> International Students Scholarship
                        <br><a href="https://www.fudan.edu.cn/en/" target="_blank" class="btn-custom">🔗 Fudan Internacional</a>
                    </li>
                    <li><strong>Shanghai Jiao Tong University:</strong> SJTU Scholarship
                        <br><a href="https://www.sjtu.edu.cn/english/" target="_blank" class="btn-custom">🔗 SJTU Internacional</a>
                    </li>
                </ul>

                <h5>🌍 Programas Especiais:</h5>
                <ul>
                    <li><strong>BRICS Scholarship:</strong> Para países do BRICS (inclui Brasil)</li>
                    <li><strong>Silk Road Scholarship:</strong> Foco em cooperação internacional</li>
                    <li><strong>Great Wall Program:</strong> Para pesquisadores visitantes</li>
                    <li><strong>Dragon Program:</strong> Intercâmbio de curta duração</li>
                </ul>

                <div class="highlight-box">
                    <h5>📋 Requisitos Gerais:</h5>
                    <ul>
                        <li>Excelência acadêmica (média 8.0+ ou equivalente)</li>
                        <li>Proficiência em chinês (HSK) ou inglês (IELTS/TOEFL)</li>
                        <li>Carta de motivação bem estruturada</li>
                        <li>Cartas de recomendação acadêmica</li>
                        <li>Plano de estudos detalhado</li>
                        <li>Certificado de saúde</li>
                    </ul>
                </div>

                <p><strong>💰 Valores típicos:</strong> Bolsas integrais cobrem taxas + ¥2500-3500/mês para subsistência. Bolsas parciais variam de ¥10.000 a ¥30.000 por ano.</p>
            </div>

            <!-- Universidades -->
            <div class="tab-pane fade" id="universidades" role="tabpanel">
                <h4><i class="fas fa-university me-2"></i>Principais Universidades</h4>
                <p>A China possui algumas das <strong>universidades que mais crescem no ranking mundial</strong>, com investimento massivo em educação e pesquisa. Várias instituições chinesas estão no top 100 global.</p>

                <h5>🏆 Top Universidades Chinesas:</h5>
                <ul>
                    <li><strong>Tsinghua University:</strong> #17 mundial, "MIT da China"
                        <br><a href="https://www.tsinghua.edu.cn/en/" target="_blank" class="btn-custom">🔗 Tsinghua Internacional</a>
                    </li>
                    <li><strong>Peking University:</strong> #18 mundial, mais prestigiosa da China
                        <br><a href="https://english.pku.edu.cn/" target="_blank" class="btn-custom">🔗 PKU Internacional</a>
                    </li>
                    <li><strong>Fudan University:</strong> #40 mundial, forte em ciências sociais
                        <br><a href="https://www.fudan.edu.cn/en/" target="_blank" class="btn-custom">🔗 Fudan Internacional</a>
                    </li>
                    <li><strong>Shanghai Jiao Tong University:</strong> #46 mundial, excelência em engenharia
                        <br><a href="https://en.sjtu.edu.cn/" target="_blank" class="btn-custom">🔗 SJTU Internacional</a>
                    </li>
                    <li><strong>Zhejiang University:</strong> #52 mundial, inovação tecnológica
                        <br><a href="https://www.zju.edu.cn/english/" target="_blank" class="btn-custom">🔗 ZJU Internacional</a>
                    </li>
                    <li><strong>University of Science and Technology of China:</strong> #60 mundial, ciências exatas
                        <br><a href="https://en.ustc.edu.cn/" target="_blank" class="btn-custom">🔗 USTC Internacional</a>
                    </li>
                </ul>

                <h5>🌟 Universidades Especializadas:</h5>
                <ul>
                    <li><strong>Beijing Institute of Technology:</strong> Engenharia e tecnologia</li>
                    <li><strong>Renmin University:</strong> Ciências sociais e economia</li>
                    <li><strong>Beijing Normal University:</strong> Educação e humanidades</li>
                    <li><strong>Nanjing University:</strong> Ciências naturais</li>
                </ul>

                <h5>🎓 Áreas de Excelência:</h5>
                <ul>
                    <li><strong>Engenharia e Tecnologia:</strong> Tsinghua, SJTU, USTC</li>
                    <li><strong>Ciências Naturais:</strong> PKU, Fudan, Nanjing</li>
                    <li><strong>Medicina:</strong> PKU Health Science Center, Fudan</li>
                    <li><strong>Negócios:</strong> CEIBS, Tsinghua SEM, PKU Guanghua</li>
                    <li><strong>Artes e Humanidades:</strong> PKU, Fudan, Renmin</li>
                </ul>

                <div class="highlight-box">
                    <h5>📊 Dados sobre Intercambistas:</h5>
                    <ul>
                        <li>Mais de <strong>500.000 estudantes internacionais</strong> na China</li>
                        <li><strong>Brasil</strong> está entre os top 15 países de origem</li>
                        <li>Cerca de <strong>3.000 brasileiros</strong> estudam na China anualmente</li>
                        <li>Áreas mais populares: Negócios, Engenharia, Medicina Tradicional Chinesa, Idiomas</li>
                    </ul>
                </div>
            </div>

            <!-- Comunidade Brasileira -->
            <div class="tab-pane fade" id="comunidade-br" role="tabpanel">
                <h4><i class="fas fa-flag me-2"></i>Comunidade de Brasileiros</h4>
                <p>A China possui uma <strong>comunidade brasileira crescente</strong> de aproximadamente 15.000 pessoas, concentrada principalmente em Pequim, Xangai, Guangzhou e Shenzhen.</p>

                <h5>🏙️ Principais Cidades:</h5>
                <ul>
                    <li><strong>Xangai:</strong> Maior comunidade (~6.000), centro financeiro</li>
                    <li><strong>Pequim:</strong> Segunda maior (~4.000), capital política</li>
                    <li><strong>Guangzhou:</strong> Comunidade comercial (~2.500), porta de entrada</li>
                    <li><strong>Shenzhen:</strong> Comunidade tech (~1.500), vale do silício chinês</li>
                </ul>

                <h5>🤝 Organizações e Grupos:</h5>
                <ul>
                    <li><strong>Câmara de Comércio Brasil-China:</strong> Networking empresarial</li>
                    <li><strong>Associação de Brasileiros na China:</strong> Eventos culturais</li>
                    <li><strong>Grupos no WeChat:</strong> "Brasileiros na China", "BR em Shanghai"</li>
                    <li><strong>Associações Estudantis:</strong> Grupos brasileiros em universidades</li>
                </ul>

                <h5>🎉 Eventos e Festivais:</h5>
                <ul>
                    <li><strong>Festival do Brasil:</strong> Eventos anuais em grandes cidades</li>
                    <li><strong>Carnaval Brasileiro:</strong> Celebrações em Xangai e Pequim</li>
                    <li><strong>Festa Junina:</strong> Tradições brasileiras mantidas</li>
                    <li><strong>Copa do Mundo:</strong> Encontros para assistir jogos</li>
                </ul>

                <h5>🍽️ Vida Brasileira na China:</h5>
                <ul>
                    <li><strong>Restaurantes:</strong> Mais de 50 restaurantes brasileiros</li>
                    <li><strong>Produtos brasileiros:</strong> Disponíveis em lojas especializadas</li>
                    <li><strong>Capoeira:</strong> Grupos ativos em várias cidades</li>
                    <li><strong>Música:</strong> Eventos de música brasileira</li>
                </ul>

                <div class="highlight-box">
                    <h5>📱 Recursos Úteis:</h5>
                    <ul>
                        <li><strong>Consulados:</strong> Pequim, Xangai, Guangzhou</li>
                        <li><strong>Apps:</strong> WeChat (essencial), grupos brasileiros</li>
                        <li><strong>VPN:</strong> Necessário para acessar sites brasileiros</li>
                        <li><strong>Igreja:</strong> Missas em português em algumas cidades</li>
                    </ul>
                </div>
            </div>

            <!-- Cultura e Costumes -->
            <div class="tab-pane fade" id="cultura" role="tabpanel">
                <h4><i class="fas fa-yin-yang me-2"></i>Cultura e Costumes Locais</h4>
                <p>A cultura chinesa é uma das <strong>mais antigas e ricas do mundo</strong>, com 5.000 anos de história. Valores como respeito, hierarquia, harmonia e "face" (mianzi) são fundamentais.</p>

                <h5>🤝 Características Culturais:</h5>
                <ul>
                    <li><strong>Hierarquia:</strong> Respeito por idade e posição social</li>
                    <li><strong>"Face" (Mianzi):</strong> Conceito de dignidade e reputação</li>
                    <li><strong>Guanxi:</strong> Redes de relacionamento são cruciais</li>
                    <li><strong>Harmonia:</strong> Evitar conflitos diretos</li>
                    <li><strong>Coletivismo:</strong> Bem do grupo sobre individual</li>
                </ul>

                <h5>🏮 Tradições e Festivais:</h5>
                <ul>
                    <li><strong>Ano Novo Chinês (Spring Festival):</strong> Maior celebração do ano</li>
                    <li><strong>Festival do Meio do Outono:</strong> Reunião familiar</li>
                    <li><strong>Festival do Barco Dragão:</strong> Tradição milenar</li>
                    <li><strong>Golden Week:</strong> Semana de férias nacionais</li>
                </ul>

                <h5>🍜 Culinária e Etiqueta:</h5>
                <ul>
                    <li><strong>Pauzinhos:</strong> Aprender a usar é essencial</li>
                    <li><strong>Chá:</strong> Cultura do chá muito importante</li>
                    <li><strong>Banquetes:</strong> Rituais específicos de cortesia</li>
                    <li><strong>Comida quente:</strong> Preferência por alimentos quentes</li>
                </ul>

                <h5>🏛️ Filosofia e Valores:</h5>
                <ul>
                    <li><strong>Confucionismo:</strong> Ética, educação, respeito</li>
                    <li><strong>Taoísmo:</strong> Harmonia com a natureza</li>
                    <li><strong>Budismo:</strong> Compaixão e iluminação</li>
                    <li><strong>Feng Shui:</strong> Harmonia espacial</li>
                </ul>

                <div class="highlight-box">
                    <h5>⚠️ Dicas Importantes:</h5>
                    <ul>
                        <li>Nunca aponte com o dedo - use a mão aberta</li>
                        <li>Receba cartões de visita com as duas mãos</li>
                        <li>Evite tópicos políticos sensíveis</li>
                        <li>Aprenda cumprimentos básicos em chinês</li>
                        <li>Use WeChat - é essencial para vida social</li>
                    </ul>
                </div>
            </div>

            <!-- Calendário Acadêmico -->
            <div class="tab-pane fade" id="calendario" role="tabpanel">
                <h4><i class="fas fa-calendar-alt me-2"></i>Calendário Acadêmico</h4>
                <p>O ano acadêmico chinês segue o <strong>sistema de dois semestres</strong>, iniciando em setembro e terminando em julho, com férias de inverno durante o Ano Novo Chinês.</p>

                <div class="row">
                    <div class="col-md-6">
                        <h5>📅 Primeiro Semestre (Outono):</h5>
                        <ul>
                            <li><strong>Início:</strong> Início de setembro</li>
                            <li><strong>Término:</strong> Final de janeiro</li>
                            <li><strong>Exames:</strong> Janeiro</li>
                            <li><strong>Férias de inverno:</strong> 4-6 semanas (Ano Novo Chinês)</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>📅 Segundo Semestre (Primavera):</h5>
                        <ul>
                            <li><strong>Início:</strong> Fevereiro/março</li>
                            <li><strong>Término:</strong> Junho/julho</li>
                            <li><strong>Exames:</strong> Junho</li>
                            <li><strong>Férias de verão:</strong> Julho-agosto</li>
                        </ul>
                    </div>
                </div>

                <h5>🎓 Períodos de Aplicação:</h5>
                <ul>
                    <li><strong>Semestre de outono:</strong> Aplicações até 30 de abril</li>
                    <li><strong>Semestre de primavera:</strong> Aplicações até 15 de novembro</li>
                    <li><strong>Bolsas governamentais:</strong> Aplicações até 31 de março</li>
                    <li><strong>Visto de estudante:</strong> Aplicar 2-3 meses antes</li>
                </ul>

                <h5>🏮 Feriados Importantes:</h5>
                <ul>
                    <li><strong>Ano Novo Chinês:</strong> 1-2 semanas de férias (jan/fev)</li>
                    <li><strong>Golden Week:</strong> 1ª semana de outubro</li>
                    <li><strong>Dia Nacional:</strong> 1º de outubro</li>
                    <li><strong>Festival Qingming:</strong> Abril (3 dias)</li>
                </ul>

                <div class="highlight-box">
                    <h5>⏰ Cronograma Recomendado:</h5>
                    <ul>
                        <li><strong>12 meses antes:</strong> Pesquisar universidades e começar chinês</li>
                        <li><strong>10 meses antes:</strong> Preparar documentos e HSK</li>
                        <li><strong>8 meses antes:</strong> Aplicar para universidades e bolsas</li>
                        <li><strong>6 meses antes:</strong> Receber ofertas e aplicar para visto</li>
                        <li><strong>3 meses antes:</strong> Finalizar acomodação e preparativos</li>
                        <li><strong>1 mês antes:</strong> Chegada e orientação</li>
                    </ul>
                </div>
            </div>

            <!-- Guia para Bolsas -->
            <div class="tab-pane fade" id="guia" role="tabpanel">
                <h4><i class="fas fa-map me-2"></i>Guia para Conseguir Bolsas</h4>
                <p>Conseguir uma bolsa na China é <strong>mais acessível</strong> que em muitos países ocidentais, pois o governo chinês investe pesadamente em atrair estudantes internacionais.</p>

                <h5>📚 O que Estudar:</h5>
                <ul>
                    <li><strong>Chinês Mandarim:</strong> HSK 4-6 dependendo do programa</li>
                    <li><strong>Inglês:</strong> IELTS 6.0+ ou TOEFL 80+ para programas em inglês</li>
                    <li><strong>Área acadêmica:</strong> Mantenha média alta (8.0+ no Brasil)</li>
                    <li><strong>Cultura chinesa:</strong> Demonstre interesse genuíno pela China</li>
                </ul>

                <h5>📋 Documentação Necessária:</h5>
                <ul>
                    <li><strong>Formulário de aplicação:</strong> Online via CSC ou universidade</li>
                    <li><strong>Diploma e histórico:</strong> Traduzidos e notarizados</li>
                    <li><strong>Teste de idioma:</strong> HSK ou IELTS/TOEFL</li>
                    <li><strong>Plano de estudos:</strong> Detalhado e específico</li>
                    <li><strong>Cartas de recomendação:</strong> 2 professores</li>
                    <li><strong>Certificado médico:</strong> Exame de saúde específico</li>
                    <li><strong>Certificado de antecedentes criminais:</strong> Apostilado</li>
                </ul>

                <h5>✍️ Como Fazer Plano de Estudos:</h5>
                <ul>
                    <li><strong>Estrutura:</strong> Objetivos, metodologia, cronograma, resultados esperados</li>
                    <li><strong>Específico:</strong> Mencione professores e projetos da universidade</li>
                    <li><strong>Relevância:</strong> Como contribuirá para Brasil-China</li>
                    <li><strong>Tamanho:</strong> 800-1500 palavras</li>
                </ul>

                <h5>💌 Carta de Motivação:</h5>
                <ul>
                    <li><strong>Interesse pela China:</strong> Demonstre conhecimento cultural</li>
                    <li><strong>Objetivos claros:</strong> Como os estudos ajudarão sua carreira</li>
                    <li><strong>Contribuição:</strong> Como retribuirá à sociedade</li>
                    <li><strong>Tom respeitoso:</strong> Formal e humilde</li>
                </ul>

                <div class="highlight-box">
                    <h5>🎯 Preparação para Entrevistas:</h5>
                    <ul>
                        <li><strong>Pesquise:</strong> História, cultura e política chinesa básica</li>
                        <li><strong>Pratique:</strong> Perguntas em chinês básico</li>
                        <li><strong>Demonstre:</strong> Respeito pela cultura chinesa</li>
                        <li><strong>Prepare:</strong> Perguntas sobre cooperação Brasil-China</li>
                    </ul>
                </div>

                <p><strong>🔗 Links Úteis:</strong></p>
                <a href="https://www.campuschina.org/" target="_blank" class="btn-custom">🎓 Campus China</a>
                <a href="https://www.csc.edu.cn/" target="_blank" class="btn-custom">🏆 China Scholarship Council</a>
            </div>

            <!-- Trabalho com Visto de Estudante -->
            <div class="tab-pane fade" id="trabalho" role="tabpanel">
                <h4><i class="fas fa-briefcase me-2"></i>Trabalho com Visto de Estudante</h4>
                <p><strong>Limitado!</strong> Estudantes internacionais na China têm <strong>restrições significativas</strong> para trabalhar, mas existem algumas oportunidades específicas permitidas.</p>

                <h5>⏰ Permissões de Trabalho:</h5>
                <ul>
                    <li><strong>On-campus:</strong> Trabalhos dentro da universidade são permitidos</li>
                    <li><strong>Part-time off-campus:</strong> Apenas com permissão especial</li>
                    <li><strong>Estágios:</strong> Permitidos se parte do currículo</li>
                    <li><strong>Trabalho de verão:</strong> Possível com autorização</li>
                </ul>

                <h5>📄 Documentos Necessários:</h5>
                <ul>
                    <li><strong>Permissão da universidade:</strong> Carta oficial</li>
                    <li><strong>Permissão do PSB:</strong> Polícia de Segurança Pública</li>
                    <li><strong>Visto de estudante válido:</strong> Categoria X1 ou X2</li>
                    <li><strong>Certificado de matrícula:</strong> Comprovação de estudos</li>
                </ul>

                <h5>💼 Tipos de Trabalho Permitidos:</h5>
                <ul>
                    <li><strong>Assistente de ensino:</strong> Aulas de português/inglês</li>
                    <li><strong>Pesquisa acadêmica:</strong> Projetos universitários</li>
                    <li><strong>Tradução:</strong> Português-chinês (freelance limitado)</li>
                    <li><strong>Tutoring:</strong> Aulas particulares de idiomas</li>
                </ul>

                <h5>🏢 Oportunidades Comuns:</h5>
                <ul>
                    <li><strong>Ensino de português:</strong> ¥100-200/hora</li>
                    <li><strong>Tradução:</strong> ¥150-300/hora</li>
                    <li><strong>Assistente de pesquisa:</strong> ¥50-100/hora</li>
                    <li><strong>Trabalhos no campus:</strong> ¥30-50/hora</li>
                </ul>

                <h5>🎓 Pós-Graduação - Oportunidades:</h5>
                <ul>
                    <li><strong>Visto de trabalho:</strong> Possível após graduação</li>
                    <li><strong>Programa de talentos:</strong> Para graduados em universidades top</li>
                    <li><strong>Startups:</strong> Crescente ecossistema empreendedor</li>
                    <li><strong>Multinacionais:</strong> Empresas brasileiras na China</li>
                </ul>

                <div class="highlight-box">
                    <h5>⚠️ Importantes Considerações:</h5>
                    <ul>
                        <li>Trabalhar sem permissão pode resultar em deportação</li>
                        <li>Processo burocrático pode ser longo e complexo</li>
                        <li>Foque nos estudos - oportunidades de trabalho são limitadas</li>
                        <li>Construa guanxi (relacionamentos) para futuras oportunidades</li>
                    </ul>
                </div>

                <p><strong>🎯 Dica:</strong> Use o período de estudos para construir relacionamentos e aprender chinês - isso será mais valioso que trabalhos de meio período.</p>
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
