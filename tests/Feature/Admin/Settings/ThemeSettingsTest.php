<?php

namespace Tests\Feature\Admin\Settings;

use App\Models\ThemeConfig;
use App\Repositories\ThemeConfigRepository;
use App\Services\Theme\ThemeCatalog;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;
use Webkul\User\Models\User;

class ThemeSettingsTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the ThemeConfigRepository to avoid database dependency
        $mockConfig = Mockery::mock(ThemeConfig::class);
        $mockConfig->shouldReceive('getAttribute')->with('selected_theme')->andReturn('default');
        $mockConfig->shouldReceive('getAttribute')->with('is_active')->andReturn(true);
        $mockConfig->shouldReceive('offsetGet')->andReturnUsing(function ($key) {
            return match($key) {
                'selected_theme' => 'default',
                'is_active' => true,
                default => null,
            };
        });
        $mockConfig->shouldReceive('offsetExists')->andReturn(true);

        $mockRepo = Mockery::mock(ThemeConfigRepository::class);
        $mockRepo->shouldReceive('get')->andReturn($mockConfig);

        $this->app->instance(ThemeConfigRepository::class, $mockRepo);

        // Mock User authentication
        $mockUser = Mockery::mock(User::class);
        $mockUser->shouldReceive('getAuthIdentifier')->andReturn(1);
        $mockUser->shouldReceive('getAuthIdentifierName')->andReturn('id');
        $mockUser->shouldReceive('getKey')->andReturn(1);
        $mockUser->shouldReceive('getAttribute')->andReturn(null);

        $this->actingAs($mockUser, 'user');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function theme_catalog_returns_default_theme_with_colors(): void
    {
        $catalog = new ThemeCatalog();
        $themes = $catalog->all();

        $this->assertArrayHasKey('default', $themes);
        $this->assertArrayHasKey('colors', $themes['default']);
        $this->assertArrayHasKey('primary', $themes['default']['colors']);
    }

    /**
     * @test
     */
    public function theme_catalog_all_themes_have_required_color_keys(): void
    {
        $catalog = new ThemeCatalog();
        $themes = $catalog->all();

        $requiredColors = ['primary', 'primary_dark', 'primary_light', 'success', 'warning', 'danger'];

        foreach ($themes as $slug => $theme) {
            $this->assertArrayHasKey('colors', $theme, "Theme '{$slug}' missing 'colors'");

            foreach ($requiredColors as $colorKey) {
                $this->assertArrayHasKey(
                    $colorKey,
                    $theme['colors'],
                    "Theme '{$slug}' missing 'colors.{$colorKey}'"
                );
            }
        }
    }

    /**
     * @test
     */
    public function theme_catalog_colors_are_valid_hex(): void
    {
        $catalog = new ThemeCatalog();
        $themes = $catalog->all();

        $hexPattern = '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/';

        foreach ($themes as $slug => $theme) {
            foreach ($theme['colors'] as $colorName => $colorValue) {
                $this->assertMatchesRegularExpression(
                    $hexPattern,
                    $colorValue,
                    "Theme '{$slug}' has invalid hex color for '{$colorName}': {$colorValue}"
                );
            }
        }
    }

    /**
     * @test
     */
    public function theme_catalog_default_theme_has_correct_structure(): void
    {
        $catalog = new ThemeCatalog();
        $default = $catalog->get('default');

        $this->assertNotNull($default);
        $this->assertEquals('default', $default['slug']);
        $this->assertEquals('Padrão Krayin', $default['name']);
        $this->assertArrayHasKey('version', $default);
        $this->assertArrayHasKey('description', $default);
        $this->assertArrayHasKey('colors', $default);
    }

    /**
     * @test
     */
    public function theme_settings_controller_prepares_themes_for_js(): void
    {
        $catalog = new ThemeCatalog();
        $availableThemes = $catalog->all();

        // Simulate what the controller does
        $themesForJs = [];
        foreach ($availableThemes as $theme) {
            $themesForJs[$theme['slug']] = [
                'slug'   => $theme['slug'],
                'name'   => $theme['name'] ?? 'Unnamed',
                'colors' => $theme['colors'] ?? [],
            ];
        }

        // Verify structure
        $this->assertArrayHasKey('default', $themesForJs);
        $this->assertArrayHasKey('slug', $themesForJs['default']);
        $this->assertArrayHasKey('name', $themesForJs['default']);
        $this->assertArrayHasKey('colors', $themesForJs['default']);
        $this->assertArrayHasKey('primary', $themesForJs['default']['colors']);
    }

    /**
     * @test
     */
    public function route_admin_settings_theme_index_exists(): void
    {
        $this->assertTrue(
            \Illuminate\Support\Facades\Route::has('admin.settings.theme.index'),
            'Route admin.settings.theme.index does not exist'
        );
    }

    /**
     * @test
     */
    public function route_points_to_correct_controller(): void
    {
        $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('admin.settings.theme.index');

        $this->assertNotNull($route);
        $this->assertStringContainsString(
            'ThemeSettingsController',
            $route->getActionName()
        );
    }

    /**
     * @test
     */
    public function route_uses_app_controller_not_package(): void
    {
        $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('admin.settings.theme.index');

        $this->assertNotNull($route);
        $this->assertStringContainsString(
            'App\\Http\\Controllers\\Admin\\Settings\\ThemeSettingsController',
            $route->getActionName()
        );
        $this->assertStringNotContainsString(
            'Webkul\\ThemeManager',
            $route->getActionName()
        );
    }
}
