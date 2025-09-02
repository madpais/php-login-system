<?php
/**
 * Teste do Sistema de Países Visitados
 */

require_once 'config.php';
require_once 'tracking_paises.php';

// Iniciar sessão
iniciarSessaoSegura();

// Fazer login automático para teste
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
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste - Sistema de Países Visitados</title>
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
        .country-mini-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #28a745;
        }
        .btn-test {
            margin: 5px;
            padding: 10px 20px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'header_status.php'; ?>
    
    <div class="container mt-4">
        <div class="test-card text-center">
            <h1>🌍 Teste do Sistema de Países Visitados</h1>
            <p class="lead">Teste completo das funcionalidades de tracking de países</p>
            
            <?php if ($usuario_id): ?>
                <div class="alert alert-success">
                    <h5>👤 Usuário Logado: <?= $_SESSION['usuario_nome'] ?></h5>
                    <p>ID: <?= $usuario_id ?></p>
                </div>
            <?php else: ?>
                <div class="alert alert-danger">
                    <h5>❌ Usuário não está logado</h5>
                    <a href="login.php" class="btn btn-primary">Fazer Login</a>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if ($usuario_id): ?>
            <div class="test-card">
                <h3>📊 Estatísticas Atuais</h3>
                <?php
                $estatisticas = obterEstatisticasPaises($usuario_id);
                $paises_visitados = obterPaisesVisitados($usuario_id);
                ?>
                
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-primary"><?= $estatisticas['total_paises'] ?></h4>
                            <small>Países Visitados</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-success"><?= $estatisticas['total_visitas'] ?></h4>
                            <small>Total de Visitas</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-warning">
                                <?= $estatisticas['pais_mais_visitado'] ? $estatisticas['pais_mais_visitado']['pais_nome'] : 'Nenhum' ?>
                            </h4>
                            <small>País Mais Visitado</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-info">
                                <?= $estatisticas['pais_mais_visitado'] ? $estatisticas['pais_mais_visitado']['total_visitas'] : 0 ?>x
                            </h4>
                            <small>Máximo de Visitas</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="test-card">
                <h3>🧪 Testes de Funcionalidade</h3>
                
                <div class="row">
                    <div class="col-md-6">
                        <h5>🔗 Testar Visitas</h5>
                        <p>Clique nos botões para simular visitas aos países:</p>
                        
                        <a href="paises/eua.php" class="btn btn-primary btn-test">
                            🇺🇸 Visitar EUA
                        </a>
                        <a href="paises/canada.php" class="btn btn-success btn-test">
                            🇨🇦 Visitar Canadá
                        </a>
                        <a href="paises/australia.php" class="btn btn-warning btn-test">
                            🇦🇺 Visitar Austrália
                        </a>
                        <a href="paises/alemanha.php" class="btn btn-info btn-test">
                            🇩🇪 Visitar Alemanha
                        </a>
                    </div>
                    
                    <div class="col-md-6">
                        <h5>📋 Verificar Sistema</h5>
                        <p>Links para verificar o sistema:</p>
                        
                        <a href="pesquisa_por_pais.php" class="btn btn-secondary btn-test">
                            🔍 Página de Pesquisa
                        </a>
                        <a href="pagina_usuario.php" class="btn btn-dark btn-test">
                            👤 Meu Perfil
                        </a>
                        <a href="?" class="btn btn-outline-primary btn-test">
                            🔄 Recarregar Teste
                        </a>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($paises_visitados)): ?>
                <div class="test-card">
                    <h3>🌍 Países Já Visitados</h3>
                    <div class="row">
                        <?php foreach ($paises_visitados as $codigo => $dados): ?>
                            <div class="col-md-6">
                                <div class="country-mini-card">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1"><?= $dados['pais_nome'] ?></h6>
                                            <small class="text-muted">
                                                Primeira visita: <?= date('d/m/Y H:i', strtotime($dados['data_primeira_visita'])) ?>
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-success"><?= $dados['total_visitas'] ?>x</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="test-card">
                <h3>📝 Instruções de Teste</h3>
                <ol>
                    <li><strong>Teste de Visita:</strong>
                        <ul>
                            <li>Clique em "🇺🇸 Visitar EUA" para ir à página dos Estados Unidos</li>
                            <li>Verifique se aparece a notificação de primeira visita</li>
                            <li>Volte para esta página e veja se as estatísticas mudaram</li>
                        </ul>
                    </li>
                    <li><strong>Teste de Selo:</strong>
                        <ul>
                            <li>Vá para "🔍 Página de Pesquisa"</li>
                            <li>Verifique se os países visitados têm o selo "Visitado"</li>
                            <li>Visite mais países e veja os selos aparecerem</li>
                        </ul>
                    </li>
                    <li><strong>Teste de Contador:</strong>
                        <ul>
                            <li>Visite o mesmo país várias vezes</li>
                            <li>Verifique se o contador de visitas aumenta</li>
                            <li>Veja se aparece "2x", "3x", etc. no selo</li>
                        </ul>
                    </li>
                </ol>
            </div>
        <?php endif; ?>
        
        <div class="test-card">
            <h3>🔧 Funcionalidades Implementadas</h3>
            <div class="row">
                <div class="col-md-6">
                    <h5>✅ Sistema de Tracking</h5>
                    <ul>
                        <li>Registro automático de visitas</li>
                        <li>Contador de visitas por país</li>
                        <li>Data da primeira e última visita</li>
                        <li>Estatísticas consolidadas</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>✅ Interface Visual</h5>
                    <ul>
                        <li>Selos "Visitado" nos cards</li>
                        <li>Contador de visitas (2x, 3x, etc.)</li>
                        <li>Notificação de primeira visita</li>
                        <li>Estatísticas no perfil do usuário</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh a cada 30 segundos para ver mudanças
        setTimeout(function() {
            console.log("Auto-refresh em 30 segundos...");
        }, 30000);
        
        console.log("Sistema de Países Visitados - Teste Ativo");
        console.log("Usuário:", "<?= $_SESSION['usuario_nome'] ?? 'Não logado' ?>");
        console.log("Total de países visitados:", <?= $estatisticas['total_paises'] ?? 0 ?>);
    </script>
<?php require_once __DIR__ . '/footer.php'; ?>
</body>
</html>
