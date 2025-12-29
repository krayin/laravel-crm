<?php

namespace Tests\Unit;

use App\Models\BrandKitOverride;
use App\Support\BrandKitResolver;
use App\Support\CssValidator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use Tests\Traits\BrandKitTestSchema;

/**
 * Testes unitários para BrandKitResolver.
 *
 * Foco: Clear Semantics, Precedência, Preview Isolation, Type Casting.
 *
 * Usa schema mínimo em memória para evitar dependência das migrations
 * completas do Krayin (que podem falhar com SQLite).
 */
class BrandKitResolverTest extends TestCase
{
    use BrandKitTestSchema;

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

        $this->resolver = new BrandKitResolver(new CssValidator());
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
     * O BrandKitResolver usa storage_path() + File::, não Storage::disk().
     */
    private function createFakeTheme(string $slug, array $themeJson = []): void
    {
        $defaultJson = [
            'name' => ucfirst($slug),
            'version' => '1.0.0',
            'color_primary' => '#FF0000',
            'color_primary_dark' => '#CC0000',
            'login_bg_opacity' => 75,
        ];

        $path = storage_path("app/public/themes/{$slug}");
        File::ensureDirectoryExists($path);
        File::put(
            "{$path}/theme.json",
            json_encode(array_replace_recursive($defaultJson, $themeJson)),
        );

        $this->createdThemes[] = $slug;
    }

    // =========================================================================
    // CLEAR SEMANTICS TESTS
    // =========================================================================

    /** @test */
    public function clear_sentinel_removes_value_from_lower_layers(): void
    {
        // Arrange: tema com cor definida
        $this->createFakeTheme('test-theme', [
            'color_primary' => '#FF0000',
        ]);

        // Arrange: override com CLEAR
        BrandKitOverride::create([
            'scope_key' => 'global',
            'theme_slug' => 'test-theme',
            'override_key' => 'color_primary',
            'value' => null, // CLEAR
            'is_active' => true,
        ]);

        // Act
        $result = $this->resolver->resolve('global', 'test-theme');

        // Assert: deve voltar para o default, não o theme.json
        $this->assertEquals('#1E40AF', $result['config']['color_primary']);
    }

    /** @test */
    public function empty_string_value_is_treated_as_clear(): void
    {
        // Arrange
        $this->createFakeTheme('test-theme', [
            'color_primary' => '#FF0000',
        ]);

        // Override com string vazia
        BrandKitOverride::create([
            'scope_key' => 'global',
            'theme_slug' => 'test-theme',
            'override_key' => 'color_primary',
            'value' => '', // CLEAR via string vazia
            'is_active' => true,
        ]);

        // Act
        $result = $this->resolver->resolve('global', 'test-theme');

        // Assert: volta para default
        $this->assertEquals('#1E40AF', $result['config']['color_primary']);
    }

    /** @test */
    public function inactive_override_is_ignored(): void
    {
        // Arrange
        $this->createFakeTheme('test-theme', [
            'color_primary' => '#FF0000',
        ]);

        // Override inativo
        BrandKitOverride::create([
            'scope_key' => 'global',
            'theme_slug' => 'test-theme',
            'override_key' => 'color_primary',
            'value' => '#00FF00',
            'is_active' => false, // Inativo
        ]);

        // Act
        $result = $this->resolver->resolve('global', 'test-theme');

        // Assert: usa valor do theme.json
        $this->assertEquals('#FF0000', $result['config']['color_primary']);
    }

    /** @test */
    public function active_override_with_value_wins(): void
    {
        // Arrange
        $this->createFakeTheme('test-theme', [
            'color_primary' => '#FF0000',
        ]);

        // Override ativo com valor
        BrandKitOverride::create([
            'scope_key' => 'global',
            'theme_slug' => 'test-theme',
            'override_key' => 'color_primary',
            'value' => '#00FF00',
            'is_active' => true,
        ]);

        // Act
        $result = $this->resolver->resolve('global', 'test-theme');

        // Assert: override vence
        $this->assertEquals('#00FF00', $result['config']['color_primary']);
    }

    // =========================================================================
    // PREVIEW ISOLATION TESTS
    // =========================================================================

    /** @test */
    public function preview_mode_bypasses_cache(): void
    {
        // Arrange: popula cache com valor antigo
        $this->createFakeTheme('test-theme', [
            'color_primary' => '#AAAAAA',
        ]);

        $cached = $this->resolver->resolve('global', 'test-theme');
        $this->assertEquals('#AAAAAA', $cached['config']['color_primary']);

        // Atualiza theme.json
        $this->createFakeTheme('test-theme', [
            'color_primary' => '#BBBBBB',
        ]);

        // Act: resolve sem preview (deve usar cache)
        $fromCache = $this->resolver->resolve('global', 'test-theme');

        // Act: resolve com preview (deve bypass cache)
        $fromPreview = $this->resolver->resolve('global', 'test-theme', true);

        // Assert
        $this->assertEquals('#AAAAAA', $fromCache['config']['color_primary']); // Cache
        $this->assertEquals('#BBBBBB', $fromPreview['config']['color_primary']); // Fresh
    }

    /** @test */
    public function preview_overrides_are_applied_on_top(): void
    {
        // Arrange
        $this->createFakeTheme('test-theme', [
            'color_primary' => '#FF0000',
        ]);

        // Act: preview com overrides
        $result = $this->resolver->resolve(
            'global',
            'test-theme',
            true,
            ['color_primary' => '#PREVIEW'],
        );

        // Assert: preview override vence
        $this->assertEquals('#PREVIEW', $result['config']['color_primary']);
        $this->assertTrue($result['is_preview']);
    }

    /** @test */
    public function preview_does_not_pollute_global_cache(): void
    {
        // Arrange
        $this->createFakeTheme('test-theme', [
            'color_primary' => '#ORIGINAL',
        ]);

        // Act: resolve normal primeiro
        $this->resolver->resolve('global', 'test-theme');

        // Act: resolve com preview
        $this->resolver->resolve('global', 'test-theme', true, [
            'color_primary' => '#PREVIEW',
        ]);

        // Act: resolve normal novamente
        $afterPreview = $this->resolver->resolve('global', 'test-theme');

        // Assert: cache não foi poluído pelo preview
        $this->assertEquals('#ORIGINAL', $afterPreview['config']['color_primary']);
    }

    // =========================================================================
    // TYPE CASTING TESTS
    // =========================================================================

    /** @test */
    public function opacity_is_clamped_to_0_100(): void
    {
        // Arrange
        $this->createFakeTheme('test-theme', [
            'login_bg_opacity' => 150, // Acima do máximo
        ]);

        // Act
        $result = $this->resolver->resolve('global', 'test-theme');

        // Assert: clamp para 100
        $this->assertEquals(100, $result['config']['login_bg_opacity']);
    }

    /** @test */
    public function opacity_negative_is_clamped_to_0(): void
    {
        // Arrange
        $this->createFakeTheme('test-theme', [
            'login_bg_opacity' => -50,
        ]);

        // Act
        $result = $this->resolver->resolve('global', 'test-theme');

        // Assert: clamp para 0
        $this->assertEquals(0, $result['config']['login_bg_opacity']);
    }

    /** @test */
    public function zoom_is_clamped_to_50_150(): void
    {
        // Arrange: zoom abaixo do mínimo
        $this->createFakeTheme('test-theme', [
            'login_bg_zoom' => 30,
        ]);

        // Act
        $result = $this->resolver->resolve('global', 'test-theme');

        // Assert: clamp para 50
        $this->assertEquals(50, $result['config']['login_bg_zoom']);
    }

    /** @test */
    public function boolean_values_are_properly_cast(): void
    {
        // Arrange: valores que devem virar true
        $this->createFakeTheme('test-theme', [
            'login_show_powered_by' => '1',
            'login_card_enabled' => 'true',
            'login_card_sparkles' => 1,
        ]);

        // Act
        $result = $this->resolver->resolve('global', 'test-theme');

        // Assert
        $this->assertTrue($result['config']['login_show_powered_by']);
        $this->assertTrue($result['config']['login_card_enabled']);
        $this->assertTrue($result['config']['login_card_sparkles']);
    }

    /** @test */
    public function color_is_normalized_to_uppercase_hex(): void
    {
        // Arrange
        $this->createFakeTheme('test-theme', [
            'color_primary' => 'ff0000', // Sem #
        ]);

        // Act
        $result = $this->resolver->resolve('global', 'test-theme');

        // Assert: normalizado para #FF0000
        $this->assertEquals('#FF0000', $result['config']['color_primary']);
    }

    // =========================================================================
    // CACHE INVALIDATION TESTS
    // =========================================================================

    /** @test */
    public function invalidate_clears_specific_cache(): void
    {
        // Arrange
        $this->createFakeTheme('test-theme');
        $this->resolver->resolve('global', 'test-theme');

        // Verifica que cache existe
        $cacheKey = 'brand_kit.resolved.v1.global.test-theme';
        $this->assertTrue(Cache::has($cacheKey));

        // Act
        $this->resolver->invalidate('global', 'test-theme');

        // Assert
        $this->assertFalse(Cache::has($cacheKey));
    }

    /** @test */
    public function invalidate_all_global_clears_all_themes(): void
    {
        // Arrange: cria múltiplos temas
        $this->createFakeTheme('theme-a');
        $this->createFakeTheme('theme-b');

        $this->resolver->resolve('global', 'theme-a');
        $this->resolver->resolve('global', 'theme-b');

        // Act
        $this->resolver->invalidateAllGlobal();

        // Assert: ambos devem ser invalidados
        $this->assertFalse(Cache::has('brand_kit.resolved.v1.global.theme-a'));
        $this->assertFalse(Cache::has('brand_kit.resolved.v1.global.theme-b'));
    }

    // =========================================================================
    // SECURITY TESTS
    // =========================================================================

    /** @test */
    public function sanitize_key_prevents_path_traversal(): void
    {
        // Arrange: tentativa de path traversal
        $dangerous = '../../../etc/passwd';

        // Act: resolve com slug perigoso
        $result = $this->resolver->resolve('global', $dangerous);

        // Assert: deve sanitizar removendo caracteres perigosos (., /)
        // '../../../etc/passwd' vira 'etcpasswd' (sem path traversal)
        $this->assertEquals('etcpasswd', $result['theme_slug']);
    }

    /** @test */
    public function sanitize_key_removes_special_characters(): void
    {
        // Act
        $result = $this->resolver->resolve('global', 'theme<script>');

        // Assert
        $this->assertEquals('themescript', $result['theme_slug']);
    }
}
