<?php
require_once 'config.php';
require_once 'verificar_auth.php';
require_once 'questoes_manager.php';

// Verificar se o usuário está logado
verificarLogin();

// Conectar ao banco de dados
$pdo = conectarBD();

// Verificar se o tipo de prova foi especificado
if (!isset($_GET['tipo'])) {
    header('Location: simulador_provas.php');
    exit;
}

$tipo_prova = $_GET['tipo'];

// Definir configurações das provas
$config_provas = [
    'toefl' => [
        'nome' => 'TOEFL',
        'duracao_minutos' => 180, // 3h
        'questoes_total' => 120,
        'cor' => '#4CAF50'
    ],
    'ielts' => [
        'nome' => 'IELTS',
        'duracao_minutos' => 165, // 2h45min
        'questoes_total' => 100,
        'cor' => '#2196F3'
    ],
    'sat' => [
        'nome' => 'SAT',
        'duracao_minutos' => 180, // 3h
        'questoes_total' => 154,
        'cor' => '#FF9800'
    ],
    'dele' => [
        'nome' => 'DELE',
        'duracao_minutos' => 240, // 4h
        'questoes_total' => 80,
        'cor' => '#E91E63'
    ],
    'delf' => [
        'nome' => 'DELF',
        'duracao_minutos' => 150, // 2h30min
        'questoes_total' => 75,
        'cor' => '#3F51B5'
    ],
    'testdaf' => [
        'nome' => 'TestDaF',
        'duracao_minutos' => 190, // 3h10min
        'questoes_total' => 85,
        'cor' => '#795548'
    ],
    'jlpt' => [
        'nome' => 'JLPT',
        'duracao_minutos' => 170, // 2h50min
        'questoes_total' => 95,
        'cor' => '#9C27B0'
    ],
    'hsk' => [
        'nome' => 'HSK',
        'duracao_minutos' => 135, // 2h15min
        'questoes_total' => 70,
        'cor' => '#607D8B'
    ]
];

if (!isset($config_provas[$tipo_prova])) {
    header('Location: simulador_provas.php');
    exit;
}

$prova = $config_provas[$tipo_prova];

// Gerar token CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Processar início do teste
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['iniciar_teste'])) {
    // Verificar token CSRF
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Token CSRF inválido');
    }
    
    // Criar nova sessão de teste
    $stmt = $pdo->prepare("INSERT INTO sessoes_teste (usuario_id, tipo_prova, inicio, duracao_minutos, status) VALUES (?, ?, NOW(), ?, 'ativo')");
    $stmt->execute([$_SESSION['user_id'], $tipo_prova, $prova['duracao_minutos']]);
    $sessao_id = $pdo->lastInsertId();
    
    // Redirecionar para a execução do teste
    header("Location: executar_teste.php?tipo=$tipo_prova&sessao=$sessao_id&executando=1");
    exit;
}

// Se está executando o teste
if (isset($_GET['executando']) && isset($_GET['sessao'])) {
    $sessao_id = $_GET['sessao'];
    
    // Verificar se a sessão existe e pertence ao usuário
    $stmt = $pdo->prepare("SELECT * FROM sessoes_teste WHERE id = ? AND usuario_id = ? AND status = 'ativo'");
    $stmt->execute([$sessao_id, $_SESSION['user_id']]);
    $sessao = $stmt->fetch();
    
    if (!$sessao) {
        header('Location: simulador_provas.php');
        exit;
    }
    
    // Verificar se o tempo não expirou
    $inicio = new DateTime($sessao['inicio']);
    $agora = new DateTime();
    $tempo_decorrido = $agora->getTimestamp() - $inicio->getTimestamp();
    $tempo_limite = $sessao['duracao_minutos'] * 60;
    
    if ($tempo_decorrido >= $tempo_limite) {
        // Tempo expirado, finalizar teste
        $stmt = $pdo->prepare("UPDATE sessoes_teste SET status = 'expirado', fim = NOW() WHERE id = ?");
        $stmt->execute([$sessao_id]);
        header('Location: resultado_teste.php?sessao=' . $sessao_id);
        exit;
    }
    
    $tempo_restante = $tempo_limite - $tempo_decorrido;
    
    // Carregar questões (simuladas por enquanto)
    $questoes_simuladas = [];
    for ($i = 1; $i <= $prova['questoes_total']; $i++) {
        $questoes_simuladas[] = [
            'id' => $i,
            'enunciado' => "Esta é a questão número $i do simulado {$prova['nome']}. O conteúdo das questões será carregado posteriormente de um arquivo JSON/XML.",
            'alternativas' => [
                'a' => 'Alternativa A - Primeira opção de resposta',
                'b' => 'Alternativa B - Segunda opção de resposta', 
                'c' => 'Alternativa C - Terceira opção de resposta',
                'd' => 'Alternativa D - Quarta opção de resposta',
                'e' => 'Alternativa E - Quinta opção de resposta'
            ],
            'resposta_correta' => ['a', 'b', 'c', 'd', 'e'][rand(0, 4)]
        ];
    }
    
    include 'interface_teste.php';
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $prova['nome']; ?> - Simulador</title>
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        .teste-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .teste-header {
            text-align: center;
            background: linear-gradient(135deg, <?php echo $prova['cor']; ?>, <?php echo $prova['cor']; ?>dd);
            color: white;
            padding: 40px 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .teste-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .teste-info {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-item {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid <?php echo $prova['cor']; ?>;
        }
        
        .info-item h3 {
            color: #333;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }
        
        .info-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: <?php echo $prova['cor']; ?>;
        }
        
        .instrucoes {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #2196F3;
            margin-bottom: 30px;
        }
        
        .instrucoes h3 {
            color: #1976D2;
            margin-bottom: 15px;
        }
        
        .instrucoes ul {
            color: #333;
            line-height: 1.6;
        }
        
        .instrucoes li {
            margin-bottom: 8px;
        }
        
        .btn-iniciar-teste {
            width: 100%;
            padding: 20px;
            background: linear-gradient(135deg, <?php echo $prova['cor']; ?>, <?php echo $prova['cor']; ?>dd);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-iniciar-teste:hover {
            filter: brightness(1.1);
            transform: translateY(-2px);
        }
        
        .aviso-importante {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .aviso-importante strong {
            color: #d63031;
        }
    </style>
</head>
<body>
    <?php include 'header_status.php'; ?>
    
    <div class="teste-container">
        <div class="teste-header">
            <h1>📝 <?php echo $prova['nome']; ?></h1>
            <p>Prepare-se para o simulado</p>
        </div>
        
        <div class="teste-info">
            <div class="info-grid">
                <div class="info-item">
                    <h3>Duração</h3>
                    <div class="info-value"><?php echo floor($prova['duracao_minutos'] / 60); ?>h<?php echo $prova['duracao_minutos'] % 60 > 0 ? sprintf('%02dm', $prova['duracao_minutos'] % 60) : ''; ?></div>
                </div>
                <div class="info-item">
                    <h3>Questões</h3>
                    <div class="info-value"><?php echo $prova['questoes_total']; ?></div>
                </div>
                <div class="info-item">
                    <h3>Tipo</h3>
                    <div class="info-value"><?php echo $prova['nome']; ?></div>
                </div>
            </div>
            
            <div class="instrucoes">
                <h3>📋 Instruções Importantes</h3>
                <ul>
                    <li>Leia atentamente cada questão antes de responder</li>
                    <li>Você pode navegar entre as questões livremente</li>
                    <li>Suas respostas são salvas automaticamente</li>
                    <li>O cronômetro iniciará assim que você clicar em "Iniciar Teste"</li>
                    <li>Certifique-se de ter uma conexão estável com a internet</li>
                    <li>Não feche a aba do navegador durante o teste</li>
                </ul>
            </div>
            
            <div class="aviso-importante">
                <strong>⚠️ Atenção:</strong> Uma vez iniciado, o teste não pode ser pausado. 
                Certifique-se de que você tem tempo suficiente para completá-lo.
            </div>
            
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <button type="submit" name="iniciar_teste" class="btn-iniciar-teste">
                    🚀 Iniciar Teste Agora
                </button>
            </form>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="simulador_provas.php" class="btn btn-secondary">
                ← Voltar para Seleção de Provas
            </a>
        </div>
    </div>
    
    <script src="public/js/main.js"></script>
</body>
</html>