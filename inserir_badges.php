<?php
/**
 * Script para inserir todas as badges do sistema
 */

require_once 'config.php';

try {
    $pdo = conectarBD();
    
    echo "🏆 INSERINDO BADGES NO SISTEMA\n";
    echo "==============================\n\n";
    
    // Array com todas as badges
    $badges = [
        // BADGES DE PROVAS
        [
            'codigo' => 'prova_bronze',
            'nome' => 'Primeiro Passo',
            'descricao' => 'Realizou uma prova e obteve entre 20% e 40% de acertos',
            'icone' => '🥉',
            'tipo' => 'pontuacao',
            'categoria' => 'teste',
            'condicao_valor' => 20,
            'raridade' => 'comum',
            'experiencia_bonus' => 50,
            'imagem' => 'badge_prova_bronze.jpg'
        ],
        [
            'codigo' => 'prova_prata',
            'nome' => 'Progredindo',
            'descricao' => 'Realizou uma prova e obteve entre 40% e 60% de acertos',
            'icone' => '🥈',
            'tipo' => 'pontuacao',
            'categoria' => 'teste',
            'condicao_valor' => 40,
            'raridade' => 'comum',
            'experiencia_bonus' => 100,
            'imagem' => 'badge_prova_prata.jpg'
        ],
        [
            'codigo' => 'prova_ouro',
            'nome' => 'Bom Desempenho',
            'descricao' => 'Realizou uma prova e obteve entre 60% e 80% de acertos',
            'icone' => '🥇',
            'tipo' => 'pontuacao',
            'categoria' => 'teste',
            'condicao_valor' => 60,
            'raridade' => 'raro',
            'experiencia_bonus' => 200,
            'imagem' => 'badge_prova_ouro.jpg'
        ],
        [
            'codigo' => 'prova_rubi',
            'nome' => 'Excelente',
            'descricao' => 'Realizou uma prova e obteve entre 80% e 99% de acertos',
            'icone' => '💎',
            'tipo' => 'pontuacao',
            'categoria' => 'teste',
            'condicao_valor' => 80,
            'raridade' => 'epico',
            'experiencia_bonus' => 300,
            'imagem' => 'badge_prova_rubi.jpg'
        ],
        [
            'codigo' => 'prova_diamante',
            'nome' => 'Perfeição',
            'descricao' => 'Realizou uma prova e obteve 100% de acertos',
            'icone' => '💎',
            'tipo' => 'pontuacao',
            'categoria' => 'teste',
            'condicao_valor' => 100,
            'raridade' => 'lendario',
            'experiencia_bonus' => 500,
            'imagem' => 'badge_prova_diamante.jpg'
        ],
        
        // BADGES DE FÓRUM
        [
            'codigo' => 'forum_bronze',
            'nome' => 'Primeira Participação',
            'descricao' => 'Criou 1 tópico ou respondeu 1 pergunta no fórum',
            'icone' => '💬',
            'tipo' => 'frequencia',
            'categoria' => 'forum',
            'condicao_valor' => 1,
            'raridade' => 'comum',
            'experiencia_bonus' => 50,
            'imagem' => 'badge_forum_bronze.jpg'
        ],
        [
            'codigo' => 'forum_prata',
            'nome' => 'Participante Ativo',
            'descricao' => 'Criou mais de 3 tópicos ou respondeu mais de 3 perguntas',
            'icone' => '💬',
            'tipo' => 'frequencia',
            'categoria' => 'forum',
            'condicao_valor' => 3,
            'raridade' => 'comum',
            'experiencia_bonus' => 100,
            'imagem' => 'badge_forum_prata.jpg'
        ],
        [
            'codigo' => 'forum_ouro',
            'nome' => 'Colaborador',
            'descricao' => 'Criou mais de 5 tópicos ou respondeu mais de 5 perguntas',
            'icone' => '💬',
            'tipo' => 'frequencia',
            'categoria' => 'forum',
            'condicao_valor' => 5,
            'raridade' => 'raro',
            'experiencia_bonus' => 200,
            'imagem' => 'badge_forum_ouro.jpg'
        ],
        [
            'codigo' => 'forum_rubi',
            'nome' => 'Expert da Comunidade',
            'descricao' => 'Criou mais de 7 tópicos ou respondeu mais de 7 perguntas',
            'icone' => '💬',
            'tipo' => 'frequencia',
            'categoria' => 'forum',
            'condicao_valor' => 7,
            'raridade' => 'epico',
            'experiencia_bonus' => 300,
            'imagem' => 'badge_forum_rubi.jpg'
        ],
        [
            'codigo' => 'forum_diamante',
            'nome' => 'Líder da Comunidade',
            'descricao' => 'Criou mais de 9 tópicos ou respondeu mais de 9 perguntas',
            'icone' => '💬',
            'tipo' => 'frequencia',
            'categoria' => 'forum',
            'condicao_valor' => 9,
            'raridade' => 'lendario',
            'experiencia_bonus' => 500,
            'imagem' => 'badge_forum_diamante.jpg'
        ],
        
        // BADGES DE GPA
        [
            'codigo' => 'gpa_bronze',
            'nome' => 'Primeiro GPA',
            'descricao' => 'Calculou GPA entre 2.0 e 2.5',
            'icone' => '📊',
            'tipo' => 'pontuacao',
            'categoria' => 'geral',
            'condicao_valor' => 20, // 2.0 * 10 para armazenar como int
            'raridade' => 'comum',
            'experiencia_bonus' => 50,
            'imagem' => 'badge_gpa_bronze.jpg'
        ],
        [
            'codigo' => 'gpa_prata',
            'nome' => 'Bom GPA',
            'descricao' => 'Calculou GPA entre 2.5 e 3.0',
            'icone' => '📊',
            'tipo' => 'pontuacao',
            'categoria' => 'geral',
            'condicao_valor' => 25, // 2.5 * 10
            'raridade' => 'comum',
            'experiencia_bonus' => 100,
            'imagem' => 'badge_gpa_prata.jpg'
        ],
        [
            'codigo' => 'gpa_ouro',
            'nome' => 'Ótimo GPA',
            'descricao' => 'Calculou GPA entre 3.0 e 3.5',
            'icone' => '📊',
            'tipo' => 'pontuacao',
            'categoria' => 'geral',
            'condicao_valor' => 30, // 3.0 * 10
            'raridade' => 'raro',
            'experiencia_bonus' => 200,
            'imagem' => 'badge_gpa_ouro.jpg'
        ],
        [
            'codigo' => 'gpa_rubi',
            'nome' => 'Excelente GPA',
            'descricao' => 'Calculou GPA entre 3.5 e 4.0',
            'icone' => '📊',
            'tipo' => 'pontuacao',
            'categoria' => 'geral',
            'condicao_valor' => 35, // 3.5 * 10
            'raridade' => 'epico',
            'experiencia_bonus' => 300,
            'imagem' => 'badge_gpa_rubi.jpg'
        ],
        [
            'codigo' => 'gpa_diamante',
            'nome' => 'GPA Perfeito',
            'descricao' => 'Calculou GPA 4.0',
            'icone' => '📊',
            'tipo' => 'pontuacao',
            'categoria' => 'geral',
            'condicao_valor' => 40, // 4.0 * 10
            'raridade' => 'lendario',
            'experiencia_bonus' => 500,
            'imagem' => 'badge_gpa_diamante.jpg'
        ],
        
        // BADGES DE PAÍSES
        [
            'codigo' => 'paises_bronze',
            'nome' => 'Explorador Iniciante',
            'descricao' => 'Visitou 5 ou mais países',
            'icone' => '🌍',
            'tipo' => 'frequencia',
            'categoria' => 'social',
            'condicao_valor' => 5,
            'raridade' => 'comum',
            'experiencia_bonus' => 50,
            'imagem' => 'badge_paises_bronze.jpg'
        ],
        [
            'codigo' => 'paises_prata',
            'nome' => 'Viajante',
            'descricao' => 'Visitou 10 ou mais países',
            'icone' => '🌍',
            'tipo' => 'frequencia',
            'categoria' => 'social',
            'condicao_valor' => 10,
            'raridade' => 'comum',
            'experiencia_bonus' => 100,
            'imagem' => 'badge_paises_prata.jpg'
        ],
        [
            'codigo' => 'paises_ouro',
            'nome' => 'Explorador',
            'descricao' => 'Visitou 15 ou mais países',
            'icone' => '🌍',
            'tipo' => 'frequencia',
            'categoria' => 'social',
            'condicao_valor' => 15,
            'raridade' => 'raro',
            'experiencia_bonus' => 200,
            'imagem' => 'badge_paises_ouro.jpg'
        ],
        [
            'codigo' => 'paises_rubi',
            'nome' => 'Aventureiro',
            'descricao' => 'Visitou 20 ou mais países',
            'icone' => '🌍',
            'tipo' => 'frequencia',
            'categoria' => 'social',
            'condicao_valor' => 20,
            'raridade' => 'epico',
            'experiencia_bonus' => 300,
            'imagem' => 'badge_paises_rubi.jpg'
        ],
        [
            'codigo' => 'paises_diamante',
            'nome' => 'Explorador Mundial',
            'descricao' => 'Visitou todos os 28 países disponíveis',
            'icone' => '🌍',
            'tipo' => 'frequencia',
            'categoria' => 'social',
            'condicao_valor' => 28,
            'raridade' => 'lendario',
            'experiencia_bonus' => 500,
            'imagem' => 'badge_paises_diamante.jpg'
        ]
    ];
    
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
        experiencia_bonus = VALUES(experiencia_bonus)
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
    
    echo "\n📊 RESUMO:\n";
    echo "==========\n";
    echo "✅ Badges inseridas: $inseridas\n";
    echo "🔄 Badges atualizadas: $atualizadas\n";
    echo "📝 Total de badges: " . count($badges) . "\n";
    
    echo "\n🎉 BADGES INSERIDAS COM SUCESSO!\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>
