<?php
// Footer do sistema DayDreaming
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer - DayDreaming</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-brand">
                    <h2>DayDreaming</h2>
                    <p>Sua plataforma para transformar sonhos de intercâmbio em realidade</p>
                </div>
                
                <div class="footer-links">
                    <div class="footer-column">
                        <h3>Navegação</h3>
                        <ul>
                            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                            <li><a href="calculadora.php"><i class="fas fa-calculator"></i> Conversor GPA</a></li>
                            <li><a href="forum.php"><i class="fas fa-comments"></i> Fórum</a></li>
                            <li><a href="ranking.php"><i class="fas fa-trophy"></i> Rank Universidades</a></li>
                        </ul>
                    </div>
                    
                    <div class="footer-column">
                        <h3>Ferramentas</h3>
                        <ul>
                            <li><a href="simulador_provas.php"><i class="fas fa-file-alt"></i> Simulador de Provas</a></li>
                            <li><a href="simulador_entrevistas.php"><i class="fas fa-user-tie"></i> Simulador de Entrevistas</a></li>
                            <li><a href="teste_vocacional.php"><i class="fas fa-compass"></i> Teste Vocacional</a></li>
                            <li><a href="pesquisa_por_pais.php"><i class="fas fa-globe"></i> Guia de Países</a></li>
                        </ul>
                    </div>
                    
                    <div class="footer-column">
                        <h3>Legal</h3>
                        <ul>
                            <li><a href="termos_uso.php"><i class="fas fa-file-contract"></i> Termos de Uso</a></li>
                            <li><a href="politica_privacidade.php"><i class="fas fa-shield-alt"></i> Política de Privacidade</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> DayDreaming. Todos os direitos reservados.</p>
                <div>
                    <a href="termos_uso.php">Termos de Uso</a> | 
                    <a href="politica_privacidade.php">Política de Privacidade</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>