<?php
/**
 * Verificar tracking em todos os países
 */

echo "🔍 VERIFICANDO TRACKING EM TODOS OS PAÍSES\n";
echo "==========================================\n\n";

// Lista de todos os países
$paises = [
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

echo "📋 1. VERIFICANDO ARQUIVOS DE PAÍSES:\n";
echo "=====================================\n";

$paises_com_tracking = [];
$paises_sem_tracking = [];
$paises_inexistentes = [];

foreach ($paises as $codigo => $nome) {
    $arquivo = "paises/$codigo.php";
    
    if (file_exists($arquivo)) {
        $conteudo = file_get_contents($arquivo);
        
        if (strpos($conteudo, 'tracking_paises.php') !== false && 
            strpos($conteudo, 'registrarVisitaPais') !== false) {
            $paises_com_tracking[] = "$nome ($codigo)";
            echo "✅ $nome ($codigo) - Tracking implementado\n";
        } else {
            $paises_sem_tracking[] = "$nome ($codigo)";
            echo "❌ $nome ($codigo) - SEM tracking\n";
        }
    } else {
        $paises_inexistentes[] = "$nome ($codigo)";
        echo "⚠️ $nome ($codigo) - Arquivo não existe\n";
    }
}

echo "\n📋 2. VERIFICANDO SELOS NA PESQUISA:\n";
echo "====================================\n";

$arquivo_pesquisa = 'pesquisa_por_pais.php';
$conteudo_pesquisa = file_get_contents($arquivo_pesquisa);

$paises_com_selo = [];
$paises_sem_selo = [];

foreach ($paises as $codigo => $nome) {
    if (strpos($conteudo_pesquisa, "paises_visitados['$codigo']") !== false) {
        $paises_com_selo[] = "$nome ($codigo)";
        echo "✅ $nome ($codigo) - Selo implementado\n";
    } else {
        $paises_sem_selo[] = "$nome ($codigo)";
        echo "❌ $nome ($codigo) - SEM selo\n";
    }
}

echo "\n📊 RESUMO:\n";
echo "==========\n";
echo "✅ Países com tracking: " . count($paises_com_tracking) . "/" . count($paises) . "\n";
echo "✅ Países com selo: " . count($paises_com_selo) . "/" . count($paises) . "\n";
echo "❌ Países sem tracking: " . count($paises_sem_tracking) . "\n";
echo "❌ Países sem selo: " . count($paises_sem_selo) . "\n";
echo "⚠️ Arquivos inexistentes: " . count($paises_inexistentes) . "\n";

if (!empty($paises_com_tracking)) {
    echo "\n✅ PAÍSES COM TRACKING:\n";
    foreach ($paises_com_tracking as $pais) {
        echo "  - $pais\n";
    }
}

if (!empty($paises_sem_tracking)) {
    echo "\n❌ PAÍSES SEM TRACKING:\n";
    foreach ($paises_sem_tracking as $pais) {
        echo "  - $pais\n";
    }
}

if (!empty($paises_com_selo)) {
    echo "\n✅ PAÍSES COM SELO:\n";
    foreach ($paises_com_selo as $pais) {
        echo "  - $pais\n";
    }
}

if (!empty($paises_sem_selo)) {
    echo "\n❌ PAÍSES SEM SELO:\n";
    foreach ($paises_sem_selo as $pais) {
        echo "  - $pais\n";
    }
}

echo "\n🔧 PRÓXIMOS PASSOS:\n";
echo "===================\n";
echo "1. Adicionar tracking nos países sem tracking\n";
echo "2. Adicionar selos nos países sem selo\n";
echo "3. Testar países principais (EUA, Canadá, Austrália)\n";

?>
