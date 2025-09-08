<?php
/**
 * Sistema de Tracking de Países Visitados
 */

require_once "config.php";
require_once "sistema_badges.php";

function registrarVisitaPais($usuario_id, $pais_codigo) {
    if (!$usuario_id || !$pais_codigo) {
        return false;
    }
    
    // Mapeamento de países
    $paises_nomes = [
        "australia" => "Austrália",
        "belgica" => "Bélgica", 
        "canada" => "Canadá",
        "china" => "China",
        "dinamarca" => "Dinamarca",
        "finlandia" => "Finlândia",
        "franca" => "França",
        "alemanha" => "Alemanha",
        "holanda" => "Holanda",
        "hungria" => "Hungria",
        "india" => "Índia",
        "indonesia" => "Indonésia",
        "irlanda" => "Irlanda",
        "italia" => "Itália",
        "japao" => "Japão",
        "malasia" => "Malásia",
        "noruega" => "Noruega",
        "portugal" => "Portugal",
        "arabia" => "Arábia Saudita",
        "singapura" => "Singapura",
        "africa" => "África do Sul",
        "coreia" => "Coreia do Sul",
        "espanha" => "Espanha",
        "suecia" => "Suécia",
        "suica" => "Suíça",
        "emirados" => "Emirados Árabes Unidos",
        "reinounido" => "Reino Unido",
        "eua" => "Estados Unidos"
    ];
    
    $pais_nome = $paises_nomes[$pais_codigo] ?? ucfirst($pais_codigo);
    
    try {
        $pdo = conectarBD();
        
        // Verificar se já visitou
        $stmt = $pdo->prepare("
            SELECT id, total_visitas 
            FROM paises_visitados 
            WHERE usuario_id = ? AND pais_codigo = ?
        ");
        $stmt->execute([$usuario_id, $pais_codigo]);
        $visita_existente = $stmt->fetch();
        
        if ($visita_existente) {
            // Incrementar contador de visitas
            $stmt = $pdo->prepare("
                UPDATE paises_visitados 
                SET total_visitas = total_visitas + 1,
                    ultima_visita = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            $stmt->execute([$visita_existente["id"]]);
            
            $resultado = [
                "primeira_visita" => false,
                "total_visitas" => $visita_existente["total_visitas"] + 1,
                "pais_nome" => $pais_nome
            ];
        } else {
            // Primeira visita
            $stmt = $pdo->prepare("
                INSERT INTO paises_visitados (usuario_id, pais_codigo, pais_nome)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$usuario_id, $pais_codigo, $pais_nome]);
            
            $resultado = [
                "primeira_visita" => true,
                "total_visitas" => 1,
                "pais_nome" => $pais_nome
            ];
        }
        
        // Verificar badges de países após registrar visita
        verificarBadgesPaises($usuario_id);
        
        return $resultado;
        
    } catch (Exception $e) {
        error_log("Erro ao registrar visita: " . $e->getMessage());
        return false;
    }
}

function obterPaisesVisitados($usuario_id) {
    if (!$usuario_id) {
        return [];
    }
    
    try {
        $pdo = conectarBD();
        
        $stmt = $pdo->prepare("
            SELECT pais_codigo, pais_nome, total_visitas, 
                   data_primeira_visita, ultima_visita
            FROM paises_visitados 
            WHERE usuario_id = ?
            ORDER BY data_primeira_visita DESC
        ");
        $stmt->execute([$usuario_id]);
        
        $paises = [];
        while ($row = $stmt->fetch()) {
            $paises[$row["pais_codigo"]] = $row;
        }
        
        return $paises;
        
    } catch (Exception $e) {
        error_log("Erro ao obter países visitados: " . $e->getMessage());
        return [];
    }
}

function contarPaisesVisitados($usuario_id) {
    if (!$usuario_id) {
        return 0;
    }
    
    try {
        $pdo = conectarBD();
        
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total
            FROM paises_visitados 
            WHERE usuario_id = ?
        ");
        $stmt->execute([$usuario_id]);
        
        $result = $stmt->fetch();
        return $result["total"] ?? 0;
        
    } catch (Exception $e) {
        error_log("Erro ao contar países visitados: " . $e->getMessage());
        return 0;
    }
}

function obterEstatisticasPaises($usuario_id) {
    if (!$usuario_id) {
        return [
            "total_paises" => 0,
            "total_visitas" => 0,
            "pais_mais_visitado" => null,
            "primeira_visita" => null,
            "ultima_visita" => null
        ];
    }
    
    try {
        $pdo = conectarBD();
        
        // Estatísticas gerais
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_paises,
                SUM(total_visitas) as total_visitas,
                MIN(data_primeira_visita) as primeira_visita,
                MAX(ultima_visita) as ultima_visita
            FROM paises_visitados 
            WHERE usuario_id = ?
        ");
        $stmt->execute([$usuario_id]);
        $stats = $stmt->fetch();
        
        // País mais visitado
        $stmt = $pdo->prepare("
            SELECT pais_nome, total_visitas
            FROM paises_visitados 
            WHERE usuario_id = ?
            ORDER BY total_visitas DESC, data_primeira_visita ASC
            LIMIT 1
        ");
        $stmt->execute([$usuario_id]);
        $pais_mais_visitado = $stmt->fetch();
        
        return [
            "total_paises" => $stats["total_paises"] ?? 0,
            "total_visitas" => $stats["total_visitas"] ?? 0,
            "pais_mais_visitado" => $pais_mais_visitado,
            "primeira_visita" => $stats["primeira_visita"],
            "ultima_visita" => $stats["ultima_visita"]
        ];
        
    } catch (Exception $e) {
        error_log("Erro ao obter estatísticas: " . $e->getMessage());
        return [
            "total_paises" => 0,
            "total_visitas" => 0,
            "pais_mais_visitado" => null,
            "primeira_visita" => null,
            "ultima_visita" => null
        ];
    }
}
?>