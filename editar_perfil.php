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
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $escola = trim($_POST['escola'] ?? '');
        $serie_ano = trim($_POST['serie_ano'] ?? '');
        $cidade_estado = trim($_POST['cidade_estado'] ?? '');
        $gpa = $_POST['gpa'] ?? null;
        $idiomas = $_POST['idiomas'] ?? [];
        $exames_realizados = $_POST['exames_realizados'] ?? [];
        $pais_interesse = $_POST['pais_interesse'] ?? '';
        $meta_intercambio = $_POST['meta_intercambio'] ?? '';
        $meta_prazo = $_POST['meta_prazo'] ?? '';
        $biografia = trim($_POST['biografia'] ?? '');
        $background_cor = $_POST['background_cor'] ?? '#4CAF50';
        
        // Validações básicas
        if (empty($nome) || empty($email)) {
            $erro = "Nome e email são obrigatórios.";
        } else {
            // Verificar se email já existe para outro usuário
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
            $stmt->execute([$email, $usuario_id]);
            if ($stmt->rowCount() > 0) {
                $erro = "Este email já está sendo usado por outro usuário.";
            } else {
                // Atualizar dados do usuário
                $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
                $stmt->execute([$nome, $email, $usuario_id]);
                
                // Atualizar ou inserir perfil
                $stmt = $pdo->prepare("
                    INSERT INTO perfil_usuario (
                        usuario_id, escola, serie_ano, cidade_estado, gpa, idiomas, 
                        exames_realizados, pais_interesse, meta_intercambio, meta_prazo, 
                        biografia, background_cor
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                        escola = VALUES(escola),
                        serie_ano = VALUES(serie_ano),
                        cidade_estado = VALUES(cidade_estado),
                        gpa = VALUES(gpa),
                        idiomas = VALUES(idiomas),
                        exames_realizados = VALUES(exames_realizados),
                        pais_interesse = VALUES(pais_interesse),
                        meta_intercambio = VALUES(meta_intercambio),
                        meta_prazo = VALUES(meta_prazo),
                        biografia = VALUES(biografia),
                        background_cor = VALUES(background_cor)
                ");
                
                $stmt->execute([
                    $usuario_id,
                    $escola,
                    $serie_ano,
                    $cidade_estado,
                    $gpa ?: null,
                    json_encode($idiomas),
                    json_encode($exames_realizados),
                    $pais_interesse,
                    $meta_intercambio,
                    $meta_prazo,
                    $biografia,
                    $background_cor
                ]);
                
                // Registrar atividade
                $stmt = $pdo->prepare("
                    INSERT INTO historico_atividades (usuario_id, tipo_atividade, descricao, pontos_ganhos)
                    VALUES (?, 'perfil_atualizado', 'Perfil atualizado', 5)
                ");
                $stmt->execute([$usuario_id]);
                
                $mensagem = "Perfil atualizado com sucesso!";
            }
        }
    }
    
    // Buscar dados atuais do usuário
    $stmt = $pdo->prepare("
        SELECT u.*, p.*
        FROM usuarios u
        LEFT JOIN perfil_usuario p ON u.id = p.usuario_id
        WHERE u.id = ?
    ");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        header("Location: login.php");
        exit;
    }
    
} catch (Exception $e) {
    error_log("Erro na edição de perfil: " . $e->getMessage());
    $erro = "Erro ao processar dados do perfil.";
}

// Decodificar arrays JSON
$idiomas_selecionados = $usuario['idiomas'] ? json_decode($usuario['idiomas'], true) : [];
$exames_selecionados = $usuario['exames_realizados'] ? json_decode($usuario['exames_realizados'], true) : [];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - DayDreaming</title>
    
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
        
        .form-container {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            margin: 2rem 0;
        }
        
        .section-title {
            color: #333;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #4CAF50;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
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
        
        .color-picker {
            width: 60px;
            height: 40px;
            border-radius: 10px;
            border: 2px solid #e9ecef;
            cursor: pointer;
        }
        
        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        
        .form-check {
            background: #f8f9fa;
            padding: 0.5rem;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .form-check:hover {
            background: #e9ecef;
        }
        
        .form-check-input:checked {
            background-color: #4CAF50;
            border-color: #4CAF50;
        }
        
        .alert {
            border-radius: 15px;
            border: none;
        }
    </style>
</head>
<body>
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
                <div class="form-container">
                    <div class="text-center mb-4">
                        <h1 class="h2 text-primary">
                            <i class="fas fa-user-edit me-2"></i>
                            Editar Perfil
                        </h1>
                        <p class="text-muted">Personalize suas informações e configure seu perfil</p>
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

                    <form method="POST">
                        <div class="row">
                            <!-- Informações Básicas -->
                            <div class="col-lg-6">
                                <h3 class="section-title">
                                    <i class="fas fa-user text-primary"></i>
                                    Informações Básicas
                                </h3>
                                
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome Completo *</label>
                                    <input type="text" class="form-control" id="nome" name="nome" 
                                           value="<?= htmlspecialchars($usuario['nome']) ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= htmlspecialchars($usuario['email']) ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="biografia" class="form-label">Biografia</label>
                                    <textarea class="form-control" id="biografia" name="biografia" rows="4" 
                                              placeholder="Conte um pouco sobre você..."><?= htmlspecialchars($usuario['biografia'] ?? '') ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="background_cor" class="form-label">Cor do Fundo do Perfil</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <input type="color" class="color-picker" id="background_cor" name="background_cor" 
                                               value="<?= htmlspecialchars($usuario['background_cor'] ?? '#4CAF50') ?>">
                                        <span class="text-muted">Escolha a cor de fundo do seu perfil</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Dados Acadêmicos -->
                            <div class="col-lg-6">
                                <h3 class="section-title">
                                    <i class="fas fa-graduation-cap text-success"></i>
                                    Dados Acadêmicos
                                </h3>
                                
                                <div class="mb-3">
                                    <label for="escola" class="form-label">Escola/Universidade</label>
                                    <input type="text" class="form-control" id="escola" name="escola" 
                                           value="<?= htmlspecialchars($usuario['escola'] ?? '') ?>"
                                           placeholder="Nome da sua instituição de ensino">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="serie_ano" class="form-label">Série/Ano</label>
                                    <input type="text" class="form-control" id="serie_ano" name="serie_ano" 
                                           value="<?= htmlspecialchars($usuario['serie_ano'] ?? '') ?>"
                                           placeholder="Ex: 3º ano do Ensino Médio">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="cidade_estado" class="form-label">Cidade/Estado</label>
                                    <input type="text" class="form-control" id="cidade_estado" name="cidade_estado" 
                                           value="<?= htmlspecialchars($usuario['cidade_estado'] ?? '') ?>"
                                           placeholder="Ex: São Paulo/SP">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="gpa" class="form-label">GPA (0.00 - 4.00)</label>
                                    <input type="number" class="form-control" id="gpa" name="gpa" 
                                           value="<?= $usuario['gpa'] ?>" step="0.01" min="0" max="4"
                                           placeholder="Ex: 3.75">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <!-- Idiomas -->
                            <div class="col-lg-6">
                                <h3 class="section-title">
                                    <i class="fas fa-language text-info"></i>
                                    Idiomas
                                </h3>
                                
                                <div class="checkbox-group">
                                    <?php 
                                    $idiomas_disponiveis = ['Português', 'Inglês', 'Espanhol', 'Francês', 'Alemão', 'Italiano', 'Japonês', 'Chinês', 'Coreano', 'Árabe'];
                                    foreach ($idiomas_disponiveis as $idioma): 
                                    ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="idiomas[]" 
                                                   value="<?= $idioma ?>" id="idioma_<?= strtolower($idioma) ?>"
                                                   <?= in_array($idioma, $idiomas_selecionados) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="idioma_<?= strtolower($idioma) ?>">
                                                <?= $idioma ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Exames Realizados -->
                            <div class="col-lg-6">
                                <h3 class="section-title">
                                    <i class="fas fa-certificate text-warning"></i>
                                    Exames Realizados
                                </h3>
                                
                                <div class="checkbox-group">
                                    <?php 
                                    $exames_disponiveis = ['TOEFL', 'IELTS', 'SAT', 'DELE', 'DELF', 'TestDaF', 'JLPT', 'HSK', 'GRE', 'GMAT'];
                                    foreach ($exames_disponiveis as $exame): 
                                    ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="exames_realizados[]" 
                                                   value="<?= $exame ?>" id="exame_<?= strtolower($exame) ?>"
                                                   <?= in_array($exame, $exames_selecionados) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="exame_<?= strtolower($exame) ?>">
                                                <?= $exame ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <!-- Metas de Intercâmbio -->
                            <div class="col-lg-12">
                                <h3 class="section-title">
                                    <i class="fas fa-target text-danger"></i>
                                    Metas de Intercâmbio
                                </h3>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="pais_interesse" class="form-label">País de Interesse</label>
                                            <select class="form-select" id="pais_interesse" name="pais_interesse">
                                                <option value="">Selecione um país</option>
                                                <?php 
                                                $paises = ['Estados Unidos', 'Reino Unido', 'Canadá', 'Austrália', 'Alemanha', 'França', 'Espanha', 'Itália', 'Japão', 'China', 'Coreia do Sul'];
                                                foreach ($paises as $pais): 
                                                ?>
                                                    <option value="<?= $pais ?>" <?= $usuario['pais_interesse'] === $pais ? 'selected' : '' ?>>
                                                        <?= $pais ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="meta_intercambio" class="form-label">Tipo de Intercâmbio</label>
                                            <select class="form-select" id="meta_intercambio" name="meta_intercambio">
                                                <option value="">Selecione o tipo</option>
                                                <option value="graduacao" <?= $usuario['meta_intercambio'] === 'graduacao' ? 'selected' : '' ?>>Graduação</option>
                                                <option value="pos_graduacao" <?= $usuario['meta_intercambio'] === 'pos_graduacao' ? 'selected' : '' ?>>Pós-graduação</option>
                                                <option value="mestrado" <?= $usuario['meta_intercambio'] === 'mestrado' ? 'selected' : '' ?>>Mestrado</option>
                                                <option value="doutorado" <?= $usuario['meta_intercambio'] === 'doutorado' ? 'selected' : '' ?>>Doutorado</option>
                                                <option value="curso_idioma" <?= $usuario['meta_intercambio'] === 'curso_idioma' ? 'selected' : '' ?>>Curso de Idioma</option>
                                                <option value="trabalho" <?= $usuario['meta_intercambio'] === 'trabalho' ? 'selected' : '' ?>>Trabalho</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="meta_prazo" class="form-label">Prazo</label>
                                            <select class="form-select" id="meta_prazo" name="meta_prazo">
                                                <option value="">Selecione o prazo</option>
                                                <option value="6_meses" <?= $usuario['meta_prazo'] === '6_meses' ? 'selected' : '' ?>>6 meses</option>
                                                <option value="1_ano" <?= $usuario['meta_prazo'] === '1_ano' ? 'selected' : '' ?>>1 ano</option>
                                                <option value="2_anos" <?= $usuario['meta_prazo'] === '2_anos' ? 'selected' : '' ?>>2 anos</option>
                                                <option value="3_anos" <?= $usuario['meta_prazo'] === '3_anos' ? 'selected' : '' ?>>3 anos</option>
                                                <option value="mais_3_anos" <?= $usuario['meta_prazo'] === 'mais_3_anos' ? 'selected' : '' ?>>Mais de 3 anos</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary me-3">
                                <i class="fas fa-save me-2"></i>Salvar Alterações
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
