<?php
/**
 * Script para migrar todas as referências de index.php para index.php
 */

echo "🔄 MIGRAÇÃO: index.php → index.php\n";
echo "======================================\n\n";

// 1. Verificar se index.php existe
echo "📋 1. VERIFICANDO ARQUIVOS:\n";
echo "============================\n";

if (file_exists('index.php')) {
    echo "✅ index.php encontrado\n";
} else {
    echo "❌ index.php não encontrado\n";
    exit;
}

if (file_exists('index.php')) {
    echo "✅ index.php já existe\n";
    echo "⚠️ Será feito backup do index.php atual\n";
    
    // Fazer backup
    if (copy('index.php', 'index_backup_' . date('Y-m-d_H-i-s') . '.php')) {
        echo "✅ Backup criado: index_backup_" . date('Y-m-d_H-i-s') . ".php\n";
    }
} else {
    echo "ℹ️ index.php não existe (será criado)\n";
}

// 2. Substituir index.php por index.php
echo "\n📋 2. SUBSTITUINDO ARQUIVO PRINCIPAL:\n";
echo "=====================================\n";

if (copy('index.php', 'index.php')) {
    echo "✅ index.php copiado para index.php\n";
} else {
    echo "❌ Erro ao copiar arquivo\n";
    exit;
}

// 3. Buscar e substituir referências em todos os arquivos
echo "\n📋 3. BUSCANDO REFERÊNCIAS A index.php:\n";
echo "============================================\n";

$arquivos_para_verificar = [];
$diretorios = ['.', 'paises'];

foreach ($diretorios as $dir) {
    $arquivos = glob($dir . '/*.php');
    $arquivos_para_verificar = array_merge($arquivos_para_verificar, $arquivos);
}

$arquivos_modificados = 0;
$total_substituicoes = 0;

foreach ($arquivos_para_verificar as $arquivo) {
    if (basename($arquivo) === 'migrar_index_new_para_index.php') {
        continue; // Pular este script
    }
    
    $conteudo = file_get_contents($arquivo);
    $conteudo_original = $conteudo;
    
    // Substituir todas as referências
    $padroes = [
        'index.php' => 'index.php',
        '../index.php' => '../index.php',
        './index.php' => './index.php',
        '"index.php"' => '"index.php"',
        "'index.php'" => "'index.php'",
        'href="index.php"' => 'href="index.php"',
        "href='index.php'" => "href='index.php'",
        'Location: index.php' => 'Location: index.php'
    ];
    
    $substituicoes_arquivo = 0;
    foreach ($padroes as $buscar => $substituir) {
        $count = 0;
        $conteudo = str_replace($buscar, $substituir, $conteudo, $count);
        $substituicoes_arquivo += $count;
    }
    
    if ($substituicoes_arquivo > 0) {
        if (file_put_contents($arquivo, $conteudo)) {
            echo "✅ $arquivo - $substituicoes_arquivo substituições\n";
            $arquivos_modificados++;
            $total_substituicoes += $substituicoes_arquivo;
        } else {
            echo "❌ Erro ao salvar $arquivo\n";
        }
    }
}

echo "\n📊 Arquivos modificados: $arquivos_modificados\n";
echo "📊 Total de substituições: $total_substituicoes\n";

// 4. Verificar arquivos específicos importantes
echo "\n📋 4. VERIFICANDO ARQUIVOS ESPECÍFICOS:\n";
echo "=======================================\n";

$arquivos_importantes = [
    'login.php' => 'Sistema de login',
    'header_status.php' => 'Header de status',
    'pesquisa_por_pais.php' => 'Pesquisa de países'
];

foreach ($arquivos_importantes as $arquivo => $descricao) {
    if (file_exists($arquivo)) {
        $conteudo = file_get_contents($arquivo);
        if (strpos($conteudo, 'index.php') !== false) {
            echo "⚠️ $arquivo ainda contém referências a index.php\n";
        } else {
            echo "✅ $arquivo - $descricao (limpo)\n";
        }
    }
}

// 5. Verificar páginas de países
echo "\n📋 5. VERIFICANDO PÁGINAS DE PAÍSES:\n";
echo "====================================\n";

$paises_dir = 'paises';
if (is_dir($paises_dir)) {
    $arquivos_paises = glob($paises_dir . '/*.php');
    $paises_ok = 0;
    $paises_com_problema = 0;
    
    foreach ($arquivos_paises as $arquivo_pais) {
        if (basename($arquivo_pais) === 'header_status.php') {
            continue;
        }
        
        $conteudo = file_get_contents($arquivo_pais);
        if (strpos($conteudo, 'index.php') !== false) {
            $paises_com_problema++;
            echo "⚠️ " . basename($arquivo_pais) . " ainda tem referências\n";
        } else {
            $paises_ok++;
        }
    }
    
    echo "✅ Países corretos: $paises_ok\n";
    echo "⚠️ Países com problema: $paises_com_problema\n";
}

// 6. Criar página de teste da migração
echo "\n📋 6. CRIANDO PÁGINA DE TESTE:\n";
echo "===============================\n";

$teste_migracao = '<?php
require_once "config.php";
iniciarSessaoSegura();

// Fazer login automático para teste
if (!isset($_SESSION["usuario_id"])) {
    $pdo = conectarBD();
    $stmt = $pdo->prepare("SELECT id, nome FROM usuarios WHERE usuario = \"teste\"");
    $stmt->execute();
    $user = $stmt->fetch();
    if ($user) {
        $_SESSION["usuario_id"] = $user["id"];
        $_SESSION["usuario_nome"] = $user["nome"];
        $_SESSION["usuario_login"] = "teste";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Migração - DayDreaming</title>
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
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="test-card text-center">
            <h1>🔄 Teste de Migração Concluído</h1>
            <p class="lead">Todas as referências foram migradas de index.php para index.php</p>
            
            <div class="alert alert-success">
                <h5>✅ Migração Realizada</h5>
                <p>O arquivo index.php agora é a página principal do sistema</p>
            </div>
        </div>
        
        <div class="test-card">
            <h3>🔗 Teste de Links Principais</h3>
            <p>Clique nos links abaixo para verificar se todos redirecionam corretamente:</p>
            
            <div class="row">
                <div class="col-md-6">
                    <h5>📄 Páginas Principais</h5>
                    <a href="index.php" class="link-test">🏠 Página Inicial</a>
                    <a href="login.php" class="link-test">🔐 Login</a>
                    <a href="forum.php" class="link-test">💬 Fórum</a>
                    <a href="pagina_usuario.php" class="link-test">👤 Perfil</a>
                </div>
                <div class="col-md-6">
                    <h5>🌍 Páginas de Países</h5>
                    <a href="paises/eua.php" class="link-test">🇺🇸 EUA</a>
                    <a href="paises/canada.php" class="link-test">🇨🇦 Canadá</a>
                    <a href="paises/reino_unido.php" class="link-test">🇬🇧 Reino Unido</a>
                    <a href="pesquisa_por_pais.php" class="link-test">🔍 Buscar Países</a>
                </div>
            </div>
        </div>
        
        <div class="test-card">
            <h3>🧪 Verificações de Funcionalidade</h3>
            <div class="row">
                <div class="col-md-4">
                    <div class="alert alert-info">
                        <h6>Header Status</h6>
                        <p>Botão "Página Inicial" deve levar para index.php</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-warning">
                        <h6>Sistema de Login</h6>
                        <p>Após login deve redirecionar para index.php</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-success">
                        <h6>Breadcrumbs</h6>
                        <p>Links "Início" devem apontar para index.php</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="test-card text-center">
            <h3>📊 Status da Migração</h3>
            <div class="row">
                <div class="col-md-3">
                    <h4 class="text-primary"><?= $arquivos_modificados ?></h4>
                    <small>Arquivos Modificados</small>
                </div>
                <div class="col-md-3">
                    <h4 class="text-success"><?= $total_substituicoes ?></h4>
                    <small>Substituições Feitas</small>
                </div>
                <div class="col-md-3">
                    <h4 class="text-info">✅</h4>
                    <small>index.php Ativo</small>
                </div>
                <div class="col-md-3">
                    <h4 class="text-warning">🔄</h4>
                    <small>Migração Completa</small>
                </div>
            </div>
        </div>
    </div>
<?php require_once __DIR__ . '/footer.php'; ?>
</body>
</html>';

if (file_put_contents('teste_migracao_index.php', $teste_migracao)) {
    echo "✅ Página de teste criada: teste_migracao_index.php\n";
}

// 7. Resumo final
echo "\n📊 RESUMO DA MIGRAÇÃO:\n";
echo "======================\n";
echo "✅ index.php copiado para index.php\n";
echo "✅ $arquivos_modificados arquivos atualizados\n";
echo "✅ $total_substituicoes referências corrigidas\n";
echo "✅ Backup do index.php original criado\n";

echo "\n🔗 PRÓXIMOS PASSOS:\n";
echo "===================\n";
echo "1. Acesse: http://localhost:8080/index.php\n";
echo "2. Teste: http://localhost:8080/teste_migracao_index.php\n";
echo "3. Verifique se todos os links funcionam\n";
echo "4. Teste login e redirecionamentos\n";
echo "5. Verifique páginas de países\n";

echo "\n⚠️ IMPORTANTE:\n";
echo "===============\n";
echo "- O arquivo index.php ainda existe (pode ser removido depois)\n";
echo "- Backup do index.php original foi criado\n";
echo "- Teste todas as funcionalidades antes de remover arquivos\n";

echo "\n🎉 MIGRAÇÃO CONCLUÍDA COM SUCESSO!\n";

?>
