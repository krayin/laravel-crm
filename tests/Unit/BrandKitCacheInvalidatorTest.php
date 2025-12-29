<?php

namespace Tests\Unit;

use App\Services\BrandKitCacheInvalidator;
use App\Support\BrandKitResolver;
use App\Support\ThemeCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use Tests\Traits\BrandKitTestSchema;

/**
 * Testes para BrandKitCacheInvalidator.
 *
 * Foco: Garantir que invalidação é centralizada e completa.
 *
 * Usa schema mínimo em memória para evitar dependência das migrations
 * completas do Krayin (que podem falhar com SQLite).
 */
class BrandKitCacheInvalidatorTest extends TestCase
{
    use BrandKitTestSchema;

    private BrandKitCacheInvalidator $invalidator;
    private BrandKitResolver $resolver;
    private array $createdThemes = [];

    protected function setUp(): void
    {
        parent::setUp();

        // Schema mínimo em memória
        $this->setUpBrandKitSchema();

        // Cache em memória para isolamento total
        config()->set('cache.default', 'array');
        Cache::flush();

        // Garante que o diretório base de temas exista (necessário no CI)
        $themesPath = storage_path('app/public/themes');
        if (!File::isDirectory($themesPath)) {
            File::makeDirectory($themesPath, 0755, true);
        }

        $this->resolver = app(BrandKitResolver::class);
        $this->invalidator = app(BrandKitCacheInvalidator::class);
    }

    protected function tearDown(): void
    {
        Cache::flush();

        // Limpa temas criados durante o teste
        foreach ($this->createdThemes as $slug) {
            $path = storage_path("app/public/themes/{$slug}");
            if (File::isDirectory($path)) {
                File::deleteDirectory($path);
            }
        }

        $this->tearDownBrandKitSchema();
        parent::tearDown();
    }

    /**
     * Helper: cria um tema fake no storage real.
     */
    private function createFakeTheme(string $slug): void
    {
        $path = storage_path("app/public/themes/{$slug}");
        File::ensureDirectoryExists($path);
        File::put(
            "{$path}/theme.json",
            json_encode([
                'name' => ucfirst($slug),
                'version' => '1.0.0',
                'color_primary' => '#FF0000',
            ]),
        );

        $this->createdThemes[] = $slug;
    }

    // =========================================================================
    // AFTER BRANDKIT CHANGE TESTS
    // =========================================================================

    /** @test */
    public function after_brandkit_change_invalidates_specific_theme(): void
    {
        // Arrange: popula cache
        $this->createFakeTheme('test-theme');
        $this->resolver->resolve('global', 'test-theme');

        $cacheKey = 'brand_kit.resolved.v1.global.test-theme';
        $this->assertTrue(Cache::has($cacheKey));

        // Act
        $this->invalidator->afterBrandKitChange('global', 'test-theme');

        // Assert: cache específico invalidado
        $this->assertFalse(Cache::has($cacheKey));
    }

    /** @test */
    public function after_brandkit_change_invalidates_theme_cache(): void
    {
        // Arrange: popula ThemeCache
        ThemeCache::putContext(new \App\Support\ThemeContext(
            enabled: true,
            slug: 'test',
            scopeKey: 'global',
            config: [],
            loginConfig: [],
            isPreview: false,
            customCssAdmin: '',
            customCssLogin: '',
        ));
        ThemeCache::putConfig((object) ['id' => 1]);

        $this->assertTrue(ThemeCache::hasContext());
        $this->assertTrue(ThemeCache::hasConfig());

        // Act
        $this->invalidator->afterBrandKitChange('global', 'test-theme');

        // Assert: ThemeCache também invalidado
        $this->assertFalse(ThemeCache::hasContext());
        $this->assertFalse(ThemeCache::hasConfig());
    }

    /** @test */
    public function after_brandkit_change_with_wildcard_invalidates_all_global(): void
    {
        // Arrange: cria múltiplos temas
        $this->createFakeTheme('theme-a');
        $this->createFakeTheme('theme-b');

        $this->resolver->resolve('global', 'theme-a');
        $this->resolver->resolve('global', 'theme-b');

        // Act: wildcard
        $this->invalidator->afterBrandKitChange('global', '*');

        // Assert: todos invalidados
        $this->assertFalse(Cache::has('brand_kit.resolved.v1.global.theme-a'));
        $this->assertFalse(Cache::has('brand_kit.resolved.v1.global.theme-b'));
    }

    // =========================================================================
    // INVALIDATE ALL TESTS
    // =========================================================================

    /** @test */
    public function invalidate_all_clears_everything(): void
    {
        // Arrange
        $this->createFakeTheme('test-theme');
        $this->resolver->resolve('global', 'test-theme');

        ThemeCache::putContext(new \App\Support\ThemeContext(
            enabled: true,
            slug: 'test',
            scopeKey: 'global',
            config: [],
            loginConfig: [],
            isPreview: false,
            customCssAdmin: '',
            customCssLogin: '',
        ));

        // Act
        $this->invalidator->invalidateAll();

        // Assert
        $this->assertFalse(Cache::has('brand_kit.resolved.v1.global.test-theme'));
        $this->assertFalse(ThemeCache::hasContext());
    }

    // =========================================================================
    // AFTER THEME SWITCH TESTS
    // =========================================================================

    /** @test */
    public function after_theme_switch_does_full_flush(): void
    {
        // Arrange
        $this->createFakeTheme('old-theme');
        $this->createFakeTheme('new-theme');

        $this->resolver->resolve('global', 'old-theme');

        ThemeCache::putContext(new \App\Support\ThemeContext(
            enabled: true,
            slug: 'old-theme',
            scopeKey: 'global',
            config: [],
            loginConfig: [],
            isPreview: false,
            customCssAdmin: '',
            customCssLogin: '',
        ));

        // Act
        $this->invalidator->afterThemeSwitch('new-theme');

        // Assert: tudo invalidado
        $this->assertFalse(ThemeCache::hasContext());
        $this->assertFalse(Cache::has('brand_kit.resolved.v1.global.old-theme'));
    }

    // =========================================================================
    // AFTER SNAPSHOT RESTORE TESTS
    // =========================================================================

    /** @test */
    public function after_snapshot_restore_does_full_flush(): void
    {
        // Arrange
        $this->createFakeTheme('test-theme');
        $this->resolver->resolve('global', 'test-theme');

        ThemeCache::putConfig((object) ['id' => 1]);

        // Act
        $this->invalidator->afterSnapshotRestore('global', 'test-theme');

        // Assert
        $this->assertFalse(ThemeCache::hasConfig());
        $this->assertFalse(Cache::has('brand_kit.resolved.v1.global.test-theme'));
    }
}
