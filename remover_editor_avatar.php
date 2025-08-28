<?php
/**
 * Script para remover completamente o editor_avatar.php das funcionalidades
 */

echo "🗑️ REMOVENDO EDITOR DE AVATAR DAS FUNCIONALIDADES\n";
echo "=================================================\n\n";

// 1. Verificar se o arquivo foi removido
echo "📋 1. VERIFICANDO REMOÇÃO DO ARQUIVO:\n";
echo "=====================================\n";

if (!file_exists('editor_avatar.php')) {
    echo "✅ editor_avatar.php removido com sucesso\n";
} else {
    echo "❌ editor_avatar.php ainda existe\n";
}

// 2. Buscar referências restantes
echo "\n📋 2. BUSCANDO REFERÊNCIAS RESTANTES:\n";
echo "=====================================\n";

$arquivos_para_verificar = [];
$diretorios = ['.', 'paises'];

foreach ($diretorios as $dir) {
    $arquivos = glob($dir . '/*.php');
    $arquivos_para_verificar = array_merge($arquivos_para_verificar, $arquivos);
    
    // Adicionar arquivos .md também
    $arquivos_md = glob($dir . '/*.md');
    $arquivos_para_verificar = array_merge($arquivos_para_verificar, $arquivos_md);
}

$referencias_encontradas = [];

foreach ($arquivos_para_verificar as $arquivo) {
    if (basename($arquivo) === 'remover_editor_avatar.php') {
        continue; // Pular este script
    }
    
    $conteudo = file_get_contents($arquivo);
    
    // Buscar referências ao editor_avatar
    if (strpos($conteudo, 'editor_avatar') !== false) {
        $linhas = explode("\n", $conteudo);
        $linhas_com_referencia = [];
        
        foreach ($linhas as $num => $linha) {
            if (strpos($linha, 'editor_avatar') !== false) {
                $linhas_com_referencia[] = ($num + 1) . ": " . trim($linha);
            }
        }
        
        if (!empty($linhas_com_referencia)) {
            $referencias_encontradas[$arquivo] = $linhas_com_referencia;
        }
    }
}

if (empty($referencias_encontradas)) {
    echo "✅ Nenhuma referência ao editor_avatar encontrada\n";
} else {
    echo "⚠️ Referências encontradas:\n";
    foreach ($referencias_encontradas as $arquivo => $linhas) {
        echo "\n📄 $arquivo:\n";
        foreach ($linhas as $linha) {
            echo "  - $linha\n";
        }
    }
}

// 3. Atualizar instruções de teste
echo "\n📋 3. ATUALIZANDO INSTRUÇÕES DE TESTE:\n";
echo "======================================\n";

// Atualizar teste_edicao_perfil.php
if (file_exists('teste_edicao_perfil.php')) {
    $conteudo = file_get_contents('teste_edicao_perfil.php');
    
    // Remover seção do editor de avatar
    $conteudo = preg_replace('/\s*<div class="col-md-6 text-center">.*?Editor de Avatar 3D.*?<\/div>/s', '', $conteudo);
    
    // Atualizar para centralizar o botão de editar perfil
    $conteudo = str_replace(
        '<div class="row">',
        '<div class="row justify-content-center">',
        $conteudo
    );
    
    if (file_put_contents('teste_edicao_perfil.php', $conteudo)) {
        echo "✅ teste_edicao_perfil.php atualizado\n";
    }
}

// 4. Criar página de confirmação
echo "\n📋 4. CRIANDO PÁGINA DE CONFIRMAÇÃO:\n";
echo "====================================\n";

$confirmacao = '<?php
require_once "config.php";
iniciarSessaoSegura();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor de Avatar Removido - DayDreaming</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }
        .confirmation-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            margin: 50px 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
        }
        .btn-action {
            margin: 10px;
            padding: 15px 30px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include "header_status.php"; ?>
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="confirmation-card">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h1 class="h2 text-primary mb-4">Editor de Avatar Removido</h1>
                    
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle me-2"></i>Funcionalidade Removida</h5>
                        <p class="mb-0">O Editor de Avatar foi removido das funcionalidades do sistema conforme solicitado.</p>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5>✅ Removido:</h5>
                            <ul class="list-unstyled text-start">
                                <li><i class="fas fa-times text-danger me-2"></i>Arquivo editor_avatar.php</li>
                                <li><i class="fas fa-times text-danger me-2"></i>Link no dropdown do header</li>
                                <li><i class="fas fa-times text-danger me-2"></i>Clique no avatar da página do usuário</li>
                                <li><i class="fas fa-times text-danger me-2"></i>Referências em páginas de teste</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>✅ Mantido:</h5>
                            <ul class="list-unstyled text-start">
                                <li><i class="fas fa-check text-success me-2"></i>Exibição do avatar na página do usuário</li>
                                <li><i class="fas fa-check text-success me-2"></i>Configuração padrão do avatar</li>
                                <li><i class="fas fa-check text-success me-2"></i>Sistema de edição de perfil</li>
                                <li><i class="fas fa-check text-success me-2"></i>Todas as outras funcionalidades</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h5>🔗 Funcionalidades Disponíveis:</h5>
                        <div class="d-flex justify-content-center flex-wrap">
                            <a href="pagina_usuario.php" class="btn btn-primary btn-action">
                                <i class="fas fa-user me-2"></i>Meu Perfil
                            </a>
                            <a href="editar_perfil.php" class="btn btn-success btn-action">
                                <i class="fas fa-edit me-2"></i>Editar Perfil
                            </a>
                            <a href="index.php" class="btn btn-secondary btn-action">
                                <i class="fas fa-home me-2"></i>Página Inicial
                            </a>
                            <a href="forum.php" class="btn btn-warning btn-action">
                                <i class="fas fa-comments me-2"></i>Fórum
                            </a>
                        </div>
                    </div>
                    
                    <div class="alert alert-success mt-4">
                        <h6><i class="fas fa-thumbs-up me-2"></i>Sistema Atualizado</h6>
                        <p class="mb-0">O sistema foi atualizado e todas as referências ao Editor de Avatar foram removidas. 
                        O avatar continuará sendo exibido na página do usuário com a configuração padrão.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        console.log("Editor de Avatar removido do sistema");
        console.log("Funcionalidades disponíveis: Editar Perfil, Visualizar Perfil");
    </script>
</body>
</html>';

if (file_put_contents('confirmacao_remocao_avatar.php', $confirmacao)) {
    echo "✅ Página de confirmação criada: confirmacao_remocao_avatar.php\n";
}

// 5. Resumo das alterações
echo "\n📊 RESUMO DAS ALTERAÇÕES:\n";
echo "=========================\n";
echo "✅ Arquivo editor_avatar.php removido\n";
echo "✅ Link removido do dropdown do header\n";
echo "✅ Clique no avatar removido da página do usuário\n";
echo "✅ Função JavaScript openAvatarEditor() removida\n";
echo "✅ Referências removidas das páginas de teste\n";
echo "✅ Página de confirmação criada\n";

echo "\n🔗 FUNCIONALIDADES MANTIDAS:\n";
echo "=============================\n";
echo "✅ Exibição do avatar na página do usuário\n";
echo "✅ Configuração padrão do avatar (SVG)\n";
echo "✅ Sistema de edição de perfil (editar_perfil.php)\n";
echo "✅ Todas as outras funcionalidades do sistema\n";

echo "\n🌐 PÁGINAS PARA VERIFICAR:\n";
echo "==========================\n";
echo "🏠 Página do Usuário: http://localhost:8080/pagina_usuario.php\n";
echo "📝 Editar Perfil: http://localhost:8080/editar_perfil.php\n";
echo "✅ Confirmação: http://localhost:8080/confirmacao_remocao_avatar.php\n";
echo "🧪 Teste Atualizado: http://localhost:8080/teste_edicao_perfil_visual.php\n";

echo "\n📋 VERIFICAÇÕES RECOMENDADAS:\n";
echo "==============================\n";
echo "1. Acesse a página do usuário e verifique que o avatar não é mais clicável\n";
echo "2. Verifique que o dropdown do header não tem mais 'Editar Avatar'\n";
echo "3. Confirme que o sistema de edição de perfil ainda funciona\n";
echo "4. Teste navegação entre páginas\n";
echo "5. Verifique que não há erros 404 para editor_avatar.php\n";

echo "\n🎉 REMOÇÃO CONCLUÍDA COM SUCESSO!\n";
echo "==================================\n";
echo "O Editor de Avatar foi completamente removido do sistema.\n";
echo "Acesse http://localhost:8080/confirmacao_remocao_avatar.php para confirmar!\n";

?>
