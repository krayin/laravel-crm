# 🛠️ FERRAMENTAS DE DESENVOLVIMENTO KRAYIN
## Análise, Validação e Guia de Uso para Customização Profissional

**Versão:** 1.0.0  
**Data:** Dezembro 2025  
**Contexto:** Customização profissional do Krayin CRM com Docker Swarm  
**Status:** ✅ VALIDADO PARA USO

---

## 📋 ÍNDICE

1. [Análise de Validade](#-análise-de-validade)
2. [Blade Tracer - O Localizador de Views](#-blade-tracer---o-localizador-de-views)
3. [Package Generator - O Gerador de Código](#-package-generator---o-gerador-de-código)
4. [Debug Bar - O Monitor de Performance](#-debug-bar---o-monitor-de-performance)
5. [Fluxo de Trabalho Integrado](#-fluxo-de-trabalho-integrado)
6. [Matriz de Uso por Cenário](#-matriz-de-uso-por-cenário)
7. [Instalação Completa do Toolkit](#-instalação-completa-do-toolkit)
8. [Boas Práticas e Recomendações](#-boas-práticas-e-recomendações)

---

## 🎯 ANÁLISE DE VALIDADE

### Contexto do Seu Cenário

Você está trabalhando com:
- ✅ Customização visual completa (login, dashboard, cores, logos)
- ✅ Criação de packages customizados (CustomTheme, CustomWorkflow)
- ✅ Override de controllers, models e views
- ✅ Sistema de eventos e listeners
- ✅ Deploy com Docker Swarm + Traefik
- ✅ Ambiente profissional com múltiplos desenvolvedores

### Veredicto por Ferramenta

| Ferramenta | Válida? | Impacto | Recomendação |
|------------|:-------:|:-------:|--------------|
| **Blade Tracer** | ✅ SIM | 🔥 ALTO | Obrigatória para localizar views |
| **Package Generator** | ✅ SIM | 🔥 ALTO | Obrigatória para criar packages |
| **Debug Bar** | ✅ SIM | 🟡 MÉDIO | Recomendada para diagnóstico |

### Por que São Válidas no Seu Cenário?

```
┌─────────────────────────────────────────────────────────────────────┐
│                    SEU FLUXO DE TRABALHO                            │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  1. IDENTIFICAR                    ← BLADE TRACER                   │
│     "Qual view preciso alterar?"                                    │
│                                                                     │
│  2. CRIAR                          ← PACKAGE GENERATOR              │
│     "Preciso de um novo package"                                    │
│                                                                     │
│  3. IMPLEMENTAR                    ← Seu código + Anatomia          │
│     "Override, listener, view..."                                   │
│                                                                     │
│  4. VALIDAR                        ← DEBUG BAR + BLADE TRACER       │
│     "Está funcionando? Performance ok?"                             │
│                                                                     │
│  5. DEPLOY                         ← Docker Swarm                   │
│     "Build, push, update"                                           │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

**Conclusão:** As três ferramentas se complementam e resolvem problemas reais do seu fluxo de customização. São **altamente recomendadas** para ambiente de desenvolvimento.

---

## 🔍 BLADE TRACER - O Localizador de Views

### O que é?

```
╔═══════════════════════════════════════════════════════════════════════════════╗
║  KRAYIN BLADE TRACER                                                          ║
║  ─────────────────────────────────────────────────────────────────────────── ║
║  Pacote: krayin/krayin-blade-tracer                                           ║
║  Função: Mostrar o caminho do arquivo Blade ao passar o mouse sobre          ║
║          qualquer elemento da página                                          ║
║  Requisito: Krayin v1.0.0+                                                    ║
╚═══════════════════════════════════════════════════════════════════════════════╝
```

### Como Funciona

```
┌─────────────────────────────────────────────────────────────────────┐
│                    BLADE TRACER EM AÇÃO                             │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  BROWSER (Tela de Login)                                            │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │                                                             │   │
│  │    ┌────────────────────────────────────────┐              │   │
│  │    │         FORMULÁRIO DE LOGIN            │              │   │
│  │    │                                        │   🖱️ HOVER    │   │
│  │    │  Email: [________________]             │              │   │
│  │    │  Senha: [________________]             │              │   │
│  │    │                                        │              │   │
│  │    │  [ ENTRAR ]                            │              │   │
│  │    │                                        │              │   │
│  │    └────────────────────────────────────────┘              │   │
│  │                                                             │   │
│  │    ┌────────────────────────────────────────────────────┐  │   │
│  │    │ 📁 packages/Webkul/Admin/Resources/views/auth/     │  │   │
│  │    │    login.blade.php                                 │  │   │
│  │    └────────────────────────────────────────────────────┘  │   │
│  │                          ↑ TOOLTIP DO BLADE TRACER         │   │
│  │                                                             │   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### Instalação

```bash
# 1. Instalar o pacote
composer require krayin/krayin-blade-tracer

# 2. Limpar cache de views
php artisan view:clear

# 3. Pronto! Acesse qualquer página e passe o mouse sobre elementos
```

### Como Ajuda no Seu Cenário

#### Problema 1: "Qual view customizar para alterar o login?"

```
SEM BLADE TRACER:
──────────────────
1. Abrir projeto no editor
2. Buscar por "login" em todos os arquivos
3. Encontrar 15+ arquivos com "login"
4. Analisar cada um para descobrir qual é o correto
5. Tentar e errar até achar o certo
⏱️ Tempo: 30-60 minutos

COM BLADE TRACER:
─────────────────
1. Abrir /admin/login no browser
2. Passar mouse sobre o formulário
3. Ler: "packages/Webkul/Admin/Resources/views/auth/login.blade.php"
4. Copiar para seu package e customizar
⏱️ Tempo: 2 minutos
```

#### Problema 2: "Onde está o empty state de leads?"

```
SEM BLADE TRACER:
──────────────────
1. Abrir tela de leads vazia
2. Buscar por "No records", "empty", "vazio"...
3. Encontrar múltiplos resultados em partials, components
4. Debugar com dd() ou var_dump() para confirmar
⏱️ Tempo: 20-40 minutos

COM BLADE TRACER:
─────────────────
1. Abrir /admin/leads (sem leads)
2. Passar mouse sobre a mensagem vazia
3. Ler: "packages/Webkul/Admin/Resources/views/components/empty-state.blade.php"
⏱️ Tempo: 30 segundos
```

#### Problema 3: "Meu override não está funcionando"

```
DIAGNÓSTICO COM BLADE TRACER:
─────────────────────────────
1. Criar override em packages/Webkul/CustomTheme/Resources/views/auth/login.blade.php
2. Recarregar página com Blade Tracer ativo
3. Passar mouse sobre o elemento

SE mostrar: "packages/Webkul/CustomTheme/Resources/views/auth/login.blade.php"
   ✅ Override funcionando!

SE mostrar: "packages/Webkul/Admin/Resources/views/auth/login.blade.php"
   ❌ Override NÃO está funcionando
   → Verificar: namespace, caminho, vendor:publish, cache
```

### Casos de Uso Práticos

| Cenário | Ação com Blade Tracer |
|---------|----------------------|
| Customizar login | Hover no form → copiar path → criar override |
| Trocar logo | Hover no logo → ver onde está sendo renderizado |
| Alterar sidebar | Hover em cada item → identificar partial/component |
| Mudar empty state | Hover na mensagem vazia → identificar view |
| Customizar cards | Hover no card → ver se é component reutilizável |
| Alterar modal | Hover no modal → identificar arquivo |
| Mudar footer | Hover no footer → ver partial |

### Workflow Recomendado

```bash
# 1. Habilitar Blade Tracer no DEV
composer require krayin/krayin-blade-tracer
php artisan view:clear

# 2. Mapear TODAS as views que precisa customizar
# Navegue por: login, dashboard, leads, contacts, quotes, settings
# Para cada tela, passe o mouse e anote os paths

# 3. Criar documento de mapeamento
# Exemplo:
# - Login: packages/Webkul/Admin/Resources/views/auth/login.blade.php
# - Dashboard: packages/Webkul/Admin/Resources/views/admin/dashboard.blade.php
# - Lead List: packages/Webkul/Lead/Resources/views/leads/index.blade.php
# - etc.

# 4. Criar overrides no seu package CustomTheme baseado no mapeamento

# 5. Validar que overrides estão funcionando (tooltip deve mostrar SEU package)
```

### ⚠️ Importante: Apenas em DEV

```php
// NÃO usar em produção!
// O Blade Tracer expõe caminhos internos do sistema

// Em composer.json, instalar como dev dependency:
composer require krayin/krayin-blade-tracer --dev

// Ou verificar APP_ENV antes de carregar
// (o pacote deve fazer isso automaticamente)
```

---

## 🏭 PACKAGE GENERATOR - O Gerador de Código

### O que é?

```
╔═══════════════════════════════════════════════════════════════════════════════╗
║  KRAYIN PACKAGE GENERATOR                                                     ║
║  ─────────────────────────────────────────────────────────────────────────── ║
║  Pacote: krayin/krayin-package-generator                                      ║
║  Função: Gerar estrutura de packages e artefatos (models, repositories,       ║
║          events, controllers) via comandos Artisan                            ║
║  Requisito: Krayin v1.0.0+                                                    ║
╚═══════════════════════════════════════════════════════════════════════════════╝
```

### Como Funciona

```
┌─────────────────────────────────────────────────────────────────────┐
│                 PACKAGE GENERATOR EM AÇÃO                           │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  TERMINAL                                                           │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │                                                             │   │
│  │  $ php artisan package:make Webkul/CustomWorkflow           │   │
│  │                                                             │   │
│  │  Creating package structure...                              │   │
│  │  ✓ Created: packages/Webkul/CustomWorkflow/                 │   │
│  │  ✓ Created: src/Providers/CustomWorkflowServiceProvider.php │   │
│  │  ✓ Created: src/Config/                                     │   │
│  │  ✓ Created: src/Routes/                                     │   │
│  │  ✓ Created: Resources/views/                                │   │
│  │  ✓ Created: Resources/lang/                                 │   │
│  │  ✓ Created: composer.json                                   │   │
│  │                                                             │   │
│  │  Package created successfully!                              │   │
│  │                                                             │   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  RESULTADO                                                          │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │  packages/Webkul/CustomWorkflow/                            │   │
│  │  ├── src/                                                   │   │
│  │  │   ├── Providers/                                         │   │
│  │  │   │   └── CustomWorkflowServiceProvider.php              │   │
│  │  │   ├── Config/                                            │   │
│  │  │   ├── Routes/                                            │   │
│  │  │   └── ...                                                │   │
│  │  ├── Resources/                                             │   │
│  │  │   ├── views/                                             │   │
│  │  │   └── lang/                                              │   │
│  │  └── composer.json                                          │   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### Instalação

```bash
# Instalar o pacote
composer require krayin/krayin-package-generator

# Verificar comandos disponíveis
php artisan list | grep package
```

### Comandos Disponíveis

```bash
# ═══════════════════════════════════════════════════════════════════
# CRIAR PACKAGE COMPLETO
# ═══════════════════════════════════════════════════════════════════

# Criar novo package (se não existe)
php artisan package:make Webkul/CustomTheme

# Criar package sobrescrevendo existente
php artisan package:make Webkul/CustomTheme --force

# ═══════════════════════════════════════════════════════════════════
# CRIAR ARTEFATOS ESPECÍFICOS
# ═══════════════════════════════════════════════════════════════════

# Criar Model
php artisan package:make-model Lead Webkul/CustomTheme
# → packages/Webkul/CustomTheme/src/Models/Lead.php

# Criar Repository
php artisan package:make-repository LeadRepository Webkul/CustomTheme
# → packages/Webkul/CustomTheme/src/Repositories/LeadRepository.php

# Criar Controller
php artisan package:make-controller LeadController Webkul/CustomTheme
# → packages/Webkul/CustomTheme/src/Http/Controllers/LeadController.php

# Criar Event
php artisan package:make-event LeadCreated Webkul/CustomTheme
# → packages/Webkul/CustomTheme/src/Events/LeadCreated.php

# Criar Listener
php artisan package:make-listener SendLeadNotification Webkul/CustomTheme
# → packages/Webkul/CustomTheme/src/Listeners/SendLeadNotification.php

# Criar Migration
php artisan package:make-migration create_custom_leads_table Webkul/CustomTheme
# → packages/Webkul/CustomTheme/Database/Migrations/xxxx_create_custom_leads_table.php
```

### Como Ajuda no Seu Cenário

#### Problema 1: "Preciso criar um novo package do zero"

```
SEM PACKAGE GENERATOR:
──────────────────────
1. Criar pasta packages/Webkul/CustomTheme/
2. Criar subpastas: src/, Resources/, Database/
3. Criar mais subpastas: Providers/, Controllers/, Models/, etc.
4. Criar composer.json manualmente
5. Criar ServiceProvider manualmente
6. Copiar boilerplate de outro package
7. Ajustar namespaces em todos os arquivos
8. Errar namespace, corrigir, testar, errar de novo...
⏱️ Tempo: 30-60 minutos
❌ Risco: Erros de estrutura, namespaces errados, esquecimentos

COM PACKAGE GENERATOR:
──────────────────────
1. php artisan package:make Webkul/CustomTheme
2. Pronto!
⏱️ Tempo: 10 segundos
✅ Estrutura 100% correta e padronizada
```

#### Problema 2: "Preciso adicionar um Model ao meu package"

```
SEM PACKAGE GENERATOR:
──────────────────────
1. Criar arquivo Lead.php em src/Models/
2. Copiar boilerplate de outro Model
3. Ajustar namespace, class name
4. Criar Contract (interface)
5. Registrar no ServiceProvider
6. Testar, corrigir erros de namespace...
⏱️ Tempo: 15-20 minutos

COM PACKAGE GENERATOR:
──────────────────────
1. php artisan package:make-model Lead Webkul/CustomTheme
2. Pronto!
⏱️ Tempo: 5 segundos
```

#### Problema 3: "Time novo, cada dev cria packages de forma diferente"

```
SEM PACKAGE GENERATOR:
──────────────────────
Dev A: src/Providers/
Dev B: Providers/ (sem src)
Dev C: providers/ (lowercase)

Resultado: Inconsistência, bugs, confusão

COM PACKAGE GENERATOR:
──────────────────────
Todos usam: php artisan package:make
Resultado: Estrutura idêntica em todos os packages
```

### Workflow Completo: Criar Package do Zero

```bash
# ═══════════════════════════════════════════════════════════════════
# EXEMPLO: Criar package CustomWorkflow
# ═══════════════════════════════════════════════════════════════════

# 1. Gerar estrutura base
php artisan package:make Webkul/CustomWorkflow

# 2. Adicionar Model
php artisan package:make-model Workflow Webkul/CustomWorkflow

# 3. Adicionar Repository
php artisan package:make-repository WorkflowRepository Webkul/CustomWorkflow

# 4. Adicionar Controller
php artisan package:make-controller WorkflowController Webkul/CustomWorkflow

# 5. Adicionar Event
php artisan package:make-event WorkflowTriggered Webkul/CustomWorkflow

# 6. Adicionar Listener
php artisan package:make-listener ExecuteWorkflowAction Webkul/CustomWorkflow

# 7. Registrar no composer.json raiz
# Adicionar em autoload.psr-4:
# "Webkul\\CustomWorkflow\\": "packages/Webkul/CustomWorkflow/src"

# 8. Rodar autoload
composer dump-autoload

# 9. Adicionar em config/modules.php
# 'CustomWorkflow' (por último na lista)

# 10. Limpar cache
php artisan optimize:clear

# 11. Publicar assets (se tiver)
php artisan vendor:publish --provider="Webkul\\CustomWorkflow\\Providers\\CustomWorkflowServiceProvider"
```

### Resultado Final

```
packages/Webkul/CustomWorkflow/
├── src/
│   ├── Providers/
│   │   └── CustomWorkflowServiceProvider.php   ✅ Gerado
│   ├── Http/
│   │   └── Controllers/
│   │       └── WorkflowController.php          ✅ Gerado
│   ├── Models/
│   │   └── Workflow.php                        ✅ Gerado
│   ├── Repositories/
│   │   └── WorkflowRepository.php              ✅ Gerado
│   ├── Events/
│   │   └── WorkflowTriggered.php               ✅ Gerado
│   ├── Listeners/
│   │   └── ExecuteWorkflowAction.php           ✅ Gerado
│   ├── Routes/
│   └── Config/
├── Resources/
│   ├── views/
│   └── lang/
├── Database/
│   └── Migrations/
└── composer.json                               ✅ Gerado
```

---

## 📊 DEBUG BAR - O Monitor de Performance

### O que é?

```
╔═══════════════════════════════════════════════════════════════════════════════╗
║  KRAYIN DEBUG BAR                                                             ║
║  ─────────────────────────────────────────────────────────────────────────── ║
║  Pacote: krayin/krayin-debug-bar                                              ║
║  Função: Exibir estatísticas de performance agrupadas por módulo Krayin      ║
║          (queries, tempo de execução, memória, etc.)                          ║
║  Requisito: Krayin v1.0.0+                                                    ║
╚═══════════════════════════════════════════════════════════════════════════════╝
```

### Como Funciona

```
┌─────────────────────────────────────────────────────────────────────┐
│                    DEBUG BAR EM AÇÃO                                │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  BROWSER (qualquer página)                                          │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │                                                             │   │
│  │           CONTEÚDO NORMAL DA PÁGINA                         │   │
│  │                                                             │   │
│  │                                                             │   │
│  │                                                             │   │
│  ├─────────────────────────────────────────────────────────────┤   │
│  │  DEBUG BAR                                                  │   │
│  │  ┌────────────────────────────────────────────────────────┐│   │
│  │  │ ⏱️ 245ms │ 🗄️ 42 queries │ 💾 28MB │ 📦 12 modules    ││   │
│  │  │                                                        ││   │
│  │  │ Module      │ Queries │ Time    │ Memory              ││   │
│  │  │ ────────────┼─────────┼─────────┼─────────────────────││   │
│  │  │ Admin       │ 12      │ 45ms    │ 8MB                 ││   │
│  │  │ Lead        │ 8       │ 32ms    │ 4MB                 ││   │
│  │  │ Contact     │ 6       │ 28ms    │ 3MB                 ││   │
│  │  │ CustomTheme │ 4       │ 15ms    │ 2MB     ← SEU PKG   ││   │
│  │  │ ...         │ ...     │ ...     │ ...                 ││   │
│  │  └────────────────────────────────────────────────────────┘│   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### Instalação

```bash
# 1. Instalar o pacote
composer require krayin/krayin-debug-bar

# 2. Cachear rotas
php artisan route:cache

# 3. Acessar qualquer página - a barra aparece no rodapé
```

### Como Ajuda no Seu Cenário

#### Problema 1: "Meu package custom está deixando o sistema lento?"

```
COM DEBUG BAR:
──────────────
1. Adicionar seu package CustomTheme
2. Navegar pelas páginas com Debug Bar ativa
3. Observar a coluna do seu module

SE CustomTheme mostra: 2 queries, 15ms
   ✅ Seu package está performático!

SE CustomTheme mostra: 50 queries, 500ms
   ⚠️ Problema de performance! Investigar:
   - N+1 queries no model?
   - Listener muito pesado?
   - Eager loading faltando?
```

#### Problema 2: "A página está lenta, mas não sei qual módulo é o culpado"

```
COM DEBUG BAR:
──────────────
1. Abrir página lenta
2. Observar Debug Bar

Módulo      │ Queries │ Time
────────────┼─────────┼────────
Admin       │ 12      │ 45ms    ← OK
Lead        │ 85      │ 850ms   ← 🚨 PROBLEMA!
Contact     │ 6       │ 28ms    ← OK

Diagnóstico: O módulo Lead está fazendo 85 queries!
Ação: Investigar LeadRepository, verificar eager loading
```

#### Problema 3: "Deploy em produção - preciso validar performance"

```
WORKFLOW COM DEBUG BAR:
───────────────────────
1. Deploy em staging com Debug Bar ativa
2. Navegar por todas as telas principais
3. Anotar métricas por módulo
4. Comparar com baseline anterior

Página      │ Queries │ Time   │ Status
────────────┼─────────┼────────┼─────────
Login       │ 8       │ 120ms  │ ✅ OK
Dashboard   │ 45      │ 380ms  │ ⚠️ Investigar
Lead List   │ 32      │ 250ms  │ ✅ OK
Lead View   │ 18      │ 180ms  │ ✅ OK

→ Investigar dashboard antes de ir para produção
```

### Métricas Disponíveis

| Métrica | O que mostra | Para que serve |
|---------|--------------|----------------|
| **Queries** | Número de consultas SQL | Identificar N+1, queries excessivas |
| **Time** | Tempo de execução | Identificar gargalos |
| **Memory** | Uso de memória | Identificar leaks |
| **Modules** | Stats por package | Isolar problemas por módulo |
| **Route** | Rota resolvida | Confirmar controller correto |
| **Request** | Dados do request | Debug de formulários |

### ⚠️ Importante: Apenas em DEV/Staging

```php
// NÃO usar em produção!
// A Debug Bar adiciona overhead e expõe informações internas

// Instalar como dev dependency:
composer require krayin/krayin-debug-bar --dev

// Ou configurar para carregar apenas em dev
// (verificar .env APP_DEBUG=true)
```

---

## 🔄 FLUXO DE TRABALHO INTEGRADO

### As 3 Ferramentas Trabalhando Juntas

```
┌─────────────────────────────────────────────────────────────────────┐
│                FLUXO INTEGRADO DE DESENVOLVIMENTO                   │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌────────────────────────────────────────────────────────────┐    │
│  │                    1. DESCOBRIR                            │    │
│  │                    ────────────                            │    │
│  │                                                            │    │
│  │    "Preciso customizar a tela de leads"                    │    │
│  │                                                            │    │
│  │    🔍 BLADE TRACER                                         │    │
│  │    → Hover sobre card de lead                              │    │
│  │    → Descobre: packages/Webkul/Lead/Resources/views/...    │    │
│  │                                                            │    │
│  └────────────────────────────────────────────────────────────┘    │
│                              ↓                                      │
│  ┌────────────────────────────────────────────────────────────┐    │
│  │                    2. CRIAR                                │    │
│  │                    ────────                                │    │
│  │                                                            │    │
│  │    "Preciso de um package para minhas customizações"       │    │
│  │                                                            │    │
│  │    🏭 PACKAGE GENERATOR                                    │    │
│  │    → php artisan package:make Webkul/CustomTheme           │    │
│  │    → php artisan package:make-model CustomLead ...         │    │
│  │    → Estrutura pronta em segundos!                         │    │
│  │                                                            │    │
│  └────────────────────────────────────────────────────────────┘    │
│                              ↓                                      │
│  ┌────────────────────────────────────────────────────────────┐    │
│  │                    3. IMPLEMENTAR                          │    │
│  │                    ──────────────                          │    │
│  │                                                            │    │
│  │    "Agora vou fazer os overrides e listeners"              │    │
│  │                                                            │    │
│  │    📝 SEU CÓDIGO (baseado na Anatomia Krayin)              │    │
│  │    → Copiar view descoberta pelo Blade Tracer              │    │
│  │    → Criar override no package gerado                      │    │
│  │    → Implementar listeners, controllers, models            │    │
│  │                                                            │    │
│  └────────────────────────────────────────────────────────────┘    │
│                              ↓                                      │
│  ┌────────────────────────────────────────────────────────────┐    │
│  │                    4. VALIDAR                              │    │
│  │                    ──────────                              │    │
│  │                                                            │    │
│  │    "Minha customização está funcionando?"                  │    │
│  │                                                            │    │
│  │    🔍 BLADE TRACER                                         │    │
│  │    → Hover deve mostrar SEU package, não o original        │    │
│  │    → Se mostra original = override não funcionou           │    │
│  │                                                            │    │
│  │    📊 DEBUG BAR                                            │    │
│  │    → Verificar performance do seu package                  │    │
│  │    → Queries, tempo, memória estão ok?                     │    │
│  │                                                            │    │
│  └────────────────────────────────────────────────────────────┘    │
│                              ↓                                      │
│  ┌────────────────────────────────────────────────────────────┐    │
│  │                    5. DEPLOY                               │    │
│  │                    ────────                                │    │
│  │                                                            │    │
│  │    "Vou subir para produção"                               │    │
│  │                                                            │    │
│  │    🐳 DOCKER SWARM                                         │    │
│  │    → docker build (sem debug tools)                        │    │
│  │    → docker push                                           │    │
│  │    → docker service update                                 │    │
│  │                                                            │    │
│  └────────────────────────────────────────────────────────────┘    │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### Exemplo Prático Completo

```bash
# ═══════════════════════════════════════════════════════════════════
# CENÁRIO: Customizar card de lead com campo "prioridade"
# ═══════════════════════════════════════════════════════════════════

# PASSO 1: DESCOBRIR (Blade Tracer)
# ─────────────────────────────────
# Acessar /admin/leads
# Hover sobre card de lead
# Resultado: packages/Webkul/Lead/Resources/views/leads/view/cards/info.blade.php

# PASSO 2: CRIAR (Package Generator)
# ──────────────────────────────────
php artisan package:make Webkul/CustomLead
php artisan package:make-model Lead Webkul/CustomLead

# Registrar no composer.json e config/modules.php
# composer dump-autoload

# PASSO 3: IMPLEMENTAR
# ────────────────────
# Copiar view descoberta:
mkdir -p packages/Webkul/CustomLead/Resources/views/leads/view/cards/
cp packages/Webkul/Lead/Resources/views/leads/view/cards/info.blade.php \
   packages/Webkul/CustomLead/Resources/views/leads/view/cards/info.blade.php

# Editar view com campo prioridade:
# ... adicionar HTML do campo prioridade ...

# Override model para adicionar prioridade ao $fillable:
# ... editar packages/Webkul/CustomLead/src/Models/Lead.php ...

# Registrar override no ServiceProvider:
# ... $this->app->concord->registerModel(...) ...

# Publicar e limpar cache:
php artisan vendor:publish --tag=customlead-views
php artisan optimize:clear

# PASSO 4: VALIDAR (Blade Tracer + Debug Bar)
# ───────────────────────────────────────────
# Acessar /admin/leads
# Hover sobre card → deve mostrar "packages/Webkul/CustomLead/..."  ✅
# Debug Bar → verificar que CustomLead tem poucas queries  ✅

# PASSO 5: DEPLOY
# ───────────────
docker build -t projeto:v1.0.0 .
docker push registry/projeto:v1.0.0
docker service update --image registry/projeto:v1.0.0 krayin_krayin
```

---

## 📊 MATRIZ DE USO POR CENÁRIO

### Quando Usar Cada Ferramenta?

| Cenário | Blade Tracer | Package Generator | Debug Bar |
|---------|:------------:|:-----------------:|:---------:|
| **Localizar view para customizar** | ✅ | ❌ | ❌ |
| **Criar novo package** | ❌ | ✅ | ❌ |
| **Criar model/controller/etc** | ❌ | ✅ | ❌ |
| **Validar que override funcionou** | ✅ | ❌ | ❌ |
| **Identificar problema de performance** | ❌ | ❌ | ✅ |
| **Comparar performance antes/depois** | ❌ | ❌ | ✅ |
| **Onboarding de novo dev** | ✅ | ❌ | ❌ |
| **Mapear estrutura do projeto** | ✅ | ❌ | ❌ |
| **Padronizar criação de packages** | ❌ | ✅ | ❌ |
| **Debug de queries lentas** | ❌ | ❌ | ✅ |

### Frequência de Uso

```
┌─────────────────────────────────────────────────────────────────────┐
│                    FREQUÊNCIA DE USO                                │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  BLADE TRACER                                                       │
│  ─────────────                                                      │
│  ████████████████████████████░░░░░░░░░░  70%                       │
│  Uso: Constante durante desenvolvimento de views                   │
│                                                                     │
│  PACKAGE GENERATOR                                                  │
│  ─────────────────                                                  │
│  ████████░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░  20%                       │
│  Uso: Apenas quando cria novo package/artefato                     │
│                                                                     │
│  DEBUG BAR                                                          │
│  ─────────                                                          │
│  ████░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░  10%                       │
│  Uso: Ocasional, para diagnóstico de performance                   │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 📦 INSTALAÇÃO COMPLETA DO TOOLKIT

### Script de Setup do Ambiente de Desenvolvimento

```bash
#!/bin/bash
# setup-dev-tools.sh
# Instala todas as ferramentas de desenvolvimento Krayin

set -e

echo "═══════════════════════════════════════════════════════════════════"
echo "  🛠️  KRAYIN DEV TOOLKIT - INSTALAÇÃO COMPLETA"
echo "═══════════════════════════════════════════════════════════════════"

# 1. Blade Tracer
echo ""
echo "📦 Instalando Blade Tracer..."
composer require krayin/krayin-blade-tracer --dev

# 2. Package Generator
echo ""
echo "📦 Instalando Package Generator..."
composer require krayin/krayin-package-generator --dev

# 3. Debug Bar
echo ""
echo "📦 Instalando Debug Bar..."
composer require krayin/krayin-debug-bar --dev

# 4. Limpar caches
echo ""
echo "🧹 Limpando caches..."
php artisan view:clear
php artisan route:cache
php artisan optimize:clear

echo ""
echo "═══════════════════════════════════════════════════════════════════"
echo "  ✅ INSTALAÇÃO COMPLETA!"
echo "═══════════════════════════════════════════════════════════════════"
echo ""
echo "  Ferramentas instaladas:"
echo "  • Blade Tracer    → Passe o mouse sobre elementos para ver views"
echo "  • Package Generator → php artisan package:make ..."
echo "  • Debug Bar       → Barra de debug no rodapé das páginas"
echo ""
echo "  ⚠️  Lembre-se: Use apenas em ambiente de DESENVOLVIMENTO!"
echo ""
```

### Verificar Instalação

```bash
# Verificar comandos do Package Generator
php artisan list | grep package

# Saída esperada:
# package:make              Create a new package
# package:make-controller   Create a new controller
# package:make-model        Create a new model
# package:make-repository   Create a new repository
# ...

# Verificar que Blade Tracer está ativo
# Acesse qualquer página e passe o mouse - tooltip deve aparecer

# Verificar que Debug Bar está ativa
# Acesse qualquer página - barra deve aparecer no rodapé
```

### Configuração no composer.json

```json
{
  "require": {
    "php": "^8.1",
    "webkul/krayin": "^1.0"
  },
  "require-dev": {
    "krayin/krayin-blade-tracer": "^1.0",
    "krayin/krayin-package-generator": "^1.0",
    "krayin/krayin-debug-bar": "^1.0"
  }
}
```

### Dockerfile (Sem Ferramentas DEV em Produção)

```dockerfile
# Em produção, instalar SEM dev dependencies
RUN composer install \
    --no-dev \            # <-- Isso exclui as ferramentas de dev
    --no-interaction \
    --optimize-autoloader
```

---

## ✅ BOAS PRÁTICAS E RECOMENDAÇÕES

### Do's ✅

```
✅ Instalar ferramentas como --dev dependency
✅ Usar Blade Tracer para mapear TODAS as views antes de customizar
✅ Usar Package Generator para TODOS os packages novos
✅ Verificar Debug Bar antes de ir para produção
✅ Documentar mapeamento de views descoberto pelo Blade Tracer
✅ Remover/desabilitar ferramentas em produção
```

### Don'ts ❌

```
❌ Instalar ferramentas em produção
❌ Commitar .env com APP_DEBUG=true
❌ Criar packages manualmente quando o Generator está disponível
❌ Ignorar alertas de performance da Debug Bar
❌ Expor caminhos de arquivos para usuários finais
```

### Checklist de Uso

```
☐ Ambiente de desenvolvimento configurado?
   ├── APP_ENV=local
   ├── APP_DEBUG=true
   └── Ferramentas instaladas como --dev

☐ Blade Tracer funcionando?
   └── Hover sobre elemento mostra path?

☐ Package Generator funcionando?
   └── php artisan package:make ... funciona?

☐ Debug Bar funcionando?
   └── Barra aparece no rodapé das páginas?

☐ Antes de deploy para produção:
   ├── composer install --no-dev
   ├── APP_DEBUG=false
   └── Ferramentas não são carregadas
```

---

## 📚 REFERÊNCIAS

| Ferramenta | GitHub | Packagist |
|------------|--------|-----------|
| Blade Tracer | [github.com/krayin/krayin-blade-tracer](https://github.com/krayin/krayin-blade-tracer) | `krayin/krayin-blade-tracer` |
| Package Generator | [github.com/krayin/krayin-package-generator](https://github.com/krayin/krayin-package-generator) | `krayin/krayin-package-generator` |
| Debug Bar | [github.com/krayin/krayin-debug-bar](https://github.com/krayin/krayin-debug-bar) | `krayin/krayin-debug-bar` |

---

## 🏆 RESUMO EXECUTIVO

```
╔═══════════════════════════════════════════════════════════════════════════════╗
║                    TOOLKIT DE DESENVOLVIMENTO KRAYIN                          ║
╠═══════════════════════════════════════════════════════════════════════════════╣
║                                                                               ║
║  🔍 BLADE TRACER                                                              ║
║     Para: Localizar views                                                     ║
║     Validade: ✅ ALTA (obrigatório)                                           ║
║     Quando: Sempre que precisar descobrir qual arquivo customizar            ║
║                                                                               ║
║  🏭 PACKAGE GENERATOR                                                         ║
║     Para: Criar packages e artefatos                                         ║
║     Validade: ✅ ALTA (obrigatório)                                           ║
║     Quando: Sempre que criar novo package, model, controller, etc.           ║
║                                                                               ║
║  📊 DEBUG BAR                                                                 ║
║     Para: Monitorar performance                                              ║
║     Validade: ✅ MÉDIA (recomendado)                                          ║
║     Quando: Diagnóstico de performance, validação pré-deploy                 ║
║                                                                               ║
║  ═══════════════════════════════════════════════════════════════════════════ ║
║                                                                               ║
║  FLUXO: DESCOBRIR → CRIAR → IMPLEMENTAR → VALIDAR → DEPLOY                   ║
║         (Tracer)   (Generator) (Código)   (Tracer+Bar) (Docker)              ║
║                                                                               ║
║  REGRA DE OURO: Apenas em DEV, nunca em PROD!                                ║
║                                                                               ║
╚═══════════════════════════════════════════════════════════════════════════════╝
```

---

**Versão:** 1.0.0  
**Data:** Dezembro 2025  
**Status:** ✅ VALIDADO PARA USO EM DESENVOLVIMENTO  

---

*"Ferramentas certas, desenvolvimento eficiente."*
