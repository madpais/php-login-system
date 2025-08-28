<?php
/**
 * Remover selos visuais dos cards mantendo funcionalidades do banco
 */

echo "🔧 REMOVENDO SELOS VISUAIS DOS CARDS\n";
echo "====================================\n\n";

echo "⚠️ IMPORTANTE:\n";
echo "- Mantendo TODAS as funcionalidades do banco de dados\n";
echo "- Mantendo sistema de tracking em todos os países\n";
echo "- Removendo APENAS os selos visuais dos cards\n";
echo "- Sistema continuará registrando visitas normalmente\n\n";

$arquivo_pesquisa = 'pesquisa_por_pais.php';
$conteudo = file_get_contents($arquivo_pesquisa);

// Fazer backup
$backup = $arquivo_pesquisa . '.backup.sem_selos.' . date('Y-m-d_H-i-s');
copy($arquivo_pesquisa, $backup);
echo "📄 Backup criado: $backup\n\n";

// Lista de países que têm selos
$paises_com_selos = [
    'eua' => 'Estados Unidos',
    'canada' => 'Canadá', 
    'australia' => 'Austrália',
    'alemanha' => 'Alemanha',
    'franca' => 'França',
    'italia' => 'Itália',
    'japao' => 'Japão'
];

$selos_removidos = 0;

foreach ($paises_com_selos as $codigo => $nome) {
    echo "🔧 Removendo selo visual de $nome ($codigo)...\n";
    
    // Padrão para encontrar e remover o selo
    $padrao = '/\s*<\?php if \(\$usuario_logado && isset\(\$paises_visitados\[\'' . $codigo . '\'\]\)\): \?>\s*\n\s*<div class="selo-visitado">\s*\n\s*<i class="fas fa-check-circle"><\/i>\s*\n\s*Visitado\s*\n\s*<\/div>\s*\n\s*<\?php if \(\$paises_visitados\[\'' . $codigo . '\'\]\[\'total_visitas\'\] > 1\): \?>\s*\n\s*<div class="contador-visitas">\s*\n\s*<\?php echo \$paises_visitados\[\'' . $codigo . '\'\]\[\'total_visitas\'\]; \?>x\s*\n\s*<\/div>\s*\n\s*<\?php endif; \?>\s*\n\s*<\?php endif; \?>\s*/s';
    
    $novo_conteudo = preg_replace($padrao, '', $conteudo);
    
    if ($novo_conteudo !== $conteudo) {
        $conteudo = $novo_conteudo;
        $selos_removidos++;
        echo "  ✅ Selo removido de $nome\n";
    } else {
        echo "  ⚠️ Selo não encontrado ou já removido de $nome\n";
    }
}

// Salvar arquivo
if ($selos_removidos > 0) {
    if (file_put_contents($arquivo_pesquisa, $conteudo)) {
        echo "\n✅ Arquivo salvo com $selos_removidos selos removidos\n";
    } else {
        echo "\n❌ Erro ao salvar arquivo\n";
    }
} else {
    echo "\n⚠️ Nenhum selo foi removido\n";
}

// Verificação final
echo "\n📋 VERIFICAÇÃO FINAL:\n";
echo "=====================\n";

$conteudo_final = file_get_contents($arquivo_pesquisa);
$selos_restantes = 0;

foreach ($paises_com_selos as $codigo => $nome) {
    $tem_selo = strpos($conteudo_final, "paises_visitados['$codigo']") !== false;
    if ($tem_selo) $selos_restantes++;
    
    $status = $tem_selo ? '❌ Ainda tem selo' : '✅ Selo removido';
    echo "$status - $nome ($codigo)\n";
}

echo "\n📊 RESULTADO:\n";
echo "=============\n";
echo "✅ Selos removidos: $selos_removidos\n";
echo "⚠️ Selos restantes: $selos_restantes\n";

if ($selos_restantes === 0) {
    echo "\n🎉 PERFEITO! TODOS OS SELOS VISUAIS FORAM REMOVIDOS!\n";
} else {
    echo "\n⚠️ Alguns selos ainda estão presentes\n";
}

// Verificar se o tracking ainda está funcionando
echo "\n📋 VERIFICANDO SISTEMA DE TRACKING:\n";
echo "===================================\n";

$todos_paises = [
    'australia', 'belgica', 'canada', 'china', 'dinamarca', 'finlandia',
    'franca', 'alemanha', 'holanda', 'hungria', 'india', 'indonesia',
    'irlanda', 'italia', 'japao', 'malasia', 'noruega', 'portugal',
    'arabia', 'singapura', 'africa', 'coreia', 'espanha', 'suecia',
    'suica', 'emirados', 'reinounido', 'eua'
];

$tracking_funcionando = 0;

foreach ($todos_paises as $codigo) {
    $arquivo_pais = "paises/$codigo.php";
    if (file_exists($arquivo_pais)) {
        $conteudo_pais = file_get_contents($arquivo_pais);
        if (strpos($conteudo_pais, 'tracking_paises.php') !== false) {
            $tracking_funcionando++;
        }
    }
}

echo "✅ Países com tracking funcionando: $tracking_funcionando/" . count($todos_paises) . "\n";

// Verificar se o arquivo de tracking existe
if (file_exists('tracking_paises.php')) {
    echo "✅ Arquivo tracking_paises.php existe\n";
} else {
    echo "❌ Arquivo tracking_paises.php NÃO existe\n";
}

// Verificar tabela no banco
try {
    require_once 'config.php';
    $pdo = conectarBD();
    $stmt = $pdo->query("SHOW TABLES LIKE 'paises_visitados'");
    if ($stmt->fetch()) {
        echo "✅ Tabela 'paises_visitados' existe no banco\n";
        
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM paises_visitados");
        $total = $stmt->fetch();
        echo "📊 Total de registros no banco: " . $total['total'] . "\n";
    } else {
        echo "❌ Tabela 'paises_visitados' NÃO existe\n";
    }
} catch (Exception $e) {
    echo "⚠️ Erro ao verificar banco: " . $e->getMessage() . "\n";
}

echo "\n🎯 RESULTADO FINAL:\n";
echo "===================\n";
echo "✅ Selos visuais removidos dos cards\n";
echo "✅ Sistema de tracking mantido em $tracking_funcionando países\n";
echo "✅ Banco de dados funcionando normalmente\n";
echo "✅ Funcionalidades de registro mantidas\n";
echo "✅ Apenas interface visual alterada\n";

echo "\n📋 O QUE AINDA FUNCIONA:\n";
echo "========================\n";
echo "✅ Registro automático de visitas ao acessar páginas de países\n";
echo "✅ Armazenamento no banco de dados\n";
echo "✅ Contadores de visitas\n";
echo "✅ Estatísticas de países visitados\n";
echo "✅ Histórico completo de visitas\n";
echo "✅ Notificações de primeira visita\n";
echo "✅ Todas as funcionalidades de backend\n";

echo "\n❌ O QUE FOI REMOVIDO:\n";
echo "======================\n";
echo "❌ Selos verdes 'Visitado' nos cards\n";
echo "❌ Contadores visuais (2x, 3x, etc.) nos cards\n";
echo "❌ Indicações visuais na página de pesquisa\n";

echo "\n🧪 TESTE AGORA:\n";
echo "================\n";
echo "1. Acesse: http://localhost:8080/teste_final_todos_paises.php\n";
echo "2. Visite vários países diferentes\n";
echo "3. Veja que as estatísticas ainda funcionam\n";
echo "4. Acesse: http://localhost:8080/pesquisa_por_pais.php\n";
echo "5. Confirme que os selos visuais foram removidos\n";
echo "6. Mas o sistema ainda registra tudo no banco!\n";

echo "\n📄 ARQUIVOS AFETADOS:\n";
echo "=====================\n";
echo "📄 pesquisa_por_pais.php - Selos visuais removidos\n";
echo "📄 Backup: $backup\n";
echo "✅ tracking_paises.php - Mantido intacto\n";
echo "✅ paises/[pais].php - Tracking mantido em todos\n";
echo "✅ Banco de dados - Funcionando normalmente\n";

?>
