<?php
/**
 * Teste Final da Marcação de Países Visitados
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
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste Final - Marcação de Países</title>
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
        .country-mini {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #007bff;
        }
        .visited {
            border-left-color: #28a745;
            background: #d4edda;
        }
        .btn-test {
            margin: 5px;
            padding: 12px 20px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'header_status.php'; ?>
    
    <div class="container mt-4">
        <div class="test-card text-center">
            <h1>🧪 Teste Final - Marcação de Países Visitados</h1>
            <p class="lead">Verificação completa do sistema de tracking</p>
            
            <div class="alert alert-info">
                <h5>📊 Status Atual</h5>
                <p><strong>Usuário:</strong> <?= $_SESSION['usuario_nome'] ?? 'Não logado' ?></p>
                <p><strong>ID:</strong> <?= $usuario_id ?? 'N/A' ?></p>
                <p><strong>Países visitados:</strong> <?= count($paises_visitados) ?></p>
            </div>
        </div>
        
        <div class="test-card">
            <h3>🎯 Teste de Funcionalidades</h3>
            
            <div class="row">
                <div class="col-md-6">
                    <h5>🌍 Visitar Países</h5>
                    <p>Clique para registrar visitas:</p>
                    
                    <a href="paises/eua.php" class="btn btn-primary btn-test">
                        🇺🇸 Visitar EUA
                    </a>
                    <a href="paises/canada.php" class="btn btn-success btn-test">
                        🇨🇦 Visitar Canadá
                    </a>
                    <a href="paises/australia.php" class="btn btn-warning btn-test">
                        🇦🇺 Visitar Austrália
                    </a>
                </div>
                
                <div class="col-md-6">
                    <h5>🔍 Verificar Selos</h5>
                    <p>Páginas para verificar:</p>
                    
                    <a href="pesquisa_por_pais.php" class="btn btn-info btn-test">
                        📋 Página de Pesquisa
                    </a>
                    <a href="debug_selos_pesquisa.php" class="btn btn-secondary btn-test">
                        🔍 Debug de Selos
                    </a>
                    <a href="?" class="btn btn-outline-primary btn-test">
                        🔄 Recarregar Teste
                    </a>
                </div>
            </div>
        </div>
        
        <div class="test-card">
            <h3>📊 Países Visitados</h3>
            
            <?php if (!empty($paises_visitados)): ?>
                <div class="row">
                    <?php foreach ($paises_visitados as $codigo => $dados): ?>
                        <div class="col-md-6">
                            <div class="country-mini visited">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <?= $dados['pais_nome'] ?>
                                        </h6>
                                        <small class="text-muted">
                                            Primeira visita: <?= date('d/m/Y H:i', strtotime($dados['data_primeira_visita'])) ?>
                                        </small>
                                    </div>
                                    <div>
                                        <span class="badge bg-success"><?= $dados['total_visitas'] ?>x</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <h6><i class="fas fa-info-circle me-2"></i>Nenhum país visitado ainda</h6>
                    <p class="mb-0">Clique nos botões acima para visitar países e ver o sistema funcionando!</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="test-card">
            <h3>📝 Instruções de Teste</h3>
            
            <div class="alert alert-primary">
                <h6>🧪 Como testar o sistema:</h6>
                <ol class="mb-0">
                    <li><strong>Visite um país:</strong> Clique em "🇺🇸 Visitar EUA"</li>
                    <li><strong>Veja a notificação:</strong> Observe a mensagem de primeira visita</li>
                    <li><strong>Volte aqui:</strong> Clique em "🔄 Recarregar Teste"</li>
                    <li><strong>Verifique o registro:</strong> Veja se o país aparece na lista acima</li>
                    <li><strong>Teste os selos:</strong> Vá para "📋 Página de Pesquisa"</li>
                    <li><strong>Confirme o selo:</strong> Veja se aparece "Visitado" no card do país</li>
                    <li><strong>Teste contador:</strong> Visite o mesmo país novamente</li>
                    <li><strong>Veja o contador:</strong> Observe se aparece "2x", "3x", etc.</li>
                </ol>
            </div>
            
            <div class="alert alert-success">
                <h6>✅ O que deve funcionar:</h6>
                <ul class="mb-0">
                    <li>Registro automático de visitas ao acessar páginas de países</li>
                    <li>Selos "Visitado" aparecendo nos cards da página de pesquisa</li>
                    <li>Contador de visitas para países visitados múltiplas vezes</li>
                    <li>Notificações de primeira visita nas páginas dos países</li>
                    <li>Estatísticas atualizadas em tempo real</li>
                </ul>
            </div>
        </div>
        
        <div class="test-card">
            <h3>🔧 Troubleshooting</h3>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="alert alert-warning">
                        <h6>⚠️ Se os selos não aparecerem:</h6>
                        <ul class="mb-0">
                            <li>Limpe o cache do navegador (Ctrl+Shift+Del)</li>
                            <li>Use modo incógnito</li>
                            <li>Recarregue com Ctrl+F5</li>
                            <li>Verifique se está logado</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-info">
                        <h6>🔍 Para debug avançado:</h6>
                        <ul class="mb-0">
                            <li>Abra o console do navegador (F12)</li>
                            <li>Verifique erros JavaScript</li>
                            <li>Use a página debug_selos_pesquisa.php</li>
                            <li>Execute debug_marcacao_paises.php</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        console.log("Teste Final - Sistema de Países Visitados");
        console.log("Usuário logado:", <?= $usuario_logado ? 'true' : 'false' ?>);
        console.log("Países visitados:", <?= json_encode($paises_visitados) ?>);
        
        // Verificar localStorage para debug
        if (localStorage.getItem('debug_paises')) {
            console.log("Debug ativo:", localStorage.getItem('debug_paises'));
        }
        
        // Ativar debug
        localStorage.setItem('debug_paises', 'true');
    </script>
<?php require_once __DIR__ . '/footer.php'; ?>
</body>
</html>
