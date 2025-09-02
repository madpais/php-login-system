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
$resultado_visita = registrarVisitaPais($_SESSION['usuario_id'], 'dinamarca');

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
    <title>Dinamarca - Guia Completo para Estudantes - DayDreaming</title>
    
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
                            <img src="https://flagcdn.com/w80/dk.png" alt="Bandeira da Dinamarca" class="country-flag">
                            <h1 class="country-title">Dinamarca</h1>
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
                                <i class="fas fa-map-marker-alt me-1"></i>Dinamarca
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
        <img src="../imagens/dinamarca_home.png" alt="Dinamarca - Paisagem" class="hero-image">
    </div>

    <!-- Cards de Informações Básicas -->
    <div class="container my-5">
        <div class="row g-4">
            <!-- População -->
            <div class="col-md-4">
                <div class="info-card">
                    <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="População" class="icon">
                    <h3>5,9M</h3>
                    <span class="badge">População</span>
                    <p>Habitantes em todo o país</p>
                </div>
            </div>

            <!-- IDH -->
            <div class="col-md-4">
                <div class="info-card">
                    <img src="https://cdn-icons-png.flaticon.com/512/616/616492.png" alt="IDH" class="icon">
                    <h3>6º</h3>
                    <span class="badge">IDH Mundial</span>
                    <p>Índice de Desenvolvimento Humano</p>
                </div>
            </div>

            <!-- Moeda -->
            <div class="col-md-4">
                <div class="info-card">
                    <img src="https://cdn-icons-png.flaticon.com/512/2331/2331970.png" alt="Moeda" class="icon">
                    <h3>DKK</h3>
                    <span class="badge">Moeda Local</span>
                    <p>Coroa Dinamarquesa</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Título da Seção -->
    <div class="section-title">
        <h2>🇩🇰 Guia Completo: Estudar na Dinamarca</h2>
        <p>Descubra o país mais feliz do mundo e suas oportunidades educacionais excepcionais</p>
    </div>

    <!-- Menu de Navegação por Abas -->
    <div class="container">
        <ul class="nav nav-tabs justify-content-center" id="dinamarcaTab" role="tablist">
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
        <div class="tab-content" id="dinamarcaTabContent">
            <!-- Idioma Oficial -->
            <div class="tab-pane fade show active" id="idioma" role="tabpanel">
                <h4><i class="fas fa-language me-2"></i>Idioma Oficial</h4>
                <p><strong>Dinamarquês (Dansk)</strong> é o idioma oficial da Dinamarca, falado por cerca de 6 milhões de pessoas. Pertence à família das línguas germânicas do norte, relacionado ao sueco e norueguês.</p>

                <div class="row">
                    <div class="col-md-6">
                        <h5>🇩🇰 Dinamarquês (Dansk)</h5>
                        <ul>
                            <li><strong>Falantes:</strong> 6 milhões nativos</li>
                            <li><strong>Família:</strong> Germânica do Norte</li>
                            <li><strong>Escrita:</strong> Alfabeto latino (29 letras)</li>
                            <li><strong>Características:</strong> Entonação melódica única</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>🌍 Inglês na Educação</h5>
                        <ul>
                            <li><strong>Proficiência:</strong> 86% dos dinamarqueses falam inglês</li>
                            <li><strong>Programas internacionais:</strong> Muitos cursos em inglês</li>
                            <li><strong>Testes aceitos:</strong> IELTS, TOEFL, Cambridge</li>
                            <li><strong>Nível exigido:</strong> 6.5-7.0 IELTS</li>
                        </ul>
                    </div>
                </div>

                <div class="highlight-box">
                    <h5>🎯 Dicas para Brasileiros:</h5>
                    <ul>
                        <li><strong>Dificuldade:</strong> Moderada para falantes de português</li>
                        <li><strong>Pronúncia:</strong> Stød (parada glotal) é característica única</li>
                        <li><strong>Inglês:</strong> Amplamente falado, especialmente em universidades</li>
                        <li><strong>Preparação:</strong> Inglês fluente é suficiente para a maioria dos programas</li>
                    </ul>
                </div>

                <h5>📚 Requisitos de Proficiência:</h5>
                <ul>
                    <li><strong>Graduação em inglês:</strong> IELTS 6.5 ou TOEFL 88</li>
                    <li><strong>Pós-graduação em inglês:</strong> IELTS 7.0 ou TOEFL 100</li>
                    <li><strong>Programas em dinamarquês:</strong> Studieprøven ou equivalente</li>
                    <li><strong>Cursos de dinamarquês:</strong> Gratuitos para estudantes internacionais</li>
                </ul>

                <h5>🏫 Aprendendo Dinamarquês:</h5>
                <ul>
                    <li><strong>Duolingo:</strong> Curso gratuito de dinamarquês</li>
                    <li><strong>Sprogcenter:</strong> Centros de idiomas nas universidades</li>
                    <li><strong>Apps recomendados:</strong> Babbel, Mondly, Memrise</li>
                    <li><strong>Vantagem:</strong> Facilita integração e oportunidades de trabalho</li>
                </ul>
            </div>

            <!-- Clima -->
            <div class="tab-pane fade" id="clima" role="tabpanel">
                <h4><i class="fas fa-cloud me-2"></i>Clima Típico</h4>
                <p>A Dinamarca possui um <strong>clima oceânico temperado</strong>, caracterizado por invernos amenos e verões frescos, com alta umidade e ventos frequentes do Mar do Norte e Báltico.</p>

                <div class="row">
                    <div class="col-md-6">
                        <h5>🌡️ Temperaturas Médias:</h5>
                        <ul>
                            <li><strong>Inverno (Dez-Fev):</strong> 0-4°C, dias curtos</li>
                            <li><strong>Primavera (Mar-Mai):</strong> 5-15°C, agradável</li>
                            <li><strong>Verão (Jun-Ago):</strong> 15-22°C, dias longos</li>
                            <li><strong>Outono (Set-Nov):</strong> 8-15°C, chuvoso</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>☔ Características Climáticas:</h5>
                        <ul>
                            <li><strong>Precipitação:</strong> 600-800mm anuais</li>
                            <li><strong>Dias chuvosos:</strong> 170+ por ano</li>
                            <li><strong>Umidade:</strong> Alta (80-85%)</li>
                            <li><strong>Vento:</strong> Constante, especialmente no oeste</li>
                        </ul>
                    </div>
                </div>

                <div class="highlight-box">
                    <h5>🧳 Dicas de Vestuário:</h5>
                    <ul>
                        <li><strong>Essencial:</strong> Casaco impermeável e guarda-chuva</li>
                        <li><strong>Inverno:</strong> Roupas em camadas, casaco quente</li>
                        <li><strong>Verão:</strong> Roupas leves + casaco para a noite</li>
                        <li><strong>Calçados:</strong> Sapatos impermeáveis são fundamentais</li>
                    </ul>
                </div>

                <h5>☀️ Luz Solar:</h5>
                <ul>
                    <li><strong>Verão:</strong> Até 17 horas de luz solar (noites brancas)</li>
                    <li><strong>Inverno:</strong> Apenas 7 horas de luz solar</li>
                    <li><strong>Hygge:</strong> Conceito dinamarquês para lidar com o inverno</li>
                    <li><strong>Vitamina D:</strong> Suplementação recomendada no inverno</li>
                </ul>
            </div>

            <!-- Custo de Vida -->
            <div class="tab-pane fade" id="custos" role="tabpanel">
                <h4><i class="fas fa-coins me-2"></i>Custo de Vida</h4>
                <p>A Dinamarca tem um <strong>custo de vida alto</strong>, mas oferece excelente qualidade de vida, serviços públicos gratuitos e salários elevados para estudantes que trabalham.</p>

                <div class="row">
                    <div class="col-md-6">
                        <h5>🏠 Acomodação (por mês):</h5>
                        <ul>
                            <li><strong>Kollegium (residência):</strong> DKK 3.000-5.000</li>
                            <li><strong>Quarto compartilhado:</strong> DKK 4.000-7.000</li>
                            <li><strong>Apartamento próprio:</strong> DKK 8.000-15.000</li>
                            <li><strong>Homestay:</strong> DKK 5.000-8.000</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>🍽️ Alimentação (por mês):</h5>
                        <ul>
                            <li><strong>Supermercado:</strong> DKK 2.000-3.000</li>
                            <li><strong>Cantina universitária:</strong> DKK 50-80 por refeição</li>
                            <li><strong>Restaurante médio:</strong> DKK 200-350 por refeição</li>
                            <li><strong>Fast food:</strong> DKK 80-120 por refeição</li>
                        </ul>
                    </div>
                </div>

                <h5>🚌 Transporte:</h5>
                <ul>
                    <li><strong>Passe mensal estudantil:</strong> DKK 375 (desconto de 50%)</li>
                    <li><strong>Bicicleta:</strong> Meio de transporte mais popular</li>
                    <li><strong>Trem nacional:</strong> Eficiente e pontual</li>
                    <li><strong>Transporte urbano:</strong> DKK 24-36 por viagem</li>
                </ul>

                <h5>📚 Outros Custos:</h5>
                <ul>
                    <li><strong>Livros e materiais:</strong> DKK 2.000-4.000 por ano</li>
                    <li><strong>Seguro saúde:</strong> Gratuito (sistema público)</li>
                    <li><strong>Atividades sociais:</strong> DKK 1.000-2.000 por mês</li>
                    <li><strong>Telefone/Internet:</strong> DKK 200-400 por mês</li>
                </ul>

                <div class="highlight-box">
                    <h5>💡 Dicas para Economizar:</h5>
                    <ul>
                        <li>Use bicicleta - transporte gratuito e saudável</li>
                        <li>Aproveite descontos estudantis em transporte e cultura</li>
                        <li>Cozinhe em casa - supermercados têm preços razoáveis</li>
                        <li>Compre em lojas de segunda mão (genbrug)</li>
                    </ul>
                </div>

                <p><strong>💰 Orçamento mensal total:</strong> DKK 8.000-15.000 (dependendo do estilo de vida)</p>
            </div>

            <!-- Bolsas de Estudo -->
            <div class="tab-pane fade" id="bolsas" role="tabpanel">
                <h4><i class="fas fa-graduation-cap me-2"></i>Bolsas de Estudo</h4>
                <p>A Dinamarca oferece <strong>diversas oportunidades de bolsas</strong> para estudantes internacionais, especialmente para programas de mestrado e doutorado.</p>

                <h5>🏛️ Bolsas Governamentais:</h5>
                <ul>
                    <li><strong>Danish Government Scholarships:</strong> Para estudantes de países em desenvolvimento</li>
                    <li><strong>Nordplus:</strong> Para estudantes dos países nórdicos e bálticos</li>
                    <li><strong>Erasmus+:</strong> Para estudantes europeus</li>
                    <li><strong>Danish Agency for Higher Education:</strong> Várias modalidades</li>
                </ul>

                <h5>🎓 Bolsas Universitárias Principais:</h5>
                <ul>
                    <li><strong>University of Copenhagen:</strong> Excellence Scholarship Programme
                        <br><a href="https://studies.ku.dk/masters/tuition-fees-and-scholarships/" target="_blank" class="btn-custom">🔗 UCPH Scholarships</a>
                    </li>
                    <li><strong>Technical University of Denmark (DTU):</strong> DTU Scholarships
                        <br><a href="https://www.dtu.dk/english/education/fees-and-scholarships" target="_blank" class="btn-custom">🔗 DTU Scholarships</a>
                    </li>
                    <li><strong>Aarhus University:</strong> AU Scholarships
                        <br><a href="https://international.au.dk/education/admissions/scholarships/" target="_blank" class="btn-custom">🔗 AU Scholarships</a>
                    </li>
                    <li><strong>Copenhagen Business School:</strong> CBS Scholarships
                        <br><a href="https://www.cbs.dk/en/study/fees-and-funding" target="_blank" class="btn-custom">🔗 CBS Scholarships</a>
                    </li>
                </ul>

                <h5>🌍 Programas Especiais:</h5>
                <ul>
                    <li><strong>Marie Curie Fellowships:</strong> Para pesquisadores</li>
                    <li><strong>Fulbright Denmark:</strong> Para cidadãos americanos</li>
                    <li><strong>CAPES/CNPq:</strong> Bolsas brasileiras para estudos no exterior</li>
                    <li><strong>Nordea Foundation:</strong> Para estudos em sustentabilidade</li>
                </ul>

                <div class="highlight-box">
                    <h5>📋 Requisitos Gerais:</h5>
                    <ul>
                        <li>Excelência acadêmica (média 8.5+ ou equivalente)</li>
                        <li>Proficiência em inglês comprovada</li>
                        <li>Carta de motivação bem estruturada</li>
                        <li>Cartas de recomendação acadêmica</li>
                        <li>Projeto de pesquisa (para pós-graduação)</li>
                        <li>Demonstração de interesse pela Dinamarca</li>
                    </ul>
                </div>

                <p><strong>💰 Valores típicos:</strong> Bolsas parciais cobrem 25-100% das taxas. Bolsas integrais podem incluir DKK 6.000-12.000/mês para subsistência.</p>
            </div>

            <!-- Universidades -->
            <div class="tab-pane fade" id="universidades" role="tabpanel">
                <h4><i class="fas fa-university me-2"></i>Principais Universidades</h4>
                <p>A Dinamarca possui <strong>universidades de classe mundial</strong> conhecidas pela excelência em pesquisa, inovação e ensino de alta qualidade, especialmente em sustentabilidade e tecnologia.</p>

                <h5>🏆 Top Universidades Dinamarquesas:</h5>
                <ul>
                    <li><strong>University of Copenhagen (UCPH):</strong> #76 mundial, maior e mais antiga
                        <br><a href="https://studies.ku.dk/masters/" target="_blank" class="btn-custom">🔗 UCPH Internacional</a>
                    </li>
                    <li><strong>Technical University of Denmark (DTU):</strong> #99 mundial, líder em engenharia
                        <br><a href="https://www.dtu.dk/english/education" target="_blank" class="btn-custom">🔗 DTU Internacional</a>
                    </li>
                    <li><strong>Aarhus University:</strong> #155 mundial, forte em ciências sociais
                        <br><a href="https://international.au.dk/education/" target="_blank" class="btn-custom">🔗 AU Internacional</a>
                    </li>
                    <li><strong>Copenhagen Business School (CBS):</strong> Top 15 em negócios na Europa
                        <br><a href="https://www.cbs.dk/en/study" target="_blank" class="btn-custom">🔗 CBS Internacional</a>
                    </li>
                    <li><strong>Aalborg University (AAU):</strong> Inovação em aprendizado baseado em problemas
                        <br><a href="https://www.en.aau.dk/education/" target="_blank" class="btn-custom">🔗 AAU Internacional</a>
                    </li>
                </ul>

                <h5>🌟 Universidades Especializadas:</h5>
                <ul>
                    <li><strong>IT University of Copenhagen:</strong> Tecnologia da informação</li>
                    <li><strong>Royal Danish Academy:</strong> Arte e arquitetura</li>
                    <li><strong>University of Southern Denmark:</strong> Ciências da saúde</li>
                    <li><strong>Roskilde University:</strong> Estudos interdisciplinares</li>
                </ul>

                <h5>🎓 Áreas de Excelência:</h5>
                <ul>
                    <li><strong>Sustentabilidade e Energia:</strong> DTU, UCPH</li>
                    <li><strong>Ciências da Vida:</strong> UCPH, University of Southern Denmark</li>
                    <li><strong>Negócios e Economia:</strong> CBS, Aarhus</li>
                    <li><strong>Tecnologia da Informação:</strong> ITU, DTU, AAU</li>
                    <li><strong>Design e Arquitetura:</strong> Royal Danish Academy</li>
                </ul>

                <div class="highlight-box">
                    <h5>📊 Dados sobre Intercambistas:</h5>
                    <ul>
                        <li>Mais de <strong>40.000 estudantes internacionais</strong> na Dinamarca</li>
                        <li><strong>Brasil</strong> está entre os top 20 países de origem</li>
                        <li>Cerca de <strong>800 brasileiros</strong> estudam na Dinamarca anualmente</li>
                        <li>Áreas mais populares: Sustentabilidade, Negócios, Engenharia, Design</li>
                    </ul>
                </div>
            </div>

            <!-- Comunidade Brasileira -->
            <div class="tab-pane fade" id="comunidade-br" role="tabpanel">
                <h4><i class="fas fa-flag me-2"></i>Comunidade de Brasileiros</h4>
                <p>A Dinamarca possui uma <strong>comunidade brasileira pequena mas unida</strong> de aproximadamente 3.000 pessoas, concentrada principalmente em Copenhague, Aarhus e Aalborg.</p>

                <h5>🏙️ Principais Cidades:</h5>
                <ul>
                    <li><strong>Copenhague:</strong> Maior comunidade (~1.500), capital e centro cultural</li>
                    <li><strong>Aarhus:</strong> Segunda maior (~800), cidade universitária</li>
                    <li><strong>Aalborg:</strong> Comunidade crescente (~400), centro tecnológico</li>
                    <li><strong>Odense:</strong> Comunidade menor (~300), cidade de Hans Christian Andersen</li>
                </ul>

                <h5>🤝 Organizações e Grupos:</h5>
                <ul>
                    <li><strong>Associação Brasil-Dinamarca:</strong> Eventos culturais e networking</li>
                    <li><strong>Brazilian Community Denmark:</strong> Grupo no Facebook</li>
                    <li><strong>Grupos de WhatsApp:</strong> "Brasileiros na Dinamarca"</li>
                    <li><strong>Associações Estudantis:</strong> Grupos brasileiros nas universidades</li>
                </ul>

                <h5>🎉 Eventos e Festivais:</h5>
                <ul>
                    <li><strong>Festival do Brasil:</strong> Evento anual em Copenhague</li>
                    <li><strong>Festa Junina:</strong> Celebrações tradicionais</li>
                    <li><strong>Copa do Mundo:</strong> Encontros para assistir jogos</li>
                    <li><strong>Capoeira:</strong> Grupos ativos em várias cidades</li>
                </ul>

                <h5>🍽️ Vida Brasileira na Dinamarca:</h5>
                <ul>
                    <li><strong>Restaurantes:</strong> Poucos, mas autênticos restaurantes brasileiros</li>
                    <li><strong>Produtos brasileiros:</strong> Disponíveis em lojas especializadas</li>
                    <li><strong>Música:</strong> Eventos ocasionais de música brasileira</li>
                    <li><strong>Futebol:</strong> Times amadores brasileiros</li>
                </ul>

                <div class="highlight-box">
                    <h5>📱 Recursos Úteis:</h5>
                    <ul>
                        <li><strong>Consulado:</strong> Consulado Honorário em Copenhague</li>
                        <li><strong>Apps:</strong> Grupos no Facebook e WhatsApp</li>
                        <li><strong>Igreja:</strong> Missas em português ocasionalmente</li>
                        <li><strong>Apoio:</strong> Comunidade muito acolhedora e solidária</li>
                    </ul>
                </div>
            </div>

            <!-- Cultura e Costumes -->
            <div class="tab-pane fade" id="cultura" role="tabpanel">
                <h4><i class="fas fa-heart me-2"></i>Cultura e Costumes Locais</h4>
                <p>A cultura dinamarquesa é conhecida pelo <strong>conceito de "hygge"</strong>, igualdade social, sustentabilidade e um dos maiores índices de felicidade do mundo.</p>

                <h5>🤝 Características Culturais:</h5>
                <ul>
                    <li><strong>Hygge:</strong> Conceito de aconchego, bem-estar e momentos especiais</li>
                    <li><strong>Janteloven:</strong> Lei de Jante - modéstia e igualdade</li>
                    <li><strong>Lagom:</strong> Equilíbrio e moderação em tudo</li>
                    <li><strong>Pontualidade:</strong> Ser pontual é extremamente importante</li>
                    <li><strong>Informalidade:</strong> Sociedade igualitária e informal</li>
                </ul>

                <h5>🚴 Estilo de Vida:</h5>
                <ul>
                    <li><strong>Ciclismo:</strong> 40% dos dinamarqueses usam bicicleta diariamente</li>
                    <li><strong>Work-life balance:</strong> 37 horas de trabalho por semana</li>
                    <li><strong>Sustentabilidade:</strong> Consciência ambiental muito forte</li>
                    <li><strong>Design:</strong> Valorização do design funcional e minimalista</li>
                </ul>

                <h5>🎭 Tradições e Festivais:</h5>
                <ul>
                    <li><strong>Midsummer (Sankt Hans):</strong> Solstício de verão com fogueiras</li>
                    <li><strong>Lucia Day:</strong> 13 de dezembro, festival de luzes</li>
                    <li><strong>Fastelavn:</strong> Carnaval dinamarquês</li>
                    <li><strong>Constitution Day:</strong> 5 de junho, feriado nacional</li>
                </ul>

                <h5>🍽️ Culinária:</h5>
                <ul>
                    <li><strong>Smørrebrød:</strong> Sanduíches abertos tradicionais</li>
                    <li><strong>New Nordic Cuisine:</strong> Movimento gastronômico inovador</li>
                    <li><strong>Café culture:</strong> Cultura do café muito forte</li>
                    <li><strong>Organic food:</strong> Grande preferência por alimentos orgânicos</li>
                </ul>

                <div class="highlight-box">
                    <h5>⚠️ Dicas Importantes:</h5>
                    <ul>
                        <li>Seja sempre pontual - atrasos são considerados desrespeitosos</li>
                        <li>Remova os sapatos ao entrar em casas</li>
                        <li>Evite ostentação - modéstia é valorizada</li>
                        <li>Aprenda a andar de bicicleta - é essencial</li>
                        <li>Respeite o espaço pessoal - dinamarqueses valorizam privacidade</li>
                    </ul>
                </div>
            </div>

            <!-- Calendário Acadêmico -->
            <div class="tab-pane fade" id="calendario" role="tabpanel">
                <h4><i class="fas fa-calendar-alt me-2"></i>Calendário Acadêmico</h4>
                <p>O ano acadêmico dinamarquês segue o <strong>sistema europeu</strong>, iniciando em setembro e terminando em junho, dividido em dois semestres com períodos de exames bem definidos.</p>

                <div class="row">
                    <div class="col-md-6">
                        <h5>📅 Semestre de Outono:</h5>
                        <ul>
                            <li><strong>Início:</strong> Início de setembro</li>
                            <li><strong>Término:</strong> Final de janeiro</li>
                            <li><strong>Exames:</strong> Janeiro</li>
                            <li><strong>Férias de inverno:</strong> 2 semanas em dezembro</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>📅 Semestre de Primavera:</h5>
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
                    <li><strong>Semestre de outono:</strong> Aplicações até 15 de março</li>
                    <li><strong>Semestre de primavera:</strong> Aplicações até 1º de setembro</li>
                    <li><strong>Bolsas:</strong> Aplicações geralmente 6-8 meses antes</li>
                    <li><strong>Visto:</strong> Aplicar 2-3 meses antes do início</li>
                </ul>

                <h5>🏖️ Feriados e Pausas:</h5>
                <ul>
                    <li><strong>Páscoa:</strong> 1 semana de férias (março/abril)</li>
                    <li><strong>Pentecostes:</strong> Feriado longo (maio)</li>
                    <li><strong>Verão:</strong> Universidades fechadas em julho</li>
                    <li><strong>Natal:</strong> 2 semanas de férias</li>
                </ul>

                <div class="highlight-box">
                    <h5>⏰ Cronograma Recomendado:</h5>
                    <ul>
                        <li><strong>12 meses antes:</strong> Pesquisar universidades e programas</li>
                        <li><strong>10 meses antes:</strong> Preparar documentos e IELTS</li>
                        <li><strong>8 meses antes:</strong> Aplicar para universidades e bolsas</li>
                        <li><strong>6 meses antes:</strong> Receber ofertas e aplicar para visto</li>
                        <li><strong>3 meses antes:</strong> Organizar acomodação e chegada</li>
                        <li><strong>1 mês antes:</strong> Finalizar preparativos e documentos</li>
                    </ul>
                </div>
            </div>

            <!-- Guia para Bolsas -->
            <div class="tab-pane fade" id="guia" role="tabpanel">
                <h4><i class="fas fa-map me-2"></i>Guia para Conseguir Bolsas</h4>
                <p>Conseguir uma bolsa na Dinamarca requer <strong>excelência acadêmica e demonstração clara de motivação</strong>. O processo é competitivo, mas as oportunidades existem.</p>

                <h5>📚 O que Estudar:</h5>
                <ul>
                    <li><strong>Inglês:</strong> IELTS 7.0+ ou TOEFL 100+ para competitividade</li>
                    <li><strong>Área acadêmica:</strong> Mantenha média alta (9.0+ no Brasil)</li>
                    <li><strong>Sustentabilidade:</strong> Conhecimento em temas ambientais é vantagem</li>
                    <li><strong>Cultura dinamarquesa:</strong> Demonstre interesse genuíno pelo país</li>
                </ul>

                <h5>📋 Documentação Necessária:</h5>
                <ul>
                    <li><strong>Histórico acadêmico:</strong> Traduzido e certificado</li>
                    <li><strong>Diploma:</strong> Traduzido e certificado</li>
                    <li><strong>Teste de inglês:</strong> IELTS/TOEFL válido</li>
                    <li><strong>CV europeu:</strong> Formato Europass</li>
                    <li><strong>Carta de motivação:</strong> Específica para cada programa</li>
                    <li><strong>Cartas de recomendação:</strong> 2-3 professores</li>
                    <li><strong>Portfólio:</strong> Para áreas criativas</li>
                </ul>

                <h5>✍️ Como Fazer CV Europeu:</h5>
                <ul>
                    <li><strong>Formato:</strong> Use template Europass oficial</li>
                    <li><strong>Seções:</strong> Dados pessoais, educação, experiência, habilidades</li>
                    <li><strong>Idiomas:</strong> Use escala europeia (A1-C2)</li>
                    <li><strong>Competências:</strong> Destaque habilidades digitais e sociais</li>
                </ul>

                <h5>💌 Carta de Motivação:</h5>
                <ul>
                    <li><strong>Estrutura:</strong> Motivação, objetivos, contribuição, conclusão</li>
                    <li><strong>Conteúdo:</strong> Por que Dinamarca, por que este programa</li>
                    <li><strong>Valores:</strong> Mencione sustentabilidade, igualdade, inovação</li>
                    <li><strong>Tamanho:</strong> 1 página, máximo 600 palavras</li>
                </ul>

                <div class="highlight-box">
                    <h5>🎯 Preparação para Entrevistas:</h5>
                    <ul>
                        <li><strong>Pesquise:</strong> Universidade, programa, professores, projetos</li>
                        <li><strong>Pratique:</strong> Perguntas sobre motivação e objetivos</li>
                        <li><strong>Demonstre:</strong> Conhecimento sobre cultura dinamarquesa</li>
                        <li><strong>Prepare:</strong> Perguntas sobre sustentabilidade e inovação</li>
                    </ul>
                </div>

                <p><strong>🔗 Links Úteis:</strong></p>
                <a href="https://studyindenmark.dk/" target="_blank" class="btn-custom">🎓 Study in Denmark</a>
                <a href="https://www.ufm.dk/en" target="_blank" class="btn-custom">🏆 Ministry of Education</a>
            </div>

            <!-- Trabalho com Visto de Estudante -->
            <div class="tab-pane fade" id="trabalho" role="tabpanel">
                <h4><i class="fas fa-briefcase me-2"></i>Trabalho com Visto de Estudante</h4>
                <p><strong>Sim!</strong> Estudantes internacionais na Dinamarca podem trabalhar com visto de estudante, e o país oferece <strong>excelentes oportunidades</strong> de trabalho e salários altos.</p>

                <h5>⏰ Permissões de Trabalho:</h5>
                <ul>
                    <li><strong>Durante estudos:</strong> Máximo 20 horas por semana</li>
                    <li><strong>Férias de verão:</strong> Tempo integral (junho-agosto)</li>
                    <li><strong>Outras férias:</strong> Tempo integral durante pausas acadêmicas</li>
                    <li><strong>Estágios:</strong> Tempo integral se parte do currículo</li>
                </ul>

                <h5>📄 Documentos Necessários:</h5>
                <ul>
                    <li><strong>CPR number:</strong> Número de identificação dinamarquês</li>
                    <li><strong>Borgerservice.dk:</strong> Registro online obrigatório</li>
                    <li><strong>Conta bancária:</strong> Necessária para receber salário</li>
                    <li><strong>Visto de estudante válido:</strong> Com permissão de trabalho</li>
                </ul>

                <h5>💼 Tipos de Trabalho Disponíveis:</h5>
                <ul>
                    <li><strong>Hospitality:</strong> Restaurantes, cafés, hotéis</li>
                    <li><strong>Retail:</strong> Lojas, supermercados</li>
                    <li><strong>Tutoring:</strong> Aulas particulares de português/inglês</li>
                    <li><strong>Assistente de pesquisa:</strong> Projetos universitários</li>
                    <li><strong>Delivery:</strong> Wolt, Just Eat (muito popular)</li>
                </ul>

                <h5>🏢 Oportunidades por Setor:</h5>
                <ul>
                    <li><strong>Hospitality:</strong> DKK 130-150/hora</li>
                    <li><strong>Retail:</strong> DKK 120-140/hora</li>
                    <li><strong>Tutoring:</strong> DKK 200-400/hora</li>
                    <li><strong>Assistente de pesquisa:</strong> DKK 150-200/hora</li>
                    <li><strong>Delivery:</strong> DKK 100-150/hora + gorjetas</li>
                </ul>

                <h5>🎓 Pós-Graduação - Oportunidades:</h5>
                <ul>
                    <li><strong>Job Search Visa:</strong> 3 anos para procurar trabalho após graduação</li>
                    <li><strong>Green Card:</strong> Sistema de pontos para residência</li>
                    <li><strong>Fast Track:</strong> Processo acelerado para profissionais qualificados</li>
                    <li><strong>Startup Visa:</strong> Para empreendedores</li>
                </ul>

                <div class="highlight-box">
                    <h5>💰 Benefícios Financeiros:</h5>
                    <ul>
                        <li><strong>Salário mínimo:</strong> DKK 120-130/hora (um dos mais altos do mundo)</li>
                        <li><strong>Renda mensal:</strong> DKK 8.000-12.000 (20h/semana)</li>
                        <li><strong>Cobertura de custos:</strong> 60-80% das despesas de vida</li>
                        <li><strong>Benefícios:</strong> Férias pagas, seguro saúde gratuito</li>
                    </ul>
                </div>

                <p><strong>🎯 Dica:</strong> A Dinamarca tem escassez de mão de obra em muitos setores, oferecendo excelentes oportunidades para estudantes internacionais.</p>
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
