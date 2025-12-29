# Glossário do Projeto

Terminologia, convenções de nomenclatura e referência rápida para o projeto Krayin BrandKit.

---

## Índice

1. [Módulos Internos](#1-módulos-internos)
2. [Chaves de Cache](#2-chaves-de-cache)
3. [Convenções de Pastas](#3-convenções-de-pastas)
4. [Convenções de Nomenclatura](#4-convenções-de-nomenclatura)
5. [Terminologia de Domínio](#5-terminologia-de-domínio)
6. [Tabelas do Banco](#6-tabelas-do-banco)
7. [Eventos e Jobs](#7-eventos-e-jobs)
8. [Configurações](#8-configurações)

---

## 1. Módulos Internos

### Core Classes

| Classe | Namespace | Responsabilidade |
|--------|-----------|------------------|
| `BrandKitRepository` | `App\Support` | CRUD de overrides, CSS, snapshots. Camada de dados. |
| `BrandKitResolver` | `App\Support` | Resolução de config com cadeia: DEFAULTS ← theme.json ← overrides. Cache. |
| `CssValidator` | `App\Support` | Validação de segurança de CSS. Bloqueia padrões maliciosos. |
| `ThemeSelectionResolver` | `App\Support` | Resolve qual tema está selecionado (do banco). |
| `ThemeContext` | `App\Support` | DTO que carrega configuração resolvida para as views. |
| `ThemeContextFactory` | `App\Support` | Cria instâncias de `ThemeContext` com dependências injetadas. |

### Middleware

| Middleware | Responsabilidade |
|------------|------------------|
| `ShareThemeContext` | Injeta `$themeContext` em todas as views admin. |

### Controllers

| Controller | Responsabilidade |
|------------|------------------|
| `BrandKitController` | API/endpoints para gerenciar BrandKit (overrides, CSS, snapshots). |

### Models

| Model | Tabela | Descrição |
|-------|--------|-----------|
| `BrandKitOverride` | `brand_kit_overrides` | Sobrescritas de valores do tema. |
| `BrandKitCustomCss` | `brand_kit_custom_css` | CSS customizado por target (admin/login). |
| `BrandKitSnapshot` | `brand_kit_snapshots` | Backup pontual de estado. |

### Form Requests

| Request | Endpoints | Validação Principal |
|---------|-----------|---------------------|
| `SetOverrideRequest` | `POST /override` | `override_key` (string), `value` (nullable) |
| `AddCustomCssRequest` | `POST /custom-css` | `css_content` (max 50KB), segurança |
| `UpdateCustomCssRequest` | `PUT /custom-css/{id}` | Similar ao Add, com `id` |
| `ToggleCssRequest` | `POST /custom-css/{id}/toggle` | `is_enabled` (boolean) |
| `RestoreSnapshotRequest` | `POST /snapshot/restore` | `snapshot_id` (exists) |

---

## 2. Chaves de Cache

### Padrão de Nomenclatura

```
{modulo}.{tipo}.v{versao}.{scope}.{theme}
```

### Chaves Principais

| Chave | TTL | Descrição |
|-------|-----|-----------|
| `brand_kit.resolved.v1.global.default` | 1h | Config resolvida para tema default |
| `brand_kit.resolved.v1.global.dark` | 1h | Config resolvida para tema dark |
| `brand_kit.resolved.v1.{empresa_id}.default` | 1h | Config resolvida para empresa específica |
| `theme.selected_slug.v1` | 1h | Slug do tema selecionado |
| `brand_kit.css.v1.{id}` | 1h | CSS customizado cacheado |

### Versão de Cache (VERSION)

```php
// Em BrandKitResolver.php
private const VERSION = 1;  // Incrementar para invalidar todas as caches
```

### Invalidação

```php
// Uma chave específica
$resolver->invalidate('global', 'default');

// Todas as chaves globais (todos os temas)
$resolver->invalidateAllGlobal();

// Via artisan
php artisan cache:clear  // Nuclear - tudo
```

### Debug de Cache

```bash
# Redis - listar keys
redis-cli KEYS 'brand_kit.*'

# Redis - ver valor
redis-cli GET 'brand_kit.resolved.v1.global.default'

# File - localização
storage/framework/cache/data/
```

---

## 3. Convenções de Pastas

### Estrutura do Projeto

```
laravel-crm/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── BrandKitController.php      # Controller principal
│   │   ├── Middleware/
│   │   │   └── ShareThemeContext.php       # Injeta themeContext
│   │   └── Requests/
│   │       └── Admin/
│   │           └── BrandKit/               # Form Requests
│   │               ├── SetOverrideRequest.php
│   │               ├── AddCustomCssRequest.php
│   │               └── ...
│   ├── Models/
│   │   ├── BrandKitOverride.php
│   │   ├── BrandKitCustomCss.php
│   │   └── BrandKitSnapshot.php
│   ├── Providers/
│   │   └── AppServiceProvider.php          # Bindings do container
│   └── Support/                            # Classes de suporte/domínio
│       ├── BrandKitRepository.php
│       ├── BrandKitResolver.php
│       ├── CssValidator.php
│       ├── ThemeContext.php
│       ├── ThemeContextFactory.php
│       └── ThemeSelectionResolver.php
├── database/
│   └── migrations/
│       ├── 2025_12_25_205615_create_brand_kit_overrides_table.php
│       ├── 2025_12_25_205628_create_brand_kit_custom_css_table.php
│       └── 2025_12_25_205637_create_brand_kit_snapshots_table.php
├── docs/                                   # Documentação
│   ├── BRANDKIT_EVOLUTION.md
│   ├── DEPLOY_ENVIRONMENT.md
│   ├── GLOSSARY.md                         # Este arquivo
│   ├── INCIDENT_LOG.md
│   ├── KRAYIN_LESSONS_LEARNED.md
│   ├── REAL_WORLD_EXAMPLES.md
│   └── RUNBOOKS.md
├── resources/
│   └── views/
│       └── vendor/
│           └── admin/
│               └── partials/
│                   └── theme-head.blade.php  # Injeta CSS/variables
├── routes/
│   ├── brand-kit.php                       # Rotas do BrandKit
│   └── web.php
└── storage/
    └── app/
        └── public/
            ├── themes/                     # Presets de temas
            │   ├── default/
            │   │   └── theme.json
            │   └── dark/
            │       └── theme.json
            └── theme-manager/              # Uploads de usuários
                ├── logos/
                └── backgrounds/
```

### Convenções por Tipo de Arquivo

| Tipo | Local | Exemplo |
|------|-------|---------|
| Controller | `app/Http/Controllers/` | `BrandKitController.php` |
| Model | `app/Models/` | `BrandKitOverride.php` |
| Migration | `database/migrations/` | `2025_12_25_HHMMSS_create_xxx.php` |
| Form Request | `app/Http/Requests/Admin/{Feature}/` | `SetOverrideRequest.php` |
| Service/Support | `app/Support/` | `BrandKitRepository.php` |
| View customizada | `resources/views/vendor/{package}/` | `theme-head.blade.php` |
| Tema preset | `storage/app/public/themes/{slug}/` | `theme.json` |
| Upload usuário | `storage/app/public/theme-manager/` | `logo_123.png` |

---

## 4. Convenções de Nomenclatura

### PHP

| Tipo | Convenção | Exemplo |
|------|-----------|---------|
| Classe | PascalCase | `BrandKitRepository` |
| Método | camelCase | `setOverride()` |
| Variável | camelCase | `$themeContext` |
| Constante | UPPER_SNAKE | `BLOCKED_PATTERNS` |
| Arquivo | PascalCase.php | `BrandKitResolver.php` |

### Banco de Dados

| Tipo | Convenção | Exemplo |
|------|-----------|---------|
| Tabela | snake_case (plural) | `brand_kit_overrides` |
| Coluna | snake_case | `override_key` |
| FK | tabela_singular_id | `snapshot_id` |
| Index | idx_tabela_coluna | `idx_overrides_scope_theme` |
| Unique | uk_tabela_colunas | `uk_overrides_key_scope_theme` |

### Cache Keys

| Tipo | Convenção | Exemplo |
|------|-----------|---------|
| Chave | modulo.tipo.versao.params | `brand_kit.resolved.v1.global.default` |
| Separador | ponto (.) | |
| Versão | v + número | `v1` |

### Rotas

| Tipo | Convenção | Exemplo |
|------|-----------|---------|
| Prefixo | kebab-case | `/admin/brand-kit/` |
| Recurso | kebab-case | `/custom-css/{id}` |
| Ação | kebab-case | `/custom-css/{id}/toggle` |

### Views

| Tipo | Convenção | Exemplo |
|------|-----------|---------|
| Blade | kebab-case.blade.php | `theme-head.blade.php` |
| Partial | _prefixo ou diretório partials/ | `partials/theme-head` |
| Componente | kebab-case | `<x-brand-kit-preview />` |

### Variáveis Blade

| Variável | Tipo | Conteúdo |
|----------|------|----------|
| `$themeContext` | `ThemeContext` | Config resolvida completa |
| `$themeConfig` | `array` | Subset da config para view específica |

---

## 5. Terminologia de Domínio

### Conceitos Principais

| Termo | Definição |
|-------|-----------|
| **BrandKit** | Sistema de customização visual do CRM (cores, logos, CSS). |
| **Override** | Sobrescrita de um valor específico do tema (ex: `color_primary`). |
| **Preset** | Tema base pré-configurado (ex: default, dark). Arquivo `theme.json`. |
| **Theme** | Conjunto de configurações visuais. Identificado por `theme_slug`. |
| **Scope** | Nível de aplicação: `global` (todo sistema) ou `empresa_{id}`. |
| **Snapshot** | Backup pontual do estado do BrandKit para rollback. |
| **Resolver** | Componente que combina DEFAULTS + preset + overrides. |
| **ThemeContext** | DTO que transporta config resolvida para as views. |

### Cadeia de Resolução

```
1. DEFAULTS (código)      ← Valores hardcoded
2. theme.json (arquivo)   ← Preset selecionado
3. overrides (banco)      ← Sobrescritas do usuário
4. → Config Final         ← Resultado resolvido
```

### Targets de CSS

| Target | Onde aplica |
|--------|-------------|
| `admin` | Apenas área administrativa |
| `login` | Apenas tela de login |
| `both` | Ambos |

### Estados

| Estado | Significado |
|--------|-------------|
| `is_active = true` | Override/CSS está ativo |
| `is_active = false` | Override/CSS desativado |
| `is_enabled = true` | CSS habilitado para renderização |
| `value = null` | Override removido (usa valor do preset) |

---

## 6. Tabelas do Banco

### brand_kit_overrides

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | bigint PK | |
| `override_key` | varchar(100) | Ex: `color_primary`, `logo_main` |
| `value` | text nullable | Valor sobrescrito |
| `scope_key` | varchar(50) | `global` ou `empresa_{id}` |
| `theme_slug` | varchar(50) | `default`, `dark`, etc. |
| `is_active` | boolean | Se está ativo |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

**Index:** `uk_overrides_key_scope_theme` (unique)

### brand_kit_custom_css

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | bigint PK | |
| `name` | varchar(100) | Nome descritivo |
| `css_content` | text | CSS raw |
| `target` | enum | `admin`, `login`, `both` |
| `is_enabled` | boolean | Se renderiza |
| `order` | int | Ordem de aplicação |
| `scope_key` | varchar(50) | |
| `theme_slug` | varchar(50) | |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

### brand_kit_snapshots

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | bigint PK | |
| `name` | varchar(100) | Nome/descrição do snapshot |
| `reason` | varchar(255) nullable | Motivo do backup |
| `overrides_data` | json | Dump dos overrides |
| `custom_css_data` | json | Dump dos CSS |
| `scope_key` | varchar(50) | |
| `theme_slug` | varchar(50) | |
| `created_by` | bigint FK nullable | User que criou |
| `created_at` | timestamp | |

---

## 7. Eventos e Jobs

### Eventos (planejados)

| Evento | Trigger | Payload |
|--------|---------|---------|
| `BrandKitUpdated` | Override/CSS alterado | `scope_key`, `theme_slug`, `changes` |
| `SnapshotCreated` | Snapshot manual/automático | `snapshot_id`, `reason` |
| `SnapshotRestored` | Restauração executada | `snapshot_id` |
| `CacheInvalidated` | Cache limpo | `scope_key`, `theme_slug` |

### Jobs (planejados)

| Job | Responsabilidade |
|-----|------------------|
| `InvalidateBrandKitCacheJob` | Limpar cache em background |
| `CreateAutoSnapshotJob` | Criar snapshot automático |
| `NotifyBrandKitChangeJob` | Notificar admins de mudanças |

---

## 8. Configurações

### .env Variables

| Variável | Default | Descrição |
|----------|---------|-----------|
| `BRANDKIT_CACHE_TTL` | `3600` | TTL do cache em segundos |
| `BRANDKIT_CSS_MAX_SIZE` | `51200` | Tamanho máximo do CSS (50KB) |
| `BRANDKIT_SNAPSHOT_AUTO` | `true` | Criar snapshot automático antes de operações destrutivas |

### config/brandkit.php (futuro)

```php
return [
    'cache' => [
        'enabled' => env('BRANDKIT_CACHE_ENABLED', true),
        'ttl' => env('BRANDKIT_CACHE_TTL', 3600),
        'prefix' => 'brand_kit',
        'version' => 1,
    ],
    
    'css' => [
        'max_size' => env('BRANDKIT_CSS_MAX_SIZE', 51200),
        'blocked_patterns' => [
            '@import',
            'expression(',
            'javascript:',
            'data:text/html',
            '-moz-binding',
            'behavior:',
            'vbscript:',
            '<script',
        ],
    ],
    
    'snapshot' => [
        'auto_create' => env('BRANDKIT_SNAPSHOT_AUTO', true),
        'max_per_scope' => 10,
    ],
    
    'themes' => [
        'default_slug' => 'default',
        'presets_path' => storage_path('app/public/themes'),
    ],
];
```

### KEY_MAP (Resolver)

Mapeamento de chaves de override para chaves de configuração:

```php
private const KEY_MAP = [
    // Cores
    'color_primary'          => 'colors.primary',
    'color_secondary'        => 'colors.secondary',
    'color_accent'           => 'colors.accent',
    'color_background'       => 'colors.background',
    'color_text'             => 'colors.text',
    
    // Logos
    'logo_main'              => 'logos.main',
    'logo_light'             => 'logos.light',
    'logo_icon'              => 'logos.icon',
    'favicon'                => 'logos.favicon',
    
    // Login
    'login_bg_image'         => 'login.background.image',
    'login_bg_opacity'       => 'login.background.opacity',
    'login_card_style'       => 'login.card.style',
    
    // Fonts
    'font_family'            => 'fonts.family',
    'font_size_base'         => 'fonts.size.base',
];
```

---

## Quick Reference

### Comandos Frequentes

```bash
# Limpar cache do BrandKit
php artisan tinker --execute="app(\App\Support\BrandKitResolver::class)->invalidateAllGlobal();"

# Ver config resolvida
php artisan tinker --execute="dump(app(\App\Support\BrandKitResolver::class)->resolve('global','default'));"

# Ver tema selecionado
php artisan tinker --execute="dump(app(\App\Support\ThemeSelectionResolver::class)->getSelectedThemeSlug());"

# Listar overrides ativos
php artisan tinker --execute="dump(\App\Models\BrandKitOverride::where('is_active',true)->get());"

# Criar snapshot manual
php artisan tinker --execute="app(\App\Support\BrandKitRepository::class)->createSnapshot('global','default','Backup manual');"
```

### URLs da API

| Método | URL | Ação |
|--------|-----|------|
| `GET` | `/admin/brand-kit/config` | Config resolvida |
| `POST` | `/admin/brand-kit/override` | Criar/atualizar override |
| `DELETE` | `/admin/brand-kit/override/{key}` | Remover override |
| `POST` | `/admin/brand-kit/custom-css` | Adicionar CSS |
| `PUT` | `/admin/brand-kit/custom-css/{id}` | Atualizar CSS |
| `DELETE` | `/admin/brand-kit/custom-css/{id}` | Remover CSS |
| `POST` | `/admin/brand-kit/custom-css/{id}/toggle` | Toggle CSS |
| `GET` | `/admin/brand-kit/snapshots` | Listar snapshots |
| `POST` | `/admin/brand-kit/snapshot` | Criar snapshot |
| `POST` | `/admin/brand-kit/snapshot/restore` | Restaurar snapshot |

---

*Documento criado em: 2025-12-27*  
*Última atualização: 2025-12-27*
