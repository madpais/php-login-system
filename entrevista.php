<?php
require_once 'config.php';

// Iniciar sessão de forma segura
iniciarSessaoSegura();

// Verificar se o usuário está logado
$usuario_logado = isset($_SESSION['usuario_id']);
$usuario_nome = $_SESSION['usuario_nome'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulador de Entrevista para Bolsas Internacionais - DayDreaming</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="imagens/logo_50px_sem_bgd.png">
    <link rel="shortcut icon" type="image/png" href="imagens/logo_50px_sem_bgd.png">
    <link rel="apple-touch-icon" href="imagens/logo_50px_sem_bgd.png">
    
    <style>
        body {
            background: url('https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?auto=format&fit=crop&w=1500&q=80') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #222;
            margin: 0;
            padding: 0;
        }
        .overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255,255,255,0.85);
            z-index: 0;
        }
        .container {
            position: relative;
            max-width: 520px;
            margin: 60px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
            padding: 40px 36px 32px 36px;
            z-index: 1;
        }
        h1 {
            text-align: center;
            margin-bottom: 18px;
            font-size: 2em;
            color: #2575fc;
            font-weight: 700;
        }
        .interviewer {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
        }
        .interviewer img {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            margin-right: 16px;
            border: 2px solid #2575fc;
        }
        .interviewer span {
            font-size: 1.1em;
            color: #333;
            font-weight: 500;
        }
        .question {
            font-size: 1.15em;
            margin-bottom: 18px;
            background: #f2f7ff;
            padding: 16px;
            border-radius: 10px;
            border-left: 4px solid #2575fc;
            color: #222;
            box-shadow: 0 2px 8px rgba(37,117,252,0.07);
        }
        input, button {
            width: 100%;
            padding: 13px;
            margin-bottom: 16px;
            border-radius: 8px;
            border: 1px solid #dbeafe;
            font-size: 1em;
            box-sizing: border-box;
        }
        input {
            background: #f8fafc;
            color: #222;
        }
        button {
            background: linear-gradient(90deg, #2575fc 0%, #6a11cb 100%);
            color: #fff;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.2s;
            border: none;
            box-shadow: 0 2px 8px rgba(37,117,252,0.09);
        }
        button:hover {
            background: linear-gradient(90deg, #6a11cb 0%, #2575fc 100%);
        }
        .feedback {
            margin-bottom: 16px;
            font-style: italic;
            color: #2575fc;
        }
        .score {
            text-align: center;
            font-size: 1.25em;
            margin-top: 28px;
            color: #2575fc;
            font-weight: 600;
        }
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(37, 117, 252, 0.1);
            border: 2px solid #2575fc;
            color: #2575fc;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            z-index: 2;
        }
        .back-button:hover {
            background: #2575fc;
            color: white;
            text-decoration: none;
        }
        @media (max-width: 600px) {
            .container {
                padding: 18px 8px 16px 8px;
            }
            h1 {
                font-size: 1.3em;
            }
            .back-button {
                position: relative;
                top: 0;
                left: 0;
                margin-bottom: 20px;
                display: block;
                text-align: center;
            }
        }
    </style>
</head>
<body>`

    <a href="index.php" class="back-button">← Voltar ao Início</a>
    
    <div class="overlay"></div>
    <div class="container">
        <h1>Entrevista de Bolsa Internacional</h1>
        <div class="interviewer">
            <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Entrevistador">
            <span>Entrevistador</span>
        </div>
        <div class="question" id="question"></div>
        <input type="text" id="answer" placeholder="Digite sua resposta aqui...">
        <button onclick="nextQuestion()">Responder</button>
        <div class="feedback" id="feedback"></div>
        <div class="score" id="score"></div>
    </div>
    <script>
        const perguntas = [
            "Por favor, fale um pouco sobre você e sua trajetória acadêmica.",
            "Quais são suas principais qualidades e defeitos?",
            "O que motivou sua escolha por estudar neste país em específico?",
            "Por que você escolheu este curso para sua formação?",
            "Quais razões levaram você a buscar este curso fora do Brasil?",
            "Você pretende voltar para o Brasil após os estudos? Se sim, o que pretende levar de aprendizado para o país?",
            "Quais são seus planos e objetivos para o futuro profissional?",
            "Você já teve experiência morando sozinho? Como foi?",
            "Quais aspectos você mais aprecia na cultura e sociedade deste país?",
            "Existe algo neste país que você considera um desafio ou que não gosta?",
            "Por que acredita que deve ser selecionado para receber a bolsa de estudos?",
            "De que forma pretende contribuir para a comunidade acadêmica local?",
            "Quais objetivos profissionais você espera alcançar após concluir a bolsa?",
            "Como você costuma lidar com desafios e situações multiculturais?"
        ];
        let atual = 0;
        let pontuacao = 0;

        function avaliarResposta(resposta) {
            if (resposta.trim().length > 50) {
                return { pontos: 2, texto: "Boa resposta! Mostrou profundidade." };
            } else if (resposta.trim().length > 20) {
                return { pontos: 1, texto: "Resposta razoável." };
            } else {
                return { pontos: 0, texto: "Resposta muito curta." };
            }
        }

        function nextQuestion() {
            const answerInput = document.getElementById('answer');
            const feedbackDiv = document.getElementById('feedback');
            if (atual > 0) {
                const resposta = answerInput.value;
                const avaliacao = avaliarResposta(resposta);
                pontuacao += avaliacao.pontos;
                feedbackDiv.textContent = avaliacao.texto;
            }
            answerInput.value = '';
            if (atual < perguntas.length) {
                document.getElementById('question').textContent = perguntas[atual];
                atual++;
            } else {
                document.getElementById('question').textContent = '';
                document.getElementById('answer').style.display = 'none';
                document.querySelector('button').style.display = 'none';
                feedbackDiv.textContent = '';
                document.getElementById('score').textContent = `Entrevista concluída! Sua pontuação: ${pontuacao}/${perguntas.length*2}`;
            }
        }

        // Inicia a primeira pergunta
        window.onload = nextQuestion;
    </script>
</body>
</html>
