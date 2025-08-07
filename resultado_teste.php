<?php
require_once 'config.php';
require_once 'verificar_auth.php';
require_once 'badges_manager.php';

// Verificar se o usuário está logado
verificarLogin();

// Conectar ao banco de dados
$pdo = conectarBD();

if (!isset($_GET['sessao'])) {
    header('Location: simulador_provas.php');
    exit;
}

$sessao_id = $_GET['sessao'];

// Buscar dados da sessão e resultado
$stmt = $pdo->prepare("
    SELECT st.*, rt.pontuacao, rt.acertos, rt.total_questoes, rt.questoes_respondidas, rt.tempo_gasto
    FROM sessoes_teste st
    LEFT JOIN resultados_testes rt ON st.id = rt.sessao_id
    WHERE st.id = ? AND st.usuario_id = ?
");
$stmt->execute([$sessao_id, $_SESSION['user_id']]);
$resultado = $stmt->fetch();

if (!$resultado) {
    header('Location: simulador_provas.php');
    exit;
}

// Configurações das provas
$config_provas = [
    'toefl' => ['nome' => 'TOEFL', 'cor' => '#4CAF50'],
    'ielts' => ['nome' => 'IELTS', 'cor' => '#2196F3'],
    'sat' => ['nome' => 'SAT', 'cor' => '#FF9800'],
    'dele' => ['nome' => 'DELE', 'cor' => '#E91E63'],
    'delf' => ['nome' => 'DELF', 'cor' => '#3F51B5'],
    'testdaf' => ['nome' => 'TestDaF', 'cor' => '#795548'],
    'jlpt' => ['nome' => 'JLPT', 'cor' => '#9C27B0'],
    'hsk' => ['nome' => 'HSK', 'cor' => '#F44336']
];

$prova = $config_provas[$resultado['tipo_prova']];

// Buscar badges conquistadas neste teste
$stmt = $pdo->prepare("
    SELECT b.nome, b.descricao, b.icone 
    FROM badges b 
    INNER JOIN usuario_badges ub ON b.id = ub.badge_id 
    WHERE ub.usuario_id = ? AND DATE(ub.data_conquista) = DATE(NOW())
");
$stmt->execute([$_SESSION['user_id']]);
$badges_conquistadas = $stmt->fetchAll();

// Calcular estatísticas
$porcentagem_acertos = $resultado['total_questoes'] > 0 ? ($resultado['acertos'] / $resultado['total_questoes']) * 100 : 0;
$tempo_formatado = gmdate('H:i:s', $resultado['tempo_gasto']);

// Determinar nível de desempenho
function obterNivelDesempenho($pontuacao) {
    if ($pontuacao >= 90) return ['nivel' => 'Excelente', 'cor' => '#4CAF50', 'icone' => '🏆'];
    if ($pontuacao >= 80) return ['nivel' => 'Muito Bom', 'cor' => '#2196F3', 'icone' => '🥇'];
    if ($pontuacao >= 70) return ['nivel' => 'Bom', 'cor' => '#FF9800', 'icone' => '🥈'];
    if ($pontuacao >= 60) return ['nivel' => 'Satisfatório', 'cor' => '#FFC107', 'icone' => '🥉'];
    return ['nivel' => 'Precisa Melhorar', 'cor' => '#F44336', 'icone' => '📚'];
}

$desempenho = obterNivelDesempenho($resultado['pontuacao']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado do Teste - <?php echo $prova['nome']; ?></title>
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        .resultado-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .resultado-header {
            text-align: center;
            background: linear-gradient(135deg, <?php echo $prova['cor']; ?>, <?php echo $prova['cor']; ?>dd);
            color: white;
            padding: 40px 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .resultado-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .pontuacao-principal {
            background: white;
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .pontuacao-numero {
            font-size: 4rem;
            font-weight: bold;
            color: <?php echo $desempenho['cor']; ?>;
            margin-bottom: 10px;
        }
        
        .nivel-desempenho {
            display: inline-block;
            background: <?php echo $desempenho['cor']; ?>;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 20px;
        }
        
        .estatisticas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-left: 4px solid <?php echo $prova['cor']; ?>;
        }
        
        .stat-card h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1rem;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: <?php echo $prova['cor']; ?>;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        .badges-conquistadas {
            background: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .badges-conquistadas h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        
        .badges-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .badge-card {
            background: linear-gradient(45deg, #FFD700, #FFA500);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            color: #333;
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3);
        }
        
        .badge-icone {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .badge-nome {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .badge-descricao {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .analise-detalhada {
            background: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .progresso-visual {
            margin: 20px 0;
        }
        
        .barra-progresso {
            width: 100%;
            height: 20px;
            background: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 10px;
        }
        
        .progresso-fill {
            height: 100%;
            background: linear-gradient(90deg, <?php echo $desempenho['cor']; ?>, <?php echo $desempenho['cor']; ?>dd);
            border-radius: 10px;
            transition: width 1s ease;
        }
        
        .acoes-resultado {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn-acao {
            padding: 15px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-refazer {
            background: <?php echo $prova['cor']; ?>;
            color: white;
        }
        
        .btn-historico {
            background: #6c757d;
            color: white;
        }
        
        .btn-simulador {
            background: #28a745;
            color: white;
        }
        
        .btn-acao:hover {
            filter: brightness(1.1);
            transform: translateY(-2px);
        }
        
        .dicas-melhoria {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #2196F3;
            margin-top: 20px;
        }
        
        .dicas-melhoria h3 {
            color: #1976D2;
            margin-bottom: 15px;
        }
        
        .dicas-melhoria ul {
            color: #333;
            line-height: 1.6;
        }
        
        @media (max-width: 768px) {
            .resultado-header h1 {
                font-size: 2rem;
            }
            
            .pontuacao-numero {
                font-size: 3rem;
            }
            
            .estatisticas-grid {
                grid-template-columns: 1fr;
            }
            
            .acoes-resultado {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'header_status.php'; ?>
    
    <div class="resultado-container">
        <div class="resultado-header">
            <h1><?php echo $desempenho['icone']; ?> Resultado do Teste</h1>
            <p><?php echo $prova['nome']; ?> • <?php echo date('d/m/Y H:i', strtotime($resultado['inicio'])); ?></p>
        </div>
        
        <div class="pontuacao-principal">
            <div class="pontuacao-numero"><?php echo number_format($resultado['pontuacao'], 1); ?>%</div>
            <div class="nivel-desempenho">
                <?php echo $desempenho['icone']; ?> <?php echo $desempenho['nivel']; ?>
            </div>
            <div class="progresso-visual">
                <div class="barra-progresso">
                    <div class="progresso-fill" style="width: <?php echo $resultado['pontuacao']; ?>%"></div>
                </div>
                <p>Você acertou <?php echo $resultado['acertos']; ?> de <?php echo $resultado['total_questoes']; ?> questões</p>
            </div>
        </div>
        
        <div class="estatisticas-grid">
            <div class="stat-card">
                <h3>Questões Acertadas</h3>
                <div class="stat-value"><?php echo $resultado['acertos']; ?></div>
                <div class="stat-label">de <?php echo $resultado['total_questoes']; ?> questões</div>
            </div>
            
            <div class="stat-card">
                <h3>Questões Respondidas</h3>
                <div class="stat-value"><?php echo $resultado['questoes_respondidas']; ?></div>
                <div class="stat-label">de <?php echo $resultado['total_questoes']; ?> questões</div>
            </div>
            
            <div class="stat-card">
                <h3>Tempo Gasto</h3>
                <div class="stat-value"><?php echo $tempo_formatado; ?></div>
                <div class="stat-label">tempo total</div>
            </div>
            
            <div class="stat-card">
                <h3>Taxa de Acerto</h3>
                <div class="stat-value"><?php echo number_format($porcentagem_acertos, 1); ?>%</div>
                <div class="stat-label">das respondidas</div>
            </div>
        </div>
        
        <?php if (!empty($badges_conquistadas)): ?>
        <div class="badges-conquistadas">
            <h2>🏆 Badges Conquistadas!</h2>
            <div class="badges-grid">
                <?php foreach ($badges_conquistadas as $badge): ?>
                    <div class="badge-card">
                        <div class="badge-icone"><?php echo $badge['icone']; ?></div>
                        <div class="badge-nome"><?php echo htmlspecialchars($badge['nome']); ?></div>
                        <div class="badge-descricao"><?php echo htmlspecialchars($badge['descricao']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="analise-detalhada">
            <h2>📊 Análise Detalhada</h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0;">
                <div>
                    <h4>Desempenho Geral</h4>
                    <p>Sua pontuação de <strong><?php echo number_format($resultado['pontuacao'], 1); ?>%</strong> 
                    <?php if ($resultado['pontuacao'] >= 70): ?>
                        indica um bom domínio do conteúdo. Continue praticando para manter este nível!
                    <?php elseif ($resultado['pontuacao'] >= 60): ?>
                        mostra que você está no caminho certo. Com mais estudo, pode alcançar resultados ainda melhores.
                    <?php else: ?>
                        indica que há espaço para melhoria. Recomendamos revisar os conteúdos e fazer mais simulados.
                    <?php endif; ?>
                    </p>
                </div>
                
                <div>
                    <h4>Tempo de Execução</h4>
                    <p>Você completou o teste em <strong><?php echo $tempo_formatado; ?></strong>.
                    <?php 
                    $tempo_medio_por_questao = $resultado['tempo_gasto'] / $resultado['questoes_respondidas'];
                    if ($tempo_medio_por_questao < 60): ?>
                        Você foi bem rápido nas respostas. Certifique-se de ler as questões com atenção.
                    <?php elseif ($tempo_medio_por_questao > 180): ?>
                        Você levou bastante tempo por questão. Pratique para ganhar mais agilidade.
                    <?php else: ?>
                        Seu ritmo foi adequado para este tipo de prova.
                    <?php endif; ?>
                    </p>
                </div>
            </div>
            
            <?php if ($resultado['pontuacao'] < 70): ?>
            <div class="dicas-melhoria">
                <h3>💡 Dicas para Melhorar</h3>
                <ul>
                    <li>Revise os conteúdos das questões que você errou</li>
                    <li>Faça mais simulados para ganhar experiência</li>
                    <li>Pratique gerenciamento de tempo durante os estudos</li>
                    <li>Identifique suas áreas de maior dificuldade</li>
                    <li>Busque materiais complementares de estudo</li>
                </ul>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="acoes-resultado">
            <a href="executar_teste.php?tipo=<?php echo $resultado['tipo_prova']; ?>" class="btn-acao btn-refazer">
                🔄 Refazer Teste
            </a>
            
            <a href="historico_testes.php" class="btn-acao btn-historico">
                📊 Ver Histórico
            </a>
            
            <a href="simulador_provas.php" class="btn-acao btn-simulador">
                🎯 Outros Simulados
            </a>
        </div>
    </div>
    
    <script src="public/js/main.js"></script>
    <script>
        // Animação da barra de progresso
        document.addEventListener('DOMContentLoaded', function() {
            const progressBar = document.querySelector('.progresso-fill');
            if (progressBar) {
                setTimeout(() => {
                    progressBar.style.width = '<?php echo $resultado['pontuacao']; ?>%';
                }, 500);
            }
        });
    </script>
</body>
</html>