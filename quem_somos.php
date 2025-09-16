<?php
require_once 'config.php';

// Iniciar sessão de forma segura
iniciarSessaoSegura();

// Verificar se o usuário está logado
$usuario_logado = isset($_SESSION['usuario_id']);
$usuario_nome = $_SESSION['usuario_nome'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quem Somos - DayDreaming</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="imagens/logo_50px_sem_bgd.png">
    <link rel="shortcut icon" type="image/png" href="imagens/logo_50px_sem_bgd.png">
    <link rel="apple-touch-icon" href="imagens/logo_50px_sem_bgd.png">
    
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

        .hero-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 80px 0;
            text-align: center;
        }

        .hero-title {
            color: var(--primary-color);
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .hero-subtitle {
            color: var(--text-light);
            font-size: 1.3rem;
            margin-bottom: 40px;
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

        .mission-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            border-top: 5px solid var(--secondary-color);
            transition: transform 0.3s ease;
        }

        .mission-card:hover {
            transform: translateY(-10px);
        }

        .mission-card .icon {
            font-size: 4rem;
            color: var(--secondary-color);
            margin-bottom: 20px;
        }

        .mission-card h3 {
            color: var(--primary-color);
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .team-member {
            background: white;
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .team-member:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(42, 157, 244, 0.15);
            border-color: var(--secondary-color);
        }

        .member-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 20px;
            border: 4px solid var(--secondary-color);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .member-name {
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .member-role {
            color: var(--secondary-color);
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .member-description {
            color: var(--text-light);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .member-experience {
            background: var(--bg-light);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .member-experience h6 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 10px;
        }

        .member-experience ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .member-experience li {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-bottom: 5px;
            padding-left: 15px;
            position: relative;
        }

        .member-experience li::before {
            content: '•';
            color: var(--secondary-color);
            position: absolute;
            left: 0;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }

        .social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--secondary-color);
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1.2rem;
        }

        .social-link:hover {
            background: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }

        .development-notice {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border: 2px solid #ffc107;
            border-radius: 15px;
            padding: 30px;
            margin: 40px 0;
            text-align: center;
        }

        .development-notice h4 {
            color: #856404;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .development-notice p {
            color: #856404;
            font-size: 1.1rem;
            margin: 0;
        }

        .stats-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 60px 0;
            margin: 60px 0;
        }

        .stat-item {
            text-align: center;
            padding: 20px;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--accent-color);
        }

        .stat-label {
            font-size: 1.1rem;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.1rem;
            }

            .section-title h2 {
                font-size: 2rem;
            }

            .member-photo {
                width: 120px;
                height: 120px;
            }

            .mission-card {
                padding: 30px 20px;
            }

            .team-member {
                padding: 25px 20px;
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
                            <img src="Imagens/Logo_DayDreaming_trasp 1.png" alt="Logo DayDreaming">
                            <h1 style="color: white; font-size: 2rem; font-weight: 700; margin: 0;">Quem Somos</h1>
                        </div>
                    </div>
    </div>
    </div>
    </div>
    </div>

    <?php include 'nav_padronizada.php'; ?>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <h1 class="hero-title">Conheça a Equipe DayDreaming</h1>
            <p class="hero-subtitle">Jovens apaixonados por democratizar o acesso à educação internacional</p>
        </div>
    </div>

    <!-- Sobre o Projeto -->
    <div class="container my-5">
        <div class="section-title">
            <h2>🎯 Nossa Missão</h2>
            <p>Transformar sonhos de intercâmbio em realidade para estudantes da rede pública</p>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="mission-card">
                    <div class="icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3>Democratizar o Acesso</h3>
                    <p>Oferecemos ferramentas gratuitas e informações essenciais para que qualquer estudante brasileiro possa planejar seus estudos no exterior, independente de sua condição socioeconômica.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="mission-card">
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Construir Comunidade</h3>
                    <p>Criamos uma rede de apoio entre estudantes com objetivos similares, promovendo troca de experiências, dicas valiosas e parcerias de estudo.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="mission-card">
                    <div class="icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h3>Conectar ao Mundo</h3>
                    <p>Facilitamos o acesso a informações sobre universidades, bolsas de estudo e oportunidades educacionais em diversos países ao redor do mundo.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Aviso de Desenvolvimento -->
    <div class="container">
        <div class="development-notice">
            <h4><i class="fas fa-tools me-2"></i>Projeto em Desenvolvimento Contínuo</h4>
            <p>O DayDreaming é um projeto em constante evolução. Para sua plena eficácia, investimos muito tempo em pesquisa, desenvolvimento e experiência no assunto. Estamos sempre aprimorando nossas ferramentas e expandindo nosso conhecimento para melhor servir nossa comunidade.</p>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number">5</div>
                        <div class="stat-label">Integrantes</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number">25+</div>
                        <div class="stat-label">Países</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number">100+</div>
                        <div class="stat-label">Universidades</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number">∞</div>
                        <div class="stat-label">Sonhos</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Equipe -->
    <div class="container my-5">
        <div class="section-title">
            <h2>👥 Nossa Equipe</h2>
            <p>Conheça os jovens por trás do DayDreaming</p>
        </div>

        <div class="row">
            <!-- Integrante 1 -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="team-member">
                    <img src="imagens/placeholder_avatar.png" alt="Integrante 1" class="member-photo">
                    <h4 class="member-name">Kevin Sergio</h4>
                    <p class="member-role">Líder de Projeto</p>
                    <p class="member-description">
                        Estudante apaixonado por tecnologia e educação. Responsável pela coordenação geral do projeto e desenvolvimento da visão estratégica.
                    </p>
                    <div class="member-experience">
                        <h6>Experiência:</h6>
                        <ul>
                            <li>Idealizador do projeto</li>
                            <li>Desenvolvimento Web</li>
                            <li>Pesquisa Educacional</li>
                        </ul>
                    </div>
                    <div class="social-links">
                        <a href="https://github.com/KevinSjr" class="social-link" title="GitHub">
                            <i class="fab fa-github"></i>
                        </a>
                        <a href="#" class="social-link" title="LinkedIn">
                            <i class="fab fa-linkedin"></i>
                        </a>
                        <a href="#" class="social-link" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Integrante 2 -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="team-member">
                    <img src="imagens/placeholder_avatar.png" alt="Integrante 2" class="member-photo">
                    <h4 class="member-name">Sabrina Nery</h4>
                    <p class="member-role">Desenvolvedor Frontend/Backend</p>
                    <p class="member-description">
                        Estudante apaixonado por tecnologia nas áreas de desenvolvimento e hardware. 
                    </p>
                    <div class="member-experience">
                        <h6>Experiência:</h6>
                        <ul>
                            <li>HTML/CSS/JavaScript</li>
                            <li>Design de Interface</li>
                            <li>Responsividade</li>
                        </ul>
                    </div>
                    <div class="social-links">
                        <a href="#" class="social-link" title="GitHub">
                            <i class="fab fa-github"></i>
                        </a>
                        <a href="#" class="social-link" title="LinkedIn">
                            <i class="fab fa-linkedin"></i>
                        </a>
                        <a href="#" class="social-link" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Integrante 3 -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="team-member">
                    <img src="imagens/placeholder_avatar.png" alt="Integrante 3" class="member-photo">
                    <h4 class="member-name">Marcos Pais</h4>
                    <p class="member-role">Desenvolvedor LABUBU</p>
                    <p class="member-description">
                        Labubu focado em Banco de Dados.
                    </p>
                    <div class="member-experience">
                        <h6>Experiência:</h6>
                        <ul>
                            <li>PHP/MySQL</li>
                            <li>Arquitetura de Sistemas</li>
                            <li>Segurança Web</li>
                        </ul>
                    </div>
                    <div class="social-links">
                        <a href="#" class="social-link" title="GitHub">
                            <i class="fab fa-github"></i>
                        </a>
                        <a href="#" class="social-link" title="LinkedIn">
                            <i class="fab fa-linkedin"></i>
                        </a>
                        <a href="#" class="social-link" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Integrante 4 -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="team-member">
                    <img src="imagens/placeholder_avatar.png" alt="Integrante 4" class="member-photo">
                    <h4 class="member-name">José Cláudio</h4>
                    <p class="member-role">Pesquisador de Conteúdo</p>
                    <p class="member-description">
                        Responsável por pesquisar e manter informações atualizadas sobre universidades, bolsas de estudo e processos de aplicação.
                    </p>
                    <div class="member-experience">
                        <h6>Experiência:</h6>
                        <ul>
                            <li>Pesquisa Acadêmica</li>
                            <li>Análise de Dados</li>
                            <li>Comunicação Científica</li>
                        </ul>
                    </div>
                    <div class="social-links">
                        <a href="#" class="social-link" title="GitHub">
                            <i class="fab fa-github"></i>
                        </a>
                        <a href="#" class="social-link" title="LinkedIn">
                            <i class="fab fa-linkedin"></i>
                        </a>
                        <a href="#" class="social-link" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Integrante 5 -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="team-member">
                    <img src="imagens/placeholder_avatar.png" alt="Integrante 5" class="member-photo">
                    <h4 class="member-name">Bruno Tuts Tuts</h4>
                    <p class="member-role">Não fez nada</p>
                    <p class="member-description">
                        Contribuições para o projeto: Nenhuma.
                    </p>
                    <div class="member-experience">
                        <h6>Experiência:</h6>
                        <ul>
                            <li>Incompetente</li>
                            <li>Enganou a banca</li>
                            <li>A espera de sua varoa</li>
                        </ul>
                    </div>
                    <div class="social-links">
                        <a href="#" class="social-link" title="GitHub">
                            <i class="fab fa-github"></i>
                        </a>
                        <a href="#" class="social-link" title="LinkedIn">
                            <i class="fab fa-linkedin"></i>
                        </a>
                        <a href="#" class="social-link" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animação suave ao rolar
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observar elementos para animação
            document.querySelectorAll('.team-member, .mission-card').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(el);
            });
        });
    </script>
    <?php require_once __DIR__ . '/footer.php'; ?>
</body>
</html>
