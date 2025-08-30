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
    <?php require_once 'head_common.php'; ?>
    <title>Calculadora de GPA - DayDreaming</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <style> 
        .header-container {
            background-color: #03254c;
            padding: 20px 0;
        }
        img {
            max-width: 100%;
            height: auto;
        }
        .navbutton {
            color: white;
            font-size: clamp(10px, 2vw, 20px);
            text-align: center;
            width: 100%;
            text-decoration: none;
            display: block;
            padding: 15px 5px;
        }
        .navbutton:hover {
            color: #f0f0f0;
            text-decoration: none;
        }
        @media (max-width: 768px) {
            .header-container {
                padding: 10px 0;
            }
        }
        .title1{ 
            font-size: clamp(18px, 4vw, 32px);
        }
        strong{
            color: #2a9df4;
        }
        .notas-box {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .nota-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .nota-label {
            flex-grow: 1;
            margin-right: 10px;
            font-weight: 500;
        }
        .nota-input {
            width: 80px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn-remove {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            padding: 5px;
            margin-left: 10px;
            transition: transform 0.2s;
        }
        .btn-remove:hover {
            transform: scale(1.2);
            color: #c82333;
        }
        .btn-remove i {
            font-size: 18px;
        }
        .action-buttons {
            text-align: center;
            margin-top: 20px;
        }
        .action-buttons button {
            margin: 0 10px;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
        }
        #resultado-container {
            text-align: center;
            margin-top: 20px;
            display: none;
        }
        #resultado {
            font-size: 20px;
            font-weight: bold;
            color: #03254c;
            margin-bottom: 15px;
        }
        .titulo-notas {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .titulo-notas img {
            margin-right: 15px;
        }
        .como-funciona {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-top: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .como-funciona p:first-child {
            font-weight: bold;
            font-size: 1.2rem;
            color: #03254c;
            margin-bottom: 10px;
        }
        
        /* Melhorias de responsividade */
        @media (max-width: 576px) {
            .container-fluid {
                padding-left: 10px;
                padding-right: 10px;
            }
            .nota-item {
                flex-direction: column;
                align-items: flex-start;
            }
            .nota-label {
                margin-bottom: 5px;
                margin-right: 0;
            }
            .nota-input {
                width: 100%;
                margin-bottom: 5px;
            }
            .btn-remove {
                align-self: flex-end;
            }
            .action-buttons button {
                display: block;
                width: 100%;
                margin: 10px 0;
            }
            .titulo-notas {
                flex-direction: column;
                text-align: center;
            }
            .titulo-notas img {
                margin-right: 0;
                margin-bottom: 10px;
            }
        }
        
        @media (min-width: 577px) and (max-width: 768px) {
            .nota-item {
                flex-wrap: wrap;
            }
            .nota-label {
                width: 100%;
                margin-right: 0;
                margin-bottom: 5px;
            }
        }
        
        @media (min-width: 769px) and (max-width: 992px) {
            .nota-item {
                padding: 8px;
            }
            .nota-input {
                width: 70px;
            }
        }
    </style>
</head>
<body>
    <!-- Header de Status de Login -->
    <div style="background: linear-gradient(135deg, #187bcb 0%, #6c5ce7 100%); color: white; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <?php if ($usuario_logado): ?>
                <span>✅ Logado como: <?php echo htmlspecialchars($nome_usuario ?? 'Usuário'); ?></span>
            <?php else: ?>
                <span>❌ Você não está logado</span>
            <?php endif; ?>
        </div>
        <div>
            <?php if ($usuario_logado): ?>
                <a href="logout.php" style="color: white; text-decoration: none; background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px;">🚪 Sair</a>
            <?php else: ?>
                <a href="login.php" style="color: white; text-decoration: none; background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px;">🔑 Fazer Login</a>
            <?php endif; ?>
        </div>
    </div>


    
    <!-- Navigation -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-3 col-lg-3 col-sm-6 col-md-6 col-6" style="background-color: #2a9df4; min-height: 60px; border: 5px solid white; display: flex; justify-content: center; align-items: center;">
                <a href="index.php#quem-somos" class="navbutton">Quem Somos</a>
            </div>
            <div class="col-xl-3 col-lg-3 col-sm-6 col-md-6 col-6" style="background-color: #2a9df4; min-height: 60px; border: 5px solid white; display: flex; justify-content: center; align-items: center;">
                <a href="testes_internacionais.php" class="navbutton">Teste Vocacional</a>
            </div>
            <div class="col-xl-3 col-lg-3 col-sm-6 col-md-6 col-6" style="background-color: #2a9df4; min-height: 60px; border: 5px solid white; display: flex; justify-content: center; align-items: center;">
                <a href="simulador_provas.php" class="navbutton">Simulador Prático</a>
            </div>
            <div class="col-xl-3 col-lg-3 col-sm-6 col-md-6 col-6" style="background-color: #2a9df4; min-height: 60px; border: 5px solid white; display: flex; justify-content: center; align-items: center;">
                <a href="forum.php" class="navbutton">Comunidade</a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid mt-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 text-center">
                <p class="title1">Calculadora de <strong>GPA</strong></p>
                <p>Insira suas notas do ensino médio e calcule seu GPA para universidades internacionais</p>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                <div class="titulo-notas">
                    <img src="imagens/calculadora (1).png" style="width: 60px;height: 60px;">
                    <div>
                        <p class="title1" style="font-weight: bold; margin-bottom: 0;">Suas Notas</p>
                        <p>Insira suas notas de 0 a 10 do sistema brasileiro</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-3">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-8">
                <div class="notas-box">
                    <div id="notas-container" class="row"></div>
                </div>
                <div class="action-buttons">
                    <button onclick="adicionarNota()" class="btn btn-primary">Adicionar Nota</button>
                    <button onclick="converterGPA()" class="btn btn-success">Converter para GPA</button>
                </div>
                <div id="resultado-container">
                    <div class="d-flex justify-content-center align-items-center flex-wrap">
                        <div id="resultado"></div>
                        <?php if ($usuario_logado): ?>
                            <button onclick="salvarGPA()" class="btn btn-success ml-3">Salvar GPA</button>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-info ml-3">
                                <i class="fas fa-sign-in-alt"></i> Faça login para salvar
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                <div class="como-funciona">
                    <p>Como funciona?</p>
                    <p>Este calculador converte suas notas do sistema brasileiro (0-10) para o sistema americano GPA (0-4.0). A conversão é feita através da fórmula: GPA = (Nota Brasileira ÷ 10) × 4</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let contadorNotas = 0;

        function criarCampoNota() {
            contadorNotas++;
            const container = document.getElementById("notas-container");

            // Criar uma nova linha a cada 4 notas
            if (contadorNotas % 4 === 1) {
                const newRow = document.createElement("div");
                newRow.classList.add("w-100");
                container.appendChild(newRow);
            }

            // Criar o elemento da nota
            const label = document.createElement("div");
            label.classList.add("nota-item", "col-6", "col-md-3"); // 2 por linha em mobile, 4 em desktop
            label.id = `nota-${contadorNotas}`;
            label.innerHTML = `
                <div class="nota-label">Nota ${contadorNotas} (0 a 10):</div>
                <input type="number" class="nota-input nota" step="0.1" min="0" max="10">
                <button onclick="removerNota('nota-${contadorNotas}')" class="btn-remove">
                    <i class="fas fa-trash-alt"></i>
                </button>
            `;
            container.appendChild(label);
        }

        function adicionarNota() {
            criarCampoNota();
        }

        function removerNota(id) {
            const campo = document.getElementById(id);
            if (campo) campo.remove();
        }

        function converterNotaParaGPA(nota) {
            return (nota / 10) * 4;
        }

        function converterGPA() {
            const inputs = document.querySelectorAll(".nota");
            let soma = 0;
            let total = 0;
            inputs.forEach(input => {
                const valor = parseFloat(input.value);
                if (!isNaN(valor)) {
                    soma += converterNotaParaGPA(valor);
                    total++;
                }
            });
            const mediaGPA = total > 0 ? (soma / total).toFixed(2) : "N/A";
            const resultado = total > 0
                ? `Seu GPA aproximado é: ${mediaGPA}`
                : "Por favor, insira ao menos uma nota válida.";

            document.getElementById("resultado").innerText = resultado;
            document.getElementById("resultado-container").style.display = "block";
        }

        function salvarGPA() {
            const resultado = document.getElementById("resultado").innerText;
            if (resultado && resultado !== "Por favor, insira ao menos uma nota válida.") {
                <?php if ($usuario_logado): ?>
                    // Coletar notas para envio
                    const inputs = document.querySelectorAll(".nota");
                    const notas = [];
                    inputs.forEach(input => {
                        const valor = parseFloat(input.value);
                        if (!isNaN(valor)) {
                            notas.push(valor);
                        }
                    });

                    if (notas.length === 0) {
                        alert("Nenhuma nota válida encontrada.");
                        return;
                    }

                    // Calcular GPA
                    let soma = 0;
                    notas.forEach(nota => {
                        soma += (nota / 10) * 4;
                    });
                    const gpa = (soma / notas.length).toFixed(2);

                    // Coletar dados para envio
                    const dadosEnvio = {
                        gpa: parseFloat(gpa),
                        notas: notas
                    };

                    // Enviar para o servidor
                    fetch('salvar_gpa.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(dadosEnvio)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("GPA salvo com sucesso!");
                            if (data.badge_conquistada) {
                                alert("🏆 Parabéns! Você conquistou uma nova badge: " + data.badge_nome);
                            }
                        } else {
                            alert("Erro ao salvar GPA: " + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        alert("Erro ao salvar GPA. Tente novamente.");
                    });
                <?php else: ?>
                    alert("Faça login para salvar seu GPA.");
                    window.location.href = 'login.php';
                <?php endif; ?>
            } else {
                alert("Não há GPA para salvar. Por favor, calcule seu GPA primeiro.");
            }
        }

        // Criar 12 campos iniciais ao carregar
        window.onload = function () {
            for (let i = 0; i < 12; i++) {
                criarCampoNota();
            }
        };
    </script>
</body>
</html>
