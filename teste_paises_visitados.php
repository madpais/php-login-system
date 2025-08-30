<?php
require_once 'config.php';
require_once 'tracking_paises.php';

echo "🧪 TESTE DE PAÍSES VISITADOS\n";
echo "============================\n\n";

try {
    $pdo = conectarBD();
    
    // Verificar se a tabela existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'paises_visitados'");
    if ($stmt->rowCount() == 0) {
        echo "❌ Tabela 'paises_visitados' não existe\n";
        exit;
    }
    
    // Contar registros
    $stmt = $pdo->query("SELECT COUNT(*) FROM paises_visitados");
    $total = $stmt->fetchColumn();
    echo "📊 Total de registros na tabela: $total\n\n";
    
    // Verificar quais usuários têm países visitados
    $stmt = $pdo->query("SELECT DISTINCT usuario_id FROM paises_visitados");
    $usuarios_com_paises = $stmt->fetchAll();
    echo "👥 Usuários com países visitados: ";
    foreach ($usuarios_com_paises as $u) {
        echo $u['usuario_id'] . " ";
    }
    echo "\n\n";

    // Testar com usuário ID 1 (admin)
    $usuario_id = 1;
    echo "👤 Testando com usuário ID: $usuario_id\n";

    $paises = obterPaisesVisitados($usuario_id);
    echo "🌍 Países visitados: " . count($paises) . "\n";

    if (!empty($paises)) {
        foreach ($paises as $codigo => $dados) {
            echo "  • {$dados['pais_nome']} ({$dados['total_visitas']}x)\n";
        }
    } else {
        echo "  Nenhum país visitado ainda.\n";

        // Vamos registrar uma visita de teste
        echo "\n🧪 Registrando visita de teste ao Brasil...\n";
        $resultado = registrarVisitaPais($usuario_id, 'brasil');
        if ($resultado) {
            echo "✅ Visita registrada com sucesso!\n";

            // Testar novamente
            $paises = obterPaisesVisitados($usuario_id);
            echo "🌍 Países visitados após teste: " . count($paises) . "\n";
            foreach ($paises as $codigo => $dados) {
                echo "  • {$dados['pais_nome']} ({$dados['total_visitas']}x)\n";
            }
        } else {
            echo "❌ Erro ao registrar visita\n";
        }
    }

    echo "\n✅ Teste concluído!\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>
