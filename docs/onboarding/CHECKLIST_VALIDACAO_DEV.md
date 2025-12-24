# 🎯 CHECKLIST DE VALIDAÇÃO - DESENVOLVIMENTO KRAYIN
## Perguntas para Verificar Conformidade com a Documentação

**Objetivo:** Validar se o desenvolvedor está seguindo corretamente os padrões estabelecidos na Anatomia Geral do Krayin e nas Ferramentas de Desenvolvimento.

**Uso:** Code review, onboarding, validação pré-deploy, auditoria técnica.

---

## 📋 ÍNDICE

1. [Estrutura de Package](#1-estrutura-de-package)
2. [Service Provider](#2-service-provider)
3. [Overrides](#3-overrides)
4. [Eventos e Listeners](#4-eventos-e-listeners)
5. [Views e Assets](#5-views-e-assets)
6. [Docker e Deploy](#6-docker-e-deploy)
7. [Ferramentas de Desenvolvimento](#7-ferramentas-de-desenvolvimento)
8. [Boas Práticas](#8-boas-práticas)
9. [Troubleshooting](#9-troubleshooting)

---

## 1. ESTRUTURA DE PACKAGE

### Perguntas Fundamentais

```
┌─────────────────────────────────────────────────────────────────────┐
│  1.1  Qual nome você deu ao package?                                │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: PascalCase (ex: CustomTheme, CustomWorkflow)          │
│  ❌ ERRADO: customtheme, custom_theme, custom-theme                 │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  1.2  Onde está localizado o package?                               │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: packages/Webkul/{NomePackage}/                        │
│  ❌ ERRADO: app/Packages/, src/, modules/                           │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  1.3  Qual namespace você está usando?                              │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: Webkul\{NomePackage}\                                 │
│  ❌ ERRADO: App\Packages\, Custom\, Modules\                        │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  1.4  Como você criou a estrutura do package?                       │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: php artisan package:make Webkul/NomePackage           │
│  ⚠️ ACEITÁVEL: Manualmente seguindo estrutura canônica             │
│  ❌ ERRADO: Copiei de outro projeto / Criei do zero sem referência │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  1.5  Seu package tem composer.json próprio?                        │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: Sim, com name, autoload PSR-4, extra.laravel          │
│  ❌ ERRADO: Não / Só tem autoload no composer.json raiz             │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  1.6  Onde você registrou o package?                                │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: config/modules.php (por ÚLTIMO na lista)              │
│  ❌ ERRADO: config/app.php / Não registrei / No meio da lista       │
└─────────────────────────────────────────────────────────────────────┘
```

### Pergunta de Verificação Estrutural

```
┌─────────────────────────────────────────────────────────────────────┐
│  1.7  Mostre a estrutura de pastas do seu package.                  │
│       Ela segue este padrão?                                        │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  packages/Webkul/{NomePackage}/                                     │
│  ├── src/                                                           │
│  │   ├── Providers/           ← ServiceProvider obrigatório         │
│  │   ├── Http/Controllers/    ← Se tiver controllers                │
│  │   ├── Models/              ← Se tiver models                     │
│  │   ├── Repositories/        ← Se tiver repositories               │
│  │   ├── Listeners/           ← Se tiver listeners                  │
│  │   ├── Events/              ← Se tiver eventos próprios           │
│  │   ├── Routes/              ← Se tiver rotas                      │
│  │   └── Config/              ← Se tiver configs                    │
│  ├── Resources/                                                     │
│  │   ├── views/               ← Se tiver views                      │
│  │   ├── lang/                ← Se tiver traduções                  │
│  │   └── assets/              ← Se tiver CSS/JS/imagens             │
│  ├── Database/                                                      │
│  │   ├── Migrations/          ← Se tiver migrations                 │
│  │   └── Seeders/             ← Se tiver seeders                    │
│  └── composer.json            ← Obrigatório                         │
│                                                                     │
│  ✅ ESPERADO: Estrutura igual ou subset desta                       │
│  ❌ ERRADO: Pastas fora do padrão, nomes diferentes                │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 2. SERVICE PROVIDER

### Perguntas Fundamentais

```
┌─────────────────────────────────────────────────────────────────────┐
│  2.1  Qual o nome do seu ServiceProvider?                           │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: {NomePackage}ServiceProvider                          │
│  ❌ ERRADO: Provider, ServiceProvider, MyProvider                   │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  2.2  O que você colocou no método register()?                      │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO:                                                       │
│     • Merge de configs                                              │
│     • Bindings de Controller ($this->app->bind())                   │
│     • Registro de singletons                                        │
│                                                                     │
│  ❌ ERRADO:                                                         │
│     • Rotas                                                         │
│     • Views                                                         │
│     • Migrations                                                    │
│     • Override de Model com Concord                                 │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  2.3  O que você colocou no método boot()?                          │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO:                                                       │
│     • loadRoutesFrom()                                              │
│     • loadViewsFrom()                                               │
│     • loadTranslationsFrom()                                        │
│     • loadMigrationsFrom()                                          │
│     • publishes() para assets                                       │
│     • Override de Model com Concord                                 │
│                                                                     │
│  ❌ ERRADO:                                                         │
│     • Bindings de container                                         │
│     • Merge de configs                                              │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  2.4  Você tem EventServiceProvider separado?                       │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO (se usa eventos): Sim, registrado no boot() do main    │
│  ⚠️ ACEITÁVEL: Eventos no próprio ServiceProvider (packages simples)│
│  ❌ ERRADO: Eventos em arquivo solto sem registro                   │
└─────────────────────────────────────────────────────────────────────┘
```

### Pergunta de Código

```
┌─────────────────────────────────────────────────────────────────────┐
│  2.5  Mostre seu ServiceProvider. Ele segue este padrão?            │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  class CustomThemeServiceProvider extends ServiceProvider           │
│  {                                                                  │
│      public function register()                                     │
│      {                                                              │
│          // Configs                                                 │
│          $this->mergeConfigFrom(__DIR__.'/../Config/config.php',    │
│              'customtheme');                                        │
│                                                                     │
│          // Controller overrides                                    │
│          $this->app->bind(                                          │
│              \Webkul\Admin\Http\Controllers\LeadController::class,  │
│              \Webkul\CustomTheme\Http\Controllers\LeadController::class │
│          );                                                         │
│      }                                                              │
│                                                                     │
│      public function boot()                                         │
│      {                                                              │
│          // Routes                                                  │
│          $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');       │
│                                                                     │
│          // Views                                                   │
│          $this->loadViewsFrom(__DIR__.'/../Resources/views',        │
│              'customtheme');                                        │
│                                                                     │
│          // Translations                                            │
│          $this->loadTranslationsFrom(__DIR__.'/../Resources/lang',  │
│              'customtheme');                                        │
│                                                                     │
│          // Model overrides (Concord)                               │
│          $this->app->concord->registerModel(                        │
│              \Webkul\Lead\Contracts\Lead::class,                    │
│              \Webkul\CustomTheme\Models\Lead::class                 │
│          );                                                         │
│                                                                     │
│          // Publishes                                               │
│          $this->publishes([                                         │
│              __DIR__.'/../Resources/assets' =>                      │
│                  public_path('vendor/customtheme')                  │
│          ], 'customtheme-assets');                                  │
│      }                                                              │
│  }                                                                  │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 3. OVERRIDES

### Controller Override

```
┌─────────────────────────────────────────────────────────────────────┐
│  3.1  Como você fez o override do controller?                       │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO:                                                       │
│     1. Estendi o controller original                                │
│     2. Mantive os eventos before/after                              │
│     3. Registrei com $this->app->bind() no register()               │
│                                                                     │
│  ❌ ERRADO:                                                         │
│     • Criei controller do zero sem estender                         │
│     • Editei o controller original em packages/Webkul/Admin/        │
│     • Registrei no boot() ao invés de register()                    │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  3.2  Você manteve os eventos before/after do controller original?  │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: Sim, com Event::dispatch('entity.action.before/after')│
│  ❌ ERRADO: Removi / Não sei o que são                              │
└─────────────────────────────────────────────────────────────────────┘
```

### Model Override

```
┌─────────────────────────────────────────────────────────────────────┐
│  3.3  Como você fez o override do model?                            │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO:                                                       │
│     1. Estendi o model original                                     │
│     2. Adicionei ao $fillable os novos campos                       │
│     3. Registrei com Concord usando CONTRACT (interface)            │
│        $this->app->concord->registerModel(                          │
│            \Webkul\Lead\Contracts\Lead::class,  ← CONTRACT          │
│            \Webkul\CustomTheme\Models\Lead::class                   │
│        );                                                           │
│                                                                     │
│  ❌ ERRADO:                                                         │
│     • Registrei usando a CLASSE direta ao invés do Contract         │
│     • Editei o model original em packages/Webkul/Lead/              │
│     • Não estendi o model original                                  │
│     • Registrei no register() ao invés de boot()                    │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  3.4  Por que você usou Contract ao invés da classe direta?         │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: Porque o Concord resolve models via interface         │
│               (Contract), permitindo substituição transparente       │
│                                                                     │
│  ❌ ERRADO: Não sei / Usei classe direta                            │
└─────────────────────────────────────────────────────────────────────┘
```

### View Override

```
┌─────────────────────────────────────────────────────────────────────┐
│  3.5  Como você descobriu qual view precisava customizar?           │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: Usei o Blade Tracer (hover sobre elemento)            │
│  ⚠️ ACEITÁVEL: Busquei no código / Segui a rota até o controller   │
│  ❌ ERRADO: Chutei / Fui tentando até acertar                       │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  3.6  Onde está sua view customizada?                               │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO (opção 1 - Package):                                   │
│     packages/Webkul/CustomTheme/Resources/views/...                 │
│     + vendor:publish para resources/views/vendor/customtheme/       │
│                                                                     │
│  ✅ ESPERADO (opção 2 - Vendor Override):                           │
│     resources/views/vendor/{package-original}/...                   │
│                                                                     │
│  ❌ ERRADO:                                                         │
│     • Editei a view original em packages/Webkul/Admin/              │
│     • Coloquei em resources/views/ sem estrutura vendor             │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  3.7  A estrutura de pastas da sua view espelha a original?         │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: Sim, exatamente igual                                 │
│     Original: packages/Webkul/Admin/Resources/views/auth/login.blade.php │
│     Override: packages/Webkul/CustomTheme/Resources/views/auth/login.blade.php │
│                                                                     │
│  ❌ ERRADO: Estrutura diferente, nomes diferentes                   │
└─────────────────────────────────────────────────────────────────────┘
```

### View Render Event

```
┌─────────────────────────────────────────────────────────────────────┐
│  3.8  Quando você usou View Render Event vs View Override?          │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO:                                                       │
│     • View Render Event: Para injetar pequeno HTML sem duplicar view│
│     • View Override: Para alterar estrutura significativa da view  │
│                                                                     │
│  ❌ ERRADO:                                                         │
│     • Dupliquei view inteira só para adicionar um botão             │
│     • Não conheço View Render Events                                │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  3.9  Como você registrou o View Render Event?                      │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO:                                                       │
│     Event::listen('admin.leads.create.form_buttons.before',         │
│         fn($viewRenderEventManager) =>                              │
│             $viewRenderEventManager->addTemplate('customtheme::partials.button') │
│     );                                                              │
│                                                                     │
│  ❌ ERRADO: Não sei como funciona                                   │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 4. EVENTOS E LISTENERS

### Perguntas Fundamentais

```
┌─────────────────────────────────────────────────────────────────────┐
│  4.1  Qual evento você está escutando?                              │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: Eventos do catálogo oficial                           │
│     • lead.create.after                                             │
│     • lead.update.before                                            │
│     • contact.person.create.after                                   │
│     • quote.create.after                                            │
│     • etc.                                                          │
│                                                                     │
│  ❌ ERRADO: Evento inventado que não existe no Krayin               │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  4.2  Onde você registrou o listener?                               │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO:                                                       │
│     • EventServiceProvider do seu package                           │
│     • Registrado no boot() do ServiceProvider principal             │
│                                                                     │
│  ❌ ERRADO:                                                         │
│     • Arquivo solto sem registro                                    │
│     • No meio de um controller                                      │
│     • No EventServiceProvider do Laravel (app/Providers)            │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  4.3  Qual notação você usou para registrar o listener?             │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO (qualquer uma):                                        │
│     • String: Event::listen('event', 'Namespace\Listener@method')   │
│     • Closure: Event::listen('event', function($data) {...})        │
│     • Array: Event::listen('event', [Listener::class, 'handle'])    │
│                                                                     │
│  ❌ ERRADO: Sintaxe incorreta ou fora do EventServiceProvider       │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  4.4  Seu listener precisa enviar email. Está usando queue?         │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: Sim, Mailable implementa ShouldQueue                  │
│  ❌ ERRADO: Não / Email síncrono travando a requisição              │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  4.5  Você testou o listener antes de fazer deploy?                 │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO:                                                       │
│     php artisan tinker                                              │
│     >>> event('lead.create.after', \Webkul\Lead\Models\Lead::first()) │
│                                                                     │
│  ❌ ERRADO: Não testei / Só testei em produção                      │
└─────────────────────────────────────────────────────────────────────┘
```

### Pergunta de Código

```
┌─────────────────────────────────────────────────────────────────────┐
│  4.6  Mostre seu EventServiceProvider. Ele segue este padrão?       │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  namespace Webkul\CustomTheme\Providers;                            │
│                                                                     │
│  use Illuminate\Support\Facades\Event;                              │
│  use Illuminate\Support\ServiceProvider;                            │
│                                                                     │
│  class EventServiceProvider extends ServiceProvider                 │
│  {                                                                  │
│      public function boot()                                         │
│      {                                                              │
│          Event::listen(                                             │
│              'lead.create.after',                                   │
│              'Webkul\CustomTheme\Listeners\LeadListener@handleCreate' │
│          );                                                         │
│                                                                     │
│          Event::listen(                                             │
│              'lead.update.after',                                   │
│              'Webkul\CustomTheme\Listeners\LeadListener@handleUpdate' │
│          );                                                         │
│      }                                                              │
│  }                                                                  │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 5. VIEWS E ASSETS

### Views

```
┌─────────────────────────────────────────────────────────────────────┐
│  5.1  Qual namespace você usou para suas views?                     │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: lowercase do nome do package                          │
│     Package: CustomTheme                                            │
│     Namespace: customtheme                                          │
│     Uso: @include('customtheme::partials.header')                   │
│                                                                     │
│  ❌ ERRADO: PascalCase, snake_case, ou sem namespace                │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  5.2  Você validou que seu override está funcionando?               │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO:                                                       │
│     1. Ativei Blade Tracer                                          │
│     2. Acessei a página                                             │
│     3. Hover mostrou meu package, não o original                    │
│                                                                     │
│  ❌ ERRADO: Assumi que estava funcionando sem validar               │
└─────────────────────────────────────────────────────────────────────┘
```

### Assets (CSS/JS/Imagens)

```
┌─────────────────────────────────────────────────────────────────────┐
│  5.3  Onde estão seus assets?                                       │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO:                                                       │
│     Origem: packages/Webkul/CustomTheme/Resources/assets/           │
│     Destino: public/vendor/customtheme/ (após vendor:publish)       │
│                                                                     │
│  ❌ ERRADO:                                                         │
│     • Direto em public/ sem estrutura vendor                        │
│     • Sem vendor:publish configurado                                │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  5.4  Como você está referenciando os assets nas views?             │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO:                                                       │
│     {{ asset('vendor/customtheme/css/theme.css') }}                 │
│     {{ asset('vendor/customtheme/images/logo.png') }}               │
│                                                                     │
│  ❌ ERRADO:                                                         │
│     • Path hardcoded: /public/vendor/...                            │
│     • Path relativo: ./css/theme.css                                │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  5.5  Você publicou os assets?                                      │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO:                                                       │
│     php artisan vendor:publish --tag=customtheme-assets --force     │
│                                                                     │
│  ❌ ERRADO: Não / Copiei manualmente para public/                   │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  5.6  Qual o tamanho do seu logo?                                   │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO:                                                       │
│     • Logo principal: 200x60px                                      │
│     • Logo ícone: 32x32px                                           │
│     • Favicon: 16x16, 32x32px                                       │
│                                                                     │
│  ❌ ERRADO: Tamanhos muito diferentes que quebram layout            │
└─────────────────────────────────────────────────────────────────────┘
```

### CSS Variables

```
┌─────────────────────────────────────────────────────────────────────┐
│  5.7  Como você customizou as cores?                                │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: Usando CSS Variables                                  │
│     :root {                                                         │
│         --primary-color: #1E40AF;                                   │
│         --secondary-color: #3B82F6;                                 │
│     }                                                               │
│                                                                     │
│  ❌ ERRADO:                                                         │
│     • Cores hardcoded em cada elemento                              │
│     • Editei CSS do core                                            │
└─────────────────────────────────────────────────────────────────────┘
```

### Traduções

```
┌─────────────────────────────────────────────────────────────────────┐
│  5.8  Você tem traduções pt_BR?                                     │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO:                                                       │
│     Arquivo: Resources/lang/pt_BR/app.php                           │
│     Uso: {{ trans('customtheme::app.common.save') }}                │
│                                                                     │
│  ❌ ERRADO: Textos hardcoded em português nas views                 │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 6. DOCKER E DEPLOY

### Dockerfile

```
┌─────────────────────────────────────────────────────────────────────┐
│  6.1  Seu Dockerfile executa vendor:publish?                        │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: Sim, no build ou no entrypoint                        │
│     RUN php artisan vendor:publish --force --all                    │
│     # ou                                                            │
│     RUN php artisan vendor:publish --tag=customtheme-assets --force │
│                                                                     │
│  ❌ ERRADO: Não / Assets não aparecem em produção                   │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  6.2  Seu Dockerfile executa optimize:clear?                        │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: Sim                                                   │
│     RUN php artisan optimize:clear                                  │
│                                                                     │
│  ❌ ERRADO: Não / Cache antigo sendo deployado                      │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  6.3  Seu config/modules.php inclui o package custom?               │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: Sim, por ÚLTIMO na lista                              │
│  ❌ ERRADO: Não / No meio da lista / Package não carrega            │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  6.4  Seu composer.json raiz tem o path repository?                 │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO:                                                       │
│     "repositories": [                                               │
│         {"type": "path", "url": "packages/Webkul/*"}                │
│     ]                                                               │
│                                                                     │
│  ❌ ERRADO: Não / Package não encontrado durante build              │
└─────────────────────────────────────────────────────────────────────┘
```

### Deploy e Cache

```
┌─────────────────────────────────────────────────────────────────────┐
│  6.5  Qual comando você usa para limpar cache?                      │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: php artisan optimize:clear                            │
│     (limpa config, route, view, event, cache de uma vez)            │
│                                                                     │
│  ⚠️ ACEITÁVEL: Comandos individuais                                │
│     php artisan cache:clear                                         │
│     php artisan config:clear                                        │
│     php artisan route:clear                                         │
│     php artisan view:clear                                          │
│                                                                     │
│  ❌ ERRADO: Não limpo cache / Só limpo em produção                  │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  6.6  Você gera cache em produção?                                  │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: Sim, após optimize:clear                              │
│     php artisan config:cache                                        │
│     php artisan route:cache                                         │
│     php artisan view:cache                                          │
│                                                                     │
│  ❌ ERRADO: Não gero cache em produção / Performance ruim           │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  6.7  Você testou em staging antes de ir para produção?             │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: Sim, com checklist completo                           │
│     • Login funciona                                                │
│     • Assets carregam                                               │
│     • Customizações visíveis                                        │
│     • Logs limpos                                                   │
│     • Performance ok (Debug Bar)                                    │
│                                                                     │
│  ❌ ERRADO: Deploy direto em produção                               │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 7. FERRAMENTAS DE DESENVOLVIMENTO

### Blade Tracer

```
┌─────────────────────────────────────────────────────────────────────┐
│  7.1  Você usou Blade Tracer para localizar views?                  │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: Sim, sempre antes de criar override                   │
│  ❌ ERRADO: Não conheço / Fui procurando manualmente                │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  7.2  Blade Tracer está instalado como dev dependency?              │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: Sim, composer require --dev                           │
│  ❌ ERRADO: Instalado como dependência normal                       │
└─────────────────────────────────────────────────────────────────────┘
```

### Package Generator

```
┌─────────────────────────────────────────────────────────────────────┐
│  7.3  Você usou Package Generator para criar o package?             │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: Sim, php artisan package:make                         │
│  ⚠️ ACEITÁVEL: Não, mas segui estrutura canônica                   │
│  ❌ ERRADO: Criei estrutura diferente do padrão                     │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  7.4  Você usou os comandos para gerar artefatos?                   │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO:                                                       │
│     php artisan package:make-model ...                              │
│     php artisan package:make-controller ...                         │
│     php artisan package:make-repository ...                         │
│                                                                     │
│  ⚠️ ACEITÁVEL: Criei manualmente seguindo padrão                   │
│  ❌ ERRADO: Criei com estrutura/namespace diferente                 │
└─────────────────────────────────────────────────────────────────────┘
```

### Debug Bar

```
┌─────────────────────────────────────────────────────────────────────┐
│  7.5  Você verificou a performance do seu package?                  │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: Sim, usei Debug Bar para verificar                    │
│     • Quantidade de queries                                         │
│     • Tempo de execução                                             │
│     • Uso de memória                                                │
│                                                                     │
│  ❌ ERRADO: Não verifiquei / Só vou ver se reclamarem               │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  7.6  As ferramentas de dev vão para produção?                      │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: Não, composer install --no-dev em produção            │
│  ❌ ERRADO: Sim / Blade Tracer visível para usuários                │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 8. BOAS PRÁTICAS

### Anti-patterns

```
┌─────────────────────────────────────────────────────────────────────┐
│  8.1  Você editou algum arquivo em packages/Webkul/Admin/ ou        │
│       outros packages do core?                                      │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: Não, criei override no meu package                    │
│  ❌ ERRADO: Sim, editei arquivo do core                             │
│                                                                     │
│  ⚠️ CONSEQUÊNCIA DO ERRO: Próximo composer update vai sobrescrever │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  8.2  Você commitou .env com secrets?                               │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: Não, .env está no .gitignore                          │
│  ❌ ERRADO: Sim / Secrets expostos no repositório                   │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  8.3  Você rodou composer update direto em produção?                │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: Não, sempre testo em staging primeiro                 │
│  ❌ ERRADO: Sim / Sistema quebrou em produção                       │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  8.4  Sua lógica pesada está em views?                              │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: Não, lógica em Controllers/Services                   │
│  ❌ ERRADO: Sim, PHP complexo dentro de Blade                       │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  8.5  Você usou classe direta ao invés de Contract no Concord?      │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: Não, sempre uso Contract (interface)                  │
│  ❌ ERRADO: Sim, usei classe direta e override não funciona         │
└─────────────────────────────────────────────────────────────────────┘
```

### Documentação

```
┌─────────────────────────────────────────────────────────────────────┐
│  8.6  Seu package tem README.md?                                    │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: Sim, com:                                             │
│     • Descrição do que faz                                          │
│     • Instruções de instalação                                      │
│     • Lista de overrides/listeners                                  │
│     • Como testar                                                   │
│                                                                     │
│  ❌ ERRADO: Não / Ninguém sabe o que o package faz                  │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  8.7  Você documentou as customizações feitas?                      │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO: Sim, lista de:                                        │
│     • Controllers sobrescritos                                      │
│     • Models sobrescritos                                           │
│     • Views sobrescritas                                            │
│     • Listeners adicionados                                         │
│     • Eventos customizados                                          │
│                                                                     │
│  ❌ ERRADO: Não / Próximo dev vai ter que descobrir sozinho         │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 9. TROUBLESHOOTING

### Perguntas de Diagnóstico

```
┌─────────────────────────────────────────────────────────────────────┐
│  9.1  "Minha view não está sendo usada"                             │
│       O que você verifica primeiro?                                 │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO:                                                       │
│     1. Blade Tracer - ver qual view está ativa                      │
│     2. Estrutura de pastas - espelha a original?                    │
│     3. vendor:publish executado?                                    │
│     4. optimize:clear executado?                                    │
│                                                                     │
│  ❌ ERRADO: Não sei diagnosticar                                    │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  9.2  "Meu listener não dispara"                                    │
│       O que você verifica primeiro?                                 │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO:                                                       │
│     1. EventServiceProvider está registrado no ServiceProvider?     │
│     2. Nome do evento está correto?                                 │
│     3. Namespace do listener está correto?                          │
│     4. Método existe e recebe parâmetro correto?                    │
│     5. Testar com tinker: event('nome.evento', $data)               │
│                                                                     │
│  ❌ ERRADO: Não sei diagnosticar                                    │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  9.3  "Meu model override não carrega"                              │
│       O que você verifica primeiro?                                 │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO:                                                       │
│     1. Usando Contract ao invés de classe direta?                   │
│     2. Registrado no boot() com Concord?                            │
│     3. Estende o model original?                                    │
│     4. Package está por último no config/modules.php?               │
│                                                                     │
│  ❌ ERRADO: Não sei diagnosticar                                    │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  9.4  "Assets não aparecem (404)"                                   │
│       O que você verifica primeiro?                                 │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO:                                                       │
│     1. vendor:publish executado?                                    │
│     2. Tag correta no publishes()?                                  │
│     3. Arquivos existem em public/vendor/...?                       │
│     4. Cache do browser?                                            │
│                                                                     │
│  ❌ ERRADO: Não sei diagnosticar                                    │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  9.5  "Package não aparece/carrega"                                 │
│       O que você verifica primeiro?                                 │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO:                                                       │
│     1. Adicionado em config/modules.php?                            │
│     2. composer dump-autoload executado?                            │
│     3. Autoload PSR-4 no composer.json?                             │
│     4. ServiceProvider existe e está correto?                       │
│     5. Verificar: cat config/modules.php | grep NomePackage         │
│                                                                     │
│  ❌ ERRADO: Não sei diagnosticar                                    │
└─────────────────────────────────────────────────────────────────────┘
```

### Comandos de Diagnóstico

```
┌─────────────────────────────────────────────────────────────────────┐
│  9.6  Quais comandos você usa para diagnosticar problemas?          │
├─────────────────────────────────────────────────────────────────────┤
│  ✅ ESPERADO (conhecer pelo menos estes):                           │
│                                                                     │
│     # Verificar se package está registrado                          │
│     cat config/modules.php | grep -i custom                         │
│                                                                     │
│     # Verificar se assets existem                                   │
│     ls -la public/vendor/customtheme/                               │
│                                                                     │
│     # Ver logs de erro                                              │
│     tail -f storage/logs/laravel.log                                │
│                                                                     │
│     # Testar evento manualmente                                     │
│     php artisan tinker                                              │
│     >>> event('lead.update.after', Lead::first());                  │
│                                                                     │
│     # Ver info geral do Laravel                                     │
│     php artisan about                                               │
│                                                                     │
│     # Docker logs                                                   │
│     docker logs krayin --tail 100                                   │
│                                                                     │
│  ❌ ERRADO: Não conheço comandos de diagnóstico                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 📊 SCORECARD DE VALIDAÇÃO

### Como Usar

Para cada seção, conte quantas respostas foram ✅ ESPERADO:

| Seção | Perguntas | Mínimo Aceitável | Ideal |
|-------|:---------:|:----------------:|:-----:|
| 1. Estrutura de Package | 7 | 5 | 7 |
| 2. Service Provider | 5 | 4 | 5 |
| 3. Overrides | 9 | 7 | 9 |
| 4. Eventos e Listeners | 6 | 5 | 6 |
| 5. Views e Assets | 8 | 6 | 8 |
| 6. Docker e Deploy | 7 | 6 | 7 |
| 7. Ferramentas de Dev | 6 | 4 | 6 |
| 8. Boas Práticas | 7 | 6 | 7 |
| 9. Troubleshooting | 6 | 4 | 6 |
| **TOTAL** | **61** | **47** | **61** |

### Interpretação

```
47-61 pontos: ✅ Desenvolvimento alinhado com a documentação
35-46 pontos: ⚠️ Revisar pontos fracos antes de prosseguir
< 35 pontos:  ❌ Parar e estudar a documentação novamente
```

---

## 🔄 QUANDO APLICAR ESTE CHECKLIST

| Momento | Seções Prioritárias |
|---------|---------------------|
| **Onboarding de dev** | Todas (1-9) |
| **Antes de criar package** | 1, 2, 7 |
| **Durante code review** | 1-5, 8 |
| **Antes de deploy** | 5, 6, 8 |
| **Troubleshooting** | 9 |
| **Auditoria técnica** | Todas (1-9) |

---

*"Se não consegue responder, não está pronto para deploy."*
