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
$resultado_visita = registrarVisitaPais($_SESSION['usuario_id'], 'africa');

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
    <title>África do Sul - Guia Completo para Estudantes - DayDreaming</title>
    
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
                            <img src="https://flagcdn.com/w80/za.png" alt="Bandeira da África do Sul" class="country-flag">
                            <h1 class="country-title">África do Sul</h1>
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
                                <i class="fas fa-map-marker-alt me-1"></i>África do Sul
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
        <img src="../imagens/africa_home.png" alt="África do Sul - Paisagem" class="hero-image">
    </div>

    <!-- Cards de Informações Básicas -->
    <div class="container my-5">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="info-card">
                    <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="População" class="icon">
                    <h3>60M</h3>
                    <span class="badge">População</span>
                    <p>Habitantes em todo o país</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-card">
                    <img src="https://cdn-icons-png.flaticon.com/512/616/616492.png" alt="IDH" class="icon">
                    <h3>114º</h3>
                    <span class="badge">IDH Mundial</span>
                    <p>Índice de Desenvolvimento Humano</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-card">
                    <img src="https://cdn-icons-png.flaticon.com/512/2331/2331970.png" alt="Moeda" class="icon">
                    <h3>ZAR</h3>
                    <span class="badge">Moeda Local</span>
                    <p>Rand Sul-Africano</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Título da Seção -->
    <div class="section-title">
        <h2>🇿🇦 Guia Completo: Estudar na África do Sul</h2>
        <p>Descubra a nação arco-íris com diversidade cultural e oportunidades únicas</p>
    </div>

    <!-- Menu de Navegação por Abas -->
    <div class="container">
        <ul class="nav nav-tabs justify-content-center" id="africaTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="idioma-tab" data-bs-toggle="tab" data-bs-target="#idioma" type="button" role="tab">🗣️ Idiomas</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="clima-tab" data-bs-toggle="tab" data-bs-target="#clima" type="button" role="tab">🌡️ Clima</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="custos-tab" data-bs-toggle="tab" data-bs-target="#custos" type="button" role="tab">💰 Custos</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="bolsas-tab" data-bs-toggle="tab" data-bs-target="#bolsas" type="button" role="tab">🎓 Bolsas</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="universidades-tab" data-bs-toggle="tab" data-bs-target="#universidades" type="button" role="tab">🏛️ Universidades</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="comunidade-tab" data-bs-toggle="tab" data-bs-target="#comunidade-br" type="button" role="tab">🇧🇷 Brasileiros</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="cultura-tab" data-bs-toggle="tab" data-bs-target="#cultura" type="button" role="tab">🎭 Cultura</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="calendario-tab" data-bs-toggle="tab" data-bs-target="#calendario" type="button" role="tab">📅 Calendário</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="guia-tab" data-bs-toggle="tab" data-bs-target="#guia" type="button" role="tab">📋 Guia Bolsas</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="trabalho-tab" data-bs-toggle="tab" data-bs-target="#trabalho" type="button" role="tab">💼 Trabalho</button>
            </li>
        </ul>

        <!-- Conteúdo das Abas -->
        <div class="tab-content" id="africaTabContent">
            <!-- Idiomas -->
            <div class="tab-pane fade show active" id="idioma" role="tabpanel">
                <h4><i class="fas fa-language me-2"></i>Idiomas Oficiais</h4>
                <p>A África do Sul possui <strong>11 idiomas oficiais</strong> com inglês e africâner como principais na educação.</p>
                <ul>
                    <li><strong>Inglês:</strong> Língua principal de educação e negócios</li>
                    <li><strong>Africâner:</strong> Falado por 13% da população</li>
                    <li><strong>Zulu:</strong> Maior grupo linguístico (23%)</li>
                    <li><strong>Xhosa:</strong> Segundo maior grupo (16%)</li>
                    <li><strong>Requisitos:</strong> IELTS 6.0-6.5 ou TOEFL 80-90</li>
                </ul>
            </div>

            <!-- Clima -->
            <div class="tab-pane fade" id="clima" role="tabpanel">
                <h4><i class="fas fa-sun me-2"></i>Clima Típico</h4>
                <p>A África do Sul possui <strong>clima subtropical</strong> com estações invertidas ao Brasil e grande diversidade regional.</p>
                <ul>
                    <li><strong>Verão (Dez-Mar):</strong> 20-30°C, estação chuvosa</li>
                    <li><strong>Outono (Abr-Mai):</strong> 15-25°C, agradável</li>
                    <li><strong>Inverno (Jun-Ago):</strong> 5-20°C, seco</li>
                    <li><strong>Primavera (Set-Nov):</strong> 15-25°C, floração</li>
                    <li><strong>Vantagem:</strong> Clima ameno, muitos dias de sol</li>
                </ul>
            </div>

            <!-- Custo de Vida -->
            <div class="tab-pane fade" id="custos" role="tabpanel">
                <h4><i class="fas fa-coins me-2"></i>Custo de Vida</h4>
                <p>A África do Sul oferece <strong>excelente custo-benefício</strong> com padrão de vida bom e custos baixos.</p>
                <ul>
                    <li><strong>Acomodação:</strong> ZAR 3.000-8.000/mês (€150-400)</li>
                    <li><strong>Alimentação:</strong> ZAR 2.000-4.000/mês (€100-200)</li>
                    <li><strong>Transporte:</strong> ZAR 500-1.000/mês (€25-50)</li>
                    <li><strong>Total:</strong> ZAR 6.000-15.000/mês (€300-750)</li>
                    <li><strong>Vantagem:</strong> Um dos países mais baratos para estudar</li>
                </ul>
            </div>

            <!-- Bolsas -->
            <div class="tab-pane fade" id="bolsas" role="tabpanel">
                <h4><i class="fas fa-graduation-cap me-2"></i>Bolsas de Estudo</h4>
                <p>A África do Sul oferece <strong>diversas bolsas</strong> e taxas educacionais muito acessíveis.</p>
                <ul>
                    <li><strong>Mandela Rhodes Scholarships:</strong> Para liderança africana
                        <br><a href="https://www.mandelarhodes.org/" target="_blank" class="btn-custom">🔗 Mandela Rhodes</a>
                    </li>
                    <li><strong>UCT International Academic Excellence Scholarships:</strong> University of Cape Town
                        <br><a href="https://www.uct.ac.za/" target="_blank" class="btn-custom">🔗 UCT</a>
                    </li>
                    <li><strong>Wits Scholarships:</strong> University of the Witwatersrand
                        <br><a href="https://www.wits.ac.za/" target="_blank" class="btn-custom">🔗 Wits</a>
                    </li>
                    <li><strong>Baixas taxas:</strong> ZAR 40.000-80.000/ano (€2.000-4.000)</li>
                </ul>
            </div>

            <!-- Universidades -->
            <div class="tab-pane fade" id="universidades" role="tabpanel">
                <h4><i class="fas fa-university me-2"></i>Principais Universidades</h4>
                <p>A África do Sul possui <strong>universidades de qualidade</strong> com tradição em pesquisa e diversidade.</p>
                <ul>
                    <li><strong>University of Cape Town (UCT):</strong> #226 mundial, mais prestigiosa
                        <br><a href="https://www.uct.ac.za/" target="_blank" class="btn-custom">🔗 UCT</a>
                    </li>
                    <li><strong>University of the Witwatersrand (Wits):</strong> #428 mundial
                        <br><a href="https://www.wits.ac.za/" target="_blank" class="btn-custom">🔗 Wits</a>
                    </li>
                    <li><strong>Stellenbosch University:</strong> #401-500 mundial
                        <br><a href="https://www.sun.ac.za/" target="_blank" class="btn-custom">🔗 Stellenbosch</a>
                    </li>
                    <li><strong>80.000+ estudantes internacionais</strong></li>
                </ul>
            </div>

            <!-- Comunidade Brasileira -->
            <div class="tab-pane fade" id="comunidade-br" role="tabpanel">
                <h4><i class="fas fa-flag me-2"></i>Comunidade de Brasileiros</h4>
                <p>A África do Sul possui uma <strong>pequena comunidade brasileira</strong> de aproximadamente 3.000 pessoas.</p>
                <ul>
                    <li><strong>Principais cidades:</strong> Cidade do Cabo, Joanesburgo, Durban</li>
                    <li><strong>Setores:</strong> Mineração, agricultura, turismo, educação</li>
                    <li><strong>Organizações:</strong> Câmara Brasil-África do Sul</li>
                    <li><strong>Recursos:</strong> Embaixada em Pretória, consulado em Cidade do Cabo</li>
                    <li><strong>Eventos:</strong> Festival do Brasil, encontros culturais</li>
                </ul>
            </div>

            <!-- Cultura -->
            <div class="tab-pane fade" id="cultura" role="tabpanel">
                <h4><i class="fas fa-rainbow me-2"></i>Cultura e Costumes</h4>
                <p>A cultura sul-africana é conhecida como <strong>"nação arco-íris"</strong> pela diversidade étnica e cultural.</p>
                <ul>
                    <li><strong>Diversidade:</strong> 11 idiomas oficiais, múltiplas etnias</li>
                    <li><strong>Ubuntu:</strong> Filosofia de humanidade e interconexão</li>
                    <li><strong>História:</strong> Superação do apartheid, reconciliação</li>
                    <li><strong>Música:</strong> Jazz, kwaito, amapiano</li>
                    <li><strong>Braai:</strong> Tradição do churrasco sul-africano</li>
                </ul>
            </div>

            <!-- Calendário -->
            <div class="tab-pane fade" id="calendario" role="tabpanel">
                <h4><i class="fas fa-calendar-alt me-2"></i>Calendário Acadêmico</h4>
                <p>O ano acadêmico sul-africano segue <strong>dois semestres</strong> com início em fevereiro.</p>
                <ul>
                    <li><strong>Primeiro semestre:</strong> Fevereiro-junho</li>
                    <li><strong>Segundo semestre:</strong> Julho-novembro</li>
                    <li><strong>Aplicações:</strong> Agosto-outubro (ano anterior)</li>
                    <li><strong>Férias:</strong> Dezembro-janeiro, junho-julho</li>
                </ul>
            </div>

            <!-- Guia para Bolsas -->
            <div class="tab-pane fade" id="guia" role="tabpanel">
                <h4><i class="fas fa-map me-2"></i>Guia para Conseguir Bolsas</h4>
                <p>Para estudar na África do Sul, foque em <strong>boa performance acadêmica e interesse em desenvolvimento africano</strong>.</p>
                <ul>
                    <li><strong>Inglês:</strong> IELTS 6.0-6.5 ou TOEFL 80-90</li>
                    <li><strong>Documentos:</strong> Histórico, diploma, carta de motivação</li>
                    <li><strong>Áreas prioritárias:</strong> Medicina, engenharia, ciências sociais</li>
                    <li><strong>Links úteis:</strong>
                        <br><a href="https://www.studysa.org/" target="_blank" class="btn-custom">🎓 Study SA</a>
                        <br><a href="https://www.uct.ac.za/" target="_blank" class="btn-custom">🏆 UCT</a>
                    </li>
                </ul>
            </div>

            <!-- Trabalho -->
            <div class="tab-pane fade" id="trabalho" role="tabpanel">
                <h4><i class="fas fa-briefcase me-2"></i>Trabalho com Visto de Estudante</h4>
                <p><strong>Limitado!</strong> Estudantes podem trabalhar apenas 20h/semana com permissão especial.</p>
                <ul>
                    <li><strong>Permissão:</strong> 20h/semana com autorização do Departamento de Assuntos Internos</li>
                    <li><strong>Salário:</strong> ZAR 50-100/hora</li>
                    <li><strong>Setores:</strong> Tutoria, pesquisa, serviços</li>
                    <li><strong>Pós-graduação:</strong> Oportunidades em setores em crescimento</li>
                    <li><strong>Vantagem:</strong> Gateway para África, economia emergente</li>
                </ul>
            </div>
        </div>
    </div>

   

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function scrollToSection(sectionId) {
            alert('Funcionalidade em desenvolvimento!');
        }
    </script>
<?php require_once 'footer_paises.php'; ?>
</body>
</html>
