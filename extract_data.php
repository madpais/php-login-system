<?php
/**
 * Script para extrair dados das tabelas existentes e gerar comandos INSERT
 */

// Configurações do banco de dados
$host = 'localhost';
$dbname = 'db_daydreamming_project'; // Atualizado para corresponder ao config.php
$username = 'root';
$password = '';

// Tabelas a serem extraídas
$tables = [
    'usuarios',
    'perfil_usuario',
    'badges',
    'niveis_usuario',
    'questoes',
    'sessoes_teste',
    'respostas_usuario',
    'resultados_testes',
    'configuracoes_sistema',
    'logs_acesso',
    'logs_sistema',
    'notificacoes',
    'notificacoes_usuario',
    'historico_atividades',
    'historico_experiencia',
    'paises_visitados',
    'usuario_badges',
    'forum_categorias',
    'forum_topicos',
    'forum_respostas',
    'forum_curtidas',
    'forum_moderacao'
];

echo "🔍 EXTRAINDO DADOS DAS TABELAS\n";
echo "================================\n\n";

try {
    // Conectar ao banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conectado ao banco de dados '$dbname'\n\n";
    
    $output = "";
    
    foreach ($tables as $table) {
        echo "📋 Extraindo dados da tabela '$table'...\n";
        
        // Verificar se a tabela existe
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() == 0) {
            echo "⚠️ Tabela '$table' não encontrada. Pulando...\n";
            continue;
        }
        
        // Obter estrutura da tabela
        $stmt = $pdo->query("DESCRIBE $table");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Obter dados da tabela
        $stmt = $pdo->query("SELECT * FROM $table");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($rows) > 0) {
            $output .= "\n    // Inserir dados na tabela $table\n";
            
            foreach ($rows as $row) {
                $columns_str = implode(", ", array_keys($row));
                $values = [];
                
                foreach ($row as $value) {
                    if ($value === null) {
                        $values[] = "NULL";
                    } elseif (is_numeric($value)) {
                        $values[] = $value;
                    } else {
                        $values[] = "'" . addslashes($value) . "'";
                    }
                }
                
                $values_str = implode(", ", $values);
                $output .= "    \$pdo->exec(\"INSERT INTO $table ($columns_str) VALUES ($values_str)\");\n";
            }
            
            echo "✅ Extraídos " . count($rows) . " registros da tabela '$table'\n";
        } else {
            echo "ℹ️ Nenhum dado encontrado na tabela '$table'\n";
        }
    }
    
    // Salvar os comandos INSERT em um arquivo
    $filename = 'insert_data.php';
    file_put_contents($filename, "<?php\n/**\n * Comandos INSERT gerados automaticamente\n * Data: " . date('Y-m-d H:i:s') . "\n */\n\n$output\n");
    
    echo "\n✅ Comandos INSERT gerados com sucesso no arquivo '$filename'\n";
    
} catch (PDOException $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}