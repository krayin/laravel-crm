=================================================================================
📚 BIBLIOTECA DE SNIPPETS REUTILIZÁVEIS
=================================================================================
Padrões testados e aprovados para passar dados do PHP ao JavaScript via Blade
=================================================================================

## 1️⃣ PADRÃO BÁSICO - Array Simples

### Controller
```php
public function index()
{
    $items = [
        ['id' => 1, 'name' => 'Item 1'],
        ['id' => 2, 'name' => 'Item 2']
    ];
    
    $itemsForJs = $items; // Passar direto se estrutura já está correta
    
    return view('view', compact('itemsForJs'));
}
```

### Blade
```blade
<script>
    const items = {!! json_encode($itemsForJs) !!};
    console.log(items);
</script>
```

=================================================================================

## 2️⃣ PADRÃO INTERMEDIÁRIO - Transformação de Dados

### Controller
```php
public function index()
{
    $rawData = $this->repository->getAll();
    
    // Transformar para formato adequado ao JavaScript
    $dataForJs = [];
    foreach ($rawData as $item) {
        $dataForJs[$item->id] = [
            'id' => $item->id,
            'name' => $item->name,
            'active' => (bool) $item->active,
            'metadata' => json_decode($item->metadata, true) ?? []
        ];
    }
    
    return view('view', compact('dataForJs'));
}
```

### Blade
```blade
<script>
    window.appData = {!! json_encode($dataForJs) !!};
</script>
```

=================================================================================

## 3️⃣ PADRÃO AVANÇADO - Com Fallbacks e Validação

### Controller
```php
public function index()
{
    $themes = $this->getThemes();
    
    $themesForJs = [];
    foreach ($themes as $theme) {
        // Validar estrutura
        if (!isset($theme['slug'])) {
            \Log::warning('Theme without slug', ['theme' => $theme]);
            continue;
        }
        
        $themesForJs[$theme['slug']] = [
            'slug' => $theme['slug'],
            'name' => $theme['name'] ?? 'Unnamed',
            'config' => $theme['config'] ?? $this->getDefaultConfig(),
            'colors' => $theme['colors'] ?? [
                'primary' => '#1E40AF',
                'secondary' => '#64748B'
            ]
        ];
    }
    
    return view('view', compact('themesForJs'));
}

private function getDefaultConfig(): array
{
    return [
        'enabled' => true,
        'options' => []
    ];
}
```

### Blade
```blade
<script>
(function() {
    'use strict';
    
    window.themes = {!! json_encode($themesForJs) !!};
    
    // Validação no JavaScript
    if (!window.themes || Object.keys(window.themes).length === 0) {
        console.error('No themes loaded');
        return;
    }
    
    console.log('Loaded themes:', Object.keys(window.themes));
})();
</script>
```

=================================================================================

## 4️⃣ PADRÃO COM DTO (Data Transfer Object)

### DTO Class
```php
namespace App\DataTransferObjects;

class ThemeViewData
{
    public string $slug;
    public string $name;
    public array $colors;
    public bool $isActive;
    
    public static function fromModel($theme): self
    {
        $dto = new self();
        $dto->slug = $theme['slug'] ?? 'unknown';
        $dto->name = $theme['name'] ?? 'Unnamed';
        $dto->colors = $theme['colors'] ?? self::defaultColors();
        $dto->isActive = $theme['is_active'] ?? false;
        return $dto;
    }
    
    public function toArray(): array
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name,
            'colors' => $this->colors,
            'isActive' => $this->isActive
        ];
    }
    
    private static function defaultColors(): array
    {
        return [
            'primary' => '#1E40AF',
            'secondary' => '#64748B'
        ];
    }
}
```

### Controller
```php
use App\DataTransferObjects\ThemeViewData;

public function index()
{
    $rawThemes = $this->repository->getAll();
    
    $themesForJs = collect($rawThemes)
        ->map(fn($theme) => ThemeViewData::fromModel($theme))
        ->map(fn($dto) => $dto->toArray())
        ->keyBy('slug')
        ->toArray();
    
    return view('view', compact('themesForJs'));
}
```

=================================================================================

## 5️⃣ PADRÃO COM VIEW COMPOSER

### View Composer
```php
namespace App\Http\ViewComposers;

use Illuminate\View\View;

class ThemeComposer
{
    public function __construct(
        private ThemeRepository $repository
    ) {}
    
    public function compose(View $view): void
    {
        $themes = $this->repository->getActive();
        
        $themesForJs = [];
        foreach ($themes as $theme) {
            $themesForJs[$theme->slug] = [
                'slug' => $theme->slug,
                'name' => $theme->name,
                'colors' => $theme->colors
            ];
        }
        
        $view->with('themesForJs', $themesForJs);
    }
}
```

### Service Provider
```php
use App\Http\ViewComposers\ThemeComposer;

public function boot(): void
{
    View::composer('admin.settings.*', ThemeComposer::class);
}
```

### Blade
```blade
<script>
    // $themesForJs já disponível automaticamente
    window.themes = {!! json_encode($themesForJs) !!};
</script>
```

=================================================================================

## 6️⃣ PADRÃO COM MÚLTIPLOS DATASETS

### Controller
```php
public function index()
{
    $themes = $this->getThemes();
    $users = $this->getUsers();
    $settings = $this->getSettings();
    
    // Preparar múltiplos datasets
    $jsData = [
        'themes' => $this->prepareThemes($themes),
        'users' => $this->prepareUsers($users),
        'settings' => $this->prepareSettings($settings)
    ];
    
    return view('view', compact('jsData'));
}

private function prepareThemes($themes): array
{
    return array_map(fn($t) => [
        'slug' => $t['slug'],
        'name' => $t['name']
    ], $themes);
}
```

### Blade
```blade
<script>
    // Inicializar namespace global
    window.appData = {!! json_encode($jsData) !!};
    
    // Acessar dados
    console.log('Themes:', window.appData.themes);
    console.log('Users:', window.appData.users);
    console.log('Settings:', window.appData.settings);
</script>
```

=================================================================================

## 7️⃣ PADRÃO COM LAZY LOADING

### Controller
```php
public function index()
{
    // Apenas IDs inicialmente
    $themeIds = $this->repository->getAllIds();
    
    return view('view', compact('themeIds'));
}

public function getTheme($id)
{
    $theme = $this->repository->find($id);
    
    return response()->json([
        'slug' => $theme->slug,
        'name' => $theme->name,
        'colors' => $theme->colors
    ]);
}
```

### Blade
```blade
<script>
    const themeIds = {!! json_encode($themeIds) !!};
    
    // Carregar sob demanda
    async function loadTheme(id) {
        const response = await fetch(`/api/themes/${id}`);
        const theme = await response.json();
        return theme;
    }
    
    // Usar quando necessário
    themeIds.forEach(id => {
        // Carregar apenas quando usuário clicar
        document.querySelector(`[data-theme="${id}"]`)
            .addEventListener('click', async () => {
                const theme = await loadTheme(id);
                console.log('Loaded:', theme);
            });
    });
</script>
```

=================================================================================

## 8️⃣ PADRÃO COM CACHE

### Controller
```php
use Illuminate\Support\Facades\Cache;

public function index()
{
    $themesForJs = Cache::remember('themes_for_js', 3600, function () {
        $themes = $this->repository->getAll();
        
        $result = [];
        foreach ($themes as $theme) {
            $result[$theme->slug] = [
                'slug' => $theme->slug,
                'name' => $theme->name,
                'colors' => $theme->colors
            ];
        }
        
        return $result;
    });
    
    return view('view', compact('themesForJs'));
}

// Invalidar cache quando dados mudarem
public function update(Request $request)
{
    // ... atualizar tema ...
    
    Cache::forget('themes_for_js');
}
```

=================================================================================

## 9️⃣ PADRÃO COM COLLECTION HELPERS

### Controller
```php
public function index()
{
    $themes = $this->repository->getAll();
    
    $themesForJs = collect($themes)
        ->filter(fn($theme) => $theme->is_active)
        ->map(function($theme) {
            return [
                'slug' => $theme->slug,
                'name' => $theme->name,
                'colors' => $theme->colors ?? $this->getDefaultColors()
            ];
        })
        ->keyBy('slug')
        ->toArray();
    
    return view('view', compact('themesForJs'));
}
```

**ATENÇÃO**: Este padrão funciona no CONTROLLER, não na VIEW!

=================================================================================

## 🔟 PADRÃO COM ESCAPE DE DADOS

### Controller
```php
public function index()
{
    $messages = $this->getMessages();
    
    $messagesForJs = array_map(function($msg) {
        return [
            'id' => $msg->id,
            'text' => $msg->text, // Será escapado pelo json_encode
            'html' => e($msg->html_content) // Escape manual se necessário
        ];
    }, $messages);
    
    return view('view', compact('messagesForJs'));
}
```

### Blade
```blade
<script>
    // JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
    const messages = {!! json_encode($messagesForJs, JSON_HEX_TAG | JSON_HEX_AMP) !!};
</script>
```

=================================================================================

## 🎯 ANTI-PADRÕES (NUNCA USE)

### ❌ Closure na View
```blade
<!-- NUNCA FAÇA ISTO -->
{!! json_encode(collect($items)->map(function($item) {
    return ['id' => $item->id];
})) !!}
```

### ❌ Loop Blade Gerando JavaScript
```blade
<!-- NUNCA FAÇA ISTO -->
<script>
    const data = {};
    @foreach($items as $item)
        data['{{ $item->id }}'] = {!! json_encode($item) !!};
    @endforeach
</script>
```

### ❌ @php Complexo na View
```blade
<!-- NUNCA FAÇA ISTO -->
@php
    $processed = [];
    foreach($items as $item) {
        if (complexCondition($item)) {
            $processed[] = transform($item);
        }
    }
@endphp
```

=================================================================================

## ✅ CHECKLIST DE IMPLEMENTAÇÃO

Ao passar dados do PHP para JavaScript:

- [ ] Processar no Controller, não na View
- [ ] Usar json_encode simples no Blade
- [ ] Adicionar fallbacks/defaults no PHP
- [ ] Validar estrutura dos dados
- [ ] Testar com dados vazios
- [ ] Testar com dados malformados
- [ ] Verificar HTML source gerado
- [ ] Console.log para validar no browser
- [ ] Limpar cache após mudanças
- [ ] Documentar estrutura de dados

=================================================================================

## 🧪 TEMPLATE DE TESTE

### Test Case
```php
namespace Tests\Feature;

use Tests\TestCase;

class ThemeDataTest extends TestCase
{
    public function test_themes_serialize_correctly_for_javascript()
    {
        $controller = new ThemeController(...);
        $view = $controller->index();
        $data = $view->getData();
        
        // Verificar que dados existem
        $this->assertArrayHasKey('themesForJs', $data);
        
        // Verificar estrutura
        $themesForJs = $data['themesForJs'];
        $this->assertIsArray($themesForJs);
        
        // Verificar que serializa corretamente
        $json = json_encode($themesForJs);
        $this->assertIsString($json);
        
        // Verificar que deserializa corretamente
        $decoded = json_decode($json, true);
        $this->assertEquals($themesForJs, $decoded);
        
        // Verificar dados específicos
        $this->assertEquals('#7C3AED', $decoded['starter']['colors']['primary']);
    }
    
    public function test_view_renders_javascript_correctly()
    {
        $response = $this->get('/admin/settings/theme');
        
        $response->assertStatus(200);
        $response->assertSee('window.themeData', false);
        $response->assertSee('"primary":"#7C3AED"', false);
    }
}
```

=================================================================================

BIBLIOTECA DE SNIPPETS - Copie e adapte conforme necessário!
Última atualização: 29/12/2025
