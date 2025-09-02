<?php
/**
 * Teste de Múltiplos Países Visitados
 */

require_once 'config.php';
require_once 'tracking_paises.php';

// Iniciar sessão
iniciarSessaoSegura();

// Fazer login automático
if (!isset($_SESSION['usuario_id'])) {
    try {
        $pdo = conectarBD();
        $stmt = $pdo->prepare("SELECT id, usuario, senha, nome, is_admin FROM usuarios WHERE usuario = 'teste'");
        $stmt->execute();
        $usuario_teste = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario_teste && password_verify('teste123', $usuario_teste['senha'])) {
            $_SESSION['usuario_id'] = $usuario_teste['id'];
            $_SESSION['usuario_nome'] = $usuario_teste['nome'];
            $_SESSION['usuario_login'] = $usuario_teste['usuario'];
            $_SESSION['is_admin'] = (bool)$usuario_teste['is_admin'];
        }
    } catch (Exception $e) {
        // Ignorar erro
    }
}

$usuario_id = $_SESSION['usuario_id'] ?? null;
$usuario_logado = isset($_SESSION['usuario_id']);
$paises_visitados = [];

if ($usuario_logado) {
    $paises_visitados = obterPaisesVisitados($_SESSION['usuario_id']);
    $estatisticas = obterEstatisticasPaises($_SESSION['usuario_id']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste - Múltiplos Países Visitados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }
        .test-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin: 20px 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .country-btn {
            margin: 5px;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .country-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .visited-country {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
        }
        .stat-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'header_status.php'; ?>
    
    <div class="container mt-4">
        <div class="test-card text-center">
            <h1>🌍 Teste - Múltiplos Países Visitados</h1>
            <p class="lead">Teste o sistema de tracking com vários países</p>
            
            <div class="alert alert-info">
                <h5>📊 Status Atual</h5>
                <p><strong>Usuário:</strong> <?= $_SESSION['usuario_nome'] ?? 'Não logado' ?></p>
                <p><strong>Países visitados:</strong> <?= count($paises_visitados) ?></p>
                <p><strong>Total de visitas:</strong> <?= $estatisticas['total_visitas'] ?? 0 ?></p>
            </div>
        </div>
        
        <div class="test-card">
            <h3>📊 Estatísticas Gerais</h3>
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-box">
                        <h4 class="text-primary"><?= $estatisticas['total_paises'] ?? 0 ?></h4>
                        <small>Países Visitados</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <h4 class="text-success"><?= $estatisticas['total_visitas'] ?? 0 ?></h4>
                        <small>Total de Visitas</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <h4 class="text-warning">
                            <?= $estatisticas['pais_mais_visitado']['pais_nome'] ?? 'Nenhum' ?>
                        </h4>
                        <small>País Mais Visitado</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <h4 class="text-info">
                            <?= $estatisticas['pais_mais_visitado']['total_visitas'] ?? 0 ?>x
                        </h4>
                        <small>Máximo de Visitas</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="test-card">
            <h3>🌍 Países com Tracking Implementado</h3>
            <p>Clique nos países abaixo para registrar visitas:</p>
            
            <div class="row">
                <div class="col-md-4">
                    <h5>🇺🇸 Américas</h5>
                    <a href="paises/eua.php" class="btn btn-primary country-btn w-100 mb-2">
                        🇺🇸 Estados Unidos
                    </a>
                    <a href="paises/canada.php" class="btn btn-success country-btn w-100 mb-2">
                        🇨🇦 Canadá
                    </a>
                </div>
                
                <div class="col-md-4">
                    <h5>🇪🇺 Europa</h5>
                    <a href="paises/alemanha.php" class="btn btn-warning country-btn w-100 mb-2">
                        🇩🇪 Alemanha
                    </a>
                    <a href="paises/franca.php" class="btn btn-info country-btn w-100 mb-2">
                        🇫🇷 França
                    </a>
                    <a href="paises/italia.php" class="btn btn-secondary country-btn w-100 mb-2">
                        🇮🇹 Itália
                    </a>
                    <a href="paises/espanha.php" class="btn btn-danger country-btn w-100 mb-2">
                        🇪🇸 Espanha
                    </a>
                    <a href="paises/reinounido.php" class="btn btn-dark country-btn w-100 mb-2">
                        🇬🇧 Reino Unido
                    </a>
                </div>
                
                <div class="col-md-4">
                    <h5>🌏 Ásia & Oceania</h5>
                    <a href="paises/australia.php" class="btn btn-warning country-btn w-100 mb-2">
                        🇦🇺 Austrália
                    </a>
                    <a href="paises/japao.php" class="btn btn-light country-btn w-100 mb-2">
                        🇯🇵 Japão
                    </a>
                    <a href="paises/coreia.php" class="btn btn-primary country-btn w-100 mb-2">
                        🇰🇷 Coreia do Sul
                    </a>
                    <a href="paises/china.php" class="btn btn-danger country-btn w-100 mb-2">
                        🇨🇳 China
                    </a>
                    <a href="paises/singapura.php" class="btn btn-success country-btn w-100 mb-2">
                        🇸🇬 Singapura
                    </a>
                </div>
            </div>
        </div>
        
        <?php if (!empty($paises_visitados)): ?>
            <div class="test-card">
                <h3>✅ Países Já Visitados</h3>
                <div class="row">
                    <?php foreach ($paises_visitados as $codigo => $dados): ?>
                        <div class="col-md-6">
                            <div class="visited-country">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <?= $dados['pais_nome'] ?>
                                        </h6>
                                        <small class="text-muted">
                                            Primeira visita: <?= date('d/m/Y H:i', strtotime($dados['data_primeira_visita'])) ?>
                                        </small>
                                        <?php if ($dados['total_visitas'] > 1): ?>
                                            <br><small class="text-muted">
                                                Última visita: <?= date('d/m/Y H:i', strtotime($dados['ultima_visita'])) ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <span class="badge bg-success fs-6"><?= $dados['total_visitas'] ?>x</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="test-card">
            <h3>🔍 Verificar Selos</h3>
            <div class="row">
                <div class="col-md-6">
                    <a href="pesquisa_por_pais.php" class="btn btn-primary w-100 mb-2">
                        📋 Página de Pesquisa de Países
                    </a>
                    <p><small>Veja os selos "Visitado" nos cards dos países</small></p>
                </div>
                <div class="col-md-6">
                    <a href="?" class="btn btn-secondary w-100 mb-2">
                        🔄 Recarregar Teste
                    </a>
                    <p><small>Atualize as estatísticas após visitar países</small></p>
                </div>
            </div>
        </div>
        
        <div class="test-card">
            <h3>📝 Instruções de Teste</h3>
            <div class="alert alert-primary">
                <h6>🧪 Como testar múltiplos países:</h6>
                <ol class="mb-0">
                    <li><strong>Visite vários países:</strong> Clique nos botões acima para visitar diferentes países</li>
                    <li><strong>Observe as notificações:</strong> Veja as mensagens de primeira visita</li>
                    <li><strong>Recarregue o teste:</strong> Clique em "🔄 Recarregar Teste" para ver as estatísticas</li>
                    <li><strong>Verifique os selos:</strong> Vá para "📋 Página de Pesquisa" e veja os selos</li>
                    <li><strong>Teste contadores:</strong> Visite o mesmo país várias vezes</li>
                    <li><strong>Compare estatísticas:</strong> Veja qual país tem mais visitas</li>
                </ol>
            </div>
            
            <div class="alert alert-success">
                <h6>✅ Países com tracking completo:</h6>
                <p class="mb-0">EUA, Canadá, Austrália, Alemanha, França, Itália, Espanha, Reino Unido, Japão, Coreia do Sul, China, Singapura</p>
            </div>
        </div>
    </div>
    
    <script>
        console.log("Teste Múltiplos Países - Sistema Ativo");
        console.log("Países visitados:", <?= json_encode($paises_visitados) ?>);
        console.log("Estatísticas:", <?= json_encode($estatisticas ?? []) ?>);
    </script>
<?php require_once __DIR__ . '/footer.php'; ?>
</body>
</html>
