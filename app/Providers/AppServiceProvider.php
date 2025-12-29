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

        // View Composer para injetar availableThemes na view do ThemeManager
        View::composer('theme-manager::admin.settings.theme.index', function (
            $view,
        ) {
            $resolver = app(\App\Support\BrandKitResolver::class);
            $availableThemes = $this->getAvailableThemesWithMetadata($resolver);
            $view->with('availableThemes', $availableThemes);
        });
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
            ];

            if (file_exists($themeJsonPath)) {
                $json = json_decode(file_get_contents($themeJsonPath), true);
                if (is_array($json)) {
                    $metadata['name'] = $json['name'] ?? $metadata['name'];
                    $metadata['description'] = $json['description'] ?? '';
                    $metadata['version'] = $json['version'] ?? '1.0.0';
                }
            }

            $themes[] = $metadata;
        }

        return $themes;
    }
}
