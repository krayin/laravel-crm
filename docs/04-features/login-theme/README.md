# Documentação Completa: Sistema Multi-Tema Login Krayin CRM

## Índice
1. [Visão Geral](#visão-geral)
2. [Arquitetura](#arquitetura)
3. [Arquivos Criados](#arquivos-criados)
4. [Código Detalhado](#código-detalhado)
5. [Fluxo de Funcionamento](#fluxo-de-funcionamento)
6. [Configurações](#configurações)
7. [Como Usar](#como-usar)

---

## Visão Geral

### Objetivo
Implementar um sistema de temas customizáveis para a página de login do Krayin CRM que seja:
- **Upgrade-safe**: Zero modificações em `packages/Webkul/*`
- **Pré-auth**: Funciona antes do usuário fazer login
- **Determinístico**: `is_active` + `selected_theme` define tudo
- **Seguro**: Validações de permissão para alterações

### Princípios de Design
1. Usar overrides de view via `View::prependNamespace()`
2. Persistir `selected_theme` via `DB::table()->update()` (sem depender de `$fillable`)
3. Background e overlay via `body::before` e `body::after` (CSS puro, sem markup extra)
4. CSS base inline (não depende de Vite build)
5. Cache com chave padronizada para invalidação precisa

---

## Arquitetura

```
┌─────────────────────────────────────────────────────────────────┐
│                        REQUEST FLOW                              │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  1. Request HTTP                                                 │
│       ↓                                                          │
│  2. CaptureThemeSelection (Middleware Global)                    │
│       → Early-return se não for rota de update tema              │
│       → Valida auth + permission                                 │
│       → Persiste selected_theme via DB::table()                  │
│       → Limpa cache específico                                   │
│       ↓                                                          │
│  3. ShareThemeContext (Middleware Web)                           │
│       → Early-return se não for rota admin                       │
│       → Cria ThemeContext via Factory                            │
│       → Compartilha com View::share()                            │
│       ↓                                                          │
│  4. ThemeBootProvider (Service Provider)                         │
│       → Registra View::prependNamespace('admin', ...)            │
│       → Permite override de views sem tocar packages/            │
│       ↓                                                          │
│  5. View Override (resources/views/vendor/admin/...)             │
│       → login.blade.php renderiza tema ou padrão                 │
│       → theme-head.blade.php injeta CSS variables                │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## Arquivos Criados

### Estrutura de Diretórios
```
laravel-crm/
├── app/
│   ├── Http/
│   │   ├── Kernel.php                          # (modificado) - Adicionou middlewares
│   │   └── Middleware/
│   │       ├── CaptureThemeSelection.php       # (NOVO) - Captura seleção de tema
│   │       └── ShareThemeContext.php           # (NOVO) - Compartilha contexto
│   ├── Providers/
│   │   └── ThemeBootProvider.php               # (NOVO) - Registra overrides
│   └── Support/
│       ├── ThemeContext.php                    # (NOVO) - Value Object imutável
│       └── ThemeContextFactory.php             # (NOVO) - Factory com cache
├── config/
│   └── app.php                                 # (modificado) - Registrou provider
├── database/
│   └── migrations/
│       └── 2024_12_23_100000_add_selected_theme_to_theme_configs.php  # (NOVO)
├── resources/
│   └── views/
│       └── vendor/
│           └── admin/
│               ├── sessions/
│               │   └── login.blade.php         # (NOVO) - Override do login
│               ├── components/
│               │   └── layouts/
│               │       └── anonymous.blade.php # (NOVO) - Override do layout
│               └── partials/
│                   └── theme-head.blade.php    # (NOVO) - CSS do tema
└── .env                                        # (modificado) - APP_URL, DEBUGBAR
```

---

## Código Detalhado

### 1. Migration: Adicionar coluna `selected_theme`

**Arquivo:** `database/migrations/2024_12_23_100000_add_selected_theme_to_theme_configs.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('theme_configs', function (Blueprint $table) {
            $table->string('selected_theme', 50)
                  ->default('default')
                  ->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('theme_configs', function (Blueprint $table) {
            $table->dropColumn('selected_theme');
        });
    }
};
```

**Propósito:** Adiciona a coluna `selected_theme` à tabela `theme_configs` para permitir múltiplos temas.

---

### 2. ThemeContext: Value Object Imutável

**Arquivo:** `app/Support/ThemeContext.php`

```php
<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Value object imutável que representa o contexto do tema atual.
 * Usado para passar informações de tema para as views de forma type-safe.
 */
final class ThemeContext
{
    /**
     * @param bool   $enabled      Se o sistema de temas está ativo
     * @param string $slug         Slug do tema selecionado
     * @param array  $config       Configurações gerais (cores, logos)
     * @param array  $loginConfig  Configurações específicas do login
     */
    public function __construct(
        public readonly bool $enabled,
        public readonly string $slug,
        public readonly array $config,
        public readonly array $loginConfig
    ) {}

    /**
     * Retorna classes CSS para o body baseado no estado do tema.
     */
    public function bodyClasses(): string
    {
        $classes = [];

        if ($this->enabled) {
            $classes[] = 'theme-enabled';
            $classes[] = 'theme-' . $this->slug;
        } else {
            $classes[] = 'theme-disabled';
        }

        // Sempre adiciona classe de login (útil para estilos específicos)
        $classes[] = 'theme-login';

        // Adiciona classe de background se tiver imagem configurada
        if ($this->enabled && !empty($this->loginConfig['bg_image'])) {
            $classes[] = 'theme-login-bg';
        }

        // Adiciona classe de card customizado se habilitado
        if ($this->enabled && !empty($this->loginConfig['card_enabled'])) {
            $classes[] = 'theme-login-card-custom';
        }

        return implode(' ', $classes);
    }

    /**
     * Obtém valor de configuração geral com fallback.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (!$this->enabled) {
            return $default;
        }

        return $this->config[$key] ?? $default;
    }

    /**
     * Obtém valor de configuração de login com fallback.
     */
    public function login(string $key, mixed $default = null): mixed
    {
        if (!$this->enabled) {
            return $default;
        }

        return $this->loginConfig[$key] ?? $default;
    }

    /**
     * Obtém URL do logo por tipo (main, light, icon).
     * Retorna null se tema desativado ou logo não configurado.
     */
    public function logo(string $type): ?string
    {
        if (!$this->enabled) {
            return null;
        }

        $key = "logo_{$type}";
        $filename = $this->config[$key] ?? null;

        if (empty($filename)) {
            return null;
        }

        // Assets do tema ficam em storage/app/public/themes/{slug}/
        // ou no path legado theme-manager/
        if (Storage::disk('public')->exists("themes/{$this->slug}/{$filename}")) {
            return Storage::disk('public')->url("themes/{$this->slug}/{$filename}");
        }

        // Fallback para path legado (theme-manager/)
        if (Storage::disk('public')->exists("theme-manager/{$filename}")) {
            return Storage::disk('public')->url("theme-manager/{$filename}");
        }

        return null;
    }

    /**
     * Obtém URL do CSS externo do tema (opcional).
     */
    public function cssUrl(): ?string
    {
        if (!$this->enabled) {
            return null;
        }

        $cssPath = "themes/{$this->slug}/theme.css";

        if (Storage::disk('public')->exists($cssPath)) {
            return Storage::disk('public')->url($cssPath) . '?v=' . filemtime(storage_path("app/public/{$cssPath}"));
        }

        return null;
    }

    /**
     * Obtém URL do background de login.
     */
    public function loginBgUrl(): ?string
    {
        if (!$this->enabled) {
            return null;
        }

        $filename = $this->loginConfig['bg_image'] ?? null;

        if (empty($filename)) {
            return null;
        }

        // Se já é URL completa
        if (str_starts_with($filename, 'http')) {
            return $filename;
        }

        // Tenta no diretório do tema
        if (Storage::disk('public')->exists("themes/{$this->slug}/{$filename}")) {
            return Storage::disk('public')->url("themes/{$this->slug}/{$filename}");
        }

        // Fallback para path legado
        if (Storage::disk('public')->exists("theme-manager/{$filename}")) {
            return Storage::disk('public')->url("theme-manager/{$filename}");
        }

        return null;
    }

    /**
     * Obtém URL do background do card de login.
     */
    public function loginCardBgUrl(): ?string
    {
        if (!$this->enabled || empty($this->loginConfig['card_enabled'])) {
            return null;
        }

        $filename = $this->loginConfig['card_bg_image'] ?? null;

        if (empty($filename)) {
            return null;
        }

        if (str_starts_with($filename, 'http')) {
            return $filename;
        }

        if (Storage::disk('public')->exists("themes/{$this->slug}/{$filename}")) {
            return Storage::disk('public')->url("themes/{$this->slug}/{$filename}");
        }

        if (Storage::disk('public')->exists("theme-manager/{$filename}")) {
            return Storage::disk('public')->url("theme-manager/{$filename}");
        }

        return null;
    }

    /**
     * Verifica se o card customizado está habilitado.
     */
    public function hasCustomCard(): bool
    {
        return $this->enabled && !empty($this->loginConfig['card_enabled']);
    }

    /**
     * Verifica se deve mostrar "Powered by".
     */
    public function showPoweredBy(): bool
    {
        if (!$this->enabled) {
            return true; // Padrão quando tema desativado
        }

        return (bool) ($this->loginConfig['show_powered_by'] ?? true);
    }

    /**
     * Retorna contexto vazio/desativado (para fallback).
     */
    public static function disabled(): self
    {
        return new self(
            enabled: false,
            slug: 'default',
            config: [],
            loginConfig: []
        );
    }
}
```

**Propósito:** 
- Objeto imutável que encapsula todo o estado do tema
- Métodos helper para acessar configurações de forma segura
- Gera classes CSS dinamicamente baseado no estado

---

### 3. ThemeContextFactory: Factory com Cache

**Arquivo:** `app/Support/ThemeContextFactory.php`

```php
<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Factory para criar instâncias de ThemeContext.
 *
 * Lê configurações do banco e do helper existente sem modificar packages/.
 * Usa cache próprio com chave padronizada para invalidação precisa.
 */
class ThemeContextFactory
{
    /**
     * Cache key padronizada (mesma usada no CaptureThemeSelection).
     */
    private const CACHE_KEY = "theme_context.factory.v1";

    /**
     * TTL do cache em segundos (1 hora).
     */
    private const CACHE_TTL = 3600;

    /**
     * Cria o ThemeContext baseado nas configurações atuais.
     * Usa cache para evitar queries repetidas.
     */
    public static function make(): ThemeContext
    {
        try {
            return Cache::remember(
                self::CACHE_KEY,
                self::CACHE_TTL,
                function () {
                    return self::buildContext();
                },
            );
        } catch (\Throwable $e) {
            Log::warning(
                "[Theme] Cache error, building context without cache: " .
                    $e->getMessage(),
            );

            try {
                return self::buildContext();
            } catch (\Throwable $e2) {
                Log::error(
                    "[Theme] ThemeContextFactory error: " . $e2->getMessage(),
                );
                return ThemeContext::disabled();
            }
        }
    }

    /**
     * Constrói o ThemeContext (sem cache).
     */
    private static function buildContext(): ThemeContext
    {
        // Usa o helper existente para verificar is_active (aproveita cache dele)
        $isActive = app("theme")->isActive();

        if (!$isActive) {
            return ThemeContext::disabled();
        }

        // Busca selected_theme via query direta (sem depender de $fillable)
        $selectedTheme =
            DB::table("theme_configs")
                ->where("id", 1)
                ->value("selected_theme") ?? "default";

        // Sanitiza o slug
        $slug = self::sanitizeSlug($selectedTheme);

        // Valida se o tema existe (diretório ou fallback para default)
        if ($slug !== "default" && !self::themeExists($slug)) {
            Log::warning(
                "[Theme] Theme '{$slug}' not found, falling back to 'default'",
            );
            $slug = "default";
        }

        // Obtém configurações do helper existente (usa cache dele)
        $themeHelper = app("theme");
        $config = self::extractConfig($themeHelper);
        $loginConfig = self::extractLoginConfig($themeHelper);

        return new ThemeContext(
            enabled: true,
            slug: $slug,
            config: $config,
            loginConfig: $loginConfig,
        );
    }

    /**
     * Sanitiza o slug do tema.
     */
    private static function sanitizeSlug(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace("/[^a-z0-9\-_]/", "", $value) ?? "";
        $value = trim($value, "-_");

        return $value ?: "default";
    }

    /**
     * Verifica se um tema existe no storage.
     */
    private static function themeExists(string $slug): bool
    {
        $disk = Storage::disk("public");
        $dir = "themes/{$slug}";

        if (!$disk->exists($dir)) {
            return false;
        }

        return true;
    }

    /**
     * Extrai configurações gerais do ThemeHelper existente.
     */
    private static function extractConfig($themeHelper): array
    {
        $config = $themeHelper->getConfig();

        return [
            "color_primary" => $config->color_primary ?? "#1E40AF",
            "color_primary_dark" => $config->color_primary_dark ?? "#1E3A8A",
            "color_primary_light" => $config->color_primary_light ?? "#3B82F6",
            "color_success" => $config->color_success ?? "#10B981",
            "color_warning" => $config->color_warning ?? "#F59E0B",
            "color_danger" => $config->color_danger ?? "#EF4444",
            "logo_main" => $config->logo_main ?? null,
            "logo_light" => $config->logo_light ?? null,
            "logo_icon" => $config->logo_icon ?? null,
            "favicon" => $config->favicon ?? null,
        ];
    }

    /**
     * Extrai configurações de login do ThemeHelper existente.
     */
    private static function extractLoginConfig($themeHelper): array
    {
        $config = $themeHelper->getConfig();

        return [
            "bg_image" => $config->login_bg_image ?? null,
            "bg_zoom" => (int) ($config->login_bg_zoom ?? 100),
            "bg_opacity" => (int) ($config->login_bg_opacity ?? 50),
            "show_powered_by" =>
                (bool) ($config->login_show_powered_by ?? true),
            "card_enabled" => (bool) ($config->login_card_enabled ?? false),
            "card_bg_image" => $config->login_card_bg_image ?? null,
            "card_bg_opacity" => (int) ($config->login_card_bg_opacity ?? 62),
            "card_overlay_color" =>
                $config->login_card_overlay_color ?? "rgba(10, 45, 15, 0.78)",
            "card_title" => $config->login_card_title ?? "Bem-vindo",
            "card_subtitle" =>
                $config->login_card_subtitle ??
                "Acesse sua conta para continuar",
            "card_sparkles" => (bool) ($config->login_card_sparkles ?? false),
            "card_help_link" => (bool) ($config->login_card_help_link ?? true),
            "card_support_email" =>
                $config->login_card_support_email ?? "suporte@empresa.com.br",
        ];
    }

    /**
     * Limpa o cache do ThemeContext.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);

        try {
            app("theme")->clearCache();
        } catch (\Throwable $e) {
            Log::warning(
                "[Theme] Could not clear ThemeHelper cache: " .
                    $e->getMessage(),
            );
        }
    }

    /**
     * Retorna a cache key (para debug/testes).
     */
    public static function getCacheKey(): string
    {
        return self::CACHE_KEY;
    }
}
```

**Propósito:**
- Cria instâncias de ThemeContext com cache
- Lê do helper existente do Krayin (não modifica packages/)
- Cache com TTL de 1 hora
- Sanitização e validação de slugs

---

### 4. CaptureThemeSelection: Middleware Global

**Arquivo:** `app/Http/Middleware/CaptureThemeSelection.php`

```php
<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\ThemeContextFactory;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware GLOBAL para capturar seleção de tema.
 *
 * Intercepta POST/PUT/PATCH para a rota de update do Theme Manager
 * e persiste selected_theme via DB::table() (bypass de $fillable).
 *
 * IMPORTANTE: Este middleware usa early-return para performance.
 * Apenas processa requests que atendem TODOS os critérios.
 */
final class CaptureThemeSelection
{
    /**
     * Nome da rota que salva configurações do tema.
     */
    private const ROUTE_NAME = 'admin.settings.theme.update';

    /**
     * Cache key do ThemeContextFactory (deve ser idêntica).
     */
    private const THEME_CONTEXT_CACHE_KEY = 'theme_context.factory.v1';

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Early-return: só processa se atender todos os critérios
        if (!$this->shouldCapture($request)) {
            return $next($request);
        }

        // Captura e persiste
        $this->captureThemeSelection($request);

        return $next($request);
    }

    /**
     * Verifica se deve capturar a seleção de tema.
     * Retorna false (early-return) se qualquer condição falhar.
     */
    private function shouldCapture(Request $request): bool
    {
        // 1. Verifica nome da rota
        $routeName = $request->route()?->getName();
        if ($routeName !== self::ROUTE_NAME) {
            return false;
        }

        // 2. Verifica método HTTP
        if (!in_array(strtoupper($request->method()), ['POST', 'PUT', 'PATCH'], true)) {
            return false;
        }

        // 3. Verifica autenticação e permissão
        if (!$this->userCanChangeTheme($request)) {
            return false;
        }

        // 4. Verifica se tem o campo selected_theme
        if (!$request->has('selected_theme')) {
            return false;
        }

        return true;
    }

    /**
     * Verifica se o usuário atual pode alterar o tema.
     */
    private function userCanChangeTheme(Request $request): bool
    {
        // Usa o guard 'user' do Krayin Admin
        $user = auth('user')->user();

        if (!$user) {
            Log::warning('[Theme] CaptureThemeSelection: No authenticated user');
            return false;
        }

        // Verifica se user está ativo
        if (!($user->status ?? false)) {
            Log::warning('[Theme] CaptureThemeSelection: User is not active', [
                'user_id' => $user->id,
            ]);
            return false;
        }

        // Verifica permissão via Bouncer (ACL do Krayin)
        if (!bouncer()->hasPermission('settings.theme.edit')) {
            Log::warning('[Theme] CaptureThemeSelection: User lacks permission', [
                'user_id' => $user->id,
                'permission' => 'settings.theme.edit',
            ]);
            return false;
        }

        return true;
    }

    /**
     * Captura e persiste a seleção de tema.
     */
    private function captureThemeSelection(Request $request): void
    {
        $selectedTheme = $request->input('selected_theme');

        // Sanitiza o valor
        $slug = $this->sanitizeSlug($selectedTheme);

        // Valida se o tema existe
        if (!$this->themeExists($slug)) {
            Log::warning('[Theme] CaptureThemeSelection: Theme does not exist', [
                'requested' => $selectedTheme,
                'sanitized' => $slug,
            ]);
            return;
        }

        try {
            // Persiste via Query Builder (bypass de $fillable)
            DB::table('theme_configs')
                ->where('id', 1)
                ->update([
                    'selected_theme' => $slug,
                    'updated_at' => now(),
                ]);

            // Limpa caches específicos
            $this->clearThemeCaches();

            Log::info('[Theme] CaptureThemeSelection: Theme updated', [
                'selected_theme' => $slug,
                'user_id' => auth('user')->id(),
            ]);
        } catch (\Throwable $e) {
            Log::error('[Theme] CaptureThemeSelection: Failed to update', [
                'error' => $e->getMessage(),
                'selected_theme' => $slug,
            ]);
        }
    }

    /**
     * Sanitiza o slug do tema.
     */
    private function sanitizeSlug(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9\-_]/', '', $value) ?? '';
        $value = trim($value, '-_');

        return $value ?: 'default';
    }

    /**
     * Verifica se um tema existe.
     */
    private function themeExists(string $slug): bool
    {
        // 'default' sempre existe (é o fallback)
        if ($slug === 'default') {
            return true;
        }

        // Verifica se existe o diretório do tema
        $disk = Storage::disk('public');
        $themeDir = "themes/{$slug}";

        return $disk->exists($themeDir);
    }

    /**
     * Limpa caches relacionados ao tema.
     */
    private function clearThemeCaches(): void
    {
        // Limpa cache do ThemeContextFactory
        Cache::forget(self::THEME_CONTEXT_CACHE_KEY);

        // Limpa cache do ThemeHelper do package (se disponível)
        try {
            if (app()->bound('theme')) {
                app('theme')->clearCache();
            }
        } catch (\Throwable $e) {
            // Ignora erros ao limpar cache do package
        }

        Log::debug('[Theme] CaptureThemeSelection: Caches cleared');
    }
}
```

**Propósito:**
- Intercepta POST/PUT/PATCH para rota de update do Theme Manager
- Valida autenticação (guard 'user') e permissão (bouncer)
- Persiste `selected_theme` via `DB::table()->update()` (bypass de `$fillable`)
- Limpa caches específicos (não usa `cache:clear` global)

---

### 5. ShareThemeContext: Middleware Web

**Arquivo:** `app/Http/Middleware/ShareThemeContext.php`

```php
<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\ThemeContext;
use App\Support\ThemeContextFactory;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para compartilhar ThemeContext com todas as views admin.
 *
 * Usa early-return para performance: só processa rotas admin.
 * Compartilha via View::share() para disponibilizar em todas as views.
 */
final class ShareThemeContext
{
    /**
     * Prefixo das rotas admin (configurável via config ou .env).
     */
    private string $adminPrefix;

    public function __construct()
    {
        $this->adminPrefix = config('app.admin_prefix', 'admin');
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Early-return: só processa rotas admin
        if (!$this->isAdminRoute($request)) {
            return $next($request);
        }

        // Cria e compartilha o contexto
        $this->shareContext();

        return $next($request);
    }

    /**
     * Verifica se é uma rota admin.
     */
    private function isAdminRoute(Request $request): bool
    {
        $path = $request->path();

        // Exato match ou começa com admin/
        return $path === $this->adminPrefix
            || str_starts_with($path, $this->adminPrefix . '/');
    }

    /**
     * Cria e compartilha o ThemeContext.
     */
    private function shareContext(): void
    {
        try {
            $themeContext = ThemeContextFactory::make();
        } catch (\Throwable $e) {
            Log::error('[Theme] ShareThemeContext: Failed to create context', [
                'error' => $e->getMessage(),
            ]);
            $themeContext = ThemeContext::disabled();
        }

        // Compartilha com todas as views
        View::share('themeContext', $themeContext);
    }
}
```

**Propósito:**
- Compartilha ThemeContext com todas as views admin
- Early-return para rotas não-admin (performance)
- Fallback para ThemeContext::disabled() em caso de erro

---

### 6. ThemeBootProvider: Service Provider

**Arquivo:** `app/Providers/ThemeBootProvider.php`

```php
<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * Provider para boot do sistema de temas.
 *
 * Registra override de views via View::prependNamespace().
 * Deve ser registrado APÓS ThemeManagerServiceProvider.
 */
class ThemeBootProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerViewOverrides();
    }

    /**
     * Registra overrides de views do admin.
     *
     * Usa prependNamespace para que views em resources/views/vendor/admin/
     * tenham prioridade sobre as views do package.
     */
    private function registerViewOverrides(): void
    {
        $vendorAdminPath = resource_path('views/vendor/admin');

        // Só registra se o diretório existir
        if (!is_dir($vendorAdminPath)) {
            return;
        }

        // Prepend para ter prioridade sobre o package
        View::prependNamespace('admin', $vendorAdminPath);
    }
}
```

**Propósito:**
- Registra `View::prependNamespace('admin', ...)` para permitir override de views
- Views em `resources/views/vendor/admin/` têm prioridade sobre `packages/Webkul/Admin/`

---

### 7. Kernel.php: Registro de Middlewares

**Arquivo:** `app/Http/Kernel.php` (modificado)

```php
// No grupo 'web':
protected $middlewareGroups = [
    'web' => [
        // ... outros middlewares ...
        \App\Http\Middleware\ShareThemeContext::class,
        \App\Http\Middleware\CaptureThemeSelection::class,
    ],
    // ...
];
```

---

### 8. config/app.php: Registro do Provider

**Arquivo:** `config/app.php` (modificado)

```php
'providers' => [
    // ... outros providers ...
    
    // Deve vir APÓS ThemeManagerServiceProvider
    Webkul\ThemeManager\Providers\ThemeManagerServiceProvider::class,
    App\Providers\ThemeBootProvider::class,
],
```

---

### 9. Login View Override

**Arquivo:** `resources/views/vendor/admin/sessions/login.blade.php`

```blade
{{--
    Login View Override (Upgrade-Safe)

    Este arquivo sobrescreve: packages/Webkul/Admin/src/Resources/views/sessions/login.blade.php
    Registrado via: App\Providers\ThemeBootProvider (View::prependNamespace)

    Comportamento:
    - $themeContext->enabled = true  → Renderiza login temático
    - $themeContext->enabled = false → Renderiza login padrão Krayin
--}}

@php
    // ThemeContext é injetado via ShareThemeContext middleware
    // Fallback seguro caso não exista
    $themeContext = $themeContext ?? \App\Support\ThemeContext::disabled();
@endphp

@if(!$themeContext->enabled)
    {{-- ================================================================
         TEMA DESATIVADO: Usa layout padrão do Admin (100% Krayin)
         ================================================================ --}}
    <x-admin::layouts.anonymous>
        <x-slot:title>
            @lang('admin::app.users.login.title')
        </x-slot>

        <div class="flex h-[100vh] flex-col items-center justify-center gap-10">
            {{-- Logo padrão --}}
            <div class="flex flex-col items-center gap-5">
                @if ($logo = core()->getConfigData('general.design.admin_logo.logo_image'))
                    <img class="h-10 w-[110px]" src="{{ Storage::url($logo) }}" alt="{{ config('app.name') }}" />
                @else
                    <img class="w-max" src="{{ vite()->asset('images/logo.svg') }}" alt="{{ config('app.name') }}" />
                @endif

                {{-- Card de login padrão --}}
                <div class="box-shadow flex min-w-[300px] flex-col rounded-md bg-white dark:bg-gray-900">
                    {!! view_render_event('admin.sessions.login.form_controls.before') !!}

                    <x-admin::form :action="route('admin.session.store')">
                        <p class="p-4 text-xl font-bold text-gray-800 dark:text-white">
                            @lang('admin::app.users.login.title')
                        </p>

                        <div class="border-y p-4 dark:border-gray-800">
                            {{-- Email --}}
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.users.login.email')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="email"
                                    class="w-[254px] max-w-full"
                                    id="email"
                                    name="email"
                                    rules="required|email"
                                    :label="trans('admin::app.users.login.email')"
                                    :placeholder="trans('admin::app.users.login.email')"
                                />

                                <x-admin::form.control-group.error control-name="email" />
                            </x-admin::form.control-group>

                            {{-- Password --}}
                            <x-admin::form.control-group class="relative w-full">
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.users.login.password')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="password"
                                    class="w-[254px] max-w-full ltr:pr-10 rtl:pl-10"
                                    id="password"
                                    name="password"
                                    rules="required|min:6"
                                    :label="trans('admin::app.users.login.password')"
                                    :placeholder="trans('admin::app.users.login.password')"
                                />

                                <span
                                    class="icon-eye-hide absolute top-11 -translate-y-2/4 cursor-pointer text-2xl ltr:right-3 rtl:left-3"
                                    onclick="switchVisibility()"
                                    id="visibilityIcon"
                                    role="presentation"
                                    tabindex="0"
                                ></span>

                                <x-admin::form.control-group.error control-name="password" />
                            </x-admin::form.control-group>
                        </div>

                        <div class="flex items-center justify-between p-4">
                            <a class="cursor-pointer text-xs font-semibold leading-6 text-brandColor"
                               href="{{ route('admin.forgot_password.create') }}">
                                @lang('admin::app.users.login.forget-password-link')
                            </a>

                            <button class="primary-button" aria-label="{{ trans('admin::app.users.login.submit-btn')}}">
                                @lang('admin::app.users.login.submit-btn')
                            </button>
                        </div>
                    </x-admin::form>

                    {!! view_render_event('admin.sessions.login.form_controls.after') !!}
                </div>
            </div>

            {{-- Powered By --}}
            <div class="text-sm font-normal">
                @lang('admin::app.components.layouts.powered-by.description', [
                    'krayin' => '<a class="text-brandColor hover:underline" href="https://krayincrm.com/">Krayin</a>',
                    'webkul' => '<a class="text-brandColor hover:underline" href="https://webkul.com/">Webkul</a>',
                ])
            </div>
        </div>

        @push('scripts')
            <script>
                function switchVisibility() {
                    let passwordField = document.getElementById("password");
                    let visibilityIcon = document.getElementById("visibilityIcon");
                    passwordField.type = passwordField.type === "password" ? "text" : "password";
                    visibilityIcon.classList.toggle("icon-eye");
                }
            </script>
        @endpush
    </x-admin::layouts.anonymous>

@else
    {{-- ================================================================
         TEMA ATIVADO: Renderiza login temático completo
         Background via body::before/::after (CSS, não markup)
         ================================================================ --}}
    <!DOCTYPE html>
    <html
        lang="{{ app()->getLocale() }}"
        dir="{{ in_array(app()->getLocale(), ['fa', 'ar']) ? 'rtl' : 'ltr' }}"
        class="{{ request()->cookie('dark_mode') ? 'dark' : '' }}"
    >
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@lang('admin::app.users.login.title') - {{ config('app.name') }}</title>

        {{-- Favicon --}}
        @if($favicon = $themeContext->get('favicon'))
            @if($faviconUrl = Storage::disk('public')->url("theme-manager/{$favicon}"))
                <link rel="icon" href="{{ $faviconUrl }}" type="image/x-icon">
            @endif
        @else
            <link rel="icon" href="{{ vite()->asset('images/favicon.ico') }}" type="image/x-icon">
        @endif

        {{-- Krayin Admin Assets --}}
        {{ vite()->set(['src/Resources/assets/css/app.css', 'src/Resources/assets/js/app.js']) }}

        {{-- Theme CSS Variables e Estilos (inline, não depende de build) --}}
        @include('admin::partials.theme-head')
    </head>
    <body class="{{ $themeContext->bodyClasses() }}">

        <div class="theme-login-layer">
            {{-- Logo --}}
            <div class="theme-login-logo">
                @php
                    $logoUrl = $themeContext->logo('main')
                            ?? $themeContext->logo('light')
                            ?? (core()->getConfigData('general.design.admin_logo.logo_image')
                                ? Storage::url(core()->getConfigData('general.design.admin_logo.logo_image'))
                                : null)
                            ?? vite()->asset('images/logo.svg');
                @endphp
                <img src="{{ $logoUrl }}" alt="{{ config('app.name') }}">
            </div>

            {{-- Card de Login --}}
            <div class="theme-login-card">
                {{-- Sparkles (se habilitado) --}}
                @if($themeContext->login('card_sparkles'))
                    <div class="theme-login-sparkles">
                        @for($i = 0; $i < 8; $i++)
                            <div class="theme-login-sparkle"></div>
                        @endfor
                    </div>
                @endif

                <div class="theme-login-card-content">
                    {{-- Header do Card (se card customizado habilitado) --}}
                    @if($themeContext->hasCustomCard())
                        <div class="theme-login-card-header">
                            <h1 class="theme-login-card-title">
                                {{ $themeContext->login('card_title') ?: __('admin::app.users.login.title') }}
                            </h1>
                            @if($subtitle = $themeContext->login('card_subtitle'))
                                <p class="theme-login-card-subtitle">{{ $subtitle }}</p>
                            @endif
                        </div>
                    @endif

                    {{-- Corpo do Card (Formulário) --}}
                    <div class="theme-login-card-body">
                        {!! view_render_event('admin.sessions.login.form_controls.before') !!}

                        {{-- Título (se card NÃO customizado) --}}
                        @if(!$themeContext->hasCustomCard())
                            <h2 class="theme-login-card-title" style="margin-bottom: 1.5rem;">
                                @lang('admin::app.users.login.title')
                            </h2>
                        @endif

                        {{-- Flash Messages --}}
                        @if(session('error'))
                            <div class="theme-login-alert theme-login-alert-error">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="theme-login-alert theme-login-alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        {{-- Validation Errors --}}
                        @if($errors->any())
                            <div class="theme-login-alert theme-login-alert-error">
                                @foreach($errors->all() as $error)
                                    <p style="margin: 0;">{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        {{-- Formulário de Login --}}
                        <form method="POST" action="{{ route('admin.session.store') }}">
                            @csrf

                            {{-- Email --}}
                            <div class="theme-login-form-group">
                                <label for="email" class="theme-login-label">
                                    @lang('admin::app.users.login.email') <span style="color: var(--theme-danger);">*</span>
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="theme-login-input"
                                    placeholder="@lang('admin::app.users.login.email')"
                                    value="{{ old('email') }}"
                                    required
                                    autofocus
                                >
                                @error('email')
                                    <span class="theme-login-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div class="theme-login-form-group">
                                <label for="password" class="theme-login-label">
                                    @lang('admin::app.users.login.password') <span style="color: var(--theme-danger);">*</span>
                                </label>
                                <div class="theme-login-input-wrapper">
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        class="theme-login-input"
                                        placeholder="@lang('admin::app.users.login.password')"
                                        required
                                        style="padding-right: 3rem;"
                                    >
                                    <button
                                        type="button"
                                        class="theme-login-password-toggle icon-eye-hide"
                                        id="visibilityIcon"
                                        onclick="switchVisibility()"
                                        aria-label="Toggle password visibility"
                                    ></button>
                                </div>
                                @error('password')
                                    <span class="theme-login-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Actions --}}
                            <div class="theme-login-actions">
                                <a href="{{ route('admin.forgot_password.create') }}" class="theme-login-forgot-link">
                                    @lang('admin::app.users.login.forget-password-link')
                                </a>

                                <button type="submit" class="theme-login-submit">
                                    @lang('admin::app.users.login.submit-btn')
                                </button>
                            </div>
                        </form>

                        {!! view_render_event('admin.sessions.login.form_controls.after') !!}
                    </div>

                    {{-- Help Link (se habilitado) --}}
                    @if($themeContext->hasCustomCard() && $themeContext->login('card_help_link'))
                        <div class="theme-login-help">
                            <a href="mailto:{{ $themeContext->login('card_support_email', 'suporte@empresa.com.br') }}">
                                @lang('Precisa de ajuda? Contate o suporte')
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Powered By --}}
            @if($themeContext->showPoweredBy())
                <div class="theme-login-powered">
                    @lang('admin::app.components.layouts.powered-by.description', [
                        'krayin' => '<a href="https://krayincrm.com/">Krayin</a>',
                        'webkul' => '<a href="https://webkul.com/">Webkul</a>',
                    ])
                </div>
            @endif
        </div>

        {{-- Password Toggle Script --}}
        <script>
            function switchVisibility() {
                const passwordField = document.getElementById("password");
                const visibilityIcon = document.getElementById("visibilityIcon");

                if (passwordField.type === "password") {
                    passwordField.type = "text";
                    visibilityIcon.classList.remove("icon-eye-hide");
                    visibilityIcon.classList.add("icon-eye");
                } else {
                    passwordField.type = "password";
                    visibilityIcon.classList.remove("icon-eye");
                    visibilityIcon.classList.add("icon-eye-hide");
                }
            }
        </script>
    </body>
    </html>
@endif
```

---

### 10. Theme Head Partial (CSS)

**Arquivo:** `resources/views/vendor/admin/partials/theme-head.blade.php`

```blade
{{--
    Theme Head Partial - CSS Variables e Estilos Base
    Injetado no <head> do layout anonymous para temas de login.
--}}

@if($themeContext->enabled)
{{-- CSS Variables --}}
<style id="theme-css-vars">
:root {
    /* Cores do tema */
    --theme-primary: {{ $themeContext->get('color_primary', '#1E40AF') }};
    --theme-primary-dark: {{ $themeContext->get('color_primary_dark', '#1E3A8A') }};
    --theme-primary-light: {{ $themeContext->get('color_primary_light', '#3B82F6') }};
    --theme-success: {{ $themeContext->get('color_success', '#10B981') }};
    --theme-warning: {{ $themeContext->get('color_warning', '#F59E0B') }};
    --theme-danger: {{ $themeContext->get('color_danger', '#EF4444') }};

    /* Login background */
    @if($bgUrl = $themeContext->loginBgUrl())
    --theme-login-bg-url: url('{{ $bgUrl }}');
    @endif
    /* bg_opacity = visibilidade da imagem. 100 = imagem 100% visível, overlay 0%. */
    --theme-login-bg-opacity: {{ 1 - ($themeContext->login('bg_opacity', 50) / 100) }};
    --theme-login-bg-zoom: {{ $themeContext->login('bg_zoom', 100) / 100 }};

    /* Login card */
    @if($themeContext->hasCustomCard())
    @if($cardBgUrl = $themeContext->loginCardBgUrl())
    --theme-login-card-bg-url: url('{{ $cardBgUrl }}');
    @endif
    --theme-login-card-bg-opacity: {{ $themeContext->login('card_bg_opacity', 62) / 100 }};
    --theme-login-card-overlay: {{ $themeContext->login('card_overlay_color', 'rgba(10, 45, 15, 0.78)') }};
    @endif
}
</style>

{{-- Estilos Base do Tema --}}
<style id="theme-login-styles">
/* Background via pseudo-elements */
body.theme-login-bg::before {
    content: '';
    position: fixed;
    inset: 0;
    z-index: 0;
    background-image: var(--theme-login-bg-url);
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    transform: scale(var(--theme-login-bg-zoom, 1));
    transform-origin: center center;
}

/* Dark overlay layer */
body.theme-login-bg::after {
    content: '';
    position: fixed;
    inset: 0;
    z-index: 1;
    background-color: rgba(0, 0, 0, var(--theme-login-bg-opacity, 0.5));
    pointer-events: none;
}

/* Layout principal */
.theme-login-layer {
    position: relative;
    z-index: 10;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

/* Logo */
.theme-login-logo {
    margin-bottom: 2rem;
    text-align: center;
}

.theme-login-logo img {
    max-height: 60px;
    max-width: 200px;
    width: auto;
    height: auto;
}

/* Card de login */
.theme-login-card {
    width: 100%;
    max-width: 400px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    overflow: hidden;
}

/* Card customizado */
body.theme-login-card-custom .theme-login-card {
    position: relative;
    background-color: var(--theme-login-card-overlay, rgba(10, 45, 15, 0.78));
    background-image: var(--theme-login-card-bg-url);
    background-size: cover;
    background-position: center;
    background-blend-mode: overlay;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

body.theme-login-card-custom .theme-login-card-content {
    position: relative;
    z-index: 2;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 12px;
    margin: 0;
}

/* Form elements, buttons, etc... */
/* (código CSS completo no arquivo original) */
</style>

{{-- CSS externo do tema (opcional) --}}
@if($themeCssUrl = $themeContext->cssUrl())
<link rel="stylesheet" href="{{ $themeCssUrl }}">
@endif
@endif
```

---

## Fluxo de Funcionamento

### 1. Usuário acessa `/admin/login`

```
Request → Kernel (middlewares) → ShareThemeContext → View
                                        ↓
                            ThemeContextFactory::make()
                                        ↓
                            Cache hit? → Retorna ThemeContext
                            Cache miss? → buildContext()
                                              ↓
                                    app('theme')->isActive()
                                              ↓
                                    DB::table('theme_configs')->value('selected_theme')
                                              ↓
                                    ThemeContext(enabled, slug, config, loginConfig)
                                              ↓
                            View::share('themeContext', $ctx)
                                              ↓
                            login.blade.php → @if($themeContext->enabled)
                                              ↓
                            Renderiza tema ou padrão
```

### 2. Admin altera tema no Theme Manager

```
POST /admin/settings/theme → CaptureThemeSelection middleware
                                        ↓
                            shouldCapture()? → Verifica rota, método, auth, permission
                                        ↓
                            captureThemeSelection() → Sanitiza slug
                                                   → Valida tema existe
                                                   → DB::table()->update()
                                                   → clearThemeCaches()
                                        ↓
                            Próximo request usa novo tema
```

---

## Configurações

### Tabela `theme_configs`

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `is_active` | boolean | Se o sistema de temas está ativo |
| `selected_theme` | string(50) | Slug do tema selecionado |
| `color_primary` | string | Cor primária (#HEX) |
| `logo_main` | string | Filename do logo principal |
| `login_bg_image` | string | Filename do background do login |
| `login_bg_opacity` | int | Opacidade do background (0-100) |
| `login_card_enabled` | boolean | Se card customizado está ativo |
| ... | ... | ... |

### Diretório de Assets

```
storage/app/public/
├── themes/
│   └── {slug}/
│       ├── theme.json      # Metadados do tema
│       ├── theme.css       # CSS customizado
│       ├── login-bg.jpg    # Background do login
│       └── logo.png        # Logo do tema
└── theme-manager/          # Path legado (fallback)
    └── *.png, *.jpg, etc
```

---

## Como Usar

### Comandos Úteis

```bash
# Limpar caches
php artisan cache:clear
php artisan view:clear
php artisan config:clear

# Rodar migration
php artisan migrate --path=database/migrations/2024_12_23_100000_add_selected_theme_to_theme_configs.php

# Iniciar servidor
php artisan serve --host=127.0.0.1 --port=8080
```

### Criar Novo Tema

1. Criar diretório: `storage/app/public/themes/{slug}/`
2. Adicionar arquivos (theme.css, logo.png, etc)
3. No Theme Manager, selecionar o tema

### Desativar Tema

1. No Theme Manager, desmarcar "is_active"
2. Ou via código: `DB::table('theme_configs')->where('id', 1)->update(['is_active' => 0])`

---

## Considerações de Segurança

1. **Sanitização de slug**: Remove caracteres especiais, força lowercase
2. **Validação de existência**: Tema deve existir no storage
3. **Autenticação**: Verifica guard 'user' do Krayin
4. **Permissão ACL**: Verifica `settings.theme.edit` via Bouncer
5. **Cache específico**: Não usa `cache:clear` global (DoS protection)

---

## Compatibilidade

- **Laravel**: 10.x / 11.x
- **Krayin CRM**: 1.x / 2.x
- **PHP**: 8.1+
- **Upgrade-safe**: Não modifica `packages/Webkul/*`

---

*Documentação gerada em: 2025-12-23*
