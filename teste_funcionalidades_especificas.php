<?php
/**
 * Teste específico das funcionalidades que podem ter problemas
 * Fórum, Notificações, Países e Questões
 */

require_once 'config.php';
iniciarSessaoSegura();

echo "🧪 TESTE DE FUNCIONALIDADES ESPECÍFICAS\n";
echo "========================================\n\n";

$problemas = [];
$sucessos = [];

try {
    $pdo = conectarBD();
    
    // 1. TESTE DO SISTEMA DE FÓRUM
    echo "📋 1. SISTEMA DE FÓRUM:\n";
    echo "=======================\n";
    
    // Verificar categorias
    $stmt = $pdo->query("SELECT COUNT(*) FROM forum_categorias");
    $categorias = $stmt->fetchColumn();
    echo "Categorias: $categorias\n";
    
    if ($categorias > 0) {
        echo "✅ Categorias do fórum: OK\n";
        $sucessos[] = "Categorias do fórum";
    } else {
        echo "❌ Nenhuma categoria encontrada\n";
        $problemas[] = "Fórum sem categorias";
    }
    
    // Verificar tópicos
    $stmt = $pdo->query("SELECT COUNT(*) FROM forum_topicos");
    $topicos = $stmt->fetchColumn();
    echo "Tópicos: $topicos\n";
    
    // Verificar respostas
    $stmt = $pdo->query("SELECT COUNT(*) FROM forum_respostas");
    $respostas = $stmt->fetchColumn();
    echo "Respostas: $respostas\n";
    
    if ($topicos > 0 || $respostas > 0) {
        echo "✅ Fórum com conteúdo: OK\n";
        $sucessos[] = "Fórum funcional";
    } else {
        echo "⚠️ Fórum sem conteúdo (normal em instalação nova)\n";
    }
    
    // Testar criação de tópico (simulação)
    echo "\n🧪 Testando estrutura para criação de tópico...\n";
    try {
        $stmt = $pdo->prepare("SELECT id, nome FROM forum_categorias LIMIT 1");
        $stmt->execute();
        $categoria = $stmt->fetch();
        
        if ($categoria) {
            echo "✅ Estrutura para criação de tópicos: OK\n";
            echo "   Categoria de teste: {$categoria['nome']}\n";
            $sucessos[] = "Estrutura do fórum";
        } else {
            echo "❌ Sem categorias para criar tópicos\n";
            $problemas[] = "Fórum sem categorias válidas";
        }
    } catch (Exception $e) {
        echo "❌ Erro na estrutura do fórum: " . $e->getMessage() . "\n";
        $problemas[] = "Erro na estrutura do fórum";
    }
    
    // 2. TESTE DO SISTEMA DE NOTIFICAÇÕES
    echo "\n📋 2. SISTEMA DE NOTIFICAÇÕES:\n";
    echo "==============================\n";
    
    // Verificar tabela de notificações
    $stmt = $pdo->query("SELECT COUNT(*) FROM notificacoes");
    $notificacoes = $stmt->fetchColumn();
    echo "Notificações na tabela principal: $notificacoes\n";
    
    // Verificar tabela de notificações do usuário
    $stmt = $pdo->query("SELECT COUNT(*) FROM notificacoes_usuario");
    $notificacoes_usuario = $stmt->fetchColumn();
    echo "Notificações de usuários: $notificacoes_usuario\n";
    
    // Verificar se o arquivo de sistema de notificações existe
    if (file_exists('sistema_notificacoes.php')) {
        echo "✅ Arquivo sistema_notificacoes.php: Existe\n";
        $sucessos[] = "Sistema de notificações (arquivo)";
    } else {
        echo "❌ Arquivo sistema_notificacoes.php: NÃO EXISTE\n";
        $problemas[] = "Arquivo de notificações não encontrado";
    }
    
    if (file_exists('componente_notificacoes.php')) {
        echo "✅ Componente de notificações: Existe\n";
        $sucessos[] = "Componente de notificações";
    } else {
        echo "❌ Componente de notificações: NÃO EXISTE\n";
        $problemas[] = "Componente de notificações não encontrado";
    }
    
    // 3. TESTE DO SISTEMA DE PAÍSES
    echo "\n📋 3. SISTEMA DE PAÍSES:\n";
    echo "========================\n";
    
    // Verificar países visitados
    $stmt = $pdo->query("SELECT COUNT(*) FROM paises_visitados");
    $paises_visitados = $stmt->fetchColumn();
    echo "Registros de países visitados: $paises_visitados\n";
    
    if ($paises_visitados > 0) {
        echo "✅ Sistema de países: Funcional\n";
        $sucessos[] = "Sistema de países";
        
        // Mostrar alguns países
        $stmt = $pdo->query("SELECT DISTINCT pais_nome FROM paises_visitados LIMIT 5");
        $paises = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "   Países registrados: " . implode(', ', $paises) . "\n";
    } else {
        echo "⚠️ Nenhum país visitado registrado (normal em instalação nova)\n";
    }
    
    // Verificar se há páginas de países
    $paises_disponiveis = [];
    $paises_exemplo = ['brasil', 'eua', 'canada', 'australia', 'reino-unido'];
    
    foreach ($paises_exemplo as $pais) {
        if (file_exists("paises/$pais.php")) {
            $paises_disponiveis[] = $pais;
        }
    }
    
    if (count($paises_disponiveis) > 0) {
        echo "✅ Páginas de países encontradas: " . count($paises_disponiveis) . "\n";
        echo "   Exemplos: " . implode(', ', $paises_disponiveis) . "\n";
        $sucessos[] = "Páginas de países";
    } else {
        echo "❌ Nenhuma página de país encontrada\n";
        $problemas[] = "Páginas de países não encontradas";
    }
    
    // 4. TESTE DO SISTEMA DE QUESTÕES
    echo "\n📋 4. SISTEMA DE QUESTÕES:\n";
    echo "=========================\n";
    
    // Verificar questões
    $stmt = $pdo->query("SELECT COUNT(*) FROM questoes");
    $total_questoes = $stmt->fetchColumn();
    echo "Total de questões: $total_questoes\n";
    
    if ($total_questoes > 0) {
        echo "✅ Banco de questões: OK\n";
        $sucessos[] = "Banco de questões";
        
        // Verificar tipos de prova
        $stmt = $pdo->query("SELECT DISTINCT tipo_prova FROM questoes");
        $tipos_prova = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "   Tipos de prova: " . implode(', ', $tipos_prova) . "\n";
        
        // Verificar sessões de teste
        $stmt = $pdo->query("SELECT COUNT(*) FROM sessoes_teste");
        $sessoes = $stmt->fetchColumn();
        echo "   Sessões de teste: $sessoes\n";
        
        // Verificar resultados
        $stmt = $pdo->query("SELECT COUNT(*) FROM resultados_testes");
        $resultados = $stmt->fetchColumn();
        echo "   Resultados salvos: $resultados\n";
        
        if ($resultados > 0) {
            echo "✅ Sistema de testes: Funcional\n";
            $sucessos[] = "Sistema de testes";
        } else {
            echo "⚠️ Nenhum resultado de teste (normal em instalação nova)\n";
        }
    } else {
        echo "❌ Nenhuma questão encontrada\n";
        $problemas[] = "Banco de questões vazio";
    }
    
    // Verificar arquivo do simulador
    if (file_exists('simulador_provas.php')) {
        echo "✅ Simulador de provas: Existe\n";
        $sucessos[] = "Simulador de provas";
    } else {
        echo "❌ Simulador de provas: NÃO EXISTE\n";
        $problemas[] = "Simulador de provas não encontrado";
    }
    
    // 5. TESTE DE ARQUIVOS ESSENCIAIS
    echo "\n📋 5. ARQUIVOS ESSENCIAIS:\n";
    echo "=========================\n";
    
    $arquivos_essenciais = [
        'forum.php' => 'Página principal do fórum',
        'paises.php' => 'Página de países (pode não existir)',
        'questoes.php' => 'Página de questões (pode não existir)',
        'simulador.php' => 'Simulador (pode ser simulador_provas.php)',
        'simulador_provas.php' => 'Simulador de provas',
        'pagina_usuario.php' => 'Dashboard do usuário',
        'todas_notificacoes.php' => 'Página de notificações'
    ];
    
    foreach ($arquivos_essenciais as $arquivo => $descricao) {
        if (file_exists($arquivo)) {
            echo "✅ $arquivo: Existe - $descricao\n";
            $sucessos[] = "Arquivo $arquivo";
        } else {
            echo "❌ $arquivo: NÃO EXISTE - $descricao\n";
            // Não adicionar como problema se for arquivo opcional
            if (!in_array($arquivo, ['paises.php', 'questoes.php', 'simulador.php'])) {
                $problemas[] = "Arquivo $arquivo não encontrado";
            }
        }
    }
    
    // 6. RESUMO FINAL
    echo "\n📋 6. RESUMO DO TESTE:\n";
    echo "=====================\n";
    
    echo "\n✅ SUCESSOS (" . count($sucessos) . "):\n";
    foreach ($sucessos as $sucesso) {
        echo "   - $sucesso\n";
    }
    
    if (count($problemas) > 0) {
        echo "\n❌ PROBLEMAS ENCONTRADOS (" . count($problemas) . "):\n";
        foreach ($problemas as $problema) {
            echo "   - $problema\n";
        }
        
        echo "\n🔧 RECOMENDAÇÕES:\n";
        echo "1. Execute: php instalar_completo.php\n";
        echo "2. Verifique se todos os arquivos foram copiados corretamente\n";
        echo "3. Execute: php setup_database.php\n";
    } else {
        echo "\n🎉 TODAS AS FUNCIONALIDADES ESTÃO OK!\n";
    }
    
    // Calcular score
    $total_verificacoes = count($sucessos) + count($problemas);
    $score = $total_verificacoes > 0 ? round((count($sucessos) / $total_verificacoes) * 100) : 0;
    
    echo "\n📊 SCORE DE FUNCIONALIDADES: $score%\n";
    
    if ($score >= 90) {
        echo "🟢 Funcionalidades em excelente estado\n";
    } elseif ($score >= 70) {
        echo "🟡 Funcionalidades em bom estado\n";
    } elseif ($score >= 50) {
        echo "🟠 Funcionalidades precisam de atenção\n";
    } else {
        echo "🔴 Funcionalidades precisam de correções urgentes\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro geral: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
}

echo "\n✅ Teste de funcionalidades concluído!\n";
?>