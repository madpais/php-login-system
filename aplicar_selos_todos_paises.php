<?php
/**
 * Script para aplicar selos de visitado a todos os países na pesquisa_por_pais.php
 */

echo "🏷️ APLICANDO SELOS DE VISITADO A TODOS OS PAÍSES\n";
echo "================================================\n\n";

// Lista de países e seus códigos
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

// Ler o arquivo atual
$arquivo = 'pesquisa_por_pais.php';
$conteudo = file_get_contents($arquivo);

if (!$conteudo) {
    echo "❌ Erro ao ler o arquivo $arquivo\n";
    exit;
}

echo "📋 Processando " . count($paises) . " países...\n\n";

$modificacoes = 0;

foreach ($paises as $codigo => $nome) {
    echo "🔍 Processando $nome ($codigo)...\n";
    
    // Padrão para encontrar o card do país (já processamos a Austrália)
    if ($codigo === 'australia') {
        echo "  ✅ Austrália já processada\n";
        continue;
    }
    
    // Padrão para encontrar o início do card
    $padrao_inicio = "/<!-- $nome -->\s*\n\s*<div class=\"col-lg-3 col-md-4 col-sm-6 col-12\">\s*\n\s*<div class=\"card country-card\"[^>]*>\s*\n/";
    
    // Verificar se o país já tem selo
    if (strpos($conteudo, "paises_visitados['$codigo']") !== false) {
        echo "  ✅ $nome já tem selo de visitado\n";
        continue;
    }
    
    // Buscar o padrão específico para cada país
    $padrao_card = "/<!-- $nome -->\s*\n\s*<div class=\"col-lg-3 col-md-4 col-sm-6 col-12\">\s*\n\s*<div class=\"card country-card\"([^>]*?)>\s*\n\s*<div class=\"card-body\">/s";
    
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
    
    $substituicao = "<!-- $nome -->
            <div class=\"col-lg-3 col-md-4 col-sm-6 col-12\">
                <div class=\"card country-card\"$1>
$selo_codigo";
    
    $novo_conteudo = preg_replace($padrao_card, $substituicao, $conteudo);
    
    if ($novo_conteudo !== $conteudo) {
        $conteudo = $novo_conteudo;
        $modificacoes++;
        echo "  ✅ Selo adicionado para $nome\n";
    } else {
        echo "  ⚠️ Não foi possível adicionar selo para $nome\n";
    }
}

// Salvar o arquivo modificado
if ($modificacoes > 0) {
    if (file_put_contents($arquivo, $conteudo)) {
        echo "\n✅ Arquivo salvo com $modificacoes modificações!\n";
    } else {
        echo "\n❌ Erro ao salvar o arquivo!\n";
    }
} else {
    echo "\n⚠️ Nenhuma modificação foi necessária.\n";
}

echo "\n📊 RESUMO:\n";
echo "==========\n";
echo "✅ Países processados: " . count($paises) . "\n";
echo "✅ Modificações feitas: $modificacoes\n";
echo "✅ Sistema de selos implementado\n";

echo "\n🔗 PRÓXIMO PASSO:\n";
echo "==================\n";
echo "Adicionar tracking nas páginas individuais de países\n";

?>
