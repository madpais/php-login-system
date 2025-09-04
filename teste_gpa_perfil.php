<?php
/**
 * Teste para verificar se o GPA está sendo salvo no perfil
 */

require_once 'config.php';
iniciarSessaoSegura();

echo "🧪 TESTE DE SALVAMENTO DE GPA NO PERFIL\n";
echo "=========================================\n\n";

// Simular um usuário logado (usar ID 1 como exemplo)
$usuario_teste = 1;

// Verificar se o usuário existe
try {
    $pdo = conectarBD();
    $stmt = $pdo->prepare("SELECT id, nome FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario_teste]);
    $usuario = $stmt->fetch();
    
    if (!$usuario) {
        echo "❌ Usuário de teste (ID: $usuario_teste) não encontrado\n";
        echo "Criando usuário de teste...\n";
        
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
        $stmt->execute(['Teste GPA', 'teste@gpa.com', password_hash('123456', PASSWORD_DEFAULT)]);
        $usuario_teste = $pdo->lastInsertId();
        echo "✅ Usuário de teste criado com ID: $usuario_teste\n";
    } else {
        echo "✅ Usuário de teste encontrado: {$usuario['nome']} (ID: $usuario_teste)\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro ao verificar usuário: " . $e->getMessage() . "\n";
    exit(1);
}

// Dados de teste
$gpa_teste = 3.75;
$notas_teste = [8.5, 9.0, 7.5, 8.0, 9.5];

echo "\n📊 Dados do teste:\n";
echo "GPA: $gpa_teste\n";
echo "Notas: " . implode(', ', $notas_teste) . "\n\n";

// Verificar estado antes do teste
echo "📋 ESTADO ANTES DO TESTE:\n";
echo "========================\n";

try {
    // Verificar GPA atual no perfil
    $stmt = $pdo->prepare("SELECT gpa FROM perfil_usuario WHERE usuario_id = ?");
    $stmt->execute([$usuario_teste]);
    $perfil_atual = $stmt->fetch();
    
    if ($perfil_atual) {
        echo "GPA atual no perfil: " . ($perfil_atual['gpa'] ?? 'NULL') . "\n";
    } else {
        echo "Perfil não existe para o usuário\n";
    }
    
    // Contar registros na tabela usuario_gpa
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuario_gpa WHERE usuario_id = ?");
    $stmt->execute([$usuario_teste]);
    $count_gpa = $stmt->fetchColumn();
    echo "Registros de GPA na tabela usuario_gpa: $count_gpa\n";
    
} catch (Exception $e) {
    echo "❌ Erro ao verificar estado atual: " . $e->getMessage() . "\n";
}

// Executar teste de salvamento
echo "\n🧪 EXECUTANDO TESTE DE SALVAMENTO:\n";
echo "==================================\n";

try {
    // Chamar a função salvarGPA
    if (function_exists('salvarGPA')) {
        $resultado = salvarGPA($usuario_teste, $gpa_teste, $notas_teste);
        
        if ($resultado) {
            echo "✅ Função salvarGPA executada com sucesso\n";
        } else {
            echo "❌ Função salvarGPA retornou false\n";
        }
    } else {
        echo "❌ Função salvarGPA não está disponível\n";
        exit(1);
    }
    
} catch (Exception $e) {
    echo "❌ Erro ao executar salvarGPA: " . $e->getMessage() . "\n";
    exit(1);
}

// Verificar estado após o teste
echo "\n📋 ESTADO APÓS O TESTE:\n";
echo "======================\n";

try {
    // Verificar se foi salvo na tabela usuario_gpa
    $stmt = $pdo->prepare("SELECT gpa_calculado, data_calculo FROM usuario_gpa WHERE usuario_id = ? ORDER BY data_calculo DESC LIMIT 1");
    $stmt->execute([$usuario_teste]);
    $ultimo_gpa = $stmt->fetch();
    
    if ($ultimo_gpa) {
        echo "✅ GPA salvo na tabela usuario_gpa: {$ultimo_gpa['gpa_calculado']}\n";
        echo "   Data: {$ultimo_gpa['data_calculo']}\n";
    } else {
        echo "❌ GPA não encontrado na tabela usuario_gpa\n";
    }
    
    // Verificar se foi salvo no perfil
    $stmt = $pdo->prepare("SELECT gpa FROM perfil_usuario WHERE usuario_id = ?");
    $stmt->execute([$usuario_teste]);
    $perfil_atualizado = $stmt->fetch();
    
    if ($perfil_atualizado && $perfil_atualizado['gpa'] !== null) {
        echo "✅ GPA salvo no perfil: {$perfil_atualizado['gpa']}\n";
        
        // Verificar se o valor está correto
        if (abs($perfil_atualizado['gpa'] - $gpa_teste) < 0.01) {
            echo "✅ Valor do GPA no perfil está correto\n";
        } else {
            echo "❌ Valor do GPA no perfil está incorreto\n";
            echo "   Esperado: $gpa_teste\n";
            echo "   Encontrado: {$perfil_atualizado['gpa']}\n";
        }
    } else {
        echo "❌ GPA não encontrado no perfil ou é NULL\n";
    }
    
    // Verificar badges conquistadas
    $stmt = $pdo->prepare("
        SELECT b.codigo, b.nome 
        FROM usuario_badges ub 
        JOIN badges b ON ub.badge_codigo = b.codigo 
        WHERE ub.usuario_id = ? AND b.codigo LIKE 'gpa_%'
    ");
    $stmt->execute([$usuario_teste]);
    $badges_gpa = $stmt->fetchAll();
    
    if (count($badges_gpa) > 0) {
        echo "✅ Badges de GPA conquistadas: " . count($badges_gpa) . "\n";
        foreach ($badges_gpa as $badge) {
            echo "   - {$badge['codigo']}: {$badge['nome']}\n";
        }
    } else {
        echo "⚠️ Nenhuma badge de GPA conquistada\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro ao verificar resultado: " . $e->getMessage() . "\n";
}

// Resumo do teste
echo "\n📋 RESUMO DO TESTE:\n";
echo "==================\n";

if ($resultado && $perfil_atualizado && $perfil_atualizado['gpa'] !== null) {
    echo "✅ TESTE PASSOU: GPA está sendo salvo corretamente no perfil\n";
    echo "   O problema foi corrigido com sucesso!\n";
} else {
    echo "❌ TESTE FALHOU: GPA ainda não está sendo salvo no perfil\n";
    echo "   Verifique os logs de erro para mais detalhes\n";
}

echo "\n✅ Teste concluído!\n";
?>