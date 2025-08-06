<?php
session_start();

// Verificar se o usuário está logado
$usuario_logado = isset($_SESSION['usuario_id']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/style.css">
    <title>Testes Internacionais - Guia Completo</title>
    <style>
        .content-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            backdrop-filter: blur(20px);
            box-shadow: 0 0 36px 1px rgba(0, 0, 0, 0.1);
            margin-top: 1rem;
            margin-bottom: 1rem;
        }
        
        .header-section {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .header-section h1 {
            color: var(--primary-color);
            font-size: 2.2rem;
            margin-bottom: 0.8rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        
        .header-section p {
            font-size: 1rem;
            line-height: 1.5;
            color: var(--color);
            opacity: 0.8;
        }
        
        .test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .test-card {
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid hsla(0, 0%, 45%, 0.3);
            border-radius: 12px;
            padding: 1.2rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(15px);
        }
        
        .test-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border-color: var(--primary-color);
        }
        
        .test-card h3 {
            color: var(--primary-color);
            font-size: 1.3rem;
            margin-bottom: 0.4rem;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 0.4rem;
        }
        
        .test-card .test-subtitle {
            font-size: 0.85rem;
            color: var(--color);
            opacity: 0.7;
            margin-bottom: 0.8rem;
            font-style: italic;
        }
        
        .test-info {
            margin-bottom: 0.8rem;
        }
        
        .test-info strong {
            color: var(--primary-color);
            display: block;
            margin-bottom: 0.3rem;
        }
        
        .test-info p {
            margin: 0;
            line-height: 1.4;
            font-size: 0.9rem;
        }
        
        .country-section {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 2px solid var(--primary-color);
        }
        
        .country-section h2 {
            color: var(--primary-color);
            font-size: 1.8rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .country-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.2rem;
        }
        
        .country-card {
            background: rgba(24, 123, 205, 0.1);
            border: 1px solid var(--primary-color);
            border-radius: 10px;
            padding: 1rem;
            transition: all 0.3s ease;
        }
        
        .country-card:hover {
            background: rgba(24, 123, 205, 0.2);
            transform: scale(1.02);
        }
        
        .country-card h4 {
            color: var(--primary-color);
            font-size: 1.2rem;
            margin-bottom: 0.6rem;
            display: flex;
            align-items: center;
        }
        
        .country-card h4::before {
            content: "🌍";
            margin-right: 0.5rem;
            font-size: 1.2rem;
        }
        
        .country-card ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .country-card li {
            padding: 0.25rem 0;
            border-bottom: 1px solid rgba(24, 123, 205, 0.2);
            font-size: 0.85rem;
        }
        
        .country-card li:last-child {
            border-bottom: none;
        }
        
        .tips-section {
            margin-top: 2rem;
            padding: 1.5rem;
            background: rgba(24, 123, 205, 0.1);
            border-radius: 12px;
            border-left: 5px solid var(--primary-color);
        }
        
        .tips-section h3 {
            color: var(--primary-color);
            font-size: 1.4rem;
            margin-bottom: 0.8rem;
        }
        
        .tips-list {
            list-style: none;
            padding: 0;
        }
        
        .tips-list li {
            padding: 0.6rem 0;
            border-bottom: 1px solid rgba(24, 123, 205, 0.2);
            position: relative;
            padding-left: 1.8rem;
            font-size: 0.9rem;
        }
        
        .tips-list li::before {
            content: "✓";
            position: absolute;
            left: 0;
            color: var(--primary-color);
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .tips-list li:last-child {
            border-bottom: none;
        }
        
        .simulator-button {
            display: inline-block;
            background: linear-gradient(135deg, var(--primary-color), #1565c0);
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: bold;
            margin-top: 0.8rem;
            transition: all 0.3s ease;
            text-align: center;
            border: none;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(24, 123, 205, 0.3);
        }
        
        .simulator-button:hover {
            background: linear-gradient(135deg, #1565c0, #0d47a1);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(24, 123, 205, 0.4);
        }
        
        .simulator-button::before {
            content: "🎯 ";
            margin-right: 0.3rem;
        }
        
        .filter-section {
            margin: 2rem 0;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            border: 2px solid rgba(24, 123, 205, 0.3);
        }
        
        .filter-title {
            color: var(--primary-color);
            font-size: 1.4rem;
            text-align: center;
            margin-bottom: 1rem;
        }
        
        .continent-filters {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.8rem;
            margin-bottom: 1.5rem;
        }
        
        .filter-btn {
            background: linear-gradient(135deg, var(--primary-color), #1565c0);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(24, 123, 205, 0.3);
        }
        
        .filter-btn:hover {
            background: linear-gradient(135deg, #1565c0, #0d47a1);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(24, 123, 205, 0.4);
        }
        
        .filter-btn.active {
            background: linear-gradient(135deg, #0d47a1, #1565c0);
            box-shadow: 0 4px 15px rgba(24, 123, 205, 0.5);
        }
        
        .country-filter-section {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1.2rem;
            margin: 1.2rem 0;
            border: 1px solid rgba(24, 123, 205, 0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }
        
        .country-filter-title {
            color: var(--primary-color);
            font-size: 1.1rem;
            margin: 0;
            text-align: center;
            font-weight: 600;
        }
        
        .country-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
            justify-content: center;
        }
        
        .country-dropdown {
            background: linear-gradient(135deg, var(--primary-color), #1565c0);
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(24, 123, 205, 0.3);
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
        }
        
        .country-dropdown:hover {
            background: linear-gradient(135deg, #1565c0, #0d47a1);
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(24, 123, 205, 0.4);
        }
        
        .country-dropdown option {
            background: white;
            color: #333;
            padding: 8px;
        }
        
        .continent-section {
            display: none;
            margin-top: 1rem;
        }
        
        .continent-section.active {
            display: block;
        }
        
        .continent-title {
            color: var(--primary-color);
            font-size: 1.2rem;
            margin-bottom: 1rem;
            text-align: center;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 0.5rem;
        }
        
        .back-button {
            display: inline-block;
            background-color: var(--primary-color);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 2rem;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .back-button:hover {
            background-color: #1565c0;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        @media (max-width: 768px) {
            .content-container {
                padding: 1rem;
                margin: 1rem;
            }
            
            .header-section h1 {
                font-size: 1.8rem;
            }
            
            .test-grid, .country-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Fixed background with dream clouds */
        body {
            background-image: url('dream-clouds.svg');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            position: relative;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(1px);
            z-index: -1;
        }
    </style>
</head>
<body>
    <?php include 'header_status.php'; ?>
    
    <section class="container">
        <div class="content-container">
            <div class="header-section">
                <h1>Guia Completo de Testes Internacionais</h1>
                <p>Descubra os principais testes e provas exigidos para estudar no exterior. Prepare-se adequadamente para conquistar sua vaga em universidades internacionais.</p>
                
                <div class="filter-section">
                    <h3 class="filter-title">🌍 Filtrar por Continente</h3>
                    <div class="continent-filters">
                        <button class="filter-btn active" onclick="showContinent('all', this)">Todos</button>
                        <button class="filter-btn" onclick="showContinent('america', this)">🌎 América</button>
                        <button class="filter-btn" onclick="showContinent('europa', this)">🇪🇺 Europa</button>
                        <button class="filter-btn" onclick="showContinent('asia', this)">🌏 Ásia</button>
                        <button class="filter-btn" onclick="showContinent('oceania', this)">🇦🇺 Oceania</button>
                        <button class="filter-btn" onclick="showContinent('africa', this)">🌍 África</button>
                    </div>
                    
                    <div class="country-filter-section">
                        <h4 class="country-filter-title">🏛️ Filtrar por País</h4>
                        <div class="country-filters">
                            <select class="country-dropdown" onchange="showCountryFromDropdown(this.value)">
                                <option value="all">Todos os Países</option>
                                <option value="usa">🇺🇸 Estados Unidos</option>
                                <option value="canada">🇨🇦 Canadá</option>
                                <option value="argentina">🇦🇷 Argentina</option>
                                <option value="uk">🇬🇧 Reino Unido</option>
                                <option value="australia">🇦🇺 Austrália</option>
                                <option value="germany">🇩🇪 Alemanha</option>
                                <option value="france">🇫🇷 França</option>
                                <option value="spain">🇪🇸 Espanha</option>
                                <option value="italy">🇮🇹 Itália</option>
                                <option value="japan">🇯🇵 Japão</option>
                                <option value="china">🇨🇳 China</option>
                                <option value="southkorea">🇰🇷 Coreia do Sul</option>
                                <option value="newzealand">🇳🇿 Nova Zelândia</option>
                                <option value="southafrica">🇿🇦 África do Sul</option>
                            </select>
                        </div>
                    </div>
                    
                    <div id="america" class="continent-section">
                        <h4 class="continent-title">América do Norte e Sul</h4>
                        <div class="country-grid">
                            <div class="country-card" data-country="usa">
                                     <h4>🇺🇸 Estados Unidos</h4>
                                <ul>
                                    <li><strong>Graduação:</strong> SAT ou ACT + TOEFL/IELTS</li>
                                    <li><strong>Pós-graduação:</strong> GRE ou GMAT + TOEFL/IELTS</li>
                                    <li><strong>SAT Subject Tests:</strong> Para universidades competitivas</li>
                                    <li><strong>LSAT:</strong> Para Direito</li>
                                    <li><strong>MCAT:</strong> Para Medicina</li>
                                </ul>
                                <button class="simulator-button" onclick="verificarLogin()">Simulador SAT/TOEFL</button>
                            </div>
                            
                            <div class="country-card" data-country="canada">
                                     <h4>🇨🇦 Canadá</h4>
                                <ul>
                                    <li><strong>Inglês:</strong> IELTS ou TOEFL</li>
                                    <li><strong>Francês:</strong> DELF/DALF (Quebec)</li>
                                    <li><strong>Graduação:</strong> Varia por província</li>
                                    <li><strong>Pós-graduação:</strong> GRE/GMAT conforme área</li>
                                    <li><strong>TEF:</strong> Test d'Évaluation de Français</li>
                                </ul>
                                <button class="simulator-button" onclick="verificarLogin()">Simulador IELTS/DELF</button>
                            </div>
                            
                            <div class="country-card" data-country="argentina">
                                     <h4>🇦🇷 Argentina</h4>
                                 <ul>
                                     <li><strong>Espanhol:</strong> DELE ou SIELE</li>
                                     <li><strong>Inglês:</strong> TOEFL/IELTS (cursos em inglês)</li>
                                     <li><strong>Graduação:</strong> Bachillerato ou equivalente</li>
                                     <li><strong>Universidades públicas:</strong> Gratuitas</li>
                                 </ul>
                                 <button class="simulator-button" onclick="verificarLogin()">Simulador DELE</button>
                             </div>
                        </div>
                    </div>
                    
                    <div id="europa" class="continent-section">
                        <h4 class="continent-title">Europa</h4>
                        <div class="country-grid">
                             <div class="country-card" data-country="uk">
                                     <h4>🇬🇧 Reino Unido</h4>
                                 <ul>
                                     <li><strong>Proficiência:</strong> IELTS (preferencial) ou TOEFL</li>
                                     <li><strong>Graduação:</strong> A-Levels ou IB</li>
                                     <li><strong>UKCAT/BMAT:</strong> Para Medicina</li>
                                     <li><strong>LNAT:</strong> Para Direito</li>
                                     <li><strong>IELTS for UKVI:</strong> Para vistos</li>
                                 </ul>
                                 <button class="simulator-button" onclick="verificarLogin()">Simulador IELTS</button>
                             </div>
                             
                             <div class="country-card" data-country="germany">
                                     <h4>🇩🇪 Alemanha</h4>
                                 <ul>
                                     <li><strong>Alemão:</strong> TestDaF ou DSH</li>
                                     <li><strong>Inglês:</strong> TOEFL/IELTS (cursos em inglês)</li>
                                     <li><strong>Graduação:</strong> Abitur ou Studienkolleg</li>
                                     <li><strong>Goethe-Zertifikat:</strong> Certificação alemã</li>
                                 </ul>
                                 <button class="simulator-button" onclick="verificarLogin()">Simulador TestDaF</button>
                             </div>
                             
                             <div class="country-card" data-country="france">
                                   <h4>🇫🇷 França</h4>
                                 <ul>
                                     <li><strong>Francês:</strong> DELF/DALF ou TCF</li>
                                     <li><strong>Inglês:</strong> TOEFL/IELTS (cursos em inglês)</li>
                                     <li><strong>Graduação:</strong> Baccalauréat</li>
                                     <li><strong>Grandes Écoles:</strong> Concursos específicos</li>
                                 </ul>
                                 <button class="simulator-button" onclick="verificarLogin()">Simulador DELF/DALF</button>
                             </div>
                             
                             <div class="country-card" data-country="spain">
                                   <h4>🇪🇸 Espanha</h4>
                                 <ul>
                                     <li><strong>Espanhol:</strong> DELE ou SIELE</li>
                                     <li><strong>Inglês:</strong> TOEFL/IELTS (cursos em inglês)</li>
                                     <li><strong>Graduação:</strong> Bachillerato + PCE</li>
                                     <li><strong>EBAU:</strong> Evaluación de Bachillerato</li>
                                 </ul>
                                 <button class="simulator-button" onclick="verificarLogin()">Simulador DELE</button>
                             </div>
                             
                             <div class="country-card" data-country="italy">
                                   <h4>🇮🇹 Itália</h4>
                                 <ul>
                                     <li><strong>Italiano:</strong> CILS ou CELI</li>
                                     <li><strong>Inglês:</strong> TOEFL/IELTS (cursos em inglês)</li>
                                     <li><strong>Graduação:</strong> Diploma di Maturità</li>
                                     <li><strong>TOLC:</strong> Test Online CISIA</li>
                                 </ul>
                                 <button class="simulator-button" onclick="verificarLogin()">Simulador CILS</button>
                             </div>
                         </div>
                    </div>
                    
                    <div id="asia" class="continent-section">
                        <h4 class="continent-title">Ásia</h4>
                        <div class="country-grid">
                             <div class="country-card" data-country="japan">
                                   <h4>🇯🇵 Japão</h4>
                                 <ul>
                                     <li><strong>Japonês:</strong> JLPT (N1-N5)</li>
                                     <li><strong>Inglês:</strong> TOEFL/IELTS</li>
                                     <li><strong>EJU:</strong> Examination for Japanese Universities</li>
                                     <li><strong>Graduação:</strong> 12 anos de educação</li>
                                 </ul>
                                 <button class="simulator-button" onclick="verificarLogin()">Simulador JLPT</button>
                             </div>
                             
                             <div class="country-card" data-country="china">
                                   <h4>🇨🇳 China</h4>
                                 <ul>
                                     <li><strong>Chinês:</strong> HSK (1-6) ou HSKK</li>
                                     <li><strong>Inglês:</strong> TOEFL/IELTS (cursos em inglês)</li>
                                     <li><strong>Graduação:</strong> Gaokao ou equivalente</li>
                                     <li><strong>BCT:</strong> Business Chinese Test</li>
                                 </ul>
                                 <button class="simulator-button" onclick="verificarLogin()">Simulador HSK</button>
                             </div>
                             
                             <div class="country-card" data-country="southkorea">
                                   <h4>🇰🇷 Coreia do Sul</h4>
                                 <ul>
                                     <li><strong>Coreano:</strong> TOPIK (1-6)</li>
                                     <li><strong>Inglês:</strong> TOEFL/IELTS</li>
                                     <li><strong>KSAT:</strong> Korean Scholastic Aptitude Test</li>
                                     <li><strong>Graduação:</strong> 12 anos de educação</li>
                                 </ul>
                                 <button class="simulator-button" onclick="verificarLogin()">Simulador TOPIK</button>
                             </div>
                         </div>
                    </div>
                    
                    <div id="oceania" class="continent-section">
                        <h4 class="continent-title">Oceania</h4>
                        <div class="country-grid">
                             <div class="country-card" data-country="australia">
                                   <h4>🇦🇺 Austrália</h4>
                                 <ul>
                                     <li><strong>Proficiência:</strong> IELTS (preferencial) ou TOEFL</li>
                                     <li><strong>Graduação:</strong> ATAR ou IB</li>
                                     <li><strong>UCAT:</strong> Para Medicina/Odontologia</li>
                                     <li><strong>GAMSAT:</strong> Graduate Medical School</li>
                                     <li><strong>PTE Academic:</strong> Alternativa ao IELTS</li>
                                 </ul>
                                 <button class="simulator-button" onclick="verificarLogin()">Simulador IELTS</button>
                             </div>
                             
                             <div class="country-card" data-country="newzealand">
                                   <h4>🇳🇿 Nova Zelândia</h4>
                                 <ul>
                                     <li><strong>Proficiência:</strong> IELTS ou TOEFL</li>
                                     <li><strong>Graduação:</strong> NCEA Level 3 ou IB</li>
                                     <li><strong>UCAT ANZ:</strong> Para Medicina</li>
                                     <li><strong>Imigração:</strong> IELTS General Training</li>
                                 </ul>
                                 <button class="simulator-button" onclick="verificarLogin()">Simulador IELTS</button>
                             </div>
                         </div>
                    </div>
                    
                    <div id="africa" class="continent-section">
                        <h4 class="continent-title">África</h4>
                        <div class="country-grid">
                             <div class="country-card" data-country="southafrica">
                                   <h4>🇿🇦 África do Sul</h4>
                                 <ul>
                                     <li><strong>Inglês:</strong> IELTS ou TOEFL</li>
                                     <li><strong>Graduação:</strong> National Senior Certificate</li>
                                     <li><strong>NBT:</strong> National Benchmark Tests</li>
                                     <li><strong>Afrikaans:</strong> Para algumas universidades</li>
                                 </ul>
                                 <button class="simulator-button" onclick="verificarLogin()">Simulador IELTS</button>
                             </div>
                         </div>
                    </div>
                </div>
            </div>
            
            <div class="test-grid">
                <div class="test-card">
                    <h3>TOEFL</h3>
                    <div class="test-subtitle">Test of English as a Foreign Language</div>
                    <div class="test-info">
                        <strong>Para quem é:</strong>
                        <p>Estudantes que desejam fazer graduação ou pós-graduação em universidades de países de língua inglesa, principalmente EUA e Canadá.</p>
                    </div>
                    <div class="test-info">
                        <strong>Formato:</strong>
                        <p>Exame 100% online com foco em ambiente acadêmico. Avalia leitura, audição, escrita e fala. Duração: 3h30. Pontuação: 0 a 120 pontos.</p>
                    </div>
                    <div class="test-info">
                        <strong>Custo:</strong>
                        <p>Aproximadamente R$ 1.300 (versão iBT) ou R$ 690-750 (versão ITP).</p>
                    </div>
                </div>
                
                <div class="test-card">
                    <h3>IELTS</h3>
                    <div class="test-subtitle">International English Language Testing System</div>
                    <div class="test-info">
                        <strong>Para quem é:</strong>
                        <p>Estudantes e profissionais que querem estudar ou trabalhar no Reino Unido, Austrália, Irlanda, Nova Zelândia e mais de 140 países.</p>
                    </div>
                    <div class="test-info">
                        <strong>Formato:</strong>
                        <p>Pode ser feito no papel ou computador. Avalia as quatro habilidades do idioma. Duração: 2h45. Pontuação: 0 a 9.</p>
                    </div>
                    <div class="test-info">
                        <strong>Custo:</strong>
                        <p>Entre R$ 1.171 a R$ 1.265, dependendo da modalidade.</p>
                    </div>
                </div>
                
                <div class="test-card">
                    <h3>SAT</h3>
                    <div class="test-subtitle">Scholastic Assessment Test</div>
                    <div class="test-info">
                        <strong>Para quem é:</strong>
                        <p>Estudantes que desejam ingressar em universidades americanas para graduação. Equivalente ao ENEM brasileiro.</p>
                    </div>
                    <div class="test-info">
                        <strong>Formato:</strong>
                        <p>Duas seções principais: Matemática (800 pontos) e Leitura/Escrita (800 pontos). Pontuação máxima: 1600. Duração: 3 horas.</p>
                    </div>
                    <div class="test-info">
                        <strong>Custo:</strong>
                        <p>US$ 51 + taxa regional de US$ 31 para o Brasil.</p>
                    </div>
                </div>
                
                <div class="test-card">
                    <h3>ACT</h3>
                    <div class="test-subtitle">American College Test</div>
                    <div class="test-info">
                        <strong>Para quem é:</strong>
                        <p>Alternativa ao SAT para ingresso em universidades americanas. Aceito pela maioria das instituições dos EUA.</p>
                    </div>
                    <div class="test-info">
                        <strong>Formato:</strong>
                        <p>Quatro seções: Inglês, Matemática, Interpretação de texto e Raciocínio científico. Pontuação: 1 a 36. Duração: 2h45.</p>
                    </div>
                    <div class="test-info">
                        <strong>Diferencial:</strong>
                        <p>Inclui seção de ciências, ideal para quem tem bom desempenho em ciências da natureza.</p>
                    </div>
                </div>
                
                <div class="test-card">
                    <h3>GRE</h3>
                    <div class="test-subtitle">Graduate Record Examination</div>
                    <div class="test-info">
                        <strong>Para quem é:</strong>
                        <p>Estudantes que desejam fazer mestrado ou doutorado em diversas áreas (exceto MBA e medicina).</p>
                    </div>
                    <div class="test-info">
                        <strong>Formato:</strong>
                        <p>Avalia raciocínio verbal, quantitativo e escrita analítica. Teste baseado em computador.</p>
                    </div>
                    <div class="test-info">
                        <strong>Uso:</strong>
                        <p>Amplamente aceito para programas de pós-graduação em universidades americanas e internacionais.</p>
                    </div>
                </div>
                
                <div class="test-card">
                    <h3>GMAT</h3>
                    <div class="test-subtitle">Graduate Management Admission Test</div>
                    <div class="test-info">
                        <strong>Para quem é:</strong>
                        <p>Candidatos a programas de MBA e pós-graduação em administração e negócios.</p>
                    </div>
                    <div class="test-info">
                        <strong>Formato:</strong>
                        <p>Quatro seções: Escrita analítica, Raciocínio integrado, Quantitativo e Verbal. Teste online.</p>
                    </div>
                    <div class="test-info">
                        <strong>Foco:</strong>
                        <p>Habilidades específicas para gestão e administração de empresas.</p>
                    </div>
                </div>
            </div>
            
            <div class="country-section">
                <h2>Testes por País/Região</h2>
                <div class="country-grid">
                    <div class="country-card">
                        <h4>Estados Unidos</h4>
                        <ul>
                            <li><strong>Graduação:</strong> SAT ou ACT + TOEFL/IELTS</li>
                            <li><strong>Pós-graduação:</strong> GRE ou GMAT + TOEFL/IELTS</li>
                            <li><strong>SAT Subject Tests:</strong> Para universidades mais competitivas</li>
                            <li><strong>Universidades aceitas:</strong> Todas as principais universidades</li>
                        </ul>
                        <button class="simulator-button" onclick="verificarLogin()">Simulador SAT/TOEFL</button>
                    </div>
                    
                    <div class="country-card">
                        <h4>Reino Unido</h4>
                        <ul>
                            <li><strong>Proficiência:</strong> IELTS (preferencial) ou TOEFL</li>
                            <li><strong>Graduação:</strong> A-Levels ou equivalente internacional</li>
                            <li><strong>Pós-graduação:</strong> Varia por universidade</li>
                            <li><strong>Especial:</strong> IELTS for UKVI para vistos</li>
                        </ul>
                        <button class="simulator-button" onclick="verificarLogin()">Simulador IELTS</button>
                    </div>
                    
                    <div class="country-card">
                        <h4>Canadá</h4>
                        <ul>
                            <li><strong>Inglês:</strong> IELTS ou TOEFL</li>
                            <li><strong>Francês:</strong> DELF/DALF (Quebec)</li>
                            <li><strong>Graduação:</strong> Varia por província</li>
                            <li><strong>Pós-graduação:</strong> GRE/GMAT conforme área</li>
                        </ul>
                        <button class="simulator-button" onclick="verificarLogin()">Simulador IELTS/DELF</button>
                    </div>
                    
                    <div class="country-card">
                        <h4>Austrália</h4>
                        <ul>
                            <li><strong>Proficiência:</strong> IELTS (preferencial)</li>
                            <li><strong>Graduação:</strong> ATAR ou equivalente internacional</li>
                            <li><strong>Pós-graduação:</strong> Varia por universidade</li>
                            <li><strong>Imigração:</strong> IELTS General Training</li>
                        </ul>
                        <button class="simulator-button" onclick="verificarLogin()">Simulador IELTS</button>
                    </div>
                    
                    <div class="country-card">
                        <h4>França</h4>
                        <ul>
                            <li><strong>Francês:</strong> DELF/DALF</li>
                            <li><strong>Inglês:</strong> TOEFL/IELTS (cursos em inglês)</li>
                            <li><strong>Graduação:</strong> Baccalauréat ou equivalente</li>
                            <li><strong>Grandes Écoles:</strong> Concursos específicos</li>
                        </ul>
                        <button class="simulator-button" onclick="verificarLogin()">Simulador DELF/DALF</button>
                    </div>
                    
                    <div class="country-card">
                        <h4>Alemanha</h4>
                        <ul>
                            <li><strong>Alemão:</strong> TestDaF ou DSH</li>
                            <li><strong>Inglês:</strong> TOEFL/IELTS (cursos em inglês)</li>
                            <li><strong>Graduação:</strong> Abitur ou equivalente</li>
                            <li><strong>Universidades públicas:</strong> Geralmente gratuitas</li>
                        </ul>
                        <button class="simulator-button" onclick="verificarLogin()">Simulador TestDaF</button>
                    </div>
                </div>
            </div>
            
            <div class="tips-section">
                <h3>Dicas Importantes para Preparação</h3>
                <ul class="tips-list">
                    <li><strong>Planeje com antecedência:</strong> Alguns testes têm poucas datas disponíveis e podem ter lista de espera.</li>
                    <li><strong>Conheça o formato:</strong> Cada teste tem estrutura específica. Pratique com simulados oficiais.</li>
                    <li><strong>Verifique a validade:</strong> TOEFL e IELTS são válidos por 2 anos. Cambridge não expira.</li>
                    <li><strong>Escolha estrategicamente:</strong> SAT vs ACT, TOEFL vs IELTS - considere suas habilidades.</li>
                    <li><strong>Prepare-se adequadamente:</strong> Invista em cursos preparatórios ou materiais de qualidade.</li>
                    <li><strong>Considere múltiplas tentativas:</strong> A maioria dos testes pode ser refeita para melhorar a pontuação.</li>
                    <li><strong>Verifique requisitos específicos:</strong> Cada universidade tem exigências diferentes de pontuação mínima.</li>
                    <li><strong>Documente tudo:</strong> Mantenha cópias de todos os certificados e resultados.</li>
                </ul>
            </div>
            
            <div style="text-align: center;">
                <a href="index.php" class="back-button">Voltar à Página Inicial</a>
            </div>
        </div>
    </section>

    <script>
        // Função para mostrar/ocultar seções
        function toggleSection(sectionId) {
            const section = document.getElementById(sectionId);
            const allSections = document.querySelectorAll('.test-section, .tips-section');
            
            allSections.forEach(s => {
                if (s.id === sectionId) {
                    s.style.display = s.style.display === 'none' ? 'block' : 'none';
                } else {
                    s.style.display = 'none';
                }
            });
        }
        
        // Função para filtrar continentes
        function showContinent(continent, clickedButton = null) {
            // Remove classe active de todos os botões
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Adiciona classe active ao botão clicado ou ao botão "Todos" se não especificado
            if (clickedButton) {
                clickedButton.classList.add('active');
            } else {
                // Se não há botão especificado, ativa o botão correspondente
                const targetButton = document.querySelector(`[onclick="showContinent('${continent}')"]`);
                if (targetButton) {
                    targetButton.classList.add('active');
                }
            }
            
            // Esconde todas as seções de continente
            document.querySelectorAll('.continent-section').forEach(section => {
                section.style.display = 'none';
            });
            
            // Mostra a seção selecionada ou todas
            if (continent === 'all') {
                document.querySelectorAll('.continent-section').forEach(section => {
                    section.style.display = 'block';
                });
            } else {
                const targetSection = document.getElementById(continent);
                if (targetSection) {
                    targetSection.style.display = 'block';
                }
            }
        }
        
        // Função para filtrar países
        function showCountry(country, clickedButton = null) {
            // Remove classe active de todos os botões de país
            document.querySelectorAll('.country-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Adiciona classe active ao botão clicado
            if (clickedButton) {
                clickedButton.classList.add('active');
            }
            
            // Mostra/esconde cards de países
            document.querySelectorAll('.country-card').forEach(card => {
                if (country === 'all') {
                    card.style.display = 'block';
                } else {
                    const countryData = card.getAttribute('data-country');
                    if (countryData === country) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                }
            });
        }
        
        // Função para filtrar países via dropdown
        function showCountryFromDropdown(country) {
            // Mostra/esconde cards de países
            document.querySelectorAll('.country-card').forEach(card => {
                if (country === 'all') {
                    card.style.display = 'block';
                } else {
                    const countryData = card.getAttribute('data-country');
                    if (countryData === country) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                }
            });
        }
        
        // Função para verificar login antes de acessar simulador
        function verificarLogin() {
            <?php if (!$usuario_logado): ?>
                alert('Você precisa estar logado para prosseguir!');
                
                if (confirm('Deseja efetuar o login?')) {
                    window.location.href = 'login.php';
                }
                return false;
            <?php else: ?>
                alert('Simulador em desenvolvimento!');
                return true;
            <?php endif; ?>
        }
        
        // Inicializar mostrando todos os continentes e países
        document.addEventListener('DOMContentLoaded', function() {
            showContinent('all');
            showCountry('all');
        });
    </script>
</body>
</html>