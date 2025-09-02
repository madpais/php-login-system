<?php
/**
 * Comandos INSERT gerados automaticamente
 * Data: 2025-09-02 03:37:54
 */


    // Inserir dados na tabela usuarios
    $pdo->exec("INSERT INTO usuarios (id, nome, usuario, email, senha, is_admin, ativo, data_criacao, ultimo_acesso, ultimo_logout) VALUES (1, 'Administrador', 'admin', 'admin@daydreamming.com', '$2y$10$wj2./v4McroYwA09hlkyQ.n5wKgrGnVy18ulNvf5iqXXdwl7gVahK', 1, 1, '2025-08-28 00:11:13', NULL, NULL)");
    $pdo->exec("INSERT INTO usuarios (id, nome, usuario, email, senha, is_admin, ativo, data_criacao, ultimo_acesso, ultimo_logout) VALUES (2, 'Usuário Teste', 'teste', 'teste@daydreamming.com', '$2y$10$3e/EL3rga.iI61mBzv1UmexvH3SXPJy/HgryMZ1ABCkDseQ8ZXPf6', 0, 1, '2025-08-28 00:11:13', NULL, NULL)");

    // Inserir dados na tabela badges
    $pdo->exec("INSERT INTO badges (id, codigo, nome, descricao, icone, tipo, categoria, condicao_valor, raridade, experiencia_bonus, ativa, data_criacao) VALUES (1, 'primeiro_teste', 'Primeiro Teste', 'Complete seu primeiro teste', '🎯', 'especial', 'teste', 1, 'comum', 100, 1, '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO badges (id, codigo, nome, descricao, icone, tipo, categoria, condicao_valor, raridade, experiencia_bonus, ativa, data_criacao) VALUES (2, 'dez_testes', '10 Testes', 'Complete 10 testes', '🔟', 'frequencia', 'teste', 10, 'comum', 200, 1, '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO badges (id, codigo, nome, descricao, icone, tipo, categoria, condicao_valor, raridade, experiencia_bonus, ativa, data_criacao) VALUES (3, 'cem_testes', '100 Testes', 'Complete 100 testes', '💯', 'frequencia', 'teste', 100, 'raro', 500, 1, '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO badges (id, codigo, nome, descricao, icone, tipo, categoria, condicao_valor, raridade, experiencia_bonus, ativa, data_criacao) VALUES (4, 'pontuacao_alta', 'Pontuação Alta', 'Obtenha mais de 90% em um teste', '⭐', 'pontuacao', 'teste', 90, 'raro', 300, 1, '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO badges (id, codigo, nome, descricao, icone, tipo, categoria, condicao_valor, raridade, experiencia_bonus, ativa, data_criacao) VALUES (5, 'pontuacao_perfeita', 'Pontuação Perfeita', 'Obtenha 100% em um teste', '🏆', 'pontuacao', 'teste', 100, 'epico', 1000, 1, '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO badges (id, codigo, nome, descricao, icone, tipo, categoria, condicao_valor, raridade, experiencia_bonus, ativa, data_criacao) VALUES (6, 'participante_forum', 'Participante do Fórum', 'Crie seu primeiro tópico no fórum', '💬', 'social', 'forum', 1, 'comum', 150, 1, '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO badges (id, codigo, nome, descricao, icone, tipo, categoria, condicao_valor, raridade, experiencia_bonus, ativa, data_criacao) VALUES (7, 'colaborador', 'Colaborador', 'Responda 10 tópicos no fórum', '🤝', 'social', 'forum', 10, 'raro', 400, 1, '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO badges (id, codigo, nome, descricao, icone, tipo, categoria, condicao_valor, raridade, experiencia_bonus, ativa, data_criacao) VALUES (8, 'veterano', 'Veterano', 'Use o sistema por 30 dias', '🎖️', 'tempo', 'geral', 30, 'epico', 800, 1, '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO badges (id, codigo, nome, descricao, icone, tipo, categoria, condicao_valor, raridade, experiencia_bonus, ativa, data_criacao) VALUES (9, 'explorador', 'Explorador', 'Visite 10 países diferentes', '🌍', 'especial', 'geral', 10, 'raro', 350, 1, '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO badges (id, codigo, nome, descricao, icone, tipo, categoria, condicao_valor, raridade, experiencia_bonus, ativa, data_criacao) VALUES (10, 'globetrotter', 'Globetrotter', 'Visite todos os países disponíveis', '✈️', 'especial', 'geral', 28, 'lendario', 2000, 1, '2025-08-28 00:11:13')");

    // Inserir dados na tabela configuracoes_sistema
    $pdo->exec("INSERT INTO configuracoes_sistema (id, chave, valor, tipo, categoria, descricao, editavel, data_criacao, data_atualizacao) VALUES (1, 'site_nome', 'DayDreamming', 'string', 'geral', 'Nome do site', 1, '2025-08-28 00:11:13', '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO configuracoes_sistema (id, chave, valor, tipo, categoria, descricao, editavel, data_criacao, data_atualizacao) VALUES (2, 'site_descricao', 'Plataforma de preparação para intercâmbio', 'string', 'geral', 'Descrição do site', 1, '2025-08-28 00:11:13', '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO configuracoes_sistema (id, chave, valor, tipo, categoria, descricao, editavel, data_criacao, data_atualizacao) VALUES (3, 'manutencao_ativa', 0, 'boolean', 'sistema', 'Modo manutenção ativo', 1, '2025-08-28 00:11:13', '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO configuracoes_sistema (id, chave, valor, tipo, categoria, descricao, editavel, data_criacao, data_atualizacao) VALUES (4, 'registro_aberto', 1, 'boolean', 'usuarios', 'Permitir novos registros', 1, '2025-08-28 00:11:13', '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO configuracoes_sistema (id, chave, valor, tipo, categoria, descricao, editavel, data_criacao, data_atualizacao) VALUES (5, 'forum_ativo', 1, 'boolean', 'forum', 'Fórum ativo', 1, '2025-08-28 00:11:13', '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO configuracoes_sistema (id, chave, valor, tipo, categoria, descricao, editavel, data_criacao, data_atualizacao) VALUES (6, 'moderacao_automatica', 0, 'boolean', 'forum', 'Moderação automática do fórum', 1, '2025-08-28 00:11:13', '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO configuracoes_sistema (id, chave, valor, tipo, categoria, descricao, editavel, data_criacao, data_atualizacao) VALUES (7, 'max_tentativas_login', 5, 'integer', 'seguranca', 'Máximo de tentativas de login', 1, '2025-08-28 00:11:13', '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO configuracoes_sistema (id, chave, valor, tipo, categoria, descricao, editavel, data_criacao, data_atualizacao) VALUES (8, 'tempo_bloqueio_login', 15, 'integer', 'seguranca', 'Tempo de bloqueio em minutos', 1, '2025-08-28 00:11:13', '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO configuracoes_sistema (id, chave, valor, tipo, categoria, descricao, editavel, data_criacao, data_atualizacao) VALUES (9, 'duracao_teste_padrao', 60, 'integer', 'testes', 'Duração padrão dos testes em minutos', 1, '2025-08-28 00:11:13', '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO configuracoes_sistema (id, chave, valor, tipo, categoria, descricao, editavel, data_criacao, data_atualizacao) VALUES (10, 'questoes_por_teste', 20, 'integer', 'testes', 'Número de questões por teste', 1, '2025-08-28 00:11:13', '2025-08-28 00:11:13')");

    // Inserir dados na tabela forum_categorias
    $pdo->exec("INSERT INTO forum_categorias (id, nome, descricao, cor, icone, ativo, ordem, data_criacao) VALUES (1, 'Geral', 'Discussões gerais sobre intercâmbio', '#007bff', '💬', 1, 1, '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO forum_categorias (id, nome, descricao, cor, icone, ativo, ordem, data_criacao) VALUES (2, 'Testes e Preparação', 'Dicas e discussões sobre testes', '#28a745', '📚', 1, 2, '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO forum_categorias (id, nome, descricao, cor, icone, ativo, ordem, data_criacao) VALUES (3, 'Países e Destinos', 'Informações sobre países e destinos', '#17a2b8', '🌍', 1, 3, '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO forum_categorias (id, nome, descricao, cor, icone, ativo, ordem, data_criacao) VALUES (4, 'Experiências', 'Compartilhe suas experiências', '#ffc107', '✨', 1, 4, '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO forum_categorias (id, nome, descricao, cor, icone, ativo, ordem, data_criacao) VALUES (5, 'Dúvidas e Suporte', 'Tire suas dúvidas aqui', '#dc3545', '❓', 1, 5, '2025-08-28 00:11:13')");
    $pdo->exec("INSERT INTO forum_categorias (id, nome, descricao, cor, icone, ativo, ordem, data_criacao) VALUES (6, 'Geral', 'Discussões gerais sobre intercâmbio', '#007bff', '💬', 1, 1, '2025-09-01 22:02:05')");
    $pdo->exec("INSERT INTO forum_categorias (id, nome, descricao, cor, icone, ativo, ordem, data_criacao) VALUES (7, 'Testes e Preparação', 'Dicas e discussões sobre testes', '#28a745', '📚', 1, 2, '2025-09-01 22:02:05')");
    $pdo->exec("INSERT INTO forum_categorias (id, nome, descricao, cor, icone, ativo, ordem, data_criacao) VALUES (8, 'Países e Destinos', 'Informações sobre países e destinos', '#17a2b8', '🌍', 1, 3, '2025-09-01 22:02:05')");
    $pdo->exec("INSERT INTO forum_categorias (id, nome, descricao, cor, icone, ativo, ordem, data_criacao) VALUES (9, 'Experiências', 'Compartilhe suas experiências', '#ffc107', '✨', 1, 4, '2025-09-01 22:02:05')");
    $pdo->exec("INSERT INTO forum_categorias (id, nome, descricao, cor, icone, ativo, ordem, data_criacao) VALUES (10, 'Dúvidas e Suporte', 'Tire suas dúvidas aqui', '#dc3545', '❓', 1, 5, '2025-09-01 22:02:05')");

