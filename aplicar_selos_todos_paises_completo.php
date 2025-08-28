<?php
/**
 * Script para aplicar selos de visitado a TODOS os países
 */

echo "🏷️ APLICANDO SELOS A TODOS OS PAÍSES\n";
echo "====================================\n\n";

// Lista completa de países
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

// Ler arquivo
$arquivo = 'pesquisa_por_pais.php';
$conteudo = file_get_contents($arquivo);

if (!$conteudo) {
    echo "❌ Erro ao ler arquivo\n";
    exit;
}

echo "📋 Processando " . count($paises) . " países...\n\n";

$modificacoes = 0;

foreach ($paises as $codigo => $nome) {
    echo "🔍 Processando $nome ($codigo)...\n";
    
    // Verificar se já tem selo
    if (strpos($conteudo, "paises_visitados['$codigo']") !== false) {
        echo "  ✅ $nome já tem selo\n";
        continue;
    }
    
    // Buscar padrão específico para cada país
    $patterns = [
        // Padrão 1: Comentário + div + card
        "/<!-- $nome -->\s*\n\s*<div class=\"col-lg-3 col-md-4 col-sm-6 col-12\">\s*\n\s*<div class=\"card country-card\"([^>]*?)>\s*\n\s*<div class=\"card-body\">/s",
        
        // Padrão 2: Sem comentário
        "/<div class=\"col-lg-3 col-md-4 col-sm-6 col-12\">\s*\n\s*<div class=\"card country-card\"([^>]*?)>\s*\n\s*<div class=\"card-body\">/s"
    ];
    
    $selo_codigo = "                    <?php if (\$usuario_logado && isset(\$paises_visitados['$codigo'])): ?>
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
    
    $encontrado = false;
    
    // Tentar diferentes padrões
    foreach ($patterns as $i => $pattern) {
        if (preg_match($pattern, $conteudo)) {
            $substituicao = "<div class=\"col-lg-3 col-md-4 col-sm-6 col-12\">
                <div class=\"card country-card\"$1>
$selo_codigo";
            
            $novo_conteudo = preg_replace($pattern, $substituicao, $conteudo, 1);
            
            if ($novo_conteudo !== $conteudo) {
                $conteudo = $novo_conteudo;
                $modificacoes++;
                $encontrado = true;
                echo "  ✅ Selo adicionado para $nome (padrão " . ($i + 1) . ")\n";
                break;
            }
        }
    }
    
    if (!$encontrado) {
        // Tentar busca mais específica
        $busca_especifica = "/<h2>$nome<\/h2>/";
        if (preg_match($busca_especifica, $conteudo)) {
            echo "  ⚠️ Encontrado $nome mas não foi possível adicionar selo automaticamente\n";
        } else {
            echo "  ❌ $nome não encontrado no arquivo\n";
        }
    }
}

// Salvar arquivo
if ($modificacoes > 0) {
    // Fazer backup primeiro
    $backup = $arquivo . '.backup.' . date('Y-m-d_H-i-s');
    if (copy($arquivo, $backup)) {
        echo "\n✅ Backup criado: $backup\n";
    }
    
    if (file_put_contents($arquivo, $conteudo)) {
        echo "✅ Arquivo salvo com $modificacoes modificações!\n";
    } else {
        echo "❌ Erro ao salvar arquivo!\n";
    }
} else {
    echo "\n⚠️ Nenhuma modificação foi necessária.\n";
}

echo "\n📊 RESUMO:\n";
echo "==========\n";
echo "✅ Países processados: " . count($paises) . "\n";
echo "✅ Modificações feitas: $modificacoes\n";

// Verificar resultado
echo "\n🔍 VERIFICANDO RESULTADO:\n";
echo "=========================\n";

$conteudo_final = file_get_contents($arquivo);
$selos_encontrados = 0;

foreach ($paises as $codigo => $nome) {
    if (strpos($conteudo_final, "paises_visitados['$codigo']") !== false) {
        $selos_encontrados++;
    }
}

echo "✅ Selos encontrados no arquivo: $selos_encontrados/" . count($paises) . "\n";

if ($selos_encontrados === count($paises)) {
    echo "🎉 TODOS OS PAÍSES TÊM SELOS!\n";
} else {
    echo "⚠️ Alguns países ainda não têm selos\n";
    
    echo "\nPaíses sem selo:\n";
    foreach ($paises as $codigo => $nome) {
        if (strpos($conteudo_final, "paises_visitados['$codigo']") === false) {
            echo "  - $nome ($codigo)\n";
        }
    }
}

echo "\n🔗 TESTE AGORA:\n";
echo "================\n";
echo "1. Acesse: http://localhost:8080/debug_selos_pesquisa.php\n";
echo "2. Clique em 'Visitar EUA'\n";
echo "3. Volte e veja se aparece o selo\n";
echo "4. Teste: http://localhost:8080/pesquisa_por_pais.php\n";

?>
