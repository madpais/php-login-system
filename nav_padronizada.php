<?php
// Componente de navegação padronizada para todas as páginas
// Verificar se o usuário está logado
$usuario_logado = isset($_SESSION['usuario_id']);
?>
<!-- Menu de navegação principal -->
<div class="container-fluid">
    <div class="row justify-content-center">
        <!-- Botão Países -->
        <div class="col-lg col-md col-sm-6 col-6 exam-card nav-button"
             style="background: linear-gradient(135deg, #2a9df4 0%, #187bcd 100%); min-height: 100px; border: 3px solid white; padding: 15px; cursor: pointer; transition: all 0.3s ease; margin: 0 2px;"
             onclick="location.href='pesquisa_por_pais.php'">
            <div class="d-flex flex-column justify-content-center h-100 text-center">
                <i class="fas fa-globe mb-2" style="color: white; font-size: 24px;"></i>
                <p class="text1 mb-0" style="color: white; font-weight: 600; font-size: 16px;">Países</p>
                <small style="color: rgba(255,255,255,0.8);">Explore oportunidades</small>
            </div>
        </div>

        <!-- Botão Simulador Prático -->
        <div class="col-lg col-md col-sm-6 col-6 exam-card nav-button"
             style="background: linear-gradient(135deg, #2a9df4 0%, #187bcd 100%); min-height: 100px; border: 3px solid white; padding: 15px; cursor: pointer; transition: all 0.3s ease; margin: 0 2px;"
             onclick="<?php echo $usuario_logado ? "location.href='simulador_provas.php'" : "location.href='login.php'"; ?>">
            <div class="d-flex flex-column justify-content-center h-100 text-center">
                <i class="fas fa-graduation-cap mb-2" style="color: white; font-size: 24px;"></i>
                <p class="text1 mb-0" style="color: white; font-weight: 600; font-size: 16px;">Simulador Prático</p>
                <small style="color: rgba(255,255,255,0.8);">Pratique para os exames</small>
            </div>
        </div>

        <!-- Botão Teste Vocacional -->
        <div class="col-lg col-md col-sm-6 col-6 exam-card nav-button"
             style="background: linear-gradient(135deg, #2a9df4 0%, #187bcd 100%); min-height: 100px; border: 3px solid white; padding: 15px; cursor: pointer; transition: all 0.3s ease; margin: 0 2px;"
             onclick="location.href='teste_vocacional.php'">
            <div class="d-flex flex-column justify-content-center h-100 text-center">
                <i class="fas fa-compass mb-2" style="color: white; font-size: 24px;"></i>
                <p class="text1 mb-0" style="color: white; font-weight: 600; font-size: 16px;">Teste Vocacional</p>
                <small style="color: rgba(255,255,255,0.8);">Descubra seu curso</small>
            </div>
        </div>

        <!-- Botão Comunidade -->
        <div class="col-lg col-md col-sm-6 col-6 exam-card nav-button"
             style="background: linear-gradient(135deg, #2a9df4 0%, #187bcd 100%); min-height: 100px; border: 3px solid white; padding: 15px; cursor: pointer; transition: all 0.3s ease; margin: 0 2px;"
             onclick="scrollToSection('comunidade')">
            <div class="d-flex flex-column justify-content-center h-100 text-center">
                <i class="fas fa-comments mb-2" style="color: white; font-size: 24px;"></i>
                <p class="text1 mb-0" style="color: white; font-weight: 600; font-size: 16px;">Comunidade</p>
                <small style="color: rgba(255,255,255,0.8);">Conecte-se com outros</small>
            </div>
        </div>

        <!-- Botão Quem Somos -->
        <div class="col-lg col-md col-sm-6 col-6 exam-card nav-button"
             style="background: linear-gradient(135deg, #2a9df4 0%, #187bcd 100%); min-height: 100px; border: 3px solid white; padding: 15px; cursor: pointer; transition: all 0.3s ease; margin: 0 2px;"
             onclick="location.href='quem_somos.php'">
            <div class="d-flex flex-column justify-content-center h-100 text-center">
                <i class="fas fa-users mb-2" style="color: white; font-size: 24px;"></i>
                <p class="text1 mb-0" style="color: white; font-weight: 600; font-size: 16px;">Quem Somos</p>
                <small style="color: rgba(255,255,255,0.8);">Nossa missão</small>
            </div>
        </div>
    </div>
</div>

<!-- Espaçamento entre navegação e banner -->
<div class="nav-banner-spacing"></div>

<style>
.nav-banner-spacing {
    height: 60px;
    background: linear-gradient(to bottom, rgba(42, 157, 244, 0.05) 0%, transparent 100%);       
}

.navbutton {
    color: white;
    font-size: clamp(12px, 2vw, 18px);
    text-align: center;
    width: 100%;
    font-weight: 600;
    margin: 0;
    transition: all 0.3s ease;
}

.nav-item-container {
    background: linear-gradient(135deg, #2a9df4 0%, #187bcd 100%);
    min-height: 80px;
    border: 3px solid white;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 15px 5px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.nav-item-container:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(42, 157, 244, 0.3);
}

.nav-item-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.nav-item-container:hover::before {
    left: 100%;
}

@media (max-width: 768px) {
    .nav-banner-spacing {
        height: 40px;
    }
}
</style>

<script>
// Função para scroll suave para seções
function scrollToSection(sectionId) {
    const element = document.getElementById(sectionId);
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    } else {
        // Se não encontrar a seção na página atual, redirecionar para index
        if (window.location.pathname !== '/index.php' && window.location.pathname !== '/') {
            window.location.href = 'index.php#' + sectionId;
        }
    }
}
</script>
