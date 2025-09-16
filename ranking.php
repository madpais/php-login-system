<?php
require_once 'config.php';
iniciarSessaoSegura();

// Verificar se o usuário está logado
$usuario_logado = isset($_SESSION['usuario_id']);
$nome_usuario = $usuario_logado && isset($_SESSION['nome']) ? $_SESSION['nome'] : 'Visitante';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking de Universidades - DayDreaming</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.11.0/css/flag-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background-color: #2a9df4;
            color: white;
            padding: 30px 0;
            text-align: center;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 18px;
            opacity: 0.9;
        }
        
        .filters {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .filters h3 {
            margin-bottom: 15px;
            color: #2c3e50;
        }
        
        .filter-row {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #2c3e50;
        }
        
        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .filter-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        
        .btn {
            background-color: #2a9df4;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #1a8ce4;
        }
        
        .btn-secondary {
            background-color: #95a5a6;
        }
        
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
        
        .ranking-table {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .table-header {
            background-color: #2a9df4;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .table-header h3 {
            font-size: 20px;
            font-weight: 500;
        }
        
        .sort-options {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .sort-options select {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .sort-options option {
            background-color: #2a9df4;
        }
        
        .table-content {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background-color: #f8f9fa;
        }
        
        th {
            padding: 15px;
            text-align: left;
            font-weight: 500;
            color: #2c3e50;
            border-bottom: 1px solid #eee;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        .university-rank {
            font-weight: 700;
            color: #2a9df4;
            font-size: 18px;
        }
        
        .university-logo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }
        
        .university-info {
            display: flex;
            align-items: center;
        }
        
        .university-name {
            font-weight: 500;
            color: #2c3e50;
        }
        
        .university-country {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .score {
            font-weight: 500;
            color: #2c3e50;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        
        .pagination button {
            background-color: white;
            border: 1px solid #ddd;
            padding: 8px 12px;
            margin: 0 5px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .pagination button:hover {
            background-color: #f8f9fa;
        }
        
        .pagination button.active {
            background-color: #2a9df4;
            color: white;
            border-color: #2a9df4;
        }
        
        .pagination button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .university-details {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 30px;
            display: none;
        }
        
        .university-details.active {
            display: block;
        }
        
        .details-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .details-logo {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            object-fit: cover;
            margin-right: 20px;
        }
        
        .details-title h2 {
            font-size: 24px;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        
        .details-title p {
            color: #7f8c8d;
        }
        
        .details-content {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .detail-card {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #2a9df4;
        }
        
        .detail-card h4 {
            margin-bottom: 10px;
            color: #2c3e50;
        }
        
        .detail-card p {
            color: #7f8c8d;
            margin-bottom: 5px;
        }
        
        .detail-card .score-value {
            font-size: 24px;
            font-weight: 700;
            color: #2a9df4;
        }
        
        .close-details {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-left: auto;
        }
        
        .close-details:hover {
            background-color: #c0392b;
        }
        
        .no-results {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
        }
        
        .no-results i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #bdc3c7;
        }
        
        /* Footer */
        .footer {
            background-color: #2a9df4;
            color: white;
            padding: 40px 0 20px;
            margin-top: 50px;
        }
        
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .footer-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .footer-brand {
            flex: 1;
            min-width: 250px;
            margin-bottom: 20px;
        }
        
        .footer-brand h2 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .footer-brand p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .footer-links {
            display: flex;
            flex-wrap: wrap;
            flex: 2;
            justify-content: space-between;
        }
        
        .footer-column {
            flex: 1;
            min-width: 200px;
            margin-bottom: 20px;
            padding: 0 15px;
        }
        
        .footer-column h3 {
            font-size: 18px;
            margin-bottom: 15px;
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer-column h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 2px;
            background-color: rgba(255, 255, 255, 0.5);
        }
        
        .footer-column ul {
            list-style: none;
        }
        
        .footer-column ul li {
            margin-bottom: 10px;
        }
        
        .footer-column ul li a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s;
            display: flex;
            align-items: center;
        }
        
        .footer-column ul li a:hover {
            color: white;
        }
        
        .footer-column ul li a i {
            margin-right: 8px;
            font-size: 14px;
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            text-align: center;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
        }
        
        .footer-bottom a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            margin: 0 10px;
            transition: color 0.3s;
        }
        
        .footer-bottom a:hover {
            color: white;
        }
        
        @media (max-width: 768px) {
            .filter-row {
                flex-direction: column;
            }
            
            .filter-buttons {
                justify-content: center;
            }
            
            .table-header {
                flex-direction: column;
                gap: 15px;
            }
            
            .sort-options {
                width: 100%;
                justify-content: space-between;
            }
            
            .university-info {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .university-logo {
                margin-right: 0;
                margin-bottom: 10px;
            }
            
            .details-header {
                flex-direction: column;
                text-align: center;
            }
            
            .details-logo {
                margin-right: 0;
                margin-bottom: 15px;
            }
            
            .footer-content {
                flex-direction: column;
            }
            
            .footer-brand {
                text-align: center;
            }
            
            .footer-links {
                flex-direction: column;
            }
            
            .footer-column {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Header -->
    <?php include 'nav_padronizada.php'; ?>

    <div class="container">
        <div class="header">
            <h1>Ranking Mundial de Universidades</h1>
            <p>Descubra as melhores instituições de ensino superior do mundo</p>
        </div>
        
        <div class="filters">
            <h3>Filtrar Resultados</h3>
            <div class="filter-row">
                <div class="filter-group">
                    <label for="country">País</label>
                    <select id="country">
                        <option value="">Todos os Países</option>
                        <option value="us">Estados Unidos</option>
                        <option value="gb">Reino Unido</option>
                        <option value="ca">Canadá</option>
                        <option value="au">Austrália</option>
                        <option value="de">Alemanha</option>
                        <option value="fr">França</option>
                        <option value="jp">Japão</option>
                        <option value="cn">China</option>
                        <option value="kr">Coreia do Sul</option>
                        <option value="sg">Singapura</option>
                        <option value="ch">Suíça</option>
                        <option value="nl">Países Baixos</option>
                        <option value="se">Suécia</option>
                        <option value="hk">Hong Kong</option>
                        <option value="be">Bélgica</option>
                        <option value="dk">Dinamarca</option>
                        <option value="ie">Irlanda</option>
                        <option value="fi">Finlândia</option>
                        <option value="nz">Nova Zelândia</option>
                        <option value="at">Áustria</option>
                        <option value="it">Itália</option>
                        <option value="es">Espanha</option>
                        <option value="no">Noruega</option>
                        <option value="il">Israel</option>
                        <option value="ru">Rússia</option>
                        <option value="my">Malásia</option>
                        <option value="mx">México</option>
                        <option value="ar">Argentina</option>
                        <option value="br">Brasil</option>
                        <option value="za">África do Sul</option>
                        <option value="in">Índia</option>
                        <option value="th">Tailândia</option>
                        <option value="tw">Taiwan</option>
                        <option value="sa">Arábia Saudita</option>
                        <option value="tr">Turquia</option>
                        <option value="ae">Emirados Árabes</option>
                        <option value="cl">Chile</option>
                        <option value="co">Colômbia</option>
                        <option value="pl">Polônia</option>
                        <option value="cz">República Tcheca</option>
                        <option value="hu">Hungria</option>
                        <option value="gr">Grécia</option>
                        <option value="pt">Portugal</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="region">Região</label>
                    <select id="region">
                        <option value="">Todas as Regiões</option>
                        <option value="americas">Américas</option>
                        <option value="europe">Europa</option>
                        <option value="asia">Ásia</option>
                        <option value="africa">África</option>
                        <option value="oceania">Oceania</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="subject">Área de Estudo</label>
                    <select id="subject">
                        <option value="">Todas as Áreas</option>
                        <option value="engineering">Engenharia</option>
                        <option value="medicine">Medicina</option>
                        <option value="business">Negócios</option>
                        <option value="arts">Artes</option>
                        <option value="sciences">Ciências</option>
                        <option value="law">Direito</option>
                    </select>
                </div>
            </div>
            <div class="filter-row">
                <div class="filter-group">
                    <label for="search">Buscar Universidade</label>
                    <input type="text" id="search" placeholder="Digite o nome da universidade">
                </div>
            </div>
            <div class="filter-buttons">
                <button class="btn btn-secondary" id="clear-filters">Limpar Filtros</button>
                <button class="btn" id="apply-filters">Aplicar Filtros</button>
            </div>
        </div>
        
        <div class="ranking-table">
            <div class="table-header">
                <h3>Resultados</h3>
                <div class="sort-options">
                    <span>Ordenar por:</span>
                    <select id="sort-by">
                        <option value="rank">Ranking</option>
                        <option value="name">Nome</option>
                        <option value="score">Pontuação</option>
                    </select>
                    <select id="order-by">
                        <option value="asc">Crescente</option>
                        <option value="desc">Decrescente</option>
                    </select>
                </div>
            </div>
            <div class="table-content">
                <table>
                    <thead>
                        <tr>
                            <th>Ranking</th>
                            <th>Universidade</th>
                            <th>País</th>
                            <th>Pontuação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="university-table-body">
                        <!-- As linhas da tabela serão preenchidas dinamicamente via JavaScript -->
                    </tbody>
                </table>
                <div class="no-results" id="no-results" style="display: none;">
                    <i class="fas fa-search"></i>
                    <h3>Nenhuma universidade encontrada</h3>
                    <p>Tente ajustar seus filtros ou termos de busca</p>
                </div>
            </div>
        </div>
        
        <div class="pagination" id="pagination">
            <!-- Os botões de paginação serão preenchidos dinamicamente via JavaScript -->
        </div>
        
        <div class="university-details" id="university-details">
            <div class="details-header">
                <img src="" alt="" class="details-logo" id="details-logo">
                <div class="details-title">
                    <h2 id="details-name"></h2>
                    <p id="details-location"></p>
                </div>
                <button class="close-details" id="close-details">
                    <i class="fas fa-times"></i> Fechar
                </button>
            </div>
            <div class="details-content" id="details-content">
                <!-- Os cards de detalhes serão preenchidos dinamicamente via JavaScript -->
            </div>
        </div>
    </div>
    
 
                   
    </footer>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dados das universidades - 100 universidades únicas
            const universities = [
                {
                    id: 1,
                    rank: 1,
                    name: "Massachusetts Institute of Technology (MIT)",
                    country: "Estados Unidos",
                    countryCode: "us",
                    region: "americas",
                    score: 100.0,
                    logo: "https://picsum.photos/seed/mit/40/40.jpg",
                    location: "Cambridge, Massachusetts, Estados Unidos",
                    details: [
                        { title: "Pontuação Geral", value: "100.0", description: "Ranking: #1" },
                        { title: "Reputação Acadêmica", value: "100.0", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "99.8", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "99.5", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "99.9", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "98.7", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "97.2", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "99.3", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 2,
                    rank: 2,
                    name: "Stanford University",
                    country: "Estados Unidos",
                    countryCode: "us",
                    region: "americas",
                    score: 98.7,
                    logo: "https://picsum.photos/seed/stanford/40/40.jpg",
                    location: "Stanford, California, Estados Unidos",
                    details: [
                        { title: "Pontuação Geral", value: "98.7", description: "Ranking: #2" },
                        { title: "Reputação Acadêmica", value: "99.5", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "99.2", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "98.1", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "98.5", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "97.8", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "96.5", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "98.3", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 3,
                    rank: 3,
                    name: "Harvard University",
                    country: "Estados Unidos",
                    countryCode: "us",
                    region: "americas",
                    score: 97.8,
                    logo: "https://picsum.photos/seed/harvard/40/40.jpg",
                    location: "Cambridge, Massachusetts, Estados Unidos",
                    details: [
                        { title: "Pontuação Geral", value: "97.8", description: "Ranking: #3" },
                        { title: "Reputação Acadêmica", value: "99.8", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "99.5", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "97.2", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "97.5", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "96.8", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "95.7", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "97.3", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 4,
                    rank: 4,
                    name: "University of Cambridge",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 96.9,
                    logo: "https://picsum.photos/seed/cambridge/40/40.jpg",
                    location: "Cambridge, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "96.9", description: "Ranking: #4" },
                        { title: "Reputação Acadêmica", value: "98.5", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "98.2", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "96.7", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "97.1", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "95.9", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "94.8", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "96.4", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 5,
                    rank: 5,
                    name: "University of Oxford",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 96.5,
                    logo: "https://picsum.photos/seed/oxford/40/40.jpg",
                    location: "Oxford, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "96.5", description: "Ranking: #5" },
                        { title: "Reputação Acadêmica", value: "98.3", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "98.0", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "96.3", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "96.8", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "95.5", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "94.2", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "96.0", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 6,
                    rank: 6,
                    name: "ETH Zurich",
                    country: "Suíça",
                    countryCode: "ch",
                    region: "europe",
                    score: 95.2,
                    logo: "https://picsum.photos/seed/eth/40/40.jpg",
                    location: "Zurique, Suíça",
                    details: [
                        { title: "Pontuação Geral", value: "95.2", description: "Ranking: #6" },
                        { title: "Reputação Acadêmica", value: "96.8", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "96.5", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "95.0", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "95.5", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "94.7", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "93.5", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "94.8", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 7,
                    rank: 7,
                    name: "University of Chicago",
                    country: "Estados Unidos",
                    countryCode: "us",
                    region: "americas",
                    score: 94.8,
                    logo: "https://picsum.photos/seed/uchicago/40/40.jpg",
                    location: "Chicago, Illinois, Estados Unidos",
                    details: [
                        { title: "Pontuação Geral", value: "94.8", description: "Ranking: #7" },
                        { title: "Reputação Acadêmica", value: "96.5", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "96.2", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "94.6", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "95.1", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "94.3", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "93.0", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "94.4", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 8,
                    rank: 8,
                    name: "Imperial College London",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 94.1,
                    logo: "https://picsum.photos/seed/imperial/40/40.jpg",
                    location: "Londres, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "94.1", description: "Ranking: #8" },
                        { title: "Reputação Acadêmica", value: "95.8", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "95.5", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "93.9", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "94.4", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "93.6", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "92.3", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "93.7", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 9,
                    rank: 9,
                    name: "University College London",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 93.6,
                    logo: "https://picsum.photos/seed/ucl/40/40.jpg",
                    location: "Londres, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "93.6", description: "Ranking: #9" },
                        { title: "Reputação Acadêmica", value: "95.2", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "94.9", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "93.4", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "93.9", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "93.1", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "91.8", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "93.2", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 10,
                    rank: 10,
                    name: "National University of Singapore",
                    country: "Singapura",
                    countryCode: "sg",
                    region: "asia",
                    score: 93.2,
                    logo: "https://picsum.photos/seed/nus/40/40.jpg",
                    location: "Singapura",
                    details: [
                        { title: "Pontuação Geral", value: "93.2", description: "Ranking: #10" },
                        { title: "Reputação Acadêmica", value: "94.8", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "94.5", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "93.0", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "93.5", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "92.7", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "91.4", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "92.8", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 11,
                    rank: 11,
                    name: "University of Toronto",
                    country: "Canadá",
                    countryCode: "ca",
                    region: "americas",
                    score: 92.8,
                    logo: "https://picsum.photos/seed/toronto/40/40.jpg",
                    location: "Toronto, Canadá",
                    details: [
                        { title: "Pontuação Geral", value: "92.8", description: "Ranking: #11" },
                        { title: "Reputação Acadêmica", value: "94.4", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "94.1", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "92.6", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "93.1", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "92.3", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "91.0", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "92.4", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 12,
                    rank: 12,
                    name: "University of Melbourne",
                    country: "Austrália",
                    countryCode: "au",
                    region: "oceania",
                    score: 92.4,
                    logo: "https://picsum.photos/seed/melbourne/40/40.jpg",
                    location: "Melbourne, Austrália",
                    details: [
                        { title: "Pontuação Geral", value: "92.4", description: "Ranking: #12" },
                        { title: "Reputação Acadêmica", value: "94.0", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "93.7", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "92.2", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "92.7", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "91.9", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "90.6", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "92.0", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 13,
                    rank: 13,
                    name: "Technical University of Munich",
                    country: "Alemanha",
                    countryCode: "de",
                    region: "europe",
                    score: 92.0,
                    logo: "https://picsum.photos/seed/tum/40/40.jpg",
                    location: "Munique, Alemanha",
                    details: [
                        { title: "Pontuação Geral", value: "92.0", description: "Ranking: #13" },
                        { title: "Reputação Acadêmica", value: "93.6", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "93.3", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "91.8", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "92.3", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "91.5", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "90.2", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "91.6", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 14,
                    rank: 14,
                    name: "Sorbonne University",
                    country: "França",
                    countryCode: "fr",
                    region: "europe",
                    score: 91.6,
                    logo: "https://picsum.photos/seed/sorbonne/40/40.jpg",
                    location: "Paris, França",
                    details: [
                        { title: "Pontuação Geral", value: "91.6", description: "Ranking: #14" },
                        { title: "Reputação Acadêmica", value: "93.2", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "92.9", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "91.4", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "91.9", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "91.1", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "89.8", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "91.2", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 15,
                    rank: 15,
                    name: "University of Tokyo",
                    country: "Japão",
                    countryCode: "jp",
                    region: "asia",
                    score: 91.2,
                    logo: "https://picsum.photos/seed/tokyo/40/40.jpg",
                    location: "Tóquio, Japão",
                    details: [
                        { title: "Pontuação Geral", value: "91.2", description: "Ranking: #15" },
                        { title: "Reputação Acadêmica", value: "92.8", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "92.5", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "91.0", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "91.5", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "90.7", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "89.4", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "90.8", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 16,
                    rank: 16,
                    name: "Seoul National University",
                    country: "Coreia do Sul",
                    countryCode: "kr",
                    region: "asia",
                    score: 90.8,
                    logo: "https://picsum.photos/seed/snu/40/40.jpg",
                    location: "Seul, Coreia do Sul",
                    details: [
                        { title: "Pontuação Geral", value: "90.8", description: "Ranking: #16" },
                        { title: "Reputação Acadêmica", value: "92.4", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "92.1", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "90.6", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "91.1", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "90.3", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "89.0", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "90.4", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 17,
                    rank: 17,
                    name: "Tsinghua University",
                    country: "China",
                    countryCode: "cn",
                    region: "asia",
                    score: 90.4,
                    logo: "https://picsum.photos/seed/tsinghua/40/40.jpg",
                    location: "Pequim, China",
                    details: [
                        { title: "Pontuação Geral", value: "90.4", description: "Ranking: #17" },
                        { title: "Reputação Acadêmica", value: "92.0", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "91.7", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "90.2", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "90.7", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "89.9", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "88.6", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "90.0", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 18,
                    rank: 18,
                    name: "University of São Paulo",
                    country: "Brasil",
                    countryCode: "br",
                    region: "americas",
                    score: 90.0,
                    logo: "https://picsum.photos/seed/usp/40/40.jpg",
                    location: "São Paulo, Brasil",
                    details: [
                        { title: "Pontuação Geral", value: "90.0", description: "Ranking: #18" },
                        { title: "Reputação Acadêmica", value: "91.6", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "91.3", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "89.8", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "90.3", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "89.5", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "88.2", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "89.6", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 19,
                    rank: 19,
                    name: "Yale University",
                    country: "Estados Unidos",
                    countryCode: "us",
                    region: "americas",
                    score: 89.6,
                    logo: "https://picsum.photos/seed/yale/40/40.jpg",
                    location: "New Haven, Connecticut, Estados Unidos",
                    details: [
                        { title: "Pontuação Geral", value: "89.6", description: "Ranking: #19" },
                        { title: "Reputação Acadêmica", value: "91.2", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "90.9", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "89.4", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "89.9", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "89.1", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "87.8", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "89.2", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 20,
                    rank: 20,
                    name: "Johns Hopkins University",
                    country: "Estados Unidos",
                    countryCode: "us",
                    region: "americas",
                    score: 89.2,
                    logo: "https://picsum.photos/seed/jhu/40/40.jpg",
                    location: "Baltimore, Maryland, Estados Unidos",
                    details: [
                        { title: "Pontuação Geral", value: "89.2", description: "Ranking: #20" },
                        { title: "Reputação Acadêmica", value: "90.8", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "90.5", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "89.0", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "89.5", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "88.7", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "87.4", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "88.8", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 21,
                    rank: 21,
                    name: "Cornell University",
                    country: "Estados Unidos",
                    countryCode: "us",
                    region: "americas",
                    score: 88.8,
                    logo: "https://picsum.photos/seed/cornell/40/40.jpg",
                    location: "Ithaca, Nova York, Estados Unidos",
                    details: [
                        { title: "Pontuação Geral", value: "88.8", description: "Ranking: #21" },
                        { title: "Reputação Acadêmica", value: "90.4", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "90.1", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "88.6", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "89.1", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "88.3", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "87.0", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "88.4", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 22,
                    rank: 22,
                    name: "University of Edinburgh",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 88.4,
                    logo: "https://picsum.photos/seed/edinburgh/40/40.jpg",
                    location: "Edimburgo, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "88.4", description: "Ranking: #22" },
                        { title: "Reputação Acadêmica", value: "90.0", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "89.7", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "88.2", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "88.7", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "87.9", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "86.6", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "88.0", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 23,
                    rank: 23,
                    name: "Columbia University",
                    country: "Estados Unidos",
                    countryCode: "us",
                    region: "americas",
                    score: 88.0,
                    logo: "https://picsum.photos/seed/columbia/40/40.jpg",
                    location: "Nova York, Nova York, Estados Unidos",
                    details: [
                        { title: "Pontuação Geral", value: "88.0", description: "Ranking: #23" },
                        { title: "Reputação Acadêmica", value: "89.6", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "89.3", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "87.8", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "88.3", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "87.5", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "86.2", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "87.6", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 24,
                    rank: 24,
                    name: "University of California, Berkeley",
                    country: "Estados Unidos",
                    countryCode: "us",
                    region: "americas",
                    score: 87.6,
                    logo: "https://picsum.photos/seed/berkeley/40/40.jpg",
                    location: "Berkeley, Califórnia, Estados Unidos",
                    details: [
                        { title: "Pontuação Geral", value: "87.6", description: "Ranking: #24" },
                        { title: "Reputação Acadêmica", value: "89.2", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "88.9", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "87.4", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "87.9", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "87.1", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "85.8", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "87.2", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 25,
                    rank: 25,
                    name: "University of California, Los Angeles",
                    country: "Estados Unidos",
                    countryCode: "us",
                    region: "americas",
                    score: 87.2,
                    logo: "https://picsum.photos/seed/ucla/40/40.jpg",
                    location: "Los Angeles, Califórnia, Estados Unidos",
                    details: [
                        { title: "Pontuação Geral", value: "87.2", description: "Ranking: #25" },
                        { title: "Reputação Acadêmica", value: "88.8", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "88.5", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "87.0", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "87.5", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "86.7", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "85.4", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "86.8", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 26,
                    rank: 26,
                    name: "University of Michigan",
                    country: "Estados Unidos",
                    countryCode: "us",
                    region: "americas",
                    score: 86.8,
                    logo: "https://picsum.photos/seed/michigan/40/40.jpg",
                    location: "Ann Arbor, Michigan, Estados Unidos",
                    details: [
                        { title: "Pontuação Geral", value: "86.8", description: "Ranking: #26" },
                        { title: "Reputação Acadêmica", value: "88.4", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "88.1", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "86.6", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "87.1", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "86.3", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "85.0", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "86.4", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 27,
                    rank: 27,
                    name: "University of Sydney",
                    country: "Austrália",
                    countryCode: "au",
                    region: "oceania",
                    score: 86.4,
                    logo: "https://picsum.photos/seed/sydney/40/40.jpg",
                    location: "Sydney, Austrália",
                    details: [
                        { title: "Pontuação Geral", value: "86.4", description: "Ranking: #27" },
                        { title: "Reputação Acadêmica", value: "88.0", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "87.7", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "86.2", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "86.7", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "85.9", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "84.6", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "86.0", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 28,
                    rank: 28,
                    name: "University of New South Wales",
                    country: "Austrália",
                    countryCode: "au",
                    region: "oceania",
                    score: 86.0,
                    logo: "https://picsum.photos/seed/unsw/40/40.jpg",
                    location: "Sydney, Austrália",
                    details: [
                        { title: "Pontuação Geral", value: "86.0", description: "Ranking: #28" },
                        { title: "Reputação Acadêmica", value: "87.6", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "87.3", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "85.8", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "86.3", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "85.5", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "84.2", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "85.6", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 29,
                    rank: 29,
                    name: "McGill University",
                    country: "Canadá",
                    countryCode: "ca",
                    region: "americas",
                    score: 85.6,
                    logo: "https://picsum.photos/seed/mcgill/40/40.jpg",
                    location: "Montreal, Canadá",
                    details: [
                        { title: "Pontuação Geral", value: "85.6", description: "Ranking: #29" },
                        { title: "Reputação Acadêmica", value: "87.2", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "86.9", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "85.4", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "85.9", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "85.1", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "83.8", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "85.2", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 30,
                    rank: 30,
                    name: "University of Manchester",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 85.2,
                    logo: "https://picsum.photos/seed/manchester/40/40.jpg",
                    location: "Manchester, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "85.2", description: "Ranking: #30" },
                        { title: "Reputação Acadêmica", value: "86.8", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "86.5", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "85.0", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "85.5", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "84.7", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "83.4", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "84.8", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 31,
                    rank: 31,
                    name: "King's College London",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 84.8,
                    logo: "https://picsum.photos/seed/kings/40/40.jpg",
                    location: "Londres, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "84.8", description: "Ranking: #31" },
                        { title: "Reputação Acadêmica", value: "86.4", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "86.1", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "84.6", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "85.1", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "84.3", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "83.0", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "84.4", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 32,
                    rank: 32,
                    name: "University of Hong Kong",
                    country: "Hong Kong",
                    countryCode: "hk",
                    region: "asia",
                    score: 84.4,
                    logo: "https://picsum.photos/seed/hku/40/40.jpg",
                    location: "Hong Kong",
                    details: [
                        { title: "Pontuação Geral", value: "84.4", description: "Ranking: #32" },
                        { title: "Reputação Acadêmica", value: "86.0", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "85.7", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "84.2", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "84.7", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "83.9", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "82.6", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "84.0", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 33,
                    rank: 33,
                    name: "University of Queensland",
                    country: "Austrália",
                    countryCode: "au",
                    region: "oceania",
                    score: 84.0,
                    logo: "https://picsum.photos/seed/queensland/40/40.jpg",
                    location: "Brisbane, Austrália",
                    details: [
                        { title: "Pontuação Geral", value: "84.0", description: "Ranking: #33" },
                        { title: "Reputação Acadêmica", value: "85.6", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "85.3", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "83.8", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "84.3", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "83.5", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "82.2", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "83.6", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 34,
                    rank: 34,
                    name: "University of British Columbia",
                    country: "Canadá",
                    countryCode: "ca",
                    region: "americas",
                    score: 83.6,
                    logo: "https://picsum.photos/seed/ubc/40/40.jpg",
                    location: "Vancouver, Canadá",
                    details: [
                        { title: "Pontuação Geral", value: "83.6", description: "Ranking: #34" },
                        { title: "Reputação Acadêmica", value: "85.2", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "84.9", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "83.4", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "83.9", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "83.1", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "81.8", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "83.2", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 35,
                    rank: 35,
                    name: "Australian National University",
                    country: "Austrália",
                    countryCode: "au",
                    region: "oceania",
                    score: 83.2,
                    logo: "https://picsum.photos/seed/anu/40/40.jpg",
                    location: "Canberra, Austrália",
                    details: [
                        { title: "Pontuação Geral", value: "83.2", description: "Ranking: #35" },
                        { title: "Reputação Acadêmica", value: "84.8", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "84.5", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "83.0", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "83.5", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "82.7", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "81.4", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "82.8", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 36,
                    rank: 36,
                    name: "London School of Economics and Political Science",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 82.8,
                    logo: "https://picsum.photos/seed/lse/40/40.jpg",
                    location: "Londres, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "82.8", description: "Ranking: #36" },
                        { title: "Reputação Acadêmica", value: "84.4", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "84.1", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "82.6", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "83.1", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "82.3", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "81.0", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "82.4", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 37,
                    rank: 37,
                    name: "New York University",
                    country: "Estados Unidos",
                    countryCode: "us",
                    region: "americas",
                    score: 82.4,
                    logo: "https://picsum.photos/seed/nyu/40/40.jpg",
                    location: "Nova York, Nova York, Estados Unidos",
                    details: [
                        { title: "Pontuação Geral", value: "82.4", description: "Ranking: #37" },
                        { title: "Reputação Acadêmica", value: "84.0", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "83.7", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "82.2", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "82.7", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "81.9", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "80.6", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "82.0", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 38,
                    rank: 38,
                    name: "University of Amsterdam",
                    country: "Países Baixos",
                    countryCode: "nl",
                    region: "europe",
                    score: 82.0,
                    logo: "https://picsum.photos/seed/amsterdam/40/40.jpg",
                    location: "Amsterdã, Países Baixos",
                    details: [
                        { title: "Pontuação Geral", value: "82.0", description: "Ranking: #38" },
                        { title: "Reputação Acadêmica", value: "83.6", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "83.3", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "81.8", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "82.3", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "81.5", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "80.2", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "81.6", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 39,
                    rank: 39,
                    name: "University of Copenhagen",
                    country: "Dinamarca",
                    countryCode: "dk",
                    region: "europe",
                    score: 81.6,
                    logo: "https://picsum.photos/seed/copenhagen/40/40.jpg",
                    location: "Copenhague, Dinamarca",
                    details: [
                        { title: "Pontuação Geral", value: "81.6", description: "Ranking: #39" },
                        { title: "Reputação Acadêmica", value: "83.2", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "82.9", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "81.4", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "81.9", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "81.1", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "79.8", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "81.2", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 40,
                    rank: 40,
                    name: "University of Glasgow",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 81.2,
                    logo: "https://picsum.photos/seed/glasgow/40/40.jpg",
                    location: "Glasgow, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "81.2", description: "Ranking: #40" },
                        { title: "Reputação Acadêmica", value: "82.8", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "82.5", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "81.0", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "81.5", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "80.7", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "79.4", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "80.8", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 41,
                    rank: 41,
                    name: "University of Toronto",
                    country: "Canadá",
                    countryCode: "ca",
                    region: "americas",
                    score: 80.8,
                    logo: "https://picsum.photos/seed/toronto2/40/40.jpg",
                    location: "Toronto, Canadá",
                    details: [
                        { title: "Pontuação Geral", value: "80.8", description: "Ranking: #41" },
                        { title: "Reputação Acadêmica", value: "82.4", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "82.1", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "80.6", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "81.1", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "80.3", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "79.0", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "80.4", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 42,
                    rank: 42,
                    name: "University of Bristol",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 80.4,
                    logo: "https://picsum.photos/seed/bristol/40/40.jpg",
                    location: "Bristol, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "80.4", description: "Ranking: #42" },
                        { title: "Reputação Acadêmica", value: "82.0", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "81.7", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "80.2", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "80.7", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "79.9", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "78.6", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "80.0", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 43,
                    rank: 43,
                    name: "University of Sheffield",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 80.0,
                    logo: "https://picsum.photos/seed/sheffield/40/40.jpg",
                    location: "Sheffield, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "80.0", description: "Ranking: #43" },
                        { title: "Reputação Acadêmica", value: "81.6", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "81.3", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "79.8", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "80.3", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "79.5", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "78.2", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "79.6", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 44,
                    rank: 44,
                    name: "University of Warwick",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 79.6,
                    logo: "https://picsum.photos/seed/warwick/40/40.jpg",
                    location: "Coventry, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "79.6", description: "Ranking: #44" },
                        { title: "Reputação Acadêmica", value: "81.2", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "80.9", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "79.4", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "79.9", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "79.1", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "77.8", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "79.2", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 45,
                    rank: 45,
                    name: "University of Birmingham",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 79.2,
                    logo: "https://picsum.photos/seed/birmingham/40/40.jpg",
                    location: "Birmingham, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "79.2", description: "Ranking: #45" },
                        { title: "Reputação Acadêmica", value: "80.8", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "80.5", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "79.0", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "79.5", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "78.7", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "77.4", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "78.8", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 46,
                    rank: 46,
                    name: "University of Leeds",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 78.8,
                    logo: "https://picsum.photos/seed/leeds/40/40.jpg",
                    location: "Leeds, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "78.8", description: "Ranking: #46" },
                        { title: "Reputação Acadêmica", value: "80.4", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "80.1", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "78.6", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "79.1", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "78.3", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "77.0", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "78.4", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 47,
                    rank: 47,
                    name: "University of Southampton",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 78.4,
                    logo: "https://picsum.photos/seed/southampton/40/40.jpg",
                    location: "Southampton, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "78.4", description: "Ranking: #47" },
                        { title: "Reputação Acadêmica", value: "80.0", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "79.7", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "78.2", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "78.7", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "77.9", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "76.6", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "78.0", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 48,
                    rank: 48,
                    name: "University of Nottingham",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 78.0,
                    logo: "https://picsum.photos/seed/nottingham/40/40.jpg",
                    location: "Nottingham, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "78.0", description: "Ranking: #48" },
                        { title: "Reputação Acadêmica", value: "79.6", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "79.3", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "77.8", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "78.3", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "77.5", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "76.2", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "77.6", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 49,
                    rank: 49,
                    name: "University of Liverpool",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 77.6,
                    logo: "https://picsum.photos/seed/liverpool/40/40.jpg",
                    location: "Liverpool, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "77.6", description: "Ranking: #49" },
                        { title: "Reputação Acadêmica", value: "79.2", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "78.9", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "77.4", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "77.9", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "77.1", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "75.8", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "77.2", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 50,
                    rank: 50,
                    name: "University of Aberdeen",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 77.2,
                    logo: "https://picsum.photos/seed/aberdeen/40/40.jpg",
                    location: "Aberdeen, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "77.2", description: "Ranking: #50" },
                        { title: "Reputação Acadêmica", value: "78.8", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "78.5", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "77.0", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "77.5", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "76.7", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "75.4", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "76.8", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 51,
                    rank: 51,
                    name: "University of St Andrews",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 76.8,
                    logo: "https://picsum.photos/seed/standrews/40/40.jpg",
                    location: "St Andrews, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "76.8", description: "Ranking: #51" },
                        { title: "Reputação Acadêmica", value: "78.4", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "78.1", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "76.6", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "77.1", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "76.3", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "75.0", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "76.4", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 52,
                    rank: 52,
                    name: "University of Dundee",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 76.4,
                    logo: "https://picsum.photos/seed/dundee/40/40.jpg",
                    location: "Dundee, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "76.4", description: "Ranking: #52" },
                        { title: "Reputação Acadêmica", value: "78.0", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "77.7", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "76.2", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "76.7", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "75.9", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "74.6", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "76.0", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 53,
                    rank: 53,
                    name: "University of York",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 76.0,
                    logo: "https://picsum.photos/seed/york/40/40.jpg",
                    location: "York, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "76.0", description: "Ranking: #53" },
                        { title: "Reputação Acadêmica", value: "77.6", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "77.3", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "75.8", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "76.3", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "75.5", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "74.2", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "75.6", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 54,
                    rank: 54,
                    name: "University of Exeter",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 75.6,
                    logo: "https://picsum.photos/seed/exeter/40/40.jpg",
                    location: "Exeter, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "75.6", description: "Ranking: #54" },
                        { title: "Reputação Acadêmica", value: "77.2", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "76.9", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "75.4", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "75.9", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "75.1", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "73.8", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "75.2", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 55,
                    rank: 55,
                    name: "University of Sussex",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 75.2,
                    logo: "https://picsum.photos/seed/sussex/40/40.jpg",
                    location: "Brighton, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "75.2", description: "Ranking: #55" },
                        { title: "Reputação Acadêmica", value: "76.8", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "76.5", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "75.0", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "75.5", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "74.7", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "73.4", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "74.8", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 56,
                    rank: 56,
                    name: "University of Reading",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 74.8,
                    logo: "https://picsum.photos/seed/reading/40/40.jpg",
                    location: "Reading, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "74.8", description: "Ranking: #56" },
                        { title: "Reputação Acadêmica", value: "76.4", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "76.1", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "74.6", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "75.1", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "74.3", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "73.0", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "74.4", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 57,
                    rank: 57,
                    name: "University of Surrey",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 74.4,
                    logo: "https://picsum.photos/seed/surrey/40/40.jpg",
                    location: "Guildford, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "74.4", description: "Ranking: #57" },
                        { title: "Reputação Acadêmica", value: "76.0", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "75.7", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "74.2", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "74.7", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "73.9", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "72.6", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "74.0", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 58,
                    rank: 58,
                    name: "University of Bath",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 74.0,
                    logo: "https://picsum.photos/seed/bath/40/40.jpg",
                    location: "Bath, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "74.0", description: "Ranking: #58" },
                        { title: "Reputação Acadêmica", value: "75.6", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "75.3", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "73.8", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "74.3", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "73.5", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "72.2", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "73.6", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 59,
                    rank: 59,
                    name: "University of Leicester",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 73.6,
                    logo: "https://picsum.photos/seed/leicester/40/40.jpg",
                    location: "Leicester, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "73.6", description: "Ranking: #59" },
                        { title: "Reputação Acadêmica", value: "75.2", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "74.9", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "73.4", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "73.9", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "73.1", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "71.8", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "73.2", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 60,
                    rank: 60,
                    name: "University of Stirling",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 73.2,
                    logo: "https://picsum.photos/seed/stirling/40/40.jpg",
                    location: "Stirling, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "73.2", description: "Ranking: #60" },
                        { title: "Reputação Acadêmica", value: "74.8", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "74.5", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "73.0", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "73.5", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "72.7", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "71.4", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "72.8", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 61,
                    rank: 61,
                    name: "University of Strathclyde",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 72.8,
                    logo: "https://picsum.photos/seed/strathclyde/40/40.jpg",
                    location: "Glasgow, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "72.8", description: "Ranking: #61" },
                        { title: "Reputação Acadêmica", value: "74.4", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "74.1", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "72.6", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "73.1", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "72.3", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "71.0", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "72.4", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 62,
                    rank: 62,
                    name: "University of Loughborough",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 72.4,
                    logo: "https://picsum.photos/seed/loughborough/40/40.jpg",
                    location: "Loughborough, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "72.4", description: "Ranking: #62" },
                        { title: "Reputação Acadêmica", value: "74.0", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "73.7", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "72.2", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "72.7", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "71.9", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "70.6", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "72.0", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 63,
                    rank: 63,
                    name: "University of East Anglia",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 72.0,
                    logo: "https://picsum.photos/seed/uea/40/40.jpg",
                    location: "Norwich, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "72.0", description: "Ranking: #63" },
                        { title: "Reputação Acadêmica", value: "73.6", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "73.3", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "71.8", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "72.3", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "71.5", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "70.2", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "71.6", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 64,
                    rank: 64,
                    name: "University of Essex",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 71.6,
                    logo: "https://picsum.photos/seed/essex/40/40.jpg",
                    location: "Colchester, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "71.6", description: "Ranking: #64" },
                        { title: "Reputação Acadêmica", value: "73.2", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "72.9", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "71.4", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "71.9", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "71.1", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "69.8", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "71.2", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 65,
                    rank: 65,
                    name: "University of Kent",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 71.2,
                    logo: "https://picsum.photos/seed/kent/40/40.jpg",
                    location: "Canterbury, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "71.2", description: "Ranking: #65" },
                        { title: "Reputação Acadêmica", value: "72.8", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "72.5", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "71.0", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "71.5", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "70.7", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "69.4", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "70.8", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 66,
                    rank: 66,
                    name: "University of Lancaster",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 70.8,
                    logo: "https://picsum.photos/seed/lancaster/40/40.jpg",
                    location: "Lancaster, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "70.8", description: "Ranking: #66" },
                        { title: "Reputação Acadêmica", value: "72.4", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "72.1", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "70.6", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "71.1", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "70.3", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "69.0", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "70.4", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 67,
                    rank: 67,
                    name: "University of Newcastle",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 70.4,
                    logo: "https://picsum.photos/seed/newcastle/40/40.jpg",
                    location: "Newcastle upon Tyne, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "70.4", description: "Ranking: #67" },
                        { title: "Reputação Acadêmica", value: "72.0", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "71.7", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "70.2", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "70.7", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "69.9", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "68.6", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "70.0", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 68,
                    rank: 68,
                    name: "University of Cardiff",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 70.0,
                    logo: "https://picsum.photos/seed/cardiff/40/40.jpg",
                    location: "Cardiff, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "70.0", description: "Ranking: #68" },
                        { title: "Reputação Acadêmica", value: "71.6", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "71.3", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "69.8", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "70.3", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "69.5", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "68.2", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "69.6", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 69,
                    rank: 69,
                    name: "University of Liverpool",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 69.6,
                    logo: "https://picsum.photos/seed/liverpool2/40/40.jpg",
                    location: "Liverpool, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "69.6", description: "Ranking: #69" },
                        { title: "Reputação Acadêmica", value: "71.2", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "70.9", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "69.4", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "69.9", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "69.1", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "67.8", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "69.2", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 70,
                    rank: 70,
                    name: "University of Sheffield",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 69.2,
                    logo: "https://picsum.photos/seed/sheffield2/40/40.jpg",
                    location: "Sheffield, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "69.2", description: "Ranking: #70" },
                        { title: "Reputação Acadêmica", value: "70.8", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "70.5", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "69.0", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "69.5", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "68.7", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "67.4", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "68.8", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 71,
                    rank: 71,
                    name: "University of Glasgow",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 68.8,
                    logo: "https://picsum.photos/seed/glasgow2/40/40.jpg",
                    location: "Glasgow, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "68.8", description: "Ranking: #71" },
                        { title: "Reputação Acadêmica", value: "70.4", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "70.1", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "68.6", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "69.1", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "68.3", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "67.0", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "68.4", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 72,
                    rank: 72,
                    name: "University of Manchester",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 68.4,
                    logo: "https://picsum.photos/seed/manchester2/40/40.jpg",
                    location: "Manchester, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "68.4", description: "Ranking: #72" },
                        { title: "Reputação Acadêmica", value: "70.0", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "69.7", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "68.2", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "68.7", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "67.9", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "66.6", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "68.0", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 73,
                    rank: 73,
                    name: "University of Edinburgh",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 68.0,
                    logo: "https://picsum.photos/seed/edinburgh2/40/40.jpg",
                    location: "Edimburgo, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "68.0", description: "Ranking: #73" },
                        { title: "Reputação Acadêmica", value: "69.6", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "69.3", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "67.8", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "68.3", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "67.5", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "66.2", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "67.6", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 74,
                    rank: 74,
                    name: "University of Bristol",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 67.6,
                    logo: "https://picsum.photos/seed/bristol2/40/40.jpg",
                    location: "Bristol, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "67.6", description: "Ranking: #74" },
                        { title: "Reputação Acadêmica", value: "69.2", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "68.9", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "67.4", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "67.9", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "67.1", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "65.8", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "67.2", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 75,
                    rank: 75,
                    name: "University of Leeds",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 67.2,
                    logo: "https://picsum.photos/seed/leeds2/40/40.jpg",
                    location: "Leeds, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "67.2", description: "Ranking: #75" },
                        { title: "Reputação Acadêmica", value: "68.8", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "68.5", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "67.0", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "67.5", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "66.7", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "65.4", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "66.8", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 76,
                    rank: 76,
                    name: "University of Southampton",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 66.8,
                    logo: "https://picsum.photos/seed/southampton2/40/40.jpg",
                    location: "Southampton, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "66.8", description: "Ranking: #76" },
                        { title: "Reputação Acadêmica", value: "68.4", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "68.1", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "66.6", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "67.1", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "66.3", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "65.0", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "66.4", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 77,
                    rank: 77,
                    name: "University of Nottingham",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 66.4,
                    logo: "https://picsum.photos/seed/nottingham2/40/40.jpg",
                    location: "Nottingham, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "66.4", description: "Ranking: #77" },
                        { title: "Reputação Acadêmica", value: "68.0", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "67.7", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "66.2", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "66.7", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "65.9", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "64.6", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "66.0", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 78,
                    rank: 78,
                    name: "University of Liverpool",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 66.0,
                    logo: "https://picsum.photos/seed/liverpool3/40/40.jpg",
                    location: "Liverpool, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "66.0", description: "Ranking: #78" },
                        { title: "Reputação Acadêmica", value: "67.6", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "67.3", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "65.8", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "66.3", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "65.5", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "64.2", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "65.6", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 79,
                    rank: 79,
                    name: "University of Aberdeen",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 65.6,
                    logo: "https://picsum.photos/seed/aberdeen2/40/40.jpg",
                    location: "Aberdeen, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "65.6", description: "Ranking: #79" },
                        { title: "Reputação Acadêmica", value: "67.2", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "66.9", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "65.4", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "65.9", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "65.1", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "63.8", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "65.2", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 80,
                    rank: 80,
                    name: "University of St Andrews",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 65.2,
                    logo: "https://picsum.photos/seed/standrews2/40/40.jpg",
                    location: "St Andrews, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "65.2", description: "Ranking: #80" },
                        { title: "Reputação Acadêmica", value: "66.8", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "66.5", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "65.0", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "65.5", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "64.7", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "63.4", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "64.8", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 81,
                    rank: 81,
                    name: "University of Dundee",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 64.8,
                    logo: "https://picsum.photos/seed/dundee2/40/40.jpg",
                    location: "Dundee, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "64.8", description: "Ranking: #81" },
                        { title: "Reputação Acadêmica", value: "66.4", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "66.1", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "64.6", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "65.1", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "64.3", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "63.0", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "64.4", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 82,
                    rank: 82,
                    name: "University of York",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 64.4,
                    logo: "https://picsum.photos/seed/york2/40/40.jpg",
                    location: "York, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "64.4", description: "Ranking: #82" },
                        { title: "Reputação Acadêmica", value: "66.0", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "65.7", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "64.2", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "64.7", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "63.9", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "62.6", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "64.0", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 83,
                    rank: 83,
                    name: "University of Exeter",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 64.0,
                    logo: "https://picsum.photos/seed/exeter2/40/40.jpg",
                    location: "Exeter, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "64.0", description: "Ranking: #83" },
                        { title: "Reputação Acadêmica", value: "65.6", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "65.3", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "63.8", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "64.3", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "63.5", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "62.2", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "63.6", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 84,
                    rank: 84,
                    name: "University of Sussex",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 63.6,
                    logo: "https://picsum.photos/seed/sussex2/40/40.jpg",
                    location: "Brighton, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "63.6", description: "Ranking: #84" },
                        { title: "Reputação Acadêmica", value: "65.2", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "64.9", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "63.4", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "63.9", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "63.1", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "61.8", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "63.2", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 85,
                    rank: 85,
                    name: "University of Reading",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 63.2,
                    logo: "https://picsum.photos/seed/reading2/40/40.jpg",
                    location: "Reading, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "63.2", description: "Ranking: #85" },
                        { title: "Reputação Acadêmica", value: "64.8", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "64.5", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "63.0", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "63.5", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "62.7", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "61.4", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "62.8", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 86,
                    rank: 86,
                    name: "University of Surrey",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 62.8,
                    logo: "https://picsum.photos/seed/surrey2/40/40.jpg",
                    location: "Guildford, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "62.8", description: "Ranking: #86" },
                        { title: "Reputação Acadêmica", value: "64.4", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "64.1", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "62.6", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "63.1", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "62.3", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "61.0", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "62.4", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 87,
                    rank: 87,
                    name: "University of Bath",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 62.4,
                    logo: "https://picsum.photos/seed/bath2/40/40.jpg",
                    location: "Bath, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "62.4", description: "Ranking: #87" },
                        { title: "Reputação Acadêmica", value: "64.0", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "63.7", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "62.2", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "62.7", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "61.9", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "60.6", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "62.0", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 88,
                    rank: 88,
                    name: "University of Leicester",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 62.0,
                    logo: "https://picsum.photos/seed/leicester2/40/40.jpg",
                    location: "Leicester, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "62.0", description: "Ranking: #88" },
                        { title: "Reputação Acadêmica", value: "63.6", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "63.3", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "61.8", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "62.3", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "61.5", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "60.2", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "61.6", description: "Atenção individualizada aos alunos" }
                    ]
                },
                {
                    id: 89,
                    rank: 89,
                    name: "University of Stirling",
                    country: "Reino Unido",
                    countryCode: "gb",
                    region: "europe",
                    score: 61.6,
                    logo: "https://picsum.photos/seed/stirling2/40/40.jpg",
                    location: "Stirling, Reino Unido",
                    details: [
                        { title: "Pontuação Geral", value: "61.6", description: "Ranking: #89" },
                        { title: "Reputação Acadêmica", value: "63.2", description: "Baseado em pesquisas acadêmicas" },
                        { title: "Reputação com Empregadores", value: "62.9", description: "Baseado em pesquisas com empregadores" },
                        { title: "Relação Professor-Aluno", value: "61.4", description: "Proporção de professores por aluno" },
                        { title: "Citações por Professor", value: "61.9", description: "Impacto da pesquisa acadêmica" },
                        { title: "Proporção de Professores Internacionais", value: "61.1", description: "Diversidade do corpo docente" },
                        { title: "Proporção de Alunos Internacionais", value: "59.8", description: "Diversidade do corpo discente" },
                        { title: "Proporção de Alunos por Professor", value: "61.2", description: "Atenção individualizada aos alunos" }
                    ]
                }
            ];
            
            // Variáveis de estado
            let filteredUniversities = [...universities];
            let currentPage = 1;
            const itemsPerPage = 10;
            
            // Elementos do DOM
            const countryFilter = document.getElementById('country');
            const regionFilter = document.getElementById('region');
            const subjectFilter = document.getElementById('subject');
            const searchInput = document.getElementById('search');
            const applyFiltersBtn = document.getElementById('apply-filters');
            const clearFiltersBtn = document.getElementById('clear-filters');
            const sortBySelect = document.getElementById('sort-by');
            const orderBySelect = document.getElementById('order-by');
            const universityTableBody = document.getElementById('university-table-body');
            const noResultsDiv = document.getElementById('no-results');
            const paginationDiv = document.getElementById('pagination');
            const universityDetails = document.getElementById('university-details');
            const closeDetailsBtn = document.getElementById('close-details');
            
            // Função para renderizar a tabela de universidades
            function renderUniversityTable() {
                // Limpar a tabela
                universityTableBody.innerHTML = '';
                
                if (filteredUniversities.length === 0) {
                    noResultsDiv.style.display = 'block';
                    return;
                } else {
                    noResultsDiv.style.display = 'none';
                }
                
                // Calcular índices de paginação
                const startIndex = (currentPage - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;
                const paginatedUniversities = filteredUniversities.slice(startIndex, endIndex);
                
                // Renderizar cada linha da tabela
                paginatedUniversities.forEach(university => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="university-rank">${university.rank}</td>
                        <td>
                            <div class="university-info">
                                <img src="${university.logo}" alt="${university.name}" class="university-logo">
                                <div>
                                    <div class="university-name">${university.name}</div>
                                    <div class="university-country">${university.country}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="country-flag fi fi-${university.countryCode}"></span> ${university.country}
                        </td>
                        <td class="score">${university.score}</td>
                        <td>
                            <button class="btn view-details" data-id="${university.id}">Ver Detalhes</button>
                        </td>
                    `;
                    universityTableBody.appendChild(row);
                });
                
                // Adicionar eventos aos botões de ver detalhes
                document.querySelectorAll('.view-details').forEach(button => {
                    button.addEventListener('click', () => {
                        const universityId = parseInt(button.getAttribute('data-id'));
                        showUniversityDetails(universityId);
                    });
                });
                
                // Renderizar paginação
                renderPagination();
            }
            
            // Função para renderizar a paginação
            function renderPagination() {
                paginationDiv.innerHTML = '';
                
                const totalPages = Math.ceil(filteredUniversities.length / itemsPerPage);
                
                // Botão Anterior
                const prevBtn = document.createElement('button');
                prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i> Anterior';
                prevBtn.disabled = currentPage === 1;
                prevBtn.addEventListener('click', () => {
                    if (currentPage > 1) {
                        currentPage--;
                        renderUniversityTable();
                    }
                });
                paginationDiv.appendChild(prevBtn);
                
                // Botões de página
                for (let i = 1; i <= totalPages; i++) {
                    const pageBtn = document.createElement('button');
                    pageBtn.textContent = i;
                    pageBtn.classList.toggle('active', i === currentPage);
                    pageBtn.addEventListener('click', () => {
                        currentPage = i;
                        renderUniversityTable();
                    });
                    paginationDiv.appendChild(pageBtn);
                }
                
                // Botão Próximo
                const nextBtn = document.createElement('button');
                nextBtn.innerHTML = 'Próximo <i class="fas fa-chevron-right"></i>';
                nextBtn.disabled = currentPage === totalPages;
                nextBtn.addEventListener('click', () => {
                    if (currentPage < totalPages) {
                        currentPage++;
                        renderUniversityTable();
                    }
                });
                paginationDiv.appendChild(nextBtn);
            }
            
            // Função para aplicar filtros
            function applyFilters() {
                const countryValue = countryFilter.value;
                const regionValue = regionFilter.value;
                const subjectValue = subjectFilter.value;
                const searchValue = searchInput.value.toLowerCase();
                
                // Filtrar as universidades
                filteredUniversities = universities.filter(university => {
                    // Filtro de país
                    if (countryValue && university.countryCode !== countryValue) {
                        return false;
                    }
                    
                    // Filtro de região
                    if (regionValue && university.region !== regionValue) {
                        return false;
                    }
                    
                    // Filtro de área de estudo (subject)
                    // Como não temos dados de áreas de estudo, vamos ignorar este filtro por enquanto
                    // if (subjectValue && university.subject !== subjectValue) {
                    //     return false;
                    // }
                    
                    // Filtro de busca
                    if (searchValue && !university.name.toLowerCase().includes(searchValue)) {
                        return false;
                    }
                    
                    return true;
                });
                
                // Ordenar as universidades
                sortUniversities();
                
                // Resetar para a primeira página
                currentPage = 1;
                
                // Renderizar a tabela
                renderUniversityTable();
            }
            
            // Função para ordenar as universidades
            function sortUniversities() {
                const sortBy = sortBySelect.value;
                const orderBy = orderBySelect.value;
                
                filteredUniversities.sort((a, b) => {
                    let valueA, valueB;
                    
                    switch (sortBy) {
                        case 'rank':
                            valueA = a.rank;
                            valueB = b.rank;
                            break;
                        case 'name':
                            valueA = a.name.toLowerCase();
                            valueB = b.name.toLowerCase();
                            break;
                        case 'score':
                            valueA = a.score;
                            valueB = b.score;
                            break;
                    }
                    
                    if (orderBy === 'asc') {
                        return valueA > valueB ? 1 : -1;
                    } else {
                        return valueA < valueB ? 1 : -1;
                    }
                });
            }
            
            // Função para limpar filtros
            function clearFilters() {
                countryFilter.value = '';
                regionFilter.value = '';
                subjectFilter.value = '';
                searchInput.value = '';
                
                // Resetar as universidades filtradas
                filteredUniversities = [...universities];
                
                // Ordenar as universidades
                sortUniversities();
                
                // Resetar para a primeira página
                currentPage = 1;
                
                // Renderizar a tabela
                renderUniversityTable();
            }
            
            // Função para exibir detalhes da universidade
            function showUniversityDetails(id) {
                const university = universities.find(u => u.id === id);
                if (!university) return;
                
                // Atualizar o conteúdo dos detalhes
                const detailsLogo = document.getElementById('details-logo');
                const detailsName = document.getElementById('details-name');
                const detailsLocation = document.getElementById('details-location');
                const detailsContent = document.getElementById('details-content');
                
                detailsLogo.src = university.logo;
                detailsLogo.alt = university.name;
                detailsName.textContent = university.name;
                detailsLocation.textContent = university.location;
                
                // Limpar e reconstruir o conteúdo dos detalhes
                detailsContent.innerHTML = '';
                university.details.forEach(detail => {
                    const detailCard = document.createElement('div');
                    detailCard.className = 'detail-card';
                    detailCard.innerHTML = `
                        <h4>${detail.title}</h4>
                        <div class="score-value">${detail.value}</div>
                        <p>${detail.description}</p>
                    `;
                    detailsContent.appendChild(detailCard);
                });
                
                // Exibir a seção de detalhes
                universityDetails.classList.add('active');
                
                // Rolar a página para a seção de detalhes
                universityDetails.scrollIntoView({ behavior: 'smooth' });
            }
            
            // Adicionar eventos aos elementos
            applyFiltersBtn.addEventListener('click', applyFilters);
            clearFiltersBtn.addEventListener('click', clearFilters);
            sortBySelect.addEventListener('change', () => {
                sortUniversities();
                renderUniversityTable();
            });
            orderBySelect.addEventListener('change', () => {
                sortUniversities();
                renderUniversityTable();
            });
            closeDetailsBtn.addEventListener('click', () => {
                universityDetails.classList.remove('active');
            });
            
            // Adicionar evento de busca em tempo real
            searchInput.addEventListener('input', applyFilters);
            
            // Renderizar a tabela inicial
            renderUniversityTable();
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<?php require_once __DIR__ . '/footer.php'; ?>
</body>
</html>