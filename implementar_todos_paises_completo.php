<?php
/**
 * Implementar tracking e selos em TODOS os países
 */

echo "🌍 IMPLEMENTANDO SISTEMA COMPLETO EM TODOS OS PAÍSES\n";
echo "====================================================\n\n";

// Lista COMPLETA de todos os países
$todos_paises = [
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

echo "📋 TOTAL DE PAÍSES: " . count($todos_paises) . "\n\n";

// PARTE 1: Adicionar tracking em todas as páginas de países
echo "📋 1. ADICIONANDO TRACKING EM TODAS AS PÁGINAS:\n";
echo "===============================================\n";

$tracking_sucessos = 0;
$tracking_erros = 0;
$tracking_ja_existem = 0;

foreach ($todos_paises as $codigo => $nome) {
    echo "🔍 Processando $nome ($codigo)...\n";
    
    $arquivo = "paises/$codigo.php";
    
    if (!file_exists($arquivo)) {
        echo "  ❌ Arquivo não existe: $arquivo\n";
        $tracking_erros++;
        continue;
    }
    
    $conteudo = file_get_contents($arquivo);
    
    // Verificar se já tem tracking
    if (strpos($conteudo, 'tracking_paises.php') !== false) {
        echo "  ✅ $nome já tem tracking\n";
        $tracking_ja_existem++;
        continue;
    }
    
    // Procurar padrão para adicionar tracking
    $padrao = '/(\$usuario_nome = \$_SESSION\[\'usuario_nome\'\] \?\? \'\';)\s*\n\?\>/';
    
    $codigo_tracking = '$1

// Registrar visita ao país
require_once \'../tracking_paises.php\';
$resultado_visita = registrarVisitaPais($_SESSION[\'usuario_id\'], \'' . $codigo . '\');

// Verificar se é primeira visita para mostrar notificação
$primeira_visita = false;
if ($resultado_visita && $resultado_visita[\'primeira_visita\']) {
    $primeira_visita = true;
    $_SESSION[\'primeira_visita_pais\'] = $resultado_visita[\'pais_nome\'];
}
?>';
    
    $novo_conteudo = preg_replace($padrao, $codigo_tracking, $conteudo);
    
    if ($novo_conteudo !== $conteudo) {
        // Fazer backup
        $backup = $arquivo . '.backup.' . date('Y-m-d_H-i-s');
        copy($arquivo, $backup);
        
        if (file_put_contents($arquivo, $novo_conteudo)) {
            echo "  ✅ Tracking adicionado em $nome\n";
            $tracking_sucessos++;
        } else {
            echo "  ❌ Erro ao salvar $arquivo\n";
            $tracking_erros++;
        }
    } else {
        echo "  ⚠️ Não foi possível adicionar tracking automaticamente em $nome\n";
        $tracking_erros++;
    }
}

echo "\n📊 RESUMO DO TRACKING:\n";
echo "======================\n";
echo "✅ Sucessos: $tracking_sucessos\n";
echo "✅ Já existiam: $tracking_ja_existem\n";
echo "❌ Erros: $tracking_erros\n";
echo "📄 Total: " . count($todos_paises) . "\n";

// PARTE 2: Adicionar selos em TODOS os países na página de pesquisa
echo "\n📋 2. ADICIONANDO SELOS EM TODOS OS PAÍSES:\n";
echo "==========================================\n";

$arquivo_pesquisa = 'pesquisa_por_pais.php';
$conteudo_pesquisa = file_get_contents($arquivo_pesquisa);

// Fazer backup da página de pesquisa
$backup_pesquisa = $arquivo_pesquisa . '.backup.' . date('Y-m-d_H-i-s');
copy($arquivo_pesquisa, $backup_pesquisa);
echo "📄 Backup criado: $backup_pesquisa\n\n";

$selos_sucessos = 0;
$selos_ja_existem = 0;
$selos_erros = 0;

foreach ($todos_paises as $codigo => $nome) {
    echo "🏷️ Adicionando selo para $nome ($codigo)...\n";
    
    // Verificar se já tem selo
    if (strpos($conteudo_pesquisa, "paises_visitados['$codigo']") !== false) {
        echo "  ✅ $nome já tem selo\n";
        $selos_ja_existem++;
        continue;
    }
    
    // Buscar diferentes padrões de card
    $padroes = [
        // Padrão 1: Com comentário
        "/<!-- $nome -->\s*\n\s*<div class=\"col-lg-3 col-md-4 col-sm-6 col-12\">\s*\n\s*<div class=\"card country-card\"([^>]*?)>\s*\n\s*<div class=\"card-body\">/s",
        
        // Padrão 2: Sem comentário, mas com o nome do país
        "/<div class=\"col-lg-3 col-md-4 col-sm-6 col-12\">\s*\n\s*<div class=\"card country-card\"([^>]*?)>\s*\n\s*<div class=\"card-body\">\s*\n\s*<div class=\"country-title-container\">\s*\n\s*<img[^>]*>\s*\n\s*<h2>$nome<\/h2>/s"
    ];
    
    $selo_adicionado = false;
    
    foreach ($padroes as $i => $padrao) {
        if (preg_match($padrao, $conteudo_pesquisa)) {
            $selo_codigo = "<!-- $nome -->
            <div class=\"col-lg-3 col-md-4 col-sm-6 col-12\">
                <div class=\"card country-card\"$1>
                    <?php if (\$usuario_logado && isset(\$paises_visitados['$codigo'])): ?>
                        <div class=\"selo-visitado\">
                            <i class=\"fas fa-check-circle\"></i>
                            Visitado
                        </div>
                        <?php if (\$paises_visitados['$codigo']['total_visitas'] > 1): ?>
                            <div class=\"contador-visitas\">
                                <?php echo \$paises_visitados['$codigo']['total_visitas']; ?>x
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <div class=\"card-body\">";
            
            if ($i === 1) {
                // Para o padrão 2, incluir o resto do conteúdo
                $selo_codigo .= "
                        <div class=\"country-title-container\">
                            <img";
            }
            
            $novo_conteudo_pesquisa = preg_replace($padrao, $selo_codigo, $conteudo_pesquisa, 1);
            
            if ($novo_conteudo_pesquisa !== $conteudo_pesquisa) {
                $conteudo_pesquisa = $novo_conteudo_pesquisa;
                $selos_sucessos++;
                $selo_adicionado = true;
                echo "  ✅ Selo adicionado para $nome (padrão " . ($i + 1) . ")\n";
                break;
            }
        }
    }
    
    if (!$selo_adicionado) {
        echo "  ⚠️ Não foi possível adicionar selo para $nome automaticamente\n";
        $selos_erros++;
    }
}

// Salvar arquivo de pesquisa
if ($selos_sucessos > 0) {
    if (file_put_contents($arquivo_pesquisa, $conteudo_pesquisa)) {
        echo "\n✅ Arquivo de pesquisa salvo com $selos_sucessos selos adicionados\n";
    } else {
        echo "\n❌ Erro ao salvar arquivo de pesquisa\n";
    }
}

echo "\n📊 RESUMO DOS SELOS:\n";
echo "====================\n";
echo "✅ Sucessos: $selos_sucessos\n";
echo "✅ Já existiam: $selos_ja_existem\n";
echo "❌ Erros: $selos_erros\n";
echo "📄 Total: " . count($todos_paises) . "\n";

// PARTE 3: Verificação final
echo "\n📋 3. VERIFICAÇÃO FINAL:\n";
echo "========================\n";

$conteudo_final = file_get_contents($arquivo_pesquisa);
$total_selos_final = 0;
$total_tracking_final = 0;

foreach ($todos_paises as $codigo => $nome) {
    $tem_selo = strpos($conteudo_final, "paises_visitados['$codigo']") !== false;
    $arquivo_pais = "paises/$codigo.php";
    $tem_tracking = false;
    
    if (file_exists($arquivo_pais)) {
        $conteudo_pais = file_get_contents($arquivo_pais);
        $tem_tracking = strpos($conteudo_pais, 'tracking_paises.php') !== false;
    }
    
    if ($tem_selo) $total_selos_final++;
    if ($tem_tracking) $total_tracking_final++;
    
    $status_selo = $tem_selo ? '✅' : '❌';
    $status_tracking = $tem_tracking ? '✅' : '❌';
    
    echo "$status_tracking $status_selo $nome ($codigo)\n";
}

echo "\n🎉 RESULTADO FINAL:\n";
echo "===================\n";
echo "✅ Países com tracking: $total_tracking_final/" . count($todos_paises) . "\n";
echo "✅ Países com selos: $total_selos_final/" . count($todos_paises) . "\n";

$percentual_tracking = round(($total_tracking_final / count($todos_paises)) * 100, 1);
$percentual_selos = round(($total_selos_final / count($todos_paises)) * 100, 1);

echo "📊 Cobertura de tracking: $percentual_tracking%\n";
echo "📊 Cobertura de selos: $percentual_selos%\n";

if ($total_tracking_final === count($todos_paises) && $total_selos_final === count($todos_paises)) {
    echo "\n🎉 PERFEITO! TODOS OS PAÍSES IMPLEMENTADOS!\n";
} else {
    echo "\n⚠️ Alguns países ainda precisam de ajustes manuais\n";
}

echo "\n🧪 TESTE AGORA:\n";
echo "================\n";
echo "1. Acesse: http://localhost:8080/teste_multiplos_paises.php\n";
echo "2. Teste vários países diferentes\n";
echo "3. Verifique: http://localhost:8080/pesquisa_por_pais.php\n";
echo "4. Confirme se todos os países visitados têm selos\n";

echo "\n📄 ARQUIVOS MODIFICADOS:\n";
echo "========================\n";
echo "📄 pesquisa_por_pais.php - Selos adicionados\n";
echo "📄 Backup: $backup_pesquisa\n";
foreach ($todos_paises as $codigo => $nome) {
    $arquivo = "paises/$codigo.php";
    if (file_exists($arquivo . '.backup.' . date('Y-m-d'))) {
        echo "📄 $arquivo - Tracking adicionado\n";
    }
}

?>
