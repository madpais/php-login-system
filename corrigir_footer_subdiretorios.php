<?php
/**
 * Script para corrigir o caminho do footer.php em arquivos em subdiretórios
 * Este script irá modificar os arquivos em subdiretórios para usar o caminho correto do footer.php
 */

// Diretório raiz do projeto
$dir_raiz = __DIR__;

// Função para processar arquivos recursivamente
function processarArquivos($diretorio, $nivel = 0) {
    global $contador;
    $arquivos = scandir($diretorio);
    
    foreach ($arquivos as $arquivo) {
        // Ignorar diretórios especiais
        if ($arquivo === '.' || $arquivo === '..' || $arquivo === '.git' || $arquivo === '.github') {
            continue;
        }
        
        $caminho_completo = $diretorio . DIRECTORY_SEPARATOR . $arquivo;
        
        // Se for um diretório, processar recursivamente com nível incrementado
        if (is_dir($caminho_completo)) {
            processarArquivos($caminho_completo, $nivel + 1);
        } 
        // Se for um arquivo PHP em um subdiretório
        elseif ($nivel > 0 && pathinfo($caminho_completo, PATHINFO_EXTENSION) === 'php') {
            
            // Ler o conteúdo do arquivo
            $conteudo = file_get_contents($caminho_completo);
            
            // Verificar se o arquivo inclui o footer com caminho incorreto
            if (strpos($conteudo, "require_once __DIR__ . '/footer.php'") !== false) {
                // Construir o caminho correto com base no nível do diretório
                $caminho_correto = str_repeat('../', $nivel) . 'footer.php';
                
                // Substituir o caminho incorreto pelo correto
                $novo_conteudo = str_replace(
                    "require_once __DIR__ . '/footer.php'", 
                    "require_once __DIR__ . '/$caminho_correto'", 
                    $conteudo
                );
                
                // Se houve alteração no conteúdo
                if ($conteudo !== $novo_conteudo) {
                    // Salvar o arquivo modificado
                    file_put_contents($caminho_completo, $novo_conteudo);
                    $contador++;
                    echo "Arquivo corrigido: $caminho_completo\n";
                }
            }
        }
    }
}

// Contador de arquivos modificados
$contador = 0;

// Iniciar o processamento
echo "Iniciando a correção do caminho do footer.php em arquivos em subdiretórios...\n";
processarArquivos($dir_raiz);
echo "\nProcesso concluído! $contador arquivos foram corrigidos.\n";
echo "O caminho do footer.php foi corrigido em todos os arquivos em subdiretórios.\n";
?>