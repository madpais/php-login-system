<?php
// Adicionar no início de cada página de país
if (isset($_SESSION["usuario_id"])) {
    require_once "../tracking_paises.php";
    
    // Extrair nome do país do arquivo atual
    $arquivo_atual = basename($_SERVER["PHP_SELF"], ".php");
    
    // Registrar visita
    $resultado_visita = registrarVisitaPais($_SESSION["usuario_id"], $arquivo_atual);
    
    if ($resultado_visita && $resultado_visita["primeira_visita"]) {
        // Primeira visita - pode mostrar notificação especial
        $_SESSION["primeira_visita_pais"] = $resultado_visita["pais_nome"];
    }
}
?>