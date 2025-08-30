<?php
require_once 'config.php';
iniciarSessaoSegura();

// Verificar se o usuário está logado
$usuario_logado = isset($_SESSION['usuario_id']);
$usuario_nome = $usuario_logado ? ($_SESSION['usuario_nome'] ?? '') : '';
$usuario_login = $usuario_logado ? ($_SESSION['usuario_login'] ?? '') : '';

// Conectar ao banco para estatísticas (se logado)
$estatisticas = null;
if ($usuario_logado) {
    try {
        $pdo = conectarBD();
        
        // Buscar estatísticas do usuário
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_testes,
                AVG(CASE 
                    WHEN st.tipo_prova = 'sat' THEN (st.acertos / 120) * 100
                    WHEN st.tipo_prova = 'toefl' THEN (st.acertos / 100) * 100
                    WHEN st.tipo_prova = 'ielts' THEN (st.acertos / 40) * 100
                    WHEN st.tipo_prova = 'gre' THEN (st.acertos / 80) * 100
                    ELSE (st.acertos / 120) * 100
                END) as media_pontuacao
            FROM sessoes_teste st
            WHERE st.usuario_id = ? AND st.status = 'finalizado'
        ");
        $stmt->execute([$_SESSION['usuario_id']]);
        $estatisticas = $stmt->fetch();
    } catch (Exception $e) {
        // Silenciar erro de conexão
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DayDreaming - Sistema de Simulados Internacionais</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="imagens/logo_50px_sem_bgd.png">
    <link rel="shortcut icon" type="image/png" href="imagens/logo_50px_sem_bgd.png">
    <link rel="apple-touch-icon" href="imagens/logo_50px_sem_bgd.png">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    
    <style>
        /* Estilos base */
        body {
            overflow-x: hidden;
        }

        .banner-section {
            width: 100%;
            height: auto;
            margin-top: -8%;
            text-align: center;
            
        }
        
        .navbutton {
            color: white;
            font-size: clamp(10px, 2vw, 20px);
            text-align: center;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .navbutton:hover {
            background-color: rgba(255,255,255,0.1);
            transform: translateY(-2px);
        }
        
        .text1 {
            font-size: clamp(10px, 2vw, 20px);
        }
        
        .title1 {
            color: #03254c;
            font-weight: bold;
            font-size: clamp(20px, 4vw, 40px);
        }
        
        .btn {
            margin: 20px auto;
            display: block;
            font-size: clamp(12px, 1.5vw, 18px);
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        img {
            max-width: 100%;
            height: auto;
        }
        
        /* Cabeçalho responsivo */
        .header-container {
            background-color: #03254c;
            padding: 20px 0;
            margin-top: 10px; /* Espaço para o header de status */
        }
        
        .logo-container img {
            max-width: 100%;
            height: auto;
        }
        
        /* Status do usuário */
        .user-status {
            color: white;
            font-size: 14px;
            text-align: center;
        }
        
        .user-stats {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 10px;
            margin-top: 10px;
        }
        
        /* Espaçamento de seções */
        .section-spacing {
            
            margin-bottom: 2%;
        }
        
        /* Cards de exames */
        .exam-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .exam-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        /* Botões de navegação principal */
        .nav-button {
            position: relative;
            overflow: hidden;
        }

        .nav-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(42, 157, 244, 0.3);
        }

        .nav-button:active {
            transform: translateY(-2px);
        }

        .nav-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .nav-button:hover::before {
            left: 100%;
        }

        /* Cards de funcionalidades */
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(42, 157, 244, 0.1);
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(42, 157, 244, 0.15);
            border-color: rgba(42, 157, 244, 0.3);
        }

        .image-container {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            background: #f8f9fa;
            padding: 15px;
            text-align: center;
        }

        .feature-image {
            max-height: 200px;
            width: auto;
            transition: transform 0.3s ease;
        }

        .feature-card:hover .feature-image {
            transform: scale(1.05);
        }

        .feature-btn {
            background: linear-gradient(135deg, #2a9df4 0%, #187bcd 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(42, 157, 244, 0.3);
        }

        .feature-btn:active {
            transform: translateY(0);
        }

        /* Seção da comunidade */
        .community-image-container {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .community-image {
            transition: transform 0.3s ease;
        }

        .community-image-container:hover .community-image {
            transform: scale(1.05);
        }

        .community-content {
            padding: 20px;
        }

        .feature-item {
            font-size: 16px;
            color: #495057;
            display: flex;
            align-items: center;
        }

        .feature-item i {
            width: 20px;
            text-align: center;
        }

        /* Cards da missão */
        .mission-card {
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 25px 15px;
            text-align: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .mission-card:hover {
            transform: translateY(-5px);
            background: rgba(255,255,255,0.15);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .about-content {
            position: relative;
        }

        .about-content::before {
            content: '';
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: #fd79a8;
            border-radius: 2px;
        }

        /* Espaçamento entre navegação e banner */
        .nav-banner-spacing {
            
            height: 60px;
            background: linear-gradient(to bottom, rgba(42, 157, 244, 0.05) 0%, transparent 100%);
        }
        
        /* Media queries */
        @media (max-width: 768px) {
            .header-container {
                padding: 10px 0;
            }

            .section-spacing {
                margin-top: 10%;
                margin-bottom: 10%;
            }

            .reverse-columns-mobile {
                flex-direction: column-reverse;
            }

            .nav-button {
                min-height: 80px !important;
                margin-bottom: 5px;
            }

            .nav-button .text1 {
                font-size: 14px !important;
            }

            .nav-button i {
                font-size: 20px !important;
            }

            .feature-image {
                max-height: 150px;
            }

            .mission-card {
                margin-bottom: 15px;
            }

            .title1 {
                font-size: 1.8rem !important;
            }

            .nav-banner-spacing {
                height: 40px;
            }
        }

        @media (max-width: 576px) {
            .btn {
                width: 100%;
            }

            .nav-button {
                min-height: 70px !important;
                padding: 10px !important;
            }

            .nav-button .text1 {
                font-size: 12px !important;
            }

            .nav-button i {
                font-size: 18px !important;
            }

            .feature-card {
                padding: 20px;
            }

            .community-content {
                padding: 10px;
            }

            .title1 {
                font-size: 1.5rem !important;
            }

            .text1 {
                font-size: 14px !important;
            }
        }
    </style>
</head>
<body>
    <?php include 'header_status.php'; ?>

   
  

    <!-- Menu de navegação principal -->
    <div class="container-fluid">
        <div class="row">
            <!-- Botão Quem Somos -->
            <div class="col-lg-3 col-md-3 col-sm-6 col-6 exam-card nav-button"
                 style="background: linear-gradient(135deg, #2a9df4 0%, #187bcd 100%); min-height: 100px; border: 3px solid white; padding: 15px; cursor: pointer; transition: all 0.3s ease;"
                 onclick="scrollToSection('quem-somos')">
                <div class="d-flex flex-column justify-content-center h-100 text-center">
                    <i class="fas fa-users mb-2" style="color: white; font-size: 24px;"></i>
                    <p class="text1 mb-0" style="color: white; font-weight: 600; font-size: 16px;">Quem Somos</p>
                    <small style="color: rgba(255,255,255,0.8);">Nossa missão</small>
                </div>
            </div>

            <!-- Botão Teste Vocacional -->
            <div class="col-lg-3 col-md-3 col-sm-6 col-6 exam-card nav-button"
                 style="background: linear-gradient(135deg, #2a9df4 0%, #187bcd 100%); min-height: 100px; border: 3px solid white; padding: 15px; cursor: pointer; transition: all 0.3s ease;"
                 onclick="alert('Funcionalidade em desenvolvimento! Em breve você poderá descobrir qual curso combina mais com seu perfil.')">
                <div class="d-flex flex-column justify-content-center h-100 text-center">
                    <i class="fas fa-compass mb-2" style="color: white; font-size: 24px;"></i>
                    <p class="text1 mb-0" style="color: white; font-weight: 600; font-size: 16px;">Teste Vocacional</p>
                    <small style="color: rgba(255,255,255,0.8);">Descubra seu curso</small>
                </div>
            </div>

            <!-- Botão Simulador Prático -->
            <div class="col-lg-3 col-md-3 col-sm-6 col-6 exam-card nav-button"
                 style="background: linear-gradient(135deg, #2a9df4 0%, #187bcd 100%); min-height: 100px; border: 3px solid white; padding: 15px; cursor: pointer; transition: all 0.3s ease;"
                 onclick="<?php echo $usuario_logado ? "location.href='simulador_provas.php'" : "location.href='login.php'"; ?>">
                <div class="d-flex flex-column justify-content-center h-100 text-center">
                    <i class="fas fa-graduation-cap mb-2" style="color: white; font-size: 24px;"></i>
                    <p class="text1 mb-0" style="color: white; font-weight: 600; font-size: 16px;">Simulador Prático</p>
                    <small style="color: rgba(255,255,255,0.8);">Pratique para os exames</small>
                </div>
            </div>

            <!-- Botão Comunidade -->
            <div class="col-lg-3 col-md-3 col-sm-6 col-6 exam-card nav-button"
                 style="background: linear-gradient(135deg, #2a9df4 0%, #187bcd 100%); min-height: 100px; border: 3px solid white; padding: 15px; cursor: pointer; transition: all 0.3s ease;"
                 onclick="scrollToSection('comunidade')">
                <div class="d-flex flex-column justify-content-center h-100 text-center">
                    <i class="fas fa-comments mb-2" style="color: white; font-size: 24px;"></i>
                    <p class="text1 mb-0" style="color: white; font-weight: 600; font-size: 16px;">Comunidade</p>
                    <small style="color: rgba(255,255,255,0.8);">Conecte-se com outros</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Espaçamento entre navegação e banner -->
    <div class="nav-banner-spacing"></div>

    <!-- Seção banner -->
     <section class="banner-section">
        <img src="Imagens/primeira_sessão.png" alt="Primeira sessão" class="img-fluid banner_s1">
    </section>

    <!-- Seção de exames e bolsas -->
    <div class="container-fluid section-spacing">
        <div class="row text-center">
            <div class="col-md-4 col-sm-4 col-12 mb-2">
                <p class="text1" style="color: #929DE3;">Proficiência em Idiomas</p>
            </div>
            <div class="col-md-4 col-sm-4 col-12 mb-2">
                <p class="text1" style="color: #187bcd;">Exames Padronizados</p>
            </div>
            <div class="col-md-4 col-sm-4 col-12 mb-2">
                <p class="text1" style="color: #1167b1;">Principais Programa de Bolsas</p>
            </div>
        </div>
        <div class="row">
            <!-- Idiomas -->    
            <div class="col-lg-3 col-md-3 col-sm-6 col-6 exam-card" style="background-color: #929DE3; min-height: 80px; border: 3px solid white; padding: 10px;" onclick="<?php echo $usuario_logado ? "location.href='executar_teste.php?tipo=toefl'" : "location.href='login.php'"; ?>">
                <p class="text1" style="color: white;">TOEFL | IELTS <br>
                    <span style="display: block; text-align: right;">Inglês</span>
                </p>
            </div>
            
            <div class="col-lg-3 col-md-3 col-sm-6 col-6 exam-card" style="background-color: #929DE3; min-height: 80px; border: 3px solid white; padding: 10px;">
                <p class="text1" style="color: white;">DELF | DALF <br>
                    <span style="display: block; text-align: right;">Francês</span>
                </p>
            </div>
            
            <div class="col-lg-3 col-md-3 col-sm-6 col-6 exam-card" style="background-color: #929DE3; min-height: 80px; border: 3px solid white; padding: 10px;">
                <p class="text1" style="color: white;">TestDaF/DSH <br>
                    <span style="display: block; text-align: right;">Alemão</span>
                </p>
            </div>
            
            <div class="col-lg-3 col-md-3 col-sm-6 col-6 exam-card" style="background-color: #929DE3; min-height: 80px; border: 3px solid white; padding: 10px;">
                <p class="text1" style="color: white;">JLPT<br>
                    <span style="display: block; text-align: right;">Japonês</span>
                </p>
            </div>
            
            <!-- Exames -->    
            <div class="col-lg-3 col-md-3 col-sm-6 col-6 exam-card" style="background-color: #187bcd; min-height: 80px; border: 3px solid white; padding: 10px;" onclick="<?php echo $usuario_logado ? "location.href='executar_teste.php?tipo=sat'" : "location.href='login.php'"; ?>">
                <p class="text1" style="color: white;">SAT <br>
                    <span style="display: block; text-align: right;">EUA</span>
                </p>
            </div>
            
            <div class="col-lg-3 col-md-3 col-sm-6 col-6 exam-card" style="background-color: #187bcd; min-height: 80px; border: 3px solid white; padding: 10px;">
                <p class="text1" style="color: white;">ACT <br>
                    <span style="display: block; text-align: right;">EUA</span>
                </p>
            </div>
            
            <div class="col-lg-3 col-md-3 col-sm-6 col-6 exam-card" style="background-color: #187bcd; min-height: 80px; border: 3px solid white; padding: 10px;" onclick="<?php echo $usuario_logado ? "location.href='executar_teste.php?tipo=gre'" : "location.href='login.php'"; ?>">
                <p class="text1" style="color: white;">GRE <br>
                    <span style="display: block; text-align: right;">EUA</span>
                </p>
            </div>
            
            <div class="col-lg-3 col-md-3 col-sm-6 col-6 exam-card" style="background-color: #187bcd; min-height: 80px; border: 3px solid white; padding: 10px;">
                <p class="text1" style="color: white;">TesteAS <br>
                    <span style="display: block; text-align: right;">Alemanha</span>
                </p>
            </div>
            
            <!-- Bolsas -->    
            <div class="col-lg-3 col-md-3 col-sm-6 col-6 exam-card" style="background-color: #1167b1; min-height: 80px; border: 3px solid white; padding: 10px;">
                <p class="text1" style="color: white;">MEXT
                    <span style="display: block; text-align: right;">Japão</span>
                </p>
            </div>
            
            <div class="col-lg-3 col-md-3 col-sm-6 col-6 exam-card" style="background-color: #1167b1; min-height: 80px; border: 3px solid white; padding: 10px;">
                <p class="text1" style="color: white;">Fullbright
                    <span style="display: block; text-align: right;">EUA</span>
                </p>
            </div>
            
            <div class="col-lg-3 col-md-3 col-sm-6 col-6 exam-card" style="background-color: #1167b1; min-height: 80px; border: 3px solid white; padding: 10px;">
                <p class="text1" style="color: white;">CSC
                    <span style="display: block; text-align: right;">China</span>
                </p>
            </div>
            
            <div class="col-lg-3 col-md-3 col-sm-6 col-6 exam-card" style="background-color: #1167b1; min-height: 80px; border: 3px solid white; padding: 10px;">
                <p class="text1" style="color: white;">DAAD
                    <span style="display: block; text-align: right;">Alemanha</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Seção Teste Vocacional -->
    <div id="teste-vocacional" class="container-fluid section-spacing">
        <div class="row">
            <div class="col-lg-1 col-md-1 d-none d-md-block"></div>
            <div class="col-lg-5 col-md-5 col-sm-12 col-12 mb-4">
                <p class="title1">Descubra sua vocação</p>
                <p class="text1">De medicina à engenharia, de artes à diplomacia — o mundo te espera. Descubra qual caminho internacional combina com você.</p>
                <button type="button" class="btn btn-primary btn-lg" onclick="alert('Funcionalidade em desenvolvimento!')">Faça já</button>
            </div>
            <div class="col-lg-1 col-md-1 d-none d-md-block"></div>
            <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                <img src="Imagens/segunda sessão.png" alt="Segunda sessão" class="img-fluid">
            </div>
        </div>
    </div>

    <!-- Seção Bolsas de Estudo -->
    <div class="container-fluid section-spacing">
        <div class="row reverse-columns-mobile">
            <div class="col-lg-1 col-md-1 d-none d-md-block"></div>
            <div class="col-lg-5 col-md-5 col-sm-12 col-12 mb-4">
                <img src="Imagens/image 52.png" alt="Bolsas de estudo" class="img-fluid">
            </div>
            <div class="col-lg-1 col-md-1 d-none d-md-block"></div>
            <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                <p class="text1">Navegue por países com programas de bolsas de estudo internacionais e conheça, de forma prática e visual, as opções disponíveis para você.
                Cada país conta com uma página exclusiva que reúne informações essenciais, como tipos de bolsas oferecidas, requisitos, universidades parceiras, dicas culturais e dados sobre o sistema educacional local.</p>
                <button type="button" class="btn btn-primary btn-lg feature-btn" onclick="location.href='pesquisa_por_pais.php'">
                    <i class="fas fa-globe me-2"></i>Explore
                </button>
            </div>
        </div>
    </div>

    <!-- Seção Ranking de Universidades -->
    <div class="container-fluid section-spacing">
        <div class="row">
            <div class="col-lg-1 col-md-1 d-none d-md-block"></div>
            <div class="col-lg-5 col-md-5 col-sm-12 col-12 mb-4">
                <p class="title1">Ranking de universidades</p>
                <p class="text1">Veja a melhor para o seu perfil</p>
                <a href="ranking.php" class="btn btn-primary btn-lg">Checar rank</a>
            </div>
            <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                <img src="imagens/image36.png" alt="Ranking de universidades" class="img-fluid">
            </div>
            <div class="col-lg-1 col-md-1 d-none d-md-block"></div>
        </div>
    </div>

    <!-- Seção Simuladores e Ferramentas -->
    <div class="container-fluid section-spacing">
        <div class="row">
            <div class="col-lg-1 col-md-1 d-none d-md-block"></div>
            <div class="col-lg-4 col-md-5 col-sm-12 col-12 mb-5">
                <div class="feature-card h-100">
                    <p class="title1">Simulador de Prova</p>
                    <p class="text1">Faça simulados para provas de diversos países e prepare-se para conquistar sua vaga internacional</p>
                    <div class="image-container mb-3">
                        <img src="Imagens/image 37 (1).png" alt="Simulador de Prova" class="img-fluid feature-image">
                    </div>
                    <button type="button" class="btn btn-primary btn-lg btn-block feature-btn" onclick="location.href='<?php echo $usuario_logado ? 'simulador_provas.php' : 'login.php'; ?>'">
                        <i class="fas fa-play-circle me-2"></i>
                        <?php echo $usuario_logado ? 'Realizar testes' : 'Entrar para testar'; ?>
                    </button>
                </div>
            </div>
            <div class="col-lg-2 col-md-1 d-none d-md-block"></div>
            <div class="col-lg-4 col-md-5 col-sm-12 col-12 mb-5">
                <div class="feature-card h-100">
                    <p class="title1">Conversor de GPA</p>
                    <p class="text1">Insira suas notas do ensino médio e calcule seu GPA para universidades internacionais</p>
                    <div class="image-container mb-3">
                        <img src="Imagens/Rectangle 73.png" alt="Conversor de GPA" class="img-fluid feature-image">
                    </div>
                    <a href="calculadora.php" class="btn btn-primary btn-lg btn-block feature-btn">
                        <i class="fas fa-calculator me-2"></i>
                        Converta suas notas
                    </a>
                </div>
            </div>
            <div class="col-lg-1 col-md-1 d-none d-md-block"></div>
        </div>
        <div class="row section-spacing">
            <div class="col-lg-1 col-md-1 d-none d-md-block"></div>
            <div class="col-lg-4 col-md-5 col-sm-12 col-12 mb-5">
                <div class="feature-card h-100">
                    <p class="title1">Simulador de Entrevista</p>
                    <p class="text1">Simule como seria uma entrevista para universidades dos seus sonhos e ganhe confiança</p>
                    <div class="image-container mb-3">
                        <img src="Imagens/image 38.png" alt="Simulador de Entrevista" class="img-fluid feature-image">
                    </div>
                    <button type="button" class="btn btn-primary btn-lg btn-block feature-btn" onclick="alert('Funcionalidade em desenvolvimento! Em breve você poderá praticar entrevistas universitárias.')">
                        <i class="fas fa-microphone me-2"></i>
                        Realizar entrevista
                    </button>
                </div>
            </div>
            <div class="col-lg-2 col-md-1 d-none d-md-block"></div>
            <div class="col-lg-4 col-md-5 col-sm-12 col-12 mb-5">
                <div class="feature-card h-100">
                    <p class="title1">Descoberta Final</p>
                    <p class="text1">Com base nas notas, entrevista e provas, veja suas chances de aprovação nas universidades</p>
                    <div class="image-container mb-3">
                        <img src="Imagens/image 39.png" alt="Descoberta Final" class="img-fluid feature-image">
                    </div>
                    <button type="button" class="btn btn-primary btn-lg btn-block feature-btn" onclick="alert('Funcionalidade em desenvolvimento! Em breve você poderá ver suas chances de aprovação.')">
                        <i class="fas fa-trophy me-2"></i>
                        Ver resultado
                    </button>
                </div>
            </div>
            <div class="col-lg-1 col-md-1 d-none d-md-block"></div>
        </div>
    </div>

    <!-- Seção Fórum Comunitário -->
    <div id="comunidade" class="container-fluid section-spacing" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 60px 0;">
        <div class="row reverse-columns-mobile align-items-center">
            <div class="col-lg-1 col-md-1 d-none d-md-block"></div>
            <div class="col-lg-5 col-md-5 col-sm-12 col-12 mb-4">
                <div class="community-image-container">
                    <img src="Imagens/image 51.png" alt="Fórum Comunitário" class="img-fluid community-image">
                </div>
            </div>
            <div class="col-lg-1 col-md-1 d-none d-md-block"></div>
            <div class="col-lg-5 col-md-5 col-sm-12 col-12">
                <div class="community-content">
                    <h2 class="title1 mb-4" style="color: #03254c;">
                        <i class="fas fa-users me-3" style="color: #2a9df4;"></i>
                        Nossa Comunidade
                    </h2>
                    <p class="text1 mb-4" style="font-size: 18px; line-height: 1.6;">
                        <strong>Conecte-se com estudantes que compartilham o mesmo sonho!</strong>
                        Nosso fórum foi criado especialmente para jovens da rede pública que buscam oportunidades de estudar no exterior.
                    </p>
                    <div class="community-features mb-4">
                        <div class="feature-item mb-3">
                            <i class="fas fa-comments" style="color: #2a9df4; margin-right: 10px;"></i>
                            <span>Troque experiências e dicas valiosas</span>
                        </div>
                        <div class="feature-item mb-3">
                            <i class="fas fa-lightbulb" style="color: #2a9df4; margin-right: 10px;"></i>
                            <span>Descubra oportunidades de bolsas</span>
                        </div>
                        <div class="feature-item mb-3">
                            <i class="fas fa-handshake" style="color: #2a9df4; margin-right: 10px;"></i>
                            <span>Encontre mentores e parceiros de estudo</span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-lg feature-btn" onclick="<?php echo $usuario_logado ? "location.href='forum.php'" : "alert('Faça login para acessar o fórum e conectar-se com nossa comunidade!')"; ?>">
                        <i class="fas fa-arrow-right me-2"></i>
                        <?php echo $usuario_logado ? 'Acessar Fórum' : 'Entrar na Comunidade'; ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção Quem Somos -->
    <div id="quem-somos" class="container-fluid section-spacing" style="background: linear-gradient(135deg, #03254c 0%, #2a9df4 100%); padding: 80px 0; color: white;">
        <div class="row">
            <div class="col-lg-2 col-md-1 d-none d-md-block"></div>
            <div class="col-lg-8 col-md-10 col-sm-12 col-12 text-center">
                <div class="about-content">
                    <h2 class="title1 mb-4" style="color: white; font-size: 2.5rem;">
                        <i class="fas fa-heart me-3" style="color: #fd79a8;"></i>
                        Quem Somos
                    </h2>
                    <p class="text1 mb-4" style="font-size: 20px; line-height: 1.8; color: rgba(255,255,255,0.95);">
                        <strong>Somos mais que uma plataforma - somos um movimento de transformação social!</strong>
                    </p>
                    <p class="text1 mb-5" style="font-size: 18px; line-height: 1.7; color: rgba(255,255,255,0.9);">
                        O DayDreaming nasceu com uma missão clara: <strong>democratizar o acesso ao ensino superior internacional</strong>,
                        especialmente para jovens da rede pública que enfrentam barreiras de informação e recursos.
                        Acreditamos que todo estudante, independente de sua origem socioeconômica, merece a chance de realizar
                        o sonho de estudar no exterior.
                    </p>

                    <div class="mission-cards row mb-5">
                        <div class="col-md-4 mb-3">
                            <div class="mission-card">
                                <i class="fas fa-graduation-cap mb-3" style="font-size: 2rem; color: #fd79a8;"></i>
                                <h5 style="color: white;">Educação Acessível</h5>
                                <p style="color: rgba(255,255,255,0.8); font-size: 14px;">Simulados gratuitos e informações sobre bolsas de estudo</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="mission-card">
                                <i class="fas fa-users mb-3" style="font-size: 2rem; color: #fd79a8;"></i>
                                <h5 style="color: white;">Comunidade Forte</h5>
                                <p style="color: rgba(255,255,255,0.8); font-size: 14px;">Rede de apoio entre estudantes com objetivos similares</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="mission-card">
                                <i class="fas fa-globe mb-3" style="font-size: 2rem; color: #fd79a8;"></i>
                                <h5 style="color: white;">Oportunidades Globais</h5>
                                <p style="color: rgba(255,255,255,0.8); font-size: 14px;">Informações sobre universidades e programas mundiais</p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($usuario_logado): ?>
                    <div class="mt-4 p-4" style="background: rgba(3, 37, 76, 0.1); border-radius: 15px;">
                        <h5 style="color: #03254c;">Bem-vindo de volta, <?php echo htmlspecialchars($usuario_nome); ?>! 🎓</h5>
                        <p class="text1">Continue sua jornada de preparação para estudar no exterior.</p>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <a href="simulador_provas.php" class="btn btn-primary btn-block">Fazer Simulados</a>
                            </div>
                            <div class="col-md-4">
                                <a href="historico_provas.php" class="btn btn-outline-primary btn-block">Ver Histórico</a>
                            </div>
                            <div class="col-md-4">
                                <a href="logout.php" class="btn btn-outline-secondary btn-block">Sair</a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="mt-4">
                        <p class="text1"><strong>Faça login para acessar todas as funcionalidades!</strong></p>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <a href="login.php" class="btn btn-primary btn-lg btn-block">Entrar</a>
                            </div>
                            <div class="col-md-6">
                                <a href="cadastro.php" class="btn btn-outline-primary btn-lg btn-block">Criar Conta</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-2 col-md-1 d-none d-md-block"></div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="container-fluid section-spacing text-center" style="background-color: #03254c; color: white; padding: 40px 0;">
        <img src="Imagens/Logo_DayDreaming_trasp 1.png" alt="Logo DayDreaming" class="img-fluid" style="max-width: 200px;">
        <p class="text1 mt-3">© 2024 DayDreaming - Sua jornada para educação internacional começa aqui!</p>
        <p class="text1">Todos os direitos reservados</p>

        <?php if ($usuario_logado): ?>
            <div class="mt-3">
                <small>Logado como: <?php echo htmlspecialchars($usuario_nome); ?> | <a href="logout.php" style="color: #2a9df4;">Sair</a></small>
            </div>
        <?php endif; ?>
    </footer>

    <script>
        // Função para scroll suave para seções
        function scrollToSection(sectionId) {
            const element = document.getElementById(sectionId);
            if (element) {
                element.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }

        // Adicionar efeitos de hover nos cards de exames
        document.addEventListener('DOMContentLoaded', function() {
            const examCards = document.querySelectorAll('.exam-card');
            examCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                    this.style.boxShadow = '0 8px 16px rgba(0,0,0,0.2)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'none';
                });
            });
        });

        // Mostrar mensagem de boas-vindas para usuários logados
        <?php if ($usuario_logado && isset($_GET['login']) && $_GET['login'] === 'success'): ?>
            setTimeout(function() {
                alert('Bem-vindo de volta, <?php echo htmlspecialchars($usuario_nome); ?>! 🎓\n\nVocê pode acessar os simulados e ver seu histórico de provas.');
            }, 1000);
        <?php endif; ?>
    </script>
</body>
</html>
