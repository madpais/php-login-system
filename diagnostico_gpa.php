<?php
/**
 * Diagnóstico do Sistema de GPA
 * Verifica por que o GPA não está gravando no perfil
 */

require_once 'config.php';
iniciarSessaoSegura();

echo "📊 DIAGNÓSTICO DO SISTEMA DE GPA\n";
echo "================================\n\n";

// 1. Verificar conexão com banco de dados
echo "📋 1. VERIFICAÇÃO DE CONEXÃO:\n";
echo "=============================\n";

try {
    $pdo = conectarBD();
    echo "✅ Conexão com banco de dados: OK\n";
    echo "Database: " . DB_NAME . "\n";
    echo "Host: " . DB_HOST . "\n";
} catch (Exception $e) {
    echo "❌ Erro de conexão: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. Verificar se a tabela usuario_gpa existe
echo "\n📋 2. VERIFICAÇÃO DA TABELA GPA:\n";
echo "===============================\n";

try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'usuario_gpa'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabela 'usuario_gpa': Existe\n";
        
        // Verificar estrutura da tabela
        $stmt = $pdo->query("DESCRIBE usuario_gpa");
        $colunas = $stmt->fetchAll();
        echo "\n📝 Estrutura da tabela:\n";
        foreach ($colunas as $coluna) {
            echo "   - {$coluna['Field']}: {$coluna['Type']} ({$coluna['Null']})\n";
        }
        
        // Contar registros
        $stmt = $pdo->query("SELECT COUNT(*) FROM usuario_gpa");
        $count = $stmt->fetchColumn();
        echo "\n📊 Registros na tabela: $count\n";
        
        if ($count > 0) {
            // Mostrar alguns exemplos
            $stmt = $pdo->query("SELECT usuario_id, gpa_calculado, data_calculo FROM usuario_gpa ORDER BY data_calculo DESC LIMIT 3");
            $registros = $stmt->fetchAll();
            echo "\n📋 Últimos registros:\n";
            foreach ($registros as $registro) {
                echo "   - Usuário {$registro['usuario_id']}: GPA {$registro['gpa_calculado']} em {$registro['data_calculo']}\n";
            }
        }
    } else {
        echo "❌ Tabela 'usuario_gpa': NÃO EXISTE\n";
        echo "\n🔧 SOLUÇÃO: Execute o script de criação:\n";
        echo "   php criar_tabela_gpa.php\n";
        echo "   OU\n";
        echo "   php setup_database.php\n";
    }
} catch (Exception $e) {
    echo "❌ Erro ao verificar tabela: " . $e->getMessage() . "\n";
}

// 3. Verificar arquivos relacionados ao GPA
echo "\n📋 3. VERIFICAÇÃO DE ARQUIVOS:\n";
echo "==============================\n";

$arquivos_gpa = [
    'salvar_gpa.php' => 'API para salvar GPA',
    'calculadora.php' => 'Calculadora de GPA',
    'criar_tabela_gpa.php' => 'Script de criação da tabela',
    'sistema_badges.php' => 'Sistema de badges (inclui funções GPA)'
];

foreach ($arquivos_gpa as $arquivo => $descricao) {
    if (file_exists($arquivo)) {
        echo "✅ $arquivo: Existe ($descricao)\n";
        
        // Verificar se o arquivo tem conteúdo
        $tamanho = filesize($arquivo);
        echo "   Tamanho: " . number_format($tamanho) . " bytes\n";
        
        // Verificar sintaxe
        $output = [];
        $return_var = 0;
        exec("php -l $arquivo 2>&1", $output, $return_var);
        if ($return_var === 0) {
            echo "   Sintaxe: OK\n";
        } else {
            echo "   ❌ Erro de sintaxe: " . implode(' ', $output) . "\n";
        }
    } else {
        echo "❌ $arquivo: NÃO EXISTE ($descricao)\n";
    }
}

// 4. Testar funções do sistema de GPA
echo "\n📋 4. TESTE DE FUNCIONALIDADES:\n";
echo "===============================\n";

// Verificar se as funções existem
if (function_exists('salvarGPA')) {
    echo "✅ Função salvarGPA: Disponível\n";
} else {
    echo "❌ Função salvarGPA: NÃO DISPONÍVEL\n";
    echo "   Verifique se sistema_badges.php está sendo incluído\n";
}

if (function_exists('verificarBadgesGPA')) {
    echo "✅ Função verificarBadgesGPA: Disponível\n";
} else {
    echo "❌ Função verificarBadgesGPA: NÃO DISPONÍVEL\n";
    echo "   Verifique se sistema_badges.php está sendo incluído\n";
}

// 5. Testar salvamento de GPA (se usuário logado)
echo "\n📋 5. TESTE DE SALVAMENTO:\n";
echo "==========================\n";

if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    echo "✅ Usuário logado: ID $usuario_id\n";
    
    if (function_exists('salvarGPA')) {
        echo "\n🧪 Testando salvamento de GPA...\n";
        
        // Dados de teste
        $gpa_teste = 3.50;
        $notas_teste = [8.5, 9.0, 7.5, 8.0];
        
        try {
            $resultado = salvarGPA($usuario_id, $gpa_teste, $notas_teste);
            if ($resultado) {
                echo "✅ Teste de salvamento: SUCESSO\n";
                
                // Verificar se foi salvo
                $stmt = $pdo->prepare("SELECT * FROM usuario_gpa WHERE usuario_id = ? ORDER BY data_calculo DESC LIMIT 1");
                $stmt->execute([$usuario_id]);
                $ultimo_gpa = $stmt->fetch();
                
                if ($ultimo_gpa) {
                    echo "✅ GPA salvo no banco: {$ultimo_gpa['gpa_calculado']}\n";
                    echo "   Data: {$ultimo_gpa['data_calculo']}\n";
                    echo "   Notas: {$ultimo_gpa['notas_utilizadas']}\n";
                } else {
                    echo "❌ GPA não encontrado no banco após salvamento\n";
                }
            } else {
                echo "❌ Teste de salvamento: FALHOU\n";
            }
        } catch (Exception $e) {
            echo "❌ Erro no teste: " . $e->getMessage() . "\n";
        }
    } else {
        echo "❌ Não é possível testar - função salvarGPA não disponível\n";
    }
} else {
    echo "⚠️ Usuário não logado - não é possível testar salvamento\n";
    echo "   Para testar completamente, faça login primeiro\n";
}

// 6. Verificar integração com perfil
echo "\n📋 6. INTEGRAÇÃO COM PERFIL:\n";
echo "============================\n";

// Verificar se existe tabela perfil_usuario
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'perfil_usuario'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabela 'perfil_usuario': Existe\n";
        
        // Verificar se tem campo GPA
        $stmt = $pdo->query("DESCRIBE perfil_usuario");
        $colunas = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (in_array('gpa', $colunas)) {
            echo "✅ Campo 'gpa' no perfil: Existe\n";
            
            // Verificar se há GPAs no perfil
            $stmt = $pdo->query("SELECT COUNT(*) FROM perfil_usuario WHERE gpa IS NOT NULL");
            $count_gpa_perfil = $stmt->fetchColumn();
            echo "   Perfis com GPA: $count_gpa_perfil\n";
        } else {
            echo "❌ Campo 'gpa' no perfil: NÃO EXISTE\n";
        }
    } else {
        echo "❌ Tabela 'perfil_usuario': NÃO EXISTE\n";
        echo "   O GPA pode não estar sendo exibido no perfil\n";
    }
} catch (Exception $e) {
    echo "❌ Erro ao verificar perfil: " . $e->getMessage() . "\n";
}

// 7. Verificar badges de GPA
echo "\n📋 7. BADGES DE GPA:\n";
echo "===================\n";

try {
    $stmt = $pdo->query("SELECT codigo, nome FROM badges WHERE codigo LIKE 'gpa_%'");
    $badges_gpa = $stmt->fetchAll();
    
    if (count($badges_gpa) > 0) {
        echo "✅ Badges de GPA encontradas: " . count($badges_gpa) . "\n";
        foreach ($badges_gpa as $badge) {
            echo "   - {$badge['codigo']}: {$badge['nome']}\n";
        }
    } else {
        echo "❌ Nenhuma badge de GPA encontrada\n";
        echo "   Execute: php inserir_badges.php\n";
    }
} catch (Exception $e) {
    echo "❌ Erro ao verificar badges: " . $e->getMessage() . "\n";
}

// 8. Resumo e recomendações
echo "\n📋 8. RESUMO E RECOMENDAÇÕES:\n";
echo "==============================\n";

echo "\n🔧 PASSOS PARA CORRIGIR PROBLEMAS:\n";
echo "\n1. Se a tabela usuario_gpa não existe:\n";
echo "   php criar_tabela_gpa.php\n";
echo "\n2. Se as funções não estão disponíveis:\n";
echo "   - Verifique se sistema_badges.php está no config.php\n";
echo "   - As inclusões já foram adicionadas automaticamente\n";
echo "\n3. Se o GPA não aparece no perfil:\n";
echo "   - Verifique se a tabela perfil_usuario existe\n";
echo "   - Execute: php criar_tabela_perfil_usuario.php\n";
echo "\n4. Para instalação completa:\n";
echo "   php instalar_completo.php\n";

echo "\n✅ Diagnóstico de GPA concluído!\n";
?>