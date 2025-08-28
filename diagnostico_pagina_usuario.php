<?php
/**
 * Diagnóstico Completo da Página de Usuário
 * Identifica e corrige problemas nas funcionalidades
 */

require_once 'config.php';
iniciarSessaoSegura();

echo "🔍 DIAGNÓSTICO COMPLETO - PÁGINA DE USUÁRIO\n";
echo "===========================================\n\n";

try {
    $pdo = conectarBD();
    echo "✅ Conectado ao banco de dados\n\n";
    
    // 1. Verificar tabelas necessárias
    echo "📋 1. VERIFICANDO TABELAS NECESSÁRIAS:\n";
    echo "======================================\n";
    
    $tabelas_necessarias = [
        'usuarios' => 'Tabela principal de usuários',
        'perfil_usuario' => 'Perfis detalhados dos usuários',
        'niveis_usuario' => 'Sistema de níveis e experiência',
        'usuario_badges' => 'Badges conquistadas',
        'badges' => 'Definição das badges',
        'historico_atividades' => 'Histórico de atividades do usuário',
        'sessoes_teste' => 'Sessões de testes realizados',
        'resultados_testes' => 'Resultados dos testes'
    ];
    
    $tabelas_existentes = [];
    $tabelas_faltando = [];
    
    foreach ($tabelas_necessarias as $tabela => $descricao) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$tabela'");
        if ($stmt->rowCount() > 0) {
            echo "✅ $tabela - $descricao\n";
            $tabelas_existentes[] = $tabela;
        } else {
            echo "❌ $tabela - $descricao (FALTANDO)\n";
            $tabelas_faltando[] = $tabela;
        }
    }
    
    // 2. Verificar estrutura das tabelas existentes
    echo "\n📋 2. VERIFICANDO ESTRUTURA DAS TABELAS:\n";
    echo "=========================================\n";
    
    // Verificar tabela usuarios
    if (in_array('usuarios', $tabelas_existentes)) {
        $stmt = $pdo->query("DESCRIBE usuarios");
        $colunas_usuarios = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $campos_necessarios = ['id', 'nome', 'usuario', 'email', 'senha', 'is_admin', 'ativo'];
        $campos_faltando = array_diff($campos_necessarios, $colunas_usuarios);
        
        if (empty($campos_faltando)) {
            echo "✅ Tabela usuarios - estrutura OK\n";
        } else {
            echo "⚠️ Tabela usuarios - campos faltando: " . implode(', ', $campos_faltando) . "\n";
        }
    }
    
    // Verificar tabela niveis_usuario
    if (in_array('niveis_usuario', $tabelas_existentes)) {
        $stmt = $pdo->query("DESCRIBE niveis_usuario");
        $colunas_niveis = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $campos_necessarios = ['id', 'usuario_id', 'nivel_atual', 'experiencia_total', 'experiencia_nivel', 'experiencia_necessaria', 'testes_completados', 'melhor_pontuacao', 'media_pontuacao'];
        $campos_faltando = array_diff($campos_necessarios, $colunas_niveis);
        
        if (empty($campos_faltando)) {
            echo "✅ Tabela niveis_usuario - estrutura OK\n";
        } else {
            echo "⚠️ Tabela niveis_usuario - campos faltando: " . implode(', ', $campos_faltando) . "\n";
        }
    }
    
    // 3. Testar queries da página de usuário
    echo "\n📋 3. TESTANDO QUERIES DA PÁGINA DE USUÁRIO:\n";
    echo "=============================================\n";
    
    // Simular login de usuário para teste
    if (!isset($_SESSION['usuario_id'])) {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE usuario = 'teste' LIMIT 1");
        $stmt->execute();
        $user = $stmt->fetch();
        if ($user) {
            $_SESSION['usuario_id'] = $user['id'];
            echo "✅ Usuário teste simulado para testes\n";
        }
    }
    
    $usuario_id = $_SESSION['usuario_id'] ?? 1;
    
    // Teste 1: Query principal de dados do usuário
    echo "\n🔍 Teste 1: Query principal de dados do usuário\n";
    try {
        $stmt = $pdo->prepare("
            SELECT u.*, p.*, n.nivel_atual, n.experiencia_total, n.experiencia_nivel, 
                   n.experiencia_necessaria, n.testes_completados, n.melhor_pontuacao, n.media_pontuacao
            FROM usuarios u
            LEFT JOIN perfil_usuario p ON u.id = p.usuario_id
            LEFT JOIN niveis_usuario n ON u.id = n.usuario_id
            WHERE u.id = ?
        ");
        $stmt->execute([$usuario_id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario) {
            echo "✅ Query principal funcionando\n";
            echo "  - Nome: " . ($usuario['nome'] ?? 'N/A') . "\n";
            echo "  - Nível: " . ($usuario['nivel_atual'] ?? 'N/A') . "\n";
            echo "  - Experiência: " . ($usuario['experiencia_total'] ?? 'N/A') . "\n";
        } else {
            echo "❌ Query principal falhou - usuário não encontrado\n";
        }
    } catch (Exception $e) {
        echo "❌ Query principal falhou: " . $e->getMessage() . "\n";
    }
    
    // Teste 2: Query de badges
    echo "\n🔍 Teste 2: Query de badges conquistadas\n";
    try {
        $stmt = $pdo->prepare("
            SELECT b.nome, b.descricao, b.icone, ub.data_conquista
            FROM usuario_badges ub
            JOIN badges b ON ub.badge_id = b.id
            WHERE ub.usuario_id = ?
            ORDER BY ub.data_conquista DESC
        ");
        $stmt->execute([$usuario_id]);
        $badges = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "✅ Query de badges funcionando - " . count($badges) . " badges encontradas\n";
    } catch (Exception $e) {
        echo "❌ Query de badges falhou: " . $e->getMessage() . "\n";
    }
    
    // Teste 3: Query de histórico de atividades
    echo "\n🔍 Teste 3: Query de histórico de atividades\n";
    try {
        $stmt = $pdo->prepare("
            SELECT tipo_atividade, descricao, pontos_ganhos, data_atividade
            FROM historico_atividades
            WHERE usuario_id = ?
            ORDER BY data_atividade DESC
            LIMIT 10
        ");
        $stmt->execute([$usuario_id]);
        $atividades = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "✅ Query de atividades funcionando - " . count($atividades) . " atividades encontradas\n";
    } catch (Exception $e) {
        echo "❌ Query de atividades falhou: " . $e->getMessage() . "\n";
    }
    
    // Teste 4: Query de país de interesse
    echo "\n🔍 Teste 4: Query de país de interesse\n";
    try {
        $stmt = $pdo->prepare("
            SELECT 
                CASE 
                    WHEN tipo_prova = 'toefl' OR tipo_prova = 'sat' THEN 'Estados Unidos'
                    WHEN tipo_prova = 'ielts' THEN 'Reino Unido'
                    WHEN tipo_prova = 'dele' THEN 'Espanha'
                    WHEN tipo_prova = 'delf' THEN 'França'
                    WHEN tipo_prova = 'testdaf' THEN 'Alemanha'
                    WHEN tipo_prova = 'jlpt' THEN 'Japão'
                    WHEN tipo_prova = 'hsk' THEN 'China'
                    ELSE 'Não definido'
                END as pais,
                COUNT(*) as total_testes
            FROM sessoes_teste
            WHERE usuario_id = ? AND status = 'finalizada'
            GROUP BY tipo_prova
            ORDER BY total_testes DESC
            LIMIT 1
        ");
        $stmt->execute([$usuario_id]);
        $pais_interesse = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "✅ Query de país de interesse funcionando\n";
        if ($pais_interesse) {
            echo "  - País: " . $pais_interesse['pais'] . "\n";
            echo "  - Testes: " . $pais_interesse['total_testes'] . "\n";
        } else {
            echo "  - Nenhum país identificado (usuário não fez testes)\n";
        }
    } catch (Exception $e) {
        echo "❌ Query de país de interesse falhou: " . $e->getMessage() . "\n";
    }
    
    // 4. Verificar dados de exemplo
    echo "\n📋 4. VERIFICANDO DADOS DE EXEMPLO:\n";
    echo "====================================\n";
    
    // Verificar se usuário tem dados de nível
    $stmt = $pdo->prepare("SELECT * FROM niveis_usuario WHERE usuario_id = ?");
    $stmt->execute([$usuario_id]);
    $nivel_data = $stmt->fetch();
    
    if (!$nivel_data) {
        echo "⚠️ Usuário não tem dados de nível - criando...\n";
        try {
            $stmt = $pdo->prepare("
                INSERT INTO niveis_usuario (usuario_id, nivel_atual, experiencia_total, experiencia_nivel, experiencia_necessaria, testes_completados, melhor_pontuacao, media_pontuacao)
                VALUES (?, 1, 0, 0, 100, 0, 0.00, 0.00)
            ");
            $stmt->execute([$usuario_id]);
            echo "✅ Dados de nível criados\n";
        } catch (Exception $e) {
            echo "❌ Erro ao criar dados de nível: " . $e->getMessage() . "\n";
        }
    } else {
        echo "✅ Usuário tem dados de nível\n";
    }
    
    // Verificar se usuário tem perfil
    if (in_array('perfil_usuario', $tabelas_existentes)) {
        $stmt = $pdo->prepare("SELECT * FROM perfil_usuario WHERE usuario_id = ?");
        $stmt->execute([$usuario_id]);
        $perfil_data = $stmt->fetch();
        
        if (!$perfil_data) {
            echo "⚠️ Usuário não tem perfil detalhado - criando...\n";
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO perfil_usuario (usuario_id, avatar_personagem, background_cor)
                    VALUES (?, ?, ?)
                ");
                $avatar_config = json_encode([
                    'cabelo_cor' => '#8B4513',
                    'cabelo_estilo' => 'curto',
                    'pele_cor' => '#FDBCB4',
                    'olhos_cor' => '#654321',
                    'roupa_cor' => '#4CAF50',
                    'roupa_estilo' => 'casual'
                ]);
                $stmt->execute([$usuario_id, $avatar_config, '#4CAF50']);
                echo "✅ Perfil detalhado criado\n";
            } catch (Exception $e) {
                echo "❌ Erro ao criar perfil: " . $e->getMessage() . "\n";
            }
        } else {
            echo "✅ Usuário tem perfil detalhado\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erro geral: " . $e->getMessage() . "\n";
}

// 5. Resumo e soluções
echo "\n📊 RESUMO E SOLUÇÕES:\n";
echo "======================\n";

if (!empty($tabelas_faltando)) {
    echo "⚠️ TABELAS FALTANDO:\n";
    foreach ($tabelas_faltando as $tabela) {
        echo "  - $tabela\n";
    }
    
    echo "\n🔧 SOLUÇÕES:\n";
    if (in_array('perfil_usuario', $tabelas_faltando)) {
        echo "  1. Execute: php criar_tabela_perfil_usuario.php\n";
    }
    if (in_array('historico_atividades', $tabelas_faltando)) {
        echo "  2. Execute: php setup_database.php (para criar historico_atividades)\n";
    }
    echo "  3. Execute: php setup_database.php (para garantir todas as tabelas)\n";
} else {
    echo "✅ Todas as tabelas necessárias existem\n";
}

echo "\n🔗 PRÓXIMOS PASSOS:\n";
echo "===================\n";
echo "1. Execute os scripts de correção sugeridos\n";
echo "2. Acesse: http://localhost:8080/pagina_usuario.php\n";
echo "3. Verifique se os erros foram corrigidos\n";
echo "4. Teste todas as funcionalidades da página\n";

?>
