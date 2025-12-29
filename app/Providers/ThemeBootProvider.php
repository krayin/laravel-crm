<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * Service Provider para configuração de temas upgrade-safe.
 *
 * Responsabilidades:
 * - Registra namespace de views vendor com prioridade sobre packages
 * - Fornece View Composer com lista de temas disponíveis
 *
 * NOTA: O middleware CaptureThemeSelection é registrado globalmente no Kernel
 * e usa early-return interno para só processar a rota admin.settings.theme.update.
 * Isso é mais confiável do que Route::matched() para rotas de packages.
 */
class ThemeBootProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerViewOverrides();
        $this->registerViewComposers();
        $this->registerThemeStylesInjection();
    }

    /**
     * Registra os overrides de views em resources/views/vendor/*.
     * Estes terão prioridade sobre os views dos packages.
     */
    private function registerViewOverrides(): void
    {
        // Override para namespace 'admin' (package Webkul/Admin)
        $vendorAdminPath = resource_path('views/vendor/admin');
        if (is_dir($vendorAdminPath)) {
            View::prependNamespace('admin', $vendorAdminPath);
        }

        // Override para namespace 'theme-manager' (package Webkul/ThemeManager)
        $vendorThemeManagerPath = resource_path('views/vendor/theme-manager');
        if (is_dir($vendorThemeManagerPath)) {
            View::prependNamespace('theme-manager', $vendorThemeManagerPath);
        }
    }

    /**
     * Registra View Composers para injetar dados nas views.
     */
    private function registerViewComposers(): void
    {
        View::composer('theme-manager::admin.settings.theme.index', function (
            $view,
        ) {
            $view->with('availableThemes', $this->getAvailableThemes());
        });
    }

    /**
     * Injeta CSS do tema no <head> via view_render_event hooks.
     * Usa os hooks 'admin.layout.head.after' e 'admin.layout.head' (anonymous layout).
     */
    private function registerThemeStylesInjection(): void
    {
        $injectStyles = function ($viewRenderEventManager) {
            $themeContext = View::shared('themeContext');

            if (! $themeContext || ! $themeContext->enabled) {
                return;
            }

            try {
                $html = view('vendor.admin.partials.theme-head', [
                    'themeContext' => $themeContext,
                ])->render();

                $viewRenderEventManager->addTemplate($html);
            } catch (\Exception $e) {
                \Log::warning('[ThemeBootProvider] Failed to render theme styles: '.$e->getMessage());
            }
        };

        // Hook para layout principal (admin autenticado)
        \Illuminate\Support\Facades\Event::listen('admin.layout.head.after', $injectStyles);

        // Hook para layout anonymous (login)
        \Illuminate\Support\Facades\Event::listen('admin.layout.head', $injectStyles);
    }

    /**
     * Obtém a lista de temas disponíveis.
     * Varre storage/app/public/themes/* procurando por theme.json válido.
     * Resultado é cacheado por 5 minutos.
     *
     * @return array Lista de temas com dados completos
     */
    private function getAvailableThemes(): array
    {
        return Cache::remember('theme.available.v1', 300, function () {
            return $this->discoverThemes();
        });
    }

    /**
     * Descobre temas válidos no diretório storage/app/public/themes.
     * Um tema é válido se possui um arquivo theme.json.
     *
     * @return array Lista de temas com dados completos
     */
    private function discoverThemes(): array
    {
        $themesPath = storage_path('app/public/themes');
        $themes = [];

        // Verifica se o diretório de temas existe
        if (! File::isDirectory($themesPath)) {
            return $themes;
        }

        // Varre os diretórios dentro de themes/
        $directories = File::directories($themesPath);

        foreach ($directories as $directory) {
            $slug = basename($directory);
            $themeJsonPath = $directory.DIRECTORY_SEPARATOR.'theme.json';

            // Tema só é válido se tiver theme.json
            if (! File::exists($themeJsonPath)) {
                continue;
            }

            // Lê e parseia o theme.json
            $themeData = $this->readThemeJson($themeJsonPath);

            // Monta array com dados completos do tema
            $themes[] = [
                'slug'        => $slug,
                'name'        => $themeData['name'] ?? ucfirst($slug),
                'description' => $themeData['description'] ?? null,
                'version'     => $themeData['version'] ?? null,
                'author'      => $themeData['author'] ?? null,
                'preview'     => $themeData['preview'] ?? null,
            ];
        }

        return $themes;
    }

    /**
     * Lê e parseia um arquivo theme.json com tratamento de erros.
     *
     * @param  string  $path  Caminho absoluto para o theme.json
     * @return array Dados do tema ou array vazio em caso de erro
     */
    private function readThemeJson(string $path): array
    {
        try {
            $contents = File::get($path);
            $data = json_decode($contents, true);

            // json_decode retorna null em caso de JSON inválido
            if (! is_array($data)) {
                return [];
            }

            return $data;
        } catch (\Exception $e) {
            // Log silencioso - não interrompe a aplicação
            // Pode adicionar logging se necessário:
            // \Log::warning("Erro ao ler theme.json: {$path}", ['error' => $e->getMessage()]);
            return [];
        }
    }
}
