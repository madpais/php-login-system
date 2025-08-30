<?php
/**
 * Verificar badges após participação no fórum
 * Incluir este código após criar tópico ou resposta
 */

// Adicionar no final do arquivo que salva tópicos/respostas
function verificarBadgesAposForum($usuario_id) {
    require_once "sistema_badges.php";
    verificarBadgesForum($usuario_id);
}

// Exemplo de uso:
// Após inserir em forum_topicos ou forum_respostas, chamar:
// verificarBadgesAposForum($usuario_id);
?>