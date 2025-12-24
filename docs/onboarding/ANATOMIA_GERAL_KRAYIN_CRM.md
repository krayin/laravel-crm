# 🧬 ANATOMIA GERAL DO KRAYIN CRM
## Documento Mestre Definitivo para Customização Profissional

**Versão:** 1.0.0 FINAL  
**Data:** Dezembro 2025  
**Autor:** Arquitetura de Soluções  
**Status:** ✅ CONSOLIDADO E APROVADO  
**Stack:** Laravel 11 | PHP 8.1+ | Docker Swarm | Traefik | Portainer

---

```
╔═══════════════════════════════════════════════════════════════════════════════╗
║                                                                               ║
║   █████╗ ███╗   ██╗ █████╗ ████████╗ ██████╗ ███╗   ███╗██╗ █████╗           ║
║  ██╔══██╗████╗  ██║██╔══██╗╚══██╔══╝██╔═══██╗████╗ ████║██║██╔══██╗          ║
║  ███████║██╔██╗ ██║███████║   ██║   ██║   ██║██╔████╔██║██║███████║          ║
║  ██╔══██║██║╚██╗██║██╔══██║   ██║   ██║   ██║██║╚██╔╝██║██║██╔══██║          ║
║  ██║  ██║██║ ╚████║██║  ██║   ██║   ╚██████╔╝██║ ╚═╝ ██║██║██║  ██║          ║
║  ╚═╝  ╚═╝╚═╝  ╚═══╝╚═╝  ╚═╝   ╚═╝    ╚═════╝ ╚═╝     ╚═╝╚═╝╚═╝  ╚═╝          ║
║                                                                               ║
║                    KRAYIN CRM - DOCUMENTO MESTRE                              ║
║                                                                               ║
╚═══════════════════════════════════════════════════════════════════════════════╝
```

---

## 📋 ÍNDICE GERAL

### PARTE I - FUNDAMENTOS
1. [O DNA do Krayin](#parte-i---o-dna-do-krayin)
2. [Arquitetura de Camadas](#arquitetura-de-camadas)
3. [Mapa de Componentes](#mapa-de-componentes)

### PARTE II - ANATOMIA DE PACKAGES
4. [Estrutura Canônica de um Package](#parte-ii---anatomia-de-packages)
5. [Service Provider - O Coração](#service-provider---o-coração-do-package)
6. [Ciclo de Vida e Carregamento](#ciclo-de-vida-e-carregamento)

### PARTE III - SISTEMA DE OVERRIDES
7. [Override de Controllers](#override-de-controllers)
8. [Override de Models (Concord)](#override-de-models-eloquent)
9. [Override de Views (Blade)](#override-de-views-blade)
10. [View Render Events](#view-render-events)

### PARTE IV - SISTEMA DE EVENTOS
11. [Anatomia de Eventos](#parte-iv---sistema-de-eventos)
12. [Event Service Provider](#event-service-provider)
13. [Listeners e Mailables](#listeners-e-mailables)
14. [Catálogo de Eventos Krayin](#catálogo-de-eventos-krayin)

### PARTE V - CUSTOMIZAÇÃO VISUAL
15. [CSS Variables e Temas](#parte-v---customização-visual)
16. [Assets e Imagens](#assets-e-imagens)
17. [Traduções Multi-idioma](#traduções-multi-idioma)

### PARTE VI - INFRAESTRUTURA DOCKER
18. [Arquitetura Docker Swarm](#parte-vi---infraestrutura-docker)
19. [Dockerfile Otimizado](#dockerfile-otimizado-para-produção)
20. [Docker Compose (DEV e PROD)](#docker-compose-dev-e-prod)
21. [Traefik e SSL](#traefik-e-ssl)

### PARTE VII - OPERAÇÕES
22. [Fluxo de Deploy](#parte-vii---operações)
23. [Cache Management](#cache-management)
24. [Troubleshooting Map](#troubleshooting-map)
25. [Comandos Essenciais](#comandos-essenciais)

### PARTE VIII - PADRÕES E CONFORMIDADE
26. [Nomenclatura e Convenções](#parte-viii---padrões-e-conformidade)
27. [Anti-Patterns](#anti-patterns)
28. [Checklists de Conformidade](#checklists-de-conformidade)
29. [Matriz de Decisão](#matriz-de-decisão)

### ANEXOS
- [A. Blueprints de Packages](#anexo-a---blueprints-de-packages)
- [B. Quick Reference (1 Página)](#anexo-b---quick-reference)
- [C. Glossário](#anexo-c---glossário)

---

# PARTE I - O DNA DO KRAYIN

## Definição Técnica

```
╔═══════════════════════════════════════════════════════════════╗
║  KRAYIN CRM = Laravel Monolith + Package-Based Architecture   ║
╚═══════════════════════════════════════════════════════════════╝
```

O **Krayin CRM** é uma plataforma open-source de gestão de relacionamento com clientes, construída sobre o framework **Laravel**. Sua arquitetura é **modular baseada em packages**, permitindo extensibilidade sem modificação do core.

### Princípios Fundamentais

| Princípio | Descrição | Implicação Prática |
|-----------|-----------|-------------------|
| **Isolamento** | Cada package é independente | Pode ser desativado sem quebrar o sistema |
| **Override** | Packages custom sobrescrevem padrão | Seu código tem prioridade sobre o core |
| **Composição** | Packages podem depender de outros | Respeitar ordem em `config/modules.php` |
| **Publicação** | Assets são publicados para `/public/vendor/` | Sempre executar `vendor:publish` |
| **Imutabilidade do Core** | Nunca editar packages originais | Criar overrides em package próprio |

### Composição Genética (Packages Core)

```
┌─────────────────────────────────────────────────────────────────────┐
│                         KRAYIN GENOME                               │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ╔═══════════════════════════════════════════════════════════════╗  │
│  ║  CORE (DNA Base)                                              ║  │
│  ║  ├── Core          → Utilities, Helpers, Base Classes        ║  │
│  ║  ├── Admin         → Painel administrativo, Auth, ACL        ║  │
│  ║  └── UI            → Componentes Vue.js, Blade Components    ║  │
│  ╚═══════════════════════════════════════════════════════════════╝  │
│                              ↓                                      │
│  ╔═══════════════════════════════════════════════════════════════╗  │
│  ║  MODULES (Funcionalidades de Negócio)                         ║  │
│  ║  ├── Contact       → Gestão de contatos (pessoas/orgs)       ║  │
│  ║  ├── Lead          → Pipeline de vendas                      ║  │
│  ║  ├── Product       → Catálogo de produtos                    ║  │
│  ║  ├── Quote         → Orçamentos e propostas                  ║  │
│  ║  ├── Activity      → Tarefas e atividades                    ║  │
│  ║  ├── User          → Gestão de usuários                      ║  │
│  ║  ├── Attribute     → Atributos customizados                  ║  │
│  ║  └── Email         → Integração de email                     ║  │
│  ╚═══════════════════════════════════════════════════════════════╝  │
│                              ↓                                      │
│  ╔═══════════════════════════════════════════════════════════════╗  │
│  ║  CUSTOM (Suas Extensões) ← VOCÊ TRABALHA AQUI!               ║  │
│  ║  ├── CustomTheme   → Tema visual customizado                 ║  │
│  ║  ├── LoginCustom   → Tela de login personalizada             ║  │
│  ║  ├── CustomWorkflow→ Automações e workflows                  ║  │
│  ║  └── [SeuPackage]  → Suas funcionalidades                    ║  │
│  ╚═══════════════════════════════════════════════════════════════╝  │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

## Arquitetura de Camadas

```
┌─────────────────────────────────────────────────────────────────────┐
│                     CAMADA DE APRESENTAÇÃO                          │
│  ┌───────────────────────────────────────────────────────────────┐  │
│  │  UI Layer                                                     │  │
│  │  ├── Blade Templates (.blade.php)     → Templates HTML        │  │
│  │  ├── Vue.js SFCs (.vue)               → Componentes reativos  │  │
│  │  ├── CSS/SCSS (.css, .scss)           → Estilos               │  │
│  │  └── JavaScript (.js)                 → Comportamento         │  │
│  └───────────────────────────────────────────────────────────────┘  │
│                              ↕                                      │
├─────────────────────────────────────────────────────────────────────┤
│                     CAMADA DE APLICAÇÃO                             │
│  ┌───────────────────────────────────────────────────────────────┐  │
│  │  HTTP Layer                                                   │  │
│  │  ├── Controllers                      → Lógica de rotas       │  │
│  │  ├── Form Requests                    → Validação             │  │
│  │  ├── Middleware                       → Auth, CORS, etc.      │  │
│  │  └── Routes (admin-routes.php)        → Definição de URLs     │  │
│  └───────────────────────────────────────────────────────────────┘  │
│                              ↕                                      │
├─────────────────────────────────────────────────────────────────────┤
│                     CAMADA DE DOMÍNIO                               │
│  ┌───────────────────────────────────────────────────────────────┐  │
│  │  Business Logic                                               │  │
│  │  ├── Models (Eloquent)                → Estrutura de dados    │  │
│  │  ├── Repositories                     → Data Access Layer     │  │
│  │  ├── Services                         → Business Rules        │  │
│  │  ├── Events + Listeners               → Comunicação async     │  │
│  │  └── Contracts (Interfaces)           → Abstrações            │  │
│  └───────────────────────────────────────────────────────────────┘  │
│                              ↕                                      │
├─────────────────────────────────────────────────────────────────────┤
│                     CAMADA DE INFRAESTRUTURA                        │
│  ┌───────────────────────────────────────────────────────────────┐  │
│  │  Config + Database                                            │  │
│  │  ├── Migrations                       → Schema SQL            │  │
│  │  ├── Seeders                          → Dados iniciais        │  │
│  │  ├── Config files                     → Configurações         │  │
│  │  └── Cache (Redis)                    → Performance           │  │
│  └───────────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────┘
```

### Mapa de Customização por Camada

| Camada | Arquivo/Pasta | Tipo de Customização | Complexidade | Método |
|--------|---------------|---------------------|--------------|--------|
| **Apresentação** | `Resources/views/` | Visual, Layout | 🟢 Baixa | View Override |
| **Apresentação** | `Resources/assets/css/` | Cores, Estilos | 🟢 Baixa | CSS Variables |
| **Apresentação** | `Resources/assets/images/` | Logo, Ícones | 🟢 Baixa | Asset Replace |
| **Aplicação** | `Http/Controllers/` | Lógica de rotas | 🟡 Média | Controller Override |
| **Aplicação** | `Routes/` | Novas rotas | 🟡 Média | Route Registration |
| **Domínio** | `Models/` | Estrutura de dados | 🔴 Alta | Model Override (Concord) |
| **Domínio** | `Listeners/` | Ações automáticas | 🟡 Média | Event Listener |
| **Infra** | `Database/Migrations/` | Schema | 🔴 Alta | Migration |

---

## Mapa de Componentes

```
┌─────────────────────────────────────────────────────────────────────┐
│                    MAPA DE COMPONENTES KRAYIN                       │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  COMPONENTE          │ LOCALIZAÇÃO                  │ CUSTOMIZAÇÃO  │
│  ════════════════════╪══════════════════════════════╪═══════════════│
│  Views (Blade)       │ packages/.../Resources/views │ Override      │
│  Assets (CSS/JS)     │ packages/.../Resources/assets│ Publish       │
│  Imagens/Logos       │ public/images/ ou assets/    │ Replace       │
│  Componentes Vue.js  │ packages/Webkul/UI/Resources │ Override      │
│  Traduções           │ packages/.../Resources/lang  │ Override      │
│  Config              │ config/ ou packages/.../Config│ Env vars     │
│  Controllers         │ packages/.../Http/Controllers│ Bind          │
│  Models              │ packages/.../Models          │ Concord       │
│  Events              │ packages/.../Events          │ Dispatch      │
│  Listeners           │ packages/.../Listeners       │ Register      │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

# PARTE II - ANATOMIA DE PACKAGES

## Estrutura Canônica de um Package

```
packages/Webkul/{NomePackage}/
│
├── src/                                        # ═══ CÓDIGO FONTE ═══
│   │
│   ├── Providers/                              # Service Providers
│   │   ├── {NomePackage}ServiceProvider.php        ← PRINCIPAL
│   │   └── EventServiceProvider.php                ← Eventos
│   │
│   ├── Http/                                   # Camada HTTP
│   │   ├── Controllers/
│   │   │   ├── Admin/                          # Controllers Admin
│   │   │   │   ├── DashboardController.php
│   │   │   │   └── LeadController.php          # Override
│   │   │   └── Api/                            # Controllers API
│   │   │       └── LeadApiController.php
│   │   ├── Middleware/
│   │   │   └── CustomMiddleware.php
│   │   └── Requests/                           # Form Requests
│   │       └── LeadRequest.php
│   │
│   ├── Models/                                 # Eloquent Models
│   │   ├── Lead.php                            # Override
│   │   └── CustomEntity.php                    # Novo
│   │
│   ├── Repositories/                           # Data Access
│   │   └── LeadRepository.php
│   │
│   ├── Contracts/                              # Interfaces
│   │   └── LeadContract.php
│   │
│   ├── Listeners/                              # Event Listeners
│   │   ├── Lead.php                            # → 'lead.update.after'
│   │   ├── Contact.php                         # → 'contact.create.after'
│   │   └── Quote.php                           # → 'quote.create.after'
│   │
│   ├── Mail/                                   # Mailable Classes
│   │   ├── LeadNotification.php
│   │   └── WelcomeEmail.php
│   │
│   ├── Events/                                 # Custom Events
│   │   └── LeadPipelineChanged.php
│   │
│   ├── Console/                                # Artisan Commands
│   │   └── Commands/
│   │       └── CustomCommand.php
│   │
│   ├── Routes/                                 # Rotas
│   │   ├── admin-routes.php
│   │   └── api-routes.php
│   │
│   └── Config/                                 # Configurações
│       ├── acl.php                             # Access Control
│       ├── menu.php                            # Menu Items
│       └── system.php                          # System Config
│
├── Resources/                                  # ═══ RECURSOS ═══
│   │
│   ├── views/                                  # Templates Blade
│   │   ├── admin/
│   │   │   ├── dashboard.blade.php
│   │   │   └── layouts/
│   │   │       └── app.blade.php
│   │   ├── auth/
│   │   │   └── login.blade.php                 # Override
│   │   ├── mail/
│   │   │   └── notification.blade.php
│   │   └── components/
│   │       └── custom-button.blade.php
│   │
│   ├── lang/                                   # Traduções
│   │   ├── en/
│   │   │   └── app.php
│   │   └── pt_BR/
│   │       └── app.php
│   │
│   └── assets/                                 # Assets Estáticos
│       ├── css/
│       │   ├── theme.css                       # Cores customizadas
│       │   └── admin.css
│       ├── js/
│       │   └── app.js
│       └── images/
│           ├── logo.png
│           ├── favicon.ico
│           └── icons/
│
├── Database/                                   # ═══ BANCO DE DADOS ═══
│   ├── Migrations/
│   │   └── 2025_01_01_000000_add_custom_fields.php
│   └── Seeders/
│       └── CustomSeeder.php
│
├── Tests/                                      # ═══ TESTES ═══
│   ├── Feature/
│   │   └── LeadTest.php
│   └── Unit/
│       └── ModelTest.php
│
├── composer.json                               # Manifesto
├── README.md                                   # Documentação
└── LICENSE
```

---

## Service Provider - O Coração do Package

O **Service Provider** é o componente central de todo package. Ele é responsável por:
- Registrar bindings de Dependency Injection
- Carregar rotas, views, traduções
- Publicar assets
- Registrar eventos e listeners

### Anatomia Completa do Service Provider

```php
<?php
// packages/Webkul/{NomePackage}/src/Providers/{NomePackage}ServiceProvider.php

namespace Webkul\NomePackage\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Routing\Router;

class NomePackageServiceProvider extends ServiceProvider
{
    /**
     * ╔═══════════════════════════════════════════════════════════════════╗
     * ║  REGISTER: Executado PRIMEIRO, ANTES de tudo                      ║
     * ╠═══════════════════════════════════════════════════════════════════╣
     * ║  ✅ USAR PARA:                                                    ║
     * ║     - Bindings de Dependency Injection                            ║
     * ║     - Registrar outros Service Providers                          ║
     * ║     - Merge de configurações                                      ║
     * ║                                                                   ║
     * ║  ❌ NÃO USAR PARA:                                                ║
     * ║     - Views, Assets, Events (usar boot())                         ║
     * ║     - Qualquer coisa que dependa de outros providers              ║
     * ╚═══════════════════════════════════════════════════════════════════╝
     */
    public function register(): void
    {
        // ═══════════════════════════════════════════════════════════════
        // 1. MERGE CONFIGURAÇÕES
        // ═══════════════════════════════════════════════════════════════
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/nomepackage.php',
            'nomepackage'
        );
        
        // ═══════════════════════════════════════════════════════════════
        // 2. BINDINGS DE DEPENDENCY INJECTION
        // ═══════════════════════════════════════════════════════════════
        $this->app->bind(
            \Webkul\NomePackage\Contracts\CustomContract::class,
            \Webkul\NomePackage\Repositories\CustomRepository::class
        );
        
        // ═══════════════════════════════════════════════════════════════
        // 3. SINGLETONS (instância única)
        // ═══════════════════════════════════════════════════════════════
        $this->app->singleton('nomepackage.service', function ($app) {
            return new \Webkul\NomePackage\Services\CustomService();
        });
        
        // ═══════════════════════════════════════════════════════════════
        // 4. CONTROLLER OVERRIDES (IMPORTANTE!)
        // ═══════════════════════════════════════════════════════════════
        $this->registerControllerOverrides();
    }
    
    /**
     * Registra overrides de controllers
     */
    protected function registerControllerOverrides(): void
    {
        // Quando Laravel precisar do controller original,
        // vai usar seu controller customizado
        $this->app->bind(
            \Webkul\Admin\Http\Controllers\Leads\LeadController::class,
            \Webkul\NomePackage\Http\Controllers\Admin\LeadController::class
        );
    }

    /**
     * ╔═══════════════════════════════════════════════════════════════════╗
     * ║  BOOT: Executado DEPOIS de todos os providers registrados         ║
     * ╠═══════════════════════════════════════════════════════════════════╣
     * ║  ✅ USAR PARA:                                                    ║
     * ║     - Views, Assets, Migrations                                   ║
     * ║     - Event Listeners                                             ║
     * ║     - Publicações (vendor:publish)                                ║
     * ║     - Comandos Artisan                                            ║
     * ║     - Model Overrides (Concord)                                   ║
     * ╚═══════════════════════════════════════════════════════════════════╝
     */
    public function boot(Router $router): void
    {
        // ═══════════════════════════════════════════════════════════════
        // 1. ROTAS
        // ═══════════════════════════════════════════════════════════════
        $this->loadRoutesFrom(__DIR__ . '/../Routes/admin-routes.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api-routes.php');
        
        // ═══════════════════════════════════════════════════════════════
        // 2. VIEWS (namespace: 'nomepackage')
        // Uso: view('nomepackage::admin.dashboard')
        // ═══════════════════════════════════════════════════════════════
        $this->loadViewsFrom(
            __DIR__ . '/../Resources/views',
            'nomepackage'
        );
        
        // ═══════════════════════════════════════════════════════════════
        // 3. TRADUÇÕES (namespace: 'nomepackage')
        // Uso: trans('nomepackage::app.title')
        // ═══════════════════════════════════════════════════════════════
        $this->loadTranslationsFrom(
            __DIR__ . '/../Resources/lang',
            'nomepackage'
        );
        
        // ═══════════════════════════════════════════════════════════════
        // 4. MIGRATIONS
        // ═══════════════════════════════════════════════════════════════
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        
        // ═══════════════════════════════════════════════════════════════
        // 5. MIDDLEWARE (opcional)
        // ═══════════════════════════════════════════════════════════════
        $router->aliasMiddleware(
            'nomepackage.auth',
            \Webkul\NomePackage\Http\Middleware\CustomMiddleware::class
        );
        
        // ═══════════════════════════════════════════════════════════════
        // 6. BLADE COMPONENTS (opcional)
        // ═══════════════════════════════════════════════════════════════
        Blade::componentNamespace(
            'Webkul\\NomePackage\\View\\Components',
            'nomepackage'
        );
        
        // ═══════════════════════════════════════════════════════════════
        // 7. PUBLICAÇÕES
        // ═══════════════════════════════════════════════════════════════
        $this->registerPublishables();
        
        // ═══════════════════════════════════════════════════════════════
        // 8. MODEL OVERRIDES (Concord)
        // ═══════════════════════════════════════════════════════════════
        $this->registerModelOverrides();
        
        // ═══════════════════════════════════════════════════════════════
        // 9. EVENT SERVICE PROVIDER
        // ═══════════════════════════════════════════════════════════════
        $this->app->register(EventServiceProvider::class);
        
        // ═══════════════════════════════════════════════════════════════
        // 10. COMANDOS ARTISAN (opcional)
        // ═══════════════════════════════════════════════════════════════
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Webkul\NomePackage\Console\Commands\CustomCommand::class,
            ]);
        }
    }
    
    /**
     * Registra assets e views para publicação
     */
    protected function registerPublishables(): void
    {
        // Assets → public/vendor/nomepackage/
        $this->publishes([
            __DIR__ . '/../Resources/assets' => public_path('vendor/nomepackage'),
        ], 'nomepackage-assets');
        
        // Views → resources/views/vendor/nomepackage/
        $this->publishes([
            __DIR__ . '/../Resources/views' => resource_path('views/vendor/nomepackage'),
        ], 'nomepackage-views');
        
        // Config → config/nomepackage.php
        $this->publishes([
            __DIR__ . '/../Config/nomepackage.php' => config_path('nomepackage.php'),
        ], 'nomepackage-config');
    }
    
    /**
     * Registra overrides de models usando Concord
     * IMPORTANTE: Sempre usar Contracts, nunca classes diretas!
     */
    protected function registerModelOverrides(): void
    {
        // ✅ CORRETO: Usando Contract
        $this->app->concord->registerModel(
            \Webkul\Lead\Contracts\Lead::class,
            \Webkul\NomePackage\Models\Lead::class
        );
        
        // ❌ ERRADO: Usando classe direta
        // $this->app->bind('Webkul\Lead\Models\Lead', Lead::class);
    }
}
```

### composer.json do Package

```json
{
  "name": "webkul/nome-package",
  "description": "Descrição do seu package",
  "version": "1.0.0",
  "type": "library",
  "license": "MIT",
  "authors": [
    {
      "name": "Seu Nome",
      "email": "seu@email.com"
    }
  ],
  "require": {
    "php": "^8.1",
    "webkul/core": "*",
    "webkul/admin": "*"
  },
  "autoload": {
    "psr-4": {
      "Webkul\\NomePackage\\": "src/"
    }
  },
  "extra": {
    "laravel": {
      "providers": [
        "Webkul\\NomePackage\\Providers\\NomePackageServiceProvider"
      ]
    }
  },
  "minimum-stability": "dev",
  "prefer-stable": true
}
```

---

## Ciclo de Vida e Carregamento

```
┌─────────────────────────────────────────────────────────────────────┐
│                  CICLO DE VIDA DO PACKAGE                           │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  1. composer dump-autoload                                          │
│     │                                                               │
│     ▼                                                               │
│  2. Laravel carrega config/modules.php                              │
│     │                                                               │
│     ▼                                                               │
│  3. TODOS os register() são executados (ordem do modules.php)       │
│     │                                                               │
│     ├─ Admin::register()                                            │
│     ├─ Core::register()                                             │
│     ├─ Lead::register()                                             │
│     ├─ Contact::register()                                          │
│     └─ NomePackage::register()  ← SEU PACKAGE                       │
│     │                                                               │
│     ▼                                                               │
│  4. TODOS os boot() são executados (mesma ordem)                    │
│     │                                                               │
│     ├─ Admin::boot()                                                │
│     ├─ Core::boot()                                                 │
│     ├─ Lead::boot()                                                 │
│     ├─ Contact::boot()                                              │
│     └─ NomePackage::boot()  ← SEU PACKAGE (último = vence)          │
│     │                                                               │
│     ▼                                                               │
│  5. Application Ready                                               │
│     │                                                               │
│     ▼                                                               │
│  6. Request Processing                                              │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### config/modules.php - Ordem de Carregamento

```php
<?php
// config/modules.php

return [
    'modules' => [
        // ═══════════════════════════════════════════════════════════
        // CORE (sempre primeiro)
        // ═══════════════════════════════════════════════════════════
        'Core',
        'Admin',
        'UI',
        
        // ═══════════════════════════════════════════════════════════
        // MÓDULOS FUNCIONAIS (ordem alfabética ou por dependência)
        // ═══════════════════════════════════════════════════════════
        'Attribute',
        'Contact',
        'Lead',
        'Product',
        'Quote',
        'Activity',
        'User',
        'Email',
        
        // ═══════════════════════════════════════════════════════════
        // CUSTOM (sempre por último!)
        // Quem carrega por último, sobrescreve
        // ═══════════════════════════════════════════════════════════
        'CustomTheme',      // Seu tema
        'LoginCustom',      // Seu login customizado
        'CustomWorkflow',   // Suas automações
    ]
];
```

**⚠️ REGRA DE OURO:** Seu package custom SEMPRE deve ser o último na lista!

---

# PARTE III - SISTEMA DE OVERRIDES

## Override de Controllers

### Quando Usar?

Use override de controller quando precisa **modificar a lógica de request/response**:
- Adicionar validação customizada
- Modificar dados antes de salvar
- Adicionar lógica de negócio
- Disparar eventos customizados

### Anatomia do Controller Override

```php
<?php
// packages/Webkul/CustomTheme/src/Http/Controllers/Admin/LeadController.php

namespace Webkul\CustomTheme\Http\Controllers\Admin;

// ⚠️ SEMPRE estender o controller original
use Webkul\Admin\Http\Controllers\Leads\LeadController as BaseLeadController;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class LeadController extends BaseLeadController
{
    /**
     * Override do método store (criar lead)
     * 
     * PADRÃO RECOMENDADO:
     * 1. Event BEFORE
     * 2. Lógica original (ou customizada)
     * 3. Customização adicional
     * 4. Event AFTER
     * 5. Response
     */
    public function store()
    {
        // ═══════════════════════════════════════════════════════════
        // BEFORE: Disparar evento antes de criar
        // ═══════════════════════════════════════════════════════════
        Event::dispatch('lead.create.before');
        
        // ═══════════════════════════════════════════════════════════
        // VALIDAÇÃO CUSTOMIZADA
        // ═══════════════════════════════════════════════════════════
        $this->validate(request(), [
            'custom_field' => 'required|string|max:255',
        ]);
        
        // ═══════════════════════════════════════════════════════════
        // CRIAR (usa repository original)
        // ═══════════════════════════════════════════════════════════
        $lead = $this->leadRepository->create(request()->all());
        
        // ═══════════════════════════════════════════════════════════
        // SUA CUSTOMIZAÇÃO
        // ═══════════════════════════════════════════════════════════
        $lead->update([
            'custom_field' => request()->input('custom_field'),
            'created_by' => auth()->user()->id,
            'source' => 'custom_form',
        ]);
        
        // Log customizado
        Log::info("Lead #{$lead->id} criado via formulário customizado");
        
        // ═══════════════════════════════════════════════════════════
        // AFTER: Disparar evento depois de criar
        // ═══════════════════════════════════════════════════════════
        Event::dispatch('lead.create.after', $lead);
        
        // ═══════════════════════════════════════════════════════════
        // RESPONSE
        // ═══════════════════════════════════════════════════════════
        session()->flash('success', trans('admin::app.leads.create-success'));
        return redirect()->route('admin.leads.index');
    }
    
    /**
     * Override do método update
     */
    public function update($id)
    {
        Event::dispatch('lead.update.before', $id);
        
        $lead = $this->leadRepository->find($id);
        
        // Validação customizada
        if (request()->input('priority') > 10) {
            return back()->withErrors(['priority' => 'Prioridade máxima é 10']);
        }
        
        // Update
        $lead = $this->leadRepository->update(request()->all(), $id);
        
        Event::dispatch('lead.update.after', $lead);
        
        session()->flash('success', trans('admin::app.leads.update-success'));
        return redirect()->route('admin.leads.index');
    }
    
    /**
     * Métodos NÃO sobrescritos são HERDADOS automaticamente
     * index(), show(), edit(), destroy() funcionam normalmente
     */
}
```

### Registrar Override no Service Provider

```php
// No register() do seu Service Provider
protected function registerControllerOverrides(): void
{
    $this->app->bind(
        \Webkul\Admin\Http\Controllers\Leads\LeadController::class,
        \Webkul\CustomTheme\Http\Controllers\Admin\LeadController::class
    );
}
```

---

## Override de Models (Eloquent)

### Quando Usar?

Use override de model quando precisa **modificar estrutura ou comportamento do Eloquent**:
- Adicionar colunas ao `$fillable`
- Adicionar relacionamentos
- Adicionar accessors/mutators
- Adicionar scopes
- Modificar casting de dados

### Anatomia do Model Override

```php
<?php
// packages/Webkul/CustomTheme/src/Models/Lead.php

namespace Webkul\CustomTheme\Models;

// ⚠️ SEMPRE estender o model original
use Webkul\Lead\Models\Lead as BaseLead;

class Lead extends BaseLead
{
    /**
     * ═══════════════════════════════════════════════════════════════
     * ADICIONAR COLUNAS AO FILLABLE
     * ═══════════════════════════════════════════════════════════════
     */
    protected $fillable = [
        // Colunas herdadas do model original são mantidas
        // Adicione apenas as novas:
        'custom_field_1',
        'custom_field_2',
        'priority',
        'metadata',
        'source',
    ];

    /**
     * ═══════════════════════════════════════════════════════════════
     * CASTS (conversão automática de tipos)
     * ═══════════════════════════════════════════════════════════════
     */
    protected $casts = [
        'metadata' => 'json',
        'priority' => 'integer',
        'is_premium' => 'boolean',
        'value' => 'decimal:2',
        'contacted_at' => 'datetime',
    ];

    /**
     * ═══════════════════════════════════════════════════════════════
     * NOVO RELACIONAMENTO
     * ═══════════════════════════════════════════════════════════════
     */
    public function customNotes()
    {
        return $this->hasMany(\Webkul\CustomTheme\Models\Note::class);
    }
    
    public function assignedUser()
    {
        return $this->belongsTo(\Webkul\User\Models\User::class, 'assigned_to');
    }

    /**
     * ═══════════════════════════════════════════════════════════════
     * ACCESSOR (propriedade computada, read-only)
     * Uso: $lead->display_name
     * ═══════════════════════════════════════════════════════════════
     */
    public function getDisplayNameAttribute()
    {
        return strtoupper($this->title) . ' (ID: ' . $this->id . ')';
    }
    
    public function getFormattedValueAttribute()
    {
        return 'R$ ' . number_format($this->value, 2, ',', '.');
    }

    /**
     * ═══════════════════════════════════════════════════════════════
     * MUTATOR (modificar valor antes de salvar)
     * Uso: $lead->title = 'teste'; → salva 'Teste'
     * ═══════════════════════════════════════════════════════════════
     */
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = ucfirst(trim($value));
    }

    /**
     * ═══════════════════════════════════════════════════════════════
     * SCOPE (query reutilizável)
     * Uso: Lead::active()->get()
     *      Lead::highPriority()->get()
     * ═══════════════════════════════════════════════════════════════
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    public function scopeHighPriority($query)
    {
        return $query->where('priority', '>=', 8);
    }
    
    public function scopeCreatedThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month);
    }

    /**
     * ═══════════════════════════════════════════════════════════════
     * MÉTODOS CUSTOMIZADOS
     * ═══════════════════════════════════════════════════════════════
     */
    public function markAsContacted()
    {
        $this->update([
            'contacted_at' => now(),
            'status' => 'contacted',
        ]);
    }
    
    public function isPremium(): bool
    {
        return $this->value >= 10000;
    }
}
```

### Registrar Override (usando Concord)

```php
// No boot() do seu Service Provider
protected function registerModelOverrides(): void
{
    // ✅ CORRETO: Usando Contract (interface)
    $this->app->concord->registerModel(
        \Webkul\Lead\Contracts\Lead::class,      // ← Interface
        \Webkul\CustomTheme\Models\Lead::class   // ← Sua implementação
    );
}
```

**⚠️ POR QUE CONTRACTS?**

```php
// ❌ NUNCA FAÇA:
$this->app->bind('Webkul\Lead\Models\Lead', Lead::class);
// Quebra se core mudar a implementação

// ✅ SEMPRE FAÇA:
$this->app->concord->registerModel(LeadContract::class, Lead::class);
// Usa contrato (interface) - estável contra updates
```

---

## Override de Views (Blade)

### Quando Usar?

Use override de view quando quer **mudar UI/layout sem modificar lógica**:
- Alterar estrutura HTML
- Adicionar elementos visuais
- Modificar formulários
- Trocar componentes

### Processo de Override

```bash
# 1. Localizar view original
find packages/Webkul -name "login.blade.php"

# 2. Criar mesma estrutura no seu package
mkdir -p packages/Webkul/CustomTheme/Resources/views/auth

# 3. Copiar view original
cp packages/Webkul/Admin/Resources/views/auth/login.blade.php \
   packages/Webkul/CustomTheme/Resources/views/auth/login.blade.php

# 4. Editar sua versão
```

### Anatomia da View Override

```blade
{{-- packages/Webkul/CustomTheme/Resources/views/auth/login.blade.php --}}

@extends('admin::layouts.anonymous-master')

@section('page_title')
    {{ trans('customtheme::app.login.title') }}
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('vendor/customtheme/css/login.css') }}">
@endpush

@section('content')
    <div class="custom-login-container">
        {{-- CUSTOMIZAÇÃO: Logo --}}
        <div class="login-header">
            <img src="{{ asset('vendor/customtheme/images/logo.png') }}" 
                 alt="Logo" 
                 class="logo">
            <h1>{{ trans('customtheme::app.login.welcome') }}</h1>
        </div>

        {{-- CUSTOMIZAÇÃO: Formulário com estilo customizado --}}
        <form method="POST" 
              action="{{ route('admin.session.create') }}" 
              class="login-form">
            @csrf
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" 
                       name="email" 
                       id="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}"
                       required 
                       autofocus>
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" 
                       name="password" 
                       id="password"
                       class="form-control @error('password') is-invalid @enderror"
                       required>
                @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember">
                    Lembrar-me
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">
                Entrar
            </button>
            
            <a href="{{ route('admin.forget-password.create') }}" class="forgot-link">
                Esqueceu a senha?
            </a>
        </form>
        
        {{-- CUSTOMIZAÇÃO: Footer --}}
        <div class="login-footer">
            <p>© {{ date('Y') }} - Seu CRM Customizado</p>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/customtheme/js/login.js') }}"></script>
@endpush
```

### Registrar e Publicar

```php
// No boot() do Service Provider
$this->publishes([
    __DIR__ . '/../Resources/views/auth/login.blade.php' =>
        resource_path('views/vendor/admin/auth/login.blade.php'),
], 'customtheme-views');
```

### Ordem de Busca do Laravel

```
1. resources/views/vendor/admin/auth/login.blade.php  ← PRIMEIRO (sua versão)
2. packages/Webkul/Admin/Resources/views/auth/login.blade.php  ← FALLBACK (original)
```

---

## View Render Events

### O que são?

**Pontos de injeção** nas views do Krayin onde você pode adicionar conteúdo **sem duplicar a view inteira**.

```
❌ BAD: Override de view de 500 linhas só para adicionar 1 linha
✅ GOOD: View Render Event para injetar pequenos trechos
```

### Como Funciona

```blade
{{-- Na view original do Krayin --}}
<div class="form-actions">
    {{-- Ponto de injeção ANTES --}}
    {!! view_render_event('admin.leads.create.form_buttons.before') !!}
    
    <button type="submit" class="primary-button">
        @lang('admin::app.leads.create.save-btn')
    </button>
    
    {{-- Ponto de injeção DEPOIS --}}
    {!! view_render_event('admin.leads.create.form_buttons.after') !!}
</div>
```

### Escutar View Render Events

```php
<?php
// packages/Webkul/CustomTheme/src/Providers/EventServiceProvider.php

namespace Webkul\CustomTheme\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // ═══════════════════════════════════════════════════════════
        // INJETAR ANTES DO BOTÃO
        // ═══════════════════════════════════════════════════════════
        Event::listen(
            'admin.leads.create.form_buttons.before',
            function ($viewRenderEventManager) {
                $viewRenderEventManager->addTemplate(
                    'customtheme::components.lead-warning'
                );
            }
        );
        
        // ═══════════════════════════════════════════════════════════
        // INJETAR DEPOIS DO BOTÃO
        // ═══════════════════════════════════════════════════════════
        Event::listen(
            'admin.leads.create.form_buttons.after',
            function ($viewRenderEventManager) {
                $viewRenderEventManager->addTemplate(
                    'customtheme::components.lead-info'
                );
            }
        );
    }
}
```

### Blade para Injetar

```blade
{{-- packages/Webkul/CustomTheme/Resources/views/components/lead-warning.blade.php --}}

<div class="alert alert-warning mb-3">
    <i class="icon-warning"></i>
    <strong>Atenção:</strong> Verifique todos os campos obrigatórios antes de salvar!
</div>
```

### Resultado Final

```html
<div class="form-actions">
    <!-- Injetado pelo View Render Event -->
    <div class="alert alert-warning mb-3">
        <i class="icon-warning"></i>
        <strong>Atenção:</strong> Verifique todos os campos obrigatórios antes de salvar!
    </div>
    
    <button type="submit" class="primary-button">
        Salvar Lead
    </button>
    
    <!-- Injetado pelo View Render Event -->
    <div class="alert alert-info">
        O lead será notificado por email automaticamente.
    </div>
</div>
```

---

# PARTE IV - SISTEMA DE EVENTOS

## Anatomia de Eventos

O Krayin usa um sistema de eventos para **comunicação desacoplada** entre componentes:

```
┌─────────────────────────────────────────────────────────────────────┐
│                    FLUXO DE EVENTOS                                 │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│   Controller                     Listeners                          │
│      │                              │                               │
│      │ Event::dispatch(            │                               │
│      │   'lead.update.after',      │                               │
│      │   $lead                     │                               │
│      │ )                           │                               │
│      │                             │                               │
│      ▼                             │                               │
│   ┌──────────────────┐             │                               │
│   │  Event Dispatcher │─────────────┼───────────────┐               │
│   └──────────────────┘             │               │               │
│                                    ▼               ▼               │
│                           ┌──────────────┐ ┌──────────────┐        │
│                           │ Listener 1   │ │ Listener 2   │        │
│                           │ (Send Email) │ │ (Update Cache)│        │
│                           └──────────────┘ └──────────────┘        │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

## Catálogo de Eventos Krayin

```php
// ═══════════════════════════════════════════════════════════════════════
// LEAD EVENTS
// ═══════════════════════════════════════════════════════════════════════
'lead.create.before'              // Antes de criar lead
'lead.create.after'               // Depois de criar lead
'lead.update.before'              // Antes de atualizar lead
'lead.update.after'               // Depois de atualizar lead
'lead.delete.before'              // Antes de deletar lead
'lead.delete.after'               // Depois de deletar lead

// ═══════════════════════════════════════════════════════════════════════
// CONTACT EVENTS
// ═══════════════════════════════════════════════════════════════════════
'contacts.person.create.before'
'contacts.person.create.after'
'contacts.person.update.before'
'contacts.person.update.after'
'contacts.person.delete.before'
'contacts.person.delete.after'
'contacts.organization.create.after'
'contacts.organization.update.after'
'contacts.organization.delete.after'

// ═══════════════════════════════════════════════════════════════════════
// QUOTE EVENTS
// ═══════════════════════════════════════════════════════════════════════
'quote.create.before'
'quote.create.after'
'quote.update.before'
'quote.update.after'
'quote.delete.before'
'quote.delete.after'

// ═══════════════════════════════════════════════════════════════════════
// PRODUCT EVENTS
// ═══════════════════════════════════════════════════════════════════════
'product.create.after'
'product.update.after'
'product.delete.after'

// ═══════════════════════════════════════════════════════════════════════
// EMAIL EVENTS
// ═══════════════════════════════════════════════════════════════════════
'email.create.after'
'email.update.after'

// ═══════════════════════════════════════════════════════════════════════
// ACTIVITY EVENTS
// ═══════════════════════════════════════════════════════════════════════
'activity.create.after'
'activity.update.after'
'activity.delete.after'

// ═══════════════════════════════════════════════════════════════════════
// USER EVENTS
// ═══════════════════════════════════════════════════════════════════════
'user.create.after'
'user.update.after'
'user.delete.after'

// Lista completa: https://devdocs.krayincrm.com/events/
```

## Event Service Provider

```php
<?php
// packages/Webkul/CustomTheme/src/Providers/EventServiceProvider.php

namespace Webkul\CustomTheme\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Mapeamento de eventos para listeners
     */
    protected $listen = [
        'lead.create.after' => [
            'Webkul\CustomTheme\Listeners\Lead@onCreate',
        ],
        'lead.update.after' => [
            'Webkul\CustomTheme\Listeners\Lead@onUpdate',
        ],
        'contact.create.after' => [
            'Webkul\CustomTheme\Listeners\Contact@onCreate',
        ],
    ];

    public function boot(): void
    {
        // ═══════════════════════════════════════════════════════════
        // MÉTODO 1: String notation (recomendado)
        // ═══════════════════════════════════════════════════════════
        Event::listen(
            'lead.update.after',
            'Webkul\CustomTheme\Listeners\Lead@onUpdate'
        );

        // ═══════════════════════════════════════════════════════════
        // MÉTODO 2: Closure (para lógica simples)
        // ═══════════════════════════════════════════════════════════
        Event::listen('lead.create.after', function ($lead) {
            \Log::info("Novo lead criado: {$lead->title}");
        });

        // ═══════════════════════════════════════════════════════════
        // MÉTODO 3: Array (múltiplos listeners)
        // ═══════════════════════════════════════════════════════════
        Event::listen('quote.create.after', [
            'Webkul\CustomTheme\Listeners\Quote@onCreate',
            'Webkul\CustomTheme\Listeners\Notification@onQuoteCreated',
        ]);
        
        // ═══════════════════════════════════════════════════════════
        // VIEW RENDER EVENTS
        // ═══════════════════════════════════════════════════════════
        Event::listen(
            'admin.leads.create.form_buttons.before',
            function ($viewRenderEventManager) {
                $viewRenderEventManager->addTemplate(
                    'customtheme::components.lead-warning'
                );
            }
        );
    }
}
```

## Listeners e Mailables

### Listener Completo

```php
<?php
// packages/Webkul/CustomTheme/src/Listeners/Lead.php

namespace Webkul\CustomTheme\Listeners;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Webkul\CustomTheme\Mail\LeadNotification;
use Webkul\CustomTheme\Mail\LeadUpdatedNotification;

class Lead
{
    /**
     * Handle lead.create.after
     */
    public function onCreate($lead)
    {
        Log::info("Lead #{$lead->id} criado: {$lead->title}");
        
        // Enviar email de boas-vindas
        Mail::queue(new LeadNotification($lead));
        
        // Limpar cache de contagem
        Cache::forget('leads_count');
        Cache::forget('leads_this_month');
    }

    /**
     * Handle lead.update.after
     */
    public function onUpdate($lead)
    {
        Log::info("Lead #{$lead->id} atualizado");
        
        // Verificar se pipeline mudou
        if ($lead->isDirty('lead_pipeline_stage_id')) {
            $this->handlePipelineChange($lead);
        }
        
        // Verificar se status mudou para "won"
        if ($lead->status === 'won' && $lead->getOriginal('status') !== 'won') {
            $this->handleLeadWon($lead);
        }
        
        // Limpar cache
        Cache::forget("lead_{$lead->id}");
    }
    
    /**
     * Handle mudança de pipeline
     */
    protected function handlePipelineChange($lead)
    {
        Log::info("Lead #{$lead->id} mudou de pipeline");
        
        // Enviar notificação
        Mail::queue(new LeadUpdatedNotification($lead));
        
        // Disparar evento customizado
        event('lead.pipeline.changed', $lead);
    }
    
    /**
     * Handle lead ganho
     */
    protected function handleLeadWon($lead)
    {
        Log::info("Lead #{$lead->id} foi GANHO!");
        
        // Atualizar métricas
        Cache::increment('leads_won_this_month');
        
        // Notificar equipe
        // ...
    }
}
```

### Mailable Completo

```php
<?php
// packages/Webkul/CustomTheme/src/Mail/LeadNotification.php

namespace Webkul\CustomTheme\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public $lead
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->lead->person?->emails?->first()?->value ?? config('mail.admin_email'),
            subject: 'Novo Lead: ' . $this->lead->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'customtheme::mail.lead-notification',
            with: [
                'leadTitle' => $this->lead->title,
                'leadValue' => $this->lead->lead_value,
                'leadUrl' => route('admin.leads.view', $this->lead->id),
                'createdAt' => $this->lead->created_at->format('d/m/Y H:i'),
            ],
        );
    }
}
```

### Email Template

```blade
{{-- packages/Webkul/CustomTheme/Resources/views/mail/lead-notification.blade.php --}}

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Lead</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4F46E5; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .btn { display: inline-block; background: #4F46E5; color: white; padding: 12px 24px; 
               text-decoration: none; border-radius: 5px; margin-top: 15px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Novo Lead Criado!</h1>
        </div>
        
        <div class="content">
            <h2>{{ $leadTitle }}</h2>
            
            <p><strong>Valor:</strong> R$ {{ number_format($leadValue, 2, ',', '.') }}</p>
            <p><strong>Criado em:</strong> {{ $createdAt }}</p>
            
            <a href="{{ $leadUrl }}" class="btn">Ver Lead no CRM</a>
        </div>
        
        <div class="footer">
            <p>Este é um email automático do seu CRM.</p>
            <p>© {{ date('Y') }} - Seu CRM Customizado</p>
        </div>
    </div>
</body>
</html>
```

---

# PARTE V - CUSTOMIZAÇÃO VISUAL

## CSS Variables e Temas

### Arquivo de Tema Principal

```css
/* packages/Webkul/CustomTheme/Resources/assets/css/theme.css */

/* ═══════════════════════════════════════════════════════════════════
   VARIÁVEIS DE COR (Customização Principal)
   ═══════════════════════════════════════════════════════════════════ */
:root {
    /* Cores Primárias */
    --primary-color: #4F46E5;           /* Indigo */
    --primary-hover: #4338CA;
    --primary-light: #EEF2FF;
    
    /* Cores Secundárias */
    --secondary-color: #10B981;         /* Emerald */
    --secondary-hover: #059669;
    
    /* Cores de Status */
    --success-color: #10B981;
    --danger-color: #EF4444;
    --warning-color: #F59E0B;
    --info-color: #3B82F6;
    
    /* Neutros */
    --text-primary: #1F2937;
    --text-secondary: #6B7280;
    --text-muted: #9CA3AF;
    --bg-primary: #FFFFFF;
    --bg-secondary: #F3F4F6;
    --bg-tertiary: #E5E7EB;
    --border-color: #E5E7EB;
    
    /* Espaçamento */
    --spacing-xs: 4px;
    --spacing-sm: 8px;
    --spacing-md: 16px;
    --spacing-lg: 24px;
    --spacing-xl: 32px;
    
    /* Bordas */
    --border-radius-sm: 4px;
    --border-radius-md: 8px;
    --border-radius-lg: 12px;
    --border-radius-full: 9999px;
    
    /* Sombras */
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    
    /* Tipografia */
    --font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    --font-size-xs: 12px;
    --font-size-sm: 14px;
    --font-size-md: 16px;
    --font-size-lg: 18px;
    --font-size-xl: 20px;
}

/* ═══════════════════════════════════════════════════════════════════
   COMPONENTES CUSTOMIZADOS
   ═══════════════════════════════════════════════════════════════════ */

/* Botões */
.btn-primary {
    background-color: var(--primary-color);
    color: white;
    padding: var(--spacing-sm) var(--spacing-md);
    border-radius: var(--border-radius-md);
    border: none;
    cursor: pointer;
    transition: background-color 0.2s;
}

.btn-primary:hover {
    background-color: var(--primary-hover);
}

/* Cards */
.custom-card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-sm);
    padding: var(--spacing-lg);
}

/* Login Container */
.custom-login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
}

.login-box {
    background: var(--bg-primary);
    padding: var(--spacing-xl);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-lg);
    width: 100%;
    max-width: 400px;
}

/* Sidebar */
.custom-sidebar {
    background: var(--primary-color);
    color: white;
}

.custom-sidebar .menu-item:hover {
    background: var(--primary-hover);
}

/* ═══════════════════════════════════════════════════════════════════
   RESPONSIVIDADE
   ═══════════════════════════════════════════════════════════════════ */
@media (max-width: 768px) {
    :root {
        --spacing-lg: 16px;
        --spacing-xl: 24px;
    }
    
    .login-box {
        margin: var(--spacing-md);
        max-width: 100%;
    }
}
```

## Assets e Imagens

### Estrutura de Assets

```
Resources/assets/
├── css/
│   ├── theme.css           # Tema principal
│   ├── login.css           # Estilos do login
│   ├── admin.css           # Estilos do admin
│   └── components/
│       ├── buttons.css
│       ├── cards.css
│       └── forms.css
├── js/
│   ├── app.js              # JavaScript principal
│   ├── login.js            # Scripts do login
│   └── components/
│       └── datepicker.js
└── images/
    ├── logo.png            # Logo principal (recomendado: 200x60px)
    ├── logo-white.png      # Logo para fundo escuro
    ├── logo-icon.png       # Ícone do logo (32x32px)
    ├── favicon.ico         # Favicon (16x16, 32x32px)
    ├── favicon-32x32.png
    ├── apple-touch-icon.png
    └── icons/
        ├── dashboard.svg
        ├── leads.svg
        └── contacts.svg
```

### Usar Assets em Blade

```blade
{{-- Logo --}}
<img src="{{ asset('vendor/customtheme/images/logo.png') }}" alt="Logo">

{{-- CSS --}}
<link rel="stylesheet" href="{{ asset('vendor/customtheme/css/theme.css') }}">

{{-- JS --}}
<script src="{{ asset('vendor/customtheme/js/app.js') }}"></script>

{{-- Favicon --}}
<link rel="icon" type="image/x-icon" href="{{ asset('vendor/customtheme/images/favicon.ico') }}">
```

### Publicar Assets

```bash
# Publicar uma vez
php artisan vendor:publish --tag=customtheme-assets

# Forçar sobrescrita
php artisan vendor:publish --tag=customtheme-assets --force

# Publicar tudo
php artisan vendor:publish --force --all
```

## Traduções Multi-idioma

### Estrutura de Traduções

```
Resources/lang/
├── en/
│   └── app.php
└── pt_BR/
    └── app.php
```

### Arquivo de Tradução

```php
<?php
// packages/Webkul/CustomTheme/Resources/lang/pt_BR/app.php

return [
    'login' => [
        'title' => 'Bem-vindo ao CRM',
        'welcome' => 'Faça login para continuar',
        'email' => 'Email',
        'password' => 'Senha',
        'remember' => 'Lembrar-me',
        'submit' => 'Entrar',
        'forgot' => 'Esqueceu a senha?',
    ],
    
    'dashboard' => [
        'title' => 'Dashboard',
        'welcome_message' => 'Bem-vindo de volta, :name!',
        'leads_today' => 'Leads Hoje',
        'leads_month' => 'Leads este Mês',
        'conversion_rate' => 'Taxa de Conversão',
    ],
    
    'leads' => [
        'title' => 'Leads',
        'create' => 'Novo Lead',
        'edit' => 'Editar Lead',
        'delete' => 'Excluir Lead',
        'save_success' => 'Lead salvo com sucesso!',
        'delete_success' => 'Lead excluído com sucesso!',
    ],
    
    'common' => [
        'save' => 'Salvar',
        'cancel' => 'Cancelar',
        'delete' => 'Excluir',
        'edit' => 'Editar',
        'view' => 'Visualizar',
        'search' => 'Buscar',
        'filter' => 'Filtrar',
        'export' => 'Exportar',
        'import' => 'Importar',
    ],
];
```

### Usar Traduções

```blade
{{-- Tradução simples --}}
{{ trans('customtheme::app.login.title') }}

{{-- Tradução com parâmetros --}}
{{ trans('customtheme::app.dashboard.welcome_message', ['name' => $user->name]) }}

{{-- Helper curto --}}
{{ __('customtheme::app.common.save') }}

{{-- Em PHP --}}
@php
    $title = trans('customtheme::app.leads.title');
@endphp
```

### Registrar Traduções

```php
// No boot() do Service Provider
$this->loadTranslationsFrom(
    __DIR__ . '/../Resources/lang',
    'customtheme'
);
```

---

# PARTE VI - INFRAESTRUTURA DOCKER

## Arquitetura Docker Swarm

```
┌─────────────────────────────────────────────────────────────────────┐
│                    ARQUITETURA DOCKER SWARM                         │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌─────────────────────────────────────────────────────────────┐    │
│  │                      TRAEFIK                                │    │
│  │              (Reverse Proxy + SSL)                          │    │
│  │                                                             │    │
│  │  • Let's Encrypt automático                                 │    │
│  │  • Load balancing                                           │    │
│  │  • HTTP → HTTPS redirect                                    │    │
│  └─────────────────────────────────────────────────────────────┘    │
│                              │                                      │
│                              ▼                                      │
│  ┌─────────────────────────────────────────────────────────────┐    │
│  │                    KRAYIN (PHP-FPM)                         │    │
│  │                                                             │    │
│  │  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐           │    │
│  │  │ Laravel     │ │ Packages    │ │ Custom      │           │    │
│  │  │ Core        │ │ (Admin,     │ │ Packages    │           │    │
│  │  │             │ │ Lead, etc.) │ │             │           │    │
│  │  └─────────────┘ └─────────────┘ └─────────────┘           │    │
│  │                                                             │    │
│  │  Volumes:                                                   │    │
│  │  • /app/storage (logs, uploads)                            │    │
│  │  • /app/public (assets)                                    │    │
│  └─────────────────────────────────────────────────────────────┘    │
│                              │                                      │
│              ┌───────────────┴───────────────┐                      │
│              ▼                               ▼                      │
│  ┌───────────────────────┐     ┌───────────────────────┐           │
│  │        MYSQL          │     │        REDIS          │           │
│  │   (Database)          │     │   (Cache + Session)   │           │
│  │                       │     │                       │           │
│  │ Volume:               │     │ Volume:               │           │
│  │ /var/lib/mysql        │     │ /data (opcional)      │           │
│  └───────────────────────┘     └───────────────────────┘           │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

## Dockerfile Otimizado para Produção

```dockerfile
# ═══════════════════════════════════════════════════════════════════
# STAGE 1: Builder (instalar dependências)
# ═══════════════════════════════════════════════════════════════════
FROM php:8.2-fpm-alpine AS builder

WORKDIR /app

# Instalar build tools
RUN apk add --no-cache --virtual .build-deps \
    gcc g++ make musl-dev git curl

# Instalar runtime deps
RUN apk add --no-cache \
    git curl libpng libjpeg-turbo libwebp zlib \
    zip unzip openssl icu-libs

# PHP extensions
RUN docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install \
        pdo_mysql gd mbstring exif fileinfo \
        pcntl bcmath intl opcache

# Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Copiar código (incluindo packages/)
COPY . /app

# Install Composer deps (sem dev)
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --optimize-autoloader \
    --classmap-authoritative

# Generate autoload otimizado
RUN composer dump-autoload --optimize --classmap-authoritative

# Publicar assets de TODOS os packages
RUN php artisan vendor:publish --force --all

# Limpar cache para fresh build
RUN php artisan optimize:clear

# ═══════════════════════════════════════════════════════════════════
# STAGE 2: Runtime (imagem final otimizada)
# ═══════════════════════════════════════════════════════════════════
FROM php:8.2-fpm-alpine

WORKDIR /app

# Runtime deps apenas
RUN apk add --no-cache \
    git curl libpng libjpeg-turbo libwebp zlib \
    openssl mysql-client redis icu-libs

# PHP extensions
RUN docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install \
        pdo_mysql gd mbstring exif fileinfo \
        pcntl bcmath intl opcache

# PHP config otimizado para produção
COPY docker/php-prod.ini /usr/local/etc/php/conf.d/prod.ini
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zz-prod.conf

# Copiar de builder (muito menor!)
COPY --from=builder --chown=www-data:www-data /app /app

# Criar diretórios e permissões
RUN mkdir -p /app/storage/logs /app/storage/framework/{sessions,views,cache} \
    /app/bootstrap/cache \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 755 /app/storage /app/bootstrap/cache

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=30s --retries=3 \
    CMD php-fpm-healthcheck || exit 1

# Entrypoint
COPY docker/docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php-fpm"]
```

### docker-entrypoint.sh

```bash
#!/bin/sh
set -e

echo "🚀 Iniciando Krayin CRM..."

# ═══════════════════════════════════════════════════════════════
# 1. Aguardar banco de dados
# ═══════════════════════════════════════════════════════════════
echo "⏳ Aguardando MySQL..."
while ! nc -z ${DB_HOST:-mysql} ${DB_PORT:-3306}; do
    sleep 1
done
echo "✅ MySQL pronto!"

# ═══════════════════════════════════════════════════════════════
# 2. Aguardar Redis
# ═══════════════════════════════════════════════════════════════
echo "⏳ Aguardando Redis..."
while ! nc -z ${REDIS_HOST:-redis} ${REDIS_PORT:-6379}; do
    sleep 1
done
echo "✅ Redis pronto!"

# ═══════════════════════════════════════════════════════════════
# 3. Gerar APP_KEY se não existir
# ═══════════════════════════════════════════════════════════════
if [ -z "$APP_KEY" ]; then
    echo "🔑 Gerando APP_KEY..."
    php artisan key:generate --force
fi

# ═══════════════════════════════════════════════════════════════
# 4. Rodar migrations (se habilitado)
# ═══════════════════════════════════════════════════════════════
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "📦 Rodando migrations..."
    php artisan migrate --force
fi

# ═══════════════════════════════════════════════════════════════
# 5. Publicar assets
# ═══════════════════════════════════════════════════════════════
echo "📄 Publicando assets..."
php artisan vendor:publish --force --all

# ═══════════════════════════════════════════════════════════════
# 6. Limpar e recriar cache
# ═══════════════════════════════════════════════════════════════
echo "🧹 Limpando cache..."
php artisan optimize:clear

if [ "$APP_ENV" = "production" ]; then
    echo "⚡ Gerando caches otimizados..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

# ═══════════════════════════════════════════════════════════════
# 7. Permissões finais
# ═══════════════════════════════════════════════════════════════
echo "🔐 Ajustando permissões..."
chown -R www-data:www-data /app/storage /app/bootstrap/cache

echo "✨ Krayin CRM pronto!"

# Executar comando (php-fpm)
exec "$@"
```

## Docker Compose (DEV e PROD)

### docker-compose.yml (Desenvolvimento)

```yaml
version: '3.9'

services:
  krayin:
    build:
      context: .
      dockerfile: Dockerfile.dev
    container_name: krayin-dev
    environment:
      - APP_ENV=local
      - APP_DEBUG=true
      - APP_KEY=${APP_KEY}
      - APP_URL=http://localhost:8000
      - DB_CONNECTION=mysql
      - DB_HOST=mysql
      - DB_PORT=3306
      - DB_DATABASE=krayin_dev
      - DB_USERNAME=krayin
      - DB_PASSWORD=secret
      - CACHE_DRIVER=redis
      - SESSION_DRIVER=redis
      - QUEUE_CONNECTION=redis
      - REDIS_HOST=redis
    volumes:
      - .:/app
      - ./packages:/app/packages
      - krayin_storage:/app/storage
    ports:
      - "9000:9000"
    depends_on:
      - mysql
      - redis
    networks:
      - krayin-dev

  nginx:
    image: nginx:alpine
    container_name: krayin-nginx-dev
    volumes:
      - ./docker/nginx-dev.conf:/etc/nginx/nginx.conf:ro
      - ./public:/app/public:ro
    ports:
      - "8000:80"
    depends_on:
      - krayin
    networks:
      - krayin-dev

  mysql:
    image: mysql:8.0
    container_name: krayin-mysql-dev
    environment:
      - MYSQL_ROOT_PASSWORD=root
      - MYSQL_DATABASE=krayin_dev
      - MYSQL_USER=krayin
      - MYSQL_PASSWORD=secret
    volumes:
      - mysql_dev:/var/lib/mysql
    ports:
      - "3306:3306"
    networks:
      - krayin-dev

  redis:
    image: redis:7-alpine
    container_name: krayin-redis-dev
    ports:
      - "6379:6379"
    networks:
      - krayin-dev

volumes:
  mysql_dev:
  krayin_storage:

networks:
  krayin-dev:
    driver: bridge
```

### docker-compose-prod.yml (Produção com Swarm)

```yaml
version: '3.9'

services:
  krayin:
    image: ${REGISTRY}/krayin:${VERSION:-latest}
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
      - APP_KEY=${APP_KEY}
      - APP_URL=https://${DOMAIN}
      - DB_CONNECTION=mysql
      - DB_HOST=mysql
      - DB_PORT=3306
      - DB_DATABASE=${DB_DATABASE}
      - DB_USERNAME=${DB_USERNAME}
      - DB_PASSWORD=${DB_PASSWORD}
      - CACHE_DRIVER=redis
      - SESSION_DRIVER=redis
      - QUEUE_CONNECTION=redis
      - REDIS_HOST=redis
      - LOG_CHANNEL=stack
      - RUN_MIGRATIONS=true
    volumes:
      - krayin_storage:/app/storage
      - krayin_public:/app/public
    networks:
      - traefik
      - krayin-internal
    depends_on:
      - mysql
      - redis
    labels:
      # Traefik
      - "traefik.enable=true"
      - "traefik.http.routers.krayin.rule=Host(`${DOMAIN}`)"
      - "traefik.http.routers.krayin.entrypoints=websecure"
      - "traefik.http.routers.krayin.tls=true"
      - "traefik.http.routers.krayin.tls.certresolver=letsencrypt"
      - "traefik.http.services.krayin.loadbalancer.server.port=9000"
      # Redirect HTTP → HTTPS
      - "traefik.http.routers.krayin-http.rule=Host(`${DOMAIN}`)"
      - "traefik.http.routers.krayin-http.entrypoints=web"
      - "traefik.http.routers.krayin-http.middlewares=redirect-https"
      - "traefik.http.middlewares.redirect-https.redirectscheme.scheme=https"
    deploy:
      replicas: 2
      resources:
        limits:
          cpus: '2'
          memory: 2G
        reservations:
          cpus: '1'
          memory: 1G
      update_config:
        parallelism: 1
        delay: 10s
        failure_action: rollback
      restart_policy:
        condition: on-failure
        delay: 5s
        max_attempts: 5

  mysql:
    image: mysql:8.0
    environment:
      - MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}
      - MYSQL_DATABASE=${DB_DATABASE}
      - MYSQL_USER=${DB_USERNAME}
      - MYSQL_PASSWORD=${DB_PASSWORD}
    volumes:
      - mysql_data:/var/lib/mysql
    networks:
      - krayin-internal
    deploy:
      placement:
        constraints:
          - node.role == manager

  redis:
    image: redis:7-alpine
    command: redis-server --appendonly yes
    volumes:
      - redis_data:/data
    networks:
      - krayin-internal
    healthcheck:
      test: ["CMD", "redis-cli", "ping"]
      interval: 10s
      timeout: 5s
      retries: 5

volumes:
  krayin_storage:
    driver: local
  krayin_public:
    driver: local
  mysql_data:
    driver: local
  redis_data:
    driver: local

networks:
  traefik:
    external: true
  krayin-internal:
    driver: overlay
```

## Traefik e SSL

### Configuração Traefik (traefik.yml)

```yaml
api:
  dashboard: true
  insecure: true

entryPoints:
  web:
    address: ":80"
  websecure:
    address: ":443"

providers:
  docker:
    endpoint: "unix:///var/run/docker.sock"
    exposedByDefault: false
    swarmMode: true

certificatesResolvers:
  letsencrypt:
    acme:
      email: ${ACME_EMAIL}
      storage: /letsencrypt/acme.json
      httpChallenge:
        entryPoint: web
```

---

# PARTE VII - OPERAÇÕES

## Fluxo de Deploy

```
┌─────────────────────────────────────────────────────────────────────┐
│                       FLUXO DE DEPLOY                               │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌──────────────┐                                                   │
│  │  DEV LOCAL   │                                                   │
│  │  ──────────  │                                                   │
│  │  1. Código   │                                                   │
│  │  2. Package  │                                                   │
│  │  3. Teste    │                                                   │
│  └──────┬───────┘                                                   │
│         │ git push                                                  │
│         ▼                                                           │
│  ┌──────────────┐                                                   │
│  │   GIT REPO   │                                                   │
│  │  ──────────  │                                                   │
│  │  • main      │                                                   │
│  │  • tags      │                                                   │
│  └──────┬───────┘                                                   │
│         │ docker build                                              │
│         ▼                                                           │
│  ┌──────────────┐                                                   │
│  │    BUILD     │                                                   │
│  │  ──────────  │                                                   │
│  │  Dockerfile  │                                                   │
│  │  + composer  │                                                   │
│  │  + publish   │                                                   │
│  └──────┬───────┘                                                   │
│         │ docker push                                               │
│         ▼                                                           │
│  ┌──────────────┐                                                   │
│  │   REGISTRY   │                                                   │
│  │  ──────────  │                                                   │
│  │  Harbor /    │                                                   │
│  │  DockerHub   │                                                   │
│  └──────┬───────┘                                                   │
│         │ docker service update                                     │
│         ▼                                                           │
│  ┌──────────────────────────────────────┐                           │
│  │          DOCKER SWARM                │                           │
│  │  ────────────────────────────────    │                           │
│  │  ┌────────┐  ┌────────┐  ┌────────┐  │                           │
│  │  │TRAEFIK │  │ KRAYIN │  │  REDIS │  │                           │
│  │  │ (SSL)  │  │ (APP)  │  │(CACHE) │  │                           │
│  │  └────────┘  └────────┘  └────────┘  │                           │
│  │                  │                   │                           │
│  │              ┌────────┐              │                           │
│  │              │ MYSQL  │              │                           │
│  │              │  (DB)  │              │                           │
│  │              └────────┘              │                           │
│  └──────────────────────────────────────┘                           │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### Script de Deploy One-Liner

```bash
#!/bin/bash
# deploy.sh

set -e

VERSION=${1:-$(git describe --tags --always)}
REGISTRY=${REGISTRY:-"seu-registry.com"}
PROJECT=${PROJECT:-"krayin"}
DOMAIN=${DOMAIN:-"seu-dominio.com"}
SERVICE=${SERVICE:-"krayin_krayin"}

echo "🔨 BUILD: ${PROJECT}:${VERSION}"
docker build -t ${PROJECT}:${VERSION} .

echo "🏷️ TAG"
docker tag ${PROJECT}:${VERSION} ${REGISTRY}/${PROJECT}:${VERSION}
docker tag ${PROJECT}:${VERSION} ${REGISTRY}/${PROJECT}:latest

echo "⬆️ PUSH"
docker push ${REGISTRY}/${PROJECT}:${VERSION}
docker push ${REGISTRY}/${PROJECT}:latest

echo "📡 DEPLOY"
docker service update \
    --image ${REGISTRY}/${PROJECT}:${VERSION} \
    --force ${SERVICE}

echo "⏳ AGUARDANDO..."
sleep 30

echo "✅ VERIFICANDO"
curl -sI https://${DOMAIN}/admin | head -1

echo "✨ Deploy ${VERSION} completo!"
```

## Cache Management

### Comandos de Cache

```bash
# ═══════════════════════════════════════════════════════════════
# LIMPAR TUDO (recomendado após qualquer mudança)
# ═══════════════════════════════════════════════════════════════
php artisan optimize:clear

# ═══════════════════════════════════════════════════════════════
# LIMPAR INDIVIDUALMENTE
# ═══════════════════════════════════════════════════════════════
php artisan cache:clear      # Cache de aplicação
php artisan config:clear     # Cache de configuração
php artisan route:clear      # Cache de rotas
php artisan view:clear       # Cache de views compiladas
php artisan event:clear      # Cache de eventos

# ═══════════════════════════════════════════════════════════════
# GERAR CACHE (produção)
# ═══════════════════════════════════════════════════════════════
php artisan config:cache     # Cachear config
php artisan route:cache      # Cachear rotas
php artisan view:cache       # Cachear views

# ═══════════════════════════════════════════════════════════════
# REDIS (se usar)
# ═══════════════════════════════════════════════════════════════
docker exec redis redis-cli FLUSHALL    # Limpar todo Redis
docker exec redis redis-cli KEYS '*'    # Listar chaves
```

### Quando Limpar Cache?

| Situação | Comando |
|----------|---------|
| Alterou view Blade | `php artisan view:clear` |
| Alterou config/ | `php artisan config:clear` |
| Alterou routes/ | `php artisan route:clear` |
| Alterou .env | `php artisan config:clear` |
| Publicou assets | `php artisan optimize:clear` |
| Deploy em produção | `php artisan optimize:clear` (depois cachear) |
| Nada funciona | `php artisan optimize:clear` + limpar browser |

## Troubleshooting Map

```
┌─────────────────────────────────────────────────────────────────────┐
│                    MAPA DE TROUBLESHOOTING                          │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  PROBLEMA                 │ DIAGNÓSTICO           │ SOLUÇÃO         │
│  ═════════════════════════╪═══════════════════════╪═════════════════│
│                                                                     │
│  "View not found"         │ View em local errado  │ Copiar mantendo │
│                           │                       │ estrutura       │
│  ─────────────────────────┼───────────────────────┼─────────────────│
│  "Asset 404"              │ Asset não publicado   │ vendor:publish  │
│                           │                       │ --tag=xxx       │
│  ─────────────────────────┼───────────────────────┼─────────────────│
│  "CSS antigo aparece"     │ Cache não limpo       │ optimize:clear  │
│                           │                       │ + F5 browser    │
│  ─────────────────────────┼───────────────────────┼─────────────────│
│  "Listener não dispara"   │ Não registrado        │ Verificar Event │
│                           │                       │ ServiceProvider │
│  ─────────────────────────┼───────────────────────┼─────────────────│
│  "Model não carrega"      │ Binding errado        │ Usar Concord    │
│                           │                       │ registerModel() │
│  ─────────────────────────┼───────────────────────┼─────────────────│
│  "Package não aparece"    │ Não registrado        │ Adicionar em    │
│                           │                       │ config/modules  │
│  ─────────────────────────┼───────────────────────┼─────────────────│
│  "Permission denied"      │ Permissão errada      │ chown -R        │
│                           │                       │ www-data        │
│  ─────────────────────────┼───────────────────────┼─────────────────│
│  "Controller não override"│ Binding errado        │ Usar bind() no  │
│                           │                       │ register()      │
│  ─────────────────────────┼───────────────────────┼─────────────────│
│  "CSRF token mismatch"    │ Session expirou       │ Login novamente │
│  ─────────────────────────┼───────────────────────┼─────────────────│
│  "Mail não envia"         │ Config errada         │ Verificar .env  │
│                           │                       │ MAIL_*          │
│  ─────────────────────────┼───────────────────────┼─────────────────│
│  "MySQL não conecta"      │ Container down        │ docker ps       │
│                           │                       │ docker logs     │
│  ─────────────────────────┼───────────────────────┼─────────────────│
│  "Deploy não atualiza"    │ Cache de imagem       │ --force no      │
│                           │                       │ service update  │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### Diagnóstico Rápido

```bash
# ═══════════════════════════════════════════════════════════════
# VERIFICAÇÕES BÁSICAS
# ═══════════════════════════════════════════════════════════════

# 1. Package registrado?
cat config/modules.php | grep -i custom

# 2. Assets publicados?
ls -la public/vendor/customtheme/

# 3. Views existem?
find packages/Webkul/CustomTheme -name "*.blade.php"

# 4. Logs de erro?
tail -f storage/logs/laravel.log

# 5. Service Provider carregado?
php artisan package:discover

# ═══════════════════════════════════════════════════════════════
# DOCKER
# ═══════════════════════════════════════════════════════════════

# Container rodando?
docker ps | grep krayin

# Logs do container
docker logs krayin --tail 100

# Entrar no container
docker exec -it krayin bash

# Verificar dentro do container
docker exec krayin php artisan about
docker exec krayin ls -la /app/public/vendor/

# ═══════════════════════════════════════════════════════════════
# TESTAR EVENTO
# ═══════════════════════════════════════════════════════════════
php artisan tinker
>>> event('lead.update.after', \Webkul\Lead\Models\Lead::first());
```

## Comandos Essenciais

### Laravel Artisan

```bash
# ═══════════════════════════════════════════════════════════════
# PUBLICAÇÃO
# ═══════════════════════════════════════════════════════════════
php artisan vendor:publish --tag=customtheme-assets
php artisan vendor:publish --tag=customtheme-views
php artisan vendor:publish --force --all

# ═══════════════════════════════════════════════════════════════
# CACHE
# ═══════════════════════════════════════════════════════════════
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ═══════════════════════════════════════════════════════════════
# DATABASE
# ═══════════════════════════════════════════════════════════════
php artisan migrate
php artisan migrate:fresh --seed
php artisan db:seed --class=CustomSeeder

# ═══════════════════════════════════════════════════════════════
# DEBUG
# ═══════════════════════════════════════════════════════════════
php artisan route:list | grep custom
php artisan tinker
php artisan about
```

### Docker

```bash
# ═══════════════════════════════════════════════════════════════
# LOCAL (docker-compose)
# ═══════════════════════════════════════════════════════════════
docker-compose build
docker-compose up -d
docker-compose down
docker-compose logs -f krayin
docker-compose exec krayin bash
docker-compose exec krayin php artisan optimize:clear

# ═══════════════════════════════════════════════════════════════
# PRODUÇÃO (docker swarm)
# ═══════════════════════════════════════════════════════════════
docker build -t projeto:v1.0.0 .
docker tag projeto:v1.0.0 registry/projeto:v1.0.0
docker push registry/projeto:v1.0.0

docker stack deploy -c docker-compose-prod.yml krayin
docker stack ps krayin
docker service ls
docker service logs -f krayin_krayin --tail 100

docker service update --image registry/projeto:v1.0.0 --force krayin_krayin

# Executar comando no container Swarm
docker exec $(docker ps -q -f "name=krayin_krayin") php artisan optimize:clear
```

### Git

```bash
# ═══════════════════════════════════════════════════════════════
# VERSIONAMENTO
# ═══════════════════════════════════════════════════════════════
git tag -a v1.0.0 -m "Release v1.0.0 - Custom Theme"
git push origin --tags

# Semantic versioning:
# v1.0.0 → v1.0.1 = Bug fix
# v1.0.0 → v1.1.0 = Feature nova
# v1.0.0 → v2.0.0 = Breaking change
```

---

# PARTE VIII - PADRÕES E CONFORMIDADE

## Nomenclatura e Convenções

### Convenções Obrigatórias

| Elemento | Padrão | Exemplo | Anti-Pattern |
|----------|--------|---------|--------------|
| **Package Name** | PascalCase | `CustomTheme` | `custom_theme`, `customtheme` |
| **Namespace** | `Webkul\{Package}\` | `Webkul\CustomTheme\` | `App\CustomTheme\` |
| **Service Provider** | `{Package}ServiceProvider` | `CustomThemeServiceProvider` | `ServiceProvider` |
| **Controller** | `{Entity}Controller` | `LeadController` | `LeadsController` |
| **Model** | Singular, PascalCase | `Lead` | `Leads`, `lead` |
| **Migration** | `yyyy_mm_dd_hhmmss_{action}_{table}` | `2025_01_01_000000_add_custom_fields_to_leads` | `add_fields` |
| **View Namespace** | lowercase | `customtheme` | `CustomTheme` |
| **Asset Tag** | `{package}-assets` | `customtheme-assets` | `assets` |
| **Route Name** | `{package}.{resource}.{action}` | `customtheme.leads.store` | `store_lead` |
| **Event Name** | `{entity}.{action}.{when}` | `lead.update.after` | `leadUpdated` |
| **Listener Method** | `on{Action}` ou `{action}` | `onUpdate`, `update` | `handleLeadUpdate` |

### Estrutura de Pastas Padrão

```
packages/Webkul/{Package}/
├── src/
│   ├── Config/
│   ├── Console/Commands/
│   ├── Contracts/
│   ├── Database/{Migrations,Seeders}/
│   ├── Events/
│   ├── Http/{Controllers,Middleware,Requests}/
│   ├── Listeners/
│   ├── Mail/
│   ├── Models/
│   ├── Providers/
│   ├── Repositories/
│   └── Routes/
├── Resources/
│   ├── assets/{css,js,images}/
│   ├── lang/{en,pt_BR}/
│   └── views/
├── Database/
│   ├── Migrations/
│   └── Seeders/
├── Tests/
│   ├── Feature/
│   └── Unit/
├── composer.json
└── README.md
```

## Anti-Patterns

### ❌ O que NUNCA fazer

| Anti-Pattern | Por Quê é Ruim | ✅ Alternativa Correta |
|--------------|----------------|----------------------|
| Editar `packages/Webkul/Admin/*` | Quebra em updates | Criar override em package próprio |
| Commitar `.env` com secrets | Exposição de dados | Usar Docker Secrets/Vault |
| `composer update` em produção | Pode quebrar | Testar em staging primeiro |
| Ignorar `vendor:publish` | Assets não aparecem | Sempre publicar após deploy |
| Esquecer `optimize:clear` | Cache antigo persiste | Limpar cache após mudanças |
| Lógica pesada em views | Difícil manutenção | Usar Controllers/Services |
| Package sem Service Provider | Não carrega | Provider é obrigatório |
| Binding de classe em models | Quebra em updates | Usar Contracts (interfaces) |
| Não documentar overrides | Equipe não entende | Documentar o quê e por quê |
| Deploy sem testar local | Quebra em produção | Sempre testar antes |

### ✅ O que SEMPRE fazer

```bash
# Após qualquer mudança de código
php artisan optimize:clear

# Após publicar assets
php artisan vendor:publish --tag=xxx-assets --force

# Antes de build Docker
git status  # Garantir que tudo está commitado
git tag vX.Y.Z

# Após deploy
docker exec container php artisan optimize:clear
curl -I https://dominio.com/admin  # Verificar se está no ar
```

## Checklists de Conformidade

### ☐ Antes de Criar Package

```
□ Nome segue PascalCase
□ Namespace correto: Webkul\{NomePackage}\
□ Service Provider criado
□ composer.json com autoload PSR-4
□ Estrutura de pastas padrão
□ README.md documentando o package
□ Adicionado em config/modules.php (por último)
□ composer.json raiz tem path repository
```

### ☐ Antes de Override

```
□ Estendendo classe original (extends BaseController)
□ Usando Contracts para models (não classes diretas)
□ Mantendo eventos before/after
□ Documentando o que foi alterado
□ Testando herança (métodos não overridden funcionam?)
□ Binding correto (Controller no register, Model no boot)
```

### ☐ Antes de Event Listener

```
□ EventServiceProvider criado
□ Registrado no Service Provider principal
□ Listener com namespace correto
□ Método existe e recebe parâmetro correto
□ Testado com php artisan tinker
```

### ☐ Antes de Build Docker

```
□ config/modules.php inclui seu package
□ composer.json tem path repository
□ Todos os assets existem em Resources/assets/
□ Dockerfile executa vendor:publish
□ Dockerfile executa optimize:clear
□ .dockerignore configurado
□ Testado localmente com docker-compose
```

### ☐ Antes de Deploy Produção

```
□ Código commitado e tagueado
□ Testado em ambiente local
□ Testado em staging
□ Todos os listeners funcionando
□ Assets publicados corretamente
□ Tradução pt_BR completa
□ Backup de banco realizado
□ Plano de rollback pronto
□ Team notificado
□ Janela de manutenção marcada
```

### ☐ Após Deploy

```
□ curl https://dominio.com → 200 OK
□ Login funciona
□ Assets carregam (F12 → Network)
□ Customização visual visível
□ Logs sem erros críticos
□ Cache invalidado no container
□ Email funcionando (se aplicável)
□ Stakeholders notificados
```

## Matriz de Decisão

### Quando Usar Cada Abordagem?

| Situação | Controller | Model | View | View Event | Listener |
|----------|:----------:|:-----:|:----:|:----------:|:--------:|
| **Alterar lógica request/response** | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Adicionar coluna no DB** | ❌ | ✅ | ❌ | ❌ | ❌ |
| **Novo relacionamento** | ❌ | ✅ | ❌ | ❌ | ❌ |
| **Alterar UI/layout inteiro** | ❌ | ❌ | ✅ | ❌ | ❌ |
| **Injetar pequeno trecho HTML** | ❌ | ❌ | ❌ | ✅ | ❌ |
| **Executar ação quando X acontece** | ❌ | ❌ | ❌ | ❌ | ✅ |
| **Enviar email após criar lead** | ❌ | ❌ | ❌ | ❌ | ✅ |
| **Validar dados customizados** | ⚡ | ❌ | ❌ | ❌ | ✅ |
| **Cache/Update relacionamentos** | ❌ | ❌ | ❌ | ❌ | ✅ |
| **Log de auditoria** | ❌ | ❌ | ❌ | ❌ | ✅ |
| **Integração externa (API)** | ⚡ | ❌ | ❌ | ❌ | ✅ |
| **Alterar cores/estilos** | ❌ | ❌ | ❌ | ❌ | ❌ | CSS |
| **Trocar logo** | ❌ | ❌ | ❌ | ❌ | ❌ | Asset |

**Legenda:** ✅ = Ideal | ⚡ = Possível | ❌ = Não recomendado

### Árvore de Decisão

```
PRECISO CUSTOMIZAR... O QUÊ?
│
├─ Lógica de request/response?
│  └─ SIM → Override Controller
│
├─ Estrutura de dados (DB)?
│  └─ SIM → Override Model (Concord)
│
├─ Interface (UI)?
│  ├─ Layout inteiro? → Override View
│  └─ Pequeno trecho? → View Render Event
│
├─ Executar ação quando algo acontece?
│  └─ SIM → Event Listener
│
├─ Cores/Estilos?
│  └─ SIM → CSS Variables
│
└─ Logo/Imagens?
   └─ SIM → Asset Replace
```

---

# ANEXOS

## Anexo A - Blueprints de Packages

### Blueprint 1: Theme Customization (Visual)

```
packages/Webkul/CustomTheme/
├── src/
│   └── Providers/
│       └── CustomThemeServiceProvider.php
├── Resources/
│   ├── views/
│   │   └── auth/
│   │       └── login.blade.php
│   ├── assets/
│   │   ├── css/
│   │   │   └── theme.css
│   │   └── images/
│   │       └── logo.png
│   └── lang/
│       └── pt_BR/
│           └── app.php
└── composer.json
```

### Blueprint 2: Workflow Automation (Eventos + Email)

```
packages/Webkul/CustomWorkflow/
├── src/
│   ├── Providers/
│   │   ├── CustomWorkflowServiceProvider.php
│   │   └── EventServiceProvider.php
│   ├── Listeners/
│   │   ├── Lead.php
│   │   └── Contact.php
│   └── Mail/
│       ├── LeadNotification.php
│       └── ContactWelcome.php
├── Resources/
│   └── views/
│       └── mail/
│           ├── lead-notification.blade.php
│           └── contact-welcome.blade.php
└── composer.json
```

### Blueprint 3: Full Override (Controller + Model + View)

```
packages/Webkul/CustomLead/
├── src/
│   ├── Providers/
│   │   ├── CustomLeadServiceProvider.php
│   │   └── EventServiceProvider.php
│   ├── Http/
│   │   └── Controllers/
│   │       └── Admin/
│   │           └── LeadController.php
│   ├── Models/
│   │   └── Lead.php
│   └── Listeners/
│       └── Lead.php
├── Resources/
│   └── views/
│       └── leads/
│           ├── create.blade.php
│           └── edit.blade.php
├── Database/
│   └── Migrations/
│       └── 2025_01_01_add_custom_fields_to_leads.php
└── composer.json
```

---

## Anexo B - Quick Reference

```
╔═══════════════════════════════════════════════════════════════════════════════╗
║                        KRAYIN CRM - QUICK REFERENCE                           ║
╠═══════════════════════════════════════════════════════════════════════════════╣
║                                                                               ║
║  ESTRUTURA MÍNIMA DO PACKAGE                                                  ║
║  ─────────────────────────────                                                ║
║  packages/Webkul/CustomTheme/                                                 ║
║  ├── src/Providers/CustomThemeServiceProvider.php                             ║
║  ├── Resources/views/                                                         ║
║  ├── Resources/assets/css/theme.css                                           ║
║  └── composer.json                                                            ║
║                                                                               ║
║  COMANDOS ESSENCIAIS                                                          ║
║  ───────────────────                                                          ║
║  php artisan vendor:publish --tag=customtheme-assets                          ║
║  php artisan optimize:clear                                                   ║
║  php artisan tinker                                                           ║
║                                                                               ║
║  DOCKER COMMANDS                                                              ║
║  ───────────────                                                              ║
║  docker-compose build && docker-compose up -d                                 ║
║  docker-compose exec krayin php artisan optimize:clear                        ║
║  docker service update --image registry/projeto:v1.0.0 --force SERVICE        ║
║                                                                               ║
║  QUANDO CUSTOMIZAR?                                                           ║
║  ─────────────────                                                            ║
║  Lógica HTTP      → Controller Override                                       ║
║  Estrutura DB     → Model Override (Concord)                                  ║
║  UI completa      → View Override                                             ║
║  UI parcial       → View Render Event                                         ║
║  Ação automática  → Event Listener                                            ║
║  Cores/Estilos    → CSS Variables                                             ║
║                                                                               ║
║  TROUBLESHOOTING RÁPIDO                                                       ║
║  ─────────────────────                                                        ║
║  Asset 404?       → php artisan vendor:publish --force --all                  ║
║  CSS antigo?      → php artisan optimize:clear + F5                           ║
║  View não acha?   → Verificar estrutura de pastas                             ║
║  Listener fail?   → Verificar EventServiceProvider                            ║
║                                                                               ║
╚═══════════════════════════════════════════════════════════════════════════════╝
```

---

## Anexo C - Glossário

| Termo | Definição |
|-------|-----------|
| **Package** | Módulo independente com código, views e assets |
| **Service Provider** | Classe que registra e inicializa um package |
| **Override** | Substituir comportamento padrão sem editar core |
| **Concord** | Sistema de registro de models do Krayin |
| **Contract** | Interface que define a estrutura de um model |
| **View Render Event** | Ponto de injeção em views para adicionar conteúdo |
| **Listener** | Classe que executa ação quando um evento é disparado |
| **Mailable** | Classe que representa um email a ser enviado |
| **Accessor** | Método que computa valor de atributo (get) |
| **Mutator** | Método que modifica valor antes de salvar (set) |
| **Scope** | Query reutilizável em model Eloquent |
| **Binding** | Associação de interface a implementação no container |

---

# 🏆 CONCLUSÃO

Este documento estabelece o **padrão canônico definitivo** para customização do Krayin CRM.

Seguindo esta anatomia, você garante:

1. ✅ **Consistência** entre todos os packages customizados
2. ✅ **Manutenibilidade** a longo prazo
3. ✅ **Segurança** contra updates do core
4. ✅ **Reprodutibilidade** em diferentes ambientes
5. ✅ **Escalabilidade** do time de desenvolvimento
6. ✅ **Documentação** para onboarding de novos devs

---

```
╔═══════════════════════════════════════════════════════════════════════════════╗
║                                                                               ║
║   "Domine a anatomia, domine a customização."                                ║
║                                                                               ║
║   Versão: 1.0.0 FINAL                                                         ║
║   Data: Dezembro 2025                                                         ║
║   Status: ✅ CONSOLIDADO E APROVADO PARA USO                                  ║
║                                                                               ║
╚═══════════════════════════════════════════════════════════════════════════════╝
```
