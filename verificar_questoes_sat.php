<?php
require_once 'config.php';

echo "\n📊 VERIFICANDO QUESTÕES SAT NO BANCO DE DADOS\n";
echo "======================================\n\n";

try {
    $pdo = conectarBD();
    
    // Total de questões SAT
    $stmt = $pdo->query("SELECT COUNT(*) FROM questoes WHERE tipo_prova = 'sat'");
    $total = $stmt->fetchColumn();
    echo "✅ Total de questões SAT: $total\n\n";
    
    // Distribuição por matéria
    echo "📚 DISTRIBUIÇÃO POR MATÉRIA:\n";
    echo "============================\n";
    $stmt = $pdo->query("SELECT materia, COUNT(*) as total FROM questoes WHERE tipo_prova = 'sat' GROUP BY materia");
    $materias = $stmt->fetchAll();
    
    foreach ($materias as $materia) {
        echo "📖 {$materia['materia']}: {$materia['total']} questões\n";
    }
    
    // Distribuição por dificuldade
    echo "\n📈 DISTRIBUIÇÃO POR DIFICULDADE:\n";
    echo "=================================\n";
    $stmt = $pdo->query("SELECT dificuldade, COUNT(*) as total FROM questoes WHERE tipo_prova = 'sat' GROUP BY dificuldade");
    $distribuicao = $stmt->fetchAll();
    
    foreach ($distribuicao as $nivel) {
        echo "📝 {$nivel['dificuldade']}: {$nivel['total']} questões\n";
    }
    
    // Exemplos de questões
    echo "\n📝 EXEMPLOS DE QUESTÕES SAT:\n";
    echo "===========================\n";
    $stmt = $pdo->query("SELECT id, numero_questao, LEFT(enunciado, 100) as enunciado_resumo, resposta_correta FROM questoes WHERE tipo_prova = 'sat' ORDER BY RAND() LIMIT 5");
    $exemplos = $stmt->fetchAll();
    
    foreach ($exemplos as $exemplo) {
        echo "ID: {$exemplo['id']} | Questão {$exemplo['numero_questao']} | Resposta: {$exemplo['resposta_correta']}\n";
        echo "Enunciado: {$exemplo['enunciado_resumo']}...\n\n";
    }
    
    echo "\n🎉 VERIFICAÇÃO CONCLUÍDA!\n";
    echo "Você pode acessar o simulador em: http://localhost:8080/simulador_provas.php\n";
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
?>