<?php
// Componente de header para mostrar status de login
require_once 'config.php';

// Iniciar sessão de forma segura
iniciarSessaoSegura();

// Headers anti-cache
if (!headers_sent()) {
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
}

// Verificar se o usuário está logado
$usuario_logado = isset($_SESSION['usuario_id']);
$usuario_nome = '';
$usuario_login = '';
$is_admin = false;

if ($usuario_logado) {
    // SEMPRE buscar dados atualizados do banco para evitar inconsistências
    try {
        $pdo = conectarBD();
        $stmt = $pdo->prepare("SELECT nome, usuario, is_admin, ativo FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['usuario_id']]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user_data && $user_data['ativo']) {
            // Usar dados do banco (sempre atualizados)
            $usuario_nome = $user_data['nome'];
            $usuario_login = $user_data['usuario'];
            $is_admin = (bool)$user_data['is_admin'];

            // Atualizar sessão com dados corretos do banco
            $_SESSION['usuario_nome'] = $usuario_nome;
            $_SESSION['usuario_login'] = $usuario_login;
            $_SESSION['is_admin'] = $is_admin;
        } else {
            // Usuário não encontrado ou inativo - limpar sessão
            $_SESSION = array();
            $usuario_logado = false;
        }
    } catch (Exception $e) {
        // Em caso de erro, usar dados da sessão como fallback
        $usuario_nome = $_SESSION['usuario_nome'] ?? '';
        $usuario_login = $_SESSION['usuario_login'] ?? '';
        $is_admin = $_SESSION['is_admin'] ?? false;
    }
}
?>

<div id="login-status-header" style="
    background: linear-gradient(135deg, #187bcb 0%, #6c5ce7 100%);
    color: white;
    padding: 10px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    position: sticky;
    top: 0;
    z-index: 1000;
">
    <div style="display: flex; align-items: center; gap: 15px;">
        <?php if ($usuario_logado): ?>
            <span style="font-weight: 500;">✅ Você está logado</span>

            <!-- Dropdown do usuário -->
            <div style="position: relative; display: inline-block;">
                <button onclick="toggleUserDropdown()" style="
                    background: rgba(255,255,255,0.2);
                    border: none;
                    color: white;
                    padding: 8px 16px;
                    border-radius: 20px;
                    font-size: 14px;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    transition: all 0.3s ease;
                " onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                    👤 <?php echo htmlspecialchars($usuario_nome); ?>
                    <span style="font-size: 12px;">▼</span>
                </button>

                <div id="userDropdown" style="
                    display: none;
                    position: absolute;
                    top: 100%;
                    right: 0;
                    background: white;
                    min-width: 200px;
                    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
                    border-radius: 10px;
                    z-index: 1000;
                    margin-top: 5px;
                    overflow: hidden;
                ">
                    <a href="pagina_usuario.php" style="
                        display: block;
                        padding: 12px 16px;
                        color: #333;
                        text-decoration: none;
                        border-bottom: 1px solid #eee;
                        transition: background 0.2s ease;
                    " onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='white'">
                        <i class="fas fa-user" style="margin-right: 8px; color: #4CAF50;"></i>
                        Meu Perfil
                    </a>

                    <a href="editar_perfil.php" style="
                        display: block;
                        padding: 12px 16px;
                        color: #333;
                        text-decoration: none;
                        border-bottom: 1px solid #eee;
                        transition: background 0.2s ease;
                    " onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='white'">
                        <i class="fas fa-edit" style="margin-right: 8px; color: #2196F3;"></i>
                        Editar Perfil
                    </a>



                    <a href="todas_notificacoes.php" style="
                        display: block;
                        padding: 12px 16px;
                        color: #333;
                        text-decoration: none;
                        border-bottom: 1px solid #eee;
                        transition: background 0.2s ease;
                    " onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='white'">
                        <i class="fas fa-bell" style="margin-right: 8px; color: #9C27B0;"></i>
                        Notificações
                    </a>

                    <a href="logout.php" style="
                        display: block;
                        padding: 12px 16px;
                        color: #dc3545;
                        text-decoration: none;
                        transition: background 0.2s ease;
                    " onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='white'">
                        <i class="fas fa-sign-out-alt" style="margin-right: 8px;"></i>
                        Sair
                    </a>
                </div>
            </div>
        <?php else: ?>
            <span style="font-weight: 500;">❌ Você não está logado</span>
        <?php endif; ?>

        <a href="index.php" style="
            background: rgba(255,255,255,0.15);
            color: white;
            text-decoration: none;
            padding: 6px 14px;
            border-radius: 18px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 1px solid rgba(255,255,255,0.25);
        " onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
            🏠 Página Inicial
        </a>


    </div>
    
    <div style="display: flex; align-items: center; gap: 10px;">
        <?php if ($usuario_logado): ?>
            <?php include 'componente_notificacoes.php'; ?>
            <a href="logout.php" style="
                background: rgba(255,255,255,0.2);
                color: white;
                text-decoration: none;
                padding: 8px 16px;
                border-radius: 20px;
                font-size: 14px;
                font-weight: 500;
                transition: all 0.3s ease;
                border: 1px solid rgba(255,255,255,0.3);
            " onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                🚪 Deslogar
            </a>
        <?php else: ?>
            <a href="login.php" style="
                background: rgba(255,255,255,0.2);
                color: white;
                text-decoration: none;
                padding: 8px 16px;
                border-radius: 20px;
                font-size: 14px;
                font-weight: 500;
                transition: all 0.3s ease;
                border: 1px solid rgba(255,255,255,0.3);
            " onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                🔑 Fazer Login
            </a>
        <?php endif; ?>
    </div>
</div>

<style>
#login-status-header a:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

@media (max-width: 768px) {
    #login-status-header {
        flex-direction: column;
        gap: 12px;
        text-align: center;
        padding: 15px 10px;
        font-size: 14px;
    }
    
    #login-status-header > div {
        flex-direction: column;
        gap: 10px;
        width: 100%;
    }

    #login-status-header > div:first-child {
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    #login-status-header span {
        font-size: 13px;
        line-height: 1.4;
        word-wrap: break-word;
    }
    
    #login-status-header a {
        font-size: 13px !important;
        padding: 10px 20px !important;
        width: fit-content;
        margin: 0 auto;
        display: block;
    }
}

@media (max-width: 480px) {
    #login-status-header {
        padding: 12px 8px;
        font-size: 13px;
    }
    
    #login-status-header span {
        font-size: 12px;
    }
    
    #login-status-header a {
        font-size: 12px !important;
        padding: 8px 16px !important;
    }
}
</style>

<!-- JavaScript para o dropdown do usuário -->
<script>
function toggleUserDropdown() {
    const dropdown = document.getElementById('userDropdown');
    if (!dropdown) return;

    const isVisible = dropdown.style.display === 'block';

    // Fechar todos os dropdowns primeiro
    document.querySelectorAll('[id$="Dropdown"]').forEach(d => {
        if (d !== dropdown) d.style.display = 'none';
    });

    dropdown.style.display = isVisible ? 'none' : 'block';
}

// Fechar dropdown quando clicar fora
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('userDropdown');
    if (!dropdown) return;

    const button = event.target.closest('button');

    if (!dropdown.contains(event.target) && (!button || !button.onclick || button.onclick.toString().indexOf('toggleUserDropdown') === -1)) {
        dropdown.style.display = 'none';
    }
});

// Fechar dropdown ao pressionar ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const dropdown = document.getElementById('userDropdown');
        if (dropdown) dropdown.style.display = 'none';
    }
});
</script>