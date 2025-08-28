<?php
/**
 * Adicionar selos manualmente para todos os países restantes
 */

echo "🏷️ ADICIONANDO SELOS MANUALMENTE PARA TODOS OS PAÍSES\n";
echo "======================================================\n\n";

// Países que ainda precisam de selos
$paises_sem_selo = [
    'belgica' => 'Bélgica',
    'china' => 'China',
    'dinamarca' => 'Dinamarca',
    'finlandia' => 'Finlândia',
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
    'reinounido' => 'Reino Unido'
];

$arquivo_pesquisa = 'pesquisa_por_pais.php';
$conteudo = file_get_contents($arquivo_pesquisa);

// Fazer backup
$backup = $arquivo_pesquisa . '.backup.manual.' . date('Y-m-d_H-i-s');
copy($arquivo_pesquisa, $backup);
echo "📄 Backup criado: $backup\n\n";

$selos_adicionados = 0;

// Função para gerar código do selo
function gerarCodigoSelo($codigo) {
    return "                    <?php if (\$usuario_logado && isset(\$paises_visitados['$codigo'])): ?>
                        <div class=\"selo-visitado\">
                            <i class=\"fas fa-check-circle\"></i>
                            Visitado
                        </div>
                        <?php if (\$paises_visitados['$codigo']['total_visitas'] > 1): ?>
                            <div class=\"contador-visitas\">
                                <?php echo \$paises_visitados['$codigo']['total_visitas']; ?>x
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>";
}

// Adicionar selos um por um
foreach ($paises_sem_selo as $codigo => $nome) {
    echo "🏷️ Adicionando selo para $nome ($codigo)...\n";
    
    // Verificar se já tem selo
    if (strpos($conteudo, "paises_visitados['$codigo']") !== false) {
        echo "  ✅ $nome já tem selo\n";
        continue;
    }
    
    // Buscar padrão específico para cada país
    $padroes = [
        // Padrão mais específico com onclick
        "/(onclick=\"[^\"]*" . preg_quote($codigo) . "\.php[^\"]*\">\s*\n\s*<div class=\"card-body\">)/",

        // Padrão com background image
        "/(style=\"background-image: url\('[^']*" . preg_quote($codigo) . "[^']*'\);\"[^>]*>\s*\n\s*<div class=\"card-body\">)/",

        // Padrão genérico
        "/(<div class=\"card country-card\"[^>]*>\s*\n\s*<div class=\"card-body\">\s*\n\s*<div class=\"country-title-container\">\s*\n\s*<img[^>]*>\s*\n\s*<h2>" . preg_quote($nome) . "<\/h2>)/s"
    ];
    
    $selo_adicionado = false;
    
    foreach ($padroes as $i => $padrao) {
        if (preg_match($padrao, $conteudo, $matches)) {
            $codigo_selo = gerarCodigoSelo($codigo);
            
            if ($i === 0) {
                // Para padrão com onclick
                $substituicao = str_replace('<div class="card-body">', $codigo_selo . "\n                    <div class=\"card-body\">", $matches[1]);
            } elseif ($i === 1) {
                // Para padrão com background
                $substituicao = str_replace('<div class="card-body">', $codigo_selo . "\n                    <div class=\"card-body\">", $matches[1]);
            } else {
                // Para padrão genérico
                $substituicao = $codigo_selo . "\n                    " . $matches[1];
            }
            
            $novo_conteudo = str_replace($matches[0], $substituicao, $conteudo);
            
            if ($novo_conteudo !== $conteudo) {
                $conteudo = $novo_conteudo;
                $selos_adicionados++;
                $selo_adicionado = true;
                echo "  ✅ Selo adicionado para $nome (padrão " . ($i + 1) . ")\n";
                break;
            }
        }
    }
    
    if (!$selo_adicionado) {
        echo "  ⚠️ Não foi possível adicionar selo para $nome\n";
    }
}

// Salvar arquivo
if ($selos_adicionados > 0) {
    if (file_put_contents($arquivo_pesquisa, $conteudo)) {
        echo "\n✅ Arquivo salvo com $selos_adicionados selos adicionados\n";
    } else {
        echo "\n❌ Erro ao salvar arquivo\n";
    }
} else {
    echo "\n⚠️ Nenhum selo foi adicionado\n";
}

// Verificação final
echo "\n📋 VERIFICAÇÃO FINAL:\n";
echo "=====================\n";

$conteudo_final = file_get_contents($arquivo_pesquisa);
$total_selos = 0;

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

foreach ($todos_paises as $codigo => $nome) {
    $tem_selo = strpos($conteudo_final, "paises_visitados['$codigo']") !== false;
    if ($tem_selo) $total_selos++;
    
    $status = $tem_selo ? '✅' : '❌';
    echo "$status $nome ($codigo)\n";
}

echo "\n🎉 RESULTADO:\n";
echo "=============\n";
echo "✅ Total de selos: $total_selos/" . count($todos_paises) . "\n";
echo "📊 Cobertura: " . round(($total_selos / count($todos_paises)) * 100, 1) . "%\n";

if ($total_selos === count($todos_paises)) {
    echo "\n🎉 PERFEITO! TODOS OS PAÍSES TÊM SELOS!\n";
} else {
    echo "\n⚠️ Alguns países ainda precisam de ajuste manual\n";
    
    // Mostrar países sem selo
    echo "\nPaíses sem selo:\n";
    foreach ($todos_paises as $codigo => $nome) {
        if (strpos($conteudo_final, "paises_visitados['$codigo']") === false) {
            echo "  - $nome ($codigo)\n";
        }
    }
}

echo "\n🧪 TESTE AGORA:\n";
echo "================\n";
echo "1. Acesse: http://localhost:8080/teste_multiplos_paises.php\n";
echo "2. Visite vários países diferentes\n";
echo "3. Vá para: http://localhost:8080/pesquisa_por_pais.php\n";
echo "4. Verifique se TODOS os países visitados têm selos\n";

?>
