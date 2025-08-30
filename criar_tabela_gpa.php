<?php
/**
 * Script para criar tabela de GPA dos usuários
 */

require_once 'config.php';

try {
    $pdo = conectarBD();
    
    echo "📊 CRIANDO TABELA DE GPA\n";
    echo "========================\n\n";
    
    // Criar tabela usuario_gpa
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS usuario_gpa (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            gpa_calculado DECIMAL(3,2) NOT NULL,
            notas_utilizadas TEXT NOT NULL, -- JSON com as notas usadas no cálculo
            data_calculo DATETIME NOT NULL,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            INDEX idx_usuario (usuario_id),
            INDEX idx_gpa (gpa_calculado),
            INDEX idx_data_calculo (data_calculo)
        ) ENGINE=InnoDB
    ");
    
    echo "✅ Tabela 'usuario_gpa' criada com sucesso!\n\n";
    
    echo "📋 ESTRUTURA DA TABELA:\n";
    echo "=======================\n";
    echo "- id: Chave primária\n";
    echo "- usuario_id: ID do usuário (FK)\n";
    echo "- gpa_calculado: GPA calculado (0.00 a 4.00)\n";
    echo "- notas_utilizadas: JSON com as notas usadas\n";
    echo "- data_calculo: Data/hora do cálculo\n";
    echo "- data_criacao: Timestamp de criação\n\n";
    
    echo "🎉 TABELA DE GPA CRIADA COM SUCESSO!\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>
