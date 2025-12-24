# 📋 MAPEAMENTO DE DEMANDA E ESTRUTURAÇÃO DE PROJETO
## Guia Completo para Levantamento, Análise e Planejamento de Customizações Krayin

**Versão:** 1.0.0  
**Data:** Dezembro 2025  
**Objetivo:** Estruturar projetos de customização antes do desenvolvimento  
**Resultado:** Escopo claro, estimativas precisas, projeto organizado

---

## 📋 ÍNDICE

1. [Visão Geral do Processo](#-visão-geral-do-processo)
2. [Fase 1: Descoberta](#-fase-1-descoberta)
3. [Fase 2: Levantamento Técnico](#-fase-2-levantamento-técnico)
4. [Fase 3: Análise de Impacto](#-fase-3-análise-de-impacto)
5. [Fase 4: Arquitetura da Solução](#-fase-4-arquitetura-da-solução)
6. [Fase 5: Estimativa e Cronograma](#-fase-5-estimativa-e-cronograma)
7. [Fase 6: Documentação do Projeto](#-fase-6-documentação-do-projeto)
8. [Templates Prontos](#-templates-prontos)
9. [Checklists de Validação](#-checklists-de-validação)

---

## 🎯 VISÃO GERAL DO PROCESSO

### Fluxo de Estruturação de Projeto

```
┌─────────────────────────────────────────────────────────────────────┐
│              FLUXO DE MAPEAMENTO E ESTRUTURAÇÃO                     │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌──────────────┐                                                   │
│  │   DEMANDA    │  Requisição inicial do cliente/stakeholder        │
│  └──────┬───────┘                                                   │
│         │                                                           │
│         ▼                                                           │
│  ┌──────────────┐                                                   │
│  │  DESCOBERTA  │  Entender contexto, objetivos, restrições         │
│  │   (Fase 1)   │  Reuniões, entrevistas, documentos existentes     │
│  └──────┬───────┘                                                   │
│         │                                                           │
│         ▼                                                           │
│  ┌──────────────┐                                                   │
│  │ LEVANTAMENTO │  Mapear telas, fluxos, integrações                │
│  │   TÉCNICO    │  Usar Blade Tracer, analisar código               │
│  │   (Fase 2)   │                                                   │
│  └──────┬───────┘                                                   │
│         │                                                           │
│         ▼                                                           │
│  ┌──────────────┐                                                   │
│  │   ANÁLISE    │  Identificar overrides, eventos, riscos           │
│  │  DE IMPACTO  │  Avaliar complexidade técnica                     │
│  │   (Fase 3)   │                                                   │
│  └──────┬───────┘                                                   │
│         │                                                           │
│         ▼                                                           │
│  ┌──────────────┐                                                   │
│  │  ARQUITETURA │  Definir packages, estrutura, padrões             │
│  │  DA SOLUÇÃO  │  Desenhar solução técnica                         │
│  │   (Fase 4)   │                                                   │
│  └──────┬───────┘                                                   │
│         │                                                           │
│         ▼                                                           │
│  ┌──────────────┐                                                   │
│  │  ESTIMATIVA  │  Calcular esforço, definir cronograma             │
│  │ E CRONOGRAMA │  Identificar dependências                         │
│  │   (Fase 5)   │                                                   │
│  └──────┬───────┘                                                   │
│         │                                                           │
│         ▼                                                           │
│  ┌──────────────┐                                                   │
│  │ DOCUMENTAÇÃO │  Formalizar escopo, criar backlog                 │
│  │  DO PROJETO  │  Aprovar com stakeholders                         │
│  │   (Fase 6)   │                                                   │
│  └──────┬───────┘                                                   │
│         │                                                           │
│         ▼                                                           │
│  ┌──────────────┐                                                   │
│  │    INÍCIO    │  Projeto estruturado e aprovado                   │
│  │     DEV      │  Seguir para desenvolvimento                      │
│  └──────────────┘                                                   │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### Por Que Estruturar Antes de Desenvolver?

| Sem Estruturação | Com Estruturação |
|------------------|------------------|
| ❌ Escopo indefinido | ✅ Escopo claro e aprovado |
| ❌ Estimativas imprecisas | ✅ Estimativas baseadas em análise |
| ❌ Retrabalho frequente | ✅ Desenvolvimento direcionado |
| ❌ Surpresas técnicas | ✅ Riscos identificados |
| ❌ Conflitos de expectativa | ✅ Alinhamento com stakeholders |
| ❌ Documentação inexistente | ✅ Projeto documentado |

---

## 🔍 FASE 1: DESCOBERTA

### 1.1 Objetivo

Entender o contexto, objetivos e restrições do projeto antes de qualquer análise técnica.

### 1.2 Questionário de Descoberta

```
┌─────────────────────────────────────────────────────────────────────┐
│                    QUESTIONÁRIO DE DESCOBERTA                       │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  CONTEXTO DO PROJETO                                                │
│  ───────────────────                                                │
│                                                                     │
│  1. Qual o nome/identificador do projeto?                           │
│     _____________________________________________________________   │
│                                                                     │
│  2. Quem é o solicitante/sponsor?                                   │
│     _____________________________________________________________   │
│                                                                     │
│  3. Qual problema estamos resolvendo?                               │
│     _____________________________________________________________   │
│     _____________________________________________________________   │
│                                                                     │
│  4. Qual o objetivo principal do projeto?                           │
│     _____________________________________________________________   │
│     _____________________________________________________________   │
│                                                                     │
│  5. Quem são os usuários afetados?                                  │
│     ☐ Vendedores    ☐ Gerentes    ☐ Administradores                │
│     ☐ Clientes      ☐ Parceiros   ☐ Outros: ___________            │
│                                                                     │
│  6. Quantos usuários serão impactados?                              │
│     ☐ 1-10    ☐ 11-50    ☐ 51-200    ☐ 200+                        │
│                                                                     │
│  ESCOPO INICIAL                                                     │
│  ──────────────                                                     │
│                                                                     │
│  7. O que DEVE ser entregue (obrigatório)?                          │
│     _____________________________________________________________   │
│     _____________________________________________________________   │
│     _____________________________________________________________   │
│                                                                     │
│  8. O que PODERIA ser entregue (desejável)?                         │
│     _____________________________________________________________   │
│     _____________________________________________________________   │
│                                                                     │
│  9. O que NÃO faz parte do escopo?                                  │
│     _____________________________________________________________   │
│     _____________________________________________________________   │
│                                                                     │
│  RESTRIÇÕES                                                         │
│  ──────────                                                         │
│                                                                     │
│  10. Qual o prazo esperado?                                         │
│      Data limite: ____/____/________                                │
│      ☐ Flexível    ☐ Negociável    ☐ Rígido                        │
│                                                                     │
│  11. Qual o orçamento disponível?                                   │
│      ☐ Definido: R$ ___________                                     │
│      ☐ A definir após estimativa                                    │
│      ☐ Sem restrição                                                │
│                                                                     │
│  12. Há restrições técnicas conhecidas?                             │
│      ☐ Versão específica do Krayin: ___________                     │
│      ☐ Infraestrutura limitada: ___________                         │
│      ☐ Integrações obrigatórias: ___________                        │
│      ☐ Outras: ___________                                          │
│                                                                     │
│  DEPENDÊNCIAS                                                       │
│  ────────────                                                       │
│                                                                     │
│  13. Há outros projetos em andamento que afetam este?               │
│      _____________________________________________________________  │
│                                                                     │
│  14. Este projeto bloqueia outros?                                  │
│      _____________________________________________________________  │
│                                                                     │
│  15. Há pessoas/equipes que precisam ser envolvidas?                │
│      _____________________________________________________________  │
│                                                                     │
│  CRITÉRIOS DE SUCESSO                                               │
│  ────────────────────                                               │
│                                                                     │
│  16. Como saberemos que o projeto foi bem-sucedido?                 │
│      _____________________________________________________________  │
│      _____________________________________________________________  │
│                                                                     │
│  17. Quais métricas serão acompanhadas?                             │
│      _____________________________________________________________  │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 1.3 Matriz de Priorização MoSCoW

```
┌─────────────────────────────────────────────────────────────────────┐
│                    MATRIZ MoSCoW                                    │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  MUST HAVE (Obrigatório)                                            │
│  ───────────────────────                                            │
│  Sem isso, o projeto não tem valor                                  │
│                                                                     │
│  Item                                          Justificativa         │
│  ┌────────────────────────────────────────────┬───────────────────┐ │
│  │                                            │                   │ │
│  ├────────────────────────────────────────────┼───────────────────┤ │
│  │                                            │                   │ │
│  ├────────────────────────────────────────────┼───────────────────┤ │
│  │                                            │                   │ │
│  └────────────────────────────────────────────┴───────────────────┘ │
│                                                                     │
│  SHOULD HAVE (Importante)                                           │
│  ────────────────────────                                           │
│  Importante, mas projeto funciona sem                               │
│                                                                     │
│  Item                                          Justificativa         │
│  ┌────────────────────────────────────────────┬───────────────────┐ │
│  │                                            │                   │ │
│  ├────────────────────────────────────────────┼───────────────────┤ │
│  │                                            │                   │ │
│  └────────────────────────────────────────────┴───────────────────┘ │
│                                                                     │
│  COULD HAVE (Desejável)                                             │
│  ──────────────────────                                             │
│  Bom ter, se sobrar tempo/orçamento                                 │
│                                                                     │
│  Item                                          Justificativa         │
│  ┌────────────────────────────────────────────┬───────────────────┐ │
│  │                                            │                   │ │
│  ├────────────────────────────────────────────┼───────────────────┤ │
│  │                                            │                   │ │
│  └────────────────────────────────────────────┴───────────────────┘ │
│                                                                     │
│  WON'T HAVE (Fora do Escopo)                                        │
│  ───────────────────────────                                        │
│  Explicitamente não será feito                                      │
│                                                                     │
│  Item                                          Motivo               │
│  ┌────────────────────────────────────────────┬───────────────────┐ │
│  │                                            │                   │ │
│  ├────────────────────────────────────────────┼───────────────────┤ │
│  │                                            │                   │ │
│  └────────────────────────────────────────────┴───────────────────┘ │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 1.4 Entregáveis da Fase 1

- [ ] Questionário de descoberta preenchido
- [ ] Matriz MoSCoW definida
- [ ] Stakeholders identificados
- [ ] Restrições documentadas
- [ ] Critérios de sucesso definidos

---

## 🔧 FASE 2: LEVANTAMENTO TÉCNICO

### 2.1 Objetivo

Mapear tecnicamente o que precisa ser customizado, usando as ferramentas de desenvolvimento.

### 2.2 Inventário de Telas

```
┌─────────────────────────────────────────────────────────────────────┐
│                    INVENTÁRIO DE TELAS                              │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  Instruções:                                                        │
│  1. Acessar cada tela mencionada nos requisitos                     │
│  2. Usar Blade Tracer para identificar views                        │
│  3. Documentar tudo nesta planilha                                  │
│                                                                     │
│  ┌─────┬────────────┬────────────────────────────────┬────────────┐ │
│  │ ID  │ Tela       │ Path (via Blade Tracer)        │ Ação       │ │
│  ├─────┼────────────┼────────────────────────────────┼────────────┤ │
│  │ T01 │            │                                │ ☐ Override │ │
│  │     │            │                                │ ☐ Injetar  │ │
│  │     │            │                                │ ☐ Manter   │ │
│  ├─────┼────────────┼────────────────────────────────┼────────────┤ │
│  │ T02 │            │                                │ ☐ Override │ │
│  │     │            │                                │ ☐ Injetar  │ │
│  │     │            │                                │ ☐ Manter   │ │
│  ├─────┼────────────┼────────────────────────────────┼────────────┤ │
│  │ T03 │            │                                │ ☐ Override │ │
│  │     │            │                                │ ☐ Injetar  │ │
│  │     │            │                                │ ☐ Manter   │ │
│  ├─────┼────────────┼────────────────────────────────┼────────────┤ │
│  │ T04 │            │                                │ ☐ Override │ │
│  │     │            │                                │ ☐ Injetar  │ │
│  │     │            │                                │ ☐ Manter   │ │
│  ├─────┼────────────┼────────────────────────────────┼────────────┤ │
│  │ T05 │            │                                │ ☐ Override │ │
│  │     │            │                                │ ☐ Injetar  │ │
│  │     │            │                                │ ☐ Manter   │ │
│  └─────┴────────────┴────────────────────────────────┴────────────┘ │
│                                                                     │
│  Legenda:                                                           │
│  • Override = Substituir view inteira                               │
│  • Injetar = Usar View Render Event                                 │
│  • Manter = Não requer alteração                                    │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 2.3 Inventário de Campos

```
┌─────────────────────────────────────────────────────────────────────┐
│                    INVENTÁRIO DE CAMPOS                             │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  Instruções:                                                        │
│  1. Listar todos os campos novos ou modificados                     │
│  2. Identificar entidade (Lead, Contact, etc.)                      │
│  3. Definir tipo de dado e regras                                   │
│                                                                     │
│  ┌─────┬────────────┬────────────┬──────────┬──────────┬──────────┐│
│  │ ID  │ Entidade   │ Campo      │ Tipo     │ Obrig.   │ Regras   ││
│  ├─────┼────────────┼────────────┼──────────┼──────────┼──────────┤│
│  │ C01 │            │            │          │ ☐ Sim    │          ││
│  │     │            │            │          │ ☐ Não    │          ││
│  ├─────┼────────────┼────────────┼──────────┼──────────┼──────────┤│
│  │ C02 │            │            │          │ ☐ Sim    │          ││
│  │     │            │            │          │ ☐ Não    │          ││
│  ├─────┼────────────┼────────────┼──────────┼──────────┼──────────┤│
│  │ C03 │            │            │          │ ☐ Sim    │          ││
│  │     │            │            │          │ ☐ Não    │          ││
│  ├─────┼────────────┼────────────┼──────────┼──────────┼──────────┤│
│  │ C04 │            │            │          │ ☐ Sim    │          ││
│  │     │            │            │          │ ☐ Não    │          ││
│  ├─────┼────────────┼────────────┼──────────┼──────────┼──────────┤│
│  │ C05 │            │            │          │ ☐ Sim    │          ││
│  │     │            │            │          │ ☐ Não    │          ││
│  └─────┴────────────┴────────────┴──────────┴──────────┴──────────┘│
│                                                                     │
│  Tipos de campo:                                                    │
│  • string (varchar)    • text          • integer                    │
│  • decimal             • boolean       • date                       │
│  • datetime            • json          • enum                       │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 2.4 Inventário de Fluxos/Automações

```
┌─────────────────────────────────────────────────────────────────────┐
│                    INVENTÁRIO DE FLUXOS                             │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  Instruções:                                                        │
│  1. Mapear cada automação ou fluxo requerido                        │
│  2. Identificar gatilho (evento)                                    │
│  3. Definir ações a executar                                        │
│                                                                     │
│  ┌─────┬──────────────────┬────────────────────┬───────────────────┐│
│  │ ID  │ Gatilho (Evento) │ Condição           │ Ação              ││
│  ├─────┼──────────────────┼────────────────────┼───────────────────┤│
│  │ F01 │                  │                    │                   ││
│  ├─────┼──────────────────┼────────────────────┼───────────────────┤│
│  │ F02 │                  │                    │                   ││
│  ├─────┼──────────────────┼────────────────────┼───────────────────┤│
│  │ F03 │                  │                    │                   ││
│  ├─────┼──────────────────┼────────────────────┼───────────────────┤│
│  │ F04 │                  │                    │                   ││
│  ├─────┼──────────────────┼────────────────────┼───────────────────┤│
│  │ F05 │                  │                    │                   ││
│  └─────┴──────────────────┴────────────────────┴───────────────────┘│
│                                                                     │
│  Exemplos de Gatilhos:                                              │
│  • lead.create.after     • lead.update.after                        │
│  • contact.person.create.after                                      │
│  • quote.create.after    • activity.create.after                    │
│                                                                     │
│  Exemplos de Ações:                                                 │
│  • Enviar email          • Criar atividade                          │
│  • Notificar Slack       • Chamar API externa                       │
│  • Atualizar campo       • Criar registro relacionado               │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 2.5 Inventário de Integrações

```
┌─────────────────────────────────────────────────────────────────────┐
│                    INVENTÁRIO DE INTEGRAÇÕES                        │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌─────┬────────────────┬────────────┬────────────┬────────────────┐│
│  │ ID  │ Sistema        │ Direção    │ Dados      │ Frequência     ││
│  ├─────┼────────────────┼────────────┼────────────┼────────────────┤│
│  │ I01 │                │ ☐ Entrada  │            │ ☐ Real-time    ││
│  │     │                │ ☐ Saída    │            │ ☐ Batch        ││
│  │     │                │ ☐ Bidirecional         │ ☐ Sob demanda  ││
│  ├─────┼────────────────┼────────────┼────────────┼────────────────┤│
│  │ I02 │                │ ☐ Entrada  │            │ ☐ Real-time    ││
│  │     │                │ ☐ Saída    │            │ ☐ Batch        ││
│  │     │                │ ☐ Bidirecional         │ ☐ Sob demanda  ││
│  ├─────┼────────────────┼────────────┼────────────┼────────────────┤│
│  │ I03 │                │ ☐ Entrada  │            │ ☐ Real-time    ││
│  │     │                │ ☐ Saída    │            │ ☐ Batch        ││
│  │     │                │ ☐ Bidirecional         │ ☐ Sob demanda  ││
│  └─────┴────────────────┴────────────┴────────────┴────────────────┘│
│                                                                     │
│  Informações Adicionais por Integração:                             │
│                                                                     │
│  I01: _______________                                               │
│  • Endpoint/URL: _______________________________________            │
│  • Autenticação: ☐ API Key  ☐ OAuth  ☐ Basic  ☐ Outra              │
│  • Documentação: _______________________________________            │
│  • Ambiente sandbox: ☐ Sim  ☐ Não                                  │
│                                                                     │
│  I02: _______________                                               │
│  • Endpoint/URL: _______________________________________            │
│  • Autenticação: ☐ API Key  ☐ OAuth  ☐ Basic  ☐ Outra              │
│  • Documentação: _______________________________________            │
│  • Ambiente sandbox: ☐ Sim  ☐ Não                                  │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 2.6 Inventário Visual

```
┌─────────────────────────────────────────────────────────────────────┐
│                    INVENTÁRIO VISUAL                                │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  IDENTIDADE VISUAL                                                  │
│  ─────────────────                                                  │
│                                                                     │
│  Cores:                                                             │
│  • Cor primária:    #________  □                                    │
│  • Cor secundária:  #________  □                                    │
│  • Cor de sucesso:  #________  □                                    │
│  • Cor de erro:     #________  □                                    │
│  • Cor de alerta:   #________  □                                    │
│                                                                     │
│  Tipografia:                                                        │
│  • Fonte principal: _______________________________________         │
│  • Fonte secundária: ______________________________________         │
│  • Fonte disponível no Google Fonts? ☐ Sim  ☐ Não                  │
│                                                                     │
│  ASSETS NECESSÁRIOS                                                 │
│  ──────────────────                                                 │
│                                                                     │
│  ☐ Logo principal (formato: ___, tamanho: ___ x ___ px)             │
│    Arquivo fornecido: ☐ Sim  ☐ Não                                 │
│                                                                     │
│  ☐ Logo ícone/favicon (formato: ___, tamanho: ___ x ___ px)         │
│    Arquivo fornecido: ☐ Sim  ☐ Não                                 │
│                                                                     │
│  ☐ Imagem de login/background                                       │
│    Arquivo fornecido: ☐ Sim  ☐ Não                                 │
│                                                                     │
│  ☐ Ícones customizados                                              │
│    Quais: ________________________________________________         │
│                                                                     │
│  ☐ Ilustrações (empty states, onboarding)                           │
│    Quais: ________________________________________________         │
│                                                                     │
│  TELAS A CUSTOMIZAR VISUALMENTE                                     │
│  ──────────────────────────────                                     │
│                                                                     │
│  ☐ Login                    ☐ Dashboard                             │
│  ☐ Sidebar/Menu             ☐ Header                                │
│  ☐ Cards                    ☐ Tabelas                               │
│  ☐ Formulários              ☐ Botões                                │
│  ☐ Modais                   ☐ Empty states                          │
│  ☐ Outras: ________________________________________________        │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 2.7 Entregáveis da Fase 2

- [ ] Inventário de telas preenchido (com paths do Blade Tracer)
- [ ] Inventário de campos definido
- [ ] Inventário de fluxos mapeado
- [ ] Inventário de integrações documentado
- [ ] Inventário visual coletado
- [ ] Assets visuais recebidos ou solicitados

---

## 📊 FASE 3: ANÁLISE DE IMPACTO

### 3.1 Objetivo

Avaliar complexidade técnica, identificar riscos e definir abordagem para cada item.

### 3.2 Matriz de Decisão Técnica

```
┌─────────────────────────────────────────────────────────────────────┐
│                    MATRIZ DE DECISÃO TÉCNICA                        │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  Para cada requisito, definir a abordagem técnica:                  │
│                                                                     │
│  ┌─────┬────────────────────┬──────────────────┬───────────────────┐│
│  │ ID  │ Requisito          │ Abordagem        │ Justificativa     ││
│  ├─────┼────────────────────┼──────────────────┼───────────────────┤│
│  │ R01 │                    │ ☐ Controller     │                   ││
│  │     │                    │ ☐ Model          │                   ││
│  │     │                    │ ☐ View           │                   ││
│  │     │                    │ ☐ Event          │                   ││
│  │     │                    │ ☐ Render Event   │                   ││
│  │     │                    │ ☐ CSS Only       │                   ││
│  │     │                    │ ☐ Novo Package   │                   ││
│  ├─────┼────────────────────┼──────────────────┼───────────────────┤│
│  │ R02 │                    │ ☐ Controller     │                   ││
│  │     │                    │ ☐ Model          │                   ││
│  │     │                    │ ☐ View           │                   ││
│  │     │                    │ ☐ Event          │                   ││
│  │     │                    │ ☐ Render Event   │                   ││
│  │     │                    │ ☐ CSS Only       │                   ││
│  │     │                    │ ☐ Novo Package   │                   ││
│  ├─────┼────────────────────┼──────────────────┼───────────────────┤│
│  │ R03 │                    │ ☐ Controller     │                   ││
│  │     │                    │ ☐ Model          │                   ││
│  │     │                    │ ☐ View           │                   ││
│  │     │                    │ ☐ Event          │                   ││
│  │     │                    │ ☐ Render Event   │                   ││
│  │     │                    │ ☐ CSS Only       │                   ││
│  │     │                    │ ☐ Novo Package   │                   ││
│  └─────┴────────────────────┴──────────────────┴───────────────────┘│
│                                                                     │
│  GUIA DE DECISÃO:                                                   │
│  ─────────────────                                                  │
│  • Alterar lógica HTTP → Controller Override                        │
│  • Adicionar campo/relacionamento → Model Override + Migration      │
│  • Mudar UI significativamente → View Override                      │
│  • Injetar pequeno HTML → View Render Event                         │
│  • Executar ação após evento → Event Listener                       │
│  • Mudar apenas visual → CSS Variables                              │
│  • Funcionalidade nova e isolada → Novo Package                     │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 3.3 Análise de Complexidade

```
┌─────────────────────────────────────────────────────────────────────┐
│                    ANÁLISE DE COMPLEXIDADE                          │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ESCALA DE COMPLEXIDADE                                             │
│  ──────────────────────                                             │
│                                                                     │
│  1 - SIMPLES (1-4h)                                                 │
│  • CSS only                                                         │
│  • View Render Event simples                                        │
│  • Tradução                                                         │
│  • Troca de asset                                                   │
│                                                                     │
│  2 - BAIXA (4-8h)                                                   │
│  • View override simples                                            │
│  • Listener simples (log, notificação)                              │
│  • Campo novo sem lógica                                            │
│                                                                     │
│  3 - MÉDIA (1-2 dias)                                               │
│  • Controller override                                              │
│  • Model override com relacionamentos                               │
│  • View override complexa                                           │
│  • Listener com email                                               │
│                                                                     │
│  4 - ALTA (2-5 dias)                                                │
│  • Novo package funcional                                           │
│  • Integração com API externa                                       │
│  • Fluxo de trabalho automatizado                                   │
│  • Múltiplos overrides coordenados                                  │
│                                                                     │
│  5 - MUITO ALTA (5+ dias)                                           │
│  • Módulo completamente novo                                        │
│  • Integração bidirecional complexa                                 │
│  • Alteração estrutural significativa                               │
│                                                                     │
│  MAPEAMENTO                                                         │
│  ──────────                                                         │
│                                                                     │
│  ┌─────┬────────────────────────────────────┬──────────┬───────────┐│
│  │ ID  │ Item                               │ Complex. │ Horas Est.││
│  ├─────┼────────────────────────────────────┼──────────┼───────────┤│
│  │     │                                    │ ☐1 ☐2 ☐3 │           ││
│  │     │                                    │ ☐4 ☐5    │           ││
│  ├─────┼────────────────────────────────────┼──────────┼───────────┤│
│  │     │                                    │ ☐1 ☐2 ☐3 │           ││
│  │     │                                    │ ☐4 ☐5    │           ││
│  ├─────┼────────────────────────────────────┼──────────┼───────────┤│
│  │     │                                    │ ☐1 ☐2 ☐3 │           ││
│  │     │                                    │ ☐4 ☐5    │           ││
│  ├─────┼────────────────────────────────────┼──────────┼───────────┤│
│  │     │                                    │ ☐1 ☐2 ☐3 │           ││
│  │     │                                    │ ☐4 ☐5    │           ││
│  ├─────┼────────────────────────────────────┼──────────┼───────────┤│
│  │     │                                    │ ☐1 ☐2 ☐3 │           ││
│  │     │                                    │ ☐4 ☐5    │           ││
│  └─────┴────────────────────────────────────┴──────────┴───────────┘│
│                                                                     │
│  TOTAIS:                                                            │
│  • Itens Complexidade 1-2: _____ (_____ horas)                      │
│  • Itens Complexidade 3:   _____ (_____ horas)                      │
│  • Itens Complexidade 4-5: _____ (_____ horas)                      │
│  • TOTAL ESTIMADO:         _____ horas                              │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 3.4 Análise de Riscos

```
┌─────────────────────────────────────────────────────────────────────┐
│                    ANÁLISE DE RISCOS                                │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌─────┬───────────────────┬───────────┬───────────┬───────────────┐│
│  │ ID  │ Risco             │ Probab.   │ Impacto   │ Mitigação     ││
│  ├─────┼───────────────────┼───────────┼───────────┼───────────────┤│
│  │ R01 │                   │ ☐ Baixa   │ ☐ Baixo   │               ││
│  │     │                   │ ☐ Média   │ ☐ Médio   │               ││
│  │     │                   │ ☐ Alta    │ ☐ Alto    │               ││
│  ├─────┼───────────────────┼───────────┼───────────┼───────────────┤│
│  │ R02 │                   │ ☐ Baixa   │ ☐ Baixo   │               ││
│  │     │                   │ ☐ Média   │ ☐ Médio   │               ││
│  │     │                   │ ☐ Alta    │ ☐ Alto    │               ││
│  ├─────┼───────────────────┼───────────┼───────────┼───────────────┤│
│  │ R03 │                   │ ☐ Baixa   │ ☐ Baixo   │               ││
│  │     │                   │ ☐ Média   │ ☐ Médio   │               ││
│  │     │                   │ ☐ Alta    │ ☐ Alto    │               ││
│  ├─────┼───────────────────┼───────────┼───────────┼───────────────┤│
│  │ R04 │                   │ ☐ Baixa   │ ☐ Baixo   │               ││
│  │     │                   │ ☐ Média   │ ☐ Médio   │               ││
│  │     │                   │ ☐ Alta    │ ☐ Alto    │               ││
│  └─────┴───────────────────┴───────────┴───────────┴───────────────┘│
│                                                                     │
│  RISCOS COMUNS EM PROJETOS KRAYIN:                                  │
│  ─────────────────────────────────                                  │
│                                                                     │
│  ☐ Atualização do Krayin quebra customizações                       │
│    Mitigação: Usar apenas overrides, nunca editar core              │
│                                                                     │
│  ☐ Performance degradada com listeners pesados                      │
│    Mitigação: Usar filas, código assíncrono                         │
│                                                                     │
│  ☐ Integração externa indisponível                                  │
│    Mitigação: Circuit breaker, fallback, retry                      │
│                                                                     │
│  ☐ Requisitos mal definidos / mudanças de escopo                    │
│    Mitigação: Documentação aprovada, change request formal          │
│                                                                     │
│  ☐ Falta de conhecimento técnico da equipe                          │
│    Mitigação: Onboarding, pair programming, documentação            │
│                                                                     │
│  ☐ Ambiente de staging diferente de produção                        │
│    Mitigação: Infraestrutura como código, containers                │
│                                                                     │
│  ☐ Dados sensíveis expostos                                         │
│    Mitigação: Criptografia, mascaramento, audit log                 │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 3.5 Dependências Técnicas

```
┌─────────────────────────────────────────────────────────────────────┐
│                    MAPA DE DEPENDÊNCIAS                             │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌─────┬─────────────────────────┬──────────────────────────────────┐│
│  │ ID  │ Item                    │ Depende de                       ││
│  ├─────┼─────────────────────────┼──────────────────────────────────┤│
│  │     │                         │                                  ││
│  ├─────┼─────────────────────────┼──────────────────────────────────┤│
│  │     │                         │                                  ││
│  ├─────┼─────────────────────────┼──────────────────────────────────┤│
│  │     │                         │                                  ││
│  ├─────┼─────────────────────────┼──────────────────────────────────┤│
│  │     │                         │                                  ││
│  └─────┴─────────────────────────┴──────────────────────────────────┘│
│                                                                     │
│  DIAGRAMA DE DEPENDÊNCIAS:                                          │
│                                                                     │
│  ┌─────────┐                                                        │
│  │  Item A │                                                        │
│  └────┬────┘                                                        │
│       │                                                             │
│       ▼                                                             │
│  ┌─────────┐     ┌─────────┐                                        │
│  │  Item B │────▶│  Item D │                                        │
│  └────┬────┘     └─────────┘                                        │
│       │                                                             │
│       ▼                                                             │
│  ┌─────────┐                                                        │
│  │  Item C │                                                        │
│  └─────────┘                                                        │
│                                                                     │
│  ORDEM DE EXECUÇÃO SUGERIDA:                                        │
│  1. ________________________                                        │
│  2. ________________________                                        │
│  3. ________________________                                        │
│  4. ________________________                                        │
│  5. ________________________                                        │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 3.6 Entregáveis da Fase 3

- [ ] Matriz de decisão técnica preenchida
- [ ] Análise de complexidade por item
- [ ] Riscos identificados e mitigações definidas
- [ ] Dependências mapeadas
- [ ] Ordem de execução definida

---

## 🏗️ FASE 4: ARQUITETURA DA SOLUÇÃO

### 4.1 Objetivo

Definir a estrutura técnica da solução: packages, componentes, padrões.

### 4.2 Definição de Packages

```
┌─────────────────────────────────────────────────────────────────────┐
│                    ESTRUTURA DE PACKAGES                            │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  PACKAGES A CRIAR                                                   │
│  ────────────────                                                   │
│                                                                     │
│  Package 1: ________________________________                        │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ Propósito:                                                    │  │
│  │ _____________________________________________________________│  │
│  │                                                               │  │
│  │ Componentes:                                                  │  │
│  │ ☐ Controllers    Quais: ________________________________     │  │
│  │ ☐ Models         Quais: ________________________________     │  │
│  │ ☐ Views          Quais: ________________________________     │  │
│  │ ☐ Listeners      Quais: ________________________________     │  │
│  │ ☐ Migrations     Quais: ________________________________     │  │
│  │ ☐ Assets         Quais: ________________________________     │  │
│  │ ☐ Traduções      Idiomas: ☐ en  ☐ pt_BR  ☐ ___             │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                     │
│  Package 2: ________________________________                        │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ Propósito:                                                    │  │
│  │ _____________________________________________________________│  │
│  │                                                               │  │
│  │ Componentes:                                                  │  │
│  │ ☐ Controllers    Quais: ________________________________     │  │
│  │ ☐ Models         Quais: ________________________________     │  │
│  │ ☐ Views          Quais: ________________________________     │  │
│  │ ☐ Listeners      Quais: ________________________________     │  │
│  │ ☐ Migrations     Quais: ________________________________     │  │
│  │ ☐ Assets         Quais: ________________________________     │  │
│  │ ☐ Traduções      Idiomas: ☐ en  ☐ pt_BR  ☐ ___             │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                     │
│  JUSTIFICATIVA DE SEPARAÇÃO:                                        │
│  ────────────────────────────                                       │
│  Por que separar em múltiplos packages (se aplicável)?              │
│  _________________________________________________________________  │
│  _________________________________________________________________  │
│                                                                     │
│  ORDEM NO config/modules.php:                                       │
│  ────────────────────────────                                       │
│  1. (Core packages existentes)                                      │
│  2. (Módulos funcionais)                                            │
│  ...                                                                │
│  N. Package1Name    ← Primeiro custom                               │
│  N+1. Package2Name  ← Último (maior prioridade de override)         │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 4.3 Diagrama de Arquitetura

```
┌─────────────────────────────────────────────────────────────────────┐
│                    DIAGRAMA DE ARQUITETURA                          │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  Use este template para desenhar a arquitetura:                     │
│                                                                     │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │                         USUÁRIO                              │   │
│  └──────────────────────────┬──────────────────────────────────┘   │
│                             │                                       │
│                             ▼                                       │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │                    CAMADA DE APRESENTAÇÃO                    │   │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐       │   │
│  │  │ View Login   │  │ View Lead    │  │ View ...     │       │   │
│  │  │ (override)   │  │ (override)   │  │              │       │   │
│  │  └──────────────┘  └──────────────┘  └──────────────┘       │   │
│  └──────────────────────────┬──────────────────────────────────┘   │
│                             │                                       │
│                             ▼                                       │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │                    CAMADA DE APLICAÇÃO                       │   │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐       │   │
│  │  │ Controller   │  │ Controller   │  │ Middleware   │       │   │
│  │  │ Lead (ovr)   │  │ ...          │  │              │       │   │
│  │  └──────────────┘  └──────────────┘  └──────────────┘       │   │
│  └──────────────────────────┬──────────────────────────────────┘   │
│                             │                                       │
│                             ▼                                       │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │                    CAMADA DE DOMÍNIO                         │   │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐       │   │
│  │  │ Model Lead   │  │ Listeners    │  │ Events       │       │   │
│  │  │ (override)   │  │              │  │              │       │   │
│  │  └──────────────┘  └──────────────┘  └──────────────┘       │   │
│  └──────────────────────────┬──────────────────────────────────┘   │
│                             │                                       │
│                             ▼                                       │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │                    CAMADA DE INFRAESTRUTURA                  │   │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐       │   │
│  │  │ MySQL        │  │ Redis        │  │ API Externa  │       │   │
│  │  │              │  │              │  │              │       │   │
│  │  └──────────────┘  └──────────────┘  └──────────────┘       │   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 4.4 Fluxo de Dados

```
┌─────────────────────────────────────────────────────────────────────┐
│                    FLUXO DE DADOS                                   │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  FLUXO 1: ________________________________                          │
│                                                                     │
│  ┌────────┐    ┌────────┐    ┌────────┐    ┌────────┐             │
│  │        │───▶│        │───▶│        │───▶│        │             │
│  │        │    │        │    │        │    │        │             │
│  └────────┘    └────────┘    └────────┘    └────────┘             │
│                                                                     │
│  Descrição: __________________________________________________      │
│  ________________________________________________________________   │
│                                                                     │
│  FLUXO 2: ________________________________                          │
│                                                                     │
│  ┌────────┐    ┌────────┐    ┌────────┐    ┌────────┐             │
│  │        │───▶│        │───▶│        │───▶│        │             │
│  │        │    │        │    │        │    │        │             │
│  └────────┘    └────────┘    └────────┘    └────────┘             │
│                                                                     │
│  Descrição: __________________________________________________      │
│  ________________________________________________________________   │
│                                                                     │
│  FLUXO 3: ________________________________                          │
│                                                                     │
│  ┌────────┐    ┌────────┐    ┌────────┐    ┌────────┐             │
│  │        │───▶│        │───▶│        │───▶│        │             │
│  │        │    │        │    │        │    │        │             │
│  └────────┘    └────────┘    └────────┘    └────────┘             │
│                                                                     │
│  Descrição: __________________________________________________      │
│  ________________________________________________________________   │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 4.5 Decisões Arquiteturais

```
┌─────────────────────────────────────────────────────────────────────┐
│                    REGISTRO DE DECISÕES (ADR)                       │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ADR-001: ________________________________                          │
│  ─────────────────────────────────────────                          │
│  Status: ☐ Proposta  ☐ Aceita  ☐ Rejeitada  ☐ Depreciada           │
│  Data: ____/____/________                                           │
│                                                                     │
│  Contexto:                                                          │
│  ________________________________________________________________   │
│  ________________________________________________________________   │
│                                                                     │
│  Decisão:                                                           │
│  ________________________________________________________________   │
│  ________________________________________________________________   │
│                                                                     │
│  Alternativas consideradas:                                         │
│  1. ____________________________________________________________   │
│  2. ____________________________________________________________   │
│                                                                     │
│  Consequências:                                                     │
│  + ____________________________________________________________    │
│  + ____________________________________________________________    │
│  - ____________________________________________________________    │
│                                                                     │
│  ═══════════════════════════════════════════════════════════════   │
│                                                                     │
│  ADR-002: ________________________________                          │
│  ─────────────────────────────────────────                          │
│  Status: ☐ Proposta  ☐ Aceita  ☐ Rejeitada  ☐ Depreciada           │
│  Data: ____/____/________                                           │
│                                                                     │
│  Contexto:                                                          │
│  ________________________________________________________________   │
│  ________________________________________________________________   │
│                                                                     │
│  Decisão:                                                           │
│  ________________________________________________________________   │
│  ________________________________________________________________   │
│                                                                     │
│  Alternativas consideradas:                                         │
│  1. ____________________________________________________________   │
│  2. ____________________________________________________________   │
│                                                                     │
│  Consequências:                                                     │
│  + ____________________________________________________________    │
│  + ____________________________________________________________    │
│  - ____________________________________________________________    │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 4.6 Entregáveis da Fase 4

- [ ] Packages definidos com componentes
- [ ] Diagrama de arquitetura desenhado
- [ ] Fluxos de dados documentados
- [ ] Decisões arquiteturais registradas
- [ ] Estrutura aprovada pelos stakeholders técnicos

---

## ⏱️ FASE 5: ESTIMATIVA E CRONOGRAMA

### 5.1 Objetivo

Calcular esforço realista e definir cronograma factível.

### 5.2 Tabela de Estimativas

```
┌─────────────────────────────────────────────────────────────────────┐
│                    ESTIMATIVA DETALHADA                             │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  FATORES DE AJUSTE:                                                 │
│  ──────────────────                                                 │
│  • Experiência da equipe com Krayin: ☐ Baixa (1.5x) ☐ Média (1.0x) ☐ Alta (0.8x) │
│  • Clareza dos requisitos: ☐ Baixa (1.3x) ☐ Média (1.0x) ☐ Alta (0.9x)          │
│  • Complexidade de integrações: ☐ Baixa (1.0x) ☐ Média (1.2x) ☐ Alta (1.5x)     │
│                                                                     │
│  Fator multiplicador total: _______                                 │
│                                                                     │
│  ┌─────┬──────────────────────────┬─────────┬─────────┬───────────┐│
│  │ ID  │ Item                     │ Base(h) │ Ajuste  │ Final(h)  ││
│  ├─────┼──────────────────────────┼─────────┼─────────┼───────────┤│
│  │     │                          │         │ x       │           ││
│  ├─────┼──────────────────────────┼─────────┼─────────┼───────────┤│
│  │     │                          │         │ x       │           ││
│  ├─────┼──────────────────────────┼─────────┼─────────┼───────────┤│
│  │     │                          │         │ x       │           ││
│  ├─────┼──────────────────────────┼─────────┼─────────┼───────────┤│
│  │     │                          │         │ x       │           ││
│  ├─────┼──────────────────────────┼─────────┼─────────┼───────────┤│
│  │     │                          │         │ x       │           ││
│  ├─────┼──────────────────────────┼─────────┼─────────┼───────────┤│
│  │     │                          │         │ x       │           ││
│  ├─────┼──────────────────────────┼─────────┼─────────┼───────────┤│
│  │     │                          │         │ x       │           ││
│  ├─────┼──────────────────────────┼─────────┼─────────┼───────────┤│
│  │     │                          │         │ x       │           ││
│  ├─────┴──────────────────────────┴─────────┴─────────┴───────────┤│
│  │ SUBTOTAL DESENVOLVIMENTO                          │            ││
│  ├────────────────────────────────────────────────────┼───────────┤│
│  │ Testes (20% do dev)                               │            ││
│  ├────────────────────────────────────────────────────┼───────────┤│
│  │ Code Review (10% do dev)                          │            ││
│  ├────────────────────────────────────────────────────┼───────────┤│
│  │ Documentação (10% do dev)                         │            ││
│  ├────────────────────────────────────────────────────┼───────────┤│
│  │ Deploy e Validação                                │            ││
│  ├────────────────────────────────────────────────────┼───────────┤│
│  │ Buffer (15% para imprevistos)                     │            ││
│  ├────────────────────────────────────────────────────┼───────────┤│
│  │ TOTAL GERAL                                       │            ││
│  └────────────────────────────────────────────────────┴───────────┘│
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 5.3 Cronograma

```
┌─────────────────────────────────────────────────────────────────────┐
│                    CRONOGRAMA DO PROJETO                            │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  DADOS DO PROJETO                                                   │
│  ─────────────────                                                  │
│  • Total de horas estimadas: _______ horas                          │
│  • Desenvolvedores alocados: _______                                │
│  • Horas/dia por dev: _______                                       │
│  • Dias úteis necessários: _______ dias                             │
│  • Data de início: ____/____/________                               │
│  • Data prevista de término: ____/____/________                     │
│                                                                     │
│  FASES DO PROJETO                                                   │
│  ────────────────                                                   │
│                                                                     │
│  Semana 1: ____/____  a  ____/____                                  │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ ☐ Setup ambiente                                              │  │
│  │ ☐ Criar estrutura de packages                                 │  │
│  │ ☐ _______________________________________________________    │  │
│  │ ☐ _______________________________________________________    │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                     │
│  Semana 2: ____/____  a  ____/____                                  │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ ☐ _______________________________________________________    │  │
│  │ ☐ _______________________________________________________    │  │
│  │ ☐ _______________________________________________________    │  │
│  │ ☐ _______________________________________________________    │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                     │
│  Semana 3: ____/____  a  ____/____                                  │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ ☐ _______________________________________________________    │  │
│  │ ☐ _______________________________________________________    │  │
│  │ ☐ _______________________________________________________    │  │
│  │ ☐ _______________________________________________________    │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                     │
│  Semana 4: ____/____  a  ____/____                                  │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ ☐ Testes finais                                               │  │
│  │ ☐ Documentação                                                │  │
│  │ ☐ Deploy staging                                              │  │
│  │ ☐ Validação com stakeholders                                  │  │
│  │ ☐ Deploy produção                                             │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                     │
│  MARCOS (MILESTONES)                                                │
│  ───────────────────                                                │
│  M1: _________________________ Data: ____/____/________             │
│  M2: _________________________ Data: ____/____/________             │
│  M3: _________________________ Data: ____/____/________             │
│  M4: _________________________ Data: ____/____/________             │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 5.4 Alocação de Recursos

```
┌─────────────────────────────────────────────────────────────────────┐
│                    ALOCAÇÃO DE RECURSOS                             │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  EQUIPE DO PROJETO                                                  │
│  ─────────────────                                                  │
│                                                                     │
│  ┌──────────────────┬─────────────┬─────────────┬──────────────────┐│
│  │ Nome             │ Papel       │ Alocação    │ Período          ││
│  ├──────────────────┼─────────────┼─────────────┼──────────────────┤│
│  │                  │ ☐ Dev       │      %      │ _____ a _____    ││
│  │                  │ ☐ Tech Lead │             │                  ││
│  │                  │ ☐ QA        │             │                  ││
│  ├──────────────────┼─────────────┼─────────────┼──────────────────┤│
│  │                  │ ☐ Dev       │      %      │ _____ a _____    ││
│  │                  │ ☐ Tech Lead │             │                  ││
│  │                  │ ☐ QA        │             │                  ││
│  ├──────────────────┼─────────────┼─────────────┼──────────────────┤│
│  │                  │ ☐ Dev       │      %      │ _____ a _____    ││
│  │                  │ ☐ Tech Lead │             │                  ││
│  │                  │ ☐ QA        │             │                  ││
│  └──────────────────┴─────────────┴─────────────┴──────────────────┘│
│                                                                     │
│  RESPONSABILIDADES                                                  │
│  ─────────────────                                                  │
│                                                                     │
│  ┌──────────────────┬──────────────────────────────────────────────┐│
│  │ Pessoa           │ Responsável por                              ││
│  ├──────────────────┼──────────────────────────────────────────────┤│
│  │                  │                                              ││
│  ├──────────────────┼──────────────────────────────────────────────┤│
│  │                  │                                              ││
│  ├──────────────────┼──────────────────────────────────────────────┤│
│  │                  │                                              ││
│  └──────────────────┴──────────────────────────────────────────────┘│
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 5.5 Entregáveis da Fase 5

- [ ] Estimativas detalhadas por item
- [ ] Cronograma com datas
- [ ] Marcos definidos
- [ ] Recursos alocados
- [ ] Aprovação do cronograma pelos stakeholders

---

## 📝 FASE 6: DOCUMENTAÇÃO DO PROJETO

### 6.1 Objetivo

Formalizar todo o levantamento em documentação oficial do projeto.

### 6.2 Documento de Escopo (SOW)

```
┌─────────────────────────────────────────────────────────────────────┐
│            DOCUMENTO DE ESCOPO - STATEMENT OF WORK                  │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  IDENTIFICAÇÃO                                                      │
│  ─────────────                                                      │
│  Projeto: ________________________________________                  │
│  Código: _________________________________________                  │
│  Versão: ____  Data: ____/____/________                             │
│  Autor: _________________________________________                   │
│  Aprovador: _____________________________________                   │
│                                                                     │
│  ═══════════════════════════════════════════════════════════════   │
│                                                                     │
│  1. VISÃO GERAL                                                     │
│  ───────────────                                                    │
│  1.1 Objetivo do Projeto                                            │
│  ________________________________________________________________   │
│  ________________________________________________________________   │
│  ________________________________________________________________   │
│                                                                     │
│  1.2 Problema a Resolver                                            │
│  ________________________________________________________________   │
│  ________________________________________________________________   │
│                                                                     │
│  1.3 Benefícios Esperados                                           │
│  • ____________________________________________________________    │
│  • ____________________________________________________________    │
│  • ____________________________________________________________    │
│                                                                     │
│  ═══════════════════════════════════════════════════════════════   │
│                                                                     │
│  2. ESCOPO                                                          │
│  ─────────                                                          │
│  2.1 Incluso no Escopo (MUST HAVE)                                  │
│  • ____________________________________________________________    │
│  • ____________________________________________________________    │
│  • ____________________________________________________________    │
│                                                                     │
│  2.2 Desejável (SHOULD/COULD HAVE)                                  │
│  • ____________________________________________________________    │
│  • ____________________________________________________________    │
│                                                                     │
│  2.3 Fora do Escopo (WON'T HAVE)                                    │
│  • ____________________________________________________________    │
│  • ____________________________________________________________    │
│                                                                     │
│  ═══════════════════════════════════════════════════════════════   │
│                                                                     │
│  3. REQUISITOS FUNCIONAIS                                           │
│  ─────────────────────────                                          │
│  RF01: _________________________________________________________   │
│  RF02: _________________________________________________________   │
│  RF03: _________________________________________________________   │
│  RF04: _________________________________________________________   │
│  RF05: _________________________________________________________   │
│                                                                     │
│  ═══════════════════════════════════════════════════════════════   │
│                                                                     │
│  4. REQUISITOS NÃO-FUNCIONAIS                                       │
│  ─────────────────────────────                                      │
│  RNF01: ________________________________________________________   │
│  RNF02: ________________________________________________________   │
│  RNF03: ________________________________________________________   │
│                                                                     │
│  ═══════════════════════════════════════════════════════════════   │
│                                                                     │
│  5. ENTREGAS                                                        │
│  ───────────                                                        │
│  ┌─────┬────────────────────────────┬────────────────┬─────────────┐│
│  │ ID  │ Entrega                    │ Data Prevista  │ Responsável ││
│  ├─────┼────────────────────────────┼────────────────┼─────────────┤│
│  │ E01 │                            │                │             ││
│  ├─────┼────────────────────────────┼────────────────┼─────────────┤│
│  │ E02 │                            │                │             ││
│  ├─────┼────────────────────────────┼────────────────┼─────────────┤│
│  │ E03 │                            │                │             ││
│  ├─────┼────────────────────────────┼────────────────┼─────────────┤│
│  │ E04 │                            │                │             ││
│  └─────┴────────────────────────────┴────────────────┴─────────────┘│
│                                                                     │
│  ═══════════════════════════════════════════════════════════════   │
│                                                                     │
│  6. CRONOGRAMA RESUMIDO                                             │
│  ───────────────────────                                            │
│  Início: ____/____/________                                         │
│  Término: ____/____/________                                        │
│  Duração: _______ dias úteis                                        │
│                                                                     │
│  ═══════════════════════════════════════════════════════════════   │
│                                                                     │
│  7. PREMISSAS E RESTRIÇÕES                                          │
│  ──────────────────────────                                         │
│  Premissas:                                                         │
│  • ____________________________________________________________    │
│  • ____________________________________________________________    │
│                                                                     │
│  Restrições:                                                        │
│  • ____________________________________________________________    │
│  • ____________________________________________________________    │
│                                                                     │
│  ═══════════════════════════════════════════════════════════════   │
│                                                                     │
│  8. RISCOS IDENTIFICADOS                                            │
│  ─────────────────────────                                          │
│  • ____________________________________________________________    │
│  • ____________________________________________________________    │
│  • ____________________________________________________________    │
│                                                                     │
│  ═══════════════════════════════════════════════════════════════   │
│                                                                     │
│  9. CRITÉRIOS DE ACEITE                                             │
│  ───────────────────────                                            │
│  O projeto será considerado completo quando:                        │
│  ☐ ____________________________________________________________    │
│  ☐ ____________________________________________________________    │
│  ☐ ____________________________________________________________    │
│                                                                     │
│  ═══════════════════════════════════════════════════════════════   │
│                                                                     │
│  10. APROVAÇÕES                                                     │
│  ───────────────                                                    │
│                                                                     │
│  Solicitante:                                                       │
│  Nome: _________________________ Data: ____/____/________           │
│  Assinatura: _______________________                                │
│                                                                     │
│  Gerente de Projeto:                                                │
│  Nome: _________________________ Data: ____/____/________           │
│  Assinatura: _______________________                                │
│                                                                     │
│  Tech Lead:                                                         │
│  Nome: _________________________ Data: ____/____/________           │
│  Assinatura: _______________________                                │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 6.3 Backlog do Projeto

```
┌─────────────────────────────────────────────────────────────────────┐
│                    BACKLOG DO PROJETO                               │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ÉPICO 1: ________________________________                          │
│  ─────────────────────────────────────────                          │
│                                                                     │
│  ┌─────────┬────────────────────────────────┬──────────┬───────────┐│
│  │ ID      │ User Story                     │ Pontos   │ Sprint    ││
│  ├─────────┼────────────────────────────────┼──────────┼───────────┤│
│  │ US-001  │ Como [persona], quero [ação],  │          │           ││
│  │         │ para [benefício]               │          │           ││
│  ├─────────┼────────────────────────────────┼──────────┼───────────┤│
│  │ US-002  │                                │          │           ││
│  ├─────────┼────────────────────────────────┼──────────┼───────────┤│
│  │ US-003  │                                │          │           ││
│  └─────────┴────────────────────────────────┴──────────┴───────────┘│
│                                                                     │
│  ÉPICO 2: ________________________________                          │
│  ─────────────────────────────────────────                          │
│                                                                     │
│  ┌─────────┬────────────────────────────────┬──────────┬───────────┐│
│  │ ID      │ User Story                     │ Pontos   │ Sprint    ││
│  ├─────────┼────────────────────────────────┼──────────┼───────────┤│
│  │ US-004  │                                │          │           ││
│  ├─────────┼────────────────────────────────┼──────────┼───────────┤│
│  │ US-005  │                                │          │           ││
│  ├─────────┼────────────────────────────────┼──────────┼───────────┤│
│  │ US-006  │                                │          │           ││
│  └─────────┴────────────────────────────────┴──────────┴───────────┘│
│                                                                     │
│  TAREFAS TÉCNICAS                                                   │
│  ────────────────                                                   │
│                                                                     │
│  ┌─────────┬────────────────────────────────┬──────────┬───────────┐│
│  │ ID      │ Tarefa                         │ Horas    │ Sprint    ││
│  ├─────────┼────────────────────────────────┼──────────┼───────────┤│
│  │ TEC-001 │ Setup package structure        │          │ 1         ││
│  ├─────────┼────────────────────────────────┼──────────┼───────────┤│
│  │ TEC-002 │ Configurar CI/CD               │          │ 1         ││
│  ├─────────┼────────────────────────────────┼──────────┼───────────┤│
│  │ TEC-003 │ Documentação técnica           │          │ Final     ││
│  └─────────┴────────────────────────────────┴──────────┴───────────┘│
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 6.4 Entregáveis da Fase 6

- [ ] Documento de Escopo (SOW) aprovado
- [ ] Backlog criado e priorizado
- [ ] Repositório Git configurado
- [ ] Board de tarefas criado (Jira, Trello, etc.)
- [ ] Kick-off realizado com a equipe

---

## 📄 TEMPLATES PRONTOS

### Template: Ficha Rápida de Projeto

```markdown
# Ficha Rápida: [Nome do Projeto]

## Resumo
| Campo | Valor |
|-------|-------|
| Código | PRJ-XXX |
| Sponsor | Nome |
| Data Início | DD/MM/AAAA |
| Data Término | DD/MM/AAAA |
| Esforço Total | XXX horas |
| Equipe | X devs |

## Escopo (3 frases)
1. [O que será feito - principal]
2. [O que será feito - secundário]
3. [O que NÃO será feito]

## Entregas
- [ ] Entrega 1 - Data
- [ ] Entrega 2 - Data
- [ ] Entrega 3 - Data

## Riscos Principais
1. Risco A - Mitigação
2. Risco B - Mitigação

## Contatos
- PM: Nome (email)
- Tech Lead: Nome (email)
- Sponsor: Nome (email)
```

### Template: Especificação de Requisito

```markdown
# REQ-XXX: [Nome do Requisito]

## Identificação
- **ID:** REQ-XXX
- **Tipo:** Funcional / Não-Funcional
- **Prioridade:** Must / Should / Could
- **Sprint:** X

## Descrição
Como [persona], quero [ação], para [benefício].

## Critérios de Aceite
- [ ] Critério 1
- [ ] Critério 2
- [ ] Critério 3

## Solução Técnica
- **Abordagem:** Controller Override / Model Override / View Override / Event Listener
- **Package:** Webkul/NomePackage
- **Arquivos afetados:**
  - path/to/file1.php
  - path/to/file2.blade.php

## Dependências
- REQ-YYY (descrição)

## Estimativa
- Desenvolvimento: X horas
- Testes: Y horas
- Total: Z horas

## Observações
[Notas adicionais]
```

### Template: Relatório de Mapeamento

```markdown
# Relatório de Mapeamento: [Nome do Projeto]

**Data:** DD/MM/AAAA  
**Autor:** Nome  
**Versão:** 1.0

---

## 1. Resumo Executivo
[2-3 parágrafos resumindo o projeto, escopo e principais decisões]

## 2. Escopo Definido

### 2.1 Incluso
| ID | Item | Prioridade |
|----|------|------------|
| 01 | ... | Must |
| 02 | ... | Should |

### 2.2 Excluído
- Item A
- Item B

## 3. Análise Técnica

### 3.1 Views a Customizar
| Tela | Path | Ação |
|------|------|------|
| Login | packages/Webkul/Admin/... | Override |

### 3.2 Campos Novos
| Entidade | Campo | Tipo |
|----------|-------|------|
| Lead | priority | enum |

### 3.3 Eventos/Automações
| Gatilho | Ação |
|---------|------|
| lead.create.after | Enviar email |

## 4. Arquitetura

### 4.1 Packages
- **CustomTheme:** UI e visual
- **CustomWorkflow:** Automações

### 4.2 Decisões Arquiteturais
1. ADR-001: [Decisão]
2. ADR-002: [Decisão]

## 5. Estimativas

| Fase | Horas |
|------|-------|
| Desenvolvimento | XX |
| Testes | XX |
| Deploy | XX |
| **Total** | **XX** |

## 6. Cronograma

| Marco | Data |
|-------|------|
| Início | DD/MM |
| M1: MVP | DD/MM |
| M2: Completo | DD/MM |
| Go-Live | DD/MM |

## 7. Riscos

| Risco | Probabilidade | Impacto | Mitigação |
|-------|---------------|---------|-----------|
| ... | Média | Alto | ... |

## 8. Próximos Passos
1. [ ] Aprovar documento
2. [ ] Criar backlog
3. [ ] Iniciar desenvolvimento

---

**Aprovações:**

| Nome | Papel | Data | Assinatura |
|------|-------|------|------------|
| | Sponsor | | |
| | Tech Lead | | |
```

---

## ✅ CHECKLISTS DE VALIDAÇÃO

### Checklist: Descoberta Completa

```
☐ Objetivo do projeto definido e documentado
☐ Problema a resolver está claro
☐ Stakeholders identificados
☐ Usuários afetados mapeados
☐ Escopo MUST definido
☐ Escopo WON'T definido (exclusões)
☐ Prazo conhecido
☐ Orçamento conhecido ou definido
☐ Restrições técnicas documentadas
☐ Critérios de sucesso estabelecidos
☐ Matriz MoSCoW preenchida
```

### Checklist: Levantamento Técnico Completo

```
☐ Todas as telas mapeadas com Blade Tracer
☐ Campos novos identificados com tipos
☐ Fluxos/automações documentados
☐ Integrações especificadas
☐ Assets visuais coletados ou solicitados
☐ Cores e identidade visual definidos
☐ Traduções necessárias listadas
```

### Checklist: Análise de Impacto Completa

```
☐ Abordagem técnica definida para cada item
☐ Complexidade estimada por item
☐ Riscos identificados com mitigações
☐ Dependências mapeadas
☐ Ordem de execução definida
```

### Checklist: Arquitetura Definida

```
☐ Packages a criar definidos
☐ Componentes de cada package listados
☐ Diagrama de arquitetura desenhado
☐ Fluxos de dados documentados
☐ Decisões arquiteturais registradas
☐ Revisão técnica realizada
```

### Checklist: Projeto Pronto para Iniciar

```
☐ Documento de Escopo aprovado
☐ Cronograma aprovado
☐ Recursos alocados
☐ Backlog criado
☐ Repositório configurado
☐ Ambiente de desenvolvimento pronto
☐ Kick-off realizado
☐ Equipe ciente das responsabilidades
```

---

## 📊 MÉTRICAS DE QUALIDADE DO MAPEAMENTO

### Indicadores de Mapeamento Bem Feito

| Indicador | Meta | Como Medir |
|-----------|------|------------|
| Cobertura de requisitos | 100% | Todos os MUST têm especificação técnica |
| Clareza de estimativas | < 20% variação | Comparar estimado vs realizado |
| Riscos antecipados | > 80% | Riscos que ocorreram estavam mapeados |
| Mudanças de escopo | < 15% | % de itens adicionados após aprovação |
| Retrabalho | < 10% | % de código refeito por requisito mal definido |

### Sinais de Alerta

```
⚠️ ATENÇÃO SE:
- Muitos itens "a definir" no documento
- Estimativas sem base técnica (chutadas)
- Nenhum risco identificado
- Escopo WON'T vazio (tudo incluído?)
- Cronograma sem buffer
- Aprovação sem revisão técnica
```

---

## 🔄 PROCESSO RESUMIDO

```
┌─────────────────────────────────────────────────────────────────────┐
│                    RESUMO DO PROCESSO                               │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  1️⃣  DESCOBERTA (1-2 dias)                                          │
│      → Questionário + MoSCoW + Stakeholders                         │
│                                                                     │
│  2️⃣  LEVANTAMENTO TÉCNICO (2-3 dias)                                │
│      → Inventário Telas/Campos/Fluxos/Integrações/Visual            │
│      → Usar Blade Tracer!                                           │
│                                                                     │
│  3️⃣  ANÁLISE DE IMPACTO (1-2 dias)                                  │
│      → Decisão técnica + Complexidade + Riscos + Dependências       │
│                                                                     │
│  4️⃣  ARQUITETURA (1 dia)                                            │
│      → Packages + Diagrama + Fluxos + ADRs                          │
│                                                                     │
│  5️⃣  ESTIMATIVA (1 dia)                                             │
│      → Horas + Cronograma + Recursos                                │
│                                                                     │
│  6️⃣  DOCUMENTAÇÃO (1 dia)                                           │
│      → SOW + Backlog + Aprovações                                   │
│                                                                     │
│  ═══════════════════════════════════════════════════════════════   │
│                                                                     │
│  TEMPO TOTAL: 7-10 dias para projeto médio                          │
│                                                                     │
│  ENTREGÁVEIS FINAIS:                                                │
│  ✓ Documento de Escopo aprovado                                     │
│  ✓ Backlog priorizado                                               │
│  ✓ Cronograma com marcos                                            │
│  ✓ Equipe alocada                                                   │
│  ✓ Projeto pronto para desenvolvimento                              │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

**Versão:** 1.0.0  
**Data:** Dezembro 2025  

---

*"Projetos bem mapeados são projetos bem executados."*
