<?php
/**
 * Script para incluir o footer.php como require_once em todas as páginas PHP do projeto
 * Este script irá modificar automaticamente todos os arquivos PHP para incluir o footer
 */

// Diretório raiz do projeto
$dir_raiz = __DIR__;

// Função para processar arquivos recursivamente
function processarArquivos($diretorio) {
    global $contador;
    $arquivos = scandir($diretorio);
    
    foreach ($arquivos as $arquivo) {
        // Ignorar diretórios especiais
        if ($arquivo === '.' || $arquivo === '..' || $arquivo === '.git' || $arquivo === '.github') {
            continue;
        }
        
        $caminho_completo = $diretorio . DIRECTORY_SEPARATOR . $arquivo;
        
        // Se for um diretório, processar recursivamente
        if (is_dir($caminho_completo)) {
            processarArquivos($caminho_completo);
        } 
        // Se for um arquivo PHP (exceto o próprio footer.php e este script)
        elseif (pathinfo($caminho_completo, PATHINFO_EXTENSION) === 'php' && 
                $arquivo !== 'footer.php' && 
                $arquivo !== basename(__FILE__)) {
            
            // Ler o conteúdo do arquivo
            $conteudo = file_get_contents($caminho_completo);
            
            // Verificar se o arquivo já inclui o footer
            if (strpos($conteudo, 'require_once') !== false && 
                strpos($conteudo, 'footer.php') !== false) {
                echo "Arquivo já inclui o footer: $caminho_completo\n";
                continue;
            }
            
            // Substituir a tag de fechamento </body> por require_once seguido da tag
            $novo_conteudo = str_replace(
                '</body>', 
                "<?php require_once __DIR__ . '/footer.php'; ?>\n</body>", 
                $conteudo
            );
            
            // Se houve alteração no conteúdo
            if ($conteudo !== $novo_conteudo) {
                // Salvar o arquivo modificado
                file_put_contents($caminho_completo, $novo_conteudo);
                $contador++;
                echo "Arquivo modificado: $caminho_completo\n";
            } else {
                echo "Arquivo sem tag </body> ou já modificado: $caminho_completo\n";
            }
        }
    }
}

// Contador de arquivos modificados
$contador = 0;

// Iniciar o processamento
echo "Iniciando a inclusão do footer.php em todos os arquivos PHP...\n";
processarArquivos($dir_raiz);
echo "\nProcesso concluído! $contador arquivos foram modificados.\n";
echo "O footer.php foi incluído como require_once em todos os arquivos PHP do projeto.\n";
?>