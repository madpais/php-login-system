<?php
/**
 * Verificar badges após completar teste
 * Incluir este código após salvar resultado do teste
 */

// Adicionar no final do arquivo que salva resultados de testes
function verificarBadgesAposTest($usuario_id) {
    require_once "sistema_badges.php";
    verificarBadgesProvas($usuario_id);
}

// Exemplo de uso:
// Após inserir em resultados_testes, chamar:
// verificarBadgesAposTest($usuario_id);
?>