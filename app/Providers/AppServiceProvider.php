<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Konekt\Concord\Facades\Concord;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Support\CssValidator::class);
        $this->app->singleton(\App\Support\BrandKitResolver::class);
        $this->app->singleton(\App\Support\ThemeSelectionResolver::class);
        $this->app->singleton(\App\Support\ThemeSlugResolver::class);
        $this->app->singleton(\App\Support\ThemeContextFactory::class);
        $this->app->singleton(\App\Repositories\BrandKitRepository::class);

        // BrandKit Services
        $this->app->singleton(\App\Services\BrandKitSnapshotService::class);
        $this->app->singleton(\App\Services\BrandKitCacheInvalidator::class);

        // Override Webkul's ThemeConfig model with our extended version
        // that includes selected_theme in fillable
        $this->app->bind(
            \Webkul\ThemeManager\Contracts\ThemeConfig::class,
            \App\Models\ThemeConfig::class,
        );

        // Override Webkul's ThemeConfigRepository to use our extended model
        $this->app->bind(
            \Webkul\ThemeManager\Repositories\ThemeConfigRepository::class,
            \App\Repositories\ThemeConfigRepository::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register our extended ThemeConfig model to override Webkul's
        // This allows selected_theme to be mass-assigned
        Concord::registerModel(
            \Webkul\ThemeManager\Contracts\ThemeConfig::class,
            \App\Models\ThemeConfig::class,
        );

        // REMOVIDO: View Composer que sobrescrevia $availableThemes
        // O ThemeController já fornece $availableThemes com cores corretas
        // View::composer('theme-manager::admin.settings.theme.index', ...);
    }

    /**
     * Get available themes with metadata from theme.json
     */
    private function getAvailableThemesWithMetadata(
        \App\Support\BrandKitResolver $resolver,
    ): array {
        $themeSlugs = $resolver->getAvailableThemes();
        $themes = [];

        foreach ($themeSlugs as $slug) {
            $themeJsonPath = storage_path(
                "app/public/themes/{$slug}/theme.json",
            );

            $metadata = [
                'slug'        => $slug,
                'name'        => ucfirst($slug),
                'description' => '',
                'version'     => '1.0.0',
                'author'      => '',
                'preview'     => '',
                'colors'      => [
                    'primary'       => '#1E40AF',
                    'primary_dark'  => '#1E3A8A',
                    'primary_light' => '#3B82F6',
                    'success'       => '#10B981',
                    'warning'       => '#F59E0B',
                    'danger'        => '#EF4444',
                ],
            ];

            if (file_exists($themeJsonPath)) {
                $json = json_decode(file_get_contents($themeJsonPath), true);
                if (is_array($json)) {
                    $metadata['name'] = $json['name'] ?? $metadata['name'];
                    $metadata['description'] = $json['description'] ?? '';
                    $metadata['version'] = $json['version'] ?? '1.0.0';
                    $metadata['author'] = $json['author'] ?? '';

                    // Carregar cores do theme.json
                    $metadata['colors'] = [
                        'primary'       => $json['color_primary'] ?? '#1E40AF',
                        'primary_dark'  => $json['color_primary_dark'] ?? '#1E3A8A',
                        'primary_light' => $json['color_primary_light'] ?? '#3B82F6',
                        'success'       => $json['color_success'] ?? '#10B981',
                        'warning'       => $json['color_warning'] ?? '#F59E0B',
                        'danger'        => $json['color_danger'] ?? '#EF4444',
                    ];
                }
            }

            $themes[] = $metadata;
        }

        return $themes;
    }
}
