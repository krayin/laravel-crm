<?php

namespace Tests\Unit\Services\Theme;

use App\Services\Theme\ThemeCatalog;
use Tests\TestCase;

class ThemeCatalogTest extends TestCase
{
    protected ThemeCatalog $catalog;

    protected function setUp(): void
    {
        parent::setUp();
        $this->catalog = new ThemeCatalog;
    }

    /**
     * @test
     */
    public function it_always_includes_default_theme(): void
    {
        $themes = $this->catalog->all();

        $this->assertArrayHasKey('default', $themes);
        $this->assertEquals('default', $themes['default']['slug']);
        $this->assertEquals('Padrão Krayin', $themes['default']['name']);
    }

    /**
     * @test
     */
    public function default_theme_has_colors_array(): void
    {
        $themes = $this->catalog->all();
        $default = $themes['default'];

        $this->assertArrayHasKey('colors', $default);
        $this->assertIsArray($default['colors']);

        // Verify all required color keys exist
        $requiredColors = ['primary', 'primary_dark', 'primary_light', 'success', 'warning', 'danger'];
        foreach ($requiredColors as $colorKey) {
            $this->assertArrayHasKey($colorKey, $default['colors'], "Missing color key: {$colorKey}");
        }
    }

    /**
     * @test
     */
    public function all_themes_expose_colors_subarray(): void
    {
        $themes = $this->catalog->all();

        foreach ($themes as $slug => $theme) {
            $this->assertArrayHasKey('colors', $theme, "Theme '{$slug}' is missing 'colors' key");
            $this->assertIsArray($theme['colors'], "Theme '{$slug}' colors should be an array");

            // Verify all required color keys exist
            $requiredColors = ['primary', 'primary_dark', 'primary_light', 'success', 'warning', 'danger'];
            foreach ($requiredColors as $colorKey) {
                $this->assertArrayHasKey(
                    $colorKey,
                    $theme['colors'],
                    "Theme '{$slug}' is missing color: {$colorKey}"
                );
            }
        }
    }

    /**
     * @test
     */
    public function theme_has_required_metadata_fields(): void
    {
        $themes = $this->catalog->all();

        foreach ($themes as $slug => $theme) {
            $this->assertArrayHasKey('slug', $theme, "Theme missing 'slug'");
            $this->assertArrayHasKey('name', $theme, "Theme missing 'name'");
            $this->assertArrayHasKey('version', $theme, "Theme missing 'version'");
            $this->assertArrayHasKey('description', $theme, "Theme missing 'description'");
            $this->assertArrayHasKey('colors', $theme, "Theme missing 'colors'");

            // Slug should match array key
            $this->assertEquals($slug, $theme['slug']);
        }
    }

    /**
     * @test
     */
    public function colors_are_valid_hex_format(): void
    {
        $themes = $this->catalog->all();
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
    public function get_returns_specific_theme(): void
    {
        $default = $this->catalog->get('default');

        $this->assertNotNull($default);
        $this->assertEquals('default', $default['slug']);
        $this->assertArrayHasKey('colors', $default);
    }

    /**
     * @test
     */
    public function get_returns_null_for_nonexistent_theme(): void
    {
        $result = $this->catalog->get('nonexistent-theme-xyz');

        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function exists_returns_true_for_default(): void
    {
        $this->assertTrue($this->catalog->exists('default'));
    }

    /**
     * @test
     */
    public function exists_returns_false_for_nonexistent(): void
    {
        $this->assertFalse($this->catalog->exists('nonexistent-theme-xyz'));
    }

    /**
     * @test
     */
    public function slugs_returns_array_of_strings(): void
    {
        $slugs = $this->catalog->slugs();

        $this->assertIsArray($slugs);
        $this->assertContains('default', $slugs);

        foreach ($slugs as $slug) {
            $this->assertIsString($slug);
        }
    }
}
