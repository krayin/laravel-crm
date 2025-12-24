# Documentacao - Krayin CRM Customizado

> Indice principal de toda documentacao do projeto.

## Estrutura de Pastas

```
docs/
├── 00-overview/          # Visao geral, contexto para IA
├── 01-architecture/      # Arquitetura, configuracao, instalacao
├── 02-implementation/    # Correcoes aplicadas, implementacoes
├── 03-testing/           # Checklists, roteiros, resultados
├── 04-features/          # Documentacao por feature
│   ├── theme-manager/    # Theme Manager do Krayin
│   └── login-theme/      # Sistema multi-tema de login
├── 05-operations/        # Deploy, runbooks, troubleshooting
│   ├── runbooks/         # Procedimentos operacionais
│   └── troubleshooting/  # Diagnosticos e debug
├── onboarding/           # Guias para novos desenvolvedores
├── _logs/                # Logs de desenvolvimento
└── _archive/             # Arquivos arquivados/obsoletos
```

## Guia Rapido

| Voce quer... | Veja |
|--------------|------|
| Entender o projeto | [00-overview/](./00-overview/) |
| Configurar ambiente | [01-architecture/](./01-architecture/) |
| Ver o que foi implementado | [02-implementation/](./02-implementation/) |
| Testar funcionalidades | [03-testing/](./03-testing/) |
| Entender o Theme Manager | [04-features/theme-manager/](./04-features/theme-manager/) |
| Entender o Login Theme | [04-features/login-theme/](./04-features/login-theme/) |
| Fazer deploy | [05-operations/runbooks/](./05-operations/runbooks/) |
| Resolver problemas | [05-operations/troubleshooting/](./05-operations/troubleshooting/) |
| Comecar como novo dev | [onboarding/](./onboarding/) |

## Documentos Principais

### Features
- **[Login Theme](./04-features/login-theme/README.md)** - Sistema multi-tema upgrade-safe
- **[Theme Manager](./04-features/theme-manager/README.md)** - Gerenciador de temas do Krayin

### Operacoes
- **[Runbook Theme Smoke](./05-operations/runbooks/RUNBOOK_THEME_SMOKE.md)** - Validacao pos-deploy

### Onboarding
- **[Guia Onboarding](./onboarding/ONBOARDING_DEV_KRAYIN.md)** - Para novos desenvolvedores
- **[Checklist Validacao](./onboarding/CHECKLIST_VALIDACAO_DEV.md)** - Verificacao de ambiente

## Changelogs

| Arquivo | Descricao |
|---------|-----------|
| [CHANGELOG.md](./CHANGELOG.md) | Historico geral de customizacoes |
| [CHANGELOG_THEME_REFACTORING.md](./CHANGELOG_THEME_REFACTORING.md) | Refatoracao do sistema de temas |

## Scripts e Ferramentas

Veja [../tools/README.md](../tools/README.md) para scripts de deploy e automacao.

---

**Ultima atualizacao:** 2024-12-24
