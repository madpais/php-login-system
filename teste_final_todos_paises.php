<?php
/**
 * Teste Final - Todos os Países Implementados
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
$estatisticas = [];

if ($usuario_logado) {
    $paises_visitados = obterPaisesVisitados($_SESSION['usuario_id']);
    $estatisticas = obterEstatisticasPaises($_SESSION['usuario_id']);
}

// Lista completa de países
$todos_paises = [
    'eua' => ['nome' => 'Estados Unidos', 'emoji' => '🇺🇸', 'continente' => 'Américas'],
    'canada' => ['nome' => 'Canadá', 'emoji' => '🇨🇦', 'continente' => 'Américas'],
    'australia' => ['nome' => 'Austrália', 'emoji' => '🇦🇺', 'continente' => 'Oceania'],
    'alemanha' => ['nome' => 'Alemanha', 'emoji' => '🇩🇪', 'continente' => 'Europa'],
    'franca' => ['nome' => 'França', 'emoji' => '🇫🇷', 'continente' => 'Europa'],
    'italia' => ['nome' => 'Itália', 'emoji' => '🇮🇹', 'continente' => 'Europa'],
    'espanha' => ['nome' => 'Espanha', 'emoji' => '🇪🇸', 'continente' => 'Europa'],
    'reinounido' => ['nome' => 'Reino Unido', 'emoji' => '🇬🇧', 'continente' => 'Europa'],
    'japao' => ['nome' => 'Japão', 'emoji' => '🇯🇵', 'continente' => 'Ásia'],
    'coreia' => ['nome' => 'Coreia do Sul', 'emoji' => '🇰🇷', 'continente' => 'Ásia'],
    'china' => ['nome' => 'China', 'emoji' => '🇨🇳', 'continente' => 'Ásia'],
    'singapura' => ['nome' => 'Singapura', 'emoji' => '🇸🇬', 'continente' => 'Ásia'],
    'india' => ['nome' => 'Índia', 'emoji' => '🇮🇳', 'continente' => 'Ásia'],
    'belgica' => ['nome' => 'Bélgica', 'emoji' => '🇧🇪', 'continente' => 'Europa'],
    'holanda' => ['nome' => 'Holanda', 'emoji' => '🇳🇱', 'continente' => 'Europa'],
    'suica' => ['nome' => 'Suíça', 'emoji' => '🇨🇭', 'continente' => 'Europa'],
    'suecia' => ['nome' => 'Suécia', 'emoji' => '🇸🇪', 'continente' => 'Europa'],
    'noruega' => ['nome' => 'Noruega', 'emoji' => '🇳🇴', 'continente' => 'Europa'],
    'dinamarca' => ['nome' => 'Dinamarca', 'emoji' => '🇩🇰', 'continente' => 'Europa'],
    'finlandia' => ['nome' => 'Finlândia', 'emoji' => '🇫🇮', 'continente' => 'Europa'],
    'portugal' => ['nome' => 'Portugal', 'emoji' => '🇵🇹', 'continente' => 'Europa'],
    'irlanda' => ['nome' => 'Irlanda', 'emoji' => '🇮🇪', 'continente' => 'Europa'],
    'hungria' => ['nome' => 'Hungria', 'emoji' => '🇭🇺', 'continente' => 'Europa'],
    'indonesia' => ['nome' => 'Indonésia', 'emoji' => '🇮🇩', 'continente' => 'Ásia'],
    'malasia' => ['nome' => 'Malásia', 'emoji' => '🇲🇾', 'continente' => 'Ásia'],
    'arabia' => ['nome' => 'Arábia Saudita', 'emoji' => '🇸🇦', 'continente' => 'Ásia'],
    'emirados' => ['nome' => 'Emirados Árabes Unidos', 'emoji' => '🇦🇪', 'continente' => 'Ásia'],
    'africa' => ['nome' => 'África do Sul', 'emoji' => '🇿🇦', 'continente' => 'África']
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste Final - Todos os Países</title>
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
            margin: 3px;
            padding: 8px 15px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        .country-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        }
        .visited-country {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 12px;
            margin: 8px 0;
            border-radius: 6px;
        }
        .stat-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin: 10px 0;
        }
        .continent-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'header_status.php'; ?>
    
    <div class="container mt-4">
        <div class="test-card text-center">
            <h1>🌍 Teste Final - Sistema Completo de Países Visitados</h1>
            <p class="lead">Teste o sistema com TODOS os 28 países implementados</p>
            
            <div class="alert alert-success">
                <h5>✅ Sistema Implementado</h5>
                <p><strong>Tracking:</strong> 28/28 países (100%)</p>
                <p><strong>Selos:</strong> 7/28 países (25%)</p>
                <p><strong>Usuário:</strong> <?= $_SESSION['usuario_nome'] ?? 'Não logado' ?></p>
            </div>
        </div>
        
        <div class="test-card">
            <h3>📊 Suas Estatísticas</h3>
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
                        <small>País Favorito</small>
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
            <h3>🌍 Todos os Países Disponíveis</h3>
            <p>Clique em qualquer país para visitá-lo (todos têm tracking ativo):</p>
            
            <?php
            $continentes = [];
            foreach ($todos_paises as $codigo => $dados) {
                $continentes[$dados['continente']][] = ['codigo' => $codigo, 'dados' => $dados];
            }
            ?>
            
            <?php foreach ($continentes as $continente => $paises): ?>
                <div class="continent-section">
                    <h5><?= $continente ?></h5>
                    <?php foreach ($paises as $pais): ?>
                        <?php 
                        $codigo = $pais['codigo'];
                        $dados = $pais['dados'];
                        $visitado = isset($paises_visitados[$codigo]);
                        $classe = $visitado ? 'btn-success' : 'btn-outline-primary';
                        $visitas = $visitado ? ' (' . $paises_visitados[$codigo]['total_visitas'] . 'x)' : '';
                        ?>
                        <a href="paises/<?= $codigo ?>.php" class="btn <?= $classe ?> country-btn">
                            <?= $dados['emoji'] ?> <?= $dados['nome'] ?><?= $visitas ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (!empty($paises_visitados)): ?>
            <div class="test-card">
                <h3>✅ Países Que Você Já Visitou</h3>
                <div class="row">
                    <?php foreach ($paises_visitados as $codigo => $dados): ?>
                        <div class="col-md-6">
                            <div class="visited-country">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <?= $todos_paises[$codigo]['emoji'] ?? '🌍' ?> <?= $dados['pais_nome'] ?>
                                        </h6>
                                        <small class="text-muted">
                                            Primeira visita: <?= date('d/m/Y H:i', strtotime($dados['data_primeira_visita'])) ?>
                                        </small>
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
            <h3>🔍 Verificar Sistema</h3>
            <div class="row">
                <div class="col-md-4">
                    <a href="pesquisa_por_pais.php" class="btn btn-primary w-100 mb-2">
                        📋 Página de Pesquisa
                    </a>
                    <p><small>Veja os selos nos países visitados</small></p>
                </div>
                <div class="col-md-4">
                    <a href="?" class="btn btn-secondary w-100 mb-2">
                        🔄 Recarregar Estatísticas
                    </a>
                    <p><small>Atualize após visitar países</small></p>
                </div>
                <div class="col-md-4">
                    <a href="pagina_usuario.php" class="btn btn-info w-100 mb-2">
                        👤 Meu Perfil
                    </a>
                    <p><small>Veja seu perfil completo</small></p>
                </div>
            </div>
        </div>
        
        <div class="test-card">
            <h3>📝 Status da Implementação</h3>
            
            <div class="alert alert-success">
                <h6>✅ Países com Sistema Completo (Tracking + Selo):</h6>
                <p class="mb-0">🇺🇸 EUA, 🇨🇦 Canadá, 🇦🇺 Austrália, 🇩🇪 Alemanha, 🇫🇷 França, 🇮🇹 Itália, 🇯🇵 Japão</p>
            </div>
            
            <div class="alert alert-warning">
                <h6>⚠️ Países com Tracking (Selos podem ser adicionados):</h6>
                <p class="mb-0">🇬🇧 Reino Unido, 🇪🇸 Espanha, 🇰🇷 Coreia do Sul, 🇨🇳 China, 🇸🇬 Singapura, 🇮🇳 Índia, e mais 15 países</p>
            </div>
            
            <div class="alert alert-info">
                <h6>📊 Progresso Atual:</h6>
                <ul class="mb-0">
                    <li><strong>Tracking implementado:</strong> 28/28 países (100%)</li>
                    <li><strong>Selos implementados:</strong> 7/28 países (25%)</li>
                    <li><strong>Funcionalidade:</strong> Sistema totalmente operacional</li>
                    <li><strong>Próximo passo:</strong> Adicionar selos nos países restantes</li>
                </ul>
            </div>
        </div>
    </div>
    
    <script>
        console.log("Sistema de Países Visitados - Implementação Completa");
        console.log("Países visitados:", <?= json_encode($paises_visitados) ?>);
        console.log("Total de países com tracking: 28");
        console.log("Total de países com selos: 7");
    </script>
</body>
</html>
