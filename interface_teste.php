<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executando: <?php echo $prova['nome']; ?></title>
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .teste-interface {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
        
        .sidebar {
            width: 300px;
            background: white;
            border-right: 1px solid #ddd;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar-header {
            padding: 20px;
            background: <?php echo $prova['cor']; ?>;
            color: white;
            text-align: center;
        }
        
        .sidebar-header h2 {
            margin: 0 0 10px 0;
            font-size: 1.2rem;
        }
        
        .cronometro {
            font-size: 1.5rem;
            font-weight: bold;
            font-family: 'Courier New', monospace;
        }
        
        .navegacao-questoes {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }
        
        .grid-questoes {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
        }
        
        .btn-questao {
            width: 40px;
            height: 40px;
            border: 2px solid #ddd;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        
        .btn-questao:hover {
            border-color: <?php echo $prova['cor']; ?>;
        }
        
        .btn-questao.atual {
            background: <?php echo $prova['cor']; ?>;
            color: white;
            border-color: <?php echo $prova['cor']; ?>;
        }
        
        .btn-questao.respondida {
            background: #4CAF50;
            color: white;
            border-color: #4CAF50;
        }
        
        .btn-questao.marcada {
            background: #FF9800;
            color: white;
            border-color: #FF9800;
        }
        
        .legenda {
            margin-top: 20px;
            font-size: 0.9rem;
        }
        
        .legenda-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .legenda-cor {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            margin-right: 8px;
        }
        
        .area-questao {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: white;
        }
        
        .questao-header {
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .questao-numero {
            font-size: 1.2rem;
            font-weight: bold;
            color: <?php echo $prova['cor']; ?>;
        }
        
        .questao-acoes {
            display: flex;
            gap: 10px;
        }
        
        .btn-marcar {
            padding: 8px 16px;
            background: #FF9800;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
        }
        
        .questao-conteudo {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }
        
        .enunciado {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 30px;
            color: #333;
        }
        
        .alternativas {
            margin-bottom: 30px;
        }
        
        .alternativa {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            padding: 15px;
            border: 2px solid #f0f0f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .alternativa:hover {
            border-color: <?php echo $prova['cor']; ?>;
            background: #f8f9ff;
        }
        
        .alternativa.selecionada {
            border-color: <?php echo $prova['cor']; ?>;
            background: #e3f2fd;
        }
        
        .alternativa input[type="radio"] {
            margin-right: 12px;
            margin-top: 2px;
        }
        
        .letra-alternativa {
            font-weight: bold;
            color: <?php echo $prova['cor']; ?>;
            margin-right: 8px;
            min-width: 20px;
        }
        
        .navegacao-questao {
            padding: 20px;
            background: #f8f9fa;
            border-top: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .btn-nav {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        
        .btn-anterior {
            background: #6c757d;
            color: white;
        }
        
        .btn-proximo {
            background: <?php echo $prova['cor']; ?>;
            color: white;
        }
        
        .btn-finalizar {
            background: #dc3545;
            color: white;
            padding: 12px 30px;
        }
        
        .btn-nav:hover {
            filter: brightness(1.1);
        }
        
        .btn-nav:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .progresso {
            flex: 1;
            margin: 0 20px;
            text-align: center;
            color: #666;
        }
        
        .barra-progresso {
            width: 100%;
            height: 8px;
            background: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 5px;
        }
        
        .progresso-fill {
            height: 100%;
            background: <?php echo $prova['cor']; ?>;
            transition: width 0.3s ease;
        }
        
        @media (max-width: 768px) {
            .teste-interface {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                height: auto;
                order: 2;
            }
            
            .navegacao-questoes {
                max-height: 200px;
            }
            
            .area-questao {
                order: 1;
                height: 70vh;
            }
        }
    </style>
</head>
<body>
    <div class="teste-interface">
        <!-- Sidebar com navegação -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2><?php echo $prova['nome']; ?></h2>
                <div class="cronometro" id="cronometro">
                    <span id="tempo-restante"><?php echo gmdate('H:i:s', $tempo_restante); ?></span>
                </div>
            </div>
            
            <div class="navegacao-questoes">
                <h3 style="margin-bottom: 15px; color: #333;">Questões</h3>
                <div class="grid-questoes" id="grid-questoes">
                    <?php for ($i = 1; $i <= $prova['questoes_total']; $i++): ?>
                        <button class="btn-questao <?php echo $i === 1 ? 'atual' : ''; ?>" 
                                onclick="irParaQuestao(<?php echo $i; ?>)" 
                                id="btn-questao-<?php echo $i; ?>">
                            <?php echo $i; ?>
                        </button>
                    <?php endfor; ?>
                </div>
                
                <div class="legenda">
                    <div class="legenda-item">
                        <div class="legenda-cor" style="background: <?php echo $prova['cor']; ?>;"></div>
                        <span>Atual</span>
                    </div>
                    <div class="legenda-item">
                        <div class="legenda-cor" style="background: #4CAF50;"></div>
                        <span>Respondida</span>
                    </div>
                    <div class="legenda-item">
                        <div class="legenda-cor" style="background: #FF9800;"></div>
                        <span>Marcada</span>
                    </div>
                    <div class="legenda-item">
                        <div class="legenda-cor" style="background: white; border: 2px solid #ddd;"></div>
                        <span>Não respondida</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Área principal da questão -->
        <div class="area-questao">
            <div class="questao-header">
                <div class="questao-numero">Questão <span id="numero-questao-atual">1</span></div>
                <div class="questao-acoes">
                    <button class="btn-marcar" onclick="marcarQuestao()">📌 Marcar</button>
                    <button class="btn-finalizar" onclick="confirmarFinalizacao()">🏁 Finalizar Teste</button>
                </div>
            </div>
            
            <div class="questao-conteudo">
                <div class="enunciado" id="enunciado-questao">
                    <!-- Conteúdo será carregado via JavaScript -->
                </div>
                
                <div class="alternativas" id="alternativas-questao">
                    <!-- Alternativas serão carregadas via JavaScript -->
                </div>
            </div>
            
            <div class="navegacao-questao">
                <button class="btn-nav btn-anterior" onclick="questaoAnterior()" id="btn-anterior">
                    ← Anterior
                </button>
                
                <div class="progresso">
                    <div>Questão <span id="questao-atual">1</span> de <?php echo $prova['questoes_total']; ?></div>
                    <div class="barra-progresso">
                        <div class="progresso-fill" id="barra-progresso" style="width: <?php echo (1 / $prova['questoes_total']) * 100; ?>%"></div>
                    </div>
                </div>
                
                <button class="btn-nav btn-proximo" onclick="proximaQuestao()" id="btn-proximo">
                    Próxima →
                </button>
            </div>
        </div>
    </div>
    
    <script>
        // Dados do teste
        const questoes = <?php echo json_encode($questoes_simuladas); ?>;
        const tempoRestante = <?php echo $tempo_restante; ?>;
        const sessaoId = <?php echo $sessao_id; ?>;
        const totalQuestoes = <?php echo $prova['questoes_total']; ?>;
        
        let questaoAtual = 1;
        let respostas = {};
        let questoesMarcadas = new Set();
        let cronometroInterval;
        
        // Inicializar teste
        document.addEventListener('DOMContentLoaded', function() {
            iniciarCronometro();
            carregarQuestao(1);
            
            // Prevenir fechamento acidental
            window.addEventListener('beforeunload', function(e) {
                e.preventDefault();
                e.returnValue = 'Tem certeza que deseja sair? Seu progresso será perdido.';
            });
        });
        
        function iniciarCronometro() {
            let segundosRestantes = tempoRestante;
            
            cronometroInterval = setInterval(function() {
                if (segundosRestantes <= 0) {
                    clearInterval(cronometroInterval);
                    finalizarTestePorTempo();
                    return;
                }
                
                const horas = Math.floor(segundosRestantes / 3600);
                const minutos = Math.floor((segundosRestantes % 3600) / 60);
                const segundos = segundosRestantes % 60;
                
                document.getElementById('tempo-restante').textContent = 
                    String(horas).padStart(2, '0') + ':' + 
                    String(minutos).padStart(2, '0') + ':' + 
                    String(segundos).padStart(2, '0');
                
                // Alerta quando restam 5 minutos
                if (segundosRestantes === 300) {
                    alert('⚠️ Atenção: Restam apenas 5 minutos!');
                }
                
                segundosRestantes--;
            }, 1000);
        }
        
        function carregarQuestao(numero) {
            const questao = questoes[numero - 1];
            
            document.getElementById('numero-questao-atual').textContent = numero;
            document.getElementById('questao-atual').textContent = numero;
            document.getElementById('enunciado-questao').textContent = questao.enunciado;
            
            // Carregar alternativas
            const alternativasContainer = document.getElementById('alternativas-questao');
            alternativasContainer.innerHTML = '';
            
            Object.entries(questao.alternativas).forEach(([letra, texto]) => {
                const div = document.createElement('div');
                div.className = 'alternativa';
                if (respostas[numero] === letra) {
                    div.classList.add('selecionada');
                }
                
                div.innerHTML = `
                    <input type="radio" name="resposta" value="${letra}" ${respostas[numero] === letra ? 'checked' : ''}>
                    <span class="letra-alternativa">${letra.toUpperCase()})</span>
                    <span>${texto}</span>
                `;
                
                div.addEventListener('click', function() {
                    selecionarAlternativa(numero, letra, div);
                });
                
                alternativasContainer.appendChild(div);
            });
            
            // Atualizar navegação
            atualizarNavegacao();
            atualizarProgresso();
        }
        
        function selecionarAlternativa(numeroQuestao, letra, elemento) {
            // Remover seleção anterior
            document.querySelectorAll('.alternativa').forEach(alt => {
                alt.classList.remove('selecionada');
            });
            
            // Adicionar nova seleção
            elemento.classList.add('selecionada');
            elemento.querySelector('input').checked = true;
            
            // Salvar resposta
            respostas[numeroQuestao] = letra;
            
            // Atualizar status da questão
            atualizarStatusQuestao(numeroQuestao);
            
            // Salvar no servidor (simulado)
            salvarResposta(numeroQuestao, letra);
        }
        
        function atualizarStatusQuestao(numero) {
            const btn = document.getElementById(`btn-questao-${numero}`);
            btn.classList.remove('atual', 'respondida', 'marcada');
            
            if (numero === questaoAtual) {
                btn.classList.add('atual');
            } else if (questoesMarcadas.has(numero)) {
                btn.classList.add('marcada');
            } else if (respostas[numero]) {
                btn.classList.add('respondida');
            }
        }
        
        function irParaQuestao(numero) {
            // Atualizar questão atual
            document.querySelectorAll('.btn-questao').forEach(btn => {
                btn.classList.remove('atual');
            });
            
            questaoAtual = numero;
            carregarQuestao(numero);
            
            // Atualizar todos os status
            for (let i = 1; i <= totalQuestoes; i++) {
                atualizarStatusQuestao(i);
            }
        }
        
        function questaoAnterior() {
            if (questaoAtual > 1) {
                irParaQuestao(questaoAtual - 1);
            }
        }
        
        function proximaQuestao() {
            if (questaoAtual < totalQuestoes) {
                irParaQuestao(questaoAtual + 1);
            }
        }
        
        function marcarQuestao() {
            if (questoesMarcadas.has(questaoAtual)) {
                questoesMarcadas.delete(questaoAtual);
            } else {
                questoesMarcadas.add(questaoAtual);
            }
            atualizarStatusQuestao(questaoAtual);
        }
        
        function atualizarNavegacao() {
            document.getElementById('btn-anterior').disabled = questaoAtual === 1;
            document.getElementById('btn-proximo').disabled = questaoAtual === totalQuestoes;
        }
        
        function atualizarProgresso() {
            const progresso = (questaoAtual / totalQuestoes) * 100;
            document.getElementById('barra-progresso').style.width = progresso + '%';
        }
        
        function salvarResposta(questao, resposta) {
            // Simular salvamento no servidor
            console.log(`Salvando resposta: Questão ${questao} = ${resposta}`);
        }
        
        function confirmarFinalizacao() {
            const respondidas = Object.keys(respostas).length;
            const naoRespondidas = totalQuestoes - respondidas;
            
            let mensagem = `Você respondeu ${respondidas} de ${totalQuestoes} questões.`;
            if (naoRespondidas > 0) {
                mensagem += `\n\n${naoRespondidas} questões não foram respondidas.`;
            }
            mensagem += '\n\nDeseja finalizar o teste?';
            
            if (confirm(mensagem)) {
                finalizarTeste();
            }
        }
        
        function finalizarTeste() {
            clearInterval(cronometroInterval);
            
            // Enviar respostas para o servidor
            const formData = new FormData();
            formData.append('sessao_id', sessaoId);
            formData.append('respostas', JSON.stringify(respostas));
            formData.append('finalizar', '1');
            
            fetch('processar_teste.php', {
                method: 'POST',
                body: formData
            }).then(response => {
                if (response.ok) {
                    window.location.href = `resultado_teste.php?sessao=${sessaoId}`;
                } else {
                    alert('Erro ao finalizar teste. Tente novamente.');
                }
            }).catch(error => {
                console.error('Erro:', error);
                alert('Erro de conexão. Tente novamente.');
            });
        }
        
        function finalizarTestePorTempo() {
            alert('⏰ Tempo esgotado! O teste será finalizado automaticamente.');
            finalizarTeste();
        }
    </script>
</body>
</html>