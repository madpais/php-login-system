<?php
/**
 * Exemplo de como integrar as funções de badges no sistema
 * Este arquivo mostra onde e como chamar as funções de verificação de badges
 */

require_once 'config.php';

echo "📋 EXEMPLO DE INTEGRAÇÃO DAS BADGES\n";
echo "===================================\n\n";

echo "🎯 1. APÓS COMPLETAR UMA PROVA/TESTE:\n";
echo "=====================================\n";
echo "// No arquivo que processa resultados de testes (ex: processar_teste.php)\n";
echo "// Após salvar o resultado no banco:\n\n";

echo "<?php\n";
echo "// Exemplo de código para adicionar após salvar resultado de teste\n";
echo "require_once 'sistema_badges.php';\n";
echo "require_once 'badges_manager.php';\n\n";

echo "// Verificar badges de provas (baseado na porcentagem de acertos)\n";
echo "verificarBadgesProvas(\$usuario_id);\n\n";

echo "// Verificar badges do BadgesManager (mais detalhadas)\n";
echo "\$badges_manager = new BadgesManager();\n";
echo "\$badges_conquistadas = \$badges_manager->verificarBadgesResultado(\$usuario_id, \$pontuacao, \$tipo_prova);\n\n";

echo "// Ou usar a função auxiliar\n";
echo "\$badges = processarResultadoCompleto(\$usuario_id, \$pontuacao, \$tipo_prova, \$tempo_gasto, \$acertos, \$total_questoes);\n";
echo "?>\n\n";

echo "🎯 2. APÓS PARTICIPAR NO FÓRUM:\n";
echo "===============================\n";
echo "// No arquivo que salva tópicos/respostas do fórum\n";
echo "// Após inserir no banco:\n\n";

echo "<?php\n";
echo "require_once 'sistema_badges.php';\n\n";
echo "// Verificar badges de fórum\n";
echo "verificarBadgesForum(\$usuario_id);\n";
echo "?>\n\n";

echo "🎯 3. APÓS CALCULAR GPA:\n";
echo "========================\n";
echo "// No arquivo que calcula/salva GPA\n";
echo "// Após salvar o GPA no banco:\n\n";

echo "<?php\n";
echo "require_once 'sistema_badges.php';\n\n";
echo "// Verificar badges de GPA\n";
echo "verificarBadgesGPA(\$usuario_id);\n";
echo "?>\n\n";

echo "🎯 4. APÓS VISITAR PAÍS:\n";
echo "========================\n";
echo "// No arquivo que registra visita a países\n";
echo "// Após registrar a visita:\n\n";

echo "<?php\n";
echo "require_once 'sistema_badges.php';\n\n";
echo "// Verificar badges de países\n";
echo "verificarBadgesPaises(\$usuario_id);\n";
echo "?>\n\n";

echo "🎯 5. VERIFICAÇÃO GERAL (RECOMENDADO):\n";
echo "======================================\n";
echo "// Para verificar todas as badges de uma vez\n";
echo "// Útil em login ou ações importantes:\n\n";

echo "<?php\n";
echo "require_once 'sistema_badges.php';\n\n";
echo "// Verificar todas as badges\n";
echo "\$badges_conquistadas = verificarTodasBadges(\$usuario_id);\n\n";
echo "// \$badges_conquistadas será um array com os tipos de badges conquistadas\n";
echo "// Ex: ['provas', 'forum', 'gpa'] se o usuário conquistou badges desses tipos\n";
echo "?>\n\n";

echo "🎯 6. EXIBIR BADGES DO USUÁRIO:\n";
echo "===============================\n";
echo "// Para mostrar as badges na página do usuário:\n\n";

echo "<?php\n";
echo "require_once 'badges_manager.php';\n\n";
echo "// Obter badges do usuário\n";
echo "\$badges_usuario = getBadgesUsuario(\$usuario_id);\n\n";
echo "// Exibir badges\n";
echo "foreach (\$badges_usuario as \$badge) {\n";
echo "    echo \"<div class='badge'>\";\n";
echo "    echo \"<img src='imagens/{\$badge['imagem']}' alt='{\$badge['nome']}'>\";\n";
echo "    echo \"<h4>{\$badge['nome']}</h4>\";\n";
echo "    echo \"<p>{\$badge['descricao']}</p>\";\n";
echo "    echo \"<small>Conquistada em: {\$badge['data_conquista']}</small>\";\n";
echo "    echo \"</div>\";\n";
echo "}\n";
echo "?>\n\n";

echo "🎯 7. EXEMPLO PRÁTICO - ARQUIVO processar_teste.php:\n";
echo "====================================================\n";

// Demonstrar com um exemplo real
echo "Vou criar dados de exemplo para demonstrar:\n\n";

try {
    $pdo = conectarBD();
    
    // Buscar um usuário
    $stmt = $pdo->query("SELECT id, nome FROM usuarios LIMIT 1");
    $usuario = $stmt->fetch();
    
    if ($usuario) {
        $usuario_id = $usuario['id'];
        echo "👤 Testando com usuário: {$usuario['nome']} (ID: $usuario_id)\n\n";
        
        // Simular inserção de resultado de teste
        echo "📝 Simulando resultado de teste...\n";
        
        // Inserir um resultado fictício
        $stmt = $pdo->prepare("
            INSERT INTO resultados_testes (usuario_id, tipo_prova, pontuacao, acertos, total_questoes, tempo_gasto, data_realizacao, erros, nao_respondidas, questoes_respondidas)
            VALUES (?, 'sat', 85, 17, 20, 1800, NOW(), 3, 0, 20)
        ");
        $stmt->execute([$usuario_id]);
        
        echo "✅ Resultado inserido: 85% (17/20 acertos)\n";
        
        // Verificar badges
        echo "🏆 Verificando badges...\n";
        
        $badge_conquistada = verificarBadgesProvas($usuario_id);
        if ($badge_conquistada) {
            echo "✅ Badge de prova conquistada!\n";
        } else {
            echo "ℹ️ Nenhuma nova badge de prova\n";
        }
        
        // Verificar todas as badges
        $todas_badges = verificarTodasBadges($usuario_id);
        echo "📊 Tipos de badges verificados: " . count($todas_badges) . "\n";
        if (!empty($todas_badges)) {
            echo "   Badges conquistadas: " . implode(', ', $todas_badges) . "\n";
        }
        
        // Mostrar badges do usuário
        if (function_exists('getBadgesUsuario')) {
            $badges_usuario = getBadgesUsuario($usuario_id);
            echo "🏅 Total de badges do usuário: " . count($badges_usuario) . "\n";
            
            if (!empty($badges_usuario)) {
                echo "\n📋 Badges conquistadas:\n";
                foreach ($badges_usuario as $badge) {
                    echo "   🏆 {$badge['nome']}: {$badge['descricao']}\n";
                }
            }
        }
        
    } else {
        echo "❌ Nenhum usuário encontrado para teste\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro no exemplo: " . $e->getMessage() . "\n";
}

echo "\n🎯 RESUMO DAS INTEGRAÇÕES NECESSÁRIAS:\n";
echo "======================================\n";
echo "1. ✅ Após completar teste: verificarBadgesProvas() + BadgesManager\n";
echo "2. ✅ Após participar no fórum: verificarBadgesForum()\n";
echo "3. ✅ Após calcular GPA: verificarBadgesGPA()\n";
echo "4. ✅ Após visitar país: verificarBadgesPaises()\n";
echo "5. ✅ Login/ações importantes: verificarTodasBadges()\n";
echo "6. ✅ Exibir badges: getBadgesUsuario()\n\n";

echo "📁 ARQUIVOS QUE PRECISAM SER MODIFICADOS:\n";
echo "=========================================\n";
echo "• processar_teste.php (ou similar) - adicionar verificação de badges de provas\n";
echo "• forum.php (salvar tópico/resposta) - adicionar verificação de badges de fórum\n";
echo "• calculadora_gpa.php (ou similar) - adicionar verificação de badges de GPA\n";
echo "• paginas de países - adicionar verificação de badges de países\n";
echo "• pagina_usuario.php - exibir badges conquistadas\n";
echo "• login.php - verificação geral de badges no login\n\n";

echo "🎉 EXEMPLO DE INTEGRAÇÃO CONCLUÍDO!\n";
?>
