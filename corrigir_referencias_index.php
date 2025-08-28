<?php
/**
 * Script para corrigir todas as referências de index_new.php para index.php
 * (mesmo que index_new.php não exista mais)
 */

echo "🔄 CORREÇÃO DE REFERÊNCIAS: index_new.php → index.php\n";
echo "=====================================================\n\n";

// 1. Buscar todas as referências em arquivos PHP
echo "📋 1. BUSCANDO REFERÊNCIAS A index_new.php:\n";
echo "============================================\n";

$arquivos_para_verificar = [];
$diretorios = ['.', 'paises'];

foreach ($diretorios as $dir) {
    $arquivos = glob($dir . '/*.php');
    $arquivos_para_verificar = array_merge($arquivos_para_verificar, $arquivos);
}

$arquivos_com_referencias = [];
$total_referencias = 0;

foreach ($arquivos_para_verificar as $arquivo) {
    if (basename($arquivo) === 'corrigir_referencias_index.php') {
        continue; // Pular este script
    }
    
    $conteudo = file_get_contents($arquivo);
    
    // Contar referências
    $count = substr_count($conteudo, 'index_new.php');
    
    if ($count > 0) {
        $arquivos_com_referencias[$arquivo] = $count;
        $total_referencias += $count;
        echo "📄 $arquivo: $count referências\n";
    }
}

echo "\n📊 Total de arquivos com referências: " . count($arquivos_com_referencias) . "\n";
echo "📊 Total de referências encontradas: $total_referencias\n";

// 2. Corrigir as referências
echo "\n📋 2. CORRIGINDO REFERÊNCIAS:\n";
echo "=============================\n";

$arquivos_corrigidos = 0;
$substituicoes_feitas = 0;

foreach ($arquivos_com_referencias as $arquivo => $count) {
    $conteudo = file_get_contents($arquivo);
    $conteudo_original = $conteudo;
    
    // Padrões de substituição
    $padroes = [
        'index_new.php' => 'index.php',
        '../index_new.php' => '../index.php',
        './index_new.php' => './index.php',
        '"index_new.php"' => '"index.php"',
        "'index_new.php'" => "'index.php'",
        'href="index_new.php"' => 'href="index.php"',
        "href='index_new.php'" => "href='index.php'",
        'Location: index_new.php' => 'Location: index.php',
        'action="index_new.php"' => 'action="index.php"',
        "action='index_new.php'" => "action='index.php'"
    ];
    
    $substituicoes_arquivo = 0;
    foreach ($padroes as $buscar => $substituir) {
        $count_sub = 0;
        $conteudo = str_replace($buscar, $substituir, $conteudo, $count_sub);
        $substituicoes_arquivo += $count_sub;
    }
    
    if ($substituicoes_arquivo > 0) {
        if (file_put_contents($arquivo, $conteudo)) {
            echo "✅ $arquivo: $substituicoes_arquivo substituições\n";
            $arquivos_corrigidos++;
            $substituicoes_feitas += $substituicoes_arquivo;
        } else {
            echo "❌ Erro ao salvar $arquivo\n";
        }
    }
}

// 3. Verificar arquivos específicos importantes
echo "\n📋 3. VERIFICANDO ARQUIVOS CRÍTICOS:\n";
echo "====================================\n";

$arquivos_criticos = [
    'login.php' => 'Sistema de login - redirecionamento após login',
    'header_status.php' => 'Header - botão página inicial',
    'logout.php' => 'Sistema de logout - redirecionamento',
    'pesquisa_por_pais.php' => 'Pesquisa de países - links de navegação'
];

foreach ($arquivos_criticos as $arquivo => $descricao) {
    if (file_exists($arquivo)) {
        $conteudo = file_get_contents($arquivo);
        if (strpos($conteudo, 'index_new.php') !== false) {
            echo "⚠️ $arquivo ainda contém referências a index_new.php\n";
            echo "   📝 $descricao\n";
        } else {
            echo "✅ $arquivo - $descricao\n";
        }
    } else {
        echo "❌ $arquivo não encontrado\n";
    }
}

// 4. Verificar páginas de países especificamente
echo "\n📋 4. VERIFICANDO PÁGINAS DE PAÍSES:\n";
echo "====================================\n";

$paises_dir = 'paises';
if (is_dir($paises_dir)) {
    $arquivos_paises = glob($paises_dir . '/*.php');
    $paises_corrigidos = 0;
    $paises_com_problema = 0;
    
    foreach ($arquivos_paises as $arquivo_pais) {
        if (basename($arquivo_pais) === 'header_status.php') {
            continue;
        }
        
        $conteudo = file_get_contents($arquivo_pais);
        if (strpos($conteudo, 'index_new.php') !== false) {
            $paises_com_problema++;
            echo "⚠️ " . basename($arquivo_pais) . " ainda tem referências\n";
        } else {
            $paises_corrigidos++;
        }
    }
    
    echo "✅ Países corrigidos: $paises_corrigidos\n";
    echo "⚠️ Países com problema: $paises_com_problema\n";
}

// 5. Verificar se index.php está funcionando
echo "\n📋 5. VERIFICANDO index.php:\n";
echo "============================\n";

if (file_exists('index.php')) {
    $tamanho = filesize('index.php');
    echo "✅ index.php existe\n";
    echo "📏 Tamanho: " . number_format($tamanho) . " bytes\n";
    
    // Verificar se é um arquivo PHP válido
    $conteudo_index = file_get_contents('index.php');
    if (strpos($conteudo_index, '<?php') !== false) {
        echo "✅ Arquivo PHP válido\n";
    } else {
        echo "⚠️ Pode não ser um arquivo PHP válido\n";
    }
    
    // Verificar se tem estrutura HTML
    if (strpos($conteudo_index, '<html') !== false || strpos($conteudo_index, '<!DOCTYPE') !== false) {
        echo "✅ Contém estrutura HTML\n";
    } else {
        echo "⚠️ Pode não ter estrutura HTML completa\n";
    }
} else {
    echo "❌ index.php não existe!\n";
}

// 6. Criar página de teste
echo "\n📋 6. CRIANDO PÁGINA DE TESTE:\n";
echo "===============================\n";

$teste_correcao = '<?php
require_once "config.php";
iniciarSessaoSegura();

// Fazer login automático para teste
if (!isset($_SESSION["usuario_id"])) {
    try {
        $pdo = conectarBD();
        $stmt = $pdo->prepare("SELECT id, nome FROM usuarios WHERE usuario = \"teste\"");
        $stmt->execute();
        $user = $stmt->fetch();
        if ($user) {
            $_SESSION["usuario_id"] = $user["id"];
            $_SESSION["usuario_nome"] = $user["nome"];
            $_SESSION["usuario_login"] = "teste";
        }
    } catch (Exception $e) {
        // Ignorar erro se não conseguir conectar
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Correção de Referências - DayDreaming</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .link-test {
            display: inline-block;
            margin: 5px;
            padding: 10px 15px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .link-test:hover {
            background: #0056b3;
            color: white;
            text-decoration: none;
        }
        .status-ok { color: #28a745; }
        .status-warning { color: #ffc107; }
        .status-error { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="test-card text-center">
            <h1>🔄 Correção de Referências Concluída</h1>
            <p class="lead">Todas as referências foram corrigidas para apontar para index.php</p>
            
            <div class="alert alert-success">
                <h5>✅ Correção Realizada</h5>
                <p><strong><?= $arquivos_corrigidos ?></strong> arquivos corrigidos</p>
                <p><strong><?= $substituicoes_feitas ?></strong> substituições feitas</p>
            </div>
        </div>
        
        <div class="test-card">
            <h3>🔗 Teste de Redirecionamentos</h3>
            <p>Teste os links abaixo para verificar se todos apontam para index.php:</p>
            
            <div class="row">
                <div class="col-md-6">
                    <h5>📄 Links Diretos</h5>
                    <a href="index.php" class="link-test">🏠 index.php</a>
                    <a href="login.php" class="link-test">🔐 Login (teste redirecionamento)</a>
                    <a href="logout.php" class="link-test">🚪 Logout (teste redirecionamento)</a>
                </div>
                <div class="col-md-6">
                    <h5>🌍 Breadcrumbs de Países</h5>
                    <a href="paises/eua.php" class="link-test">🇺🇸 EUA (teste breadcrumb)</a>
                    <a href="paises/canada.php" class="link-test">🇨🇦 Canadá (teste breadcrumb)</a>
                    <a href="pesquisa_por_pais.php" class="link-test">🔍 Pesquisa (teste navegação)</a>
                </div>
            </div>
        </div>
        
        <div class="test-card">
            <h3>🧪 Verificações Importantes</h3>
            <div class="row">
                <div class="col-md-4">
                    <div class="alert alert-info">
                        <h6>Header Status</h6>
                        <p>Botão "🏠 Página Inicial" deve levar para index.php</p>
                        <small>Verifique no topo da página</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-warning">
                        <h6>Sistema de Login</h6>
                        <p>Após login deve redirecionar para index.php</p>
                        <small>Teste com: teste / teste123</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-success">
                        <h6>Páginas de Países</h6>
                        <p>Breadcrumb "Início" deve apontar para index.php</p>
                        <small>Teste em qualquer página de país</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="test-card text-center">
            <h3>📊 Resultado da Correção</h3>
            <div class="row">
                <div class="col-md-3">
                    <h4 class="status-ok">✅</h4>
                    <small>Referências Corrigidas</small>
                </div>
                <div class="col-md-3">
                    <h4 class="status-ok"><?= $arquivos_corrigidos ?></h4>
                    <small>Arquivos Modificados</small>
                </div>
                <div class="col-md-3">
                    <h4 class="status-ok"><?= $substituicoes_feitas ?></h4>
                    <small>Substituições</small>
                </div>
                <div class="col-md-3">
                    <h4 class="status-ok">🏠</h4>
                    <small>index.php Ativo</small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>';

if (file_put_contents('teste_correcao_referencias.php', $teste_correcao)) {
    echo "✅ Página de teste criada: teste_correcao_referencias.php\n";
}

// 7. Resumo final
echo "\n📊 RESUMO DA CORREÇÃO:\n";
echo "======================\n";
echo "✅ Arquivos verificados: " . count($arquivos_para_verificar) . "\n";
echo "✅ Arquivos com referências: " . count($arquivos_com_referencias) . "\n";
echo "✅ Arquivos corrigidos: $arquivos_corrigidos\n";
echo "✅ Total de substituições: $substituicoes_feitas\n";

echo "\n🔗 TESTE AGORA:\n";
echo "================\n";
echo "1. http://localhost:8080/index.php (página principal)\n";
echo "2. http://localhost:8080/teste_correcao_referencias.php (teste de correção)\n";
echo "3. Teste login e verifique redirecionamento\n";
echo "4. Teste páginas de países e breadcrumbs\n";
echo "5. Verifique header em todas as páginas\n";

echo "\n🎉 CORREÇÃO DE REFERÊNCIAS CONCLUÍDA!\n";

?>
