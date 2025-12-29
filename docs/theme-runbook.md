# Theme System Runbook

> Guia operacional para troubleshooting e manutenção do sistema de temas.

---

## 1. Comandos de Emergência

### 1.1 Cache Stuck (UI não atualiza)

```bash
# Flush completo de cache
php artisan optimize:clear

# Ou, se preferir granular:
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

### 1.2 Restore via Tinker (se UI quebrar)

```php
php artisan tinker

# Desativar tema
DB::table('theme_configs')->where('id', 1)->update([
    'is_active' => false,
    'selected_theme' => 'default',
]);

# Limpar cache
Artisan::call('cache:clear');
```

### 1.3 Rollback de Snapshot via Tinker

```php
php artisan tinker

use App\Services\BrandKitSnapshotService;

$service = app(BrandKitSnapshotService::class);

# Lista snapshots disponíveis
$snapshots = $service->list('global', 'default', 10);
$snapshots->each(fn($s) => dump("{$s->id} - {$s->name} - {$s->created_at}"));

# Restaura um snapshot específico
$service->restore($snapshotId, auth()->id());
```

---

## 2. Diagnóstico

### 2.1 Verificar Estado Atual

```bash
# Rotas registradas
php artisan route:list | grep -E "(theme|brand-kit)"

# Verificar config do DB
php artisan tinker --execute="dump(DB::table('theme_configs')->first())"

# Verificar overrides ativos
php artisan tinker --execute="dump(App\Models\BrandKitOverride::where('is_active', true)->get())"
```

### 2.2 Verificar Cache

```php
php artisan tinker

use App\Support\ThemeCache;
use Illuminate\Support\Facades\Cache;

# ThemeCache
dump([
    'has_context' => ThemeCache::hasContext(),
    'has_config' => ThemeCache::hasConfig(),
]);

# BrandKitResolver cache
$keys = ['brand_kit.resolved.v1.global.default'];
foreach ($keys as $key) {
    dump("{$key}: " . (Cache::has($key) ? 'EXISTS' : 'EMPTY'));
}
```

### 2.3 Verificar Preview Session

```php
php artisan tinker

# Preview está ativo?
dump(session()->get('brandkit.preview.enabled'));
dump(session()->get('brandkit.preview.theme_slug'));
dump(session()->get('brandkit.preview.expires_at'));
```

---

## 3. Cenários Comuns

### 3.1 "Mudei a cor mas não aparece"

**Causa provável:** Cache não invalidado.

**Solução:**
```bash
php artisan optimize:clear
```

Ou via código (após save no controller):
```php
app(BrandKitCacheInvalidator::class)->afterBrandKitChange('global', $themeSlug);
```

### 3.2 "Preview mostra, mas ao salvar não aplica"

**Causa provável:** Preview session não foi limpa antes do save.

**Verificação:**
```php
// PreviewSession deve ser cleared após save
app(PreviewSession::class)->clear();
```

### 3.3 "Restore de snapshot não funcionou"

**Causa provável:** Cache não invalidado após restore.

**Solução:**
```php
$service->restore($snapshotId, $userId);
app(BrandKitCacheInvalidator::class)->afterSnapshotRestore('global', $themeSlug);
```

### 3.4 "Tema selecionado não existe"

**Causa provável:** Arquivo theme.json deletado ou theme_slug inválido.

**Verificação:**
```bash
ls storage/app/public/themes/
```

**Solução:**
```php
// Volta para default
DB::table('theme_configs')->where('id', 1)->update(['selected_theme' => 'default']);
Artisan::call('cache:clear');
```

---

## 4. Logs

### 4.1 Onde ficam os logs

```bash
# Laravel log
tail -f storage/logs/laravel.log

# Filtrar por tema
grep -i "theme\|brandkit" storage/logs/laravel.log | tail -50
```

### 4.2 O que é logado

| Evento | Nível | Classe |
|--------|-------|--------|
| Cache flush | DEBUG | ThemeCache |
| Cache invalidation | DEBUG | BrandKitCacheInvalidator |
| Snapshot restore | INFO | BrandKitSnapshotService |
| Theme switch | INFO | BrandKitCacheInvalidator |
| Auto-snapshot fail | WARNING | BrandKitSnapshotService |

---

## 5. Métricas de Saúde

### 5.1 Checklist Diário

- [ ] `php artisan route:list | grep theme` retorna rotas esperadas
- [ ] Página de settings carrega sem erro
- [ ] Cores aplicadas correspondem ao DB
- [ ] Preview funciona (muda e volta)
- [ ] Save persiste alterações

### 5.2 Checklist Pós-Deploy

```bash
# 1. Limpa cache
php artisan optimize:clear

# 2. Roda migrations
php artisan migrate --force

# 3. Verifica rotas
php artisan route:list | grep -c "brand-kit"  # Deve retornar 9

# 4. Verifica tabelas
php artisan tinker --execute="dump(Schema::hasTable('brand_kit_overrides'))"
php artisan tinker --execute="dump(Schema::hasTable('brand_kit_snapshots'))"

# 5. Smoke test
curl -s http://localhost/admin/settings/brand-kit -H "Accept: application/json" | jq .ok
```

---

## 6. Rollback de Emergência

### 6.1 Desativar Sistema de Temas

```php
php artisan tinker

// Desativa completamente
DB::table('theme_configs')->where('id', 1)->update([
    'is_active' => false,
]);

Artisan::call('cache:clear');
```

### 6.2 Restore para Estado Inicial

```php
php artisan tinker

// 1. Remove todos os overrides
App\Models\BrandKitOverride::query()->delete();

// 2. Remove todo CSS customizado
App\Models\BrandKitCustomCss::query()->delete();

// 3. Reset config
DB::table('theme_configs')->where('id', 1)->update([
    'is_active' => false,
    'selected_theme' => 'default',
    'color_primary' => null,
    'color_primary_dark' => null,
    'color_primary_light' => null,
    // ... demais campos para null
]);

// 4. Limpa cache
Artisan::call('cache:clear');
```

---

## 7. Contatos

| Responsável | Área |
|-------------|------|
| Backend | ThemeManager, BrandKit, Cache |
| Frontend | Views, CSS, Admin UI |
| DevOps | Cache (Redis), Session, Deploy |

---

## 8. Arquivos Críticos

| Arquivo | Responsabilidade |
|---------|------------------|
| `app/Support/BrandKitResolver.php` | Merge de precedência |
| `app/Services/BrandKitSnapshotService.php` | Snapshots transacionais |
| `app/Services/BrandKitCacheInvalidator.php` | Invalidação centralizada |
| `app/Support/ThemeCache.php` | Cache keys do tema |
| `app/Support/PreviewSession.php` | Preview isolado |
| `resources/views/vendor/theme-manager/` | View overrides |

---

---

## 9. Suite de Testes

### 9.1 Rodar Testes BrandKit

A suite de testes usa **SQLite :memory:** e **array cache** para isolamento total, sem depender das migrations do CRM.

```bash
# Via Composer (recomendado)
composer test:brandkit

# Via script (Windows)
.\scripts\test-brandkit.ps1

# Via script (Unix/Mac)
./scripts/test-brandkit.sh

# Via PHPUnit direto
php vendor/bin/phpunit tests/Unit/BrandKitResolverTest.php \
    tests/Unit/BrandKitSnapshotServiceTest.php \
    tests/Unit/BrandKitCacheInvalidatorTest.php \
    tests/Unit/PreviewSessionTest.php \
    tests/Unit/ThemeContextFactoryTest.php --no-coverage

# Via Pest (alternativo)
php vendor/bin/pest --filter "BrandKit|PreviewSession|ThemeContextFactory"
```

### 9.2 Cobertura de Código

```bash
# Via Composer
composer test:brandkit-coverage

# Via script
.\scripts\test-brandkit.ps1 -Coverage
```

### 9.3 Filtrar Testes Específicos

```bash
# Apenas testes de Preview
.\scripts\test-brandkit.ps1 -Filter "preview"

# Apenas testes de Cache
.\scripts\test-brandkit.ps1 -Filter "cache"

# Apenas testes de CLEAR
php vendor/bin/phpunit tests/Unit/BrandKitResolverTest.php --filter "clear"
```

### 9.4 Estrutura dos Testes

| Arquivo                             | Testes | Foco                                       |
| ----------------------------------- | ------ | ------------------------------------------ |
| `BrandKitResolverTest.php`          | 16     | Precedência, CLEAR, Type Casting, Sanitize |
| `BrandKitSnapshotServiceTest.php`   | 12     | Transações, Atomicidade, Auto-cleanup      |
| `BrandKitCacheInvalidatorTest.php`  | 6      | Invalidação centralizada                   |
| `PreviewSessionTest.php`            | 16     | Isolamento, Expiração, Segurança           |
| `ThemeContextFactoryTest.php`       | 26     | Factory, Config Resolution, Cache          |
| **Total**                           | **77** | ~10-15 segundos                            |

### 9.5 Trait de Schema Mínimo

Os testes usam `Tests\Traits\BrandKitTestSchema` que cria apenas as tabelas necessárias em memória:

- `brand_kit_overrides`
- `brand_kit_snapshots`
- `brand_kit_custom_css`
- `theme_configs`

Isso evita o erro `lead_stages.code` que ocorre quando SQLite tenta rodar as migrations completas do Krayin.

```php
use Tests\Traits\BrandKitTestSchema;

class MyTest extends TestCase
{
    use BrandKitTestSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpBrandKitSchema();
        config()->set('cache.default', 'array');
    }

    protected function tearDown(): void
    {
        $this->tearDownBrandKitSchema();
        parent::tearDown();
    }
}
```

---

*Última atualização: Dezembro 2024*
