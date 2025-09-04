# 🚀 Guia Rápido para Novos Colaboradores

## Sistema DayDreamming - Configuração em 5 Minutos

### 📋 Pré-requisitos
- **PHP 7.4+** instalado
- **MySQL 5.7+** ou **MariaDB** instalado
- **Servidor web** (Apache/Nginx) ou usar servidor embutido do PHP

---

## ⚡ Instalação Rápida (Recomendado)

### Método 1: Instalação Automatizada
```bash
# 1. Clone o repositório
git clone [URL_DO_REPOSITORIO]
cd php-login-system

# 2. Execute a instalação automatizada
php instalar_completo.php

# 3. Inicie o servidor
php -S localhost:8080

# 4. Acesse: http://localhost:8080
```

### Método 2: Verificação + Instalação Manual
```bash
# 1. Verifique o ambiente
php verificar_ambiente.php

# 2. Configure o banco (se necessário)
cp config.exemplo.php config.php
# Edite config.php com suas credenciais

# 3. Instale o banco de dados
php setup_database.php

# 4. Inicie o servidor
php -S localhost:8080
```

---

## 🔐 Credenciais Padrão

| Usuário | Login | Senha | Tipo |
|---------|-------|-------|---------|
| Administrador | `admin` | `admin123` | Admin |
| Usuário Teste | `teste` | `teste123` | Comum |

---

## 📁 Estrutura do Projeto

```
php-login-system/
├── 📄 config.exemplo.php      # Template de configuração
├── 📄 setup_database.php      # Instalação do banco
├── 📄 instalar_completo.php   # Instalação automatizada
├── 📄 verificar_ambiente.php  # Diagnóstico do ambiente
├── 📄 index.php              # Página inicial
├── 📄 login.php              # Sistema de login
├── 📄 forum.php              # Fórum
├── 📄 pagina_usuario.php     # Dashboard do usuário
├── 📂 paises/                # Páginas de países (28 países)
├── 📂 imagens/               # Recursos visuais
└── 📚 README_INSTALACAO.md   # Documentação completa
```

---

## 🎯 Funcionalidades Principais

### ✅ Sistema Completo Inclui:
- **👥 Sistema de Usuários** - Registro, login, perfis
- **🏆 Gamificação** - Badges, níveis, experiência
- **📚 Sistema de Testes** - Simulador de provas, questões
- **💬 Fórum** - Categorias, tópicos, moderação
- **🌍 Países** - 28 páginas de países com tracking
- **🔔 Notificações** - Sistema em tempo real
- **📊 Dashboard** - Painel personalizado do usuário
- **🔧 Admin** - Painel administrativo completo

---

## 🛠️ Ferramentas de Debug

### Scripts de Diagnóstico:
```bash
# Verificar ambiente completo
php verificar_ambiente.php

# Testar funcionalidades
php teste_sistema_completo.php

# Status do projeto
php status_projeto.php

# Verificar instalação
php verificar_instalacao.php
```

---

## 🚨 Solução de Problemas Comuns

### ❌ Erro de Conexão MySQL
```bash
# Verifique se MySQL está rodando
# Windows:
net start mysql

# Verifique credenciais em config.php
```

### ❌ Erro de Extensões PHP
```bash
# Instale extensões necessárias:
# Ubuntu/Debian:
sudo apt-get install php-mysql php-mbstring

# Windows (XAMPP): Já incluídas
```

### ❌ Erro de Permissões
```bash
# Linux/Mac:
chmod 755 .
chmod 644 *.php

# Windows: Executar como Administrador
```

---

## 📚 Documentação Adicional

| Arquivo | Descrição |
|---------|----------|
| `README_INSTALACAO.md` | **Guia completo de instalação** |
| `README_COLABORADORES.md` | Guia para desenvolvedores |
| `COMANDOS_COLABORADORES.md` | Comandos úteis |
| `MELHORIAS_IMPLEMENTADAS.md` | Histórico de melhorias |

---

## 🔗 Links Úteis Após Instalação

| Página | URL | Descrição |
|--------|-----|----------|
| **Página Inicial** | `http://localhost:8080/` | Dashboard principal |
| **Login** | `http://localhost:8080/login.php` | Sistema de autenticação |
| **Fórum** | `http://localhost:8080/forum.php` | Fórum da comunidade |
| **Perfil** | `http://localhost:8080/pagina_usuario.php` | Dashboard do usuário |
| **Simulador** | `http://localhost:8080/simulador_provas.php` | Testes e provas |
| **Admin** | `http://localhost:8080/admin_forum.php` | Painel administrativo |

---

## ⚡ Comandos Rápidos

```bash
# Reinstalar banco (limpar tudo)
php instalar_sistema_limpo.php

# Verificar se tudo está OK
php verificar_ambiente.php

# Iniciar servidor de desenvolvimento
php -S localhost:8080

# Parar servidor: Ctrl+C
```

---

## 🎉 Pronto para Desenvolver!

Após seguir estes passos, você terá:
- ✅ Sistema completamente funcional
- ✅ Banco de dados com dados de teste
- ✅ Usuários padrão configurados
- ✅ Todas as funcionalidades ativas
- ✅ Ambiente de desenvolvimento pronto

### 🚀 Próximos Passos:
1. **Explore o sistema** - Faça login e navegue pelas funcionalidades
2. **Leia a documentação** - Consulte `README_INSTALACAO.md` para detalhes
3. **Comece a desenvolver** - Siga os padrões estabelecidos no código
4. **Use as ferramentas** - Aproveite os scripts de debug disponíveis

---

## 📞 Suporte

Se encontrar problemas:
1. **Execute:** `php verificar_ambiente.php`
2. **Consulte:** `README_INSTALACAO.md`
3. **Verifique:** Logs de erro do PHP/MySQL
4. **Teste:** Scripts de diagnóstico disponíveis

**Sistema DayDreamming** - Pronto para colaboração! 🌟