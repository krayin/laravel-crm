# 🎯 PROCESSO PADRÃO DE CUSTOMIZAÇÃO KRAYIN
## Playbook Executivo - Do Pedido ao Deploy

**Versão:** 1.0.0  
**Data:** Dezembro 2025  
**Objetivo:** Fluxo único, seguro e previsível para QUALQUER customização  
**Resultado:** Zero surpresas, entregas consistentes, qualidade garantida

---

## 📋 ÍNDICE

1. [Visão Geral do Processo](#-visão-geral-do-processo)
2. [Mapa de Documentação](#-mapa-de-documentação)
3. [Os 7 Gates de Qualidade](#-os-7-gates-de-qualidade)
4. [Processo Detalhado](#-processo-detalhado)
5. [Matriz de Decisão Rápida](#-matriz-de-decisão-rápida)
6. [Templates de Execução](#-templates-de-execução)
7. [Métricas e Controle](#-métricas-e-controle)

---

## 🎯 VISÃO GERAL DO PROCESSO

### O Fluxo Completo

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    PROCESSO DE CUSTOMIZAÇÃO KRAYIN                          │
│                         "Do Pedido ao Deploy"                               │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  ┌─────────┐     ┌─────────┐     ┌─────────┐     ┌─────────┐     ┌─────────┐│
│  │ GATE 1  │     │ GATE 2  │     │ GATE 3  │     │ GATE 4  │     │ GATE 5  ││
│  │Qualifica│     │ Mapeia  │     │ Planeja │     │ Executa │     │ Valida  ││
│  └────┬────┘     └────┬────┘     └────┬────┘     └────┬────┘     └────┬────┘│
│       │               │               │               │               │     │
│       ▼               ▼               ▼               ▼               ▼     │
│  ┌─────────┐     ┌─────────┐     ┌─────────┐     ┌─────────┐     ┌─────────┐│
│  │ RECEBER │────▶│ ENTENDER│────▶│ DEFINIR │────▶│CONSTRUIR│────▶│ TESTAR  ││
│  │ DEMANDA │     │ ESCOPO  │     │ SOLUÇÃO │     │ CÓDIGO  │     │ SOLUÇÃO ││
│  └─────────┘     └─────────┘     └─────────┘     └─────────┘     └─────────┘│
│       │               │               │               │               │     │
│       │               │               │               │               │     │
│       ▼               ▼               ▼               ▼               ▼     │
│  ┌─────────┐     ┌─────────┐     ┌─────────┐     ┌─────────┐     ┌─────────┐│
│  │ É viável│     │Requisito│     │Arquitetu│     │ Código  │     │ Testes  ││
│  │ fazer?  │     │ claro?  │     │ra ok?   │     │ pronto? │     │ passam? ││
│  └────┬────┘     └────┬────┘     └────┬────┘     └────┬────┘     └────┬────┘│
│       │               │               │               │               │     │
│  ┌────┴────┐     ┌────┴────┐     ┌────┴────┐     ┌────┴────┐     ┌────┴────┐│
│  │   SIM   │     │   SIM   │     │   SIM   │     │   SIM   │     │   SIM   ││
│  └────┬────┘     └────┬────┘     └────┬────┘     └────┬────┘     └────┬────┘│
│       │               │               │               │               │     │
│       ▼               ▼               ▼               ▼               ▼     │
│  ┌─────────┐     ┌─────────┐     ┌─────────┐     ┌─────────┐     ┌─────────┐│
│  │ GATE 6  │     │         │     │         │     │         │     │ GATE 7  ││
│  │ Deploy  │     │         │     │         │     │         │     │ Entrega ││
│  └────┬────┘     │         │     │         │     │         │     └────┬────┘│
│       │          │         │     │         │     │         │          │     │
│       ▼          │         │     │         │     │         │          ▼     │
│  ┌─────────┐     │         │     │         │     │         │     ┌─────────┐│
│  │ DEPLOY  │     │         │     │         │     │         │     │PRODUÇÃO ││
│  │ STAGING │────▶│─────────┴─────┴─────────┴─────┴─────────┴────▶│  LIVE!  ││
│  └─────────┘                                                     └─────────┘│
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

### Princípios do Processo

```
┌─────────────────────────────────────────────────────────────────────┐
│                    5 PRINCÍPIOS INEGOCIÁVEIS                        │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  1️⃣  NENHUM CÓDIGO SEM ESCOPO APROVADO                              │
│      → Primeiro mapear, depois desenvolver                          │
│                                                                     │
│  2️⃣  NENHUM OVERRIDE SEM LOCALIZAÇÃO CONFIRMADA                     │
│      → Sempre usar Blade Tracer antes de criar arquivo              │
│                                                                     │
│  3️⃣  NENHUM DEPLOY SEM TESTE EM STAGING                             │
│      → Staging é obrigatório, não opcional                          │
│                                                                     │
│  4️⃣  NUNCA EDITAR CORE                                              │
│      → Apenas overrides em packages próprios                        │
│                                                                     │
│  5️⃣  SEMPRE DOCUMENTAR                                              │
│      → O que não está documentado, não existe                       │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 📚 MAPA DE DOCUMENTAÇÃO

### Qual Documento Usar em Cada Momento

```
┌─────────────────────────────────────────────────────────────────────┐
│                    MAPA DE USO DA DOCUMENTAÇÃO                      │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  MOMENTO                          DOCUMENTO                         │
│  ────────────────────────────────────────────────────────────────   │
│                                                                     │
│  "Recebi um pedido de            MAPEAMENTO_DEMANDA_PROJETO.md      │
│   customização"                  → Questionário de Descoberta       │
│                                  → Matriz MoSCoW                    │
│                                                                     │
│  "Preciso entender o que         MAPEAMENTO_DEMANDA_PROJETO.md      │
│   será customizado"              → Inventários (Telas, Campos...)   │
│                                  → Análise de Impacto               │
│                                                                     │
│  "Preciso definir como           ANATOMIA_GERAL_KRAYIN_CRM.md       │
│   fazer tecnicamente"            → Parte III: Sistema de Overrides  │
│                                  → Parte IV: Sistema de Eventos     │
│                                                                     │
│  "Vou começar a                  FERRAMENTAS_DEV_KRAYIN.md          │
│   desenvolver"                   → Blade Tracer (localizar views)   │
│                                  → Package Generator (criar pkg)    │
│                                                                     │
│  "Preciso criar um               ANATOMIA_GERAL_KRAYIN_CRM.md       │
│   package"                       → Parte II: Anatomia de Packages   │
│                                  → Anexo: Blueprints                │
│                                                                     │
│  "Preciso fazer override"        ANATOMIA_GERAL_KRAYIN_CRM.md       │
│                                  → Parte III: Sistema de Overrides  │
│                                  → Controller / Model / View        │
│                                                                     │
│  "Preciso criar listener"        ANATOMIA_GERAL_KRAYIN_CRM.md       │
│                                  → Parte IV: Sistema de Eventos     │
│                                  → Catálogo de Eventos              │
│                                                                     │
│  "Preciso customizar visual"     ANATOMIA_GERAL_KRAYIN_CRM.md       │
│                                  → Parte V: Customização Visual     │
│                                  → CSS Variables, Assets            │
│                                                                     │
│  "Vou revisar código"            CHECKLIST_VALIDACAO_DEV.md         │
│                                  → 61 perguntas por área            │
│                                  → Scorecard de validação           │
│                                                                     │
│  "Vou fazer deploy"              ANATOMIA_GERAL_KRAYIN_CRM.md       │
│                                  → Parte VI: Infraestrutura Docker  │
│                                  → Parte VII: Operações             │
│                                                                     │
│  "Algo não está                  ANATOMIA_GERAL_KRAYIN_CRM.md       │
│   funcionando"                   → Parte VII: Troubleshooting       │
│                                  → Comandos de diagnóstico          │
│                                                                     │
│  "Novo dev entrou                ONBOARDING_DEV_KRAYIN.md           │
│   no projeto"                    → Programa completo (3-5 dias)     │
│                                  → 8 fases progressivas             │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### Referência Cruzada Rápida

| Preciso de... | Documento | Seção |
|---------------|-----------|-------|
| Entender requisitos | MAPEAMENTO_DEMANDA | Fase 1-2 |
| Definir abordagem técnica | MAPEAMENTO_DEMANDA | Fase 3-4 |
| Criar package | ANATOMIA_GERAL | Parte II |
| Override de controller | ANATOMIA_GERAL | Parte III |
| Override de model | ANATOMIA_GERAL | Parte III |
| Override de view | ANATOMIA_GERAL | Parte III |
| Criar listener | ANATOMIA_GERAL | Parte IV |
| Customizar CSS | ANATOMIA_GERAL | Parte V |
| Configurar Docker | ANATOMIA_GERAL | Parte VI |
| Localizar view | FERRAMENTAS_DEV | Blade Tracer |
| Gerar artefatos | FERRAMENTAS_DEV | Package Generator |
| Verificar performance | FERRAMENTAS_DEV | Debug Bar |
| Validar código | CHECKLIST_VALIDACAO | Seções 1-9 |
| Treinar dev | ONBOARDING | Fases 1-8 |

---

## 🚦 OS 7 GATES DE QUALIDADE

### Gate = Ponto de Verificação Obrigatório

```
┌─────────────────────────────────────────────────────────────────────┐
│                    OS 7 GATES DE QUALIDADE                          │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  Cada GATE é um checkpoint. NÃO avança sem passar.                  │
│                                                                     │
│  ═══════════════════════════════════════════════════════════════   │
│                                                                     │
│  GATE 1: QUALIFICAÇÃO DA DEMANDA                                    │
│  ────────────────────────────────                                   │
│  Pergunta: "Devemos fazer isso?"                                    │
│                                                                     │
│  ☐ Demanda documentada (email, ticket, ata)                         │
│  ☐ Sponsor identificado                                             │
│  ☐ Alinhamento com objetivos do negócio                             │
│  ☐ Viabilidade técnica inicial confirmada                           │
│  ☐ Não conflita com projetos em andamento                           │
│                                                                     │
│  ✅ PASSOU? → Avança para Gate 2                                    │
│  ❌ NÃO PASSOU? → Recusar ou renegociar escopo                      │
│                                                                     │
│  ═══════════════════════════════════════════════════════════════   │
│                                                                     │
│  GATE 2: MAPEAMENTO COMPLETO                                        │
│  ───────────────────────────                                        │
│  Pergunta: "Entendemos o que precisa ser feito?"                    │
│                                                                     │
│  ☐ Questionário de descoberta preenchido                            │
│  ☐ Matriz MoSCoW definida                                           │
│  ☐ Inventário de telas completo (Blade Tracer usado)                │
│  ☐ Inventário de campos completo                                    │
│  ☐ Inventário de fluxos/eventos completo                            │
│  ☐ Integrações mapeadas                                             │
│  ☐ Assets visuais coletados                                         │
│                                                                     │
│  ✅ PASSOU? → Avança para Gate 3                                    │
│  ❌ NÃO PASSOU? → Voltar e completar mapeamento                     │
│                                                                     │
│  ═══════════════════════════════════════════════════════════════   │
│                                                                     │
│  GATE 3: ARQUITETURA APROVADA                                       │
│  ────────────────────────────                                       │
│  Pergunta: "Sabemos COMO vamos fazer?"                              │
│                                                                     │
│  ☐ Abordagem técnica definida para cada item                        │
│  ☐ Packages a criar definidos                                       │
│  ☐ Complexidade estimada                                            │
│  ☐ Riscos identificados com mitigações                              │
│  ☐ Cronograma realista                                              │
│  ☐ Recursos alocados                                                │
│  ☐ Documento de Escopo (SOW) aprovado pelo sponsor                  │
│                                                                     │
│  ✅ PASSOU? → Avança para Gate 4                                    │
│  ❌ NÃO PASSOU? → Revisar arquitetura ou renegociar escopo          │
│                                                                     │
│  ═══════════════════════════════════════════════════════════════   │
│                                                                     │
│  GATE 4: CÓDIGO PRONTO                                              │
│  ─────────────────────                                              │
│  Pergunta: "O código está completo e correto?"                      │
│                                                                     │
│  ☐ Package(s) criado(s) com Package Generator                       │
│  ☐ Estrutura segue padrão canônico                                  │
│  ☐ Overrides implementados corretamente                             │
│  ☐ Listeners funcionando (testado com tinker)                       │
│  ☐ Views validadas com Blade Tracer                                 │
│  ☐ Assets publicados                                                │
│  ☐ Traduções completas                                              │
│  ☐ Code review realizado                                            │
│  ☐ CHECKLIST_VALIDACAO_DEV > 47 pontos                              │
│                                                                     │
│  ✅ PASSOU? → Avança para Gate 5                                    │
│  ❌ NÃO PASSOU? → Corrigir código                                   │
│                                                                     │
│  ═══════════════════════════════════════════════════════════════   │
│                                                                     │
│  GATE 5: TESTES APROVADOS                                           │
│  ────────────────────────                                           │
│  Pergunta: "Funciona corretamente?"                                 │
│                                                                     │
│  ☐ Testes unitários passando (se aplicável)                         │
│  ☐ Testes funcionais passando                                       │
│  ☐ Validação visual OK                                              │
│  ☐ Performance verificada (Debug Bar)                               │
│  ☐ Logs sem erros                                                   │
│  ☐ Critérios de aceite atendidos                                    │
│                                                                     │
│  ✅ PASSOU? → Avança para Gate 6                                    │
│  ❌ NÃO PASSOU? → Corrigir bugs                                     │
│                                                                     │
│  ═══════════════════════════════════════════════════════════════   │
│                                                                     │
│  GATE 6: STAGING VALIDADO                                           │
│  ────────────────────────                                           │
│  Pergunta: "Funciona em ambiente real?"                             │
│                                                                     │
│  ☐ Build Docker sem erros                                           │
│  ☐ Deploy em staging OK                                             │
│  ☐ Aplicação responde (HTTP 200)                                    │
│  ☐ Login funciona                                                   │
│  ☐ Assets carregam                                                  │
│  ☐ Customizações visíveis                                           │
│  ☐ Integrações funcionando                                          │
│  ☐ Logs limpos                                                      │
│  ☐ Stakeholder validou em staging                                   │
│                                                                     │
│  ✅ PASSOU? → Avança para Gate 7                                    │
│  ❌ NÃO PASSOU? → Corrigir e re-deployar staging                    │
│                                                                     │
│  ═══════════════════════════════════════════════════════════════   │
│                                                                     │
│  GATE 7: PRODUÇÃO LIBERADA                                          │
│  ───────────────────────────                                        │
│  Pergunta: "Podemos ir para produção?"                              │
│                                                                     │
│  ☐ Aprovação formal do stakeholder                                  │
│  ☐ Janela de deploy definida                                        │
│  ☐ Plano de rollback pronto                                         │
│  ☐ Backup de produção realizado                                     │
│  ☐ Equipe de plantão definida                                       │
│  ☐ Comunicação aos usuários (se necessário)                         │
│  ☐ Documentação atualizada                                          │
│                                                                     │
│  ✅ PASSOU? → DEPLOY PRODUÇÃO                                       │
│  ❌ NÃO PASSOU? → Resolver pendências                               │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 📋 PROCESSO DETALHADO

### ETAPA 1: RECEBER DEMANDA
**Tempo:** 1-2 horas  
**Documento:** MAPEAMENTO_DEMANDA → Fase 1

```
┌─────────────────────────────────────────────────────────────────────┐
│  ETAPA 1: RECEBER DEMANDA                                           │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ENTRADA:                                                           │
│  • Solicitação (email, ticket, reunião)                             │
│                                                                     │
│  AÇÕES:                                                             │
│  1. Registrar demanda formalmente                                   │
│  2. Identificar sponsor e stakeholders                              │
│  3. Agendar reunião de descoberta                                   │
│  4. Enviar questionário prévio (opcional)                           │
│                                                                     │
│  SAÍDA:                                                             │
│  • Demanda registrada                                               │
│  • Reunião agendada                                                 │
│                                                                     │
│  GATE 1: Demanda qualificada? ☐ Sim ☐ Não                          │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

### ETAPA 2: MAPEAR REQUISITOS
**Tempo:** 1-3 dias  
**Documento:** MAPEAMENTO_DEMANDA → Fases 1-2

```
┌─────────────────────────────────────────────────────────────────────┐
│  ETAPA 2: MAPEAR REQUISITOS                                         │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ENTRADA:                                                           │
│  • Demanda qualificada                                              │
│                                                                     │
│  AÇÕES:                                                             │
│  1. Realizar reunião de descoberta                                  │
│     → Preencher Questionário de Descoberta                          │
│     → Definir Matriz MoSCoW                                         │
│                                                                     │
│  2. Fazer levantamento técnico                                      │
│     → Acessar sistema com Blade Tracer ativo                        │
│     → Preencher Inventário de Telas                                 │
│     → Preencher Inventário de Campos                                │
│     → Preencher Inventário de Fluxos                                │
│     → Preencher Inventário de Integrações                           │
│     → Coletar assets visuais                                        │
│                                                                     │
│  SAÍDA:                                                             │
│  • Mapeamento completo preenchido                                   │
│  • Telas identificadas com paths                                    │
│                                                                     │
│  GATE 2: Mapeamento completo? ☐ Sim ☐ Não                          │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

### ETAPA 3: DEFINIR ARQUITETURA
**Tempo:** 1-2 dias  
**Documento:** MAPEAMENTO_DEMANDA → Fases 3-4, ANATOMIA_GERAL

```
┌─────────────────────────────────────────────────────────────────────┐
│  ETAPA 3: DEFINIR ARQUITETURA                                       │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ENTRADA:                                                           │
│  • Mapeamento completo                                              │
│                                                                     │
│  AÇÕES:                                                             │
│  1. Analisar impacto técnico                                        │
│     → Definir abordagem para cada item (ver Matriz Decisão)         │
│     → Estimar complexidade (1-5)                                    │
│     → Identificar riscos                                            │
│     → Mapear dependências                                           │
│                                                                     │
│  2. Desenhar solução                                                │
│     → Definir packages a criar                                      │
│     → Listar componentes de cada package                            │
│     → Registrar decisões arquiteturais                              │
│                                                                     │
│  3. Estimar e planejar                                              │
│     → Calcular horas por item                                       │
│     → Montar cronograma                                             │
│     → Alocar recursos                                               │
│                                                                     │
│  4. Formalizar                                                      │
│     → Criar Documento de Escopo (SOW)                               │
│     → Obter aprovação do sponsor                                    │
│                                                                     │
│  SAÍDA:                                                             │
│  • SOW aprovado                                                     │
│  • Cronograma definido                                              │
│  • Backlog criado                                                   │
│                                                                     │
│  GATE 3: Arquitetura aprovada? ☐ Sim ☐ Não                         │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

### ETAPA 4: DESENVOLVER
**Tempo:** Variável (conforme estimativa)  
**Documento:** ANATOMIA_GERAL, FERRAMENTAS_DEV

```
┌─────────────────────────────────────────────────────────────────────┐
│  ETAPA 4: DESENVOLVER                                               │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ENTRADA:                                                           │
│  • SOW aprovado                                                     │
│  • Backlog priorizado                                               │
│                                                                     │
│  AÇÕES:                                                             │
│                                                                     │
│  4.1 SETUP INICIAL (uma vez por projeto)                            │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ # Criar package com generator                                 │  │
│  │ php artisan package:make Webkul/NomeProjeto                   │  │
│  │                                                               │  │
│  │ # Configurar autoload                                         │  │
│  │ # Editar composer.json raiz → adicionar PSR-4                 │  │
│  │                                                               │  │
│  │ # Registrar package                                           │  │
│  │ # Editar config/modules.php → adicionar por ÚLTIMO            │  │
│  │                                                               │  │
│  │ # Regenerar autoload                                          │  │
│  │ composer dump-autoload                                        │  │
│  │ php artisan optimize:clear                                    │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                     │
│  4.2 PARA CADA ITEM DO BACKLOG                                      │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ A) SE FOR VIEW OVERRIDE:                                      │  │
│  │    1. Usar Blade Tracer → confirmar path                      │  │
│  │    2. Copiar view mantendo estrutura                          │  │
│  │    3. Editar view                                             │  │
│  │    4. vendor:publish                                          │  │
│  │    5. view:clear                                              │  │
│  │    6. Validar com Blade Tracer                                │  │
│  │                                                               │  │
│  │ B) SE FOR CONTROLLER OVERRIDE:                                │  │
│  │    1. Criar controller em src/Http/Controllers/               │  │
│  │    2. Estender controller original                            │  │
│  │    3. Manter eventos before/after                             │  │
│  │    4. Registrar com $this->app->bind() no register()          │  │
│  │    5. optimize:clear                                          │  │
│  │    6. Testar                                                  │  │
│  │                                                               │  │
│  │ C) SE FOR MODEL OVERRIDE:                                     │  │
│  │    1. Criar model em src/Models/                              │  │
│  │    2. Estender model original                                 │  │
│  │    3. Adicionar campos ao $fillable                           │  │
│  │    4. Criar migration (se novos campos)                       │  │
│  │    5. Registrar com concord->registerModel() no boot()        │  │
│  │       → USAR CONTRACT, não classe!                            │  │
│  │    6. Rodar migration                                         │  │
│  │    7. Testar com tinker                                       │  │
│  │                                                               │  │
│  │ D) SE FOR EVENT LISTENER:                                     │  │
│  │    1. Criar EventServiceProvider                              │  │
│  │    2. Criar Listener em src/Listeners/                        │  │
│  │    3. Registrar evento no EventServiceProvider                │  │
│  │    4. Registrar EventServiceProvider no boot() principal      │  │
│  │    5. optimize:clear                                          │  │
│  │    6. Testar com tinker: event('nome.evento', $data)          │  │
│  │                                                               │  │
│  │ E) SE FOR CUSTOMIZAÇÃO VISUAL:                                │  │
│  │    1. Criar CSS em Resources/assets/css/                      │  │
│  │    2. Colocar imagens em Resources/assets/images/             │  │
│  │    3. Configurar publishes() no ServiceProvider               │  │
│  │    4. vendor:publish --tag=nome-assets --force                │  │
│  │    5. Referenciar assets nas views                            │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                     │
│  4.3 CODE REVIEW                                                    │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ • Usar CHECKLIST_VALIDACAO_DEV                                │  │
│  │ • Score mínimo: 47/61 pontos                                  │  │
│  │ • Revisar: estrutura, overrides, eventos, assets              │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                     │
│  SAÍDA:                                                             │
│  • Código completo e revisado                                       │
│  • Checklist com score > 47                                         │
│                                                                     │
│  GATE 4: Código pronto? ☐ Sim ☐ Não                                │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

### ETAPA 5: TESTAR
**Tempo:** 20% do tempo de desenvolvimento  
**Documento:** CHECKLIST_VALIDACAO_DEV

```
┌─────────────────────────────────────────────────────────────────────┐
│  ETAPA 5: TESTAR                                                    │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ENTRADA:                                                           │
│  • Código revisado                                                  │
│                                                                     │
│  AÇÕES:                                                             │
│                                                                     │
│  5.1 TESTES FUNCIONAIS                                              │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ Para cada critério de aceite:                                 │  │
│  │ ☐ Executar cenário                                            │  │
│  │ ☐ Verificar resultado esperado                                │  │
│  │ ☐ Registrar evidência (screenshot, log)                       │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                     │
│  5.2 TESTES VISUAIS                                                 │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ ☐ Cores aplicadas corretamente                                │  │
│  │ ☐ Logo aparece                                                │  │
│  │ ☐ Assets carregam (sem 404)                                   │  │
│  │ ☐ Traduções corretas                                          │  │
│  │ ☐ Layout não quebrado                                         │  │
│  │ ☐ Responsivo (se aplicável)                                   │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                     │
│  5.3 TESTES DE PERFORMANCE                                          │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ Com Debug Bar ativa:                                          │  │
│  │ ☐ Tempo de resposta aceitável (< 500ms páginas principais)    │  │
│  │ ☐ Queries não excessivas (< 50 por página)                    │  │
│  │ ☐ Memória ok (< 128MB)                                        │  │
│  │ ☐ Seu package não é gargalo                                   │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                     │
│  5.4 TESTES DE EVENTOS                                              │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ php artisan tinker                                            │  │
│  │ >>> event('nome.evento', $entidade);                          │  │
│  │                                                               │  │
│  │ ☐ Listener dispara                                            │  │
│  │ ☐ Ação executada (log, email, etc)                            │  │
│  │ ☐ Sem erros no log                                            │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                     │
│  SAÍDA:                                                             │
│  • Relatório de testes                                              │
│  • Bugs corrigidos                                                  │
│                                                                     │
│  GATE 5: Testes aprovados? ☐ Sim ☐ Não                             │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

### ETAPA 6: DEPLOY STAGING
**Tempo:** 2-4 horas  
**Documento:** ANATOMIA_GERAL → Parte VI e VII

```
┌─────────────────────────────────────────────────────────────────────┐
│  ETAPA 6: DEPLOY STAGING                                            │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ENTRADA:                                                           │
│  • Testes aprovados                                                 │
│                                                                     │
│  AÇÕES:                                                             │
│                                                                     │
│  6.1 PRÉ-BUILD                                                      │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ Verificar:                                                    │  │
│  │ ☐ config/modules.php inclui seu package                       │  │
│  │ ☐ composer.json tem path repository                           │  │
│  │ ☐ Assets existem em Resources/assets/                         │  │
│  │ ☐ Dockerfile executa vendor:publish                           │  │
│  │ ☐ Dockerfile executa optimize:clear                           │  │
│  │ ☐ Código commitado e tagueado                                 │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                     │
│  6.2 BUILD                                                          │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ VERSION="v1.0.0-$(date +%Y%m%d)"                              │  │
│  │ docker build -t projeto:$VERSION .                            │  │
│  │ docker tag projeto:$VERSION registry/projeto:$VERSION         │  │
│  │ docker push registry/projeto:$VERSION                         │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                     │
│  6.3 DEPLOY STAGING                                                 │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ docker service update \                                       │  │
│  │   --image registry/projeto:$VERSION \                         │  │
│  │   staging_krayin                                              │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                     │
│  6.4 VALIDAÇÃO STAGING                                              │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ ☐ curl retorna 200                                            │  │
│  │ ☐ Login funciona                                              │  │
│  │ ☐ Assets carregam                                             │  │
│  │ ☐ Customizações visíveis                                      │  │
│  │ ☐ Funcionalidades ok                                          │  │
│  │ ☐ Logs limpos                                                 │  │
│  │ ☐ Stakeholder validou                                         │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                     │
│  SAÍDA:                                                             │
│  • Staging validado                                                 │
│  • Aprovação do stakeholder                                         │
│                                                                     │
│  GATE 6: Staging validado? ☐ Sim ☐ Não                             │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

### ETAPA 7: DEPLOY PRODUÇÃO
**Tempo:** 1-2 horas  
**Documento:** ANATOMIA_GERAL → Parte VII

```
┌─────────────────────────────────────────────────────────────────────┐
│  ETAPA 7: DEPLOY PRODUÇÃO                                           │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ENTRADA:                                                           │
│  • Staging validado e aprovado                                      │
│                                                                     │
│  AÇÕES:                                                             │
│                                                                     │
│  7.1 PRÉ-DEPLOY                                                     │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ ☐ Aprovação formal documentada                                │  │
│  │ ☐ Janela de deploy definida                                   │  │
│  │ ☐ Backup de produção realizado                                │  │
│  │ ☐ Plano de rollback pronto                                    │  │
│  │ ☐ Equipe de plantão definida                                  │  │
│  │ ☐ Usuários comunicados (se necessário)                        │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                     │
│  7.2 DEPLOY                                                         │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ # Usar MESMA imagem validada em staging                       │  │
│  │ docker service update \                                       │  │
│  │   --image registry/projeto:$VERSION \                         │  │
│  │   production_krayin                                           │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                     │
│  7.3 VALIDAÇÃO PRODUÇÃO                                             │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ ☐ Aplicação responde                                          │  │
│  │ ☐ Login funciona                                              │  │
│  │ ☐ Customizações visíveis                                      │  │
│  │ ☐ Funcionalidades ok                                          │  │
│  │ ☐ Logs limpos                                                 │  │
│  │ ☐ Monitoramento ok                                            │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                     │
│  7.4 PÓS-DEPLOY                                                     │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ ☐ Comunicar conclusão aos stakeholders                        │  │
│  │ ☐ Atualizar documentação                                      │  │
│  │ ☐ Fechar tickets/tasks                                        │  │
│  │ ☐ Lições aprendidas (se aplicável)                            │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                     │
│  SAÍDA:                                                             │
│  • Sistema em produção                                              │
│  • Projeto concluído                                                │
│                                                                     │
│  GATE 7: Produção liberada? ☐ Sim ☐ Não                            │
│                                                                     │
│  ✅ PROJETO CONCLUÍDO!                                              │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 🎯 MATRIZ DE DECISÃO RÁPIDA

### O Que Fazer Para Cada Tipo de Customização

```
┌─────────────────────────────────────────────────────────────────────┐
│                    MATRIZ DE DECISÃO RÁPIDA                         │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  PRECISO FAZER                    ABORDAGEM          ONDE REGISTRAR │
│  ═══════════════════════════════════════════════════════════════   │
│                                                                     │
│  Mudar cores/fontes              CSS Variables       boot():        │
│                                                      publishes()    │
│                                                                     │
│  Trocar logo/imagens             Asset Replace       boot():        │
│                                                      publishes()    │
│                                                                     │
│  Alterar texto/label             Tradução            boot():        │
│                                                      loadTranslations│
│                                                                     │
│  Mudar estrutura de tela         View Override       boot():        │
│                                                      loadViewsFrom  │
│                                                                     │
│  Injetar pequeno HTML            View Render Event   Event::listen  │
│                                                                     │
│  Alterar lógica HTTP             Controller Override register():    │
│                                                      $this->app->   │
│                                                      bind()         │
│                                                                     │
│  Adicionar campo ao model        Model Override      boot():        │
│                                  + Migration         concord->      │
│                                                      registerModel  │
│                                                                     │
│  Adicionar relacionamento        Model Override      boot():        │
│                                                      concord->      │
│                                                      registerModel  │
│                                                                     │
│  Executar ação após evento       Event Listener      Event::listen  │
│                                                      no EventService│
│                                                      Provider       │
│                                                                     │
│  Enviar email automático         Event Listener +    Event::listen  │
│                                  Mailable (Queue)    + Mail::to()   │
│                                                                     │
│  Integrar com API externa        Event Listener ou   Listener ou    │
│                                  Service             Service class  │
│                                                                     │
│  Validação customizada           Request ou          Controller ou  │
│                                  Listener            Listener       │
│                                                                     │
│  Criar módulo novo               Novo Package        package:make   │
│                                                                     │
│  ═══════════════════════════════════════════════════════════════   │
│                                                                     │
│  ⚠️ NUNCA FAZER:                                                    │
│  • Editar arquivos em packages/Webkul/* (core)                      │
│  • Usar classe direta em registerModel (usar Contract)              │
│  • Deploy sem testar em staging                                     │
│  • Desenvolver sem mapear primeiro                                  │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 📝 TEMPLATES DE EXECUÇÃO

### Template: Ticket de Desenvolvimento

```markdown
# [TIPO] Descrição curta

## Contexto
- Projeto: PROJ-XXX
- Sprint: X
- Prioridade: Must/Should/Could

## Descrição
Como [persona], quero [ação], para [benefício].

## Solução Técnica
- **Abordagem:** [Controller/Model/View/Event]
- **Package:** Webkul/NomeProjeto
- **Arquivos:**
  - [ ] path/to/file1.php
  - [ ] path/to/file2.blade.php

## Critérios de Aceite
- [ ] Critério 1
- [ ] Critério 2
- [ ] Critério 3

## Checklist de Desenvolvimento
- [ ] Blade Tracer usado para localizar view (se aplicável)
- [ ] Código segue padrão da Anatomia
- [ ] Testado localmente
- [ ] Testado com tinker (se evento)
- [ ] Code review feito
- [ ] CHECKLIST_VALIDACAO_DEV > 47 pontos

## Estimativa
- Dev: X horas
- Teste: Y horas
```

### Template: Registro de Deploy

```markdown
# Deploy: [Projeto] v[Versão]

## Informações
- **Data:** DD/MM/AAAA HH:MM
- **Ambiente:** Staging / Produção
- **Responsável:** Nome
- **Imagem:** registry/projeto:versao

## Checklist Pré-Deploy
- [ ] Build sem erros
- [ ] Staging validado (se produção)
- [ ] Backup realizado
- [ ] Rollback pronto

## Checklist Pós-Deploy
- [ ] Aplicação responde
- [ ] Login funciona
- [ ] Customizações visíveis
- [ ] Logs limpos

## Rollback (se necessário)
```bash
docker service update --image registry/projeto:versao_anterior production_krayin
```

## Observações
[Notas do deploy]
```

---

## 📊 MÉTRICAS E CONTROLE

### KPIs do Processo

| Métrica | Meta | Como Medir |
|---------|------|------------|
| **Lead Time** | < 2 semanas | Tempo do pedido ao deploy |
| **Taxa de Retrabalho** | < 10% | Itens que voltaram após code review |
| **Gates Passados 1ª Vez** | > 80% | Gates que não precisaram de correção |
| **Bugs em Produção** | 0 | Bugs encontrados após deploy |
| **Satisfação Stakeholder** | > 4/5 | Pesquisa pós-entrega |

### Dashboard de Projeto

```
┌─────────────────────────────────────────────────────────────────────┐
│                    STATUS DO PROJETO                                │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  Projeto: ________________    Sprint: ___                           │
│  Data: ____/____/________                                           │
│                                                                     │
│  GATES                                                              │
│  ═════                                                              │
│  Gate 1: Qualificação    [✅ PASSOU / ⬜ PENDENTE / ❌ BLOQUEADO]   │
│  Gate 2: Mapeamento      [✅ PASSOU / ⬜ PENDENTE / ❌ BLOQUEADO]   │
│  Gate 3: Arquitetura     [✅ PASSOU / ⬜ PENDENTE / ❌ BLOQUEADO]   │
│  Gate 4: Código          [✅ PASSOU / ⬜ PENDENTE / ❌ BLOQUEADO]   │
│  Gate 5: Testes          [✅ PASSOU / ⬜ PENDENTE / ❌ BLOQUEADO]   │
│  Gate 6: Staging         [✅ PASSOU / ⬜ PENDENTE / ❌ BLOQUEADO]   │
│  Gate 7: Produção        [✅ PASSOU / ⬜ PENDENTE / ❌ BLOQUEADO]   │
│                                                                     │
│  PROGRESSO                                                          │
│  ═════════                                                          │
│  [████████████░░░░░░░░]  60%                                        │
│                                                                     │
│  Itens: 6/10 concluídos                                             │
│  Horas: 24/40 consumidas                                            │
│                                                                     │
│  BLOQUEIOS                                                          │
│  ══════════                                                         │
│  • Nenhum / [Descrição do bloqueio]                                 │
│                                                                     │
│  PRÓXIMOS PASSOS                                                    │
│  ═══════════════                                                    │
│  1. [Próxima ação]                                                  │
│  2. [Próxima ação]                                                  │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 🔄 RESUMO DO PROCESSO

```
┌─────────────────────────────────────────────────────────────────────┐
│                    PROCESSO EM 1 PÁGINA                             │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  1️⃣ RECEBER → Registrar demanda, agendar descoberta                 │
│     📄 MAPEAMENTO_DEMANDA                                           │
│     🚦 GATE 1: Demanda qualificada?                                 │
│                                                                     │
│  2️⃣ MAPEAR → Questionário + Inventários + Blade Tracer             │
│     📄 MAPEAMENTO_DEMANDA                                           │
│     🚦 GATE 2: Mapeamento completo?                                 │
│                                                                     │
│  3️⃣ ARQUITETAR → Decisão técnica + Estimativa + SOW                │
│     📄 MAPEAMENTO + ANATOMIA                                        │
│     🚦 GATE 3: Arquitetura aprovada?                                │
│                                                                     │
│  4️⃣ DESENVOLVER → Package Generator + Código + Code Review         │
│     📄 ANATOMIA + FERRAMENTAS + CHECKLIST                           │
│     🚦 GATE 4: Código pronto? (score > 47)                          │
│                                                                     │
│  5️⃣ TESTAR → Funcional + Visual + Performance + Eventos            │
│     📄 CHECKLIST                                                    │
│     🚦 GATE 5: Testes aprovados?                                    │
│                                                                     │
│  6️⃣ STAGING → Build + Deploy + Validação + Aprovação               │
│     📄 ANATOMIA (Docker)                                            │
│     🚦 GATE 6: Staging validado?                                    │
│                                                                     │
│  7️⃣ PRODUÇÃO → Deploy + Validação + Documentação                   │
│     📄 ANATOMIA (Operações)                                         │
│     🚦 GATE 7: Produção liberada?                                   │
│                                                                     │
│  ═══════════════════════════════════════════════════════════════   │
│                                                                     │
│  📦 KIT DE DOCUMENTAÇÃO:                                            │
│  • MAPEAMENTO_DEMANDA_PROJETO.md  → Fases 1-3                       │
│  • ANATOMIA_GERAL_KRAYIN_CRM.md   → Fases 4-7                       │
│  • FERRAMENTAS_DEV_KRAYIN.md      → Fase 4                          │
│  • CHECKLIST_VALIDACAO_DEV.md     → Fases 4-5                       │
│  • ONBOARDING_DEV_KRAYIN.md       → Novos devs                      │
│  • PROCESSO_CUSTOMIZACAO.md       → Este documento                  │
│                                                                     │
│  🎯 PRINCÍPIOS INEGOCIÁVEIS:                                        │
│  • Nenhum código sem escopo aprovado                                │
│  • Nunca editar core                                                │
│  • Sempre usar ferramentas (Tracer, Generator)                      │
│  • Sempre testar em staging                                         │
│  • Sempre documentar                                                │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

**Versão:** 1.0.0  
**Data:** Dezembro 2025  

---

*"Processo claro, resultado previsível."*
