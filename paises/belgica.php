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
$resultado_visita = registrarVisitaPais($_SESSION['usuario_id'], 'belgica');

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
    <title>Bélgica - Guia Completo para Estudantes - DayDreaming</title>
    
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
                            <img src="../imagens/belgium_flags_flag_16976.png" alt="Bandeira da Bélgica" class="country-flag">
                            <h1 class="country-title">Bélgica</h1>
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
                                <i class="fas fa-map-marker-alt me-1"></i>Bélgica
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
        <img src="../imagens/belgica_home.png" alt="Bélgica - Paisagem" class="hero-image">
    </div>

    <!-- Cards de Informações Básicas -->
    <div class="container my-5">
        <div class="row g-4">
            <!-- População -->
            <div class="col-md-4">
                <div class="info-card">
                    <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="População" class="icon">
                    <h3>11,6M</h3>
                    <span class="badge">População</span>
                    <p>Habitantes em todo o país</p>
                </div>
            </div>

            <!-- IDH -->
            <div class="col-md-4">
                <div class="info-card">
                    <img src="https://cdn-icons-png.flaticon.com/512/616/616492.png" alt="IDH" class="icon">
                    <h3>13º</h3>
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
        <h2>🇧🇪 Guia Completo: Estudar na Bélgica</h2>
        <p>Descubra o coração da Europa e suas oportunidades educacionais excepcionais</p>
    </div>

    <!-- Menu de Navegação por Abas -->
    <div class="container">
        <ul class="nav nav-tabs justify-content-center" id="belgicaTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="idioma-tab" data-bs-toggle="tab" data-bs-target="#idioma" type="button" role="tab">
                    🗣️ Idiomas
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
        <div class="tab-content" id="belgicaTabContent">
            <!-- Idiomas Oficiais -->
            <div class="tab-pane fade show active" id="idioma" role="tabpanel">
                <h4><i class="fas fa-language me-2"></i>Idiomas Oficiais</h4>
                <p>A Bélgica possui <strong>três idiomas oficiais</strong>: Holandês (Flamengo), Francês e Alemão. A escolha do idioma de estudo depende da região e universidade.</p>

                <div class="row">
                    <div class="col-md-4">
                        <h5>🇳🇱 Holandês (Flamengo)</h5>
                        <ul>
                            <li><strong>Região:</strong> Flandres (Norte)</li>
                            <li><strong>População:</strong> 60% dos belgas</li>
                            <li><strong>Universidades:</strong> KU Leuven, Ghent University</li>
                            <li><strong>Teste:</strong> CNaVT (Certificate Dutch as a Foreign Language)</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h5>🇫🇷 Francês</h5>
                        <ul>
                            <li><strong>Região:</strong> Valônia (Sul)</li>
                            <li><strong>População:</strong> 40% dos belgas</li>
                            <li><strong>Universidades:</strong> UCLouvain, ULB</li>
                            <li><strong>Teste:</strong> DELF/DALF, TCF</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h5>🇩🇪 Alemão</h5>
                        <ul>
                            <li><strong>Região:</strong> Comunidade Alemã (Leste)</li>
                            <li><strong>População:</strong> 1% dos belgas</li>
                            <li><strong>Universidades:</strong> Limitadas</li>
                            <li><strong>Teste:</strong> TestDaF, DSH</li>
                        </ul>
                    </div>
                </div>

                <div class="highlight-box">
                    <h5>🎯 Dicas para Brasileiros:</h5>
                    <ul>
                        <li><strong>Inglês:</strong> Muitos programas de mestrado e doutorado são oferecidos em inglês</li>
                        <li><strong>Preparação:</strong> Escolha o idioma baseado na região/universidade de interesse</li>
                        <li><strong>Cursos de idioma:</strong> Disponíveis nas próprias universidades</li>
                        <li><strong>Nível exigido:</strong> B2-C1 para graduação, C1-C2 para pós-graduação</li>
                    </ul>
                </div>

                <h5>📚 Programas em Inglês:</h5>
                <ul>
                    <li><strong>Graduação:</strong> Limitados, principalmente em negócios e engenharia</li>
                    <li><strong>Mestrado:</strong> Ampla variedade em todas as áreas</li>
                    <li><strong>Doutorado:</strong> Maioria disponível em inglês</li>
                    <li><strong>Requisito:</strong> IELTS 6.5+ ou TOEFL 90+</li>
                </ul>
            </div>

            <!-- Clima -->
            <div class="tab-pane fade" id="clima" role="tabpanel">
                <h4><i class="fas fa-cloud-rain me-2"></i>Clima Típico</h4>
                <p>A Bélgica possui um <strong>clima oceânico temperado</strong>, caracterizado por invernos amenos e verões frescos, com chuva distribuída ao longo do ano.</p>

                <div class="row">
                    <div class="col-md-6">
                        <h5>🌡️ Temperaturas Médias:</h5>
                        <ul>
                            <li><strong>Inverno (Dez-Fev):</strong> 2-7°C, período mais chuvoso</li>
                            <li><strong>Primavera (Mar-Mai):</strong> 8-18°C, ideal para estudos</li>
                            <li><strong>Verão (Jun-Ago):</strong> 15-23°C, período mais seco</li>
                            <li><strong>Outono (Set-Nov):</strong> 10-15°C, início do ano acadêmico</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>☔ Características Climáticas:</h5>
                        <ul>
                            <li><strong>Precipitação:</strong> 800-900mm anuais</li>
                            <li><strong>Dias chuvosos:</strong> 200+ por ano</li>
                            <li><strong>Umidade:</strong> Alta (75-85%)</li>
                            <li><strong>Vento:</strong> Frequente, especialmente no litoral</li>
                        </ul>
                    </div>
                </div>

                <div class="highlight-box">
                    <h5>🧳 Dicas de Vestuário:</h5>
                    <ul>
                        <li><strong>Essencial:</strong> Guarda-chuva e casaco impermeável</li>
                        <li><strong>Inverno:</strong> Roupas em camadas, casaco quente</li>
                        <li><strong>Verão:</strong> Roupas leves + casaco para a noite</li>
                        <li><strong>Calçados:</strong> Sapatos impermeáveis são fundamentais</li>
                    </ul>
                </div>

                <h5>🌍 Comparação com o Brasil:</h5>
                <ul>
                    <li><strong>Temperatura:</strong> Muito mais fria que a maioria do Brasil</li>
                    <li><strong>Chuva:</strong> Mais frequente e distribuída ao longo do ano</li>
                    <li><strong>Luz solar:</strong> Menos horas de sol, especialmente no inverno</li>
                    <li><strong>Adaptação:</strong> Período de 2-3 meses para acostumação</li>
                </ul>
            </div>

            <!-- Custo de Vida -->
            <div class="tab-pane fade" id="custos" role="tabpanel">
                <h4><i class="fas fa-euro-sign me-2"></i>Custo de Vida</h4>
                <p>A Bélgica tem um custo de vida <strong>moderado a alto</strong> comparado a outros países europeus. Bruxelas e Antuérpia são as cidades mais caras.</p>

                <div class="row">
                    <div class="col-md-6">
                        <h5>🏠 Acomodação (por mês):</h5>
                        <ul>
                            <li><strong>Quarto em casa de família:</strong> €400-600</li>
                            <li><strong>Quarto compartilhado:</strong> €300-500</li>
                            <li><strong>Residência estudantil:</strong> €350-550</li>
                            <li><strong>Apartamento próprio:</strong> €600-1200</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>🍽️ Alimentação (por mês):</h5>
                        <ul>
                            <li><strong>Supermercado:</strong> €200-300</li>
                            <li><strong>Restaurante universitário:</strong> €3-6 por refeição</li>
                            <li><strong>Restaurante médio:</strong> €15-25 por refeição</li>
                            <li><strong>Fast food:</strong> €8-12 por refeição</li>
                        </ul>
                    </div>
                </div>

                <h5>🚌 Transporte:</h5>
                <ul>
                    <li><strong>Passe mensal estudantil:</strong> €12-25 (desconto significativo)</li>
                    <li><strong>Bicicleta:</strong> Meio de transporte mais popular e econômico</li>
                    <li><strong>Trem nacional:</strong> Gratuito para estudantes menores de 26 anos nos fins de semana</li>
                    <li><strong>Transporte urbano:</strong> €2-3 por viagem</li>
                </ul>

                <h5>📚 Outros Custos:</h5>
                <ul>
                    <li><strong>Livros e materiais:</strong> €200-400 por ano</li>
                    <li><strong>Seguro saúde:</strong> €100-200 por ano</li>
                    <li><strong>Atividades sociais:</strong> €100-200 por mês</li>
                    <li><strong>Telefone/Internet:</strong> €20-40 por mês</li>
                </ul>

                <div class="highlight-box">
                    <h5>💡 Dicas para Economizar:</h5>
                    <ul>
                        <li>Use a bicicleta - transporte gratuito e saudável</li>
                        <li>Aproveite os descontos estudantis (cinema, museus, transporte)</li>
                        <li>Cozinhe em casa - supermercados têm preços razoáveis</li>
                        <li>Compre livros usados ou use bibliotecas universitárias</li>
                    </ul>
                </div>

                <p><strong>💰 Orçamento mensal total:</strong> €800-1400 (dependendo da cidade e estilo de vida)</p>
            </div>

            <!-- Bolsas de Estudo -->
            <div class="tab-pane fade" id="bolsas" role="tabpanel">
                <h4><i class="fas fa-graduation-cap me-2"></i>Bolsas de Estudo</h4>
                <p>A Bélgica oferece diversas oportunidades de bolsas, desde programas governamentais até bolsas específicas de universidades e organizações internacionais.</p>

                <h5>🏛️ Bolsas Governamentais:</h5>
                <ul>
                    <li><strong>ARES (Académie de Recherche et d'Enseignement Supérieur):</strong> Bolsas para pós-graduação em universidades francófonas</li>
                    <li><strong>VLIR-UOS:</strong> Bolsas para mestrado e doutorado em universidades flamengas</li>
                    <li><strong>WBI (Wallonie-Bruxelles International):</strong> Bolsas de excelência para estudantes internacionais</li>
                    <li><strong>FWO (Research Foundation Flanders):</strong> Bolsas de pesquisa para doutorado</li>
                </ul>

                <h5>🎓 Bolsas Universitárias:</h5>
                <ul>
                    <li><strong>KU Leuven:</strong> KU Leuven Scholarship Programme (€8.000-10.000/ano)</li>
                    <li><strong>Ghent University:</strong> Master Mind Scholarships (€8.000/ano)</li>
                    <li><strong>UCLouvain:</strong> Bourses d'excellence (cobertura parcial ou total)</li>
                    <li><strong>ULB:</strong> Excellence Scholarships (€5.000-10.000/ano)</li>
                    <li><strong>VUB:</strong> VUB Scholarships (€5.000/ano)</li>
                </ul>

                <h5>🌍 Programas Internacionais:</h5>
                <ul>
                    <li><strong>Erasmus Mundus:</strong> Bolsas integrais para programas conjuntos europeus</li>
                    <li><strong>Marie Curie Fellowships:</strong> Para pesquisadores de doutorado e pós-doutorado</li>
                    <li><strong>Fulbright Belgium:</strong> Para cidadãos americanos</li>
                    <li><strong>CAPES/CNPq:</strong> Bolsas brasileiras para estudos no exterior</li>
                </ul>

                <div class="highlight-box">
                    <h5>📋 Requisitos Gerais:</h5>
                    <ul>
                        <li>Excelência acadêmica (média 8.0+ ou equivalente)</li>
                        <li>Proficiência no idioma de instrução</li>
                        <li>Carta de motivação bem estruturada</li>
                        <li>Cartas de recomendação acadêmica</li>
                        <li>Projeto de pesquisa (para pós-graduação)</li>
                    </ul>
                </div>

                <p><strong>💰 Valores típicos:</strong> Bolsas parciais variam de €3.000 a €10.000 por ano. Bolsas integrais podem cobrir 100% das taxas + €800-1200/mês para subsistência.</p>
            </div>

            <!-- Universidades -->
            <div class="tab-pane fade" id="universidades" role="tabpanel">
                <h4><i class="fas fa-university me-2"></i>Principais Universidades</h4>
                <p>A Bélgica possui <strong>universidades de excelência mundial</strong> com forte tradição em pesquisa e inovação.</p>

                <h5>🏛️ Universidades de Destaque:</h5>
                <ul>
                    <li><strong>KU Leuven:</strong> #42 mundial (QS 2024)
                        <br><a href="https://www.kuleuven.be/" target="_blank" class="btn-custom">🔗 KU Leuven</a>
                    </li>
                    <li><strong>Ghent University:</strong> #143 mundial (QS 2024)
                        <br><a href="https://www.ugent.be/" target="_blank" class="btn-custom">🔗 Ghent University</a>
                    </li>
                    <li><strong>UCLouvain:</strong> #171 mundial (QS 2024)
                        <br><a href="https://uclouvain.be/" target="_blank" class="btn-custom">🔗 UCLouvain</a>
                    </li>
                    <li><strong>ULB (Université Libre de Bruxelles):</strong> #189 mundial (QS 2024)
                        <br><a href="https://www.ulb.be/" target="_blank" class="btn-custom">🔗 ULB</a>
                    </li>
                </ul>

                <h5>📊 Estatísticas:</h5>
                <ul>
                    <li><strong>Estudantes internacionais:</strong> 15% do total</li>
                    <li><strong>Programas em inglês:</strong> 200+ programas de mestrado</li>
                    <li><strong>Taxa de matrícula:</strong> €835-4.175/ano (não-UE)</li>
                    <li><strong>Requisito de idioma:</strong> B2-C1 no idioma de instrução</li>
                </ul>

                <div class="highlight-box">
                    <h5>🎯 Dicas para Aplicação:</h5>
                    <ul>
                        <li><strong>Prazo:</strong> Aplicações até 1º de fevereiro (setembro)</li>
                        <li><strong>Documentos:</strong> Histórico, diploma, carta de motivação</li>
                        <li><strong>Idioma:</strong> Certificado oficial de proficiência</li>
                        <li><strong>Visto:</strong> Processo pode levar 2-3 meses</li>
                    </ul>
                </div>
            </div>

            <!-- Comunidade Brasileira -->
            <div class="tab-pane fade" id="comunidade-br" role="tabpanel">
                <h4><i class="fas fa-flag me-2"></i>Comunidade de Brasileiros</h4>
                <p>A Bélgica possui uma <strong>comunidade brasileira crescente</strong>, estimada em 15.000-20.000 pessoas, concentrada principalmente nas grandes cidades.</p>

                <div class="row">
                    <div class="col-md-6">
                        <h5>🏙️ Principais Cidades:</h5>
                        <ul>
                            <li><strong>Bruxelas:</strong> Maior concentração, sede da UE</li>
                            <li><strong>Antuérpia:</strong> Centro comercial e portuário</li>
                            <li><strong>Liège:</strong> Região francófona</li>
                            <li><strong>Ghent:</strong> Cidade universitária</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>🤝 Organizações e Grupos:</h5>
                        <ul>
                            <li><strong>Embaixada do Brasil:</strong> Bruxelas</li>
                            <li><strong>Consulado Geral:</strong> Antuérpia</li>
                            <li><strong>Associações culturais:</strong> Grupos no Facebook</li>
                            <li><strong>Eventos:</strong> Festa Junina, Carnaval</li>
                        </ul>
                    </div>
                </div>

                <h5>🍽️ Vida Cultural Brasileira:</h5>
                <ul>
                    <li><strong>Restaurantes:</strong> Vários estabelecimentos brasileiros</li>
                    <li><strong>Produtos:</strong> Lojas especializadas em produtos brasileiros</li>
                    <li><strong>Eventos:</strong> Festivais e encontros regulares</li>
                    <li><strong>Redes sociais:</strong> Grupos ativos no Facebook e WhatsApp</li>
                </ul>

                <div class="highlight-box">
                    <h5>💡 Dicas de Integração:</h5>
                    <ul>
                        <li>Participe de eventos da comunidade brasileira</li>
                        <li>Use grupos no Facebook para networking</li>
                        <li>Visite a Embaixada para documentação</li>
                        <li>Explore restaurantes brasileiros para sentir-se em casa</li>
                    </ul>
                </div>
            </div>

            <!-- Cultura -->
            <div class="tab-pane fade" id="cultura" role="tabpanel">
                <h4><i class="fas fa-palette me-2"></i>Cultura e Tradições</h4>
                <p>A Bélgica possui uma <strong>cultura rica e diversificada</strong>, resultado da fusão de três comunidades linguísticas e uma história milenar.</p>

                <div class="row">
                    <div class="col-md-6">
                        <h5>🎨 Arte e Arquitetura:</h5>
                        <ul>
                            <li><strong>Arquitetura medieval:</strong> Bruges, Ghent, Antuérpia</li>
                            <li><strong>Art Nouveau:</strong> Victor Horta em Bruxelas</li>
                            <li><strong>Quadrinhos:</strong> Tintim, Smurfs, Lucky Luke</li>
                            <li><strong>Museus:</strong> Museus de arte renomados</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>🍫 Gastronomia:</h5>
                        <ul>
                            <li><strong>Chocolates:</strong> Godiva, Neuhaus, Leonidas</li>
                            <li><strong>Cervejas:</strong> 1.500+ tipos artesanais</li>
                            <li><strong>Waffles:</strong> Liège e Bruxelas</li>
                            <li><strong>Batatas fritas:</strong> Origem belga</li>
                        </ul>
                    </div>
                </div>

                <h5>🎭 Festivais e Eventos:</h5>
                <ul>
                    <li><strong>Tomorrowland:</strong> Maior festival de música eletrônica</li>
                    <li><strong>Carnaval de Binche:</strong> Patrimônio da UNESCO</li>
                    <li><strong>Festival de Bruges:</strong> Música clássica</li>
                    <li><strong>Mercados de Natal:</strong> Tradição centenária</li>
                </ul>

                <h5>🏛️ Patrimônio Mundial UNESCO:</h5>
                <ul>
                    <li><strong>Centro histórico de Bruges</strong></li>
                    <li><strong>Grande Place de Bruxelas</strong></li>
                    <li><strong>Casas de Victor Horta</strong></li>
                    <li><strong>Mina de carvão de Bois-du-Luc</strong></li>
                </ul>

                <div class="highlight-box">
                    <h5>🎯 Dicas Culturais:</h5>
                    <ul>
                        <li>Visite os centros históricos das cidades</li>
                        <li>Experimente as cervejas artesanais locais</li>
                        <li>Participe dos festivais de música</li>
                        <li>Explore os museus de arte e história</li>
                    </ul>
                </div>
            </div>

            <!-- Calendário -->
            <div class="tab-pane fade" id="calendario" role="tabpanel">
                <h4><i class="fas fa-calendar-alt me-2"></i>Calendário Acadêmico e Feriados</h4>
                <p>O ano acadêmico belga tem <strong>dois semestres</strong>, com feriados nacionais e regionais distribuídos ao longo do ano.</p>

                <div class="row">
                    <div class="col-md-6">
                        <h5>📚 Calendário Acadêmico:</h5>
                        <ul>
                            <li><strong>1º Semestre:</strong> Setembro - Janeiro</li>
                            <li><strong>Férias de Natal:</strong> 2-3 semanas em dezembro/janeiro</li>
                            <li><strong>2º Semestre:</strong> Fevereiro - Junho</li>
                            <li><strong>Férias de Páscoa:</strong> 2 semanas em março/abril</li>
                            <li><strong>Férias de Verão:</strong> Julho - Agosto</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>📅 Feriados Nacionais:</h5>
                        <ul>
                            <li><strong>1º Janeiro:</strong> Ano Novo</li>
                            <li><strong>Páscoa:</strong> Domingo e segunda-feira</li>
                            <li><strong>1º Maio:</strong> Dia do Trabalho</li>
                            <li><strong>21 Julho:</strong> Dia Nacional da Bélgica</li>
                            <li><strong>15 Agosto:</strong> Assunção de Maria</li>
                            <li><strong>1º Novembro:</strong> Dia de Todos os Santos</li>
                            <li><strong>11 Novembro:</strong> Dia do Armistício</li>
                            <li><strong>25 Dezembro:</strong> Natal</li>
                        </ul>
                    </div>
                </div>

                <h5>🗓️ Feriados Regionais:</h5>
                <ul>
                    <li><strong>Flandres:</strong> 11 de julho (Dia da Comunidade Flamenga)</li>
                    <li><strong>Valônia:</strong> 27 de setembro (Dia da Comunidade Francesa)</li>
                    <li><strong>Bruxelas:</strong> 8 de maio (Dia da Comunidade Alemã)</li>
                </ul>

                <h5>📝 Prazos Importantes:</h5>
                <ul>
                    <li><strong>Aplicações:</strong> Até 1º de fevereiro (setembro)</li>
                    <li><strong>Matrículas:</strong> Julho - setembro</li>
                    <li><strong>Início das aulas:</strong> Meados de setembro</li>
                    <li><strong>Exames:</strong> Janeiro e junho</li>
                </ul>

                <div class="highlight-box">
                    <h5>💡 Dicas de Planejamento:</h5>
                    <ul>
                        <li>Prepare documentos com antecedência</li>
                        <li>Considere feriados para viagens</li>
                        <li>Aproveite férias para explorar a Europa</li>
                        <li>Organize-se com o calendário acadêmico</li>
                    </ul>
                </div>
            </div>

            <!-- Guia Bolsas -->
            <div class="tab-pane fade" id="guia" role="tabpanel">
                <h4><i class="fas fa-map me-2"></i>Guia Completo para Bolsas</h4>
                <p>Para conseguir uma bolsa na Bélgica, é essencial <strong>planejar com antecedência</strong> e cumprir todos os requisitos.</p>

                <h5>📋 Passo a Passo:</h5>
                <ul>
                    <li><strong>1. Escolha do programa:</strong> Defina área de estudo e universidade</li>
                    <li><strong>2. Verificação de requisitos:</strong> Idiomas, notas, experiência</li>
                    <li><strong>3. Preparação de documentos:</strong> Histórico, diploma, cartas</li>
                    <li><strong>4. Aplicação:</strong> Preencha formulários com cuidado</li>
                    <li><strong>5. Entrevista:</strong> Prepare-se para possíveis entrevistas</li>
                </ul>

                <h5>🎯 Bolsas Específicas para Brasileiros:</h5>
                <ul>
                    <li><strong>CAPES/CNPq:</strong> Bolsas do governo brasileiro
                        <br><a href="https://www.capes.gov.br/" target="_blank" class="btn-custom">🔗 CAPES</a>
                    </li>
                    <li><strong>FAPESP:</strong> Fundação de Amparo à Pesquisa de SP
                        <br><a href="https://fapesp.br/" target="_blank" class="btn-custom">🔗 FAPESP</a>
                    </li>
                    <li><strong>Fulbright:</strong> Para pós-graduação
                        <br><a href="https://fulbright.org.br/" target="_blank" class="btn-custom">🔗 Fulbright</a>
                    </li>
                </ul>

                <h5>📚 Documentos Necessários:</h5>
                <ul>
                    <li><strong>Histórico escolar:</strong> Traduzido e apostilado</li>
                    <li><strong>Diploma:</strong> Reconhecido no Brasil</li>
                    <li><strong>Cartas de recomendação:</strong> 2-3 cartas acadêmicas</li>
                    <li><strong>Carta de motivação:</strong> 1-2 páginas bem estruturadas</li>
                    <li><strong>CV acadêmico:</strong> Formato europeu</li>
                    <li><strong>Certificado de idioma:</strong> B2-C1 no idioma de instrução</li>
                </ul>

                <div class="highlight-box">
                    <h5>💡 Dicas de Sucesso:</h5>
                    <ul>
                        <li><strong>Comece cedo:</strong> 12-18 meses antes da data desejada</li>
                        <li><strong>Pesquise bem:</strong> Cada bolsa tem critérios específicos</li>
                        <li><strong>Mantenha excelência acadêmica:</strong> Média 8.0+ é ideal</li>
                        <li><strong>Invista em idiomas:</strong> Francês, holandês ou alemão</li>
                        <li><strong>Construa network:</strong> Contatos acadêmicos são valiosos</li>
                    </ul>
                </div>

                <h5>🔗 Links Úteis:</h5>
                <ul>
                    <li><a href="https://www.studyinbelgium.be/" target="_blank" class="btn-custom">🎓 Study in Belgium</a></li>
                    <li><a href="https://www.vliruos.be/" target="_blank" class="btn-custom">🏆 VLIR-UOS</a></li>
                    <li><a href="https://www.ares-ac.be/" target="_blank" class="btn-custom">🌟 ARES</a></li>
                </ul>
            </div>

            <!-- Trabalho -->
            <div class="tab-pane fade" id="trabalho" role="tabpanel">
                <h4><i class="fas fa-briefcase me-2"></i>Trabalho com Visto de Estudante</h4>
                <p><strong>Sim!</strong> Estudantes internacionais podem trabalhar na Bélgica com algumas restrições e permissões específicas.</p>

                <div class="row">
                    <div class="col-md-6">
                        <h5>✅ Permissões de Trabalho:</h5>
                        <ul>
                            <li><strong>Estudantes UE:</strong> Trabalho livre sem restrições</li>
                            <li><strong>Estudantes não-UE:</strong> 20h/semana durante estudos</li>
                            <li><strong>Férias acadêmicas:</strong> Trabalho em tempo integral</li>
                            <li><strong>Autorização:</strong> Necessária para não-UE</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>💰 Salários e Benefícios:</h5>
                        <ul>
                            <li><strong>Salário mínimo:</strong> €1.593,81/mês (2024)</li>
                            <li><strong>Salário médio:</strong> €2.300/mês</li>
                            <li><strong>Salário estudante:</strong> €10-15/hora</li>
                            <li><strong>Benefícios:</strong> Seguro social, férias pagas</li>
                        </ul>
                    </div>
                </div>

                <h5>💼 Setores com Oportunidades:</h5>
                <ul>
                    <li><strong>Hospitalidade:</strong> Restaurantes, hotéis, turismo</li>
                    <li><strong>Varejo:</strong> Lojas, supermercados, centros comerciais</li>
                    <li><strong>Call centers:</strong> Atendimento multilingue</li>
                    <li><strong>Assistente de pesquisa:</strong> Universidades e institutos</li>
                    <li><strong>Babá/Au pair:</strong> Cuidado de crianças</li>
                </ul>

                <h5>📋 Requisitos para Trabalhar:</h5>
                <ul>
                    <li><strong>Visto de estudante válido</strong></li>
                    <li><strong>Número de segurança social belga</strong></li>
                    <li><strong>Conta bancária belga</strong></li>
                    <li><strong>Conhecimento básico do idioma local</strong></li>
                    <li><strong>Autorização de trabalho (não-UE)</strong></li>
                </ul>

                <h5>🎓 Pós-Graduação e Carreira:</h5>
                <ul>
                    <li><strong>Visto de busca de emprego:</strong> 12 meses após graduação</li>
                    <li><strong>Blue Card:</strong> Para profissionais altamente qualificados</li>
                    <li><strong>Startup visa:</strong> Para empreendedores</li>
                    <li><strong>Permanência:</strong> 5 anos de residência legal</li>
                </ul>

                <div class="highlight-box">
                    <h5>💡 Dicas para Encontrar Trabalho:</h5>
                    <ul>
                        <li><strong>Use redes sociais:</strong> LinkedIn, grupos do Facebook</li>
                        <li><strong>Visite agências de emprego:</strong> VDAB, Le Forem, Actiris</li>
                        <li><strong>Participe de feiras de emprego:</strong> Universidades organizam eventos</li>
                        <li><strong>Melhore idiomas:</strong> Francês, holandês ou alemão</li>
                        <li><strong>Construa network:</strong> Contatos são fundamentais</li>
                    </ul>
                </div>

                <h5>🔗 Recursos Úteis:</h5>
                <ul>
                    <li><a href="https://www.vdab.be/" target="_blank" class="btn-custom">💼 VDAB (Flandres)</a></li>
                    <li><a href="https://www.leforem.be/" target="_blank" class="btn-custom">💼 Le Forem (Valônia)</a></li>
                    <li><a href="https://www.actiris.be/" target="_blank" class="btn-custom">💼 Actiris (Bruxelas)</a></li>
                </ul>
            </div>
        </div>
    </div>

    <?php include '../footer_padronizado.php'; ?>

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
