<<<<<<< HEAD
# Sistema de Login com MySQL e PHP

Este é um sistema de login completo com interface responsiva, desenvolvido em PHP e MySQL. O sistema inclui funcionalidades de login, cadastro de novos usuários, recuperação de senha e um painel de controle básico.

## Funcionalidades

- Login de usuários
- Cadastro de novos usuários
- Recuperação de senha
- Painel de controle (dashboard)
- Interface responsiva com temas personalizáveis
- Segurança com senhas criptografadas
- Registro de logs de acesso

## Requisitos

- PHP 7.0 ou superior
- MySQL 5.6 ou superior
- Servidor web (Apache, Nginx, etc.)

## Configuração

### 1. Banco de Dados

Importe o arquivo `db_structure.sql` para o seu servidor MySQL. Isso criará as tabelas necessárias (`usuarios` e `logs_acesso`) e alguns usuários de exemplo.

```bash
mysql -u seu_usuario -p seu_banco_de_dados < db_structure.sql
```

Ou use uma ferramenta como phpMyAdmin para importar o arquivo SQL.

### 2. Configuração da Conexão

Edite o arquivo `config.php` e atualize as informações de conexão com o banco de dados:

```php
define('DB_HOST', '127.0.0.1:3306'); // Host do banco de dados
define('DB_USER', 'seu_usuario'); // Usuário do MySQL
define('DB_PASS', 'sua_senha'); // Senha do MySQL
define('DB_NAME', 'db_daydreamming_project'); // Nome do banco de dados
```

### 3. Servidor Web

Certifique-se de que os arquivos estão em um diretório acessível pelo seu servidor web. Acesse o sistema através do navegador:

```
http://localhost/caminho/para/o/projeto/
```

## Usuários de Exemplo

O sistema vem com dois usuários pré-cadastrados para testes:

1. **Administrador**
   - Usuário: admin
   - Senha: 123456
   - Email: admin@exemplo.com

2. **Usuário Teste**
   - Usuário: teste
   - Senha: 123456
   - Email: teste@exemplo.com

## Estrutura de Arquivos

- `index.php` - Redirecionamento para a página de login
- `login.php` - Página de login
- `cadastro.php` - Página de cadastro de novos usuários
- `recuperar_senha.php` - Página de recuperação de senha
- `dashboard.php` - Painel de controle após login
- `logout.php` - Script para fazer logout
- `config.php` - Configurações de conexão com o banco de dados
- `db_structure.sql` - Estrutura do banco de dados
- `style.css` - Estilos CSS
- `script.js` - Scripts JavaScript

## Segurança

Este sistema implementa várias medidas de segurança:

- Senhas armazenadas com hash usando `password_hash()` e `password_verify()`
- Proteção contra SQL Injection usando PDO com prepared statements
- Validação de entrada de dados
- Proteção contra XSS usando `htmlspecialchars()`
- Gerenciamento seguro de sessões

## Personalização

O sistema inclui um seletor de temas que permite ao usuário escolher entre diferentes esquemas de cores. Você pode adicionar mais temas editando o array no arquivo `script.js`.

## Melhorias Futuras

- Implementação completa do sistema de recuperação de senha com envio de e-mail
- Perfil de usuário com upload de foto
- Níveis de permissão (administrador, usuário comum, etc.)
- Autenticação de dois fatores
- Integração com redes sociais

## Licença

Este projeto está disponível como código aberto sob os termos da [Licença MIT](https://opensource.org/licenses/MIT).
=======
# php-login-system
>>>>>>> e7d03ca0bebfe0dc01ab7fbee36fbcf482a0321a
