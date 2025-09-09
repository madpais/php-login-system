# 🏆 INSTRUÇÕES COMPLETAS - SISTEMA DE BADGES

## 📋 RESUMO DA CORREÇÃO

✅ **PROBLEMA RESOLVIDO**: Sistema de badges 100% funcional  
✅ **TABELAS CORRIGIDAS**: Estrutura padronizada e compatível  
✅ **FUNÇÕES TESTADAS**: Todas as 6 funções principais operacionais  
✅ **BADGES COMPLETAS**: 34 badges essenciais cadastradas  

---

## 🚀 INSTALAÇÃO EM NOVA MÁQUINA

### **Opção 1: Instalação Automática (RECOMENDADA)**

```bash
# 1. Clone/copie o projeto
# 2. Configure config.php com suas credenciais de banco
# 3. Execute o instalador robusto:
php instalar_completo_novo.php
```

### **Opção 2: Instalação Manual (se houver problemas)**

```bash
# 1. Reset completo do sistema de badges
echo "CONFIRMAR" | php reset_badges_sistema.php

# 2. Inserir todas as badges
php inserir_badges_completo.php

# 3. Verificar funcionamento
php verificar_badges_funcionais.php

# 4. Executar instalação geral
php instalar_completo_novo.php
```

---

## 🔧 ESTRUTURA DAS TABELAS

### **Tabela `badges`**
```sql
CREATE TABLE badges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT NOT NULL,
    icone VARCHAR(10) NOT NULL,
    tipo ENUM('pontuacao', 'frequencia', 'especial', 'tempo', 'social') NOT NULL,
    categoria ENUM('teste', 'forum', 'geral', 'social', 'gpa', 'paises') DEFAULT 'teste',
    condicao_valor INT NULL,
    raridade ENUM('comum', 'raro', 'epico', 'lendario') DEFAULT 'comum',
    experiencia_bonus INT DEFAULT 50,
    ativa TINYINT(1) DEFAULT 1,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### **Tabela `usuario_badges`**
```sql
CREATE TABLE usuario_badges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    badge_id INT NOT NULL,
    data_conquista DATETIME NOT NULL,
    contexto VARCHAR(100) NULL,
    notificado TINYINT(1) DEFAULT 0,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_usuario_badge (usuario_id, badge_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE CASCADE
);
```

---

## 🎯 BADGES CADASTRADAS (34 TOTAL)

### **Badges de Provas (5)**
- `prova_bronze` - Primeiro Passo (20-40% acertos)
- `prova_prata` - Progredindo (40-60% acertos)
- `prova_ouro` - Bom Desempenho (60-80% acertos)
- `prova_rubi` - Excelente (80-99% acertos)
- `prova_diamante` - Perfeição (100% acertos)

### **Badges de Fórum (5)**
- `forum_bronze` - Primeira Participação (1 participação)
- `forum_prata` - Participante Ativo (3 participações)
- `forum_ouro` - Colaborador (5 participações)
- `forum_rubi` - Expert do Fórum (7 participações)
- `forum_diamante` - Mestre do Fórum (9+ participações)

### **Badges de GPA (5)**
- `gpa_bronze` - GPA Iniciante (2.0-2.5)
- `gpa_prata` - GPA Bom (2.5-3.0)
- `gpa_ouro` - GPA Excelente (3.0-3.5)
- `gpa_rubi` - GPA Superior (3.5-4.0)
- `gpa_diamante` - GPA Perfeito (4.0)

### **Badges de Países (5)**
- `paises_bronze` - Explorador Iniciante (5 países)
- `paises_prata` - Viajante (10 países)
- `paises_ouro` - Explorador (15 países)
- `paises_rubi` - Aventureiro (20 países)
- `paises_diamante` - Cidadão do Mundo (28+ países)

### **Badges Especiais (14)**
- `iniciante`, `experiente`, `mestre`, `lenda` - Níveis gerais
- `especialista_sat`, `especialista_enem`, `especialista_vestibular` - Especialistas
- `consistente`, `dedicado` - Consistência
- `maratonista`, `persistente` - Frequência
- `rapido`, `eficiente`, `perfeccionista` - Eficiência

---

## 📝 FUNÇÕES DISPONÍVEIS

### **Funções Principais**
```php
// Verificar badges específicas
verificarBadgesProvas($usuario_id);      // Após completar teste
verificarBadgesForum($usuario_id);       // Após participar no fórum
verificarBadgesGPA($usuario_id);         // Após calcular GPA
verificarBadgesPaises($usuario_id);      // Após visitar país

// Verificar todas as badges
verificarTodasBadges($usuario_id);       // Verificação geral

// Atribuir badge manualmente
atribuirBadge($usuario_id, $badge_codigo, $contexto);
```

### **Classe BadgesManager**
```php
$manager = new BadgesManager();
$badges = $manager->verificarBadgesResultado($usuario_id, $pontuacao, $tipo_prova);
$badges_usuario = getBadgesUsuario($usuario_id);
```

---

## 🔗 INTEGRAÇÃO NO SISTEMA

### **1. Após Completar Teste**
```php
// No arquivo que processa resultados de testes
require_once 'sistema_badges.php';

// Após salvar resultado no banco
verificarBadgesProvas($usuario_id);

// Ou usando BadgesManager para verificação mais detalhada
$manager = new BadgesManager();
$badges = $manager->verificarBadgesResultado($usuario_id, $pontuacao, $tipo_prova);
```

### **2. Após Participar no Fórum**
```php
// No arquivo que salva tópicos/respostas
require_once 'sistema_badges.php';

// Após inserir tópico ou resposta
verificarBadgesForum($usuario_id);
```

### **3. Após Calcular GPA**
```php
// No arquivo que calcula/salva GPA
require_once 'sistema_badges.php';

// Após salvar GPA no banco
verificarBadgesGPA($usuario_id);
```

### **4. Após Visitar País**
```php
// No arquivo que registra visita a países
require_once 'sistema_badges.php';

// Após registrar visita
verificarBadgesPaises($usuario_id);
```

### **5. No Login (Verificação Geral)**
```php
// No arquivo de login, após autenticação
require_once 'sistema_badges.php';

// Verificar todas as badges
$badges_conquistadas = verificarTodasBadges($usuario_id);
```

### **6. Exibir Badges do Usuário**
```php
// Na página do usuário
require_once 'badges_manager.php';

$badges_usuario = getBadgesUsuario($usuario_id);

foreach ($badges_usuario as $badge) {
    echo "<div class='badge'>";
    echo "<span class='badge-icon'>{$badge['icone']}</span>";
    echo "<h4>{$badge['nome']}</h4>";
    echo "<p>{$badge['descricao']}</p>";
    echo "<small>Conquistada em: {$badge['data_conquista']}</small>";
    echo "</div>";
}
```

---

## 🛠️ SCRIPTS DE MANUTENÇÃO

### **Scripts Criados**
- `reset_badges_sistema.php` - Reset completo das tabelas
- `inserir_badges_completo.php` - Inserir todas as 34 badges
- `verificar_badges_funcionais.php` - Verificação completa do sistema
- `instalar_completo_novo.php` - Instalação robusta e automática

### **Scripts de Diagnóstico**
- `verificar_estrutura_badges.php` - Verificar estrutura das tabelas
- `verificar_badges_faltantes.php` - Identificar badges ausentes

---

## ✅ CHECKLIST DE VERIFICAÇÃO

- [ ] Tabelas `badges` e `usuario_badges` existem
- [ ] 34+ badges cadastradas e ativas
- [ ] Todas as 6 funções principais disponíveis
- [ ] Classe `BadgesManager` funcional
- [ ] Teste com usuário real executado com sucesso
- [ ] Arquivos `sistema_badges.php` e `badges_manager.php` incluídos no `config.php`

---

## 🚨 SOLUÇÃO DE PROBLEMAS

### **Se badges não funcionam:**
```bash
# 1. Reset completo
echo "CONFIRMAR" | php reset_badges_sistema.php

# 2. Inserir badges
php inserir_badges_completo.php

# 3. Verificar
php verificar_badges_funcionais.php
```

### **Se funções não estão disponíveis:**
- Verificar se `config.php` inclui `sistema_badges.php` e `badges_manager.php`
- Verificar se não há erros de sintaxe nos arquivos

### **Se tabelas estão incorretas:**
- Executar `reset_badges_sistema.php` para recriar do zero

---

**Data**: 2025-01-13  
**Status**: ✅ SISTEMA 100% FUNCIONAL  
**Versão**: 2.0.0 (Robusta e testada)  
**Badges**: 34/34 (100% completas)  
**Funções**: 6/6 (100% operacionais)
