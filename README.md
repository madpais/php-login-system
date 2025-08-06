<<<<<<< HEAD
# Sistema de Login com MySQL e PHP

Este é um sistema de login completo com interface responsiva, desenvolvido em PHP e MySQL. O sistema inclui funcionalidades de login, cadastro de novos usuários, recuperação de senha, testes internacionais e uma home page moderna.

## Funcionalidades

- 🏠 **Home Page Moderna** - Interface atrativa para público jovem
- 🔐 **Sistema de Login** - Autenticação segura com criptografia
- 👤 **Cadastro de Usuários** - Registro de novos usuários
- 🔑 **Recuperação de Senha** - Sistema de reset de senha
- 🌍 **Testes Internacionais** - 15 países com filtros por continente
- 📱 **Interface Responsiva** - Design adaptável para todos os dispositivos
- 🔒 **Segurança Avançada** - Senhas criptografadas e logs de acesso
- 🎨 **Design Moderno** - Background temático com nuvens dos sonhos

## 🚀 Execução com Docker (Recomendado)

### Pré-requisitos
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows/Mac)
- Docker Engine + Docker Compose (Linux)

### Instalação do Docker

**Windows/Mac:**
1. Baixe e instale o [Docker Desktop](https://www.docker.com/products/docker-desktop/)
2. Reinicie o computador após a instalação
3. Verifique a instalação: `docker --version`

**Linux (Ubuntu/Debian):**
```bash
sudo apt update
sudo apt install docker.io docker-compose
sudo systemctl start docker
sudo usermod -aG docker $USER
```

### Instalação Rápida

1. **Clone o repositório:**
```bash
git clone https://github.com/madpais/php-login-system.git
cd php-login-system
```

2. **Execute com Docker Compose:**
```bash
# Docker Desktop (Windows/Mac) ou versões mais novas
docker compose up -d

# Ou versão standalone do docker-compose
docker-compose up -d
```

3. **Acesse a aplicação:**
- **Sistema:** http://localhost:8080
- **phpMyAdmin:** http://localhost:8081

### Comandos Úteis

```bash
# Parar os containers
docker compose down

# Ver logs em tempo real
docker compose logs -f

# Reconstruir containers após mudanças
docker compose up -d --build

# Limpar volumes (reset completo do banco)
docker compose down -v
```

### Serviços Incluídos

- **Web Server:** Apache + PHP 8.1 (porta 8080)
- **Banco de Dados:** MySQL 8.0 (porta 3306)
- **phpMyAdmin:** Interface web para MySQL (porta 8081)

### Credenciais de Acesso

**Usuários do Sistema:**
- **Admin:** `admin` / `123456`
- **Teste:** `teste` / `123456`
- **Maria:** `maria.santos` / `123456`

**Banco de Dados:**
- **Root:** `root` / `rootpassword`
- **User:** `user` / `userpassword`

## 🛠️ Instalação Manual

### Requisitos
- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache, Nginx, etc.)

### 1. Banco de Dados

Importe os arquivos SQL na seguinte ordem:

```bash
mysql -u root -p sistema_login < db_structure.sql
mysql -u root -p sistema_login < reset_database.sql
```

### 2. Configuração

O arquivo `config.php` está configurado para usar variáveis de ambiente do Docker, mas também funciona com valores padrão para desenvolvimento local.

### 3. Servidor Local

```bash
php -S localhost:8080
```

Acesse: http://localhost:8080

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
- `public/css/style.css` - Estilos CSS principais
- `public/js/main.js` - Scripts JavaScript principais

## Segurança

Este sistema implementa várias medidas de segurança:

- Senhas armazenadas com hash usando `password_hash()` e `password_verify()`
- Proteção contra SQL Injection usando PDO com prepared statements
- Validação de entrada de dados
- Proteção contra XSS usando `htmlspecialchars()`
- Gerenciamento seguro de sessões

## Personalização

O sistema inclui funcionalidades JavaScript modernas para interatividade, validação de formulários e otimizações de performance no arquivo `public/js/main.js`.

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
