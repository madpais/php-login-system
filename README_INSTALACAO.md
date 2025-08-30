# 🌍 Sistema DayDreamming - Guia de Instalação

## 📋 Visão Geral

Este guia contém instruções completas para instalação do Sistema DayDreamming em um novo ambiente. O sistema inclui **22 tabelas** com todas as funcionalidades implementadas.

## 🛠️ Pré-requisitos

- **PHP 7.4+** ou superior
- **MySQL 5.7+** ou **MariaDB 10.3+**
- **Servidor Web** (Apache, Nginx, ou servidor embutido do PHP)
- **Extensões PHP necessárias:**
  - PDO
  - PDO_MySQL
  - mbstring
  - json

## 📦 Estrutura do Sistema

### 🗄️ Tabelas Principais (22 tabelas)

#### **👥 Usuários e Perfis**
- `usuarios` - Dados básicos dos usuários
- `perfil_usuario` - Perfis detalhados dos usuários
- `niveis_usuario` - Sistema de níveis e experiência
- `usuario_badges` - Badges conquistadas pelos usuários

#### **📚 Sistema de Testes**
- `questoes` - Questões dos testes
- `sessoes_teste` - Sessões de teste ativas
- `respostas_usuario` - Respostas dos usuários
- `resultados_testes` - Resultados finais dos testes

#### **🏆 Gamificação**
- `badges` - Definição das badges
- `historico_atividades` - Histórico de atividades dos usuários
- `historico_experiencia` - Histórico de ganho de experiência

#### **🌍 Sistema de Países**
- `paises_visitados` - Tracking de países visitados

#### **💬 Fórum**
- `forum_categorias` - Categorias do fórum
- `forum_topicos` - Tópicos do fórum
- `forum_respostas` - Respostas dos tópicos
- `forum_curtidas` - Sistema de curtidas
- `forum_moderacao` - Logs de moderação

#### **🔧 Sistema**
- `configuracoes_sistema` - Configurações do sistema
- `notificacoes` - Sistema de notificações
- `notificacoes_usuario` - Notificações específicas do usuário
- `logs_acesso` - Logs de acesso
- `logs_sistema` - Logs do sistema

## 🚀 Instalação

### **Método 1: Instalação Padrão**

```bash
# 1. Clone ou baixe o projeto
git clone [URL_DO_REPOSITORIO]
cd php-login-system

# 2. Execute o script de instalação
php setup_database.php
```

### **Método 2: Instalação Limpa (Recomendado)**

```bash
# Remove banco existente e cria tudo do zero
php instalar_sistema_limpo.php
```

### **Método 3: Instalação Manual**

1. **Configure o banco de dados:**
   ```sql
   CREATE DATABASE daydreamming_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. **Execute o script:**
   ```bash
   php setup_database.php
   ```

## ⚙️ Configuração

### **1. Arquivo config.php**

Verifique se o arquivo `config.php` está configurado corretamente:

```php
<?php
// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'daydreamming_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### **2. Permissões de Arquivos**

Certifique-se de que as seguintes pastas têm permissão de escrita:
- `uploads/` (se existir)
- `logs/` (se existir)
- `cache/` (se existir)

## 👤 Usuários Padrão

O sistema cria automaticamente dois usuários:

### **🔑 Administrador**
- **Usuário:** `admin`
- **Senha:** `admin123`
- **Permissões:** Administrador completo

### **🧪 Usuário de Teste**
- **Usuário:** `teste`
- **Senha:** `teste123`
- **Permissões:** Usuário padrão

## 📊 Dados Iniciais Incluídos

### **🏆 Badges (10 badges)**
- Primeiro Teste
- 10 Testes, 100 Testes
- Pontuação Alta, Pontuação Perfeita
- Participante do Fórum, Colaborador
- Veterano, Explorador, Globetrotter

### **💬 Categorias do Fórum (5 categorias)**
- Geral
- Testes e Preparação
- Países e Destinos
- Experiências
- Dúvidas e Suporte

### **⚙️ Configurações do Sistema (10 configurações)**
- Nome do site, descrição
- Configurações de manutenção
- Configurações de registro
- Configurações do fórum
- Configurações de segurança
- Configurações de testes

## 🔍 Verificação da Instalação

### **1. Verificar Tabelas**
```sql
SHOW TABLES;
-- Deve mostrar 22 tabelas
```

### **2. Verificar Dados**
```sql
SELECT COUNT(*) FROM usuarios; -- Deve retornar 2
SELECT COUNT(*) FROM badges; -- Deve retornar 10
SELECT COUNT(*) FROM forum_categorias; -- Deve retornar 5
```

### **3. Teste de Login**
1. Acesse o sistema via navegador
2. Faça login com `admin` / `admin123`
3. Verifique se o painel administrativo está acessível

## 🌍 Funcionalidades Incluídas

### **✅ Sistema de Usuários**
- Registro e login
- Perfis personalizáveis
- Sistema de níveis e experiência
- Badges e conquistas

### **✅ Sistema de Testes**
- Múltiplos tipos de prova (TOEFL, IELTS, SAT, etc.)
- Questões de múltipla escolha
- Sistema de pontuação
- Histórico de resultados

### **✅ Sistema de Países Visitados**
- Tracking automático de visitas
- 28 países implementados
- Estatísticas de viagem
- Histórico completo

### **✅ Fórum Completo**
- Categorias organizadas
- Sistema de tópicos e respostas
- Curtidas e moderação
- Notificações

### **✅ Sistema de Notificações**
- Notificações em tempo real
- Histórico de atividades
- Badges conquistadas
- Atualizações do sistema

## 🛠️ Troubleshooting

### **❌ Erro de Conexão com Banco**
```
Solução: Verifique as credenciais em config.php
```

### **❌ Erro de Permissões**
```
Solução: Verifique permissões do usuário MySQL
GRANT ALL PRIVILEGES ON daydreamming_db.* TO 'root'@'localhost';
```

### **❌ Erro de Charset**
```
Solução: Certifique-se de que o banco usa utf8mb4
```

### **❌ Tabelas não Criadas**
```
Solução: Execute instalar_sistema_limpo.php para instalação limpa
```

## 📞 Suporte

### **🔧 Scripts Disponíveis**
- `setup_database.php` - Instalação padrão
- `instalar_sistema_limpo.php` - Instalação limpa
- `verificar_estrutura_banco.php` - Verificação do banco

### **📋 Logs**
- Verifique logs de erro do PHP
- Verifique logs do MySQL/MariaDB
- Use `verificar_estrutura_banco.php` para diagnóstico

## 🎯 Próximos Passos

1. **Configure questões dos testes**
2. **Personalize as configurações**
3. **Configure o servidor web**
4. **Teste todas as funcionalidades**
5. **Configure backup automático**

## 📈 Estatísticas da Instalação

Após a instalação bem-sucedida, você terá:
- **22 tabelas** criadas
- **2 usuários** padrão
- **10 badges** configuradas
- **5 categorias** do fórum
- **10 configurações** do sistema
- **Sistema completo** funcionando

---

**🚀 Sistema DayDreamming instalado e pronto para uso!**
