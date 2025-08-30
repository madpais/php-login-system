<?php
/**
 * Script para verificar badges e suas imagens
 */

require_once 'config.php';
require_once 'sistema_badges.php';

echo "🖼️ VERIFICANDO BADGES E IMAGENS\n";
echo "===============================\n\n";

try {
    $pdo = conectarBD();
    
    // Buscar todas as badges
    $stmt = $pdo->query("SELECT codigo, nome, descricao FROM badges ORDER BY codigo");
    $badges = $stmt->fetchAll();
    
    echo "📋 BADGES NO BANCO DE DADOS:\n";
    echo "============================\n";
    
    foreach ($badges as $badge) {
        $imagem_esperada = 'imagens/badge_' . $badge['codigo'] . '.jpg';
        $existe = file_exists($imagem_esperada);

        echo "🏅 {$badge['nome']}\n";
        echo "   Código: {$badge['codigo']}\n";
        echo "   Imagem: {$imagem_esperada}\n";
        echo "   Existe: " . ($existe ? "✅ SIM" : "❌ NÃO") . "\n";
        echo "\n";
    }
    
    // Buscar um usuário para teste
    $stmt = $pdo->query("SELECT id FROM usuarios LIMIT 1");
    $usuario = $stmt->fetch();
    
    if ($usuario) {
        $usuario_id = $usuario['id'];
        echo "\n👤 BADGES DO USUÁRIO ID: $usuario_id\n";
        echo "==================================\n";
        
        $badges_usuario = obterBadgesUsuario($usuario_id);
        
        if (empty($badges_usuario)) {
            echo "❌ Nenhuma badge conquistada ainda\n";
        } else {
            foreach ($badges_usuario as $badge) {
                $imagem_esperada = 'imagens/badge_' . $badge['codigo'] . '.jpg';
                $existe = file_exists($imagem_esperada);

                echo "🏅 {$badge['nome']}\n";
                echo "   Código: {$badge['codigo']}\n";
                echo "   Imagem: {$imagem_esperada}\n";
                echo "   Existe: " . ($existe ? "✅ SIM" : "❌ NÃO") . "\n";
                echo "   Data: {$badge['data_conquista']}\n";
                echo "\n";
            }
        }
    }
    
    echo "\n📁 VERIFICANDO PASTA DE IMAGENS:\n";
    echo "=================================\n";
    
    $pasta_imagens = 'imagens/';
    if (is_dir($pasta_imagens)) {
        $arquivos = scandir($pasta_imagens);
        $badges_encontradas = [];
        
        foreach ($arquivos as $arquivo) {
            if (preg_match('/^badge_.*\.jpg$/', $arquivo)) {
                $badges_encontradas[] = $arquivo;
            }
        }
        
        if (empty($badges_encontradas)) {
            echo "❌ Nenhuma imagem de badge encontrada na pasta imagens/\n";
        } else {
            echo "✅ Imagens de badges encontradas:\n";
            foreach ($badges_encontradas as $imagem) {
                echo "   - $imagem\n";
            }
        }
    } else {
        echo "❌ Pasta 'imagens/' não encontrada\n";
    }
    
    echo "\n💡 DICAS:\n";
    echo "=========\n";
    echo "1. As imagens devem estar na pasta 'imagens/'\n";
    echo "2. O nome deve seguir o padrão: {codigo_da_badge}.jpg\n";
    echo "3. Exemplo: forum_bronze → badge_forum_bronze.jpg\n";
    echo "4. Se a imagem não existir, será usado fallback\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>
