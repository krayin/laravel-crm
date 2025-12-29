<?php

namespace Tests\Unit;

use App\Support\ThemeCache;
use App\Support\ThemeConfigResolver;
use App\Support\ThemeContext;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Testes unitários para o core de tema.
 *
 * Foco: garantir que o login/tema não quebre em cenários críticos.
 * Não testa vendors, apenas lógica de app/.
 *
 * NOTA: Estes são testes unitários puros que não dependem do banco.
 * Usam mocks para isolar a lógica sendo testada.
 */
class ThemeContextFactoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Limpa cache antes de cada teste
        Cache::flush();

        // Configura storage fake para testes de tema
        Storage::fake("public");
    }

    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }

    /**
     * Helper: cria um tema fake no storage.
     */
    private function createFakeTheme(string $slug, array $themeJson = []): void
    {
        $defaultJson = [
            "name" => ucfirst($slug),
            "version" => "1.0.0",
            "color_primary" => "#FF0000",
            "color_primary_dark" => "#CC0000",
            "login" => [
                "bg_image" => "bg.jpg",
                "bg_opacity" => 75,
            ],
        ];

        Storage::disk("public")->put(
            "themes/{$slug}/theme.json",
            json_encode(array_replace_recursive($defaultJson, $themeJson)),
        );
    }

    /**
     * Helper: cria mock de config do DB.
     */
    private function mockDbConfig(array $attributes = []): object
    {
        $defaults = [
            "id" => 1,
            "selected_theme" => "default",
            "is_active" => false,
            "color_primary" => null,
            "color_primary_dark" => null,
            "color_primary_light" => null,
            "color_success" => null,
            "color_warning" => null,
            "color_danger" => null,
            "logo_main" => null,
            "logo_light" => null,
            "logo_icon" => null,
            "favicon" => null,
            "login_bg_image" => null,
            "login_bg_zoom" => null,
            "login_bg_opacity" => null,
            "login_show_powered_by" => null,
            "login_card_enabled" => null,
            "login_card_bg_image" => null,
            "login_card_bg_opacity" => null,
            "login_card_overlay_color" => null,
            "login_card_title" => null,
            "login_card_subtitle" => null,
            "login_card_sparkles" => null,
            "login_card_help_link" => null,
            "login_card_support_email" => null,
        ];

        return (object) array_merge($defaults, $attributes);
    }

    // =========================================================================
    // TESTE 1: DB override vence quando is_active=1 e valor presente
    // =========================================================================

    /** @test */
    public function db_override_wins_when_active_and_value_present(): void
    {
        // Arrange: tema no storage com cor vermelha
        $this->createFakeTheme("custom", [
            "color_primary" => "#FF0000",
        ]);

        // Arrange: mock de DB com is_active=1 e cor azul
        $dbConfig = $this->mockDbConfig([
            "selected_theme" => "custom",
            "is_active" => true,
            "color_primary" => "#0000FF", // Azul - deve vencer
        ]);

        // Cacheia o mock
        Cache::put(ThemeCache::KEY_CONFIG, $dbConfig, 3600);

        // Act
        $config = ThemeConfigResolver::resolveConfig("custom");

        // Assert: cor do DB vence
        $this->assertEquals("#0000FF", $config["color_primary"]);
    }

    /** @test */
    public function db_override_does_not_apply_when_inactive(): void
    {
        // Arrange: tema com cor vermelha
        $this->createFakeTheme("custom", [
            "color_primary" => "#FF0000",
        ]);

        // Arrange: mock de DB com is_active=0
        $dbConfig = $this->mockDbConfig([
            "selected_theme" => "custom",
            "is_active" => false, // Inativo
            "color_primary" => "#0000FF",
        ]);

        Cache::put(ThemeCache::KEY_CONFIG, $dbConfig, 3600);

        // Act
        $config = ThemeConfigResolver::resolveConfig("custom");

        // Assert: cor do theme.json vence (DB ignorado por is_active=0)
        $this->assertEquals("#FF0000", $config["color_primary"]);
    }

    // =========================================================================
    // TESTE 2: theme.json vence quando DB null
    // =========================================================================

    /** @test */
    public function theme_json_wins_when_db_value_is_null(): void
    {
        // Arrange: tema com cor definida no theme.json
        $this->createFakeTheme("custom", [
            "color_primary" => "#00FF00", // Verde no theme.json
        ]);

        // Arrange: DB ativo mas sem valor para color_primary
        $dbConfig = $this->mockDbConfig([
            "selected_theme" => "custom",
            "is_active" => true,
            "color_primary" => null, // NULL no DB
        ]);

        Cache::put(ThemeCache::KEY_CONFIG, $dbConfig, 3600);

        // Act
        $config = ThemeConfigResolver::resolveConfig("custom");

        // Assert: cor do theme.json vence
        $this->assertEquals("#00FF00", $config["color_primary"]);
    }

    /** @test */
    public function theme_json_wins_when_db_value_is_empty_string(): void
    {
        // Arrange
        $this->createFakeTheme("custom", [
            "color_primary" => "#00FF00",
        ]);

        $dbConfig = $this->mockDbConfig([
            "selected_theme" => "custom",
            "is_active" => true,
            "color_primary" => "", // String vazia no DB
        ]);

        Cache::put(ThemeCache::KEY_CONFIG, $dbConfig, 3600);

        // Act
        $config = ThemeConfigResolver::resolveConfig("custom");

        // Assert: theme.json vence
        $this->assertEquals("#00FF00", $config["color_primary"]);
    }

    // =========================================================================
    // TESTE 3: Fallback para default quando tema ausente
    // =========================================================================

    /** @test */
    public function fallback_to_default_when_theme_does_not_exist(): void
    {
        // Arrange: mock de DB aponta para tema que não existe
        $dbConfig = $this->mockDbConfig([
            "selected_theme" => "nonexistent_theme",
            "is_active" => true,
        ]);

        Cache::put(ThemeCache::KEY_CONFIG, $dbConfig, 3600);

        // Act
        $slug = ThemeConfigResolver::resolveSlug();

        // Assert: fallback para 'default'
        $this->assertEquals("default", $slug);
    }

    /** @test */
    public function fallback_to_defaults_when_no_theme_json(): void
    {
        // Arrange: tema 'default' (sem theme.json)
        $dbConfig = $this->mockDbConfig([
            "selected_theme" => "default",
            "is_active" => true,
            "color_primary" => null, // Sem override
        ]);

        Cache::put(ThemeCache::KEY_CONFIG, $dbConfig, 3600);

        // Act
        $config = ThemeConfigResolver::resolveConfig("default");

        // Assert: usa defaults hardcoded
        $defaults = ThemeConfigResolver::getDefaults();
        $this->assertEquals(
            $defaults["color_primary"],
            $config["color_primary"],
        );
    }

    /** @test */
    public function resolver_returns_inactive_when_is_active_false(): void
    {
        // Arrange: tema inativo
        $dbConfig = $this->mockDbConfig([
            "selected_theme" => "default",
            "is_active" => false,
        ]);

        Cache::put(ThemeCache::KEY_CONFIG, $dbConfig, 3600);

        // Act
        $isActive = ThemeConfigResolver::isActive();

        // Assert
        $this->assertFalse($isActive);
    }

    // =========================================================================
    // TESTE 4: Preview por sessão vence DB (isolamento por usuário)
    // =========================================================================

    /** @test */
    public function preview_override_resolves_correct_theme(): void
    {
        // Arrange: cria dois temas
        $this->createFakeTheme("theme-a", ["color_primary" => "#AAAAAA"]);
        $this->createFakeTheme("theme-b", ["color_primary" => "#BBBBBB"]);

        // Arrange: DB aponta para theme-a
        $dbConfig = $this->mockDbConfig([
            "selected_theme" => "theme-a",
            "is_active" => true,
        ]);

        Cache::put(ThemeCache::KEY_CONFIG, $dbConfig, 3600);

        // Act: resolve com override de preview para theme-b
        $slugWithPreview = ThemeConfigResolver::resolveSlug("theme-b");

        // Assert: preview vence
        $this->assertEquals("theme-b", $slugWithPreview);
    }

    /** @test */
    public function preview_does_not_alter_cached_config(): void
    {
        // Arrange
        $this->createFakeTheme("preview-theme", [
            "color_primary" => "#PREVIEW",
        ]);

        $dbConfig = $this->mockDbConfig([
            "selected_theme" => "default",
            "is_active" => true,
        ]);

        Cache::put(ThemeCache::KEY_CONFIG, $dbConfig, 3600);

        // Act: resolve com override de preview
        ThemeConfigResolver::resolveSlug("preview-theme");

        // Assert: cache não foi alterado
        $cachedConfig = Cache::get(ThemeCache::KEY_CONFIG);
        $this->assertEquals("default", $cachedConfig->selected_theme);
    }

    /** @test */
    public function preview_falls_back_to_default_if_theme_not_found(): void
    {
        // Arrange
        $dbConfig = $this->mockDbConfig([
            "selected_theme" => "default",
            "is_active" => true,
        ]);

        Cache::put(ThemeCache::KEY_CONFIG, $dbConfig, 3600);

        // Act: preview para tema que não existe
        $slug = ThemeConfigResolver::resolveSlug("nonexistent-preview");

        // Assert: fallback para default
        $this->assertEquals("default", $slug);
    }

    // =========================================================================
    // TESTES ADICIONAIS: Segurança e Edge Cases
    // =========================================================================

    /** @test */
    public function sanitize_slug_removes_dangerous_characters(): void
    {
        $dangerous = "../../../etc/passwd";
        $sanitized = ThemeConfigResolver::sanitizeSlug($dangerous);

        $this->assertEquals("etcpasswd", $sanitized);
    }

    /** @test */
    public function sanitize_slug_returns_default_for_empty(): void
    {
        $empty = "";
        $sanitized = ThemeConfigResolver::sanitizeSlug($empty);

        $this->assertEquals("default", $sanitized);
    }

    /** @test */
    public function sanitize_slug_normalizes_to_lowercase(): void
    {
        $mixed = "MyCustomTheme";
        $sanitized = ThemeConfigResolver::sanitizeSlug($mixed);

        $this->assertEquals("mycustomtheme", $sanitized);
    }

    /** @test */
    public function sanitize_slug_allows_hyphens_and_underscores(): void
    {
        $valid = "my-custom_theme";
        $sanitized = ThemeConfigResolver::sanitizeSlug($valid);

        $this->assertEquals("my-custom_theme", $sanitized);
    }

    /** @test */
    public function sanitize_slug_truncates_to_50_chars(): void
    {
        $long = str_repeat("a", 100);
        $sanitized = ThemeConfigResolver::sanitizeSlug($long);

        $this->assertEquals(50, strlen($sanitized));
    }

    /** @test */
    public function theme_exists_returns_true_for_default(): void
    {
        // 'default' é um tema virtual, sempre existe
        $this->assertTrue(ThemeConfigResolver::themeExists("default"));
    }

    /** @test */
    public function theme_exists_returns_false_for_missing_theme(): void
    {
        $this->assertFalse(ThemeConfigResolver::themeExists("missing-theme"));
    }

    /** @test */
    public function theme_exists_returns_true_for_valid_theme(): void
    {
        $this->createFakeTheme("valid-theme");

        $this->assertTrue(ThemeConfigResolver::themeExists("valid-theme"));
    }

    /** @test */
    public function context_disabled_returns_safe_defaults(): void
    {
        $context = ThemeContext::disabled();

        $this->assertFalse($context->enabled);
        $this->assertEquals("default", $context->slug);
        $this->assertEmpty($context->config);
        $this->assertEmpty($context->loginConfig);
        $this->assertFalse($context->isPreview);
    }

    /** @test */
    public function context_body_classes_includes_theme_slug(): void
    {
        $context = new ThemeContext(
            enabled: true,
            slug: "my-theme",
            scopeKey: "global",
            config: [],
            loginConfig: [],
            isPreview: false,
            customCssAdmin: "",
            customCssLogin: "",
        );

        $classes = $context->bodyClasses();

        $this->assertStringContainsString("theme-enabled", $classes);
        $this->assertStringContainsString("theme-my-theme", $classes);
    }

    /** @test */
    public function context_body_classes_includes_preview_mode(): void
    {
        $context = new ThemeContext(
            enabled: true,
            slug: "preview-theme",
            scopeKey: "global",
            config: [],
            loginConfig: [],
            isPreview: true,
            customCssAdmin: "",
            customCssLogin: "",
        );

        $classes = $context->bodyClasses();

        $this->assertStringContainsString("theme-preview", $classes);
    }

    /** @test */
    public function login_config_uses_correct_precedence(): void
    {
        // Arrange: tema com login config
        $this->createFakeTheme("custom", [
            "login" => [
                "bg_opacity" => 80,
                "show_powered_by" => false,
            ],
        ]);

        // Arrange: DB com override parcial
        $dbConfig = $this->mockDbConfig([
            "selected_theme" => "custom",
            "is_active" => true,
            "login_bg_opacity" => 90, // Override no DB
            "login_show_powered_by" => null, // Null, deve usar theme.json
        ]);

        Cache::put(ThemeCache::KEY_CONFIG, $dbConfig, 3600);

        // Act
        $loginConfig = ThemeConfigResolver::resolveLoginConfig("custom");

        // Assert
        $this->assertEquals(90, $loginConfig["bg_opacity"]); // DB vence
        $this->assertFalse($loginConfig["show_powered_by"]); // theme.json vence
    }

    /** @test */
    public function read_theme_json_returns_empty_for_invalid_json(): void
    {
        // Arrange: cria arquivo com JSON inválido
        Storage::disk("public")->put(
            "themes/broken/theme.json",
            "{ invalid json }",
        );

        // Act
        $result = ThemeConfigResolver::readThemeJson("broken");

        // Assert: retorna array vazio em vez de erro
        $this->assertEquals([], $result);
    }

    /** @test */
    public function read_theme_json_returns_empty_for_default(): void
    {
        // 'default' não tem theme.json (é virtual)
        $result = ThemeConfigResolver::readThemeJson("default");

        $this->assertEquals([], $result);
    }

    // =========================================================================
    // TESTES DE CACHE
    // =========================================================================

    /** @test */
    public function theme_cache_flush_clears_all_keys(): void
    {
        // Arrange
        Cache::put(ThemeCache::KEY_CONTEXT, "test", 3600);
        Cache::put(ThemeCache::KEY_CONFIG, "test", 3600);
        Cache::put(ThemeCache::KEY_AVAILABLE, "test", 3600);

        // Act
        ThemeCache::flush();

        // Assert
        $this->assertNull(Cache::get(ThemeCache::KEY_CONTEXT));
        $this->assertNull(Cache::get(ThemeCache::KEY_CONFIG));
        $this->assertNull(Cache::get(ThemeCache::KEY_AVAILABLE));
    }

    /** @test */
    public function theme_cache_keys_are_versioned(): void
    {
        // Verifica que as keys têm versão v2
        $this->assertStringContainsString(".v2", ThemeCache::KEY_CONTEXT);
        $this->assertStringContainsString(".v2", ThemeCache::KEY_CONFIG);
        $this->assertStringContainsString(".v2", ThemeCache::KEY_AVAILABLE);
    }
}
