<?php
session_start();
require_once 'config.php';
require_once 'verificar_auth.php';

// Verificar se o usuário está logado
verificarLogin();

$usuario_id = $_SESSION['usuario_id'];
$mensagem = '';
$erro = '';

try {
    $pdo = conectarBD();
    
    // Processar formulário se enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $avatar_config = [
            'cabelo_cor' => $_POST['cabelo_cor'] ?? '#8B4513',
            'cabelo_estilo' => $_POST['cabelo_estilo'] ?? 'curto',
            'pele_cor' => $_POST['pele_cor'] ?? '#FDBCB4',
            'olhos_cor' => $_POST['olhos_cor'] ?? '#654321',
            'roupa_cor' => $_POST['roupa_cor'] ?? '#4CAF50',
            'roupa_estilo' => $_POST['roupa_estilo'] ?? 'casual'
        ];
        
        // Atualizar avatar no banco
        $stmt = $pdo->prepare("
            INSERT INTO perfil_usuario (usuario_id, avatar_personagem)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE avatar_personagem = VALUES(avatar_personagem)
        ");
        
        $stmt->execute([$usuario_id, json_encode($avatar_config)]);
        
        // Registrar atividade
        $stmt = $pdo->prepare("
            INSERT INTO historico_atividades (usuario_id, tipo_atividade, descricao, pontos_ganhos)
            VALUES (?, 'perfil_atualizado', 'Avatar personalizado', 3)
        ");
        $stmt->execute([$usuario_id]);
        
        $mensagem = "Avatar atualizado com sucesso!";
    }
    
    // Buscar configuração atual do avatar
    $stmt = $pdo->prepare("
        SELECT avatar_personagem 
        FROM perfil_usuario 
        WHERE usuario_id = ?
    ");
    $stmt->execute([$usuario_id]);
    $perfil = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $avatar_config = $perfil && $perfil['avatar_personagem'] 
        ? json_decode($perfil['avatar_personagem'], true) 
        : [
            'cabelo_cor' => '#8B4513',
            'cabelo_estilo' => 'curto',
            'pele_cor' => '#FDBCB4',
            'olhos_cor' => '#654321',
            'roupa_cor' => '#4CAF50',
            'roupa_estilo' => 'casual'
        ];
    
} catch (Exception $e) {
    error_log("Erro no editor de avatar: " . $e->getMessage());
    $erro = "Erro ao processar avatar.";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor de Avatar - DayDreaming</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding-top: 80px;
        }
        
        .navbar-custom {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        
        .editor-container {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            margin: 2rem 0;
        }
        
        .avatar-preview {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
            border: 3px solid #dee2e6;
        }
        
        .control-group {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .control-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .color-option {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 3px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 0.25rem;
        }
        
        .color-option:hover {
            transform: scale(1.1);
        }
        
        .color-option.selected {
            border-color: #007bff;
            transform: scale(1.2);
        }
        
        .style-option {
            background: white;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 0.5rem;
        }
        
        .style-option:hover {
            border-color: #007bff;
            transform: translateY(-2px);
        }
        
        .style-option.selected {
            border-color: #007bff;
            background: #e3f2fd;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4CAF50, #81C784);
            border: none;
            border-radius: 25px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            border: none;
            border-radius: 25px;
            padding: 0.75rem 2rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <?php include 'header_status.php'; ?>
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="imagens/Logo_DayDreaming_trasp 1.png" alt="DayDreaming" height="40" class="me-2">
                <span class="fw-bold text-primary">DayDreaming</span>
            </a>
            
            <div class="navbar-nav ms-auto d-flex flex-row align-items-center">
                <a class="nav-link me-3" href="pesquisa_por_pais.php">
                    <i class="fas fa-globe me-1"></i>Países
                </a>
                <a class="nav-link me-3" href="testes_internacionais.php">
                    <i class="fas fa-graduation-cap me-1"></i>Testes
                </a>
                <a class="nav-link me-3" href="simulador_provas.php">
                    <i class="fas fa-laptop me-1"></i>Simuladores
                </a>
                <a class="nav-link me-3" href="forum.php">
                    <i class="fas fa-comments me-1"></i>Fórum
                </a>
                <a class="nav-link me-3" href="pagina_usuario.php">
                    <i class="fas fa-user me-1"></i>Perfil
                </a>
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i>Sair
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="editor-container">
                    <div class="text-center mb-4">
                        <h1 class="h2 text-primary">
                            <i class="fas fa-user-edit me-2"></i>
                            Editor de Avatar
                        </h1>
                        <p class="text-muted">Personalize seu avatar escolhendo cores e estilos</p>
                    </div>

                    <?php if ($mensagem): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($mensagem) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($erro): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($erro) ?>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <!-- Preview do Avatar -->
                        <div class="col-lg-4">
                            <div class="avatar-preview">
                                <h5 class="mb-3">Preview</h5>
                                <div id="avatar-preview">
                                    <!-- Avatar será renderizado aqui via JavaScript -->
                                </div>
                            </div>
                        </div>

                        <!-- Controles de Personalização -->
                        <div class="col-lg-8">
                            <form method="POST" id="avatar-form">
                                <!-- Cor da Pele -->
                                <div class="control-group">
                                    <h6 class="control-title">
                                        <i class="fas fa-palette text-warning"></i>
                                        Cor da Pele
                                    </h6>
                                    <div class="d-flex flex-wrap">
                                        <?php 
                                        $cores_pele = ['#FDBCB4', '#F1C27D', '#E0AC69', '#C68642', '#8D5524', '#654321'];
                                        foreach ($cores_pele as $cor): 
                                        ?>
                                            <div class="color-option <?= $avatar_config['pele_cor'] === $cor ? 'selected' : '' ?>" 
                                                 style="background-color: <?= $cor ?>"
                                                 onclick="selectColor('pele_cor', '<?= $cor ?>')"></div>
                                        <?php endforeach; ?>
                                    </div>
                                    <input type="hidden" name="pele_cor" id="pele_cor" value="<?= $avatar_config['pele_cor'] ?>">
                                </div>

                                <!-- Cor do Cabelo -->
                                <div class="control-group">
                                    <h6 class="control-title">
                                        <i class="fas fa-cut text-info"></i>
                                        Cor do Cabelo
                                    </h6>
                                    <div class="d-flex flex-wrap">
                                        <?php 
                                        $cores_cabelo = ['#000000', '#8B4513', '#D2691E', '#DAA520', '#FF6347', '#9932CC'];
                                        foreach ($cores_cabelo as $cor): 
                                        ?>
                                            <div class="color-option <?= $avatar_config['cabelo_cor'] === $cor ? 'selected' : '' ?>" 
                                                 style="background-color: <?= $cor ?>"
                                                 onclick="selectColor('cabelo_cor', '<?= $cor ?>')"></div>
                                        <?php endforeach; ?>
                                    </div>
                                    <input type="hidden" name="cabelo_cor" id="cabelo_cor" value="<?= $avatar_config['cabelo_cor'] ?>">
                                </div>

                                <!-- Estilo do Cabelo -->
                                <div class="control-group">
                                    <h6 class="control-title">
                                        <i class="fas fa-scissors text-secondary"></i>
                                        Estilo do Cabelo
                                    </h6>
                                    <div class="row">
                                        <?php 
                                        $estilos_cabelo = [
                                            'curto' => 'Curto',
                                            'medio' => 'Médio', 
                                            'longo' => 'Longo'
                                        ];
                                        foreach ($estilos_cabelo as $valor => $nome): 
                                        ?>
                                            <div class="col-4">
                                                <div class="style-option <?= $avatar_config['cabelo_estilo'] === $valor ? 'selected' : '' ?>"
                                                     onclick="selectStyle('cabelo_estilo', '<?= $valor ?>')">
                                                    <i class="fas fa-user-circle fa-2x mb-2"></i>
                                                    <div><?= $nome ?></div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <input type="hidden" name="cabelo_estilo" id="cabelo_estilo" value="<?= $avatar_config['cabelo_estilo'] ?>">
                                </div>

                                <!-- Cor dos Olhos -->
                                <div class="control-group">
                                    <h6 class="control-title">
                                        <i class="fas fa-eye text-primary"></i>
                                        Cor dos Olhos
                                    </h6>
                                    <div class="d-flex flex-wrap">
                                        <?php 
                                        $cores_olhos = ['#654321', '#228B22', '#4169E1', '#8B4513', '#000000', '#808080'];
                                        foreach ($cores_olhos as $cor): 
                                        ?>
                                            <div class="color-option <?= $avatar_config['olhos_cor'] === $cor ? 'selected' : '' ?>" 
                                                 style="background-color: <?= $cor ?>"
                                                 onclick="selectColor('olhos_cor', '<?= $cor ?>')"></div>
                                        <?php endforeach; ?>
                                    </div>
                                    <input type="hidden" name="olhos_cor" id="olhos_cor" value="<?= $avatar_config['olhos_cor'] ?>">
                                </div>

                                <!-- Cor da Roupa -->
                                <div class="control-group">
                                    <h6 class="control-title">
                                        <i class="fas fa-tshirt text-success"></i>
                                        Cor da Roupa
                                    </h6>
                                    <div class="d-flex flex-wrap">
                                        <?php 
                                        $cores_roupa = ['#4CAF50', '#2196F3', '#FF5722', '#9C27B0', '#FF9800', '#607D8B'];
                                        foreach ($cores_roupa as $cor): 
                                        ?>
                                            <div class="color-option <?= $avatar_config['roupa_cor'] === $cor ? 'selected' : '' ?>" 
                                                 style="background-color: <?= $cor ?>"
                                                 onclick="selectColor('roupa_cor', '<?= $cor ?>')"></div>
                                        <?php endforeach; ?>
                                    </div>
                                    <input type="hidden" name="roupa_cor" id="roupa_cor" value="<?= $avatar_config['roupa_cor'] ?>">
                                </div>

                                <input type="hidden" name="roupa_estilo" id="roupa_estilo" value="<?= $avatar_config['roupa_estilo'] ?>">

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary me-3">
                                        <i class="fas fa-save me-2"></i>Salvar Avatar
                                    </button>
                                    <a href="pagina_usuario.php" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Configuração inicial do avatar
        let avatarConfig = {
            pele_cor: '<?= $avatar_config['pele_cor'] ?>',
            cabelo_cor: '<?= $avatar_config['cabelo_cor'] ?>',
            cabelo_estilo: '<?= $avatar_config['cabelo_estilo'] ?>',
            olhos_cor: '<?= $avatar_config['olhos_cor'] ?>',
            roupa_cor: '<?= $avatar_config['roupa_cor'] ?>',
            roupa_estilo: '<?= $avatar_config['roupa_estilo'] ?>'
        };

        function selectColor(tipo, cor) {
            // Remover seleção anterior
            document.querySelectorAll('.color-option').forEach(el => el.classList.remove('selected'));
            
            // Adicionar seleção atual
            event.target.classList.add('selected');
            
            // Atualizar configuração
            avatarConfig[tipo] = cor;
            document.getElementById(tipo).value = cor;
            
            // Atualizar preview
            updateAvatarPreview();
        }

        function selectStyle(tipo, estilo) {
            // Remover seleção anterior do mesmo grupo
            event.target.closest('.control-group').querySelectorAll('.style-option').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Adicionar seleção atual
            event.target.classList.add('selected');
            
            // Atualizar configuração
            avatarConfig[tipo] = estilo;
            document.getElementById(tipo).value = estilo;
            
            // Atualizar preview
            updateAvatarPreview();
        }

        function updateAvatarPreview() {
            const preview = document.getElementById('avatar-preview');

            // Gerar cabelo baseado no estilo (estilo chibi)
            let cabeloPath = '';
            switch(avatarConfig.cabelo_estilo) {
                case 'curto':
                    cabeloPath = `
                        <path d="M25 20 Q35 12 50 15 Q65 12 75 20 Q75 25 70 28 Q60 25 50 25 Q40 25 30 28 Q25 25 25 20" fill="${avatarConfig.cabelo_cor}" stroke="#000" stroke-width="0.5"/>
                        <path d="M30 18 Q40 14 50 16 Q60 14 70 18" fill="${avatarConfig.cabelo_cor}" opacity="0.8"/>
                    `;
                    break;
                case 'longo':
                    cabeloPath = `
                        <path d="M20 20 Q30 10 50 12 Q70 10 80 20 Q80 35 75 45 Q70 50 65 48 Q60 45 55 48 Q50 50 45 48 Q40 45 35 48 Q30 50 25 45 Q20 35 20 20" fill="${avatarConfig.cabelo_cor}" stroke="#000" stroke-width="0.5"/>
                        <path d="M25 18 Q35 12 50 14 Q65 12 75 18" fill="${avatarConfig.cabelo_cor}" opacity="0.8"/>
                        <path d="M22 35 Q27 40 32 38 Q37 35 42 38" fill="${avatarConfig.cabelo_cor}" opacity="0.7"/>
                        <path d="M58 38 Q63 35 68 38 Q73 40 78 35" fill="${avatarConfig.cabelo_cor}" opacity="0.7"/>
                    `;
                    break;
                default: // médio
                    cabeloPath = `
                        <path d="M22 20 Q32 12 50 14 Q68 12 78 20 Q78 30 73 35 Q65 32 50 32 Q35 32 27 35 Q22 30 22 20" fill="${avatarConfig.cabelo_cor}" stroke="#000" stroke-width="0.5"/>
                        <path d="M27 18 Q37 14 50 16 Q63 14 73 18" fill="${avatarConfig.cabelo_cor}" opacity="0.8"/>
                    `;
            }

            preview.innerHTML = `
                <svg width="140" height="140" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <!-- Cabeça (estilo chibi - maior e mais redonda) -->
                    <ellipse cx="50" cy="35" rx="22" ry="20" fill="${avatarConfig.pele_cor}" stroke="#000" stroke-width="0.8"/>

                    <!-- Cabelo -->
                    ${cabeloPath}

                    <!-- Olhos grandes estilo chibi -->
                    <ellipse cx="42" cy="32" rx="4" ry="5" fill="#FFF" stroke="#000" stroke-width="0.5"/>
                    <ellipse cx="58" cy="32" rx="4" ry="5" fill="#FFF" stroke="#000" stroke-width="0.5"/>
                    <ellipse cx="42" cy="33" rx="3" ry="4" fill="${avatarConfig.olhos_cor}"/>
                    <ellipse cx="58" cy="33" rx="3" ry="4" fill="${avatarConfig.olhos_cor}"/>
                    <circle cx="43" cy="31" r="1.5" fill="#FFF" opacity="0.8"/>
                    <circle cx="59" cy="31" r="1.5" fill="#FFF" opacity="0.8"/>

                    <!-- Sobrancelhas -->
                    <path d="M38 28 Q42 26 46 28" stroke="#000" stroke-width="0.8" fill="none"/>
                    <path d="M54 28 Q58 26 62 28" stroke="#000" stroke-width="0.8" fill="none"/>

                    <!-- Nariz pequeno -->
                    <circle cx="50" cy="38" r="0.8" fill="#000" opacity="0.4"/>

                    <!-- Boca sorridente estilo chibi -->
                    <path d="M46 42 Q50 46 54 42" stroke="#000" stroke-width="1" fill="none"/>
                    <path d="M47 43 Q50 45 53 43" stroke="#FF69B4" stroke-width="0.5" fill="none" opacity="0.6"/>

                    <!-- Bochechas rosadas -->
                    <circle cx="35" cy="40" r="3" fill="#FFB6C1" opacity="0.6"/>
                    <circle cx="65" cy="40" r="3" fill="#FFB6C1" opacity="0.6"/>

                    <!-- Corpo chibi (menor proporcionalmente) -->
                    <ellipse cx="50" cy="70" rx="18" ry="22" fill="${avatarConfig.roupa_cor}" stroke="#000" stroke-width="0.8"/>

                    <!-- Detalhes da roupa -->
                    <rect x="44" y="58" width="12" height="8" rx="2" fill="#FFF" opacity="0.8" stroke="#000" stroke-width="0.5"/>
                    <circle cx="47" cy="62" r="1" fill="#000" opacity="0.6"/>
                    <circle cx="53" cy="62" r="1" fill="#000" opacity="0.6"/>

                    <!-- Braços -->
                    <ellipse cx="30" cy="65" rx="6" ry="12" fill="${avatarConfig.pele_cor}" stroke="#000" stroke-width="0.8"/>
                    <ellipse cx="70" cy="65" rx="6" ry="12" fill="${avatarConfig.pele_cor}" stroke="#000" stroke-width="0.8"/>

                    <!-- Mãos -->
                    <circle cx="30" cy="75" r="4" fill="${avatarConfig.pele_cor}" stroke="#000" stroke-width="0.5"/>
                    <circle cx="70" cy="75" r="4" fill="${avatarConfig.pele_cor}" stroke="#000" stroke-width="0.5"/>

                    <!-- Pernas -->
                    <ellipse cx="42" cy="88" rx="5" ry="8" fill="${avatarConfig.pele_cor}" stroke="#000" stroke-width="0.8"/>
                    <ellipse cx="58" cy="88" rx="5" ry="8" fill="${avatarConfig.pele_cor}" stroke="#000" stroke-width="0.8"/>

                    <!-- Sapatos -->
                    <ellipse cx="42" cy="95" rx="6" ry="3" fill="#8B4513" stroke="#000" stroke-width="0.5"/>
                    <ellipse cx="58" cy="95" rx="6" ry="3" fill="#8B4513" stroke="#000" stroke-width="0.5"/>
                </svg>
            `;
        }

        // Inicializar preview
        document.addEventListener('DOMContentLoaded', function() {
            updateAvatarPreview();
        });
    </script>
</body>
</html>
