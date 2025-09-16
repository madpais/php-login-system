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
$resultado_visita = registrarVisitaPais($_SESSION['usuario_id'], 'australia');

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
    <title>Austrália - Guia Completo para Estudantes - DayDreaming</title>
    
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
            max-width: 500px;
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
                            <img src="../imagens/australia_flags_flag_16971.png" alt="Bandeira da Austrália" class="country-flag">
                            <h1 class="country-title">Austrália</h1>
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
                                <i class="fas fa-map-marker-alt me-1"></i>Austrália
                            </span>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navegação -->
    <?php include 'nav_paises.php'; ?>

    <!-- Imagem Hero -->
    <div class="container-fluid hero-image-container">
        <img src="../imagens/australia_home.png" alt="Austrália - Paisagem" class="hero-image">
    </div>

    <!-- Cards de Informações Básicas -->
    <div class="container my-5">
        <div class="row g-4">
            <!-- População -->
            <div class="col-md-4">
                <div class="info-card">
                    <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="População" class="icon">
                    <h3>27,2M</h3>
                    <span class="badge">População</span>
                    <p>Habitantes em todo o país</p>
                </div>
            </div>

            <!-- IDH -->
            <div class="col-md-4">
                <div class="info-card">
                    <img src="https://cdn-icons-png.flaticon.com/512/616/616492.png" alt="IDH" class="icon">
                    <h3>7º</h3>
                    <span class="badge">IDH Mundial</span>
                    <p>Índice de Desenvolvimento Humano</p>
                </div>
            </div>

            <!-- Moeda -->
            <div class="col-md-4">
                <div class="info-card">
                    <img src="https://cdn-icons-png.flaticon.com/512/2331/2331970.png" alt="Moeda" class="icon">
                    <h3>AUD</h3>
                    <span class="badge">Moeda Local</span>
                    <p>Dólar Australiano</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Título da Seção -->
    <div class="section-title">
        <h2>🇦🇺 Guia Completo: Estudar na Austrália</h2>
        <p>Tudo que você precisa saber para realizar seu sonho de estudar na terra dos cangurus</p>
    </div>

    <!-- Menu de Navegação por Abas -->
    <div class="container">
        <ul class="nav nav-tabs justify-content-center" id="australiaTab" role="tablist">
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
        <div class="tab-content" id="australiaTabContent">
            <!-- Idioma Oficial -->
            <div class="tab-pane fade show active" id="idioma" role="tabpanel">
                <h4><i class="fas fa-language me-2"></i>Idioma Oficial</h4>
                <p><strong>Inglês</strong> é o idioma oficial da Austrália, falado por praticamente toda a população. O inglês australiano tem algumas particularidades em termos de sotaque, vocabulário e expressões locais.</p>

                <div class="highlight-box">
                    <h5>🎯 Dicas para Brasileiros:</h5>
                    <ul>
                        <li><strong>Sotaque Australiano:</strong> É considerado um dos mais distintos do mundo, com entonação única</li>
                        <li><strong>Gírias Locais:</strong> "Mate" (amigo), "Arvo" (tarde), "Brekkie" (café da manhã)</li>
                        <li><strong>Preparação:</strong> Recomenda-se nível intermediário-avançado de inglês antes da viagem</li>
                        <li><strong>Testes Aceitos:</strong> IELTS (mais comum), TOEFL, PTE Academic</li>
                    </ul>
                </div>

                <h5>📚 Requisitos de Proficiência:</h5>
                <ul>
                    <li><strong>Graduação:</strong> IELTS 6.0-6.5 (mínimo 6.0 em cada habilidade)</li>
                    <li><strong>Pós-graduação:</strong> IELTS 6.5-7.0 (mínimo 6.0-6.5 em cada habilidade)</li>
                    <li><strong>Doutorado:</strong> IELTS 7.0+ (mínimo 6.5 em cada habilidade)</li>
                    <li><strong>Cursos de Inglês:</strong> Disponíveis para todos os níveis, desde iniciante</li>
                </ul>
            </div>

            <!-- Clima -->
            <div class="tab-pane fade" id="clima" role="tabpanel">
                <h4><i class="fas fa-sun me-2"></i>Clima Típico</h4>
                <p>A Austrália possui um clima diversificado devido ao seu tamanho continental. As estações são <strong>opostas ao Brasil</strong>, sendo o verão de dezembro a fevereiro e o inverno de junho a agosto.</p>

                <div class="row">
                    <div class="col-md-6">
                        <h5>🌡️ Regiões Climáticas:</h5>
                        <ul>
                            <li><strong>Norte (Darwin, Cairns):</strong> Tropical - quente e úmido o ano todo</li>
                            <li><strong>Sudeste (Sydney, Melbourne):</strong> Temperado - quatro estações bem definidas</li>
                            <li><strong>Sudoeste (Perth):</strong> Mediterrâneo - verões secos e invernos amenos</li>
                            <li><strong>Centro:</strong> Árido/semiárido - extremos de temperatura</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>📅 Estações do Ano:</h5>
                        <ul>
                            <li><strong>Verão (Dez-Fev):</strong> 20-30°C, época de férias universitárias</li>
                            <li><strong>Outono (Mar-Mai):</strong> 15-25°C, ideal para estudos</li>
                            <li><strong>Inverno (Jun-Ago):</strong> 5-20°C, período letivo principal</li>
                            <li><strong>Primavera (Set-Nov):</strong> 15-25°C, início do ano acadêmico</li>
                        </ul>
                    </div>
                </div>

                <div class="highlight-box">
                    <h5>🧳 Dicas de Vestuário:</h5>
                    <p>Leve roupas para todas as estações, especialmente se for estudar em cidades como Melbourne, conhecida por ter "quatro estações em um dia". Protetor solar é essencial devido à forte radiação UV.</p>
                </div>
            </div>

            <!-- Custo de Vida -->
            <div class="tab-pane fade" id="custos" role="tabpanel">
                <h4><i class="fas fa-dollar-sign me-2"></i>Custo de Vida</h4>
                <p>A Austrália tem um custo de vida relativamente alto, mas oferece excelente qualidade de vida. O governo exige que estudantes internacionais comprovem <strong>AU$ 21.041 por ano</strong> para despesas de subsistência.</p>

                <div class="row">
                    <div class="col-md-6">
                        <h5>🏠 Acomodação (por semana):</h5>
                        <ul>
                            <li><strong>Homestay:</strong> AU$ 200-300</li>
                            <li><strong>Apartamento compartilhado:</strong> AU$ 150-280</li>
                            <li><strong>Residência estudantil:</strong> AU$ 250-400</li>
                            <li><strong>Apartamento próprio:</strong> AU$ 300-600</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>🍽️ Alimentação (por semana):</h5>
                        <ul>
                            <li><strong>Supermercado:</strong> AU$ 80-120</li>
                            <li><strong>Restaurante barato:</strong> AU$ 15-25 por refeição</li>
                            <li><strong>Restaurante médio:</strong> AU$ 25-40 por refeição</li>
                            <li><strong>Fast food:</strong> AU$ 8-15 por refeição</li>
                        </ul>
                    </div>
                </div>

                <h5>🚌 Transporte:</h5>
                <ul>
                    <li><strong>Passe mensal de transporte público:</strong> AU$ 100-200</li>
                    <li><strong>Desconto estudantil:</strong> 30-50% em transporte público</li>
                    <li><strong>Bicicleta:</strong> Opção econômica e sustentável</li>
                    <li><strong>Uber/Táxi:</strong> AU$ 2-4 por km</li>
                </ul>

                <div class="highlight-box">
                    <h5>💡 Dicas para Economizar:</h5>
                    <ul>
                        <li>Cozinhe em casa - pode economizar até 60% em alimentação</li>
                        <li>Use cartão de estudante para descontos</li>
                        <li>Compre em mercados asiáticos - produtos mais baratos</li>
                        <li>Aproveite atividades gratuitas: praias, parques, museus</li>
                    </ul>
                </div>
            </div>

            <!-- Bolsas de Estudo -->
            <div class="tab-pane fade" id="bolsas" role="tabpanel">
                <h4><i class="fas fa-graduation-cap me-2"></i>Bolsas de Estudo</h4>
                <p>A Austrália oferece diversas oportunidades de bolsas para estudantes internacionais, desde bolsas governamentais até programas específicos de universidades.</p>

                <h5>🏛️ Bolsas Governamentais:</h5>
                <ul>
                    <li><strong>Australia Awards Scholarships:</strong> Bolsa integral para pós-graduação, incluindo passagem, mensalidade e subsistência</li>
                    <li><strong>Research Training Program (RTP):</strong> Para estudantes de doutorado e mestrado por pesquisa</li>
                    <li><strong>Endeavour Scholarships:</strong> Para estudos de curta duração e pesquisa</li>
                </ul>

                <h5>🎓 Bolsas Universitárias:</h5>
                <ul>
                    <li><strong>University of Sydney:</strong> Sydney International Student Award (até AU$ 40.000)</li>
                    <li><strong>University of Melbourne:</strong> Melbourne International Undergraduate Scholarship</li>
                    <li><strong>Monash University:</strong> Monash International Merit Scholarship (AU$ 10.000)</li>
                    <li><strong>ANU:</strong> ANU Chancellor's International Scholarship (25-100% das taxas)</li>
                    <li><strong>UNSW:</strong> UNSW International Scholarships (até AU$ 20.000)</li>
                </ul>

                <div class="highlight-box">
                    <h5>📋 Requisitos Gerais:</h5>
                    <ul>
                        <li>Excelência acadêmica (notas altas)</li>
                        <li>Proficiência em inglês comprovada</li>
                        <li>Carta de motivação convincente</li>
                        <li>Cartas de recomendação</li>
                        <li>Experiência em pesquisa (para pós-graduação)</li>
                    </ul>
                </div>

                <p><strong>💰 Valores típicos:</strong> Bolsas parciais variam de AU$ 5.000 a AU$ 20.000 por ano. Bolsas integrais podem cobrir 100% das taxas + subsistência (AU$ 28.000-35.000/ano).</p>
            </div>

            <!-- Universidades -->
            <div class="tab-pane fade" id="universidades" role="tabpanel">
                <h4><i class="fas fa-university me-2"></i>Principais Universidades</h4>
                <p>A Austrália possui algumas das melhores universidades do mundo, com 7 instituições no top 100 global. O <strong>Group of Eight (Go8)</strong> representa as universidades de pesquisa mais prestigiosas.</p>

                <h5>🏆 Group of Eight (Go8):</h5>
                <ul>
                    <li><strong>Australian National University (ANU):</strong> #30 mundial, forte em política e relações internacionais
                        <br><a href="https://www.anu.edu.au/study/international-students" target="_blank" class="btn-custom">🔗 Informações Internacionais</a>
                    </li>
                    <li><strong>University of Melbourne:</strong> #33 mundial, excelente em medicina e educação
                        <br><a href="https://study.unimelb.edu.au/" target="_blank" class="btn-custom">🔗 Portal Internacional</a>
                    </li>
                    <li><strong>University of Sydney:</strong> #41 mundial, tradicional e prestigiosa
                        <br><a href="https://www.sydney.edu.au/study/international-students.html" target="_blank" class="btn-custom">🔗 Estudantes Internacionais</a>
                    </li>
                    <li><strong>University of New South Wales (UNSW):</strong> #43 mundial, líder em engenharia e tecnologia
                        <br><a href="https://www.unsw.edu.au/study/international-students" target="_blank" class="btn-custom">🔗 UNSW Internacional</a>
                    </li>
                    <li><strong>University of Queensland (UQ):</strong> #50 mundial, forte em ciências da vida
                        <br><a href="https://future-students.uq.edu.au/international" target="_blank" class="btn-custom">🔗 UQ Internacional</a>
                    </li>
                    <li><strong>Monash University:</strong> #57 mundial, maior universidade da Austrália
                        <br><a href="https://www.monash.edu/study/international-students" target="_blank" class="btn-custom">🔗 Monash Internacional</a>
                    </li>
                    <li><strong>University of Western Australia (UWA):</strong> #90 mundial, bela campus em Perth
                        <br><a href="https://www.uwa.edu.au/study/international-students" target="_blank" class="btn-custom">🔗 UWA Internacional</a>
                    </li>
                    <li><strong>University of Adelaide:</strong> #109 mundial, tradição em pesquisa
                        <br><a href="https://www.adelaide.edu.au/study/international-students" target="_blank" class="btn-custom">🔗 Adelaide Internacional</a>
                    </li>
                </ul>

                <h5>🌟 Outras Universidades de Destaque:</h5>
                <ul>
                    <li><strong>University of Technology Sydney (UTS):</strong> Inovação e tecnologia</li>
                    <li><strong>RMIT University:</strong> Design, arte e tecnologia</li>
                    <li><strong>Griffith University:</strong> Sustentabilidade e meio ambiente</li>
                    <li><strong>Macquarie University:</strong> Negócios e linguística</li>
                </ul>

                <div class="highlight-box">
                    <h5>📊 Dados sobre Intercambistas:</h5>
                    <ul>
                        <li>Mais de <strong>700.000 estudantes internacionais</strong> na Austrália</li>
                        <li><strong>Brasil</strong> está entre os top 15 países de origem</li>
                        <li>Cerca de <strong>8.000 brasileiros</strong> estudam na Austrália anualmente</li>
                        <li>Áreas mais populares: Negócios, Engenharia, TI, Saúde</li>
                    </ul>
                </div>
            </div>

            <!-- Comunidade Brasileira -->
            <div class="tab-pane fade" id="comunidade-br" role="tabpanel">
                <h4><i class="fas fa-flag me-2"></i>Comunidade de Brasileiros</h4>
                <p>A Austrália possui uma <strong>comunidade brasileira vibrante</strong> de aproximadamente 60.000 pessoas, concentrada principalmente em Sydney, Melbourne, Brisbane e Perth.</p>

                <h5>🏙️ Principais Cidades:</h5>
                <ul>
                    <li><strong>Sydney:</strong> Maior comunidade brasileira (~25.000), bairros como Bondi e Manly</li>
                    <li><strong>Melbourne:</strong> Segunda maior comunidade (~15.000), vida cultural intensa</li>
                    <li><strong>Brisbane:</strong> Comunidade crescente (~8.000), clima mais tropical</li>
                    <li><strong>Perth:</strong> Comunidade menor mas unida (~5.000)</li>
                </ul>

                <h5>🤝 Organizações e Grupos:</h5>
                <ul>
                    <li><strong>Câmara de Comércio Brasil-Austrália:</strong> Networking profissional</li>
                    <li><strong>Brazilian Community Council of Australia:</strong> Representação oficial</li>
                    <li><strong>Grupos no Facebook:</strong> "Brasileiros na Austrália", "Brasileiros em Sydney"</li>
                    <li><strong>Associações Estudantis:</strong> Grupos brasileiros em cada universidade</li>
                </ul>

                <h5>🎉 Eventos e Festivais:</h5>
                <ul>
                    <li><strong>Festival de Inverno de Bondi:</strong> Celebração da cultura brasileira</li>
                    <li><strong>Carnaval Australiano:</strong> Eventos em várias cidades</li>
                    <li><strong>Festa Junina:</strong> Celebrações tradicionais</li>
                    <li><strong>Copa do Mundo:</strong> Grandes encontros para assistir jogos</li>
                </ul>

                <div class="highlight-box">
                    <h5>📱 Recursos Úteis:</h5>
                    <ul>
                        <li><strong>Apps:</strong> "Brasileiros na Austrália", "Aussie Brasil"</li>
                        <li><strong>Rádios:</strong> Rádio Austral, programas em português</li>
                        <li><strong>Restaurantes:</strong> Mais de 200 restaurantes brasileiros</li>
                        <li><strong>Mercados:</strong> Produtos brasileiros disponíveis</li>
                    </ul>
                </div>
            </div>

            <!-- Cultura e Costumes -->
            <div class="tab-pane fade" id="cultura" role="tabpanel">
                <h4><i class="fas fa-theater-masks me-2"></i>Cultura e Costumes Locais</h4>
                <p>A cultura australiana é conhecida pela <strong>informalidade, multiculturalismo e amor pela natureza</strong>. Os australianos valorizam o "fair go" (oportunidade justa para todos) e têm um estilo de vida descontraído.</p>

                <h5>🤝 Características Culturais:</h5>
                <ul>
                    <li><strong>Informalidade:</strong> Tratamento casual, uso de primeiros nomes</li>
                    <li><strong>Pontualidade:</strong> Ser pontual é muito valorizado</li>
                    <li><strong>Multiculturalismo:</strong> Sociedade diversa e inclusiva</li>
                    <li><strong>Vida ao ar livre:</strong> BBQs, praias, esportes</li>
                    <li><strong>Senso de humor:</strong> Ironia e auto-depreciação</li>
                </ul>

                <h5>🏖️ Estilo de Vida:</h5>
                <ul>
                    <li><strong>Work-life balance:</strong> Equilíbrio entre trabalho e vida pessoal</li>
                    <li><strong>Café culture:</strong> Cultura do café muito forte</li>
                    <li><strong>Esportes:</strong> AFL, Rugby, Cricket, Surf</li>
                    <li><strong>Natureza:</strong> Respeito e proteção ao meio ambiente</li>
                </ul>

                <h5>🍽️ Culinária:</h5>
                <ul>
                    <li><strong>Fusion cuisine:</strong> Mistura de influências asiáticas e europeias</li>
                    <li><strong>Seafood:</strong> Frutos do mar frescos</li>
                    <li><strong>BBQ:</strong> Churrascos são tradição</li>
                    <li><strong>Café:</strong> Cultura do café artesanal</li>
                </ul>

                <div class="highlight-box">
                    <h5>⚠️ Dicas Importantes:</h5>
                    <ul>
                        <li>Não toque na cabeça das pessoas (considerado rude)</li>
                        <li>Sempre agradeça e seja educado</li>
                        <li>Respeite as regras de trânsito e meio ambiente</li>
                        <li>Aprenda sobre a cultura aborígene (muito respeitada)</li>
                    </ul>
                </div>
            </div>

            <!-- Calendário Acadêmico -->
            <div class="tab-pane fade" id="calendario" role="tabpanel">
                <h4><i class="fas fa-calendar-alt me-2"></i>Calendário Acadêmico</h4>
                <p>O ano acadêmico australiano é dividido em <strong>dois semestres</strong>, com início em fevereiro/março e julho/agosto. Algumas universidades também oferecem trimestres.</p>

                <div class="row">
                    <div class="col-md-6">
                        <h5>📅 Semestre 1:</h5>
                        <ul>
                            <li><strong>Início:</strong> Final de fevereiro/início de março</li>
                            <li><strong>Término:</strong> Final de junho</li>
                            <li><strong>Férias:</strong> Julho (2 semanas)</li>
                            <li><strong>Exames:</strong> Maio/junho</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>📅 Semestre 2:</h5>
                        <ul>
                            <li><strong>Início:</strong> Final de julho/início de agosto</li>
                            <li><strong>Término:</strong> Final de novembro</li>
                            <li><strong>Férias:</strong> Dezembro-fevereiro (verão)</li>
                            <li><strong>Exames:</strong> Outubro/novembro</li>
                        </ul>
                    </div>
                </div>

                <h5>🎓 Períodos de Aplicação:</h5>
                <ul>
                    <li><strong>Semestre 1:</strong> Aplicações até outubro do ano anterior</li>
                    <li><strong>Semestre 2:</strong> Aplicações até março/abril</li>
                    <li><strong>Bolsas:</strong> Aplicações geralmente 6-12 meses antes</li>
                    <li><strong>Visto:</strong> Aplicar 2-3 meses antes do início</li>
                </ul>

                <div class="highlight-box">
                    <h5>⏰ Cronograma Recomendado:</h5>
                    <ul>
                        <li><strong>12 meses antes:</strong> Pesquisar universidades e cursos</li>
                        <li><strong>10 meses antes:</strong> Fazer testes de inglês</li>
                        <li><strong>8 meses antes:</strong> Aplicar para universidades e bolsas</li>
                        <li><strong>6 meses antes:</strong> Receber ofertas e aceitar</li>
                        <li><strong>3 meses antes:</strong> Aplicar para visto</li>
                        <li><strong>1 mês antes:</strong> Organizar acomodação e viagem</li>
                    </ul>
                </div>
            </div>

            <!-- Guia para Bolsas -->
            <div class="tab-pane fade" id="guia" role="tabpanel">
                <h4><i class="fas fa-map me-2"></i>Guia para Conseguir Bolsas</h4>
                <p>Conseguir uma bolsa na Austrália requer <strong>planejamento, excelência acadêmica e preparação cuidadosa</strong>. Aqui está um guia completo:</p>

                <h5>📚 O que Estudar:</h5>
                <ul>
                    <li><strong>Inglês Acadêmico:</strong> IELTS/TOEFL - foque em writing e speaking</li>
                    <li><strong>Área de Interesse:</strong> Mantenha notas altas na graduação (GPA 3.5+)</li>
                    <li><strong>Pesquisa:</strong> Participe de projetos de iniciação científica</li>
                    <li><strong>Publicações:</strong> Artigos, apresentações em congressos</li>
                </ul>

                <h5>📋 Documentação Necessária:</h5>
                <ul>
                    <li><strong>Histórico Acadêmico:</strong> Traduzido e certificado</li>
                    <li><strong>Diploma:</strong> Traduzido e certificado</li>
                    <li><strong>Teste de Inglês:</strong> IELTS/TOEFL válido</li>
                    <li><strong>CV Acadêmico:</strong> Formato internacional</li>
                    <li><strong>Carta de Motivação:</strong> Personal Statement</li>
                    <li><strong>Cartas de Recomendação:</strong> 2-3 professores/supervisores</li>
                    <li><strong>Proposta de Pesquisa:</strong> Para pós-graduação</li>
                </ul>

                <h5>✍️ Como Fazer CV Acadêmico:</h5>
                <ul>
                    <li><strong>Formato:</strong> Cronológico reverso, máximo 2-3 páginas</li>
                    <li><strong>Seções:</strong> Dados pessoais, educação, experiência, publicações, prêmios</li>
                    <li><strong>Destaque:</strong> Realizações quantificáveis e relevantes</li>
                    <li><strong>Linguagem:</strong> Formal, clara e concisa</li>
                </ul>

                <h5>💌 Carta de Motivação:</h5>
                <ul>
                    <li><strong>Estrutura:</strong> Introdução, motivação, objetivos, conclusão</li>
                    <li><strong>Conteúdo:</strong> Por que este curso/universidade/país</li>
                    <li><strong>Personalização:</strong> Específica para cada aplicação</li>
                    <li><strong>Tamanho:</strong> 1-2 páginas, máximo 1000 palavras</li>
                </ul>

                <div class="highlight-box">
                    <h5>🎯 Preparação para Entrevistas:</h5>
                    <ul>
                        <li><strong>Pesquise:</strong> Universidade, curso, professores</li>
                        <li><strong>Pratique:</strong> Perguntas comuns em inglês</li>
                        <li><strong>Prepare:</strong> Exemplos de experiências relevantes</li>
                        <li><strong>Seja:</strong> Autêntico, entusiasmado e profissional</li>
                    </ul>
                </div>

                <p><strong>🔗 Links Úteis:</strong></p>
                <a href="https://www.studyaustralia.gov.au/english/scholarships" target="_blank" class="btn-custom">🎓 Study Australia - Bolsas</a>
                <a href="https://www.australiaawards.gov.au/" target="_blank" class="btn-custom">🏆 Australia Awards</a>
            </div>

            <!-- Trabalho com Visto de Estudante -->
            <div class="tab-pane fade" id="trabalho" role="tabpanel">
                <h4><i class="fas fa-briefcase me-2"></i>Trabalho com Visto de Estudante</h4>
                <p><strong>Sim!</strong> Estudantes internacionais na Austrália podem trabalhar com o visto de estudante (subclass 500), mas há <strong>limitações importantes</strong>.</p>

                <h5>⏰ Limitações de Horas:</h5>
                <ul>
                    <li><strong>Durante o período letivo:</strong> Máximo 48 horas por quinzena (24h/semana)</li>
                    <li><strong>Durante as férias:</strong> Horas ilimitadas</li>
                    <li><strong>Antes do início das aulas:</strong> Não pode trabalhar</li>
                    <li><strong>Mestrado por pesquisa/Doutorado:</strong> Horas ilimitadas</li>
                </ul>

                <h5>💼 Tipos de Trabalho Permitidos:</h5>
                <ul>
                    <li><strong>Trabalho remunerado:</strong> Qualquer emprego legal</li>
                    <li><strong>Trabalho voluntário:</strong> Sem limitação de horas</li>
                    <li><strong>Estágio não remunerado:</strong> Parte do curso, sem limitação</li>
                    <li><strong>Trabalho no campus:</strong> Universidade como empregador</li>
                </ul>

                <h5>🏢 Oportunidades Comuns:</h5>
                <ul>
                    <li><strong>Hospitality:</strong> Restaurantes, cafés, hotéis (AU$ 20-25/h)</li>
                    <li><strong>Retail:</strong> Lojas, supermercados (AU$ 18-22/h)</li>
                    <li><strong>Tutoring:</strong> Aulas particulares (AU$ 25-40/h)</li>
                    <li><strong>Campus jobs:</strong> Biblioteca, laboratórios (AU$ 22-28/h)</li>
                    <li><strong>Delivery:</strong> Uber Eats, DoorDash (AU$ 15-25/h)</li>
                </ul>

                <h5>📄 Documentos Necessários:</h5>
                <ul>
                    <li><strong>Tax File Number (TFN):</strong> Obrigatório para trabalhar</li>
                    <li><strong>ABN:</strong> Para trabalho freelance</li>
                    <li><strong>Superannuation:</strong> Fundo de aposentadoria</li>
                    <li><strong>Bank Account:</strong> Conta bancária australiana</li>
                </ul>

                <div class="highlight-box">
                    <h5>💰 Benefícios Financeiros:</h5>
                    <ul>
                        <li><strong>Salário mínimo:</strong> AU$ 21.38/hora (2023)</li>
                        <li><strong>Renda mensal:</strong> AU$ 2.000-2.500 (48h/quinzena)</li>
                        <li><strong>Cobertura de custos:</strong> 60-80% das despesas de vida</li>
                        <li><strong>Experiência:</strong> Networking e habilidades profissionais</li>
                    </ul>
                </div>

                <p><strong>⚠️ Importante:</strong> Trabalhar mais que o permitido pode resultar no cancelamento do visto. Sempre monitore suas horas e mantenha registros.</p>
            </div>
        </div>
    </div>

   

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Função para scroll suave para seções
        function scrollToSection(sectionId) {
            // Para futuras implementações de seções específicas
            alert('Funcionalidade em desenvolvimento!');
        }
    </script>
<?php require_once 'footer_paises.php'; ?>
</body>
</html>
