<?php
/**
 * Sistema de Badges - Verificação e Atribuição
 */

require_once 'config.php';

/**
 * Verifica e atribui badges de provas realizadas
 */
function verificarBadgesProvas($usuario_id) {
    try {
        $pdo = conectarBD();
        
        // Buscar melhor resultado do usuário (maior porcentagem de acertos)
        $stmt = $pdo->prepare("
            SELECT MAX((acertos * 100.0) / total_questoes) as melhor_porcentagem
            FROM resultados_testes 
            WHERE usuario_id = ? AND total_questoes > 0
        ");
        $stmt->execute([$usuario_id]);
        $resultado = $stmt->fetch();
        
        if (!$resultado || $resultado['melhor_porcentagem'] === null) {
            return false;
        }
        
        $porcentagem = $resultado['melhor_porcentagem'];
        $badge_codigo = null;
        
        // Determinar qual badge deve receber
        if ($porcentagem >= 100) {
            $badge_codigo = 'prova_diamante';
        } elseif ($porcentagem >= 80) {
            $badge_codigo = 'prova_rubi';
        } elseif ($porcentagem >= 60) {
            $badge_codigo = 'prova_ouro';
        } elseif ($porcentagem >= 40) {
            $badge_codigo = 'prova_prata';
        } elseif ($porcentagem >= 20) {
            $badge_codigo = 'prova_bronze';
        }
        
        if ($badge_codigo) {
            return atribuirBadge($usuario_id, $badge_codigo, "Prova com {$porcentagem}% de acertos");
        }
        
        return false;
        
    } catch (Exception $e) {
        error_log("Erro ao verificar badges de provas: " . $e->getMessage());
        return false;
    }
}

/**
 * Verifica e atribui badges de participação no fórum
 */
function verificarBadgesForum($usuario_id) {
    try {
        $pdo = conectarBD();
        
        // Contar tópicos criados
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total_topicos
            FROM forum_topicos 
            WHERE autor_id = ?
        ");
        $stmt->execute([$usuario_id]);
        $topicos = $stmt->fetch()['total_topicos'] ?? 0;
        
        // Contar respostas feitas
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total_respostas
            FROM forum_respostas 
            WHERE autor_id = ?
        ");
        $stmt->execute([$usuario_id]);
        $respostas = $stmt->fetch()['total_respostas'] ?? 0;
        
        $total_participacoes = $topicos + $respostas;
        $badge_codigo = null;
        
        // Determinar qual badge deve receber
        if ($total_participacoes >= 9) {
            $badge_codigo = 'forum_diamante';
        } elseif ($total_participacoes >= 7) {
            $badge_codigo = 'forum_rubi';
        } elseif ($total_participacoes >= 5) {
            $badge_codigo = 'forum_ouro';
        } elseif ($total_participacoes >= 3) {
            $badge_codigo = 'forum_prata';
        } elseif ($total_participacoes >= 1) {
            $badge_codigo = 'forum_bronze';
        }
        
        if ($badge_codigo) {
            return atribuirBadge($usuario_id, $badge_codigo, "{$total_participacoes} participações no fórum");
        }
        
        return false;
        
    } catch (Exception $e) {
        error_log("Erro ao verificar badges de fórum: " . $e->getMessage());
        return false;
    }
}

/**
 * Verifica e atribui badges de GPA
 */
function verificarBadgesGPA($usuario_id) {
    try {
        $pdo = conectarBD();
        
        // Buscar maior GPA calculado pelo usuário
        $stmt = $pdo->prepare("
            SELECT MAX(gpa_calculado) as melhor_gpa
            FROM usuario_gpa 
            WHERE usuario_id = ?
        ");
        $stmt->execute([$usuario_id]);
        $resultado = $stmt->fetch();
        
        if (!$resultado || $resultado['melhor_gpa'] === null) {
            return false;
        }
        
        $gpa = $resultado['melhor_gpa'];
        $badge_codigo = null;
        
        // Determinar qual badge deve receber
        if ($gpa >= 4.0) {
            $badge_codigo = 'gpa_diamante';
        } elseif ($gpa >= 3.5) {
            $badge_codigo = 'gpa_rubi';
        } elseif ($gpa >= 3.0) {
            $badge_codigo = 'gpa_ouro';
        } elseif ($gpa >= 2.5) {
            $badge_codigo = 'gpa_prata';
        } elseif ($gpa >= 2.0) {
            $badge_codigo = 'gpa_bronze';
        }
        
        if ($badge_codigo) {
            return atribuirBadge($usuario_id, $badge_codigo, "GPA de {$gpa}");
        }
        
        return false;
        
    } catch (Exception $e) {
        error_log("Erro ao verificar badges de GPA: " . $e->getMessage());
        return false;
    }
}

/**
 * Verifica e atribui badges de países visitados
 */
function verificarBadgesPaises($usuario_id) {
    try {
        $pdo = conectarBD();
        
        // Contar países únicos visitados
        $stmt = $pdo->prepare("
            SELECT COUNT(DISTINCT pais_codigo) as total_paises
            FROM paises_visitados 
            WHERE usuario_id = ?
        ");
        $stmt->execute([$usuario_id]);
        $resultado = $stmt->fetch();
        
        if (!$resultado) {
            return false;
        }
        
        $total_paises = $resultado['total_paises'];
        $badge_codigo = null;
        
        // Determinar qual badge deve receber
        if ($total_paises >= 28) {
            $badge_codigo = 'paises_diamante';
        } elseif ($total_paises >= 20) {
            $badge_codigo = 'paises_rubi';
        } elseif ($total_paises >= 15) {
            $badge_codigo = 'paises_ouro';
        } elseif ($total_paises >= 10) {
            $badge_codigo = 'paises_prata';
        } elseif ($total_paises >= 5) {
            $badge_codigo = 'paises_bronze';
        }
        
        if ($badge_codigo) {
            return atribuirBadge($usuario_id, $badge_codigo, "{$total_paises} países visitados");
        }
        
        return false;
        
    } catch (Exception $e) {
        error_log("Erro ao verificar badges de países: " . $e->getMessage());
        return false;
    }
}

/**
 * Atribui uma badge ao usuário (se ainda não tiver)
 */
function atribuirBadge($usuario_id, $badge_codigo, $contexto = null) {
    try {
        $pdo = conectarBD();
        
        // Buscar ID da badge
        $stmt = $pdo->prepare("SELECT id FROM badges WHERE codigo = ? AND ativa = 1");
        $stmt->execute([$badge_codigo]);
        $badge = $stmt->fetch();
        
        if (!$badge) {
            return false;
        }
        
        // Verificar se usuário já tem esta badge
        $stmt = $pdo->prepare("
            SELECT id FROM usuario_badges 
            WHERE usuario_id = ? AND badge_id = ?
        ");
        $stmt->execute([$usuario_id, $badge['id']]);
        
        if ($stmt->fetch()) {
            return false; // Já tem a badge
        }
        
        // Atribuir a badge
        $stmt = $pdo->prepare("
            INSERT INTO usuario_badges (usuario_id, badge_id, data_conquista, contexto)
            VALUES (?, ?, NOW(), ?)
        ");
        $stmt->execute([$usuario_id, $badge['id'], $contexto]);
        
        return true;
        
    } catch (Exception $e) {
        error_log("Erro ao atribuir badge: " . $e->getMessage());
        return false;
    }
}

/**
 * Verifica todas as badges para um usuário
 */
function verificarTodasBadges($usuario_id) {
    $badges_conquistadas = [];
    
    if (verificarBadgesProvas($usuario_id)) {
        $badges_conquistadas[] = 'provas';
    }
    
    if (verificarBadgesForum($usuario_id)) {
        $badges_conquistadas[] = 'forum';
    }
    
    if (verificarBadgesGPA($usuario_id)) {
        $badges_conquistadas[] = 'gpa';
    }
    
    if (verificarBadgesPaises($usuario_id)) {
        $badges_conquistadas[] = 'paises';
    }
    
    return $badges_conquistadas;
}

/**
 * Obtém todas as badges conquistadas por um usuário
 */
function obterBadgesUsuario($usuario_id) {
    try {
        $pdo = conectarBD();
        
        $stmt = $pdo->prepare("
            SELECT b.codigo, b.nome, b.descricao, b.icone, b.raridade, 
                   ub.data_conquista, ub.contexto
            FROM usuario_badges ub
            JOIN badges b ON ub.badge_id = b.id
            WHERE ub.usuario_id = ? AND b.ativa = 1
            ORDER BY ub.data_conquista DESC
        ");
        $stmt->execute([$usuario_id]);
        
        return $stmt->fetchAll();
        
    } catch (Exception $e) {
        error_log("Erro ao obter badges do usuário: " . $e->getMessage());
        return [];
    }
}

/**
 * Salva GPA calculado pelo usuário
 */
function salvarGPA($usuario_id, $gpa, $notas) {
    try {
        $pdo = conectarBD();
        
        // Salvar na tabela usuario_gpa
        $stmt = $pdo->prepare("
            INSERT INTO usuario_gpa (usuario_id, gpa_calculado, notas_utilizadas, data_calculo)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$usuario_id, $gpa, json_encode($notas)]);
        
        // Atualizar o GPA no perfil do usuário
        $stmt = $pdo->prepare("
            UPDATE perfil_usuario 
            SET gpa = ? 
            WHERE usuario_id = ?
        ");
        $stmt->execute([$gpa, $usuario_id]);
        
        // Se o perfil não existe, criar um novo
        if ($stmt->rowCount() == 0) {
            $stmt = $pdo->prepare("
                INSERT INTO perfil_usuario (usuario_id, gpa) 
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE gpa = VALUES(gpa)
            ");
            $stmt->execute([$usuario_id, $gpa]);
        }
        
        // Verificar badges de GPA após salvar
        verificarBadgesGPA($usuario_id);
        
        return true;
        
    } catch (Exception $e) {
        error_log("Erro ao salvar GPA: " . $e->getMessage());
        return false;
    }
}
?>
