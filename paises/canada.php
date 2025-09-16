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
$resultado_visita = registrarVisitaPais($_SESSION['usuario_id'], 'canada');

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
    <title>Canadá - Guia Completo para Estudantes - DayDreaming</title>
    
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
                            <img src="https://flagcdn.com/w80/ca.png" alt="Bandeira do Canadá" class="country-flag">
                            <h1 class="country-title">Canadá</h1>
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
                                <i class="fas fa-map-marker-alt me-1"></i>Canadá
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
        <img src="../imagens/canada_home.png" alt="Canadá - Paisagem" class="hero-image">
    </div>

    <!-- Cards de Informações Básicas -->
    <div class="container my-5">
        <div class="row g-4">
            <!-- População -->
            <div class="col-md-4">
                <div class="info-card">
                    <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="População" class="icon">
                    <h3>39,0M</h3>
                    <span class="badge">População</span>
                    <p>Habitantes em todo o país</p>
                </div>
            </div>

            <!-- IDH -->
            <div class="col-md-4">
                <div class="info-card">
                    <img src="https://cdn-icons-png.flaticon.com/512/616/616492.png" alt="IDH" class="icon">
                    <h3>15º</h3>
                    <span class="badge">IDH Mundial</span>
                    <p>Índice de Desenvolvimento Humano</p>
                </div>
            </div>

            <!-- Moeda -->
            <div class="col-md-4">
                <div class="info-card">
                    <img src="https://cdn-icons-png.flaticon.com/512/2331/2331970.png" alt="Moeda" class="icon">
                    <h3>CAD</h3>
                    <span class="badge">Moeda Local</span>
                    <p>Dólar Canadense</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Título da Seção -->
    <div class="section-title">
        <h2>🇨🇦 Guia Completo: Estudar no Canadá</h2>
        <p>Descubra a terra das oportunidades educacionais e qualidade de vida excepcional</p>
    </div>

    <!-- Menu de Navegação por Abas -->
    <div class="container">
        <ul class="nav nav-tabs justify-content-center" id="canadaTab" role="tablist">
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
        <div class="tab-content" id="canadaTabContent">
            <!-- Idiomas Oficiais -->
            <div class="tab-pane fade show active" id="idioma" role="tabpanel">
                <h4><i class="fas fa-language me-2"></i>Idiomas Oficiais</h4>
                <p>O Canadá possui <strong>dois idiomas oficiais</strong>: Inglês e Francês. A escolha do idioma de estudo depende da província e universidade, sendo o inglês predominante na maioria das regiões.</p>

                <div class="row">
                    <div class="col-md-6">
                        <h5>🇬🇧 Inglês</h5>
                        <ul>
                            <li><strong>Regiões:</strong> Todas as províncias exceto Quebec</li>
                            <li><strong>População:</strong> 75% dos canadenses</li>
                            <li><strong>Universidades:</strong> University of Toronto, UBC, McGill</li>
                            <li><strong>Testes:</strong> IELTS, TOEFL, CELPIP</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>🇫🇷 Francês</h5>
                        <ul>
                            <li><strong>Região:</strong> Quebec (oficial), New Brunswick (bilíngue)</li>
                            <li><strong>População:</strong> 22% dos canadenses</li>
                            <li><strong>Universidades:</strong> Université de Montréal, Laval</li>
                            <li><strong>Testes:</strong> TEF, TCF, DELF/DALF</li>
                        </ul>
                    </div>
                </div>

                <div class="highlight-box">
                    <h5>🎯 Dicas para Brasileiros:</h5>
                    <ul>
                        <li><strong>Inglês Canadense:</strong> Sotaque neutro, mais próximo ao americano</li>
                        <li><strong>Bilinguismo:</strong> Conhecer ambos os idiomas é uma vantagem</li>
                        <li><strong>Quebec:</strong> Francês é essencial para viver e trabalhar</li>
                        <li><strong>Preparação:</strong> Nível intermediário-avançado recomendado</li>
                    </ul>
                </div>

                <h5>📚 Requisitos de Proficiência:</h5>
                <ul>
                    <li><strong>Graduação:</strong> IELTS 6.0-6.5 ou TOEFL 80-90</li>
                    <li><strong>Pós-graduação:</strong> IELTS 6.5-7.0 ou TOEFL 90-100</li>
                    <li><strong>Doutorado:</strong> IELTS 7.0+ ou TOEFL 100+</li>
                    <li><strong>Programas ESL:</strong> Disponíveis para todos os níveis</li>
                </ul>
            </div>

            <!-- Clima -->
            <div class="tab-pane fade" id="clima" role="tabpanel">
                <h4><i class="fas fa-snowflake me-2"></i>Clima Típico</h4>
                <p>O Canadá possui um <strong>clima continental</strong> com grandes variações regionais. Os invernos são longos e frios, enquanto os verões são curtos e quentes. A temperatura varia drasticamente entre as regiões.</p>

                <div class="row">
                    <div class="col-md-6">
                        <h5>🌡️ Regiões Climáticas:</h5>
                        <ul>
                            <li><strong>Costa Oeste (Vancouver):</strong> Oceânico temperado, invernos amenos</li>
                            <li><strong>Pradarias (Calgary):</strong> Continental seco, extremos de temperatura</li>
                            <li><strong>Centro-Leste (Toronto):</strong> Continental úmido, quatro estações</li>
                            <li><strong>Costa Leste (Halifax):</strong> Marítimo, invernos moderados</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>❄️ Temperaturas Médias:</h5>
                        <ul>
                            <li><strong>Inverno (Dez-Mar):</strong> -15°C a -5°C (pode chegar a -40°C)</li>
                            <li><strong>Primavera (Abr-Mai):</strong> 5°C a 15°C</li>
                            <li><strong>Verão (Jun-Ago):</strong> 20°C a 30°C</li>
                            <li><strong>Outono (Set-Nov):</strong> 10°C a 20°C</li>
                        </ul>
                    </div>
                </div>

                <div class="highlight-box">
                    <h5>🧥 Preparação para o Inverno:</h5>
                    <ul>
                        <li><strong>Roupas essenciais:</strong> Casaco de inverno, botas impermeáveis, luvas, gorro</li>
                        <li><strong>Aquecimento:</strong> Todos os edifícios têm aquecimento central</li>
                        <li><strong>Transporte:</strong> Sistema público funciona normalmente no inverno</li>
                        <li><strong>Atividades:</strong> Esqui, patinação, hockey são populares</li>
                    </ul>
                </div>

                <h5>🌍 Comparação com o Brasil:</h5>
                <ul>
                    <li><strong>Temperatura:</strong> Muito mais frio, especialmente no inverno</li>
                    <li><strong>Neve:</strong> Comum de novembro a março na maioria das regiões</li>
                    <li><strong>Luz solar:</strong> Dias muito curtos no inverno (8h), longos no verão (16h)</li>
                    <li><strong>Adaptação:</strong> Período de 6-12 meses para acostumação completa</li>
                </ul>
            </div>

            <!-- Custo de Vida -->
            <div class="tab-pane fade" id="custos" role="tabpanel">
                <h4><i class="fas fa-dollar-sign me-2"></i>Custo de Vida</h4>
                <p>O Canadá tem um custo de vida <strong>moderado a alto</strong>, variando significativamente entre cidades. Toronto e Vancouver são as mais caras, enquanto cidades menores oferecem custos mais acessíveis.</p>

                <div class="row">
                    <div class="col-md-6">
                        <h5>🏠 Acomodação (por mês):</h5>
                        <ul>
                            <li><strong>Residência estudantil:</strong> CAD$ 600-1200</li>
                            <li><strong>Apartamento compartilhado:</strong> CAD$ 500-900</li>
                            <li><strong>Homestay:</strong> CAD$ 700-1000</li>
                            <li><strong>Apartamento próprio:</strong> CAD$ 1000-2500</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>🍽️ Alimentação (por mês):</h5>
                        <ul>
                            <li><strong>Supermercado:</strong> CAD$ 300-500</li>
                            <li><strong>Restaurante universitário:</strong> CAD$ 8-15 por refeição</li>
                            <li><strong>Restaurante médio:</strong> CAD$ 20-35 por refeição</li>
                            <li><strong>Fast food:</strong> CAD$ 10-18 por refeição</li>
                        </ul>
                    </div>
                </div>

                <h5>🚌 Transporte:</h5>
                <ul>
                    <li><strong>Passe mensal estudantil:</strong> CAD$ 80-150 (desconto significativo)</li>
                    <li><strong>Transporte público:</strong> Excelente em grandes cidades</li>
                    <li><strong>Bicicleta:</strong> Popular no verão, limitada no inverno</li>
                    <li><strong>Carro:</strong> Útil em cidades menores</li>
                </ul>

                <h5>📚 Outros Custos:</h5>
                <ul>
                    <li><strong>Livros e materiais:</strong> CAD$ 1000-2000 por ano</li>
                    <li><strong>Seguro saúde:</strong> CAD$ 600-1200 por ano</li>
                    <li><strong>Roupas de inverno:</strong> CAD$ 500-800 (investimento inicial)</li>
                    <li><strong>Telefone/Internet:</strong> CAD$ 50-80 por mês</li>
                </ul>

                <div class="highlight-box">
                    <h5>💡 Dicas para Economizar:</h5>
                    <ul>
                        <li>Compre roupas de inverno em liquidações de fim de temporada</li>
                        <li>Use cartão de estudante para descontos em transporte e entretenimento</li>
                        <li>Cozinhe em casa - supermercados têm preços razoáveis</li>
                        <li>Aproveite atividades gratuitas: parques, bibliotecas, eventos universitários</li>
                    </ul>
                </div>

                <p><strong>💰 Orçamento mensal total:</strong> CAD$ 1500-3000 (dependendo da cidade e estilo de vida)</p>
            </div>

            <!-- Bolsas de Estudo -->
            <div class="tab-pane fade" id="bolsas" role="tabpanel">
                <h4><i class="fas fa-graduation-cap me-2"></i>Bolsas de Estudo</h4>
                <p>O Canadá oferece <strong>excelentes oportunidades de bolsas</strong> para estudantes internacionais, desde programas governamentais até bolsas específicas de universidades e organizações.</p>

                <h5>🏛️ Bolsas Governamentais:</h5>
                <ul>
                    <li><strong>Vanier Canada Graduate Scholarships:</strong> CAD$ 50.000/ano por 3 anos (doutorado)</li>
                    <li><strong>Banting Postdoctoral Fellowships:</strong> CAD$ 70.000/ano por 2 anos</li>
                    <li><strong>Canada Graduate Scholarships (CGS):</strong> CAD$ 17.500/ano (mestrado)</li>
                    <li><strong>IDRC Research Awards:</strong> Para estudantes de países em desenvolvimento</li>
                </ul>

                <h5>🎓 Bolsas Universitárias Principais:</h5>
                <ul>
                    <li><strong>University of Toronto:</strong> Lester B. Pearson International Scholarship (cobertura total)
                        <br><a href="https://future.utoronto.ca/pearson/" target="_blank" class="btn-custom">🔗 Pearson Scholarship</a>
                    </li>
                    <li><strong>University of British Columbia:</strong> International Leader of Tomorrow Award (até CAD$ 28.000)
                        <br><a href="https://students.ubc.ca/enrolment/finances/award-search/international-leader-tomorrow-award" target="_blank" class="btn-custom">🔗 UBC Awards</a>
                    </li>
                    <li><strong>McGill University:</strong> McGill Entrance Scholarship Program (CAD$ 3.000-12.000)
                        <br><a href="https://www.mcgill.ca/studentaid/scholarships-aid/entrance" target="_blank" class="btn-custom">🔗 McGill Scholarships</a>
                    </li>
                    <li><strong>University of Waterloo:</strong> International Student Entrance Scholarships (CAD$ 2.000-10.000)
                        <br><a href="https://uwaterloo.ca/find-out-more/financing/scholarships" target="_blank" class="btn-custom">🔗 Waterloo Scholarships</a>
                    </li>
                </ul>

                <h5>🌍 Programas Provinciais:</h5>
                <ul>
                    <li><strong>Ontario Graduate Scholarship (OGS):</strong> CAD$ 15.000/ano</li>
                    <li><strong>Quebec Merit Scholarship:</strong> Para estudos em francês</li>
                    <li><strong>Alberta Graduate Excellence Scholarship:</strong> CAD$ 11.000/ano</li>
                    <li><strong>British Columbia Graduate Scholarship:</strong> CAD$ 15.000/ano</li>
                </ul>

                <div class="highlight-box">
                    <h5>📋 Requisitos Gerais:</h5>
                    <ul>
                        <li>Excelência acadêmica (GPA 3.7+ ou equivalente)</li>
                        <li>Proficiência em inglês/francês comprovada</li>
                        <li>Carta de motivação bem estruturada</li>
                        <li>Cartas de recomendação acadêmica (2-3)</li>
                        <li>Experiência em pesquisa ou liderança</li>
                        <li>Proposta de pesquisa (para pós-graduação)</li>
                    </ul>
                </div>

                <p><strong>💰 Valores típicos:</strong> Bolsas parciais variam de CAD$ 2.000 a CAD$ 15.000 por ano. Bolsas integrais podem cobrir 100% das taxas + CAD$ 15.000-25.000/ano para subsistência.</p>
            </div>

            <!-- Universidades -->
            <div class="tab-pane fade" id="universidades" role="tabpanel">
                <h4><i class="fas fa-university me-2"></i>Principais Universidades</h4>
                <p>O Canadá possui algumas das <strong>melhores universidades do mundo</strong>, com 3 instituições no top 50 global. O sistema educacional canadense é reconhecido mundialmente pela qualidade e inovação.</p>

                <h5>🏆 Top Universidades Canadenses:</h5>
                <ul>
                    <li><strong>University of Toronto:</strong> #21 mundial, maior universidade de pesquisa do Canadá
                        <br><a href="https://www.utoronto.ca/admissions/international-students" target="_blank" class="btn-custom">🔗 UofT Internacional</a>
                    </li>
                    <li><strong>McGill University:</strong> #30 mundial, "Harvard do Canadá"
                        <br><a href="https://www.mcgill.ca/international/" target="_blank" class="btn-custom">🔗 McGill Internacional</a>
                    </li>
                    <li><strong>University of British Columbia (UBC):</strong> #40 mundial, campus em Vancouver e Okanagan
                        <br><a href="https://students.ubc.ca/international-student-guide" target="_blank" class="btn-custom">🔗 UBC Internacional</a>
                    </li>
                    <li><strong>University of Alberta:</strong> #110 mundial, forte em engenharia e medicina
                        <br><a href="https://www.ualberta.ca/international/" target="_blank" class="btn-custom">🔗 UAlberta Internacional</a>
                    </li>
                    <li><strong>McMaster University:</strong> #140 mundial, inovação em ensino médico
                        <br><a href="https://future.mcmaster.ca/international/" target="_blank" class="btn-custom">🔗 McMaster Internacional</a>
                    </li>
                    <li><strong>University of Waterloo:</strong> #160 mundial, líder em tecnologia e co-op
                        <br><a href="https://uwaterloo.ca/future-students/international" target="_blank" class="btn-custom">🔗 Waterloo Internacional</a>
                    </li>
                </ul>

                <h5>🌟 Universidades Especializadas:</h5>
                <ul>
                    <li><strong>Ryerson University (Toronto Metropolitan):</strong> Inovação e tecnologia aplicada</li>
                    <li><strong>Simon Fraser University:</strong> Pesquisa interdisciplinar</li>
                    <li><strong>University of Calgary:</strong> Energia e negócios</li>
                    <li><strong>Concordia University:</strong> Arte, design e engenharia</li>
                </ul>

                <h5>🎓 Áreas de Excelência:</h5>
                <ul>
                    <li><strong>Tecnologia e Engenharia:</strong> Waterloo, UofT, UBC</li>
                    <li><strong>Medicina e Ciências da Saúde:</strong> McGill, UofT, McMaster</li>
                    <li><strong>Negócios:</strong> Rotman (UofT), Sauder (UBC), Desautels (McGill)</li>
                    <li><strong>Ciências Naturais:</strong> UBC, UofT, McGill</li>
                    <li><strong>Artes e Humanidades:</strong> UofT, McGill, UBC</li>
                </ul>

                <div class="highlight-box">
                    <h5>📊 Dados sobre Intercambistas:</h5>
                    <ul>
                        <li>Mais de <strong>800.000 estudantes internacionais</strong> no Canadá</li>
                        <li><strong>Brasil</strong> está entre os top 10 países de origem</li>
                        <li>Cerca de <strong>25.000 brasileiros</strong> estudam no Canadá anualmente</li>
                        <li>Áreas mais populares: Negócios, Engenharia, TI, Ciências da Saúde</li>
                    </ul>
                </div>
            </div>

            <!-- Comunidade Brasileira -->
            <div class="tab-pane fade" id="comunidade-br" role="tabpanel">
                <h4><i class="fas fa-flag me-2"></i>Comunidade de Brasileiros</h4>
                <p>O Canadá possui uma <strong>comunidade brasileira vibrante</strong> de aproximadamente 170.000 pessoas, concentrada principalmente em Toronto, Vancouver, Montreal e Calgary.</p>

                <h5>🏙️ Principais Cidades:</h5>
                <ul>
                    <li><strong>Toronto (GTA):</strong> Maior comunidade (~80.000), bairros como Kensington Market</li>
                    <li><strong>Vancouver:</strong> Segunda maior comunidade (~35.000), forte presença em Burnaby</li>
                    <li><strong>Montreal:</strong> Comunidade crescente (~25.000), bilíngue português-francês</li>
                    <li><strong>Calgary:</strong> Comunidade ativa (~15.000), setor de energia</li>
                </ul>

                <h5>🤝 Organizações e Grupos:</h5>
                <ul>
                    <li><strong>Câmara de Comércio Brasil-Canadá:</strong> Networking profissional</li>
                    <li><strong>Brazilian Canadian Cultural Society:</strong> Eventos culturais</li>
                    <li><strong>Grupos no Facebook:</strong> "Brasileiros no Canadá", "Brasileiros em Toronto"</li>
                    <li><strong>Associações Estudantis:</strong> BRASA (Brazilian Student Association)</li>
                </ul>

                <h5>🎉 Eventos e Festivais:</h5>
                <ul>
                    <li><strong>Festival do Brasil:</strong> Evento anual em Toronto</li>
                    <li><strong>Carnaval de Toronto:</strong> Maior celebração brasileira do Canadá</li>
                    <li><strong>Festa Junina:</strong> Celebrações em várias cidades</li>
                    <li><strong>Copa do Mundo:</strong> Grandes encontros para assistir jogos</li>
                </ul>

                <h5>🍽️ Vida Brasileira no Canadá:</h5>
                <ul>
                    <li><strong>Restaurantes:</strong> Mais de 150 restaurantes brasileiros</li>
                    <li><strong>Mercados:</strong> Produtos brasileiros em lojas especializadas</li>
                    <li><strong>Capoeira:</strong> Grupos ativos em todas as grandes cidades</li>
                    <li><strong>Música:</strong> Bandas e eventos de música brasileira</li>
                </ul>

                <div class="highlight-box">
                    <h5>📱 Recursos Úteis:</h5>
                    <ul>
                        <li><strong>Consulados:</strong> Toronto, Vancouver, Montreal</li>
                        <li><strong>Apps:</strong> "Brasileiros no Canadá", grupos de WhatsApp</li>
                        <li><strong>Rádios:</strong> Programas em português na rádio local</li>
                        <li><strong>Igreja:</strong> Missas em português em várias cidades</li>
                    </ul>
                </div>
            </div>

            <!-- Cultura e Costumes -->
            <div class="tab-pane fade" id="cultura" role="tabpanel">
                <h4><i class="fas fa-maple-leaf me-2"></i>Cultura e Costumes Locais</h4>
                <p>A cultura canadense é caracterizada pela <strong>diversidade, tolerância e cortesia</strong>. Os canadenses valorizam o multiculturalismo, a igualdade e têm uma reputação mundial de serem educados e acolhedores.</p>

                <h5>🤝 Características Culturais:</h5>
                <ul>
                    <li><strong>Cortesia:</strong> "Por favor", "obrigado" e "desculpe" são usados constantemente</li>
                    <li><strong>Multiculturalismo:</strong> Política oficial de diversidade cultural</li>
                    <li><strong>Igualdade:</strong> Forte compromisso com direitos humanos e igualdade</li>
                    <li><strong>Pontualidade:</strong> Ser pontual é muito valorizado</li>
                    <li><strong>Modéstia:</strong> Evitam ostentação e valorizam a humildade</li>
                </ul>

                <h5>🍁 Tradições Canadenses:</h5>
                <ul>
                    <li><strong>Canada Day (1º de julho):</strong> Dia nacional com celebrações</li>
                    <li><strong>Thanksgiving (outubro):</strong> Ação de graças canadense</li>
                    <li><strong>Hockey:</strong> Esporte nacional e paixão cultural</li>
                    <li><strong>Maple Syrup:</strong> Xarope de bordo, símbolo nacional</li>
                </ul>

                <h5>❄️ Vida no Inverno:</h5>
                <ul>
                    <li><strong>Atividades:</strong> Esqui, patinação, hockey, snowboarding</li>
                    <li><strong>Festivais:</strong> Winterlude (Ottawa), Festival du Voyageur (Winnipeg)</li>
                    <li><strong>Adaptação:</strong> Canadenses abraçam o inverno com atividades ao ar livre</li>
                    <li><strong>Aquecimento:</strong> Todos os espaços são bem aquecidos</li>
                </ul>

                <h5>🏠 Vida Social:</h5>
                <ul>
                    <li><strong>Tim Hortons:</strong> Cafeteria icônica, centro social</li>
                    <li><strong>Cottage Culture:</strong> Casas de campo para fins de semana</li>
                    <li><strong>Outdoor Activities:</strong> Camping, hiking, canoagem</li>
                    <li><strong>Community Centers:</strong> Centros comunitários em cada bairro</li>
                </ul>

                <div class="highlight-box">
                    <h5>⚠️ Dicas Importantes:</h5>
                    <ul>
                        <li>Sempre diga "sorry" mesmo quando não for sua culpa</li>
                        <li>Respeite filas e espaço pessoal</li>
                        <li>Gorjeta de 15-20% em restaurantes é esperada</li>
                        <li>Aprenda sobre a história indígena (muito respeitada)</li>
                    </ul>
                </div>
            </div>

            <!-- Calendário Acadêmico -->
            <div class="tab-pane fade" id="calendario" role="tabpanel">
                <h4><i class="fas fa-calendar-alt me-2"></i>Calendário Acadêmico</h4>
                <p>O ano acadêmico canadense segue o <strong>sistema norte-americano</strong>, iniciando em setembro e terminando em abril/maio, dividido em dois semestres principais com opção de semestre de verão.</p>

                <div class="row">
                    <div class="col-md-6">
                        <h5>📅 Fall Semester (Outono):</h5>
                        <ul>
                            <li><strong>Início:</strong> Início de setembro</li>
                            <li><strong>Término:</strong> Meados de dezembro</li>
                            <li><strong>Exames:</strong> Dezembro</li>
                            <li><strong>Férias:</strong> 2-3 semanas em dezembro/janeiro</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>📅 Winter Semester (Inverno):</h5>
                        <ul>
                            <li><strong>Início:</strong> Janeiro</li>
                            <li><strong>Término:</strong> Abril/maio</li>
                            <li><strong>Exames:</strong> Abril</li>
                            <li><strong>Férias de verão:</strong> Maio-agosto</li>
                        </ul>
                    </div>
                </div>

                <h5>☀️ Summer Semester (Verão - Opcional):</h5>
                <ul>
                    <li><strong>Período:</strong> Maio a agosto</li>
                    <li><strong>Duração:</strong> Cursos intensivos de 6-8 semanas</li>
                    <li><strong>Vantagem:</strong> Acelerar graduação ou recuperar créditos</li>
                    <li><strong>Trabalho:</strong> Muitos estudantes trabalham no verão</li>
                </ul>

                <h5>🎓 Períodos de Aplicação:</h5>
                <ul>
                    <li><strong>Fall Semester:</strong> Aplicações até 1º de março (principais)</li>
                    <li><strong>Winter Semester:</strong> Aplicações até 1º de outubro</li>
                    <li><strong>Summer Semester:</strong> Aplicações até 1º de março</li>
                    <li><strong>Bolsas:</strong> Aplicações geralmente 8-12 meses antes</li>
                </ul>

                <div class="highlight-box">
                    <h5>⏰ Cronograma Recomendado:</h5>
                    <ul>
                        <li><strong>15 meses antes:</strong> Pesquisar universidades e programas</li>
                        <li><strong>12 meses antes:</strong> Preparar documentos e testes de idioma</li>
                        <li><strong>10 meses antes:</strong> Aplicar para universidades e bolsas</li>
                        <li><strong>8 meses antes:</strong> Receber ofertas e aplicar para visto</li>
                        <li><strong>4 meses antes:</strong> Confirmar acomodação e seguro</li>
                        <li><strong>2 meses antes:</strong> Finalizar preparativos de viagem</li>
                    </ul>
                </div>
            </div>

            <!-- Guia para Bolsas -->
            <div class="tab-pane fade" id="guia" role="tabpanel">
                <h4><i class="fas fa-map me-2"></i>Guia para Conseguir Bolsas</h4>
                <p>Conseguir uma bolsa no Canadá requer <strong>excelência acadêmica, preparação meticulosa e aplicação estratégica</strong>. O processo é competitivo, mas as oportunidades são abundantes.</p>

                <h5>📚 O que Estudar:</h5>
                <ul>
                    <li><strong>Inglês/Francês:</strong> IELTS/TOEFL ou TEF/TCF - foque em todas as habilidades</li>
                    <li><strong>Área acadêmica:</strong> Mantenha GPA alto (3.7+ em escala 4.0)</li>
                    <li><strong>Pesquisa:</strong> Participe de projetos de iniciação científica</li>
                    <li><strong>Liderança:</strong> Atividades extracurriculares e voluntariado</li>
                </ul>

                <h5>📋 Documentação Necessária:</h5>
                <ul>
                    <li><strong>Histórico acadêmico:</strong> Traduzido e avaliado por WES ou ICAS</li>
                    <li><strong>Diploma:</strong> Traduzido e certificado</li>
                    <li><strong>Teste de idioma:</strong> IELTS/TOEFL válido (não mais que 2 anos)</li>
                    <li><strong>CV/Resume:</strong> Formato norte-americano</li>
                    <li><strong>Statement of Purpose:</strong> Carta de motivação específica</li>
                    <li><strong>Letters of Reference:</strong> 2-3 cartas de recomendação</li>
                    <li><strong>Research Proposal:</strong> Para programas de pesquisa</li>
                </ul>

                <h5>✍️ Como Fazer CV Canadense:</h5>
                <ul>
                    <li><strong>Formato:</strong> Cronológico reverso, máximo 2 páginas</li>
                    <li><strong>Seções:</strong> Contact Info, Education, Experience, Skills, Awards</li>
                    <li><strong>Linguagem:</strong> Action verbs, quantified achievements</li>
                    <li><strong>Personalização:</strong> Adapte para cada aplicação</li>
                </ul>

                <h5>💌 Statement of Purpose:</h5>
                <ul>
                    <li><strong>Estrutura:</strong> Introduction, Academic Background, Goals, Fit</li>
                    <li><strong>Conteúdo:</strong> Por que este programa/universidade específica</li>
                    <li><strong>Tom:</strong> Profissional, mas pessoal e convincente</li>
                    <li><strong>Tamanho:</strong> 1-2 páginas, máximo 1000 palavras</li>
                </ul>

                <div class="highlight-box">
                    <h5>🎯 Preparação para Entrevistas:</h5>
                    <ul>
                        <li><strong>Pesquise:</strong> Universidade, programa, professores, projetos atuais</li>
                        <li><strong>Pratique:</strong> Perguntas comuns em inglês/francês</li>
                        <li><strong>Prepare:</strong> Exemplos específicos de suas experiências</li>
                        <li><strong>Demonstre:</strong> Conhecimento sobre o Canadá e motivação genuína</li>
                    </ul>
                </div>

                <p><strong>🔗 Links Úteis:</strong></p>
                <a href="https://www.educanada.ca/scholarships-bourses/index.aspx" target="_blank" class="btn-custom">🎓 EduCanada Scholarships</a>
                <a href="https://www.scholarships-bourses.gc.ca/" target="_blank" class="btn-custom">🏆 Government Scholarships</a>
                <a href="https://www.univcan.ca/" target="_blank" class="btn-custom">🌟 Universities Canada</a>
            </div>

            <!-- Trabalho com Visto de Estudante -->
            <div class="tab-pane fade" id="trabalho" role="tabpanel">
                <h4><i class="fas fa-briefcase me-2"></i>Trabalho com Visto de Estudante</h4>
                <p><strong>Sim!</strong> Estudantes internacionais no Canadá podem trabalhar com visto de estudante, e o país oferece <strong>excelentes oportunidades</strong> de trabalho durante e após os estudos.</p>

                <h5>⏰ Permissões de Trabalho:</h5>
                <ul>
                    <li><strong>On-campus:</strong> Horas ilimitadas no campus da universidade</li>
                    <li><strong>Off-campus:</strong> Máximo 20 horas/semana durante estudos</li>
                    <li><strong>Durante férias:</strong> Tempo integral (40 horas/semana)</li>
                    <li><strong>Co-op/Estágios:</strong> Tempo integral se parte do programa</li>
                </ul>

                <h5>📄 Documentos Necessários:</h5>
                <ul>
                    <li><strong>Study Permit válido:</strong> Com autorização de trabalho</li>
                    <li><strong>Social Insurance Number (SIN):</strong> Obrigatório para trabalhar</li>
                    <li><strong>Conta bancária canadense:</strong> Para receber salário</li>
                    <li><strong>Certificado de matrícula:</strong> Comprovação de estudos</li>
                </ul>

                <h5>💼 Tipos de Trabalho Disponíveis:</h5>
                <ul>
                    <li><strong>Campus jobs:</strong> Biblioteca, laboratórios, serviços estudantis</li>
                    <li><strong>Retail:</strong> Lojas, supermercados, shopping centers</li>
                    <li><strong>Food service:</strong> Restaurantes, cafés, delivery</li>
                    <li><strong>Tutoring:</strong> Aulas particulares, assistência acadêmica</li>
                    <li><strong>Co-op programs:</strong> Estágios remunerados na área de estudo</li>
                </ul>

                <h5>🏢 Oportunidades por Setor:</h5>
                <ul>
                    <li><strong>Tecnologia:</strong> CAD$ 18-30/h (estágios), CAD$ 25-45/h (co-op)</li>
                    <li><strong>Hospitality:</strong> CAD$ 15-20/h + gorjetas</li>
                    <li><strong>Retail:</strong> CAD$ 15-18/h</li>
                    <li><strong>Tutoring:</strong> CAD$ 20-35/h</li>
                    <li><strong>Campus jobs:</strong> CAD$ 15-22/h</li>
                </ul>

                <h5>🎓 Pós-Graduação - PGWP:</h5>
                <ul>
                    <li><strong>Post-Graduation Work Permit:</strong> Até 3 anos de trabalho</li>
                    <li><strong>Duração:</strong> Baseada na duração do programa de estudos</li>
                    <li><strong>Vantagem:</strong> Experiência canadense para imigração</li>
                    <li><strong>Pathway:</strong> Caminho para residência permanente</li>
                </ul>

                <div class="highlight-box">
                    <h5>💰 Benefícios Financeiros:</h5>
                    <ul>
                        <li><strong>Salário mínimo:</strong> CAD$ 15-17/hora (varia por província)</li>
                        <li><strong>Renda mensal:</strong> CAD$ 1.200-1.600 (20h/semana)</li>
                        <li><strong>Cobertura de custos:</strong> 40-60% das despesas de vida</li>
                        <li><strong>Experiência:</strong> Networking e habilidades canadenses</li>
                    </ul>
                </div>

                <p><strong>🎯 Dica:</strong> Programas co-op são altamente recomendados - combinam estudo e trabalho, oferecendo experiência valiosa e networking na área de interesse.</p>
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
