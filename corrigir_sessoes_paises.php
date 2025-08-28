<?php
/**
 * Script para corrigir o sistema de sessões em todas as páginas de países
 */

echo "🔧 CORRIGINDO SISTEMA DE SESSÕES - PÁGINAS DE PAÍSES\n";
echo "====================================================\n\n";

$paises_dir = 'paises';
$arquivos_corrigidos = 0;
$arquivos_erro = 0;

if (is_dir($paises_dir)) {
    $arquivos = glob($paises_dir . '/*.php');
    
    foreach ($arquivos as $arquivo) {
        $nome_arquivo = basename($arquivo);
        
        // Pular o header_status.php
        if ($nome_arquivo === 'header_status.php') {
            continue;
        }
        
        echo "📄 Processando: $nome_arquivo\n";
        
        try {
            // Ler conteúdo do arquivo
            $conteudo = file_get_contents($arquivo);
            
            if ($conteudo === false) {
                echo "❌ Erro ao ler arquivo: $nome_arquivo\n";
                $arquivos_erro++;
                continue;
            }
            
            // Verificar se precisa de correção
            if (strpos($conteudo, 'session_start();') !== false && strpos($conteudo, 'iniciarSessaoSegura()') === false) {
                
                // Padrão antigo a ser substituído
                $padrao_antigo = "<?php\nsession_start();\n\n// Verificar se o usuário está logado - OBRIGATÓRIO para acessar informações dos países\n\$usuario_logado = isset(\$_SESSION['usuario_id']);";
                
                // Novo padrão
                $padrao_novo = "<?php\nrequire_once '../config.php';\n\n// Iniciar sessão de forma segura\niniciarSessaoSegura();\n\n// Verificar se o usuário está logado - OBRIGATÓRIO para acessar informações dos países\n\$usuario_logado = isset(\$_SESSION['usuario_id']);";
                
                // Substituir
                $conteudo_novo = str_replace($padrao_antigo, $padrao_novo, $conteudo);
                
                // Se não encontrou o padrão exato, tentar padrão mais simples
                if ($conteudo_novo === $conteudo) {
                    $conteudo_novo = preg_replace(
                        '/^<\?php\s*\nsession_start\(\);\s*\n/',
                        "<?php\nrequire_once '../config.php';\n\n// Iniciar sessão de forma segura\niniciarSessaoSegura();\n\n",
                        $conteudo
                    );
                }
                
                // Salvar arquivo corrigido
                if (file_put_contents($arquivo, $conteudo_novo) !== false) {
                    echo "✅ Corrigido: $nome_arquivo\n";
                    $arquivos_corrigidos++;
                } else {
                    echo "❌ Erro ao salvar: $nome_arquivo\n";
                    $arquivos_erro++;
                }
                
            } else {
                echo "ℹ️ Já corrigido ou não precisa: $nome_arquivo\n";
            }
            
        } catch (Exception $e) {
            echo "❌ Erro ao processar $nome_arquivo: " . $e->getMessage() . "\n";
            $arquivos_erro++;
        }
    }
    
    echo "\n📊 RESUMO:\n";
    echo "==========\n";
    echo "Arquivos processados: " . count($arquivos) . "\n";
    echo "Arquivos corrigidos: $arquivos_corrigidos\n";
    echo "Arquivos com erro: $arquivos_erro\n";
    
    if ($arquivos_erro === 0) {
        echo "\n🎉 TODAS AS PÁGINAS DE PAÍSES CORRIGIDAS COM SUCESSO!\n";
    } else {
        echo "\n⚠️ Alguns arquivos tiveram problemas. Verifique manualmente.\n";
    }
    
} else {
    echo "❌ Diretório 'paises' não encontrado\n";
}

// Corrigir também o header_status.php dos países
$header_paises = 'paises/header_status.php';
if (file_exists($header_paises)) {
    echo "\n🔧 Corrigindo header_status.php dos países...\n";
    
    $conteudo_header = file_get_contents($header_paises);
    
    $novo_conteudo_header = "<?php
// Componente de header para mostrar status de login - Versão para pasta paises
require_once '../config.php';

// Iniciar sessão de forma segura
iniciarSessaoSegura();

// Verificar se o usuário está logado
\$usuario_logado = isset(\$_SESSION['usuario_id']);
\$usuario_nome = '';
\$usuario_login = '';

if (\$usuario_logado) {
    \$usuario_nome = \$_SESSION['usuario_nome'] ?? '';
    \$usuario_login = \$_SESSION['usuario_login'] ?? '';
}
?>";
    
    if (file_put_contents($header_paises, $novo_conteudo_header) !== false) {
        echo "✅ Header dos países corrigido\n";
    } else {
        echo "❌ Erro ao corrigir header dos países\n";
    }
}

echo "\n🔗 TESTE RECOMENDADO:\n";
echo "=====================\n";
echo "1. Faça login em: http://localhost:8080/login.php\n";
echo "2. Acesse uma página de país: http://localhost:8080/paises/eua.php\n";
echo "3. Verifique se não pede login novamente\n";
echo "4. Navegue entre diferentes países\n";
echo "5. Volte para páginas principais\n";

?>
