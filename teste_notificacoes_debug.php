<?php
require_once "config.php";
require_once "sistema_notificacoes.php";
iniciarSessaoSegura();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$sistema_notificacoes = new SistemaNotificacoes();
$usuario_id = $_SESSION["usuario_id"];
$total_nao_lidas = $sistema_notificacoes->contarNotificacoesNaoLidas($usuario_id);
$notificacoes = $sistema_notificacoes->buscarTodasNotificacoes($usuario_id, 100);
?><!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Teste - Notificações</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container mt-4">
            <h1>🔔 Teste de Notificações</h1>
            
            <div class="alert alert-info">
                <strong>Total não lidas:</strong> 0            </div>
            
            <div class="alert alert-success">
                <strong>Total de notificações:</strong> 5            </div>
            
                            <div class="row">
                                            <div class="col-12 mb-3">
                            <div class="card ">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        🏆                                        🏆 Nova Badge Conquistada!                                                                            </h6>
                                    <p class="card-text">Parabéns! Você conquistou a badge &quot;Primeiro Teste&quot; por completar seu primeiro exame.</p>
                                    <small class="text-muted">
                                        26/08/2025 23:52                                                                                    | <a href="pagina_usuario.php">Ver mais</a>
                                                                            </small>
                                </div>
                            </div>
                        </div>
                                            <div class="col-12 mb-3">
                            <div class="card ">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        📈                                        📈 Nível Aumentado!                                                                            </h6>
                                    <p class="card-text">Você subiu para o nível 3! Continue estudando para alcançar níveis ainda maiores.</p>
                                    <small class="text-muted">
                                        26/08/2025 21:52                                                                                    | <a href="pagina_usuario.php">Ver mais</a>
                                                                            </small>
                                </div>
                            </div>
                        </div>
                                            <div class="col-12 mb-3">
                            <div class="card ">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        💬                                        💬 Nova Resposta no Fórum                                                                            </h6>
                                    <p class="card-text">João Silva respondeu ao seu tópico &quot;Dúvidas sobre SAT - Seção de Matemática&quot;.</p>
                                    <small class="text-muted">
                                        26/08/2025 19:52                                                                                    | <a href="forum.php?topico=1">Ver mais</a>
                                                                            </small>
                                </div>
                            </div>
                        </div>
                                            <div class="col-12 mb-3">
                            <div class="card ">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        👤                                        👤 Você foi mencionado!                                                                            </h6>
                                    <p class="card-text">Maria Santos mencionou você em uma discussão sobre &quot;Preparação para TOEFL&quot;.</p>
                                    <small class="text-muted">
                                        26/08/2025 17:52                                                                                    | <a href="forum.php?topico=2">Ver mais</a>
                                                                            </small>
                                </div>
                            </div>
                        </div>
                                            <div class="col-12 mb-3">
                            <div class="card ">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        🔔                                        🎉 Bem-vindo ao DayDreaming!                                                                            </h6>
                                    <p class="card-text">Explore todas as funcionalidades da plataforma e comece sua jornada rumo ao intercâmbio.</p>
                                    <small class="text-muted">
                                        26/08/2025 15:52                                                                                    | <a href="index.php">Ver mais</a>
                                                                            </small>
                                </div>
                            </div>
                        </div>
                                    </div>
                        
            <div class="mt-4">
                <a href="todas_notificacoes.php" class="btn btn-primary">Página Original</a>
                <a href="pagina_usuario.php" class="btn btn-secondary">Meu Perfil</a>
                <a href="index.php" class="btn btn-success">Início</a>
            </div>
        </div>
    </body>
    </html>
    