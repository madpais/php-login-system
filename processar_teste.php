<?php
require_once 'config.php';
require_once 'verificar_auth.php';
require_once 'badges_manager.php';

// Verificar se o usuário está logado
verificarLogin();

// Conectar ao banco de dados
$pdo = conectarBD();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido']);
    exit;
}

if (!isset($_POST['sessao_id']) || !isset($_POST['finalizar'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados incompletos']);
    exit;
}

$sessao_id = $_POST['sessao_id'];
$respostas = json_decode($_POST['respostas'] ?? '{}', true);

try {
    // Verificar se a sessão existe e pertence ao usuário
    $stmt = $pdo->prepare("SELECT * FROM sessoes_teste WHERE id = ? AND usuario_id = ? AND status = 'ativo'");
    $stmt->execute([$sessao_id, $_SESSION['user_id']]);
    $sessao = $stmt->fetch();
    
    if (!$sessao) {
        http_response_code(404);
        echo json_encode(['erro' => 'Sessão não encontrada']);
        exit;
    }
    
    // Configurações das provas
    $config_provas = [
        'toefl' => ['questoes_total' => 80],
    'ielts' => ['questoes_total' => 40],
    'sat' => ['questoes_total' => 154],
    'dele' => ['questoes_total' => 50],
    'delf' => ['questoes_total' => 45],
    'testdaf' => ['questoes_total' => 35],
    'jlpt' => ['questoes_total' => 60],
    'hsk' => ['questoes_total' => 100]
    ];
    
    $tipo_prova = $sessao['tipo_prova'];
    $questoes_total = $config_provas[$tipo_prova]['questoes_total'];
    
    // Calcular pontuação (simulada - as respostas corretas virão do JSON/XML)
    $acertos = 0;
    $questoes_respondidas = count($respostas);
    
    // Simular respostas corretas (será substituído pelo sistema real)
    $respostas_corretas = [];
    $alternativas = ['a', 'b', 'c', 'd', 'e'];
    for ($i = 1; $i <= $questoes_total; $i++) {
        $respostas_corretas[$i] = $alternativas[rand(0, 4)];
    }
    
    // Calcular acertos
    foreach ($respostas as $questao => $resposta) {
        if (isset($respostas_corretas[$questao]) && $respostas_corretas[$questao] === $resposta) {
            $acertos++;
        }
    }
    
    $pontuacao = ($acertos / $questoes_total) * 100;
    $tempo_gasto = time() - strtotime($sessao['inicio']);
    
    // Iniciar transação
    $pdo->beginTransaction();
    
    // Atualizar sessão
    $stmt = $pdo->prepare("UPDATE sessoes_teste SET status = 'finalizado', fim = NOW(), pontuacao = ?, acertos = ?, questoes_respondidas = ?, tempo_gasto = ? WHERE id = ?");
    $stmt->execute([$pontuacao, $acertos, $questoes_respondidas, $tempo_gasto, $sessao_id]);
    
    // Salvar resultado detalhado
    $stmt = $pdo->prepare("INSERT INTO resultados_testes (usuario_id, sessao_id, tipo_prova, pontuacao, acertos, total_questoes, questoes_respondidas, tempo_gasto, data_realizacao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$_SESSION['user_id'], $sessao_id, $tipo_prova, $pontuacao, $acertos, $questoes_total, $questoes_respondidas, $tempo_gasto]);
    
    // Salvar respostas individuais
    foreach ($respostas as $questao => $resposta) {
        $correta = isset($respostas_corretas[$questao]) && $respostas_corretas[$questao] === $resposta;
        $stmt = $pdo->prepare("INSERT INTO respostas_usuario (sessao_id, questao_numero, resposta_usuario, resposta_correta, acertou) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$sessao_id, $questao, $resposta, $respostas_corretas[$questao] ?? null, $correta]);
    }
    
    // Verificar e conceder badges
    concederBadges($_SESSION['user_id'], $pontuacao, $tipo_prova, $pdo);
    
    // Confirmar transação
    $pdo->commit();
    
    echo json_encode([
        'sucesso' => true,
        'pontuacao' => $pontuacao,
        'acertos' => $acertos,
        'total' => $questoes_total,
        'sessao_id' => $sessao_id
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Erro ao processar teste: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno do servidor']);
}

function concederBadges($usuario_id, $pontuacao, $tipo_prova, $pdo) {
    $badges_para_conceder = [];
    
    // Badges baseadas na pontuação
    if ($pontuacao >= 90) {
        $badges_para_conceder[] = 'excelencia';
    } elseif ($pontuacao >= 80) {
        $badges_para_conceder[] = 'muito_bom';
    } elseif ($pontuacao >= 70) {
        $badges_para_conceder[] = 'bom';
    } elseif ($pontuacao >= 60) {
        $badges_para_conceder[] = 'satisfatorio';
    }
    
    // Badge de primeiro teste
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM resultados_testes WHERE usuario_id = ?");
    $stmt->execute([$usuario_id]);
    if ($stmt->fetchColumn() == 1) { // Primeiro teste
        $badges_para_conceder[] = 'primeiro_teste';
    }
    
    // Badge específica do tipo de prova
    $badges_para_conceder[] = 'especialista_' . $tipo_prova;
    
    // Verificar badges de sequência
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM resultados_testes WHERE usuario_id = ? AND pontuacao >= 70");
    $stmt->execute([$usuario_id]);
    $testes_bons = $stmt->fetchColumn();
    
    if ($testes_bons >= 5) {
        $badges_para_conceder[] = 'consistente';
    }
    
    if ($testes_bons >= 10) {
        $badges_para_conceder[] = 'dedicado';
    }
    
    // Conceder badges que ainda não foram concedidas
    foreach ($badges_para_conceder as $badge_codigo) {
        // Verificar se a badge existe
        $stmt = $pdo->prepare("SELECT id FROM badges WHERE codigo = ?");
        $stmt->execute([$badge_codigo]);
        $badge = $stmt->fetch();
        
        if ($badge) {
            // Verificar se o usuário já tem esta badge
            $stmt = $pdo->prepare("SELECT id FROM usuario_badges WHERE usuario_id = ? AND badge_id = ?");
            $stmt->execute([$usuario_id, $badge['id']]);
            
            if (!$stmt->fetch()) {
                // Conceder a badge
                $stmt = $pdo->prepare("INSERT INTO usuario_badges (usuario_id, badge_id, data_conquista) VALUES (?, ?, NOW())");
                $stmt->execute([$usuario_id, $badge['id']]);
            }
        }
    }
}
?>