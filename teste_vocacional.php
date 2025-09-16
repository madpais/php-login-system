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
    <title>Teste Vocacional - DayDreaming</title>
    
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
            background-color: var(--bg-light);
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

        .test-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 40px;
            margin: 30px 0;
            border-top: 5px solid var(--secondary-color);
        }

        .question-container {
            margin-bottom: 30px;
        }

        .question-text {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 25px;
            line-height: 1.6;
            color: var(--primary-color);
        }

        .options-container {
            margin-left: 20px;
        }

        .option-item {
            margin-bottom: 15px;
            cursor: pointer;
            padding: 15px 20px;
            border-radius: 12px;
            transition: all 0.3s ease;
            border: 2px solid #e0e0e0;
            background: white;
        }

        .option-item:hover {
            background: linear-gradient(135deg, #f0f7ff 0%, #e6f2ff 100%);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(42, 157, 244, 0.2);
        }

        .option-selected {
            background: linear-gradient(135deg, #e6f2ff 0%, #d1e7ff 100%);
            border-color: var(--secondary-color);
            border-left: 5px solid var(--secondary-color);
            box-shadow: 0 5px 15px rgba(42, 157, 244, 0.3);
        }

        .option-letter {
            font-weight: bold;
            margin-right: 15px;
            color: var(--secondary-color);
            font-size: 1.1rem;
        }

        .navigation-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .btn-custom {
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 25px;
            transition: all 0.3s ease;
            border: none;
            font-size: 1rem;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #187bcd 100%);
            color: white;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(42, 157, 244, 0.3);
            color: white;
        }

        .btn-secondary-custom {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            color: white;
        }

        .btn-secondary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(108, 117, 125, 0.3);
            color: white;
        }

        .progress-container {
            margin-bottom: 30px;
        }

        .progress {
            height: 12px;
            border-radius: 10px;
            background: #e9ecef;
        }

        .progress-bar {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #187bcd 100%);
            border-radius: 10px;
        }

        .results-container {
            text-align: center;
            padding: 40px;
        }

        .results-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 30px;
        }

        .career-suggestion {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            text-align: left;
            border-left: 5px solid var(--secondary-color);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .career-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .career-description {
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .career-skills {
            margin-top: 15px;
        }

        .skill-tag {
            display: inline-block;
            background: linear-gradient(135deg, #e6f2ff 0%, #d1e7ff 100%);
            color: var(--primary-color);
            padding: 8px 15px;
            border-radius: 20px;
            margin-right: 8px;
            margin-bottom: 8px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .instruction-box {
            background: linear-gradient(135deg, #e6f2ff 0%, #f0f7ff 100%);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            border-left: 5px solid var(--secondary-color);
        }

        .instruction-title {
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .personality-type {
            font-size: 2rem;
            font-weight: 700;
            color: var(--secondary-color);
            margin: 30px 0;
        }

        .personality-description {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            text-align: left;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .country-recommendation {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border-radius: 15px;
            padding: 25px;
            margin: 30px 0;
            border-left: 5px solid #ffc107;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .country-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #856404;
            margin-bottom: 15px;
        }

        .country-flag {
            font-size: 2rem;
            margin-right: 10px;
        }

        .country-reason {
            margin-top: 15px;
            line-height: 1.6;
        }

        .btn-country {
            background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);
            color: #856404;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
            transition: all 0.3s ease;
        }

        .btn-country:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 193, 7, 0.3);
            color: #856404;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .test-container {
                padding: 25px 20px;
                margin: 20px 0;
            }

            .navigation-buttons {
                flex-direction: column;
                gap: 15px;
            }

            .btn-custom {
                width: 100%;
            }

            .results-title {
                font-size: 2rem;
            }

            .personality-type {
                font-size: 1.5rem;
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
                            <h1 style="color: white; font-size: 2rem; font-weight: 700; margin: 0;">Teste Vocacional</h1>
                        </div>
                    </div>
                    
            

    <?php include 'nav_padronizada.php'; ?>
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="test-container" id="test-container">
                    <div id="start-screen">
                        <div class="instruction-box">
                            <div class="instruction-title">
                                <i class="fas fa-info-circle me-2"></i>Instruções do Teste Vocacional
                            </div>
                            <ul class="mb-0">
                                <li>Este teste foi desenvolvido para ajudar você a descobrir <strong>carreiras e países</strong> que combinam com seu perfil.</li>
                                <li>Responda a todas as perguntas com <strong>honestidade</strong>, escolhendo a opção que melhor representa você.</li>
                                <li>O teste não tem respostas certas ou erradas, apenas diferentes preferências e personalidades.</li>
                                <li>Ao final, você receberá sugestões de <strong>carreiras</strong> e <strong>países</strong> baseadas em suas respostas.</li>
                                <li>O tempo estimado para completar o teste é de <strong>10 a 15 minutos</strong>.</li>
                            </ul>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-primary-custom btn-custom" onclick="startTest()">
                                <i class="fas fa-play me-2"></i>Iniciar Teste
                            </button>
                        </div>
                    </div>
                    
                    <div id="question-screen" style="display: none;">
                        <div class="progress-container">
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 0%" id="progress-bar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="text-center mt-2">
                                <small class="text-muted">Questão <span id="current-question-number">1</span> de <span id="total-questions-display">20</span></small>
                            </div>
                        </div>
                        
                        <div class="question-container">
                            <div class="question-text" id="question-text"></div>
                            <div class="options-container" id="options-container"></div>
                        </div>
                        
                        <div class="navigation-buttons">
                            <button class="btn btn-secondary-custom btn-custom" id="prev-button" onclick="previousQuestion()" disabled>
                                <i class="fas fa-arrow-left me-2"></i>Anterior
                            </button>
                            <button class="btn btn-primary-custom btn-custom" id="next-button" onclick="nextQuestion()">
                                Próximo<i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div id="results-screen" style="display: none;">
                        <div class="results-container">
                            <h2 class="results-title">
                                <i class="fas fa-star me-2"></i>Seu Perfil Profissional
                            </h2>
                            
                            <div class="personality-type" id="personality-type"></div>
                            
                            <div class="personality-description" id="personality-description"></div>
                            
                            <h3 class="mt-4 mb-3">
                                <i class="fas fa-briefcase me-2"></i>Sugestões de Carreiras
                            </h3>
                            <div id="careers-container"></div>
                            
                            <div class="country-recommendation" id="country-recommendation" style="display: none;">
                                <div class="country-title">
                                    <i class="fas fa-globe me-2"></i>País Recomendado
                                </div>
                                <div id="country-content"></div>
                            </div>
                            
                            <div class="mt-4">
                                <button class="btn btn-primary-custom btn-custom me-3" onclick="restartTest()">
                                    <i class="fas fa-redo me-2"></i>Refazer Teste
                                </button>
                                <a href="index.php" class="btn btn-secondary-custom btn-custom">
                                    <i class="fas fa-home me-2"></i>Voltar ao Início
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Perguntas do teste vocacional melhoradas
        const questions = [
            {
                question: "Em qual tipo de ambiente você prefere trabalhar?",
                options: ["A) Em um escritório estruturado e organizado", "B) Ao ar livre ou em campo", "C) Em um estúdio ou ambiente criativo", "D) Em um laboratório ou ambiente de pesquisa"]
            },
            {
                question: "Qual atividade você mais gostaria de fazer no seu tempo livre?",
                options: ["A) Resolver quebra-cabeças e desafios lógicos", "B) Praticar esportes ou atividades físicas", "C) Pintar, desenhar ou tocar um instrumento", "D) Ler sobre ciência e tecnologia"]
            },
            {
                question: "Como você prefere resolver problemas?",
                options: ["A) Analisando dados e seguindo um método lógico", "B) Agindo de forma prática e imediata", "C) Buscando soluções criativas e inovadoras", "D) Pesquisando e estudando o assunto profundamente"]
            },
            {
                question: "Qual habilidade você considera seu maior ponto forte?",
                options: ["A) Capacidade de organização e planejamento", "B) Habilidade manual e coordenação motora", "C) Criatividade e expressão artística", "D) Raciocínio analítico e curiosidade científica"]
            },
            {
                question: "Qual tipo de projeto mais te interessaria?",
                options: ["A) Desenvolver um plano de negócios", "B) Construir ou reparar algo físico", "C) Criar uma obra de arte ou design", "D) Realizar uma pesquisa científica"]
            },
            {
                question: "Como você se comunica melhor?",
                options: ["A) Através de relatórios e documentos estruturados", "B) Demonstrando na prática o que quer dizer", "C) Usando recursos visuais e criativos", "D) Explicando com base em fatos e evidências"]
            },
            {
                question: "O que mais te motiva no trabalho?",
                options: ["A) Alcançar metas e ver resultados mensuráveis", "B) Ver o impacto prático do seu trabalho", "C) Ter liberdade para expressar suas ideias", "D) Descobrir novos conhecimentos e soluções"]
            },
            {
                question: "Qual dessas matérias você mais gostava na escola?",
                options: ["A) Matemática e Administração", "B) Educação Física e Artesanato", "C) Artes e Literatura", "D) Ciências e Física"]
            },
            {
                question: "Como você lida com regras e procedimentos?",
                options: ["A) Sigo rigorosamente as regras estabelecidas", "B) Adapto as regras conforme a necessidade prática", "C) Prefiro criar minhas próprias regras", "D) Questiono as regras para entender sua lógica"]
            },
            {
                question: "Qual dessas frases mais te representa?",
                options: ["A) 'Tudo tem seu lugar e sua função'", "B) 'A prática leva à perfeição'", "C) 'A imaginação é mais importante que o conhecimento'", "D) 'Questionar tudo é o primeiro passo para a sabedoria'"]
            },
            {
                question: "Em qual tipo de equipe você prefere trabalhar?",
                options: ["A) Em equipes com papéis bem definidos", "B) Em equipes que valorizam a ação e o trabalho em conjunto", "C) Em equipes que incentivam a criatividade e a inovação", "D) Em equipes focadas em pesquisa e desenvolvimento"]
            },
            {
                question: "Como você prefere aprender algo novo?",
                options: ["A) Através de cursos estruturados e manuais", "B) Aprendendo na prática, fazendo e errando", "C) Explorando livremente e experimentando", "D) Pesquisando e estudando fontes diversas"]
            },
            {
                question: "Qual dessas atividades você acha mais gratificante?",
                options: ["A) Organizar eventos ou processos", "B) Construir algo com as próprias mãos", "C) Criar algo original e único", "D) Descobrir algo novo através da pesquisa"]
            },
            {
                question: "Qual é o seu maior objetivo profissional?",
                options: ["A) Alcançar uma posição de liderança", "B) Ser reconhecido pela sua habilidade prática", "C) Expressar sua criatividade e visão de mundo", "D) Contribuir para o avanço do conhecimento"]
            },
            {
                question: "Como você descreveria seu estilo de tomada de decisão?",
                options: ["A) Baseado em dados e análise cuidadosa", "B) Intuitivo e focado na ação imediata", "C) Criativo e buscando alternativas inovadoras", "D) Cauteloso e baseado em evidências"]
            },
            {
                question: "Qual tipo de cultura organizacional você prefere?",
                options: ["A) Hierárquica e bem estruturada", "B) Dinâmica e orientada a resultados", "C) Inovadora e criativa", "D) Acadêmica e baseada em pesquisa"]
            },
            {
                question: "O que você valoriza mais em um trabalho?",
                options: ["A) Estabilidade e benefícios", "B) Desafios práticos e resultados tangíveis", "C) Liberdade criativa e expressão pessoal", "D) Aprendizado contínuo e desenvolvimento intelectual"]
            },
            {
                question: "Como você prefere receber feedback?",
                options: ["A) Através de relatórios formais e métricas", "B) Observando os resultados práticos do seu trabalho", "C) Através de discussões criativas e colaborativas", "D) Baseado em análises detalhadas e evidências"]
            },
            {
                question: "Qual dessas situações te deixa mais satisfeito?",
                options: ["A) Completar um projeto complexo dentro do prazo", "B) Ver algo que você construiu sendo usado", "C) Receber reconhecimento por uma ideia criativa", "D) Descobrir algo que ninguém sabia antes"]
            },
            {
                question: "Como você se vê daqui a 10 anos?",
                options: ["A) Em uma posição de liderança em uma grande empresa", "B) Como especialista em uma área técnica específica", "C) Como criador ou artista reconhecido", "D) Como pesquisador ou acadêmico"]
            }
        ];
        
        // Perfis profissionais com países recomendados
        const profiles = {
            "Gestor/Administrativo": {
                description: "Você tem um perfil organizado, estratégico e gosta de planejar e coordenar atividades. Sua força está na capacidade de tomar decisões baseadas em dados e gerenciar pessoas e recursos de forma eficiente.",
                country: {
                    name: "Alemanha",
                    flag: "🇩🇪",
                    reason: "A Alemanha é conhecida por sua excelência em gestão empresarial, engenharia de processos e inovação organizacional. O país oferece programas de MBA de classe mundial e é sede de muitas multinacionais. A cultura alemã valoriza a precisão, organização e eficiência, características ideais para profissionais administrativos.",
                    link: "paises/alemanha.php"
                },
                careers: [
                    {
                        title: "Administração de Empresas",
                        description: "Profissionais de administração planejam, coordenam e dirigem atividades em organizações. Eles podem trabalhar em diversos setores como finanças, marketing, recursos humanos ou operações.",
                        skills: ["Liderança", "Planejamento", "Análise de dados", "Comunicação", "Tomada de decisão"]
                    },
                    {
                        title: "Gestão de Projetos",
                        description: "Gestores de projetos são responsáveis por planejar, executar e monitorar projetos, garantindo que sejam concluídos dentro do prazo, orçamento e escopo definidos.",
                        skills: ["Planejamento", "Organização", "Gestão de tempo", "Resolução de problemas", "Comunicação"]
                    },
                    {
                        title: "Consultoria Empresarial",
                        description: "Consultores empresariais ajudam organizações a melhorar seu desempenho, analisando problemas existentes e desenvolvendo planos para melhorias.",
                        skills: ["Análise crítica", "Resolução de problemas", "Comunicação", "Pensamento estratégico", "Liderança"]
                    }
                ]
            },
            "Prático/Operacional": {
                description: "Você tem um perfil prático, gosta de trabalhar com as mãos e ver resultados tangíveis. Sua força está na capacidade de executar tarefas com precisão e resolver problemas de forma direta e eficiente.",
                country: {
                    name: "Canadá",
                    flag: "🇨🇦",
                    reason: "O Canadá é reconhecido mundialmente por sua excelência em engenharia, tecnologia e inovação prática. O país oferece programas de engenharia de alta qualidade e tem uma forte indústria de tecnologia. A cultura canadense valoriza o trabalho prático, a inovação e a sustentabilidade, perfeito para profissionais operacionais.",
                    link: "paises/canada.php"
                },
                careers: [
                    {
                        title: "Engenharia",
                        description: "Engenheiros aplicam princípios científicos e matemáticos para desenvolver soluções técnicas para problemas práticos. Existem diversas especializações como civil, mecânica, elétrica, entre outras.",
                        skills: ["Raciocínio lógico", "Resolução de problemas", "Trabalho em equipe", "Criatividade", "Conhecimento técnico"]
                    },
                    {
                        title: "Arquitetura e Urbanismo",
                        description: "Arquitetos e urbanistas projetam edifícios, espaços públicos e cidades, combinando funcionalidade, estética e sustentabilidade.",
                        skills: ["Criatividade", "Conhecimento técnico", "Visão espacial", "Gestão de projetos", "Comunicação"]
                    },
                    {
                        title: "Saúde (áreas práticas)",
                        description: "Profissionais de saúde como enfermeiros, fisioterapeutas e técnicos de saúde trabalham diretamente com pacientes, aplicando conhecimentos práticos para cuidar da saúde e bem-estar.",
                        skills: ["Empatia", "Conhecimento técnico", "Resolução de problemas", "Comunicação", "Trabalho em equipe"]
                    }
                ]
            },
            "Criativo/Artístico": {
                description: "Você tem um perfil criativo, gosta de expressar ideias e emoções através de diversas formas. Sua força está na capacidade de pensar fora da caixa e criar soluções originais e inovadoras.",
                country: {
                    name: "França",
                    flag: "🇫🇷",
                    reason: "A França é o berço da arte, design e cultura mundial. Paris é considerada a capital da moda e do design, oferecendo inúmeras oportunidades para profissionais criativos. O país tem uma rica tradição artística e é sede de importantes museus, escolas de arte e empresas criativas.",
                    link: "paises/franca.php"
                },
                careers: [
                    {
                        title: "Design",
                        description: "Designers criam soluções visuais e funcionais para produtos, serviços e comunicações. Existem diversas áreas como design gráfico, de produto, de interiores, entre outras.",
                        skills: ["Criatividade", "Comunicação visual", "Software de design", "Pesquisa", "Trabalho em equipe"]
                    },
                    {
                        title: "Artes e Entretenimento",
                        description: "Profissionais de artes e entretenimento incluem artistas plásticos, músicos, atores, diretores e outros que criam produções artísticas para entreter e inspirar o público.",
                        skills: ["Talento artístico", "Expressão criativa", "Disciplina", "Resiliência", "Comunicação"]
                    },
                    {
                        title: "Publicidade e Marketing",
                        description: "Profissionais de publicidade e marketing criam campanhas para promover produtos, serviços e marcas, combinando criatividade com estratégias de comunicação.",
                        skills: ["Criatividade", "Comunicação", "Pesquisa de mercado", "Trabalho em equipe", "Pensamento estratégico"]
                    }
                ]
            },
            "Cientista/Pesquisador": {
                description: "Você tem um perfil analítico, curioso e gosta de investigar e descobrir novos conhecimentos. Sua força está na capacidade de pensar criticamente, analisar dados e buscar soluções baseadas em evidências.",
                country: {
                    name: "Estados Unidos",
                    flag: "🇺🇸",
                    reason: "Os Estados Unidos são líderes mundiais em pesquisa científica e inovação tecnológica. O país abriga as melhores universidades de pesquisa do mundo, como MIT, Harvard e Stanford. A cultura americana valoriza a inovação, o empreendedorismo e a descoberta científica, oferecendo excelentes oportunidades para pesquisadores.",
                    link: "paises/eua.php"
                },
                careers: [
                    {
                        title: "Ciências da Saúde",
                        description: "Profissionais como médicos, biomédicos e farmacêuticos pesquisam e aplicam conhecimentos científicos para diagnosticar, tratar e prevenir doenças.",
                        skills: ["Raciocínio lógico", "Pesquisa", "Análise de dados", "Ética profissional", "Comunicação"]
                    },
                    {
                        title: "Pesquisa Científica",
                        description: "Pesquisadores científicos investigam fenômenos naturais e sociais, desenvolvendo teorias e aplicações práticas para diversos campos do conhecimento.",
                        skills: ["Pensamento crítico", "Metodologia científica", "Análise de dados", "Persistência", "Comunicação acadêmica"]
                    },
                    {
                        title: "Tecnologia da Informação",
                        description: "Profissionais de TI desenvolvem, implementam e gerenciam sistemas de informação, software e infraestruturas tecnológicas para resolver problemas e otimizar processos.",
                        skills: ["Lógica de programação", "Resolução de problemas", "Aprendizado contínuo", "Trabalho em equipe", "Pensamento analítico"]
                    }
                ]
            }
        };
        
        let currentQuestionIndex = 0;
        let userAnswers = [];
        let userScores = {
            "Gestor/Administrativo": 0,
            "Prático/Operacional": 0,
            "Criativo/Artístico": 0,
            "Cientista/Pesquisador": 0
        };
        
        function startTest() {
            document.getElementById('start-screen').style.display = 'none';
            document.getElementById('question-screen').style.display = 'block';
            document.getElementById('total-questions-display').textContent = questions.length;
            userAnswers = new Array(questions.length).fill(null);
            showQuestion();
        }
        
        function showQuestion() {
            const question = questions[currentQuestionIndex];
            document.getElementById('question-text').textContent = question.question;
            document.getElementById('current-question-number').textContent = currentQuestionIndex + 1;
            
            const optionsContainer = document.getElementById('options-container');
            optionsContainer.innerHTML = '';
            
            question.options.forEach((option, index) => {
                const optionDiv = document.createElement('div');
                optionDiv.className = 'option-item';
                if (userAnswers[currentQuestionIndex] === index) {
                    optionDiv.classList.add('option-selected');
                }
                optionDiv.innerHTML = `<span class="option-letter">${option[0]}</span> ${option.substring(3)}`;
                optionDiv.onclick = () => selectOption(index);
                optionsContainer.appendChild(optionDiv);
            });
            
            // Atualizar botões de navegação
            document.getElementById('prev-button').disabled = currentQuestionIndex === 0;
            document.getElementById('next-button').textContent = currentQuestionIndex === questions.length - 1 ? 'Finalizar' : 'Próximo';
            
            // Atualizar barra de progresso
            const progress = ((currentQuestionIndex + 1) / questions.length) * 100;
            document.getElementById('progress-bar').style.width = `${progress}%`;
        }
        
        function selectOption(optionIndex) {
            userAnswers[currentQuestionIndex] = optionIndex;
            showQuestion(); // Atualiza a exibição para mostrar a opção selecionada
        }
        
        function previousQuestion() {
            if (currentQuestionIndex > 0) {
                currentQuestionIndex--;
                showQuestion();
            }
        }
        
        function nextQuestion() {
            if (currentQuestionIndex < questions.length - 1) {
                currentQuestionIndex++;
                showQuestion();
            } else {
                finishTest();
            }
        }
        
        function finishTest() {
            // Calcular pontuação para cada perfil
            userScores = {
                "Gestor/Administrativo": 0,
                "Prático/Operacional": 0,
                "Criativo/Artístico": 0,
                "Cientista/Pesquisador": 0
            };
            
            // Cada resposta (A, B, C, D) corresponde a um perfil
            userAnswers.forEach((answer, index) => {
                if (answer !== null) {
                    switch (answer) {
                        case 0: // Opção A
                            userScores["Gestor/Administrativo"]++;
                            break;
                        case 1: // Opção B
                            userScores["Prático/Operacional"]++;
                            break;
                        case 2: // Opção C
                            userScores["Criativo/Artístico"]++;
                            break;
                        case 3: // Opção D
                            userScores["Cientista/Pesquisador"]++;
                            break;
                    }
                }
            });
            
            // Encontrar o perfil com maior pontuação
            let topProfile = "";
            let maxScore = 0;
            
            for (const profile in userScores) {
                if (userScores[profile] > maxScore) {
                    maxScore = userScores[profile];
                    topProfile = profile;
                }
            }
            
            // Mostrar tela de resultados
            document.getElementById('question-screen').style.display = 'none';
            document.getElementById('results-screen').style.display = 'block';
            
            // Exibir o perfil principal
            document.getElementById('personality-type').textContent = topProfile;
            document.getElementById('personality-description').textContent = profiles[topProfile].description;
            
            // Exibir sugestões de carreiras
            const careersContainer = document.getElementById('careers-container');
            careersContainer.innerHTML = '';
            
            profiles[topProfile].careers.forEach(career => {
                const careerDiv = document.createElement('div');
                careerDiv.className = 'career-suggestion';
                
                let skillsHTML = '';
                career.skills.forEach(skill => {
                    skillsHTML += `<span class="skill-tag">${skill}</span>`;
                });
                
                careerDiv.innerHTML = `
                    <div class="career-title">${career.title}</div>
                    <div class="career-description">${career.description}</div>
                    <div class="career-skills"><strong>Habilidades necessárias:</strong> ${skillsHTML}</div>
                `;
                
                careersContainer.appendChild(careerDiv);
            });
            
            // Exibir recomendação de país
            const countryRecommendation = document.getElementById('country-recommendation');
            const countryContent = document.getElementById('country-content');
            
            countryContent.innerHTML = `
                <div class="country-title">
                    <span class="country-flag">${profiles[topProfile].country.flag}</span>
                    ${profiles[topProfile].country.name}
                </div>
                <div class="country-reason">
                    <strong>Por que este país é ideal para você:</strong><br>
                    ${profiles[topProfile].country.reason}
                </div>
                <a href="${profiles[topProfile].country.link}" class="btn-country">
                    <i class="fas fa-globe me-2"></i>Explorar ${profiles[topProfile].country.name}
                </a>
            `;
            
            countryRecommendation.style.display = 'block';
        }
        
        function restartTest() {
            currentQuestionIndex = 0;
            userAnswers = [];
            document.getElementById('results-screen').style.display = 'none';
            document.getElementById('start-screen').style.display = 'block';
        }
    </script>

    
</body>
</html>
