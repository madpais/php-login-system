<?php
/**
 * Adicionar tracking nos países mais importantes
 */

echo "🔧 ADICIONANDO TRACKING NOS PAÍSES IMPORTANTES\n";
echo "==============================================\n\n";

// Países mais importantes para adicionar tracking
$paises_importantes = [
    'alemanha' => 'Alemanha',
    'franca' => 'França',
    'italia' => 'Itália',
    'espanha' => 'Espanha',
    'reinounido' => 'Reino Unido',
    'japao' => 'Japão',
    'coreia' => 'Coreia do Sul',
    'china' => 'China',
    'singapura' => 'Singapura'
];

$sucessos = 0;
$erros = 0;

foreach ($paises_importantes as $codigo => $nome) {
    echo "🔍 Processando $nome ($codigo)...\n";
    
    $arquivo = "paises/$codigo.php";
    
    if (!file_exists($arquivo)) {
        echo "  ❌ Arquivo não existe: $arquivo\n";
        $erros++;
        continue;
    }
    
    $conteudo = file_get_contents($arquivo);
    
    // Verificar se já tem tracking
    if (strpos($conteudo, 'tracking_paises.php') !== false) {
        echo "  ✅ $nome já tem tracking\n";
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
            echo "  📄 Backup: $backup\n";
            $sucessos++;
        } else {
            echo "  ❌ Erro ao salvar $arquivo\n";
            $erros++;
        }
    } else {
        echo "  ⚠️ Não foi possível adicionar tracking automaticamente em $nome\n";
        $erros++;
    }
}

echo "\n📊 RESUMO:\n";
echo "==========\n";
echo "✅ Sucessos: $sucessos\n";
echo "❌ Erros: $erros\n";
echo "📄 Total processado: " . count($paises_importantes) . "\n";

// Agora adicionar selos na página de pesquisa
echo "\n🏷️ ADICIONANDO SELOS NA PÁGINA DE PESQUISA:\n";
echo "============================================\n";

$arquivo_pesquisa = 'pesquisa_por_pais.php';
$conteudo_pesquisa = file_get_contents($arquivo_pesquisa);

$selos_adicionados = 0;

foreach ($paises_importantes as $codigo => $nome) {
    echo "🔍 Adicionando selo para $nome ($codigo)...\n";
    
    // Verificar se já tem selo
    if (strpos($conteudo_pesquisa, "paises_visitados['$codigo']") !== false) {
        echo "  ✅ $nome já tem selo\n";
        continue;
    }
    
    // Buscar padrão do card
    $padrao_card = "/<!-- $nome -->\s*\n\s*<div class=\"col-lg-3 col-md-4 col-sm-6 col-12\">\s*\n\s*<div class=\"card country-card\"([^>]*?)>\s*\n\s*<div class=\"card-body\">/s";
    
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
    
    $novo_conteudo_pesquisa = preg_replace($padrao_card, $selo_codigo, $conteudo_pesquisa);
    
    if ($novo_conteudo_pesquisa !== $conteudo_pesquisa) {
        $conteudo_pesquisa = $novo_conteudo_pesquisa;
        $selos_adicionados++;
        echo "  ✅ Selo adicionado para $nome\n";
    } else {
        echo "  ⚠️ Não foi possível adicionar selo para $nome\n";
    }
}

// Salvar arquivo de pesquisa
if ($selos_adicionados > 0) {
    $backup_pesquisa = $arquivo_pesquisa . '.backup.' . date('Y-m-d_H-i-s');
    copy($arquivo_pesquisa, $backup_pesquisa);
    
    if (file_put_contents($arquivo_pesquisa, $conteudo_pesquisa)) {
        echo "\n✅ Arquivo de pesquisa salvo com $selos_adicionados selos adicionados\n";
        echo "📄 Backup: $backup_pesquisa\n";
    } else {
        echo "\n❌ Erro ao salvar arquivo de pesquisa\n";
    }
} else {
    echo "\n⚠️ Nenhum selo foi adicionado\n";
}

echo "\n🎉 PROCESSO CONCLUÍDO!\n";
echo "======================\n";
echo "✅ Tracking adicionado em $sucessos países\n";
echo "✅ Selos adicionados: $selos_adicionados\n";

echo "\n🧪 TESTE AGORA:\n";
echo "================\n";
echo "1. Acesse: http://localhost:8080/teste_final_marcacao.php\n";
echo "2. Teste os países: EUA, Canadá, Austrália, Alemanha, França\n";
echo "3. Verifique se os selos aparecem na pesquisa\n";

?>
