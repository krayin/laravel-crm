<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(\App\Support\CssValidator::class);
        $this->app->singleton(\App\Support\BrandKitResolver::class);
        $this->app->singleton(\App\Support\ThemeSelectionResolver::class);
        $this->app->singleton(\App\Support\ThemeContextFactory::class);
        $this->app->singleton(\App\Repositories\BrandKitRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // View Composer para injetar availableThemes na view do ThemeManager
        View::composer("theme-manager::admin.settings.theme.index", function (
            $view,
        ) {
            $resolver = app(\App\Support\BrandKitResolver::class);
            $availableThemes = $this->getAvailableThemesWithMetadata($resolver);
            $view->with("availableThemes", $availableThemes);
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
                "slug" => $slug,
                "name" => ucfirst($slug),
                "description" => "",
                "version" => "1.0.0",
            ];

            if (file_exists($themeJsonPath)) {
                $json = json_decode(file_get_contents($themeJsonPath), true);
                if (is_array($json)) {
                    $metadata["name"] = $json["name"] ?? $metadata["name"];
                    $metadata["description"] = $json["description"] ?? "";
                    $metadata["version"] = $json["version"] ?? "1.0.0";
                }
            }

            $themes[] = $metadata;
        }

        return $themes;
    }
}
