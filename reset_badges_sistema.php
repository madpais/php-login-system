<?php
/**
 * Script para resetar completamente o sistema de badges
 * Remove todos os dados e recria as tabelas do zero
 */

require_once 'config.php';

echo "🔄 RESET COMPLETO DO SISTEMA DE BADGES\n";
echo "======================================\n\n";

echo "⚠️ ATENÇÃO: Este script irá:\n";
echo "- Remover TODAS as badges existentes\n";
echo "- Remover TODAS as badges de usuários\n";
echo "- Recriar as tabelas do zero\n";
echo "- Inserir badges padrão\n\n";

// Confirmar ação
echo "Digite 'CONFIRMAR' para continuar: ";
$handle = fopen("php://stdin", "r");
$confirmacao = trim(fgets($handle));
fclose($handle);

if ($confirmacao !== 'CONFIRMAR') {
    echo "❌ Operação cancelada.\n";
    exit(0);
}

try {
    $pdo = conectarBD();
    
    echo "\n🗑️ REMOVENDO DADOS EXISTENTES...\n";
    echo "================================\n";
    
    // Desabilitar verificação de chaves estrangeiras temporariamente
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    // Remover tabelas se existirem
    echo "🗑️ Removendo tabela usuario_badges...\n";
    $pdo->exec("DROP TABLE IF EXISTS usuario_badges");
    echo "✅ Tabela usuario_badges removida\n";
    
    echo "🗑️ Removendo tabela badges...\n";
    $pdo->exec("DROP TABLE IF EXISTS badges");
    echo "✅ Tabela badges removida\n";
    
    // Reabilitar verificação de chaves estrangeiras
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "\n🏗️ CRIANDO TABELAS DO ZERO...\n";
    echo "=============================\n";
    
    // Criar tabela badges
    echo "🏆 Criando tabela badges...\n";
    $pdo->exec("
        CREATE TABLE badges (
            id INT AUTO_INCREMENT PRIMARY KEY,
            codigo VARCHAR(50) NOT NULL UNIQUE,
            nome VARCHAR(100) NOT NULL,
            descricao TEXT NOT NULL,
            icone VARCHAR(10) NOT NULL,
            tipo ENUM('pontuacao', 'frequencia', 'especial', 'tempo', 'social') NOT NULL,
            categoria ENUM('teste', 'forum', 'geral', 'social', 'gpa', 'paises') DEFAULT 'teste',
            condicao_valor INT NULL,
            raridade ENUM('comum', 'raro', 'epico', 'lendario') DEFAULT 'comum',
            experiencia_bonus INT DEFAULT 50,
            ativa TINYINT(1) DEFAULT 1,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_codigo (codigo),
            INDEX idx_ativa (ativa),
            INDEX idx_tipo (tipo)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Tabela badges criada com sucesso!\n";
    
    // Criar tabela usuario_badges
    echo "🎖️ Criando tabela usuario_badges...\n";
    $pdo->exec("
        CREATE TABLE usuario_badges (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            badge_id INT NOT NULL,
            data_conquista DATETIME NOT NULL,
            contexto VARCHAR(100) NULL,
            notificado TINYINT(1) DEFAULT 0,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_usuario_badge (usuario_id, badge_id),
            INDEX idx_usuario (usuario_id),
            INDEX idx_badge (badge_id),
            INDEX idx_data_conquista (data_conquista),
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Tabela usuario_badges criada com sucesso!\n";
    
    echo "\n📊 VERIFICANDO ESTRUTURA...\n";
    echo "===========================\n";
    
    // Verificar estrutura da tabela badges
    $stmt = $pdo->query("DESCRIBE badges");
    $colunas = $stmt->fetchAll();
    echo "📋 Colunas da tabela badges:\n";
    foreach ($colunas as $coluna) {
        echo "   - {$coluna['Field']}: {$coluna['Type']}\n";
    }
    
    // Verificar estrutura da tabela usuario_badges
    $stmt = $pdo->query("DESCRIBE usuario_badges");
    $colunas = $stmt->fetchAll();
    echo "\n📋 Colunas da tabela usuario_badges:\n";
    foreach ($colunas as $coluna) {
        echo "   - {$coluna['Field']}: {$coluna['Type']}\n";
    }
    
    echo "\n✅ RESET COMPLETO REALIZADO COM SUCESSO!\n";
    echo "========================================\n";
    echo "📋 Próximos passos:\n";
    echo "1. Execute: php inserir_badges_completo.php\n";
    echo "2. Execute: php verificar_badges_funcionais.php\n";
    echo "3. Execute: php instalar_completo_novo.php\n";
    
} catch (Exception $e) {
    echo "❌ Erro durante reset: " . $e->getMessage() . "\n";
    echo "📋 Detalhes do erro:\n";
    echo "   Arquivo: " . $e->getFile() . "\n";
    echo "   Linha: " . $e->getLine() . "\n";
}

echo "\n🎯 RESET CONCLUÍDO!\n";
?>
