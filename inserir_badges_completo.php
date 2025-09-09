<?php
/**
 * Script completo para inserir TODAS as badges necessárias
 * Versão definitiva e robusta
 */

require_once 'config.php';

echo "🏆 INSERÇÃO COMPLETA DE BADGES - VERSÃO DEFINITIVA\n";
echo "==================================================\n\n";

try {
    $pdo = conectarBD();
    
    // Array completo com TODAS as badges necessárias
    $badges = [
        // ===== BADGES DE PROVAS =====
        [
            'codigo' => 'prova_bronze',
            'nome' => 'Primeiro Passo',
            'descricao' => 'Obteve entre 20% e 40% de acertos em uma prova',
            'icone' => '🥉',
            'tipo' => 'pontuacao',
            'categoria' => 'teste',
            'condicao_valor' => 20,
            'raridade' => 'comum',
            'experiencia_bonus' => 50
        ],
        [
            'codigo' => 'prova_prata',
            'nome' => 'Progredindo',
            'descricao' => 'Obteve entre 40% e 60% de acertos em uma prova',
            'icone' => '🥈',
            'tipo' => 'pontuacao',
            'categoria' => 'teste',
            'condicao_valor' => 40,
            'raridade' => 'comum',
            'experiencia_bonus' => 100
        ],
        [
            'codigo' => 'prova_ouro',
            'nome' => 'Bom Desempenho',
            'descricao' => 'Obteve entre 60% e 80% de acertos em uma prova',
            'icone' => '🥇',
            'tipo' => 'pontuacao',
            'categoria' => 'teste',
            'condicao_valor' => 60,
            'raridade' => 'raro',
            'experiencia_bonus' => 200
        ],
        [
            'codigo' => 'prova_rubi',
            'nome' => 'Excelente',
            'descricao' => 'Obteve entre 80% e 99% de acertos em uma prova',
            'icone' => '💎',
            'tipo' => 'pontuacao',
            'categoria' => 'teste',
            'condicao_valor' => 80,
            'raridade' => 'epico',
            'experiencia_bonus' => 300
        ],
        [
            'codigo' => 'prova_diamante',
            'nome' => 'Perfeição',
            'descricao' => 'Obteve 100% de acertos em uma prova',
            'icone' => '💠',
            'tipo' => 'pontuacao',
            'categoria' => 'teste',
            'condicao_valor' => 100,
            'raridade' => 'lendario',
            'experiencia_bonus' => 500
        ],
        
        // ===== BADGES DE FÓRUM =====
        [
            'codigo' => 'forum_bronze',
            'nome' => 'Primeira Participação',
            'descricao' => 'Participou 1 vez no fórum (tópico ou resposta)',
            'icone' => '💬',
            'tipo' => 'frequencia',
            'categoria' => 'forum',
            'condicao_valor' => 1,
            'raridade' => 'comum',
            'experiencia_bonus' => 50
        ],
        [
            'codigo' => 'forum_prata',
            'nome' => 'Participante Ativo',
            'descricao' => 'Participou 3 vezes no fórum (tópicos ou respostas)',
            'icone' => '💬',
            'tipo' => 'frequencia',
            'categoria' => 'forum',
            'condicao_valor' => 3,
            'raridade' => 'comum',
            'experiencia_bonus' => 100
        ],
        [
            'codigo' => 'forum_ouro',
            'nome' => 'Colaborador',
            'descricao' => 'Participou 5 vezes no fórum (tópicos ou respostas)',
            'icone' => '💬',
            'tipo' => 'frequencia',
            'categoria' => 'forum',
            'condicao_valor' => 5,
            'raridade' => 'raro',
            'experiencia_bonus' => 200
        ],
        [
            'codigo' => 'forum_rubi',
            'nome' => 'Expert do Fórum',
            'descricao' => 'Participou 7 vezes no fórum (tópicos ou respostas)',
            'icone' => '💬',
            'tipo' => 'frequencia',
            'categoria' => 'forum',
            'condicao_valor' => 7,
            'raridade' => 'epico',
            'experiencia_bonus' => 300
        ],
        [
            'codigo' => 'forum_diamante',
            'nome' => 'Mestre do Fórum',
            'descricao' => 'Participou 9+ vezes no fórum (tópicos ou respostas)',
            'icone' => '💬',
            'tipo' => 'frequencia',
            'categoria' => 'forum',
            'condicao_valor' => 9,
            'raridade' => 'lendario',
            'experiencia_bonus' => 500
        ],
        
        // ===== BADGES DE GPA =====
        [
            'codigo' => 'gpa_bronze',
            'nome' => 'GPA Iniciante',
            'descricao' => 'Calculou GPA entre 2.0 e 2.5',
            'icone' => '📊',
            'tipo' => 'pontuacao',
            'categoria' => 'gpa',
            'condicao_valor' => 20, // 2.0 * 10
            'raridade' => 'comum',
            'experiencia_bonus' => 50
        ],
        [
            'codigo' => 'gpa_prata',
            'nome' => 'GPA Bom',
            'descricao' => 'Calculou GPA entre 2.5 e 3.0',
            'icone' => '📊',
            'tipo' => 'pontuacao',
            'categoria' => 'gpa',
            'condicao_valor' => 25, // 2.5 * 10
            'raridade' => 'comum',
            'experiencia_bonus' => 100
        ],
        [
            'codigo' => 'gpa_ouro',
            'nome' => 'GPA Excelente',
            'descricao' => 'Calculou GPA entre 3.0 e 3.5',
            'icone' => '📊',
            'tipo' => 'pontuacao',
            'categoria' => 'gpa',
            'condicao_valor' => 30, // 3.0 * 10
            'raridade' => 'raro',
            'experiencia_bonus' => 200
        ],
        [
            'codigo' => 'gpa_rubi',
            'nome' => 'GPA Superior',
            'descricao' => 'Calculou GPA entre 3.5 e 4.0',
            'icone' => '📊',
            'tipo' => 'pontuacao',
            'categoria' => 'gpa',
            'condicao_valor' => 35, // 3.5 * 10
            'raridade' => 'epico',
            'experiencia_bonus' => 300
        ],
        [
            'codigo' => 'gpa_diamante',
            'nome' => 'GPA Perfeito',
            'descricao' => 'Calculou GPA 4.0',
            'icone' => '📊',
            'tipo' => 'pontuacao',
            'categoria' => 'gpa',
            'condicao_valor' => 40, // 4.0 * 10
            'raridade' => 'lendario',
            'experiencia_bonus' => 500
        ],
        
        // ===== BADGES DE PAÍSES =====
        [
            'codigo' => 'paises_bronze',
            'nome' => 'Explorador Iniciante',
            'descricao' => 'Visitou 5 países diferentes',
            'icone' => '🌍',
            'tipo' => 'frequencia',
            'categoria' => 'paises',
            'condicao_valor' => 5,
            'raridade' => 'comum',
            'experiencia_bonus' => 50
        ],
        [
            'codigo' => 'paises_prata',
            'nome' => 'Viajante',
            'descricao' => 'Visitou 10 países diferentes',
            'icone' => '🌍',
            'tipo' => 'frequencia',
            'categoria' => 'paises',
            'condicao_valor' => 10,
            'raridade' => 'comum',
            'experiencia_bonus' => 100
        ],
        [
            'codigo' => 'paises_ouro',
            'nome' => 'Explorador',
            'descricao' => 'Visitou 15 países diferentes',
            'icone' => '🌍',
            'tipo' => 'frequencia',
            'categoria' => 'paises',
            'condicao_valor' => 15,
            'raridade' => 'raro',
            'experiencia_bonus' => 200
        ],
        [
            'codigo' => 'paises_rubi',
            'nome' => 'Aventureiro',
            'descricao' => 'Visitou 20 países diferentes',
            'icone' => '🌍',
            'tipo' => 'frequencia',
            'categoria' => 'paises',
            'condicao_valor' => 20,
            'raridade' => 'epico',
            'experiencia_bonus' => 300
        ],
        [
            'codigo' => 'paises_diamante',
            'nome' => 'Cidadão do Mundo',
            'descricao' => 'Visitou 28+ países diferentes',
            'icone' => '🌍',
            'tipo' => 'frequencia',
            'categoria' => 'paises',
            'condicao_valor' => 28,
            'raridade' => 'lendario',
            'experiencia_bonus' => 500
        ],

        // ===== BADGES DO BADGESMANAGER =====
        [
            'codigo' => 'iniciante',
            'nome' => 'Iniciante',
            'descricao' => 'Realizou os primeiros testes',
            'icone' => '🌱',
            'tipo' => 'especial',
            'categoria' => 'teste',
            'condicao_valor' => 1,
            'raridade' => 'comum',
            'experiencia_bonus' => 25
        ],
        [
            'codigo' => 'experiente',
            'nome' => 'Experiente',
            'descricao' => 'Realizou vários testes com bom desempenho',
            'icone' => '📚',
            'tipo' => 'especial',
            'categoria' => 'teste',
            'condicao_valor' => 5,
            'raridade' => 'comum',
            'experiencia_bonus' => 100
        ],
        [
            'codigo' => 'mestre',
            'nome' => 'Mestre',
            'descricao' => 'Demonstrou maestria em testes',
            'icone' => '🎓',
            'tipo' => 'especial',
            'categoria' => 'teste',
            'condicao_valor' => 15,
            'raridade' => 'epico',
            'experiencia_bonus' => 300
        ],
        [
            'codigo' => 'lenda',
            'nome' => 'Lenda',
            'descricao' => 'Alcançou status lendário nos testes',
            'icone' => '👑',
            'tipo' => 'especial',
            'categoria' => 'teste',
            'condicao_valor' => 30,
            'raridade' => 'lendario',
            'experiencia_bonus' => 500
        ],

        // ===== BADGES DE ESPECIALISTA =====
        [
            'codigo' => 'especialista_sat',
            'nome' => 'Especialista SAT',
            'descricao' => 'Realizou 5 testes SAT',
            'icone' => '🎯',
            'tipo' => 'frequencia',
            'categoria' => 'teste',
            'condicao_valor' => 5,
            'raridade' => 'raro',
            'experiencia_bonus' => 200
        ],
        [
            'codigo' => 'especialista_enem',
            'nome' => 'Especialista ENEM',
            'descricao' => 'Realizou 5 testes ENEM',
            'icone' => '🎯',
            'tipo' => 'frequencia',
            'categoria' => 'teste',
            'condicao_valor' => 5,
            'raridade' => 'raro',
            'experiencia_bonus' => 200
        ],
        [
            'codigo' => 'especialista_vestibular',
            'nome' => 'Especialista Vestibular',
            'descricao' => 'Realizou 5 testes de vestibular',
            'icone' => '🎯',
            'tipo' => 'frequencia',
            'categoria' => 'teste',
            'condicao_valor' => 5,
            'raridade' => 'raro',
            'experiencia_bonus' => 200
        ],

        // ===== BADGES DE CONSISTÊNCIA =====
        [
            'codigo' => 'consistente',
            'nome' => 'Consistente',
            'descricao' => 'Obteve 5 resultados acima de 70%',
            'icone' => '📈',
            'tipo' => 'especial',
            'categoria' => 'teste',
            'condicao_valor' => 5,
            'raridade' => 'raro',
            'experiencia_bonus' => 200
        ],
        [
            'codigo' => 'dedicado',
            'nome' => 'Dedicado',
            'descricao' => 'Obteve 10 resultados acima de 70%',
            'icone' => '📈',
            'tipo' => 'especial',
            'categoria' => 'teste',
            'condicao_valor' => 10,
            'raridade' => 'epico',
            'experiencia_bonus' => 300
        ],

        // ===== BADGES DE FREQUÊNCIA =====
        [
            'codigo' => 'maratonista',
            'nome' => 'Maratonista',
            'descricao' => 'Realizou 20 testes',
            'icone' => '🏃',
            'tipo' => 'frequencia',
            'categoria' => 'teste',
            'condicao_valor' => 20,
            'raridade' => 'raro',
            'experiencia_bonus' => 250
        ],
        [
            'codigo' => 'persistente',
            'nome' => 'Persistente',
            'descricao' => 'Realizou 50 testes',
            'icone' => '🏃',
            'tipo' => 'frequencia',
            'categoria' => 'teste',
            'condicao_valor' => 50,
            'raridade' => 'epico',
            'experiencia_bonus' => 400
        ],

        // ===== BADGES DE VELOCIDADE E EFICIÊNCIA =====
        [
            'codigo' => 'rapido',
            'nome' => 'Rápido',
            'descricao' => 'Completou teste em tempo recorde',
            'icone' => '⚡',
            'tipo' => 'especial',
            'categoria' => 'teste',
            'condicao_valor' => 1,
            'raridade' => 'raro',
            'experiencia_bonus' => 150
        ],
        [
            'codigo' => 'eficiente',
            'nome' => 'Eficiente',
            'descricao' => 'Obteve alta pontuação em pouco tempo',
            'icone' => '⚡',
            'tipo' => 'especial',
            'categoria' => 'teste',
            'condicao_valor' => 1,
            'raridade' => 'epico',
            'experiencia_bonus' => 250
        ],
        [
            'codigo' => 'perfeccionista',
            'nome' => 'Perfeccionista',
            'descricao' => 'Obteve pontuação perfeita múltiplas vezes',
            'icone' => '💎',
            'tipo' => 'especial',
            'categoria' => 'teste',
            'condicao_valor' => 3,
            'raridade' => 'lendario',
            'experiencia_bonus' => 500
        ]
    ];

    echo "📊 Total de badges a inserir: " . count($badges) . "\n\n";
    
    // Preparar statement para inserção
    $stmt = $pdo->prepare("
        INSERT INTO badges (codigo, nome, descricao, icone, tipo, categoria, condicao_valor, raridade, experiencia_bonus, ativa)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
        ON DUPLICATE KEY UPDATE
        nome = VALUES(nome),
        descricao = VALUES(descricao),
        icone = VALUES(icone),
        tipo = VALUES(tipo),
        categoria = VALUES(categoria),
        condicao_valor = VALUES(condicao_valor),
        raridade = VALUES(raridade),
        experiencia_bonus = VALUES(experiencia_bonus),
        ativa = 1
    ");
    
    $inseridas = 0;
    $atualizadas = 0;
    
    foreach ($badges as $badge) {
        // Verificar se já existe
        $check = $pdo->prepare("SELECT id FROM badges WHERE codigo = ?");
        $check->execute([$badge['codigo']]);
        $existe = $check->fetch();
        
        $stmt->execute([
            $badge['codigo'],
            $badge['nome'],
            $badge['descricao'],
            $badge['icone'],
            $badge['tipo'],
            $badge['categoria'],
            $badge['condicao_valor'],
            $badge['raridade'],
            $badge['experiencia_bonus']
        ]);
        
        if ($existe) {
            $atualizadas++;
            echo "🔄 Badge '{$badge['nome']}' atualizada\n";
        } else {
            $inseridas++;
            echo "✅ Badge '{$badge['nome']}' inserida\n";
        }
    }
    
    echo "\n📊 RESUMO FINAL:\n";
    echo "================\n";
    echo "✅ Badges inseridas: $inseridas\n";
    echo "🔄 Badges atualizadas: $atualizadas\n";
    echo "📝 Total processado: " . count($badges) . "\n";
    
    // Verificar resultado final
    $stmt = $pdo->query("SELECT COUNT(*) FROM badges WHERE ativa = 1");
    $total_ativas = $stmt->fetchColumn();
    echo "🏆 Total de badges ativas no sistema: $total_ativas\n";
    
    echo "\n🎉 INSERÇÃO COMPLETA REALIZADA COM SUCESSO!\n";
    
} catch (Exception $e) {
    echo "❌ Erro durante inserção: " . $e->getMessage() . "\n";
    echo "📋 Detalhes do erro:\n";
    echo "   Arquivo: " . $e->getFile() . "\n";
    echo "   Linha: " . $e->getLine() . "\n";
}

echo "\n🎯 INSERÇÃO CONCLUÍDA!\n";
?>
