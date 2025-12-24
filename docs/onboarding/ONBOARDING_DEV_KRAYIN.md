# 🚀 ONBOARDING DO DESENVOLVEDOR KRAYIN
## Guia Completo de Capacitação - Do Zero ao Deploy

**Versão:** 1.0.0  
**Duração Estimada:** 3-5 dias  
**Pré-requisitos:** PHP, Laravel básico, Docker básico  
**Resultado:** Dev capaz de criar, customizar e deployar packages Krayin

---

## 📋 ÍNDICE

1. [Visão Geral do Programa](#-fase-0-visão-geral)
2. [Fase 1: Ambiente e Fundamentos](#-fase-1-ambiente-e-fundamentos-dia-1)
3. [Fase 2: Anatomia do Krayin](#-fase-2-anatomia-do-krayin-dia-1-2)
4. [Fase 3: Primeiro Package](#-fase-3-primeiro-package-dia-2)
5. [Fase 4: Sistema de Overrides](#-fase-4-sistema-de-overrides-dia-2-3)
6. [Fase 5: Eventos e Listeners](#-fase-5-eventos-e-listeners-dia-3)
7. [Fase 6: Customização Visual](#-fase-6-customização-visual-dia-3-4)
8. [Fase 7: Docker e Deploy](#-fase-7-docker-e-deploy-dia-4-5)
9. [Fase 8: Validação Final](#-fase-8-validação-final-dia-5)
10. [Referência Rápida](#-referência-rápida)
11. [Troubleshooting](#-troubleshooting-comum)

---

## 🎯 FASE 0: VISÃO GERAL

### Objetivo do Onboarding

```
┌─────────────────────────────────────────────────────────────────────┐
│                    JORNADA DO DESENVOLVEDOR                         │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  INÍCIO                                                             │
│    │                                                                │
│    ▼                                                                │
│  ┌─────────────────┐                                                │
│  │ FASE 1: Ambiente│  → Subir Krayin local + Ferramentas DEV       │
│  └────────┬────────┘                                                │
│           │                                                         │
│           ▼                                                         │
│  ┌─────────────────┐                                                │
│  │ FASE 2: Anatomia│  → Entender arquitetura e fluxos              │
│  └────────┬────────┘                                                │
│           │                                                         │
│           ▼                                                         │
│  ┌─────────────────┐                                                │
│  │ FASE 3: Package │  → Criar primeiro package do zero             │
│  └────────┬────────┘                                                │
│           │                                                         │
│           ▼                                                         │
│  ┌─────────────────┐                                                │
│  │ FASE 4: Override│  → Dominar Controller, Model, View            │
│  └────────┬────────┘                                                │
│           │                                                         │
│           ▼                                                         │
│  ┌─────────────────┐                                                │
│  │ FASE 5: Eventos │  → Criar listeners e automações               │
│  └────────┬────────┘                                                │
│           │                                                         │
│           ▼                                                         │
│  ┌─────────────────┐                                                │
│  │ FASE 6: Visual  │  → Customizar UI, assets, traduções           │
│  └────────┬────────┘                                                │
│           │                                                         │
│           ▼                                                         │
│  ┌─────────────────┐                                                │
│  │ FASE 7: Deploy  │  → Docker build + Swarm deploy                │
│  └────────┬────────┘                                                │
│           │                                                         │
│           ▼                                                         │
│  ┌─────────────────┐                                                │
│  │ FASE 8: Validar │  → Checklist final + Certificação             │
│  └─────────────────┘                                                │
│           │                                                         │
│           ▼                                                         │
│  FIM: Dev Pronto para Produção! 🎉                                  │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### Documentação de Referência

| Documento | Descrição | Quando Usar |
|-----------|-----------|-------------|
| **ANATOMIA_GERAL_KRAYIN_CRM.md** | Documento mestre com toda arquitetura | Consulta constante |
| **FERRAMENTAS_DEV_KRAYIN.md** | Blade Tracer, Package Generator, Debug Bar | Fase 1 e durante dev |
| **CHECKLIST_VALIDACAO_DEV.md** | Perguntas de validação | Fase 8 e code reviews |

### Cronograma Sugerido

| Dia | Fases | Foco | Entregável |
|:---:|-------|------|------------|
| 1 | 1-2 | Ambiente + Teoria | Krayin rodando + conceitos |
| 2 | 3-4 | Package + Overrides | Package funcional com override |
| 3 | 5-6 | Eventos + Visual | Listener + customização visual |
| 4 | 6-7 | Visual + Docker | Theme completo + build Docker |
| 5 | 7-8 | Deploy + Validação | Deploy staging + certificação |

---

## 📦 FASE 1: AMBIENTE E FUNDAMENTOS (Dia 1)

### 1.1 Objetivo da Fase

```
┌─────────────────────────────────────────────────────────────────────┐
│  AO FINAL DESTA FASE VOCÊ TERÁ:                                     │
│                                                                     │
│  ✅ Krayin rodando localmente                                       │
│  ✅ Ferramentas de desenvolvimento instaladas                       │
│  ✅ Acesso ao painel admin                                          │
│  ✅ Entendimento da estrutura de pastas                             │
└─────────────────────────────────────────────────────────────────────┘
```

### 1.2 Subir Ambiente Local

#### Opção A: Docker Compose (Recomendado)

```bash
# 1. Clonar repositório
git clone https://github.com/krayin/laravel-crm.git krayin-crm
cd krayin-crm

# 2. Copiar .env
cp .env.example .env

# 3. Subir containers
docker-compose up -d

# 4. Instalar dependências e migrar
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --seed

# 5. Acessar
# http://localhost:8000/admin
# Email: admin@example.com
# Senha: admin123
```

#### Opção B: Instalação Local

```bash
# 1. Clonar
git clone https://github.com/krayin/laravel-crm.git krayin-crm
cd krayin-crm

# 2. Instalar
composer install
cp .env.example .env
php artisan key:generate

# 3. Configurar .env
# DB_DATABASE=krayin
# DB_USERNAME=seu_usuario
# DB_PASSWORD=sua_senha

# 4. Migrar
php artisan migrate --seed

# 5. Servir
php artisan serve

# 6. Acessar http://localhost:8000/admin
```

### 1.3 Instalar Ferramentas de Desenvolvimento

```bash
# Instalar as 3 ferramentas essenciais
composer require krayin/krayin-blade-tracer --dev
composer require krayin/krayin-package-generator --dev
composer require krayin/krayin-debug-bar --dev

# Limpar caches
php artisan view:clear
php artisan route:cache
php artisan optimize:clear

# Verificar instalação
php artisan list | grep package
# Deve mostrar: package:make, package:make-model, etc.
```

### 1.4 Validar Ferramentas

```bash
# TESTE 1: Package Generator
php artisan package:make --help
# Deve mostrar ajuda do comando

# TESTE 2: Blade Tracer
# Acessar http://localhost:8000/admin/login
# Passar mouse sobre formulário
# Deve aparecer tooltip com path do arquivo

# TESTE 3: Debug Bar
# Acessar qualquer página
# Deve aparecer barra no rodapé com stats
```

### 1.5 Conhecer Estrutura de Pastas

```bash
# Execute este comando para ver a estrutura
find packages/Webkul -maxdepth 2 -type d | head -30
```

#### Estrutura Esperada

```
krayin-crm/
├── app/                    # Laravel padrão (pouco usado no Krayin)
├── config/
│   └── modules.php         # ⭐ REGISTRO DE PACKAGES
├── packages/
│   └── Webkul/
│       ├── Admin/          # Interface administrativa
│       ├── Core/           # Funcionalidades base
│       ├── UI/             # Componentes visuais
│       ├── Lead/           # Módulo de leads
│       ├── Contact/        # Módulo de contatos
│       ├── Product/        # Módulo de produtos
│       ├── Quote/          # Módulo de cotações
│       ├── Activity/       # Módulo de atividades
│       ├── User/           # Módulo de usuários
│       ├── Attribute/      # Atributos customizados
│       └── Email/          # Módulo de email
├── public/
│   └── vendor/             # Assets publicados dos packages
├── resources/
│   └── views/
│       └── vendor/         # View overrides publicados
└── storage/
    └── logs/
        └── laravel.log     # Logs de erro
```

### 1.6 Checkpoint da Fase 1

```
┌─────────────────────────────────────────────────────────────────────┐
│  ☐ CHECKLIST FASE 1                                                 │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ☐ Krayin está rodando em http://localhost:8000 ?                   │
│  ☐ Consegui fazer login no admin?                                   │
│  ☐ Blade Tracer mostra tooltip ao passar mouse?                     │
│  ☐ Debug Bar aparece no rodapé das páginas?                         │
│  ☐ Comando php artisan package:make --help funciona?                │
│  ☐ Sei onde fica config/modules.php?                                │
│  ☐ Sei onde ficam os packages em packages/Webkul/?                  │
│                                                                     │
│  TODOS MARCADOS? → Avançar para Fase 2                              │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 🧬 FASE 2: ANATOMIA DO KRAYIN (Dia 1-2)

### 2.1 Objetivo da Fase

```
┌─────────────────────────────────────────────────────────────────────┐
│  AO FINAL DESTA FASE VOCÊ TERÁ:                                     │
│                                                                     │
│  ✅ Entendido a arquitetura package-based                           │
│  ✅ Compreendido o ciclo de vida do ServiceProvider                 │
│  ✅ Conhecimento dos 5 tipos de override                            │
│  ✅ Noção do sistema de eventos                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 2.2 Conceitos Fundamentais

#### DNA do Krayin

```
┌─────────────────────────────────────────────────────────────────────┐
│                    ARQUITETURA KRAYIN                               │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  Laravel Monolítico + Arquitetura Package-Based                     │
│                                                                     │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │                         PACKAGES                             │   │
│  │  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐           │   │
│  │  │  Core   │ │  Admin  │ │   UI    │ │  Lead   │  ...      │   │
│  │  └─────────┘ └─────────┘ └─────────┘ └─────────┘           │   │
│  │       │           │           │           │                 │   │
│  │       └───────────┴───────────┴───────────┘                 │   │
│  │                         │                                    │   │
│  │                         ▼                                    │   │
│  │              ┌─────────────────────┐                        │   │
│  │              │    Laravel Core     │                        │   │
│  │              │  (Routing, Blade,   │                        │   │
│  │              │   Eloquent, etc.)   │                        │   │
│  │              └─────────────────────┘                        │   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  CADA PACKAGE É AUTOCONTIDO:                                        │
│  • Próprios Controllers                                             │
│  • Próprios Models                                                  │
│  • Próprias Views                                                   │
│  • Próprias Rotas                                                   │
│  • Próprios Assets                                                  │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

#### Os 5 Princípios

| # | Princípio | Significado |
|:-:|-----------|-------------|
| 1 | **Isolamento** | Cada package é independente |
| 2 | **Override** | Customizar sem editar core |
| 3 | **Composição** | Packages se complementam |
| 4 | **Publicação** | Assets vão para public/ |
| 5 | **Imutabilidade do Core** | NUNCA editar packages/Webkul/* |

### 2.3 Ciclo de Vida do ServiceProvider

```
┌─────────────────────────────────────────────────────────────────────┐
│                    LIFECYCLE DO PROVIDER                            │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  1. composer dump-autoload                                          │
│     │                                                               │
│     ▼                                                               │
│  2. Laravel carrega config/modules.php                              │
│     │                                                               │
│     ▼                                                               │
│  3. TODOS os register() executam (ordem do modules.php)             │
│     │  • Bindings de container                                      │
│     │  • Merge de configs                                           │
│     │  • Controller overrides                                       │
│     │                                                               │
│     ▼                                                               │
│  4. TODOS os boot() executam (mesma ordem)                          │
│     │  • Rotas                                                      │
│     │  • Views                                                      │
│     │  • Migrations                                                 │
│     │  • Model overrides (Concord)                                  │
│     │  • Eventos                                                    │
│     │                                                               │
│     ▼                                                               │
│  5. Aplicação pronta                                                │
│     │                                                               │
│     ▼                                                               │
│  6. Processamento de requests                                       │
│                                                                     │
│  ⚠️ IMPORTANTE:                                                     │
│  • Controller override → register()                                 │
│  • Model override → boot() com Concord                              │
│  • Quem carrega POR ÚLTIMO ganha os overrides                       │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 2.4 Explorar um Package Existente

```bash
# Vamos explorar o package Lead
cd packages/Webkul/Lead

# Ver estrutura
find . -type f -name "*.php" | head -20

# Ver ServiceProvider
cat src/Providers/LeadServiceProvider.php

# Ver um Model
cat src/Models/Lead.php

# Ver um Controller
cat src/Http/Controllers/LeadController.php
```

#### Estrutura Canônica de Package

```
packages/Webkul/Lead/
├── src/
│   ├── Providers/
│   │   └── LeadServiceProvider.php      # ⭐ Ponto de entrada
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── LeadController.php       # Lógica HTTP
│   │   ├── Requests/
│   │   │   └── LeadRequest.php          # Validação
│   │   └── Middleware/
│   ├── Models/
│   │   └── Lead.php                     # Eloquent Model
│   ├── Repositories/
│   │   └── LeadRepository.php           # Acesso a dados
│   ├── Contracts/
│   │   └── Lead.php                     # ⭐ Interface (para Concord)
│   ├── Listeners/
│   ├── Events/
│   ├── Routes/
│   │   ├── web.php
│   │   └── admin.php
│   └── Config/
│       └── menu.php
├── Resources/
│   ├── views/
│   │   └── leads/
│   │       ├── index.blade.php
│   │       ├── create.blade.php
│   │       └── view.blade.php
│   ├── lang/
│   │   ├── en/
│   │   └── pt_BR/
│   └── assets/
├── Database/
│   ├── Migrations/
│   └── Seeders/
└── composer.json
```

### 2.5 Exercício Prático: Rastrear uma Requisição

```
EXERCÍCIO: Seguir o fluxo de criação de um Lead

1. Acesse http://localhost:8000/admin/leads/create
2. Use Blade Tracer para identificar a view
3. Encontre o Controller que renderiza essa view
4. Encontre o Model Lead
5. Encontre o Contract (interface) do Lead
```

#### Respostas Esperadas

```
View: packages/Webkul/Lead/Resources/views/leads/create.blade.php
Controller: packages/Webkul/Lead/src/Http/Controllers/LeadController.php
Model: packages/Webkul/Lead/src/Models/Lead.php
Contract: packages/Webkul/Lead/src/Contracts/Lead.php
```

### 2.6 Os 5 Tipos de Override

```
┌─────────────────────────────────────────────────────────────────────┐
│                    TIPOS DE OVERRIDE                                │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  1. CONTROLLER OVERRIDE                                             │
│     Quando: Alterar lógica HTTP (validação, response, etc.)         │
│     Como: $this->app->bind() no register()                          │
│                                                                     │
│  2. MODEL OVERRIDE                                                  │
│     Quando: Adicionar campos, relacionamentos, accessors            │
│     Como: concord->registerModel() no boot() usando CONTRACT        │
│                                                                     │
│  3. VIEW OVERRIDE                                                   │
│     Quando: Alterar UI significativamente                           │
│     Como: Copiar para resources/views/vendor/ ou package            │
│                                                                     │
│  4. VIEW RENDER EVENT                                               │
│     Quando: Injetar pequeno HTML sem duplicar view                  │
│     Como: Event::listen('event.name', callback)                     │
│                                                                     │
│  5. EVENT LISTENER                                                  │
│     Quando: Executar ação após evento (email, log, integração)      │
│     Como: EventServiceProvider + Listener class                     │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 2.7 Checkpoint da Fase 2

```
┌─────────────────────────────────────────────────────────────────────┐
│  ☐ CHECKLIST FASE 2                                                 │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ☐ Entendi que Krayin usa arquitetura package-based?                │
│  ☐ Sei a diferença entre register() e boot()?                       │
│  ☐ Sei que Controller override vai no register()?                   │
│  ☐ Sei que Model override vai no boot() com Concord?                │
│  ☐ Entendi que devo usar CONTRACT (interface), não classe direta?   │
│  ☐ Sei que NUNCA devo editar packages/Webkul/*?                     │
│  ☐ Consegui rastrear uma requisição do browser até o Model?         │
│                                                                     │
│  TODOS MARCADOS? → Avançar para Fase 3                              │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 🏗️ FASE 3: PRIMEIRO PACKAGE (Dia 2)

### 3.1 Objetivo da Fase

```
┌─────────────────────────────────────────────────────────────────────┐
│  AO FINAL DESTA FASE VOCÊ TERÁ:                                     │
│                                                                     │
│  ✅ Package CustomTheme criado e registrado                         │
│  ✅ ServiceProvider funcional                                       │
│  ✅ Package carregando corretamente                                 │
└─────────────────────────────────────────────────────────────────────┘
```

### 3.2 Criar Package com Generator

```bash
# Usar o Package Generator!
php artisan package:make Webkul/CustomTheme

# Verificar estrutura criada
ls -la packages/Webkul/CustomTheme/
```

### 3.3 Estrutura Gerada

```
packages/Webkul/CustomTheme/
├── src/
│   ├── Providers/
│   │   └── CustomThemeServiceProvider.php
│   ├── Config/
│   └── Routes/
├── Resources/
│   ├── views/
│   └── lang/
└── composer.json
```

### 3.4 Configurar Autoload

Editar `composer.json` na **raiz do projeto**:

```json
{
    "autoload": {
        "psr-4": {
            "Webkul\\CustomTheme\\": "packages/Webkul/CustomTheme/src"
        }
    },
    "repositories": [
        {
            "type": "path",
            "url": "packages/Webkul/*"
        }
    ]
}
```

```bash
# Regenerar autoload
composer dump-autoload
```

### 3.5 Registrar Package

Editar `config/modules.php`:

```php
<?php

return [
    // ... outros packages existentes ...
    
    // ⚠️ ADICIONAR POR ÚLTIMO!
    'CustomTheme',
];
```

### 3.6 Configurar ServiceProvider

Editar `packages/Webkul/CustomTheme/src/Providers/CustomThemeServiceProvider.php`:

```php
<?php

namespace Webkul\CustomTheme\Providers;

use Illuminate\Support\ServiceProvider;

class CustomThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     * EXECUTADO PRIMEIRO - Bindings e configs
     */
    public function register()
    {
        // Merge config (se tiver)
        // $this->mergeConfigFrom(__DIR__.'/../Config/config.php', 'customtheme');
        
        // Controller overrides virão aqui
    }

    /**
     * Bootstrap services.
     * EXECUTADO DEPOIS - Rotas, views, migrations, model overrides
     */
    public function boot()
    {
        // Rotas
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        
        // Views
        $this->loadViewsFrom(__DIR__.'/../../Resources/views', 'customtheme');
        
        // Traduções
        $this->loadTranslationsFrom(__DIR__.'/../../Resources/lang', 'customtheme');
        
        // Publishes (assets)
        $this->publishes([
            __DIR__.'/../../Resources/assets' => public_path('vendor/customtheme'),
        ], 'customtheme-assets');
        
        // Model overrides virão aqui (com Concord)
    }
}
```

### 3.7 Criar Rota de Teste

Criar `packages/Webkul/CustomTheme/src/Routes/web.php`:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::get('/customtheme/test', function () {
        return 'CustomTheme está funcionando! 🎉';
    })->name('customtheme.test');
});
```

### 3.8 Testar Package

```bash
# Limpar cache
php artisan optimize:clear

# Verificar se package está registrado
cat config/modules.php | grep -i custom
# Deve mostrar: 'CustomTheme'

# Verificar rotas
php artisan route:list | grep customtheme
# Deve mostrar a rota /customtheme/test

# Testar no browser
# Acessar: http://localhost:8000/customtheme/test
# Deve mostrar: "CustomTheme está funcionando! 🎉"
```

### 3.9 Exercício: Adicionar View

```bash
# Criar view
mkdir -p packages/Webkul/CustomTheme/Resources/views/test
```

Criar `packages/Webkul/CustomTheme/Resources/views/test/index.blade.php`:

```blade
<!DOCTYPE html>
<html>
<head>
    <title>CustomTheme Test</title>
</head>
<body>
    <h1>CustomTheme View</h1>
    <p>Se você está vendo isso, o package está funcionando corretamente!</p>
    <p>Data/Hora: {{ now() }}</p>
</body>
</html>
```

Atualizar rota em `web.php`:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::get('/customtheme/test', function () {
        return view('customtheme::test.index');
    })->name('customtheme.test');
});
```

```bash
# Limpar cache de views
php artisan view:clear

# Testar: http://localhost:8000/customtheme/test
```

### 3.10 Checkpoint da Fase 3

```
┌─────────────────────────────────────────────────────────────────────┐
│  ☐ CHECKLIST FASE 3                                                 │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ☐ Package CustomTheme criado com package:make?                     │
│  ☐ Autoload PSR-4 configurado no composer.json raiz?                │
│  ☐ composer dump-autoload executado?                                │
│  ☐ Package adicionado POR ÚLTIMO no config/modules.php?             │
│  ☐ ServiceProvider tem register() e boot() separados?               │
│  ☐ Rota /customtheme/test funciona?                                 │
│  ☐ View customtheme::test.index renderiza?                          │
│                                                                     │
│  TODOS MARCADOS? → Avançar para Fase 4                              │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 🔄 FASE 4: SISTEMA DE OVERRIDES (Dia 2-3)

### 4.1 Objetivo da Fase

```
┌─────────────────────────────────────────────────────────────────────┐
│  AO FINAL DESTA FASE VOCÊ TERÁ:                                     │
│                                                                     │
│  ✅ Controller override funcional                                   │
│  ✅ Model override funcional                                        │
│  ✅ View override funcional                                         │
│  ✅ Validado com Blade Tracer                                       │
└─────────────────────────────────────────────────────────────────────┘
```

### 4.2 Controller Override

#### Passo 1: Identificar Controller Original

```bash
# Vamos sobrescrever o LeadController
cat packages/Webkul/Lead/src/Http/Controllers/LeadController.php | head -50
```

#### Passo 2: Criar Controller Custom

```bash
# Criar estrutura
mkdir -p packages/Webkul/CustomTheme/src/Http/Controllers
```

Criar `packages/Webkul/CustomTheme/src/Http/Controllers/LeadController.php`:

```php
<?php

namespace Webkul\CustomTheme\Http\Controllers;

use Webkul\Lead\Http\Controllers\LeadController as BaseLeadController;

class LeadController extends BaseLeadController
{
    /**
     * Override do método index
     * Adiciona log customizado
     */
    public function index()
    {
        // ✅ Código custom ANTES
        \Log::info('CustomTheme: Acessando listagem de leads');
        
        // Chamar método original
        return parent::index();
    }
    
    /**
     * Override do método store
     * Mantém eventos before/after
     */
    public function store()
    {
        // ✅ Código custom ANTES
        \Log::info('CustomTheme: Criando novo lead');
        
        // Chamar método original (que dispara eventos)
        return parent::store();
    }
}
```

#### Passo 3: Registrar Override no register()

Editar `CustomThemeServiceProvider.php`:

```php
public function register()
{
    // ⭐ Controller override no register()!
    $this->app->bind(
        \Webkul\Lead\Http\Controllers\LeadController::class,
        \Webkul\CustomTheme\Http\Controllers\LeadController::class
    );
}
```

#### Passo 4: Testar

```bash
# Limpar cache
php artisan optimize:clear

# Acessar http://localhost:8000/admin/leads
# Verificar storage/logs/laravel.log
tail -f storage/logs/laravel.log | grep CustomTheme
# Deve aparecer: "CustomTheme: Acessando listagem de leads"
```

### 4.3 Model Override

#### Passo 1: Identificar Model e Contract

```bash
# Model original
cat packages/Webkul/Lead/src/Models/Lead.php | head -30

# Contract (interface) - IMPORTANTE!
cat packages/Webkul/Lead/src/Contracts/Lead.php
```

#### Passo 2: Criar Model Custom

```bash
mkdir -p packages/Webkul/CustomTheme/src/Models
```

Criar `packages/Webkul/CustomTheme/src/Models/Lead.php`:

```php
<?php

namespace Webkul\CustomTheme\Models;

use Webkul\Lead\Models\Lead as BaseLead;

class Lead extends BaseLead
{
    /**
     * Adicionar novos campos ao fillable
     */
    protected $fillable = [
        // Campos originais (copiar do BaseLead)
        'title',
        'description',
        'lead_value',
        'status',
        'lost_reason',
        'closed_at',
        'user_id',
        'person_id',
        'lead_source_id',
        'lead_type_id',
        'lead_pipeline_id',
        'lead_pipeline_stage_id',
        'expected_close_date',
        
        // ✅ Novos campos customizados
        'priority',
        'custom_field_1',
        'custom_field_2',
    ];
    
    /**
     * Accessor: Formatar prioridade
     */
    public function getPriorityLabelAttribute()
    {
        $labels = [
            'low' => '🟢 Baixa',
            'medium' => '🟡 Média',
            'high' => '🔴 Alta',
        ];
        
        return $labels[$this->priority] ?? '⚪ Indefinida';
    }
    
    /**
     * Scope: Filtrar por prioridade
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }
    
    /**
     * Novo relacionamento
     */
    public function customRelation()
    {
        // Exemplo de relacionamento customizado
        // return $this->hasMany(CustomModel::class);
    }
}
```

#### Passo 3: Registrar Override no boot() com Concord

Editar `CustomThemeServiceProvider.php`:

```php
public function boot()
{
    // ... código existente ...
    
    // ⭐ Model override no boot() usando CONTRACT!
    $this->app->concord->registerModel(
        \Webkul\Lead\Contracts\Lead::class,      // ← CONTRACT (interface)
        \Webkul\CustomTheme\Models\Lead::class   // ← Seu model
    );
}
```

#### Passo 4: Criar Migration para Novos Campos (Se Necessário)

```bash
# Gerar migration
php artisan package:make-migration add_custom_fields_to_leads Webkul/CustomTheme
```

Editar migration gerada:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('priority')->nullable()->after('status');
            $table->string('custom_field_1')->nullable();
            $table->string('custom_field_2')->nullable();
        });
    }

    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['priority', 'custom_field_1', 'custom_field_2']);
        });
    }
};
```

```bash
# Rodar migration
php artisan migrate
```

#### Passo 5: Testar

```bash
# Limpar cache
php artisan optimize:clear

# Testar no tinker
php artisan tinker
>>> $lead = \Webkul\Lead\Models\Lead::first();
>>> $lead->priority = 'high';
>>> $lead->save();
>>> $lead->priority_label;
# Deve retornar: "🔴 Alta"

>>> get_class($lead);
# Deve retornar: "Webkul\CustomTheme\Models\Lead"
```

### 4.4 View Override

#### Passo 1: Identificar View com Blade Tracer

```
1. Acessar http://localhost:8000/admin/login
2. Passar mouse sobre o formulário de login
3. Anotar: packages/Webkul/Admin/Resources/views/auth/login.blade.php
```

#### Passo 2: Copiar View Mantendo Estrutura

```bash
# Criar estrutura ESPELHANDO a original
mkdir -p packages/Webkul/CustomTheme/Resources/views/admin/auth

# Copiar view original
cp packages/Webkul/Admin/Resources/views/auth/login.blade.php \
   packages/Webkul/CustomTheme/Resources/views/admin/auth/login.blade.php
```

#### Passo 3: Customizar View

Editar `packages/Webkul/CustomTheme/Resources/views/admin/auth/login.blade.php`:

```blade
{{-- Adicionar no topo ou onde fizer sentido --}}
<style>
    /* Customizações CSS inline para teste */
    .login-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
</style>

{{-- Resto do conteúdo original com suas modificações --}}

{{-- Adicionar footer customizado --}}
<footer class="custom-footer">
    <p>© {{ date('Y') }} Minha Empresa - Versão Customizada</p>
</footer>
```

#### Passo 4: Publicar Views

Adicionar no `boot()` do ServiceProvider:

```php
// Publicar views para vendor
$this->publishes([
    __DIR__.'/../../Resources/views/admin' => resource_path('views/vendor/admin'),
], 'customtheme-views');
```

```bash
# Publicar
php artisan vendor:publish --tag=customtheme-views --force

# Limpar cache
php artisan view:clear
php artisan optimize:clear
```

#### Passo 5: Validar com Blade Tracer

```
1. Acessar http://localhost:8000/admin/login
2. Passar mouse sobre o formulário
3. Deve mostrar: resources/views/vendor/admin/auth/login.blade.php
   OU: packages/Webkul/CustomTheme/Resources/views/...
   
✅ Se mostra SEU path = Override funcionando!
❌ Se mostra path original = Override NÃO está funcionando
```

### 4.5 Checkpoint da Fase 4

```
┌─────────────────────────────────────────────────────────────────────┐
│  ☐ CHECKLIST FASE 4                                                 │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  CONTROLLER OVERRIDE:                                               │
│  ☐ Controller estende o original?                                   │
│  ☐ Registrado com $this->app->bind() no register()?                 │
│  ☐ Log aparece quando acessa a página?                              │
│                                                                     │
│  MODEL OVERRIDE:                                                    │
│  ☐ Model estende o original?                                        │
│  ☐ Registrado com concord->registerModel() no boot()?               │
│  ☐ Usou CONTRACT (interface), não classe direta?                    │
│  ☐ Tinker mostra classe do seu package?                             │
│                                                                     │
│  VIEW OVERRIDE:                                                     │
│  ☐ Estrutura de pastas espelha a original?                          │
│  ☐ vendor:publish executado?                                        │
│  ☐ view:clear executado?                                            │
│  ☐ Blade Tracer confirma que override está ativo?                   │
│                                                                     │
│  TODOS MARCADOS? → Avançar para Fase 5                              │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 📡 FASE 5: EVENTOS E LISTENERS (Dia 3)

### 5.1 Objetivo da Fase

```
┌─────────────────────────────────────────────────────────────────────┐
│  AO FINAL DESTA FASE VOCÊ TERÁ:                                     │
│                                                                     │
│  ✅ EventServiceProvider configurado                                │
│  ✅ Listener funcional para lead.create.after                       │
│  ✅ Email sendo enviado após criação de lead                        │
│  ✅ Testado com tinker                                               │
└─────────────────────────────────────────────────────────────────────┘
```

### 5.2 Catálogo de Eventos Disponíveis

```
┌─────────────────────────────────────────────────────────────────────┐
│                    EVENTOS DO KRAYIN                                │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  LEAD                              CONTACT                          │
│  • lead.create.before              • contact.person.create.after    │
│  • lead.create.after               • contact.person.update.after    │
│  • lead.update.before              • contact.organization.create.after │
│  • lead.update.after               • contact.organization.update.after │
│  • lead.delete.before                                               │
│  • lead.delete.after               QUOTE                            │
│                                    • quote.create.after             │
│  ACTIVITY                          • quote.update.after             │
│  • activity.create.after           • quote.delete.after             │
│  • activity.update.after                                            │
│  • activity.delete.after           USER                             │
│                                    • user.create.after              │
│  PRODUCT                           • user.update.after              │
│  • product.create.after            • user.delete.after              │
│  • product.update.after                                             │
│  • product.delete.after            EMAIL                            │
│                                    • email.create.after             │
│                                    • email.update.after             │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 5.3 Criar EventServiceProvider

```bash
mkdir -p packages/Webkul/CustomTheme/src/Providers
```

Criar `packages/Webkul/CustomTheme/src/Providers/EventServiceProvider.php`:

```php
<?php

namespace Webkul\CustomTheme\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Lead events
        Event::listen(
            'lead.create.after',
            'Webkul\CustomTheme\Listeners\LeadListener@handleCreate'
        );
        
        Event::listen(
            'lead.update.after',
            'Webkul\CustomTheme\Listeners\LeadListener@handleUpdate'
        );
        
        // Contact events
        Event::listen(
            'contact.person.create.after',
            'Webkul\CustomTheme\Listeners\ContactListener@handleCreate'
        );
    }
}
```

### 5.4 Registrar EventServiceProvider

Editar `CustomThemeServiceProvider.php`:

```php
public function boot()
{
    // ... código existente ...
    
    // ⭐ Registrar EventServiceProvider
    $this->app->register(EventServiceProvider::class);
}
```

### 5.5 Criar Listener

```bash
mkdir -p packages/Webkul/CustomTheme/src/Listeners
```

Criar `packages/Webkul/CustomTheme/src/Listeners/LeadListener.php`:

```php
<?php

namespace Webkul\CustomTheme\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Webkul\CustomTheme\Mail\LeadCreatedNotification;

class LeadListener
{
    /**
     * Handle lead.create.after event
     */
    public function handleCreate($lead)
    {
        Log::info('CustomTheme: Lead criado', [
            'id' => $lead->id,
            'title' => $lead->title,
            'value' => $lead->lead_value,
        ]);
        
        // Enviar email de notificação
        $this->sendNotificationEmail($lead, 'created');
        
        // Outras ações...
        // - Integrar com sistema externo
        // - Criar atividade automática
        // - Atualizar cache
    }
    
    /**
     * Handle lead.update.after event
     */
    public function handleUpdate($lead)
    {
        Log::info('CustomTheme: Lead atualizado', [
            'id' => $lead->id,
            'title' => $lead->title,
        ]);
        
        // Verificar se status mudou
        if ($lead->isDirty('status')) {
            $this->sendNotificationEmail($lead, 'status_changed');
        }
    }
    
    /**
     * Enviar email de notificação
     */
    protected function sendNotificationEmail($lead, $type)
    {
        try {
            $adminEmail = config('mail.admin_notification_email', 'admin@example.com');
            
            Mail::to($adminEmail)
                ->queue(new LeadCreatedNotification($lead, $type));
                
            Log::info('CustomTheme: Email enfileirado', [
                'lead_id' => $lead->id,
                'type' => $type,
            ]);
        } catch (\Exception $e) {
            Log::error('CustomTheme: Erro ao enviar email', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
```

### 5.6 Criar Mailable

```bash
mkdir -p packages/Webkul/CustomTheme/src/Mail
```

Criar `packages/Webkul/CustomTheme/src/Mail/LeadCreatedNotification.php`:

```php
<?php

namespace Webkul\CustomTheme\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeadCreatedNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    
    public $lead;
    public $type;
    
    public function __construct($lead, $type)
    {
        $this->lead = $lead;
        $this->type = $type;
    }
    
    public function build()
    {
        $subject = $this->type === 'created' 
            ? "Novo Lead: {$this->lead->title}"
            : "Lead Atualizado: {$this->lead->title}";
            
        return $this->subject($subject)
                    ->view('customtheme::emails.lead-notification');
    }
}
```

### 5.7 Criar View do Email

```bash
mkdir -p packages/Webkul/CustomTheme/Resources/views/emails
```

Criar `packages/Webkul/CustomTheme/Resources/views/emails/lead-notification.blade.php`:

```blade
<!DOCTYPE html>
<html>
<head>
    <title>Notificação de Lead</title>
</head>
<body>
    <h1>{{ $type === 'created' ? 'Novo Lead Criado' : 'Lead Atualizado' }}</h1>
    
    <table>
        <tr>
            <td><strong>ID:</strong></td>
            <td>{{ $lead->id }}</td>
        </tr>
        <tr>
            <td><strong>Título:</strong></td>
            <td>{{ $lead->title }}</td>
        </tr>
        <tr>
            <td><strong>Valor:</strong></td>
            <td>R$ {{ number_format($lead->lead_value, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>Status:</strong></td>
            <td>{{ $lead->status }}</td>
        </tr>
    </table>
    
    <p>
        <a href="{{ route('admin.leads.view', $lead->id) }}">
            Ver Lead no Sistema
        </a>
    </p>
</body>
</html>
```

### 5.8 Testar Listener

```bash
# Limpar cache
php artisan optimize:clear

# Testar com tinker
php artisan tinker

# Disparar evento manualmente
>>> $lead = \Webkul\Lead\Models\Lead::first();
>>> event('lead.create.after', $lead);

# Verificar log
>>> exit
tail -f storage/logs/laravel.log | grep CustomTheme
# Deve mostrar: "CustomTheme: Lead criado"
```

### 5.9 View Render Event (Injeção de HTML)

Para injetar pequeno HTML sem duplicar view inteira:

```php
// No EventServiceProvider

Event::listen('admin.leads.view.informations.after', function ($viewRenderEventManager) {
    $viewRenderEventManager->addTemplate('customtheme::partials.lead-extra-info');
});
```

Criar partial:

```bash
mkdir -p packages/Webkul/CustomTheme/Resources/views/partials
```

```blade
{{-- packages/Webkul/CustomTheme/Resources/views/partials/lead-extra-info.blade.php --}}
<div class="custom-extra-info">
    <h4>Informações Customizadas</h4>
    <p>Este conteúdo foi injetado via View Render Event!</p>
</div>
```

### 5.10 Checkpoint da Fase 5

```
┌─────────────────────────────────────────────────────────────────────┐
│  ☐ CHECKLIST FASE 5                                                 │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ☐ EventServiceProvider criado em src/Providers/?                   │
│  ☐ EventServiceProvider registrado no boot() do ServiceProvider?    │
│  ☐ Listener criado em src/Listeners/?                               │
│  ☐ Método do listener recebe parâmetro correto?                     │
│  ☐ Nome do evento está correto (lead.create.after)?                 │
│  ☐ Testou com tinker e log apareceu?                                │
│  ☐ Mailable implementa ShouldQueue?                                 │
│  ☐ View do email existe e renderiza?                                │
│                                                                     │
│  TODOS MARCADOS? → Avançar para Fase 6                              │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 🎨 FASE 6: CUSTOMIZAÇÃO VISUAL (Dia 3-4)

### 6.1 Objetivo da Fase

```
┌─────────────────────────────────────────────────────────────────────┐
│  AO FINAL DESTA FASE VOCÊ TERÁ:                                     │
│                                                                     │
│  ✅ Cores customizadas via CSS Variables                            │
│  ✅ Logo e favicon substituídos                                     │
│  ✅ Traduções pt_BR configuradas                                    │
│  ✅ Assets publicados corretamente                                  │
└─────────────────────────────────────────────────────────────────────┘
```

### 6.2 Estrutura de Assets

```bash
# Criar estrutura
mkdir -p packages/Webkul/CustomTheme/Resources/assets/css
mkdir -p packages/Webkul/CustomTheme/Resources/assets/js
mkdir -p packages/Webkul/CustomTheme/Resources/assets/images
```

### 6.3 CSS Variables (Cores)

Criar `packages/Webkul/CustomTheme/Resources/assets/css/theme.css`:

```css
/**
 * CustomTheme - Variáveis CSS
 * Sobrescreve as cores padrão do Krayin
 */

:root {
    /* ================================
       CORES PRIMÁRIAS
       ================================ */
    --primary-color: #1E40AF;           /* Azul principal */
    --primary-hover: #1E3A8A;           /* Hover do primário */
    --secondary-color: #3B82F6;         /* Azul secundário */
    
    /* ================================
       CORES DE STATUS
       ================================ */
    --success-color: #10B981;           /* Verde sucesso */
    --danger-color: #EF4444;            /* Vermelho erro */
    --warning-color: #F59E0B;           /* Amarelo aviso */
    --info-color: #3B82F6;              /* Azul info */
    
    /* ================================
       CORES NEUTRAS
       ================================ */
    --text-primary: #111827;            /* Texto principal */
    --text-secondary: #4B5563;          /* Texto secundário */
    --text-muted: #9CA3AF;              /* Texto desabilitado */
    
    --bg-primary: #FFFFFF;              /* Fundo principal */
    --bg-secondary: #F9FAFB;            /* Fundo secundário */
    --bg-tertiary: #F3F4F6;             /* Fundo terciário */
    
    --border-color: #E5E7EB;            /* Cor das bordas */
    
    /* ================================
       ESPAÇAMENTOS
       ================================ */
    --spacing-xs: 0.25rem;              /* 4px */
    --spacing-sm: 0.5rem;               /* 8px */
    --spacing-md: 1rem;                 /* 16px */
    --spacing-lg: 1.5rem;               /* 24px */
    --spacing-xl: 2rem;                 /* 32px */
    
    /* ================================
       BORDAS
       ================================ */
    --border-radius-sm: 0.25rem;        /* 4px */
    --border-radius-md: 0.375rem;       /* 6px */
    --border-radius-lg: 0.5rem;         /* 8px */
    --border-radius-full: 9999px;       /* Circular */
    
    /* ================================
       SOMBRAS
       ================================ */
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    
    /* ================================
       TIPOGRAFIA
       ================================ */
    --font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    --font-size-xs: 0.75rem;            /* 12px */
    --font-size-sm: 0.875rem;           /* 14px */
    --font-size-md: 1rem;               /* 16px */
    --font-size-lg: 1.125rem;           /* 18px */
    --font-size-xl: 1.25rem;            /* 20px */
}

/* ================================
   CUSTOMIZAÇÕES ESPECÍFICAS
   ================================ */

/* Sidebar */
.sidebar {
    background-color: var(--primary-color);
}

/* Header */
.header {
    background-color: var(--bg-primary);
    border-bottom: 1px solid var(--border-color);
}

/* Botões primários */
.btn-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.btn-primary:hover {
    background-color: var(--primary-hover);
    border-color: var(--primary-hover);
}

/* Cards */
.card {
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-sm);
}

/* Login page */
.login-container {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
}
```

### 6.4 Imagens (Logo e Favicon)

```bash
# Colocar suas imagens em:
packages/Webkul/CustomTheme/Resources/assets/images/

# Arquivos necessários:
# - logo.png (200x60px) - Logo principal
# - logo-icon.png (32x32px) - Logo pequeno
# - favicon.ico (16x16, 32x32px) - Favicon
# - favicon-32x32.png (32x32px) - Favicon PNG
# - apple-touch-icon.png (180x180px) - iOS
```

### 6.5 Traduções pt_BR

```bash
mkdir -p packages/Webkul/CustomTheme/Resources/lang/pt_BR
```

Criar `packages/Webkul/CustomTheme/Resources/lang/pt_BR/app.php`:

```php
<?php

return [
    'common' => [
        'save' => 'Salvar',
        'cancel' => 'Cancelar',
        'edit' => 'Editar',
        'delete' => 'Excluir',
        'search' => 'Buscar',
        'filter' => 'Filtrar',
        'export' => 'Exportar',
        'import' => 'Importar',
        'yes' => 'Sim',
        'no' => 'Não',
        'confirm' => 'Confirmar',
        'loading' => 'Carregando...',
        'no_records' => 'Nenhum registro encontrado',
    ],
    
    'login' => [
        'title' => 'Entrar no Sistema',
        'email' => 'E-mail',
        'password' => 'Senha',
        'remember' => 'Lembrar de mim',
        'forgot' => 'Esqueceu a senha?',
        'submit' => 'Entrar',
    ],
    
    'leads' => [
        'title' => 'Leads',
        'create' => 'Novo Lead',
        'edit' => 'Editar Lead',
        'view' => 'Visualizar Lead',
        'delete' => 'Excluir Lead',
        'list' => 'Lista de Leads',
        'value' => 'Valor',
        'status' => 'Status',
        'source' => 'Origem',
        'pipeline' => 'Pipeline',
        'stage' => 'Etapa',
    ],
    
    'contacts' => [
        'title' => 'Contatos',
        'persons' => 'Pessoas',
        'organizations' => 'Empresas',
        'create_person' => 'Nova Pessoa',
        'create_organization' => 'Nova Empresa',
    ],
    
    'dashboard' => [
        'title' => 'Painel',
        'welcome' => 'Bem-vindo ao CRM',
        'total_leads' => 'Total de Leads',
        'total_revenue' => 'Receita Total',
        'conversion_rate' => 'Taxa de Conversão',
    ],
    
    'messages' => [
        'created' => ':entity criado(a) com sucesso!',
        'updated' => ':entity atualizado(a) com sucesso!',
        'deleted' => ':entity excluído(a) com sucesso!',
        'error' => 'Ocorreu um erro. Tente novamente.',
        'confirm_delete' => 'Tem certeza que deseja excluir?',
    ],
];
```

### 6.6 Configurar ServiceProvider para Assets

Editar `CustomThemeServiceProvider.php`:

```php
public function boot()
{
    // ... código existente ...
    
    // Traduções
    $this->loadTranslationsFrom(
        __DIR__.'/../../Resources/lang',
        'customtheme'
    );
    
    // Assets - CSS, JS, Imagens
    $this->publishes([
        __DIR__.'/../../Resources/assets' => public_path('vendor/customtheme'),
    ], 'customtheme-assets');
    
    // Views
    $this->publishes([
        __DIR__.'/../../Resources/views/admin' => resource_path('views/vendor/admin'),
    ], 'customtheme-views');
}
```

### 6.7 Publicar Assets

```bash
# Publicar assets
php artisan vendor:publish --tag=customtheme-assets --force

# Verificar
ls -la public/vendor/customtheme/
# Deve mostrar: css/, js/, images/

# Publicar views
php artisan vendor:publish --tag=customtheme-views --force

# Limpar cache
php artisan optimize:clear
```

### 6.8 Referenciar Assets nas Views

Para usar os assets customizados nas suas views:

```blade
{{-- CSS --}}
<link rel="stylesheet" href="{{ asset('vendor/customtheme/css/theme.css') }}">

{{-- Logo --}}
<img src="{{ asset('vendor/customtheme/images/logo.png') }}" alt="Logo">

{{-- Favicon --}}
<link rel="icon" href="{{ asset('vendor/customtheme/images/favicon.ico') }}">

{{-- Traduções --}}
<h1>{{ trans('customtheme::app.login.title') }}</h1>
<button>{{ __('customtheme::app.common.save') }}</button>
```

### 6.9 Checkpoint da Fase 6

```
┌─────────────────────────────────────────────────────────────────────┐
│  ☐ CHECKLIST FASE 6                                                 │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  CSS:                                                               │
│  ☐ theme.css criado com CSS Variables?                              │
│  ☐ Cores principais definidas?                                      │
│                                                                     │
│  IMAGENS:                                                           │
│  ☐ Logo no tamanho correto (200x60px)?                              │
│  ☐ Favicon existe?                                                  │
│                                                                     │
│  TRADUÇÕES:                                                         │
│  ☐ Arquivo pt_BR/app.php criado?                                    │
│  ☐ loadTranslationsFrom() no ServiceProvider?                       │
│                                                                     │
│  PUBLICAÇÃO:                                                        │
│  ☐ vendor:publish executado?                                        │
│  ☐ Assets aparecem em public/vendor/customtheme/?                   │
│  ☐ Views aparecem em resources/views/vendor/?                       │
│                                                                     │
│  TODOS MARCADOS? → Avançar para Fase 7                              │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 🐳 FASE 7: DOCKER E DEPLOY (Dia 4-5)

### 7.1 Objetivo da Fase

```
┌─────────────────────────────────────────────────────────────────────┐
│  AO FINAL DESTA FASE VOCÊ TERÁ:                                     │
│                                                                     │
│  ✅ Dockerfile configurado para produção                            │
│  ✅ Build de imagem funcional                                       │
│  ✅ Deploy em staging/produção                                      │
│  ✅ Validação pós-deploy                                            │
└─────────────────────────────────────────────────────────────────────┘
```

### 7.2 Dockerfile Multi-Stage

Criar `Dockerfile`:

```dockerfile
# ============================================
# STAGE 1: Builder
# ============================================
FROM php:8.2-fpm-alpine AS builder

# Instalar dependências de build
RUN apk add --no-cache \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    icu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        zip \
        gd \
        intl \
        bcmath \
        opcache

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar código
WORKDIR /app
COPY . .

# ⚠️ IMPORTANTE: config/modules.php deve incluir seu package!
# ⚠️ IMPORTANTE: composer.json deve ter o path repository!

# Instalar dependências (sem dev!)
RUN composer install \
    --no-dev \
    --no-interaction \
    --optimize-autoloader \
    --prefer-dist

# Publicar assets de TODOS os packages
RUN php artisan vendor:publish --force --all

# Limpar caches
RUN php artisan optimize:clear

# ============================================
# STAGE 2: Runtime
# ============================================
FROM php:8.2-fpm-alpine AS runtime

# Instalar extensões runtime
RUN apk add --no-cache \
    libzip \
    libpng \
    libjpeg-turbo \
    freetype \
    icu-libs \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        zip \
        gd \
        intl \
        bcmath \
        opcache

# Configurar PHP para produção
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Configurar OPcache
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=20000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini

# Copiar aplicação do builder
WORKDIR /app
COPY --from=builder /app /app

# Ajustar permissões
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Healthcheck
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD php artisan --version || exit 1

# Entrypoint
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
```

### 7.3 Entrypoint Script

Criar `docker-entrypoint.sh`:

```bash
#!/bin/sh
set -e

echo "======================================"
echo "  Krayin CRM - Inicializando..."
echo "======================================"

# Aguardar MySQL
echo "Aguardando MySQL..."
while ! nc -z ${DB_HOST:-mysql} ${DB_PORT:-3306}; do
    sleep 1
done
echo "MySQL disponível!"

# Aguardar Redis (se configurado)
if [ ! -z "$REDIS_HOST" ]; then
    echo "Aguardando Redis..."
    while ! nc -z ${REDIS_HOST} ${REDIS_PORT:-6379}; do
        sleep 1
    done
    echo "Redis disponível!"
fi

# Gerar APP_KEY se não existir
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "Gerando APP_KEY..."
    php artisan key:generate --force
fi

# Rodar migrations (se habilitado)
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Rodando migrations..."
    php artisan migrate --force
fi

# Publicar assets
echo "Publicando assets..."
php artisan vendor:publish --force --all

# Limpar caches
echo "Limpando caches..."
php artisan optimize:clear

# Gerar caches de produção
if [ "$APP_ENV" = "production" ]; then
    echo "Gerando caches de produção..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

# Ajustar permissões
echo "Ajustando permissões..."
chown -R www-data:www-data /app/storage /app/bootstrap/cache

echo "======================================"
echo "  Krayin CRM - Pronto!"
echo "======================================"

exec "$@"
```

### 7.4 docker-compose.yml (Desenvolvimento)

```yaml
version: '3.8'

services:
  krayin:
    build:
      context: .
      target: runtime
    ports:
      - "9000:9000"
    environment:
      - APP_ENV=local
      - APP_DEBUG=true
      - DB_HOST=mysql
      - DB_DATABASE=krayin
      - DB_USERNAME=krayin
      - DB_PASSWORD=secret
      - REDIS_HOST=redis
      - RUN_MIGRATIONS=true
    volumes:
      # Live reload para desenvolvimento
      - ./packages:/app/packages
      - ./resources:/app/resources
      - ./storage:/app/storage
    depends_on:
      - mysql
      - redis
    networks:
      - krayin-network

  nginx:
    image: nginx:alpine
    ports:
      - "8000:80"
    volumes:
      - ./nginx.conf:/etc/nginx/conf.d/default.conf
      - ./public:/app/public
    depends_on:
      - krayin
    networks:
      - krayin-network

  mysql:
    image: mysql:8.0
    environment:
      - MYSQL_ROOT_PASSWORD=root
      - MYSQL_DATABASE=krayin
      - MYSQL_USER=krayin
      - MYSQL_PASSWORD=secret
    volumes:
      - mysql-data:/var/lib/mysql
    networks:
      - krayin-network

  redis:
    image: redis:alpine
    networks:
      - krayin-network

networks:
  krayin-network:
    driver: bridge

volumes:
  mysql-data:
```

### 7.5 Script de Deploy

Criar `deploy.sh`:

```bash
#!/bin/bash
set -e

# Configurações
VERSION=${1:-$(date +%Y%m%d%H%M%S)}
REGISTRY="seu-registry.com"
IMAGE_NAME="krayin-crm"
SERVICE_NAME="krayin_krayin"

echo "======================================"
echo "  Deploy Krayin CRM v${VERSION}"
echo "======================================"

# 1. Build
echo "📦 Buildando imagem..."
docker build -t ${IMAGE_NAME}:${VERSION} .
docker tag ${IMAGE_NAME}:${VERSION} ${REGISTRY}/${IMAGE_NAME}:${VERSION}
docker tag ${IMAGE_NAME}:${VERSION} ${REGISTRY}/${IMAGE_NAME}:latest

# 2. Push
echo "📤 Enviando para registry..."
docker push ${REGISTRY}/${IMAGE_NAME}:${VERSION}
docker push ${REGISTRY}/${IMAGE_NAME}:latest

# 3. Deploy
echo "🚀 Atualizando serviço..."
docker service update \
    --image ${REGISTRY}/${IMAGE_NAME}:${VERSION} \
    --force \
    ${SERVICE_NAME}

# 4. Verificar
echo "✅ Verificando deploy..."
sleep 10
docker service ps ${SERVICE_NAME}

echo "======================================"
echo "  Deploy concluído! v${VERSION}"
echo "======================================"
```

### 7.6 Checklist Pré-Build

```bash
# ⚠️ ANTES de fazer build, verificar:

# 1. config/modules.php inclui seu package?
cat config/modules.php | grep -i custom
# Deve mostrar: 'CustomTheme'

# 2. composer.json tem path repository?
cat composer.json | grep -A3 repositories
# Deve mostrar: "url": "packages/Webkul/*"

# 3. Assets existem?
ls -la packages/Webkul/CustomTheme/Resources/assets/
# Deve mostrar: css/, images/

# 4. Traduções existem?
ls -la packages/Webkul/CustomTheme/Resources/lang/pt_BR/
# Deve mostrar: app.php
```

### 7.7 Validação Pós-Deploy

```bash
# Checklist de validação
echo "=== VALIDAÇÃO PÓS-DEPLOY ==="

# 1. Aplicação responde?
curl -s -o /dev/null -w "%{http_code}" http://seu-dominio.com/admin/login
# Deve retornar: 200

# 2. Login funciona?
# Acessar manualmente e testar login

# 3. Assets carregam?
curl -s -o /dev/null -w "%{http_code}" http://seu-dominio.com/vendor/customtheme/css/theme.css
# Deve retornar: 200

# 4. Customizações visíveis?
# Verificar logo, cores, traduções

# 5. Logs limpos?
docker service logs krayin_krayin --tail 50
# Não deve ter erros

# 6. Performance ok?
# Verificar tempo de resposta das páginas
```

### 7.8 Checkpoint da Fase 7

```
┌─────────────────────────────────────────────────────────────────────┐
│  ☐ CHECKLIST FASE 7                                                 │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  PRÉ-BUILD:                                                         │
│  ☐ config/modules.php inclui CustomTheme?                           │
│  ☐ composer.json tem path repository?                               │
│  ☐ Dockerfile executa vendor:publish?                               │
│  ☐ Dockerfile executa optimize:clear?                               │
│                                                                     │
│  BUILD:                                                             │
│  ☐ docker build completou sem erros?                                │
│  ☐ Imagem foi enviada para registry?                                │
│                                                                     │
│  DEPLOY:                                                            │
│  ☐ Serviço atualizado com nova imagem?                              │
│  ☐ Containers estão healthy?                                        │
│                                                                     │
│  VALIDAÇÃO:                                                         │
│  ☐ Aplicação responde (HTTP 200)?                                   │
│  ☐ Login funciona?                                                  │
│  ☐ Assets carregam?                                                 │
│  ☐ Customizações visíveis?                                          │
│  ☐ Logs sem erros?                                                  │
│                                                                     │
│  TODOS MARCADOS? → Avançar para Fase 8                              │
└─────────────────────────────────────────────────────────────────────┘
```

---

## ✅ FASE 8: VALIDAÇÃO FINAL (Dia 5)

### 8.1 Objetivo da Fase

```
┌─────────────────────────────────────────────────────────────────────┐
│  AO FINAL DESTA FASE VOCÊ TERÁ:                                     │
│                                                                     │
│  ✅ Validação completa do conhecimento                              │
│  ✅ Identificação de pontos a melhorar                              │
│  ✅ Certificação de conclusão do onboarding                         │
└─────────────────────────────────────────────────────────────────────┘
```

### 8.2 Teste de Conhecimento

Responda sem consultar material (depois confira):

```
┌─────────────────────────────────────────────────────────────────────┐
│  TESTE DE CONHECIMENTO                                              │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  1. Onde fica o registro dos packages?                              │
│     Resposta: ________________________________________              │
│                                                                     │
│  2. Controller override vai em register() ou boot()?                │
│     Resposta: ________________________________________              │
│                                                                     │
│  3. Model override usa classe direta ou Contract?                   │
│     Resposta: ________________________________________              │
│                                                                     │
│  4. Model override vai em register() ou boot()?                     │
│     Resposta: ________________________________________              │
│                                                                     │
│  5. Qual ferramenta localiza views?                                 │
│     Resposta: ________________________________________              │
│                                                                     │
│  6. Qual comando cria um package?                                   │
│     Resposta: ________________________________________              │
│                                                                     │
│  7. Qual comando publica assets?                                    │
│     Resposta: ________________________________________              │
│                                                                     │
│  8. Qual comando limpa todos os caches?                             │
│     Resposta: ________________________________________              │
│                                                                     │
│  9. Onde assets publicados ficam?                                   │
│     Resposta: ________________________________________              │
│                                                                     │
│  10. Por que package custom deve ser o último no modules.php?       │
│      Resposta: ________________________________________             │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

#### Gabarito

```
1. config/modules.php
2. register()
3. Contract (interface)
4. boot()
5. Blade Tracer
6. php artisan package:make Webkul/NomePackage
7. php artisan vendor:publish --tag=nome-assets --force
8. php artisan optimize:clear
9. public/vendor/nome-package/
10. Quem carrega por último ganha os overrides
```

### 8.3 Exercício Prático Final

```
DESAFIO: Criar um package completo "CustomDashboard" que:

1. Adiciona um card no dashboard mostrando "Leads de Alta Prioridade"
2. Escuta o evento lead.create.after e loga quando lead é criado
3. Tem CSS customizado com cores da empresa
4. Tem tradução pt_BR

Tempo estimado: 2-3 horas

Critérios de avaliação:
☐ Package criado com Package Generator
☐ Estrutura segue padrão canônico
☐ ServiceProvider tem register() e boot() corretos
☐ EventServiceProvider registra listener
☐ Listener funciona (testar com tinker)
☐ Assets publicados e funcionando
☐ Traduções funcionando
☐ Validado com Blade Tracer
☐ Sem erros no log
```

### 8.4 Checklist Completo do Onboarding

```
┌─────────────────────────────────────────────────────────────────────┐
│  ☐ CHECKLIST FINAL - CONCLUSÃO DO ONBOARDING                        │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  AMBIENTE:                                                          │
│  ☐ Krayin rodando localmente                                        │
│  ☐ Blade Tracer instalado e funcionando                             │
│  ☐ Package Generator instalado e funcionando                        │
│  ☐ Debug Bar instalado e funcionando                                │
│                                                                     │
│  CONHECIMENTO TEÓRICO:                                              │
│  ☐ Entende arquitetura package-based                                │
│  ☐ Sabe diferença entre register() e boot()                         │
│  ☐ Sabe quando usar cada tipo de override                           │
│  ☐ Conhece o sistema de eventos                                     │
│                                                                     │
│  HABILIDADES PRÁTICAS:                                              │
│  ☐ Criou package do zero                                            │
│  ☐ Implementou controller override                                  │
│  ☐ Implementou model override                                       │
│  ☐ Implementou view override                                        │
│  ☐ Criou listener funcional                                         │
│  ☐ Customizou CSS com variables                                     │
│  ☐ Configurou traduções pt_BR                                       │
│  ☐ Publicou assets corretamente                                     │
│                                                                     │
│  DOCKER/DEPLOY:                                                     │
│  ☐ Entende Dockerfile multi-stage                                   │
│  ☐ Sabe fazer build de imagem                                       │
│  ☐ Sabe fazer deploy                                                │
│  ☐ Sabe validar pós-deploy                                          │
│                                                                     │
│  TESTE FINAL:                                                       │
│  ☐ Acertou 8+ questões do teste                                     │
│  ☐ Completou exercício prático final                                │
│                                                                     │
│  ═══════════════════════════════════════════════════════════════   │
│                                                                     │
│  TODOS OS ITENS MARCADOS?                                           │
│                                                                     │
│  ✅ PARABÉNS! Onboarding concluído com sucesso!                     │
│     Você está pronto para desenvolver no Krayin CRM.                │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 8.5 Próximos Passos

```
┌─────────────────────────────────────────────────────────────────────┐
│  APÓS O ONBOARDING                                                  │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  📚 DOCUMENTOS DE REFERÊNCIA:                                       │
│     • ANATOMIA_GERAL_KRAYIN_CRM.md → Consulta constante             │
│     • FERRAMENTAS_DEV_KRAYIN.md → Uso das ferramentas              │
│     • CHECKLIST_VALIDACAO_DEV.md → Code reviews                    │
│                                                                     │
│  🎯 PRIMEIRAS TAREFAS RECOMENDADAS:                                 │
│     1. Customizar tela de login com identidade visual               │
│     2. Adicionar campos customizados ao Lead                        │
│     3. Criar listener de integração com sistema externo             │
│     4. Implementar tradução completa pt_BR                          │
│                                                                     │
│  💡 DICAS PARA O DIA-A-DIA:                                         │
│     • Sempre use Blade Tracer antes de criar override               │
│     • Sempre use Package Generator para novos artefatos             │
│     • Sempre valide com Debug Bar antes de deploy                   │
│     • NUNCA edite packages/Webkul/* diretamente                     │
│     • Sempre teste com tinker antes de confiar no evento            │
│                                                                     │
│  🆘 SE TIVER PROBLEMAS:                                             │
│     1. Verificar logs: tail -f storage/logs/laravel.log             │
│     2. Limpar cache: php artisan optimize:clear                     │
│     3. Consultar seção Troubleshooting da Anatomia                  │
│     4. Usar Checklist de Validação para diagnosticar                │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 📋 REFERÊNCIA RÁPIDA

### Comandos Essenciais

```bash
# ═══════════════════════════════════════════════════════════════════
# PACKAGE GENERATOR
# ═══════════════════════════════════════════════════════════════════
php artisan package:make Webkul/NomePackage          # Criar package
php artisan package:make-model Nome Webkul/Package   # Criar model
php artisan package:make-controller Nome Webkul/Package  # Criar controller
php artisan package:make-repository Nome Webkul/Package  # Criar repository

# ═══════════════════════════════════════════════════════════════════
# CACHE
# ═══════════════════════════════════════════════════════════════════
php artisan optimize:clear      # Limpar TODOS os caches
php artisan view:clear          # Limpar cache de views
php artisan config:clear        # Limpar cache de config
php artisan route:clear         # Limpar cache de rotas

# ═══════════════════════════════════════════════════════════════════
# PUBLICAÇÃO
# ═══════════════════════════════════════════════════════════════════
php artisan vendor:publish --tag=nome-assets --force
php artisan vendor:publish --tag=nome-views --force
php artisan vendor:publish --force --all    # Tudo

# ═══════════════════════════════════════════════════════════════════
# DEBUG
# ═══════════════════════════════════════════════════════════════════
php artisan tinker                          # Console interativo
tail -f storage/logs/laravel.log            # Ver logs
php artisan route:list | grep nome          # Ver rotas
cat config/modules.php | grep -i nome       # Ver packages registrados

# ═══════════════════════════════════════════════════════════════════
# DOCKER
# ═══════════════════════════════════════════════════════════════════
docker-compose up -d                        # Subir containers
docker-compose exec app bash                # Entrar no container
docker build -t nome:tag .                  # Build imagem
docker service update --image nome:tag svc  # Atualizar serviço
```

### Arquivos Críticos

| Arquivo | Propósito |
|---------|-----------|
| `config/modules.php` | Registro de packages |
| `composer.json` | Autoload PSR-4, repositories |
| `{Package}ServiceProvider.php` | Ponto de entrada do package |
| `EventServiceProvider.php` | Registro de listeners |
| `storage/logs/laravel.log` | Logs de erro |

### Estrutura Canônica

```
packages/Webkul/{NomePackage}/
├── src/
│   ├── Providers/
│   │   ├── {NomePackage}ServiceProvider.php  ← Obrigatório
│   │   └── EventServiceProvider.php
│   ├── Http/Controllers/
│   ├── Models/
│   ├── Repositories/
│   ├── Listeners/
│   ├── Mail/
│   ├── Events/
│   ├── Routes/
│   └── Config/
├── Resources/
│   ├── views/
│   ├── lang/
│   └── assets/
├── Database/
│   └── Migrations/
└── composer.json                             ← Obrigatório
```

---

## 🔧 TROUBLESHOOTING COMUM

| Problema | Causa Provável | Solução |
|----------|----------------|---------|
| Package não carrega | Não está em modules.php | Adicionar ao final |
| View não aparece | Cache antigo | `php artisan view:clear` |
| Asset 404 | Não publicado | `vendor:publish --force` |
| Model override não funciona | Usou classe, não Contract | Usar interface em registerModel |
| Controller override não funciona | Registrado no boot() | Mover para register() |
| Listener não dispara | Não registrado | Verificar EventServiceProvider |
| CSS não atualiza | Cache browser | Ctrl+F5 ou optimize:clear |
| Erro de namespace | Autoload desatualizado | `composer dump-autoload` |

---

**Versão:** 1.0.0  
**Data:** Dezembro 2025  
**Duração:** 3-5 dias  

---

*"Do zero ao deploy, um passo de cada vez."*
