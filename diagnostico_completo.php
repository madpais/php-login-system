<?php
/**
 * Diagnóstico Completo do Sistema DayDreamming
 * Verifica todas as funcionalidades principais para novos colaboradores
 */

require_once 'config.php';
iniciarSessaoSegura();

echo "🔍 DIAGNÓSTICO COMPLETO DO SISTEMA DAYDREAMMING\n";
echo "===============================================\n\n";

$problemas_encontrados = [];
$avisos = [];
$sucessos = [];

// 1. VERIFICAÇÃO DE CONEXÃO E BANCO DE DADOS
echo "📋 1. CONEXÃO E BANCO DE DADOS:\n";
echo "===============================\n";

try {
    $pdo = conectarBD();
    echo "✅ Conexão com banco: OK\n";
    echo "   Database: " . DB_NAME . "\n";
    echo "   Host: " . DB_HOST . "\n";
    $sucessos[] = "Conexão com banco de dados";
} catch (Exception $e) {
    echo "❌ Erro de conexão: " . $e->getMessage() . "\n";
    $problemas_encontrados[] = "Conexão com banco de dados: " . $e->getMessage();
    exit(1);
}

// 2. VERIFICAÇÃO DE TABELAS PRINCIPAIS
echo "\n📋 2. TABELAS PRINCIPAIS:\n";
echo "========================\n";

$tabelas_essenciais = [
    'usuarios' => 'Sistema de usuários',
    'perfil_usuario' => 'Perfis de usuários',
    'badges' => 'Sistema de badges',
    'usuario_badges' => 'Badges dos usuários',
    'usuario_gpa' => 'GPAs calculados',
    'paises' => 'Sistema de países',
    'usuario_paises' => 'Países visitados',
    'questoes' => 'Sistema de questões',
    'simulados' => 'Sistema de simulados',
    'forum_topicos' => 'Fórum - tópicos',
    'forum_posts' => 'Fórum - posts',
    'notificacoes' => 'Sistema de notificações'
];

foreach ($tabelas_essenciais as $tabela => $descricao) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE '$tabela'");
        if ($stmt->rowCount() > 0) {
            // Contar registros
            $stmt = $pdo->query("SELECT COUNT(*) FROM $tabela");
            $count = $stmt->fetchColumn();
            echo "✅ $tabela: Existe ($count registros) - $descricao\n";
            $sucessos[] = "Tabela $tabela";
        } else {
            echo "❌ $tabela: NÃO EXISTE - $descricao\n";
            $problemas_encontrados[] = "Tabela $tabela não existe";
        }
    } catch (Exception $e) {
        echo "❌ $tabela: ERRO - " . $e->getMessage() . "\n";
        $problemas_encontrados[] = "Erro na tabela $tabela: " . $e->getMessage();
    }
}

// 3. VERIFICAÇÃO DE ARQUIVOS ESSENCIAIS
echo "\n📋 3. ARQUIVOS ESSENCIAIS:\n";
echo "=========================\n";

$arquivos_essenciais = [
    'config.php' => 'Configurações principais',
    'sistema_badges.php' => 'Sistema de badges',
    'badges_manager.php' => 'Gerenciador de badges',
    'salvar_gpa.php' => 'API de salvamento de GPA',
    'calculadora.php' => 'Calculadora de GPA',
    'index.php' => 'Página inicial',
    'login.php' => 'Sistema de login',
    'register.php' => 'Sistema de registro',
    'perfil.php' => 'Página de perfil',
    'forum.php' => 'Sistema de fórum',
    'paises.php' => 'Sistema de países',
    'questoes.php' => 'Sistema de questões',
    'simulador.php' => 'Sistema de simulados',
    'rank.php' => 'Sistema de ranking'
];

foreach ($arquivos_essenciais as $arquivo => $descricao) {
    if (file_exists($arquivo)) {
        $tamanho = filesize($arquivo);
        echo "✅ $arquivo: Existe (" . number_format($tamanho) . " bytes) - $descricao\n";
        
        // Verificar sintaxe PHP
        if (pathinfo($arquivo, PATHINFO_EXTENSION) === 'php') {
            $output = [];
            $return_var = 0;
            exec("php -l $arquivo 2>&1", $output, $return_var);
            if ($return_var !== 0) {
                echo "   ❌ Erro de sintaxe: " . implode(' ', $output) . "\n";
                $problemas_encontrados[] = "Erro de sintaxe em $arquivo";
            }
        }
        $sucessos[] = "Arquivo $arquivo";
    } else {
        echo "❌ $arquivo: NÃO EXISTE - $descricao\n";
        $problemas_encontrados[] = "Arquivo $arquivo não existe";
    }
}

// 4. VERIFICAÇÃO DE FUNCIONALIDADES
echo "\n📋 4. FUNCIONALIDADES PRINCIPAIS:\n";
echo "=================================\n";

// Sistema de Badges
if (class_exists('BadgesManager')) {
    echo "✅ Classe BadgesManager: Disponível\n";
    $sucessos[] = "Sistema de badges (classe)";
} else {
    echo "❌ Classe BadgesManager: NÃO DISPONÍVEL\n";
    $problemas_encontrados[] = "Classe BadgesManager não disponível";
}

if (function_exists('verificarBadgesProvas')) {
    echo "✅ Função verificarBadgesProvas: Disponível\n";
    $sucessos[] = "Sistema de badges (função)";
} else {
    echo "❌ Função verificarBadgesProvas: NÃO DISPONÍVEL\n";
    $problemas_encontrados[] = "Função verificarBadgesProvas não disponível";
}

if (function_exists('salvarGPA')) {
    echo "✅ Função salvarGPA: Disponível\n";
    $sucessos[] = "Sistema de GPA";
} else {
    echo "❌ Função salvarGPA: NÃO DISPONÍVEL\n";
    $problemas_encontrados[] = "Função salvarGPA não disponível";
}

// 5. VERIFICAÇÃO DE CONFIGURAÇÕES
echo "\n📋 5. CONFIGURAÇÕES DO SISTEMA:\n";
echo "===============================\n";

// Debug mode
if (defined('DEBUG_MODE') && DEBUG_MODE) {
    echo "⚠️ DEBUG_MODE: ATIVO (desative em produção)\n";
    $avisos[] = "DEBUG_MODE ativo";
} else {
    echo "✅ DEBUG_MODE: Desativado\n";
    $sucessos[] = "DEBUG_MODE configurado";
}

// Configurações de sessão
if (function_exists('iniciarSessaoSegura')) {
    echo "✅ Sistema de sessão segura: Disponível\n";
    $sucessos[] = "Sistema de sessão";
} else {
    echo "❌ Sistema de sessão segura: NÃO DISPONÍVEL\n";
    $problemas_encontrados[] = "Sistema de sessão não disponível";
}

// Rate limiting
if (defined('RATE_LIMIT_ENABLED')) {
    echo "✅ Rate limiting: Configurado\n";
    $sucessos[] = "Rate limiting";
} else {
    echo "⚠️ Rate limiting: Não configurado\n";
    $avisos[] = "Rate limiting não configurado";
}

// 6. VERIFICAÇÃO DE PERMISSÕES
echo "\n📋 6. PERMISSÕES DE ARQUIVOS:\n";
echo "=============================\n";

$diretorios_escrita = ['uploads', 'logs', 'cache'];

foreach ($diretorios_escrita as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "✅ $dir/: Permissão de escrita OK\n";
            $sucessos[] = "Permissão de escrita em $dir";
        } else {
            echo "❌ $dir/: SEM permissão de escrita\n";
            $problemas_encontrados[] = "Sem permissão de escrita em $dir";
        }
    } else {
        echo "⚠️ $dir/: Diretório não existe\n";
        $avisos[] = "Diretório $dir não existe";
    }
}

// 7. VERIFICAÇÃO DE EXTENSÕES PHP
echo "\n📋 7. EXTENSÕES PHP:\n";
echo "===================\n";

$extensoes_necessarias = [
    'pdo' => 'Conexão com banco de dados',
    'pdo_mysql' => 'Driver MySQL',
    'mbstring' => 'Manipulação de strings',
    'json' => 'Manipulação de JSON',
    'session' => 'Sistema de sessões',
    'curl' => 'Requisições HTTP',
    'gd' => 'Manipulação de imagens',
    'zip' => 'Compressão de arquivos'
];

foreach ($extensoes_necessarias as $ext => $descricao) {
    if (extension_loaded($ext)) {
        echo "✅ $ext: Disponível - $descricao\n";
        $sucessos[] = "Extensão $ext";
    } else {
        echo "❌ $ext: NÃO DISPONÍVEL - $descricao\n";
        $problemas_encontrados[] = "Extensão $ext não disponível";
    }
}

// 8. TESTE DE FUNCIONALIDADES ESPECÍFICAS
echo "\n📋 8. TESTES FUNCIONAIS:\n";
echo "========================\n";

// Teste de hash de senha
try {
    $hash_teste = password_hash('teste123', PASSWORD_DEFAULT);
    if (password_verify('teste123', $hash_teste)) {
        echo "✅ Sistema de hash de senhas: OK\n";
        $sucessos[] = "Sistema de hash de senhas";
    } else {
        echo "❌ Sistema de hash de senhas: FALHOU\n";
        $problemas_encontrados[] = "Sistema de hash de senhas não funciona";
    }
} catch (Exception $e) {
    echo "❌ Sistema de hash de senhas: ERRO - " . $e->getMessage() . "\n";
    $problemas_encontrados[] = "Erro no sistema de hash: " . $e->getMessage();
}

// Teste de JSON
try {
    $json_teste = json_encode(['teste' => 'valor']);
    $array_teste = json_decode($json_teste, true);
    if ($array_teste['teste'] === 'valor') {
        echo "✅ Manipulação de JSON: OK\n";
        $sucessos[] = "Manipulação de JSON";
    } else {
        echo "❌ Manipulação de JSON: FALHOU\n";
        $problemas_encontrados[] = "Manipulação de JSON não funciona";
    }
} catch (Exception $e) {
    echo "❌ Manipulação de JSON: ERRO - " . $e->getMessage() . "\n";
    $problemas_encontrados[] = "Erro na manipulação de JSON: " . $e->getMessage();
}

// 9. RESUMO FINAL
echo "\n📋 9. RESUMO DO DIAGNÓSTICO:\n";
echo "============================\n";

echo "\n✅ SUCESSOS (" . count($sucessos) . "):\n";
foreach ($sucessos as $sucesso) {
    echo "   - $sucesso\n";
}

if (count($avisos) > 0) {
    echo "\n⚠️ AVISOS (" . count($avisos) . "):\n";
    foreach ($avisos as $aviso) {
        echo "   - $aviso\n";
    }
}

if (count($problemas_encontrados) > 0) {
    echo "\n❌ PROBLEMAS ENCONTRADOS (" . count($problemas_encontrados) . "):\n";
    foreach ($problemas_encontrados as $problema) {
        echo "   - $problema\n";
    }
}

// 10. RECOMENDAÇÕES
echo "\n📋 10. RECOMENDAÇÕES PARA NOVOS COLABORADORES:\n";
echo "==============================================\n";

if (count($problemas_encontrados) > 0) {
    echo "\n🔧 AÇÕES NECESSÁRIAS:\n";
    echo "1. Execute: php instalar_completo.php\n";
    echo "2. Verifique as configurações em config.php\n";
    echo "3. Execute os scripts de criação de tabelas se necessário\n";
    echo "4. Verifique as permissões de diretórios\n";
    echo "5. Instale as extensões PHP em falta\n";
} else {
    echo "\n🎉 SISTEMA PRONTO PARA USO!\n";
    echo "Todas as funcionalidades principais estão operacionais.\n";
}

echo "\n📚 SCRIPTS ÚTEIS:\n";
echo "- php diagnostico_completo.php (este script)\n";
echo "- php diagnostico_badges.php (diagnóstico de badges)\n";
echo "- php diagnostico_gpa.php (diagnóstico de GPA)\n";
echo "- php instalar_completo.php (instalação completa)\n";
echo "- php criar_tabelas.php (criar tabelas)\n";
echo "- php inserir_badges.php (inserir badges)\n";

echo "\n✅ Diagnóstico completo concluído!\n";

// Calcular score de saúde do sistema
$total_verificacoes = count($sucessos) + count($avisos) + count($problemas_encontrados);
$score_saude = $total_verificacoes > 0 ? round((count($sucessos) / $total_verificacoes) * 100) : 0;

echo "\n📊 SCORE DE SAÚDE DO SISTEMA: $score_saude%\n";

if ($score_saude >= 90) {
    echo "🟢 Sistema em excelente estado\n";
} elseif ($score_saude >= 70) {
    echo "🟡 Sistema em bom estado, alguns ajustes recomendados\n";
} elseif ($score_saude >= 50) {
    echo "🟠 Sistema funcional, mas precisa de atenção\n";
} else {
    echo "🔴 Sistema precisa de correções urgentes\n";
}

?>