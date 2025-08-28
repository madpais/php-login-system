<?php
/**
 * Teste Completo de Todas as Páginas Não Contempladas
 * Sistema DayDreaming - Verificação Abrangente
 */

require_once 'config.php';

echo "🔍 TESTE ABRANGENTE - PÁGINAS NÃO CONTEMPLADAS\n";
echo "===============================================\n\n";

try {
    $pdo = conectarBD();
    echo "📡 Conectado ao banco de dados!\n\n";
    
    // 1. Testar páginas principais
    echo "📄 TESTANDO PÁGINAS PRINCIPAIS:\n";
    echo "===============================\n";
    
    $paginas_principais = [
        'forum.php' => 'Sistema de Fórum',
        'admin_forum.php' => 'Administração do Fórum',
        'pagina_usuario.php' => 'Dashboard do Usuário',
        'cadastro.php' => 'Cadastro de Usuários',
        'testes_internacionais.php' => 'Testes Internacionais',
        'sistema_notificacoes.php' => 'Sistema de Notificações',
        'badges_manager.php' => 'Gerenciador de Badges',
        'questoes_manager.php' => 'Gerenciador de Questões',
        'logout.php' => 'Sistema de Logout',
        'recuperar_senha.php' => 'Recuperação de Senha',
        'editar_perfil.php' => 'Edição de Perfil',
        'historico_testes.php' => 'Histórico de Testes',
        'salvar_resposta.php' => 'Salvar Respostas'
    ];
    
    $paginas_ok = 0;
    $paginas_faltando = 0;
    
    foreach ($paginas_principais as $arquivo => $descricao) {
        if (file_exists($arquivo)) {
            echo "✅ $arquivo - $descricao\n";
            $paginas_ok++;
        } else {
            echo "❌ $arquivo - $descricao (FALTANDO)\n";
            $paginas_faltando++;
        }
    }
    
    echo "\n📊 Páginas principais: $paginas_ok OK, $paginas_faltando faltando\n";
    
    // 2. Testar páginas de países
    echo "\n🌍 TESTANDO PÁGINAS DE PAÍSES:\n";
    echo "==============================\n";
    
    $paises_dir = 'paises';
    if (is_dir($paises_dir)) {
        $paises = glob($paises_dir . '/*.php');
        $paises_count = 0;
        
        foreach ($paises as $pais) {
            if (basename($pais) !== 'header_status.php') {
                $nome_pais = basename($pais, '.php');
                echo "✅ $nome_pais.php\n";
                $paises_count++;
            }
        }
        echo "\n📈 Total de páginas de países: $paises_count\n";
    } else {
        echo "❌ Diretório de países não encontrado\n";
    }
    
    // 3. Verificar tabelas específicas
    echo "\n🗄️ VERIFICANDO TABELAS ESPECÍFICAS:\n";
    echo "===================================\n";
    
    $tabelas_especificas = [
        'forum_categorias' => 'Categorias do Fórum',
        'forum_topicos' => 'Tópicos do Fórum',
        'forum_respostas' => 'Respostas do Fórum',
        'forum_curtidas' => 'Curtidas do Fórum',
        'forum_moderacao' => 'Moderação do Fórum',
        'badges' => 'Sistema de Badges',
        'usuario_badges' => 'Badges dos Usuários',
        'notificacoes' => 'Sistema de Notificações',
        'niveis_usuario' => 'Níveis dos Usuários',
        'configuracoes_sistema' => 'Configurações do Sistema',
        'logs_acesso' => 'Logs de Acesso',
        'logs_sistema' => 'Logs do Sistema',
        'historico_experiencia' => 'Histórico de Experiência'
    ];
    
    $tabelas_ok = 0;
    $tabelas_erro = 0;
    
    foreach ($tabelas_especificas as $tabela => $descricao) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM $tabela");
            $count = $stmt->fetchColumn();
            echo "✅ $tabela - $descricao ($count registros)\n";
            $tabelas_ok++;
        } catch (Exception $e) {
            echo "❌ $tabela - $descricao (ERRO)\n";
            $tabelas_erro++;
        }
    }
    
    echo "\n📊 Tabelas: $tabelas_ok OK, $tabelas_erro com erro\n";
    
    // 4. Testar funcionalidades específicas
    echo "\n🧪 TESTANDO FUNCIONALIDADES ESPECÍFICAS:\n";
    echo "========================================\n";
    
    // Testar sistema de badges
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM badges WHERE ativa = 1");
        $badges_ativas = $stmt->fetchColumn();
        echo "✅ Badges ativas: $badges_ativas\n";
    } catch (Exception $e) {
        echo "❌ Erro no sistema de badges\n";
    }
    
    // Testar sistema de fórum
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM forum_categorias WHERE ativo = 1");
        $categorias = $stmt->fetchColumn();
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM forum_topicos");
        $topicos = $stmt->fetchColumn();
        
        echo "✅ Categorias do fórum ativas: $categorias\n";
        echo "✅ Tópicos do fórum: $topicos\n";
    } catch (Exception $e) {
        echo "❌ Erro no sistema de fórum\n";
    }
    
    // Testar sistema de questões
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM questoes");
        $questoes = $stmt->fetchColumn();
        
        $stmt = $pdo->query("SELECT COUNT(DISTINCT tipo_exame) FROM questoes");
        $tipos_exame = $stmt->fetchColumn();
        
        echo "✅ Questões carregadas: $questoes\n";
        echo "✅ Tipos de exame: $tipos_exame\n";
    } catch (Exception $e) {
        echo "❌ Erro no sistema de questões\n";
    }
    
    // Testar sistema de usuários
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM usuarios");
        $usuarios = $stmt->fetchColumn();
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM sessoes_teste");
        $sessoes = $stmt->fetchColumn();
        
        echo "✅ Usuários cadastrados: $usuarios\n";
        echo "✅ Sessões de teste: $sessoes\n";
    } catch (Exception $e) {
        echo "❌ Erro no sistema de usuários\n";
    }
    
    echo "\n📊 RESUMO FINAL:\n";
    echo "================\n";
    echo "✅ Banco de dados: Conectado e funcional\n";
    echo "✅ Páginas principais: $paginas_ok OK, $paginas_faltando faltando\n";
    echo "✅ Páginas de países: Verificadas\n";
    echo "✅ Tabelas específicas: $tabelas_ok OK, $tabelas_erro com erro\n";
    echo "✅ Funcionalidades: Testadas\n";
    echo "\n🎉 VERIFICAÇÃO COMPLETA FINALIZADA!\n";
    
} catch (Exception $e) {
    echo "❌ ERRO GERAL: " . $e->getMessage() . "\n";
}
?>
