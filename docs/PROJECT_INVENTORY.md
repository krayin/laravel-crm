# Inventário Completo do Projeto

Documento gerado em: 2025-12-27  
Branch: `feat/brandkit-split-controllers`

---

## Índice

1. [Identificação do Root Real](#1-identificação-do-root-real)
2. [Árvore de Pastas](#2-árvore-de-pastas)
3. [Arquivos Editados (Git Status)](#3-arquivos-editados-git-status)
4. [Versões](#4-versões)
5. [Storage - Temas e Uploads](#5-storage---temas-e-uploads)
6. [Inventário por Grep](#6-inventário-por-grep)
7. [Configuração de Ambiente (.env)](#7-configuração-de-ambiente-env)
8. [Configurações de Cache/Session/Queue](#8-configurações-de-cachesessionqueue)
9. [Documentação Existente](#9-documentação-existente)
10. [Checklist de Validação](#10-checklist-de-validação)
11. [Riscos e Rollback](#11-riscos-e-rollback)
12. [Triagem Inicial - Respostas](#12-triagem-inicial---respostas)

---

## 1. Identificação do Root Real

### Localização

```
Root absoluto: C:\Users\Usuario\Desktop\Krayin-\laravel-crm
Path Unix:     /c/Users/Usuario/Desktop/Krayin-/laravel-crm
```

### Verificação de Arquivos Críticos

| Arquivo | Existe | Tamanho | Data Modificação |
|---------|--------|---------|------------------|
| `artisan` | Sim | 1,686 bytes | Dec 20 18:48 |
| `.env` | Sim | 1,214 bytes | Dec 23 21:18 |
| `composer.json` | Sim | 3,840 bytes | Dec 20 20:23 |
| `composer.lock` | Sim | 427,746 bytes | Dec 20 18:48 |

### Estrutura de Diretórios Raiz

```
laravel-crm/
├── .claude/           # Config Claude Code
├── .git/              # Repositório Git
├── .github/           # GitHub Actions/Templates
├── .vscode/           # VS Code settings
├── app/               # Código da aplicação
├── bootstrap/         # Bootstrap Laravel
├── config/            # Configurações
├── database/          # Migrations, Seeders, Factories
├── docker/            # Docker configs
├── docs/              # Documentação do projeto
├── lang/              # Traduções
├── packages/          # Packages Webkul (Krayin)
├── public/            # Assets públicos
├── resources/         # Views, CSS, JS
├── routes/            # Rotas
├── storage/           # Storage Laravel
├── tests/             # Testes
├── tools/             # Scripts auxiliares
└── vendor/            # Dependências Composer
```

---

## 2. Árvore de Pastas

### Estrutura Completa (até 3 níveis)

```
.
├── .claude
├── .github
│   ├── ISSUE_TEMPLATE
│   └── workflows
├── .phpunit.cache
├── .vscode
├── app
│   ├── Console
│   │   └── Commands
│   ├── Exceptions
│   ├── Http
│   │   ├── Controllers        # Controllers customizados
│   │   │   ├── Admin/BrandKit # Split controllers (novos)
│   │   │   └── BrandKitController.php (completo)
│   │   └── Middleware
│   ├── Models                 # Models BrandKit (novos)
│   ├── Providers
│   ├── Repositories           # Repository pattern (novo)
│   └── Support                # Classes de suporte (novos)
├── bootstrap
│   └── cache
├── config
├── database
│   ├── factories
│   ├── migrations             # Migrations BrandKit (novos)
│   └── seeders
├── docker
│   ├── mysql
│   ├── nginx
│   ├── php
│   └── scripts
├── docs                       # Documentação criada
├── lang
│   └── en
├── packages
│   └── Webkul                 # Packages originais Krayin
│       ├── Activity
│       ├── Admin
│       ├── Attribute
│       ├── Automation
│       ├── Contact
│       ├── Core
│       ├── DataGrid
│       ├── DataTransfer
│       ├── Email
│       ├── EmailTemplate
│       ├── Installer
│       ├── Lead
│       ├── Marketing
│       ├── Product
│       ├── Quote
│       ├── Tag
│       ├── ThemeManager       # Package ThemeManager original
│       ├── User
│       ├── Warehouse
│       └── WebForm
├── public
│   ├── admin
│   │   └── build
│   ├── fonts
│   ├── installer
│   │   └── build
│   └── webform
│       └── build
├── resources
│   ├── css
│   ├── js
│   └── views
│       └── vendor             # Views publicadas
├── routes
├── storage
│   ├── app
│   │   └── public
│   │       ├── data-transfer
│   │       ├── theme-manager  # Uploads de logos/backgrounds
│   │       └── themes         # Presets de temas
│   ├── debugbar
│   ├── framework
│   │   ├── cache
│   │   ├── sessions
│   │   ├── testing
│   │   └── views
│   └── logs
├── tests
│   ├── Feature
│   └── Unit
├── tools
│   ├── scripts
│   └── vscode
└── vendor
```

---

## 3. Arquivos Editados (Git Status)

### Arquivos Modificados (M)

```
app/Http/Middleware/ShareThemeContext.php
app/Providers/AppServiceProvider.php
app/Providers/RouteServiceProvider.php
app/Support/ThemeContext.php
app/Support/ThemeContextFactory.php
routes/web.php
```

### Arquivos Deletados (D)

```
docs/00-overview/CLAUDE.md
docs/00-overview/CONTEXTO_PARA_ZED.md
docs/00-overview/MANUAL_GIT_DEPLOY.md
docs/00-overview/PROMPT_PARA_CLAUDE_WEB.md
docs/01-architecture/CONFIGURACAO-VSCODE.md
docs/01-architecture/INSTALLATION_REPORT.md
docs/01-architecture/PROFILE-CONFIGURADO.md
docs/02-implementation/ACOES_CUSTOM_FASE_2.md
docs/02-implementation/COMMIT_PRONTO.md
docs/02-implementation/CORRECAO_FINAL_LOGOS.md
docs/02-implementation/CORRECAO_ICONE_THEME.md
docs/02-implementation/CORRECAO_LOGIN_BG_APLICADA.md
docs/02-implementation/CORRECAO_SELETORES_CSS.md
docs/02-implementation/CORRECAO_TIMEOUT.md
docs/02-implementation/CORRECOES_APLICADAS.md
docs/02-implementation/CORRECOES_CLAUDE_WEB.md
docs/02-implementation/CORRECOES_ROUND_2.md
docs/02-implementation/CORRECOES_ROUND_3.md
docs/02-implementation/HISTORICO_COMPLETO_CORRECOES.md
docs/03-testing/CHECKLIST_TESTES_RESTANTES.md
docs/03-testing/RESUMO_FINAL_TESTES.txt
docs/03-testing/TESTE_CORRECOES_ROUND_2.md
docs/03-testing/TESTE_FINAL_COMPLETO.md
docs/03-testing/TESTE_RAPIDO_TIMEOUT.md
docs/04-features/README.md
docs/04-features/login-theme/DIAGNOSTICO_BG.md
docs/04-features/login-theme/DIAGNOSTICO_POWERED_BY.md
docs/04-features/login-theme/ENTREGA_CUSTOM_CODE.md
docs/04-features/login-theme/ENTREGA_DROPDOWN_IS_ACTIVE.md
docs/04-features/login-theme/ENTREGA_ICONE.md
docs/04-features/login-theme/ENTREGA_LOGIN_CARD.md
docs/04-features/login-theme/ENTREGA_POWERED_BY.md
docs/04-features/login-theme/GUIA_ESPECIALISTA.md
docs/04-features/login-theme/INSTRUCOES_TESTE_BG.md
docs/04-features/login-theme/MAPEAMENTO_BG.md
docs/04-features/login-theme/README.md
docs/04-features/login-theme/SAGA_BACKGROUND.md
docs/04-features/login-theme/SOLUCAO_BG.md
docs/04-features/theme-manager/ANALISE_MENU.md
docs/04-features/theme-manager/COMANDOS_TESTE.md
docs/04-features/theme-manager/LOGS.md
docs/04-features/theme-manager/README.md
docs/04-features/theme-manager/RESUMO.md
docs/04-features/theme-manager/STATUS_ATUAL.md
docs/04-features/theme-manager/TEST_REPORT.md
docs/05-operations/DOCKER_SWARM_DEPLOY.md
docs/05-operations/README.md
docs/05-operations/runbooks/RUNBOOK_THEME_SMOKE.md
docs/05-operations/troubleshooting/DEBUG_UPLOAD_COMPLETO.md
docs/05-operations/troubleshooting/DIAGNOSTICO_FINAL.md
docs/05-operations/troubleshooting/PROBLEMA_ENCONTRADO.md
docs/05-operations/troubleshooting/RELATORIO_TECNICO_CUSTOM_CODE.md
docs/CHANGELOG.md
docs/CHANGELOG_THEME_REFACTORING.md
docs/README.md
docs/_archive/codigo_stelium_simplificado.txt
docs/_archive/respostas_themes.txt
docs/_logs/LOGS_LOGIN_BG_10MIN.md
docs/_logs/LOGS_THEMEMANAGER_10H.md
docs/_logs/LOGS_THEMEMANAGER_10H.txt
docs/_logs/LOGS_ULTIMOS_3MIN.md
docs/_logs/laravel_recent.log
docs/_logs/migration_output.txt
docs/onboarding/ANATOMIA_GERAL_KRAYIN_CRM.md
docs/onboarding/CHECKLIST_VALIDACAO_DEV.md
docs/onboarding/FERRAMENTAS_DEV_KRAYIN.md
docs/onboarding/MAPEAMENTO_DEMANDA_PROJETO.md
docs/onboarding/ONBOARDING_DEV_KRAYIN.md
docs/onboarding/PROCESSO_CUSTOMIZACAO_KRAYIN.md
docs/onboarding/PROMPT_INICIO_CONVERSAS.md
```

### Arquivos Novos (Untracked - ??)

```
app/Http/Controllers/Admin/BrandKit/           # Split controllers
app/Http/Controllers/BrandKitController.php    # Controller completo
app/Models/BrandKitCustomCss.php
app/Models/BrandKitOverride.php
app/Models/BrandKitSnapshot.php
app/Repositories/                              # Repository pattern
app/Support/BrandKitRepository.php
app/Support/BrandKitResolver.php
app/Support/CssValidator.php
app/Support/ThemeSelectionResolver.php
database/migrations/2025_12_25_205615_create_brand_kit_overrides_table.php
database/migrations/2025_12_25_205628_create_brand_kit_custom_css_table.php
database/migrations/2025_12_25_205637_create_brand_kit_snapshots_table.php
routes/brand-kit.php
test_classes.php                               # Scripts de teste (temp)
test_css_validator.php
test_repository.php
```

---

## 4. Versões

### Ambiente

| Componente | Versão | Notas |
|------------|--------|-------|
| PHP | ^8.2 | Requerido pelo composer.json |
| Laravel | ^10.0 | Framework base |
| Krayin CRM | - | Fork/customização |
| Node.js | - | Para Vite build |

### Dependências Principais (composer.json)

```json
{
    "require": {
        "php": "^8.2",
        "laravel/framework": "^10.0",
        "laravel/sanctum": "^3.2",
        "laravel/tinker": "^2.5",
        "laravel/ui": "^4.5",
        "konekt/concord": "^1.10",
        "prettus/l5-repository": "^2.7.9",
        "maatwebsite/excel": "^3.1",
        "barryvdh/laravel-dompdf": "^2.0.0"
    },
    "require-dev": {
        "barryvdh/laravel-debugbar": "^3.6",
        "krayin/krayin-package-generator": "dev-master",
        "pestphp/pest": "^2.6",
        "phpunit/phpunit": "^10.0"
    }
}
```

### Packages Webkul Incluídos

```
packages/Webkul/
├── Activity
├── Admin
├── Attribute
├── Automation
├── Contact
├── Core
├── DataGrid
├── DataTransfer
├── Email
├── EmailTemplate
├── Installer
├── Lead
├── Marketing
├── Product
├── Quote
├── Tag
├── ThemeManager        # Package original de temas
├── User
├── Warehouse
└── WebForm
```

---

## 5. Storage - Temas e Uploads

### Estrutura storage/app/public/

```
storage/app/public/
├── .gitignore
├── data-transfer/
├── theme-manager/              # Uploads de usuários
│   ├── .gitkeep
│   ├── 1766361682_logo_icon_OTfoFob2.png
│   ├── 1766362017_favicon_HnB4saB7.ico
│   ├── 1766362046_logo_light_wCFgYmTx.png
│   ├── 1766382327_login_bg_image_vZSShl9d.png
│   ├── 1766382984_login_bg_image_VDMm6925.png
│   ├── 1766386050_logo_main_bju5CsNY.png
│   ├── 1766390564_favicon_hhBNcDNg.ico
│   ├── 1766390564_logo_icon_7K5tQde4.png
│   ├── 1766390564_logo_light_m0ugX2xq.png
│   ├── 1766390564_logo_main_nxMzBoe3.png
│   ├── 1766410435_login_bg_image_xn7Q4yoe.png
│   ├── 1766445002_login_card_bg_image_Xzl9WeNH.jpg
│   ├── 1766617570_favicon_fy81NMB0.ico
│   ├── 1766617570_logo_icon_q7EzOe4H.png
│   ├── 1766617570_logo_light_FBNEfJ5W.png
│   ├── 1766617570_logo_main_YTOIcGns.png
│   ├── bg_login_page.jpg
│   └── bg-card.png
└── themes/                     # Presets de temas
    ├── .gitkeep
    ├── default/
    ├── meu-tema/
    ├── starter/
    ├── stelium-sanctuary/
    ├── theme-complete/
    ├── theme-minimal/
    └── theme-partial/
```

### Temas Disponíveis

| Tema | Pasta | Descrição |
|------|-------|-----------|
| default | `themes/default/` | Tema padrão |
| meu-tema | `themes/meu-tema/` | Tema customizado |
| starter | `themes/starter/` | Tema inicial |
| stelium-sanctuary | `themes/stelium-sanctuary/` | Tema Stelium |
| theme-complete | `themes/theme-complete/` | Exemplo completo |
| theme-minimal | `themes/theme-minimal/` | Exemplo minimal |
| theme-partial | `themes/theme-partial/` | Exemplo parcial |

### Uploads em theme-manager/

19 arquivos de upload:
- Logos: 8 arquivos (main, light, icon)
- Favicons: 3 arquivos
- Backgrounds: 5 arquivos (login, card)
- Outros: 3 arquivos

---

## 6. Inventário por Grep

### Referências a BrandKit no Código

```
app/Http/Controllers/Admin/BrandKit/BrandKitController.php
app/Http/Controllers/Admin/BrandKit/Concerns/BrandKitControllerHelpers.php
app/Http/Controllers/Admin/BrandKit/CustomCssController.php
app/Http/Controllers/Admin/BrandKit/OverridesController.php
app/Http/Controllers/Admin/BrandKit/SnapshotsController.php
app/Http/Controllers/BrandKitController.php
app/Http/Kernel.php                    # ShareThemeContext middleware
app/Http/Middleware/HandleThemePreview.php
app/Http/Middleware/ShareThemeContext.php
app/Models/BrandKitCustomCss.php
app/Models/BrandKitOverride.php
app/Models/BrandKitSnapshot.php
app/Providers/AppServiceProvider.php   # Bindings
app/Providers/ThemeBootProvider.php    # theme.json loading
app/Repositories/BrandKitRepository.php
app/Support/BrandKitRepository.php     # Duplicata!
app/Support/BrandKitResolver.php
app/Support/ThemeContext.php
app/Support/ThemeContextFactory.php
```

### Bindings no Container (AppServiceProvider)

```php
$this->app->singleton(\App\Support\BrandKitResolver::class);
$this->app->singleton(\App\Support\ThemeContextFactory::class);
$this->app->singleton(\App\Repositories\BrandKitRepository::class);
```

### Referências a theme.json

```
app/Http/Middleware/HandleThemePreview.php:202
app/Providers/ThemeBootProvider.php:73
app/Providers/ThemeBootProvider.php:87
app/Providers/ThemeBootProvider.php:106
app/Providers/ThemeBootProvider.php:108
app/Providers/ThemeBootProvider.php:113
app/Providers/ThemeBootProvider.php:131
app/Providers/ThemeBootProvider.php:133
app/Providers/ThemeBootProvider.php:151
```

### prependNamespace (View Override)

```
app/Providers/ThemeBootProvider.php:55
    View::prependNamespace("theme-manager", $vendorThemeManagerPath);
```

---

## 7. Configuração de Ambiente (.env)

### Ambiente Atual (sem secrets)

```bash
APP_NAME='Krayin CRM'
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8080
APP_TIMEZONE=Asia/Kolkata
APP_LOCALE=en
APP_CURRENCY=USD

DEBUGBAR_ENABLED=false

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=sqlite
DB_PREFIX=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=laravel@krayincrm.com

MAIL_RECEIVER_DRIVER=sendgrid

IMAP_HOST=imap.example.com
IMAP_PORT=993
IMAP_ENCRYPTION=ssl
IMAP_VALIDATE_CERT=true
```

### Drivers Ativos

| Driver | Valor | Notas |
|--------|-------|-------|
| `DB_CONNECTION` | `sqlite` | Banco local para dev |
| `CACHE_DRIVER` | `file` | Cache em disco |
| `QUEUE_CONNECTION` | `sync` | Síncrono (sem queue) |
| `SESSION_DRIVER` | `file` | Sessão em disco |
| `BROADCAST_DRIVER` | `log` | Apenas log |

---

## 8. Configurações de Cache/Session/Queue

### config/cache.php

```php
'default' => env('CACHE_DRIVER', 'file'),

'stores' => [
    'apc'       => ['driver' => 'apc'],
    'array'     => ['driver' => 'array'],
    'database'  => [...],
    'file'      => ['driver' => 'file', 'path' => storage_path('framework/cache/data')],
    'memcached' => [...],
    'redis'     => [...],
    'dynamodb'  => [...],
    'octane'    => [...],
],
```

### config/session.php

```php
'driver' => env('SESSION_DRIVER', 'file'),
'lifetime' => env('SESSION_LIFETIME', 120),
'expire_on_close' => false,
```

### config/queue.php

```php
'default' => env('QUEUE_CONNECTION', 'sync'),

'connections' => [
    'sync'     => ['driver' => 'sync'],
    'database' => ['driver' => 'database', 'table' => 'jobs', ...],
    'beanstalkd' => [...],
    'sqs'      => [...],
    'redis'    => [...],
],
```

---

## 9. Documentação Existente

### docs/ (7 arquivos)

| Arquivo | Tamanho | Descrição |
|---------|---------|-----------|
| `BRANDKIT_EVOLUTION.md` | 39,513 bytes | Evolução técnica step-by-step |
| `DEPLOY_ENVIRONMENT.md` | 21,152 bytes | Padrões de deploy e ambiente |
| `GLOSSARY.md` | 16,200 bytes | Glossário do projeto |
| `INCIDENT_LOG.md` | 15,543 bytes | Log de incidentes |
| `KRAYIN_LESSONS_LEARNED.md` | 19,114 bytes | Lições aprendidas |
| `REAL_WORLD_EXAMPLES.md` | 28,747 bytes | Exemplos reais |
| `RUNBOOKS.md` | 32,331 bytes | 13 runbooks de troubleshooting |

### Outros Docs no Root

| Arquivo | Descrição |
|---------|-----------|
| `CLAUDE.md` | Contexto para Claude Code |
| `README.md` | README original Krayin |
| `CHANGELOG.md` | Changelog original |
| `VERIFICACAO_SOLUCAO.md` | Verificação de solução |

---

## 10. Checklist de Validação

### Root Real do Projeto

| Check | Status | Notas |
|-------|--------|-------|
| Onde mora `artisan` | `laravel-crm/artisan` | Root correto |
| Onde mora `.env` | `laravel-crm/.env` | Root correto |
| Diretório de trabalho | `C:\Users\Usuario\Desktop\Krayin-\laravel-crm` | Correto |

### Customizações em Locais Upgrade-Safe

| Local | Seguro para Upgrade | Arquivos |
|-------|---------------------|----------|
| `app/` | Sim | Controllers, Models, Support, Repositories |
| `resources/views/vendor/` | Sim | Views publicadas |
| `routes/` | Sim | brand-kit.php, web.php |
| `database/migrations/` | Sim | Migrations BrandKit |
| `packages/Webkul/*` | **NÃO** | Verificar se foi editado |

### Conflito de Precedência

```
DEFAULTS (código) → theme.json (arquivo) → overrides (DB)
```

| Check | Status | Notas |
|-------|--------|-------|
| DEFAULTS definidos | Sim | Em `BrandKitResolver.php` |
| theme.json loading | Sim | Em `ThemeBootProvider.php` |
| Overrides DB | Sim | Tabela `brand_kit_overrides` |
| Cadeia implementada | Sim | Em `BrandKitResolver::resolve()` |

### Problemas Potenciais

| Problema | Status | Notas |
|----------|--------|-------|
| Cache preso | Possível | `CACHE_DRIVER=file` |
| View não atualiza | Possível | Verificar `php artisan view:clear` |
| Assets/versionamento | Verificar | Vite build necessário |
| Binding não carrega | **PROBLEMA** | Duplicata de Repository (ver abaixo) |

### Problema Identificado: Duplicata de Repository

```
app/Repositories/BrandKitRepository.php    # Registrado no container
app/Support/BrandKitRepository.php         # Usado pelo BrandKitController
```

**Container bind:**
```php
$this->app->singleton(\App\Repositories\BrandKitRepository::class);
```

**Controller usa:**
```php
use App\Support\BrandKitRepository;  // DIFERENTE!
```

**Ação necessária:** Consolidar em um único local.

---

## 11. Riscos e Rollback

### Risco: Edições em packages/Webkul/*

| Package | Editado | Risco |
|---------|---------|-------|
| ThemeManager | Não diretamente | Views publicadas em `vendor/` |
| Admin | Não diretamente | Views publicadas em `vendor/` |
| Outros | Não verificado | Verificar com diff |

**Rollback:**
```bash
# Reinstalar dependências e mover alterações
composer install
# Mover alterações para resources/views/vendor/
```

### Risco: Cache Mascarando Tudo

**Sintomas:**
- Alterações não refletem
- Comportamento inconsistente
- "Funciona no tinker, não funciona na request"

**Rollback/Limpeza (dev):**
```bash
php artisan optimize:clear
composer dump-autoload
```

**Em staging/prod:**
```bash
# Com cuidado por OPcache/Redis
php artisan config:cache
php artisan route:cache
php artisan view:cache
# Se Redis:
redis-cli FLUSHDB
```

### Risco: Duplicata de Arquivos

| Arquivo | Locais | Ação |
|---------|--------|------|
| BrandKitRepository | `app/Repositories/`, `app/Support/` | Consolidar |
| BrandKitController | `Admin/BrandKit/` (split), root (completo) | Escolher um |

---

## 12. Triagem Inicial - Respostas

### 1. Versões

| Componente | Versão |
|------------|--------|
| Krayin | Fork local (packages/Webkul/*) |
| Laravel | ^10.0 |
| PHP | ^8.2 |

### 2. Ambiente

| Aspecto | Valor |
|---------|-------|
| Tipo | Local (Windows) |
| Docker | Não (configs disponíveis) |
| APP_ENV | `local` |
| APP_DEBUG | `true` |

### 3. Drivers

| Driver | Valor |
|--------|-------|
| cache | `file` |
| queue | `sync` |
| session | `file` |
| database | `sqlite` |

### 4. Objetivo Final

- **Foco:** Tema/Login customization (BrandKit)
- **Funcionalidade:** Sistema de overrides, CSS customizado, snapshots
- **Não:** Leads, contatos, funcionalidades core do CRM

### 5. Edições em packages/Webkul/*

**Status:** Não diretamente editado (verificar com diff completo)

**Views publicadas em:** `resources/views/vendor/`

---

## Comandos de Verificação Rápida

```bash
# Verificar root
pwd
ls -la artisan .env

# Ver status git
git status --porcelain

# Ver arquivos modificados
git diff --name-only

# Limpar caches
php artisan optimize:clear

# Verificar bindings
php artisan tinker --execute="dump(app(\App\Support\BrandKitResolver::class));"

# Verificar config resolvida
php artisan tinker --execute="dump(app(\App\Support\BrandKitResolver::class)->resolve('global','default'));"

# Verificar tema selecionado
php artisan tinker --execute="dump(app(\App\Support\ThemeSelectionResolver::class)->getSelectedThemeSlug());"
```

---

*Documento gerado automaticamente em: 2025-12-27*
