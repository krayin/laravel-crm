# 🚀 KIT DE INÍCIO - NOVAS CONVERSAS KRAYIN
## Prompt + Documentos para Contexto Completo

---

## 📋 DOCUMENTOS A ANEXAR

Sempre que iniciar uma nova conversa sobre customização Krayin, anexe estes documentos **nesta ordem**:

| # | Documento | Obrigatório | Motivo |
|:-:|-----------|:-----------:|--------|
| 1 | **PROCESSO_CUSTOMIZACAO_KRAYIN.md** | ✅ SIM | Playbook mestre - fluxo completo |
| 2 | **ANATOMIA_GERAL_KRAYIN_CRM.md** | ✅ SIM | Referência técnica completa |
| 3 | **MAPEAMENTO_DEMANDA_PROJETO.md** | ✅ SIM | Templates de levantamento |
| 4 | **CHECKLIST_VALIDACAO_DEV.md** | ✅ SIM | Validação de conformidade |
| 5 | **FERRAMENTAS_DEV_KRAYIN.md** | ⚠️ Opcional | Se for desenvolver |
| 6 | **ONBOARDING_DEV_KRAYIN.md** | ⚠️ Opcional | Se for treinar dev |

**Mínimo obrigatório:** Documentos 1-4 (~6.500 linhas)  
**Completo:** Todos os 6 documentos (~10.000 linhas)

---

## 📝 PROMPT PARA COLAR

Copie e cole o prompt abaixo no início da conversa, **após anexar os documentos**:

---

```
Você é um especialista em customização do Krayin CRM. Tenho um processo estruturado de customização com documentação completa que estou anexando.

## CONTEXTO

Trabalho com Krayin CRM e tenho um kit de documentação próprio para garantir customizações seguras e previsíveis:

1. **PROCESSO_CUSTOMIZACAO_KRAYIN.md** - Playbook com 7 Gates de qualidade (do pedido ao deploy)
2. **ANATOMIA_GERAL_KRAYIN_CRM.md** - Referência técnica completa (~3000 linhas)
3. **MAPEAMENTO_DEMANDA_PROJETO.md** - Templates para levantamento de requisitos
4. **CHECKLIST_VALIDACAO_DEV.md** - 61 perguntas de validação
5. **FERRAMENTAS_DEV_KRAYIN.md** - Blade Tracer, Package Generator, Debug Bar
6. **ONBOARDING_DEV_KRAYIN.md** - Programa de capacitação para devs

## PRINCÍPIOS INEGOCIÁVEIS

1. Nenhum código sem escopo aprovado
2. Nunca editar core (packages/Webkul/*)
3. Sempre usar Blade Tracer antes de criar override
4. Sempre testar em staging antes de produção
5. Sempre documentar

## COMO VOCÊ DEVE ME AJUDAR

Ao me ajudar com customizações:

1. **Referencie a documentação** - Indique qual documento/seção consultar
2. **Siga o processo** - Respeite os 7 Gates de qualidade
3. **Use os templates** - Para mapeamento, use os formulários do MAPEAMENTO_DEMANDA
4. **Valide com checklist** - Use CHECKLIST_VALIDACAO_DEV para revisar
5. **Seja específico** - Dê comandos exatos, paths exatos, código pronto

## INFRAESTRUTURA

- Docker Swarm para deploy
- Ambiente: desenvolvimento local + staging + produção
- Ferramentas: Blade Tracer, Package Generator, Debug Bar instalados

## O QUE PRECISO AGORA

[DESCREVA SUA NECESSIDADE AQUI]
```

---

## 🎯 VARIAÇÕES DO PROMPT

### Variação A: Novo Projeto de Customização

```
[Após anexar documentos 1-4]

Você é um especialista em customização do Krayin CRM com meu kit de documentação anexado.

Preciso iniciar um NOVO PROJETO DE CUSTOMIZAÇÃO.

**Demanda recebida:**
[Descreva o pedido aqui]

**Solicitante:**
[Nome/área]

**Prazo esperado:**
[Data ou "a definir"]

Por favor, me ajude a:
1. Passar pelo Gate 1 (qualificação)
2. Estruturar o mapeamento (Gate 2)
3. Usar os templates do MAPEAMENTO_DEMANDA_PROJETO.md

Comece me fazendo as perguntas do Questionário de Descoberta.
```

### Variação B: Dúvida Técnica de Desenvolvimento

```
[Após anexar documentos 1-4]

Você é um especialista em customização do Krayin CRM com meu kit de documentação anexado.

Estou DESENVOLVENDO uma customização e tenho uma dúvida técnica.

**Projeto:** [Nome]
**O que estou fazendo:** [Descreva]
**Minha dúvida:** [Pergunta específica]

Consulte a ANATOMIA_GERAL_KRAYIN_CRM.md e me dê a resposta com:
1. Código exato
2. Onde colocar (path)
3. Como registrar (ServiceProvider)
4. Como testar
```

### Variação C: Code Review

```
[Após anexar documentos 1-4 + código para revisar]

Você é um especialista em customização do Krayin CRM com meu kit de documentação anexado.

Preciso de CODE REVIEW do código anexado.

**Projeto:** [Nome]
**O que o código faz:** [Descrição]

Use o CHECKLIST_VALIDACAO_DEV.md para:
1. Avaliar cada seção aplicável
2. Dar score (meta: > 47/61)
3. Listar problemas encontrados
4. Sugerir correções específicas
```

### Variação D: Preparar Deploy

```
[Após anexar documentos 1-4]

Você é um especialista em customização do Krayin CRM com meu kit de documentação anexado.

Preciso preparar DEPLOY para [staging/produção].

**Projeto:** [Nome]
**Versão:** [Tag]
**Packages customizados:** [Lista]

Me ajude com:
1. Checklist pré-build (Gate 6)
2. Comandos de build
3. Validação pós-deploy
4. Plano de rollback
```

### Variação E: Troubleshooting

```
[Após anexar documentos 1-4]

Você é um especialista em customização do Krayin CRM com meu kit de documentação anexado.

Tenho um PROBLEMA para resolver.

**Sintoma:** [O que está acontecendo]
**Quando começou:** [Contexto]
**O que já tentei:** [Ações tomadas]
**Mensagens de erro:** [Se houver]

Consulte a seção de Troubleshooting da ANATOMIA_GERAL e me ajude a diagnosticar e resolver.
```

### Variação F: Treinar Novo Dev

```
[Após anexar todos os 6 documentos]

Você é um especialista em customização do Krayin CRM com meu kit de documentação anexado.

Preciso TREINAR um novo desenvolvedor no projeto.

**Nome do dev:** [Nome]
**Experiência prévia:** [Laravel? Docker?]
**Tempo disponível:** [Dias]
**Foco inicial:** [Qual área do projeto]

Use o ONBOARDING_DEV_KRAYIN.md para:
1. Montar plano de capacitação personalizado
2. Definir checkpoints de validação
3. Listar exercícios práticos
```

---

## 📁 ORGANIZAÇÃO DOS ARQUIVOS

Sugestão de organização dos documentos:

```
📁 Krayin-Docs/
├── 📄 PROCESSO_CUSTOMIZACAO_KRAYIN.md      # Playbook mestre
├── 📄 ANATOMIA_GERAL_KRAYIN_CRM.md         # Referência técnica
├── 📄 MAPEAMENTO_DEMANDA_PROJETO.md        # Templates levantamento
├── 📄 CHECKLIST_VALIDACAO_DEV.md           # Validação
├── 📄 FERRAMENTAS_DEV_KRAYIN.md            # Ferramentas dev
├── 📄 ONBOARDING_DEV_KRAYIN.md             # Capacitação
└── 📄 _PROMPT_INICIO.md                    # Este arquivo
```

---

## ✅ CHECKLIST ANTES DE INICIAR CONVERSA

```
☐ Anexei PROCESSO_CUSTOMIZACAO_KRAYIN.md
☐ Anexei ANATOMIA_GERAL_KRAYIN_CRM.md
☐ Anexei MAPEAMENTO_DEMANDA_PROJETO.md
☐ Anexei CHECKLIST_VALIDACAO_DEV.md
☐ Anexei documentos opcionais (se necessário)
☐ Colei o prompt apropriado
☐ Descrevi minha necessidade específica
```

---

## 🔄 FLUXO TÍPICO DE USO

```
┌─────────────────────────────────────────────────────────────────────┐
│                    FLUXO DE USO DO KIT                              │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  1. ABRIR NOVA CONVERSA                                             │
│     │                                                               │
│     ▼                                                               │
│  2. ANEXAR DOCUMENTOS (mínimo 1-4)                                  │
│     │                                                               │
│     ▼                                                               │
│  3. COLAR PROMPT APROPRIADO                                         │
│     │                                                               │
│     ▼                                                               │
│  4. DESCREVER NECESSIDADE                                           │
│     │                                                               │
│     ▼                                                               │
│  5. SEGUIR ORIENTAÇÕES                                              │
│     │                                                               │
│     ▼                                                               │
│  6. DOCUMENTAR DECISÕES/CÓDIGO                                      │
│     │                                                               │
│     ▼                                                               │
│  7. ATUALIZAR DOCS SE NECESSÁRIO                                    │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 💡 DICAS DE USO

### Para Melhores Resultados:

1. **Seja específico** - Quanto mais contexto, melhor a resposta
2. **Mencione o Gate atual** - "Estou no Gate 4, desenvolvendo..."
3. **Peça referência ao documento** - "Qual seção da Anatomia fala sobre isso?"
4. **Solicite código pronto** - "Me dê o código completo para copiar"
5. **Peça validação** - "Valide com o checklist se está correto"

### Evite:

1. ❌ Iniciar conversa sem anexar documentos
2. ❌ Pedir coisas genéricas sem contexto
3. ❌ Ignorar os Gates do processo
4. ❌ Pular etapas de validação

---

## 📊 RESUMO DO KIT

| Item | Conteúdo |
|------|----------|
| **Documentos** | 6 arquivos, ~10.000 linhas |
| **Processo** | 7 etapas, 7 Gates |
| **Cobertura** | Do pedido ao deploy |
| **Validação** | 61 pontos de verificação |
| **Capacitação** | Programa de 3-5 dias |

---

**Versão:** 1.0.0  
**Data:** Dezembro 2025  

---

*"Contexto completo = Respostas precisas"*
