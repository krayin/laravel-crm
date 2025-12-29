# BrandKit - Evolução do Sistema

Documento técnico-narrativo que conta a história do desenvolvimento do BrandKit, desde o Krayin original até o estado atual. Serve como onboarding para novos desenvolvedores e referência técnica.

---

## Índice

1. [O Ponto de Partida: Krayin Original](#1-o-ponto-de-partida-krayin-original)
2. [O Problema: Por que precisávamos do BrandKit?](#2-o-problema-por-que-precisávamos-do-brandkit)
3. [Fase 1: Estrutura de Banco de Dados](#3-fase-1-estrutura-de-banco-de-dados)
4. [Fase 2: Models e Relacionamentos](#4-fase-2-models-e-relacionamentos)
5. [Fase 3: O Coração - BrandKitResolver](#5-fase-3-o-coração---brandkitresolver)
6. [Fase 4: Repository Pattern](#6-fase-4-repository-pattern)
7. [Fase 5: Validação de CSS (Segurança)](#7-fase-5-validação-de-css-segurança)
8. [Fase 6: Theme Selection e Context](#8-fase-6-theme-selection-e-context)
9. [Fase 7: Controllers e Rotas](#9-fase-7-controllers-e-rotas)
10. [Fase 8: Injeção no Frontend](#10-fase-8-injeção-no-frontend)
11. [Estado Atual e Próximos Passos](#11-estado-atual-e-próximos-passos)
12. [Glossário](#12-glossário)

---

## 1. O Ponto de Partida: Krayin Original

### O que é o Krayin?

Krayin é um CRM open-source construído em Laravel. O projeto original vem do repositório:
```
https://github.com/krayin/laravel-crm
```

### Estrutura Original (antes do BrandKit)

```
laravel-crm/
├── app/
│   ├── Http/Controllers/      # Controllers do admin
│   ├── Models/                # Eloquent models
│   └── Providers/             # Service providers
├── config/                    # Configurações Laravel
├── database/migrations/       # Migrations do CRM
├── packages/                  # Packages internos do Krayin
│   └── Webkul/
│       └── Admin/             # Package do painel admin
├── resources/views/           # Views Blade
└── routes/                    # Definições de rotas
```

### Como era a customização visual ANTES?

**Resposta curta:** Não havia sistema de customização.

Para mudar cores ou logos, você precisava:
1. Editar arquivos CSS diretamente
2. Modificar views Blade
3. Fazer rebuild do Vite
4. Perder tudo em updates do Krayin

```css
/* Antes: cores hardcoded em CSS */
.btn-primary {
    background-color: #0284C7;  /* Azul fixo do Krayin */
}

.sidebar .active {
    background-color: #0369A1;  /* Impossível mudar sem editar código */
}
```

### O Fork

Criamos um fork do Krayin para adicionar nossas customizações:
```
origin  → https://github.com/krayin/laravel-crm.git     (upstream)
myfork  → https://github.com/vitorbb1989/Krayingproject.git  (nosso)
```

---

## 2. O Problema: Por que precisávamos do BrandKit?

### Requisitos do Negócio

1. **Clientes querem suas cores** - Cada empresa quer o CRM com sua identidade visual
2. **Logos customizados** - Logo na sidebar, favicon, tela de login
3. **Sem código** - Usuário admin deve conseguir mudar sem desenvolvedor
4. **Reversível** - Se quebrar, deve poder voltar ao estado anterior
5. **Upgrade-safe** - Atualizações do Krayin não podem quebrar customizações

### Requisitos Técnicos

1. **Não modificar core** - Usar overrides, não patches
2. **Performance** - Cache agressivo, não consultar DB em cada request
3. **Segurança** - CSS de usuário não pode injetar JavaScript
4. **Multi-tenant ready** - Preparado para diferentes configs por cliente (futuro)

### A Solução: BrandKit

```
┌─────────────────────────────────────────────────────────────┐
│                         BRANDKIT                             │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│   ┌─────────────┐     ┌─────────────┐     ┌─────────────┐  │
│   │   PRESET    │ ──► │  OVERRIDES  │ ──► │   OUTPUT    │  │
│   │ (theme.json)│     │    (DB)     │     │ (CSS vars)  │  │
│   └─────────────┘     └─────────────┘     └─────────────┘  │
│                                                              │
│   Cores padrão    +   Customizações   =   Visual final      │
│   do tema              do cliente          renderizado       │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

---

## 3. Fase 1: Estrutura de Banco de Dados

### Por que banco de dados?

Opções consideradas:
- ❌ Arquivos JSON - Difícil gerenciar, sem histórico
- ❌ .env - Requer deploy para mudar
- ✅ Banco de dados - CRUD fácil, histórico, backup, queries

### Migrations Criadas

**Arquivo:** `database/migrations/2025_12_25_205615_create_brand_kit_overrides_table.php`

```php
Schema::create("brand_kit_overrides", function (Blueprint $table) {
    $table->id();
    
    // Multi-tenant ready: permite diferentes configs por "scope"
    $table->string("scope_key", 50)->default("global");
    $table->string("theme_slug", 50)->default("default");
    
    // A chave do override (ex: 'color_primary', 'logo_main')
    $table->string("override_key", 100);
    
    // O valor customizado
    $table->text("value")->nullable();
    
    // Permite "desativar" sem deletar
    $table->boolean("is_active")->default(true);
    
    // Auditoria: quem alterou
    $table->unsignedBigInteger("updated_by")->nullable();
    
    $table->timestamps();
    
    // Índices para performance
    $table->unique(["scope_key", "theme_slug", "override_key"], "bko_unique");
    $table->index(["scope_key", "theme_slug", "is_active"], "bko_lookup");
});
```

**O que cada campo significa:**

| Campo | Propósito | Exemplo |
|-------|-----------|---------|
| `scope_key` | Separação por tenant/cliente | `"global"`, `"empresa_123"` |
| `theme_slug` | Qual tema base está sendo customizado | `"default"`, `"dark"` |
| `override_key` | Qual propriedade está sendo alterada | `"color_primary"` |
| `value` | O valor customizado | `"#FF5733"` |
| `is_active` | Se o override está ativo | `true`/`false` |

**Arquivo:** `database/migrations/2025_12_25_205628_create_brand_kit_custom_css_table.php`

```php
Schema::create("brand_kit_custom_css", function (Blueprint $table) {
    $table->id();
    
    $table->string("scope_key", 50)->default("global");
    $table->string("theme_slug", 50)->default("default");
    
    // Nome amigável para o usuário
    $table->string("name", 100);
    
    // CSS customizado (até 16MB)
    $table->mediumText("css_content");
    
    // Onde aplicar: admin, login, ou ambos
    $table->string("target", 20)->default("admin");
    
    // Segurança: desabilitado por padrão
    $table->boolean("is_enabled")->default(false);
    
    // Ordem de aplicação
    $table->integer("priority")->default(100);
    
    $table->unsignedBigInteger("created_by")->nullable();
    $table->timestamps();
});
```

**Arquivo:** `database/migrations/2025_12_25_205637_create_brand_kit_snapshots_table.php`

```php
Schema::create("brand_kit_snapshots", function (Blueprint $table) {
    $table->id();
    
    $table->string("scope_key", 50)->default("global");
    $table->string("theme_slug", 50);
    $table->string("name", 100);
    
    // Versão do formato (para migrações futuras)
    $table->unsignedSmallInteger("snapshot_version")->default(1);
    
    // Estado completo serializado como JSON
    $table->json("overrides_data");
    $table->json("custom_css_data")->nullable();
    
    // Diferenciar manual vs automático
    $table->boolean("is_auto")->default(false);
    
    $table->unsignedBigInteger("created_by")->nullable();
    $table->timestamps();
});
```

### Diagrama ER

```
┌─────────────────────────┐
│   brand_kit_overrides   │
├─────────────────────────┤
│ id                      │
│ scope_key ──────────────┼──┐
│ theme_slug ─────────────┼──┤
│ override_key            │  │
│ value                   │  │
│ is_active               │  │
│ updated_by ─────────────┼──┼──► users.id
│ created_at              │  │
│ updated_at              │  │
└─────────────────────────┘  │
                             │
┌─────────────────────────┐  │
│   brand_kit_custom_css  │  │
├─────────────────────────┤  │
│ id                      │  │
│ scope_key ──────────────┼──┤  (mesmo scope/theme)
│ theme_slug ─────────────┼──┤
│ name                    │  │
│ css_content             │  │
│ target                  │  │
│ is_enabled              │  │
│ priority                │  │
│ created_by ─────────────┼──┼──► users.id
│ created_at              │  │
│ updated_at              │  │
└─────────────────────────┘  │
                             │
┌─────────────────────────┐  │
│   brand_kit_snapshots   │  │
├─────────────────────────┤  │
│ id                      │  │
│ scope_key ──────────────┼──┘
│ theme_slug ─────────────┼────
│ name                    │
│ snapshot_version        │
│ overrides_data (JSON)   │
│ custom_css_data (JSON)  │
│ is_auto                 │
│ created_by              │
│ created_at              │
│ updated_at              │
└─────────────────────────┘
```

---

## 4. Fase 2: Models e Relacionamentos

### BrandKitOverride Model

**Arquivo:** `app/Models/BrandKitOverride.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\User\Models\User;

class BrandKitOverride extends Model
{
    protected $table = "brand_kit_overrides";

    protected $fillable = [
        "scope_key",
        "theme_slug",
        "override_key",
        "value",
        "is_active",
        "updated_by",
    ];

    protected $casts = [
        "is_active" => "boolean",
    ];

    // Relacionamento com usuário que alterou
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "updated_by");
    }

    /**
     * Método estático para buscar overrides ativos.
     * Usado pelo BrandKitResolver para montar config final.
     * 
     * Retorna: ['color_primary' => '#FF5733', 'logo_main' => 'path/to/logo.png']
     */
    public static function getActiveOverrides(
        string $scopeKey,
        string $themeSlug,
    ): array {
        return static::query()
            ->where("scope_key", $scopeKey)
            ->where("theme_slug", $themeSlug)
            ->where("is_active", true)
            ->whereNotNull("value")
            ->where("value", "!=", "")
            ->pluck("value", "override_key")
            ->toArray();
    }
}
```

**Por que `getActiveOverrides` é estático?**

Performance. O Resolver precisa chamar isso frequentemente. Método estático evita instanciar model desnecessariamente.

### BrandKitCustomCss Model

**Arquivo:** `app/Models/BrandKitCustomCss.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandKitCustomCss extends Model
{
    protected $table = "brand_kit_custom_css";

    protected $fillable = [
        "scope_key",
        "theme_slug",
        "name",
        "css_content",
        "target",
        "is_enabled",
        "priority",
        "created_by",
    ];

    protected $casts = [
        "is_enabled" => "boolean",
        "priority" => "integer",
    ];

    /**
     * Retorna CSS concatenado para um target.
     * Exemplo: getEnabledCss('global', 'default', 'admin')
     * 
     * Inclui CSS com target='admin' E target='both'
     */
    public static function getEnabledCss(
        string $scopeKey,
        string $themeSlug,
        string $target,
    ): string {
        return static::query()
            ->where("scope_key", $scopeKey)
            ->where("theme_slug", $themeSlug)
            ->where("is_enabled", true)
            ->where(function ($q) use ($target) {
                $q->where("target", $target)
                  ->orWhere("target", "both");
            })
            ->orderBy("priority")  // Menor priority = aplica primeiro
            ->pluck("css_content")
            ->implode("\n\n");     // Junta todos os CSS
    }
}
```

### BrandKitSnapshot Model

**Arquivo:** `app/Models/BrandKitSnapshot.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandKitSnapshot extends Model
{
    protected $table = "brand_kit_snapshots";

    protected $fillable = [
        "scope_key",
        "theme_slug",
        "name",
        "snapshot_version",
        "overrides_data",
        "custom_css_data",
        "is_auto",
        "created_by",
    ];

    // JSON é automaticamente convertido para array
    protected $casts = [
        "overrides_data" => "array",
        "custom_css_data" => "array",
        "is_auto" => "boolean",
        "snapshot_version" => "integer",
    ];
}
```

**O que é um Snapshot?**

Pense como um "Save Game". Antes de fazer alterações arriscadas, salvamos o estado atual. Se der errado, restauramos o snapshot.

```
Snapshot #1: "Antes de mudar cores" (manual)
├── overrides_data: [{key: 'color_primary', value: '#0284C7'}, ...]
└── custom_css_data: [{name: 'Meu CSS', css_content: '...'}]

Snapshot #2: "[AUTO] Antes de resetAllOverrides" (automático)
├── overrides_data: [...]
└── custom_css_data: [...]
```

---

## 5. Fase 3: O Coração - BrandKitResolver

### O que é o Resolver?

É a classe que **resolve** a configuração final. Ele:
1. Lê o preset (theme.json)
2. Aplica overrides do banco
3. Retorna a config final
4. Cacheia o resultado

**Arquivo:** `app/Support/BrandKitResolver.php`

### Cadeia de Resolução

```
┌─────────────────┐
│    DEFAULTS     │  ← Valores hardcoded no código (fallback final)
│  (código PHP)   │
└────────┬────────┘
         │ merge
         ▼
┌─────────────────┐
│     PRESET      │  ← Arquivo theme.json do tema
│  (theme.json)   │
└────────┬────────┘
         │ merge
         ▼
┌─────────────────┐
│    OVERRIDES    │  ← Customizações do usuário (banco)
│      (DB)       │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  CONFIG FINAL   │  ← O que é usado para renderizar
└─────────────────┘
```

### Código Simplificado

```php
class BrandKitResolver
{
    // Valores padrão (último recurso)
    private const DEFAULTS = [
        "color_primary" => "#0284C7",
        "color_primary_dark" => "#0369A1",
        "color_success" => "#16A34A",
        "logo_main" => null,
        // ...
    ];

    // Mapeamento: chave nested do JSON → chave flat interna
    private const KEY_MAP = [
        "tokens.primary" => "color_primary",
        "assets.logo_main" => "logo_main",
        // ...
    ];

    public function resolve(string $scopeKey, string $themeSlug): array
    {
        // 1. Tentar cache primeiro
        $cacheKey = "brand_kit.resolved.v1.{$scopeKey}.{$themeSlug}";
        
        return Cache::remember($cacheKey, 3600, function () use ($scopeKey, $themeSlug) {
            
            // 2. Carregar preset do tema (theme.json)
            $preset = $this->loadPreset($themeSlug);
            
            // 3. Carregar overrides do banco
            $overrides = BrandKitOverride::getActiveOverrides($scopeKey, $themeSlug);
            
            // 4. Merge: DEFAULTS ← preset ← overrides
            $config = array_merge(
                self::DEFAULTS,
                $preset,
                $overrides
            );
            
            // 5. Carregar CSS customizado
            $cssAdmin = BrandKitCustomCss::getEnabledCss($scopeKey, $themeSlug, 'admin');
            $cssLogin = BrandKitCustomCss::getEnabledCss($scopeKey, $themeSlug, 'login');
            
            return [
                'config' => $config,
                'custom_css_admin' => $cssAdmin,
                'custom_css_login' => $cssLogin,
                'theme_slug' => $themeSlug,
                'scope_key' => $scopeKey,
            ];
        });
    }

    public function invalidate(string $scopeKey, string $themeSlug): void
    {
        Cache::forget("brand_kit.resolved.v1.{$scopeKey}.{$themeSlug}");
    }
}
```

### Exemplo Prático

```php
// theme.json (preset)
{
    "tokens": {
        "primary": "#0284C7"
    }
}

// Banco de dados (override)
| override_key   | value    | is_active |
|----------------|----------|-----------|
| color_primary  | #FF5733  | true      |

// Resultado do resolve()
[
    'config' => [
        'color_primary' => '#FF5733',  // ← Override venceu!
        // ...
    ]
]
```

### Por que Cache?

Sem cache:
```
Request → Query DB → Parse JSON → Merge → Render
         ~50ms      ~10ms       ~5ms
         
100 requests = 6.5 segundos de overhead
```

Com cache:
```
Request → Cache Hit → Render
          ~1ms
          
100 requests = 100ms de overhead
```

**Quando o cache é invalidado?**
- Toda vez que um override é salvo/deletado
- Toda vez que CSS é adicionado/alterado
- Manualmente via endpoint `/cache/invalidate`

---

## 6. Fase 4: Repository Pattern

### Por que Repository?

Regra de ouro: **Controller não faz query direta no banco.**

```
❌ ERRADO:
Controller → Model → Banco

✅ CERTO:
Controller → Repository → Model → Banco
```

Benefícios:
1. **Testabilidade** - Pode mockar o repository
2. **Centralização** - Toda lógica de persistência em um lugar
3. **Segurança** - Repository valida antes de salvar
4. **Auditoria** - Um lugar para adicionar logs

### BrandKitRepository

**Arquivo:** `app/Support/BrandKitRepository.php`

```php
class BrandKitRepository
{
    public function __construct(
        private BrandKitResolver $resolver,
        private CssValidator $cssValidator,
    ) {}

    /**
     * Define ou atualiza um override.
     * Se value for null/vazio, desativa o override (fallback para preset).
     */
    public function setOverride(
        string $scopeKey,
        string $themeSlug,
        string $overrideKey,
        ?string $value,
        ?int $userId = null,
    ): BrandKitOverride {
        $override = BrandKitOverride::updateOrCreate(
            [
                'scope_key' => $scopeKey,
                'theme_slug' => $themeSlug,
                'override_key' => $overrideKey,
            ],
            [
                'value' => $value,
                'is_active' => !empty($value),  // Null/vazio = desativa
                'updated_by' => $userId,
            ],
        );

        // IMPORTANTE: Invalidar cache após alteração
        $this->resolver->invalidate($scopeKey, $themeSlug);

        return $override;
    }

    /**
     * Salva CSS customizado.
     * VALIDA antes de salvar (segurança).
     */
    public function setCustomCss(
        string $scopeKey,
        string $themeSlug,
        string $target,
        string $css,
        // ...
    ): ?BrandKitCustomCss {
        // Validação de segurança
        if (!$this->cssValidator->isValid($css)) {
            return null;  // CSS rejeitado
        }

        $entry = BrandKitCustomCss::updateOrCreate(/* ... */);
        
        $this->resolver->invalidate($scopeKey, $themeSlug);
        
        return $entry;
    }

    /**
     * Cria snapshot do estado atual.
     */
    public function createSnapshot(
        string $scopeKey,
        string $themeSlug,
        string $name,
        ?int $userId = null,
    ): BrandKitSnapshot {
        // Captura estado atual
        $overrides = BrandKitOverride::query()
            ->where('scope_key', $scopeKey)
            ->where('theme_slug', $themeSlug)
            ->get()
            ->toArray();

        $customCss = BrandKitCustomCss::query()
            ->where('scope_key', $scopeKey)
            ->where('theme_slug', $themeSlug)
            ->get()
            ->toArray();

        return BrandKitSnapshot::create([
            'scope_key' => $scopeKey,
            'theme_slug' => $themeSlug,
            'name' => $name,
            'overrides_data' => $overrides,
            'custom_css_data' => $customCss,
            'created_by' => $userId,
        ]);
    }

    /**
     * Restaura um snapshot.
     * Usa transaction para garantir atomicidade.
     */
    public function restoreSnapshot(int $snapshotId): bool
    {
        $snapshot = BrandKitSnapshot::find($snapshotId);
        if (!$snapshot) return false;

        DB::transaction(function () use ($snapshot) {
            // Limpa dados atuais
            BrandKitOverride::where('scope_key', $snapshot->scope_key)
                ->where('theme_slug', $snapshot->theme_slug)
                ->delete();

            BrandKitCustomCss::where('scope_key', $snapshot->scope_key)
                ->where('theme_slug', $snapshot->theme_slug)
                ->delete();

            // Restaura do snapshot
            foreach ($snapshot->overrides_data as $row) {
                unset($row['id'], $row['created_at'], $row['updated_at']);
                BrandKitOverride::create($row);
            }

            foreach ($snapshot->custom_css_data ?? [] as $row) {
                unset($row['id'], $row['created_at'], $row['updated_at']);
                BrandKitCustomCss::create($row);
            }
        });

        $this->resolver->invalidate($snapshot->scope_key, $snapshot->theme_slug);

        return true;
    }
}
```

### Fluxo Completo: Salvar Override

```
1. Usuário clica "Salvar" na UI
          │
          ▼
2. Controller recebe request
   BrandKitController::storeOverride()
          │
          ▼
3. FormRequest valida input
   SetOverrideRequest::rules()
   - key: required, regex válido
   - value: nullable, max:1000
          │
          ▼
4. Controller chama Repository
   $this->repository->setOverride(...)
          │
          ▼
5. Repository salva no banco
   BrandKitOverride::updateOrCreate(...)
          │
          ▼
6. Repository invalida cache
   $this->resolver->invalidate(...)
          │
          ▼
7. Controller retorna resposta
   JSON ou redirect
          │
          ▼
8. Próximo request usa valor novo
   (cache foi invalidado, será reconstruído)
```

---

## 7. Fase 5: Validação de CSS (Segurança)

### O Risco

CSS de usuário pode conter vetores de ataque:

```css
/* Injeção de JavaScript via CSS (IE antigo) */
body {
    background: expression(alert('XSS'));
}

/* Carregamento de recursos externos */
@import url('https://evil.com/track.css');

/* Data URLs com JavaScript */
div {
    background: url('data:text/html,<script>alert(1)</script>');
}
```

### CssValidator

**Arquivo:** `app/Support/CssValidator.php`

```php
final class CssValidator
{
    /**
     * Padrões bloqueados - NUNCA permitir estes no CSS
     */
    private const BLOCKED_PATTERNS = [
        '/@import\b/i',                              // @import qualquer
        '/@charset\b/i',                             // @charset
        '/@namespace\b/i',                           // @namespace
        '/expression\s*\(/i',                        // IE expression()
        '/behavior\s*:/i',                           // IE behavior
        '/-moz-binding\s*:/i',                       // Firefox XBL
        '/url\s*\(\s*["\']?\s*javascript\s*:/i',     // url(javascript:)
        '/url\s*\(\s*["\']?\s*data\s*:/i',           // url(data:)
    ];

    private const MAX_SIZE = 50 * 1024; // 50KB

    /**
     * Valida CSS e retorna array de erros.
     * Array vazio = CSS válido.
     */
    public function validate(string $css): array
    {
        $errors = [];

        if (strlen($css) > self::MAX_SIZE) {
            $errors[] = 'CSS excede o tamanho máximo de 50KB.';
        }

        foreach (self::BLOCKED_PATTERNS as $pattern) {
            if (preg_match($pattern, $css)) {
                $errors[] = 'CSS contém conteúdo não permitido.';
                break;
            }
        }

        return $errors;
    }

    public function isValid(string $css): bool
    {
        return empty($this->validate($css));
    }

    /**
     * Sanitiza CSS removendo padrões perigosos.
     * Usar DEPOIS de validar (defesa em profundidade).
     */
    public function sanitize(string $css): string
    {
        $out = $css;
        foreach (self::BLOCKED_PATTERNS as $pattern) {
            $out = preg_replace($pattern, '/* blocked */', $out);
        }
        return $out;
    }
}
```

### Dupla Proteção

```
CSS do usuário
      │
      ▼
┌─────────────────┐
│   VALIDAÇÃO     │  ← Rejeita se inválido (400 Bad Request)
│  (FormRequest)  │
└────────┬────────┘
         │ se passou
         ▼
┌─────────────────┐
│  SANITIZAÇÃO    │  ← Remove padrões perigosos (defesa extra)
│  (Repository)   │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│    BANCO        │  ← Salvo já sanitizado
└─────────────────┘
```

---

## 8. Fase 6: Theme Selection e Context

### ThemeSelectionResolver

Determina qual tema está ativo no momento.

**Arquivo:** `app/Support/ThemeSelectionResolver.php`

```php
class ThemeSelectionResolver
{
    private const CACHE_KEY = 'theme.selected_slug.v1';
    private const TTL = 300; // 5 minutos

    public function getSelectedThemeSlug(): string
    {
        return Cache::remember(self::CACHE_KEY, self::TTL, function () {
            // 1. Tentar banco (theme_configs.selected_theme)
            try {
                $row = DB::table('theme_configs')->where('id', 1)->first();
                if ($row && $row->is_active && !empty($row->selected_theme)) {
                    return $this->sanitizeSlug($row->selected_theme);
                }
            } catch (\Throwable $e) {
                // Tabela pode não existir ainda
            }

            // 2. Fallback para config
            $cfg = config('theme.current', '');
            if (!empty($cfg)) {
                return $this->sanitizeSlug($cfg);
            }

            // 3. Fallback final
            return 'default';
        });
    }
}
```

### ThemeContextFactory

Cria o objeto ThemeContext que será usado nas views.

**Arquivo:** `app/Support/ThemeContextFactory.php`

```php
class ThemeContextFactory
{
    public function __construct(
        private BrandKitResolver $brandKitResolver,
        private ThemeSelectionResolver $themeSelectionResolver,
    ) {}

    public function create(?Request $request = null): ThemeContext
    {
        // Determinar scope e tema
        $scopeKey = 'global';  // Por enquanto sempre global
        $themeSlug = $this->resolveThemeSlug($request);
        
        // Buscar config do BrandKit (já resolvida)
        $brandKit = $this->brandKitResolver->resolve($scopeKey, $themeSlug);

        return new ThemeContext(
            enabled: true,
            slug: $themeSlug,
            scopeKey: $scopeKey,
            config: $brandKit['config'],
            customCssAdmin: $brandKit['custom_css_admin'],
            customCssLogin: $brandKit['custom_css_login'],
            // ...
        );
    }

    private function resolveThemeSlug(?Request $request): string
    {
        // Preview mode (session) tem prioridade
        if ($request && session()->has('theme_preview')) {
            return session('theme_preview');
        }

        // Tema persistido
        return $this->themeSelectionResolver->getSelectedThemeSlug();
    }
}
```

### ThemeContext (Value Object)

```php
readonly class ThemeContext
{
    public function __construct(
        public bool $enabled,
        public string $slug,
        public string $scopeKey,
        public array $config,
        public string $customCssAdmin,
        public string $customCssLogin,
        // ...
    ) {}

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }

    public function cssVariables(): string
    {
        // Gera CSS variables a partir da config
        $css = ':root {';
        foreach ($this->config as $key => $value) {
            if (str_starts_with($key, 'color_')) {
                $css .= "--{$key}: {$value};";
            }
        }
        return $css . '}';
    }
}
```

---

## 9. Fase 7: Controllers e Rotas

### Estrutura de Controllers

Existem atualmente 2 controllers (duplicidade a ser resolvida):

**Controller Completo (17 métodos):**
`app/Http/Controllers/BrandKitController.php`

```php
class BrandKitController extends Controller
{
    public function __construct(
        private BrandKitRepository $repository,
        private BrandKitResolver $resolver,
    ) {}

    // Leitura
    public function config(Request $request): JsonResponse { ... }
    public function themes(): JsonResponse { ... }
    public function overrides(Request $request): JsonResponse { ... }
    public function customCss(Request $request): JsonResponse { ... }
    public function snapshots(Request $request): JsonResponse { ... }

    // Escrita - Overrides
    public function storeOverride(Request $request): JsonResponse { ... }
    public function batchOverrides(Request $request): JsonResponse { ... }
    public function deleteOverride(Request $request, string $key): JsonResponse { ... }

    // Escrita - CSS
    public function storeCss(Request $request): JsonResponse { ... }
    public function toggleCss(int $id): JsonResponse { ... }
    public function deleteCss(int $id): JsonResponse { ... }

    // Escrita - Snapshots
    public function createSnapshot(Request $request): JsonResponse { ... }
    public function restoreSnapshot(Request $request, int $id): JsonResponse { ... }
    public function deleteSnapshot(int $id): JsonResponse { ... }

    // Utilitários
    public function reset(Request $request): JsonResponse { ... }
    public function preview(Request $request): JsonResponse { ... }
    public function invalidateCache(Request $request): JsonResponse { ... }
}
```

### Rotas

**Arquivo:** `routes/brand-kit.php`

```php
Route::prefix(config('app.admin_path', 'admin'))
    ->middleware(['web', 'user'])
    ->group(function () {
        Route::prefix('brand-kit')
            ->name('admin.brand-kit.')
            ->group(function () {
                // Leitura
                Route::get('/config', [BrandKitController::class, 'config']);
                Route::get('/themes', [BrandKitController::class, 'themes']);
                Route::get('/overrides', [BrandKitController::class, 'overrides']);
                Route::get('/css', [BrandKitController::class, 'customCss']);
                Route::get('/snapshots', [BrandKitController::class, 'snapshots']);

                // Overrides
                Route::post('/overrides', [BrandKitController::class, 'storeOverride']);
                Route::post('/overrides/batch', [BrandKitController::class, 'batchOverrides']);
                Route::delete('/overrides/{key}', [BrandKitController::class, 'deleteOverride']);

                // CSS
                Route::post('/css', [BrandKitController::class, 'storeCss']);
                Route::patch('/css/{id}/toggle', [BrandKitController::class, 'toggleCss']);
                Route::delete('/css/{id}', [BrandKitController::class, 'deleteCss']);

                // Snapshots
                Route::post('/snapshots', [BrandKitController::class, 'createSnapshot']);
                Route::post('/snapshots/{id}/restore', [BrandKitController::class, 'restoreSnapshot']);
                Route::delete('/snapshots/{id}', [BrandKitController::class, 'deleteSnapshot']);

                // Utilitários
                Route::post('/reset', [BrandKitController::class, 'reset']);
                Route::post('/preview', [BrandKitController::class, 'preview']);
                Route::post('/cache/invalidate', [BrandKitController::class, 'invalidateCache']);
            });
    });
```

### Form Requests

Validação de entrada nos endpoints.

**SetOverrideRequest:**
```php
public function rules(): array
{
    return [
        'scope_key' => 'nullable|string|max:50',
        'theme_slug' => 'nullable|string|max:50',
        'override_key' => 'required|string|max:100',
        'value' => 'nullable|string|max:1000',
    ];
}
```

**RestoreSnapshotRequest:**
```php
public function rules(): array
{
    return [
        'snapshot_id' => [
            'required',
            'integer',
            'exists:brand_kit_snapshots,id',  // Valida que existe no banco
        ],
    ];
}
```

---

## 10. Fase 8: Injeção no Frontend

### Onde o CSS é Injetado

**Arquivo:** `resources/views/vendor/admin/partials/theme-head.blade.php`

```blade
@if (isset($themeContext) && $themeContext->enabled)

{{-- CSS Variables do BrandKit --}}
<style id="brand-kit-css-vars">
:root {
    --theme-primary: {{ $themeContext->get('color_primary', '#1E40AF') }};
    --theme-primary-dark: {{ $themeContext->get('color_primary_dark', '#1E3A8A') }};
    --theme-success: {{ $themeContext->get('color_success', '#10B981') }};
    --theme-warning: {{ $themeContext->get('color_warning', '#F59E0B') }};
    --theme-danger: {{ $themeContext->get('color_danger', '#EF4444') }};
}
</style>

{{-- CSS Customizado (admin ou login) --}}
@php
    $isLogin = str_contains(url()->current(), '/login');
    $customCss = $isLogin 
        ? $themeContext->customCssLogin 
        : $themeContext->customCssAdmin;
@endphp

@if (!empty($customCss))
<style id="brand-kit-custom-css">
{!! $customCss !!}
</style>
@endif

@endif
```

### Como o CSS é Usado

```css
/* Antes: cor hardcoded */
.btn-primary {
    background-color: #0284C7;
}

/* Depois: usa variável CSS */
.btn-primary {
    background-color: var(--theme-primary);
}
```

Quando o usuário muda a cor:
1. Valor salvo no banco: `color_primary = '#FF5733'`
2. Resolver gera: `--theme-primary: #FF5733;`
3. CSS do botão automaticamente usa a nova cor

---

## 11. Estado Atual e Próximos Passos

### O que está funcionando

| Funcionalidade | Status |
|----------------|--------|
| Migrations | ✅ Criadas |
| Models | ✅ Funcionando |
| BrandKitResolver | ✅ Com cache |
| BrandKitRepository | ✅ CRUD completo |
| CssValidator | ✅ Segurança OK |
| ThemeSelectionResolver | ✅ Funcionando |
| Controller (API) | ✅ 17 endpoints |
| Rotas | ✅ Definidas |
| Injeção CSS | ✅ No <head> |

### Problemas Conhecidos (Issues Abertas)

| Issue | Severidade | Descrição |
|-------|------------|-----------|
| #001 | Crítica | Arquivos criados no diretório errado |
| #002 | Alta | 2 Controllers duplicados |
| #003 | Alta | 2 arquivos de rotas |
| #004 | Alta | Container registra Repository errado |
| #007 | Média | CSS sem escopo (#brand-kit-scope) |

Ver `docs/INCIDENT_LOG.md` para detalhes.

### Próximos Passos Planejados

1. **Curto prazo:**
   - [ ] Consolidar controllers (escolher 1)
   - [ ] Consolidar rotas (escolher 1 arquivo)
   - [ ] Corrigir binding do container
   - [ ] Adicionar escopo CSS

2. **Médio prazo:**
   - [ ] UI de administração (Blade/Livewire)
   - [ ] Upload de logos
   - [ ] Preview em tempo real

3. **Longo prazo:**
   - [ ] Multi-tenant (scope por empresa)
   - [ ] Temas no marketplace
   - [ ] Editor visual drag-and-drop

---

## 12. Glossário

| Termo | Definição |
|-------|-----------|
| **BrandKit** | Sistema de customização visual do Krayin |
| **Override** | Valor que sobrescreve o padrão do tema |
| **Preset** | Configuração base de um tema (theme.json) |
| **Scope** | Contexto de aplicação (global, empresa, usuário) |
| **Snapshot** | Backup do estado do BrandKit em um momento |
| **Resolver** | Classe que monta a config final (preset + overrides) |
| **Repository** | Classe que gerencia persistência (CRUD) |
| **ThemeContext** | Objeto com config resolvida, passado para views |
| **CSS Variables** | Variáveis CSS (--nome: valor) usadas para theming |
| **Sanitização** | Limpeza de conteúdo potencialmente perigoso |

---

## Referências

- [Krayin CRM](https://github.com/krayin/laravel-crm)
- [Laravel Documentation](https://laravel.com/docs)
- [CSS Custom Properties](https://developer.mozilla.org/en-US/docs/Web/CSS/--*)

---

*Documento criado em: 2025-12-27*  
*Última atualização: 2025-12-27*  
*Autor: Equipe de Desenvolvimento*
