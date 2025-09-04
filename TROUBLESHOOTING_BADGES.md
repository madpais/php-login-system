# 🏆 Troubleshooting - Sistema de Badges

## 🔍 Problema Identificado

**Sintoma:** O sistema de badges não funciona em computadores que clonaram o repositório.

**Erro comum:**
```
Classe BadgesManager não encontrada
Função verificarBadgesProvas não disponível
```

## 🎯 Causa Raiz

O problema ocorria porque os arquivos do sistema de badges (`badges_manager.php` e `sistema_badges.php`) não estavam sendo incluídos automaticamente no `config.php`, fazendo com que as classes e funções não ficassem disponíveis para o resto do sistema.

## ✅ Solução Implementada

### 1. **Correção no config.php**

Foi adicionado ao `config.php` (e `config.exemplo.php`) o seguinte código:

```php
// Incluir arquivos do sistema de badges
if (file_exists(__DIR__ . '/badges_manager.php')) {
    require_once __DIR__ . '/badges_manager.php';
}

if (file_exists(__DIR__ . '/sistema_badges.php')) {
    require_once __DIR__ . '/sistema_badges.php';
}
```

### 2. **Script de Diagnóstico**

Criado o arquivo `diagnostico_badges.php` para verificar:
- Conexão com banco de dados
- Existência das tabelas necessárias
- Presença dos arquivos de badges
- Carregamento das classes e funções
- Status geral do sistema

## 🔧 Como Verificar se Está Funcionando

### **Método 1: Diagnóstico Automático**
```bash
php diagnostico_badges.php
```

**Resultado esperado:**
```
✅ Classe BadgesManager: Disponível
✅ Instância BadgesManager: Criada com sucesso
✅ Função verificarBadgesProvas: Disponível
```

### **Método 2: Teste Manual**
```php
<?php
require_once 'config.php';

// Testar se as classes estão disponíveis
if (class_exists('BadgesManager')) {
    echo "✅ BadgesManager disponível\n";
    $manager = new BadgesManager();
    echo "✅ Instância criada com sucesso\n";
} else {
    echo "❌ BadgesManager não disponível\n";
}

if (function_exists('verificarBadgesProvas')) {
    echo "✅ Funções do sistema_badges disponíveis\n";
} else {
    echo "❌ Funções do sistema_badges não disponíveis\n";
}
?>
```

## 🚨 Se o Problema Persistir

### **1. Verificar Arquivos**
```bash
# Verificar se os arquivos existem
ls -la badges_manager.php sistema_badges.php

# Verificar se não estão vazios
wc -l badges_manager.php sistema_badges.php
```

### **2. Verificar Sintaxe**
```bash
# Testar sintaxe dos arquivos
php -l badges_manager.php
php -l sistema_badges.php
php -l config.php
```

### **3. Verificar Permissões**
```bash
# No Linux/Mac
chmod 644 badges_manager.php sistema_badges.php

# No Windows, verificar se os arquivos não estão bloqueados
```

### **4. Reinstalação Completa**
```bash
# Executar instalação completa
php instalar_completo.php

# Ou passo a passo:
php criar_tabelas.php
php inserir_badges.php
php diagnostico_badges.php
```

## 📋 Checklist de Verificação

- [ ] Arquivo `config.php` existe e tem as inclusões de badges
- [ ] Arquivo `badges_manager.php` existe e não está vazio
- [ ] Arquivo `sistema_badges.php` existe e não está vazio
- [ ] Tabelas `badges` e `usuario_badges` existem no banco
- [ ] Há badges cadastradas na tabela `badges`
- [ ] Conexão com banco de dados funciona
- [ ] Classe `BadgesManager` é carregada corretamente
- [ ] Funções como `verificarBadgesProvas` estão disponíveis

## 🎯 Prevenção Futura

### **Para Novos Colaboradores:**
1. Sempre execute `php instalar_completo.php` após clonar
2. Execute `php diagnostico_badges.php` para verificar
3. Mantenha `config.exemplo.php` atualizado

### **Para Desenvolvedores:**
1. Nunca remova as inclusões do `config.php`
2. Mantenha os arquivos de badges no diretório raiz
3. Teste sempre após modificações no sistema de badges

## 📚 Arquivos Relacionados

- `config.php` - Configuração principal (inclui badges)
- `config.exemplo.php` - Template de configuração
- `badges_manager.php` - Classe principal de badges
- `sistema_badges.php` - Funções auxiliares de badges
- `diagnostico_badges.php` - Script de diagnóstico
- `inserir_badges.php` - Script de inserção de badges
- `instalar_completo.php` - Instalação automatizada

## 🔄 Histórico de Mudanças

**2025-01-13:**
- ✅ Identificado problema de inclusão de arquivos
- ✅ Adicionadas inclusões automáticas no config.php
- ✅ Criado script de diagnóstico
- ✅ Atualizada documentação
- ✅ Testado e validado funcionamento

---

**💡 Dica:** Sempre execute `php diagnostico_badges.php` após qualquer mudança no sistema de badges para garantir que tudo está funcionando corretamente.