<?php
/**
 * API para salvar GPA calculado pelo usuário
 */

require_once 'config.php';
require_once 'sistema_badges.php';
iniciarSessaoSegura();

header('Content-Type: application/json');

// Verificar se usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não logado']);
    exit;
}

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Obter dados JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$gpa = $data['gpa'] ?? null;
$notas = $data['notas'] ?? [];

// Validar dados
if (!is_numeric($gpa) || $gpa < 0 || $gpa > 4) {
    echo json_encode(['success' => false, 'message' => 'GPA inválido']);
    exit;
}

if (empty($notas) || !is_array($notas)) {
    echo json_encode(['success' => false, 'message' => 'Notas inválidas']);
    exit;
}

// Validar notas
foreach ($notas as $nota) {
    if (!is_numeric($nota) || $nota < 0 || $nota > 10) {
        echo json_encode(['success' => false, 'message' => 'Nota inválida encontrada']);
        exit;
    }
}

try {
    // Salvar GPA
    if (salvarGPA($usuario_id, $gpa, $notas)) {
        // Verificar se conquistou nova badge
        $badges_antes = obterBadgesUsuario($usuario_id);
        verificarBadgesGPA($usuario_id);
        $badges_depois = obterBadgesUsuario($usuario_id);
        
        $nova_badge = null;
        if (count($badges_depois) > count($badges_antes)) {
            // Encontrar a nova badge
            foreach ($badges_depois as $badge) {
                $encontrada = false;
                foreach ($badges_antes as $badge_antiga) {
                    if ($badge['codigo'] === $badge_antiga['codigo']) {
                        $encontrada = true;
                        break;
                    }
                }
                if (!$encontrada && strpos($badge['codigo'], 'gpa_') === 0) {
                    $nova_badge = $badge;
                    break;
                }
            }
        }
        
        $response = [
            'success' => true,
            'message' => 'GPA salvo com sucesso',
            'gpa' => $gpa
        ];
        
        if ($nova_badge) {
            $response['badge_conquistada'] = true;
            $response['badge_nome'] = $nova_badge['nome'];
            $response['badge_descricao'] = $nova_badge['descricao'];
        }
        
        echo json_encode($response);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao salvar GPA']);
    }
    
} catch (Exception $e) {
    error_log("Erro ao salvar GPA: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}
?>
