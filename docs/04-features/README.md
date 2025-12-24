# Features

Documentacao das features customizadas do Krayin CRM.

## Subpastas

| Pasta | Descricao |
|-------|-----------|
| [theme-manager/](./theme-manager/) | Theme Manager - gerenciador de temas do pacote Webkul |
| [login-theme/](./login-theme/) | Login Theme - sistema multi-tema para tela de login (upgrade-safe) |

## Arquitetura

```
┌─────────────────────────────────────────────────────────────┐
│                      Theme System                            │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌─────────────────┐     ┌─────────────────┐                │
│  │  Theme Manager  │────▶│  Login Theme    │                │
│  │  (packages/)    │     │  (app/)         │                │
│  └─────────────────┘     └─────────────────┘                │
│         │                        │                          │
│         ▼                        ▼                          │
│  ┌─────────────────┐     ┌─────────────────┐                │
│  │ theme_configs   │     │ ThemeContext    │                │
│  │ (database)      │     │ Factory         │                │
│  └─────────────────┘     └─────────────────┘                │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

## Status

| Feature | Status | Versao |
|---------|--------|--------|
| Theme Manager | Funcional | v2.1.5 (package) |
| Login Theme | Funcional | v2.0.0 (refatorado) |
