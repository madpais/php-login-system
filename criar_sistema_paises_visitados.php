<?php
/**
 * Script para criar sistema de países visitados
 */

require_once 'config.php';

echo "🌍 CRIANDO SISTEMA DE PAÍSES VISITADOS\n";
echo "======================================\n\n";

try {
    $pdo = conectarBD();
    
    // 1. Criar tabela de países visitados
    echo "📋 1. CRIANDO TABELA DE PAÍSES VISITADOS:\n";
    echo "=========================================\n";
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS paises_visitados (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            pais_codigo VARCHAR(50) NOT NULL,
            pais_nome VARCHAR(100) NOT NULL,
            data_primeira_visita TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            total_visitas INT DEFAULT 1,
            ultima_visita TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            -- Chaves e índices
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            UNIQUE KEY unique_usuario_pais (usuario_id, pais_codigo),
            INDEX idx_usuario (usuario_id),
            INDEX idx_pais (pais_codigo),
            INDEX idx_data_visita (data_primeira_visita)
        ) ENGINE=InnoDB
    ");
    echo "✅ Tabela paises_visitados criada!\n";
    
    // 2. Criar mapeamento de países
    echo "\n📋 2. CRIANDO MAPEAMENTO DE PAÍSES:\n";
    echo "===================================\n";
    
    $paises_mapeamento = [
        'australia' => 'Austrália',
        'belgica' => 'Bélgica', 
        'canada' => 'Canadá',
        'china' => 'China',
        'dinamarca' => 'Dinamarca',
        'finlandia' => 'Finlândia',
        'franca' => 'França',
        'alemanha' => 'Alemanha',
        'holanda' => 'Holanda',
        'hungria' => 'Hungria',
        'india' => 'Índia',
        'indonesia' => 'Indonésia',
        'irlanda' => 'Irlanda',
        'italia' => 'Itália',
        'japao' => 'Japão',
        'malasia' => 'Malásia',
        'noruega' => 'Noruega',
        'portugal' => 'Portugal',
        'arabia' => 'Arábia Saudita',
        'singapura' => 'Singapura',
        'africa' => 'África do Sul',
        'coreia' => 'Coreia do Sul',
        'espanha' => 'Espanha',
        'suecia' => 'Suécia',
        'suica' => 'Suíça',
        'emirados' => 'Emirados Árabes Unidos',
        'reinounido' => 'Reino Unido',
        'eua' => 'Estados Unidos'
    ];
    
    echo "✅ Mapeamento de " . count($paises_mapeamento) . " países definido!\n";
    
    // 3. Criar arquivo de tracking
    echo "\n📋 3. CRIANDO ARQUIVO DE TRACKING:\n";
    echo "==================================\n";
    
    $tracking_code = '<?php
/**
 * Sistema de Tracking de Países Visitados
 */

require_once "config.php";

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
            
            return [
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
            
            return [
                "primeira_visita" => true,
                "total_visitas" => 1,
                "pais_nome" => $pais_nome
            ];
        }
        
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
?>';
    
    if (file_put_contents('tracking_paises.php', $tracking_code)) {
        echo "✅ Arquivo tracking_paises.php criado!\n";
    }
    
    // 4. Criar arquivo para adicionar aos países
    echo "\n📋 4. CRIANDO ARQUIVO PARA PÁGINAS DE PAÍSES:\n";
    echo "=============================================\n";
    
    $pais_tracking = '<?php
// Adicionar no início de cada página de país
if (isset($_SESSION["usuario_id"])) {
    require_once "../tracking_paises.php";
    
    // Extrair nome do país do arquivo atual
    $arquivo_atual = basename($_SERVER["PHP_SELF"], ".php");
    
    // Registrar visita
    $resultado_visita = registrarVisitaPais($_SESSION["usuario_id"], $arquivo_atual);
    
    if ($resultado_visita && $resultado_visita["primeira_visita"]) {
        // Primeira visita - pode mostrar notificação especial
        $_SESSION["primeira_visita_pais"] = $resultado_visita["pais_nome"];
    }
}
?>';
    
    if (file_put_contents('codigo_tracking_pais.php', $pais_tracking)) {
        echo "✅ Código de tracking para países criado!\n";
    }
    
    // 5. Resumo
    echo "\n📊 RESUMO DO SISTEMA CRIADO:\n";
    echo "============================\n";
    echo "✅ Tabela paises_visitados criada\n";
    echo "✅ Sistema de tracking implementado\n";
    echo "✅ Funções de estatísticas criadas\n";
    echo "✅ Mapeamento de " . count($paises_mapeamento) . " países\n";
    
    echo "\n🔗 PRÓXIMOS PASSOS:\n";
    echo "===================\n";
    echo "1. Modificar pesquisa_por_pais.php para mostrar selos\n";
    echo "2. Adicionar tracking nas páginas de países\n";
    echo "3. Atualizar página de perfil com estatísticas\n";
    echo "4. Testar sistema completo\n";
    
    echo "\n🎉 SISTEMA DE PAÍSES VISITADOS CRIADO COM SUCESSO!\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>
