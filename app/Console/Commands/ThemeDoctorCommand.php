<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * ThemeDoctorCommand - Diagnóstico de problemas comuns de tema/preview/caches.
 *
 * Uso:
 *   php artisan theme:doctor           # Diagnóstico completo
 *   php artisan theme:doctor --fix=1   # Tenta corrigir problemas
 *   php artisan theme:doctor --json=1  # Output JSON para scripts/CI
 *
 * Seguro para produção: apenas leitura (exceto --fix para storage:link).
 * Upgrade-safe: não edita vendor/ ou packages/.
 */
class ThemeDoctorCommand extends Command
{
    protected $signature = 'theme:doctor
                            {--fix=0 : Tentar corrigir problemas automaticamente}
                            {--json=0 : Output em formato JSON}';

    protected $description = 'Diagnóstico de problemas de tema, preview e cache';

    private array $notes = [];

    private array $diagnostics = [];

    public function handle(): int
    {
        $fix = (bool) $this->option('fix');
        $json = (bool) $this->option('json');

        // Coletar diagnósticos
        $this->checkEnvironment();
        $this->checkStorageLink($fix);
        $this->checkThemes();
        $this->checkDatabase();

        // Output
        if ($json) {
            $this->outputJson();
        } else {
            $this->outputHuman();
        }

        return 0;
    }

    /**
     * Seção A: Ambiente
     */
    private function checkEnvironment(): void
    {
        $this->diagnostics['env'] = app()->environment();
        $this->diagnostics['cache_driver'] = config('cache.default', 'file');
        $this->diagnostics['config_cached'] = app()->configurationIsCached();
        $this->diagnostics['routes_cached'] = app()->routesAreCached();

        // Verificar se views estão cacheadas (heurística)
        $viewCachePath = storage_path('framework/views');
        $cachedViews = File::isDirectory($viewCachePath)
            ? count(File::glob($viewCachePath.'/*.php'))
            : 0;
        $this->diagnostics['views_cached_count'] = $cachedViews;

        // Theme cache config
        $this->diagnostics['theme_cache_version'] = config('theme_cache.version', 'v2');
        $this->diagnostics['theme_cache_ttl_short'] = config('theme_cache.ttl.short', 300);
        $this->diagnostics['theme_cache_ttl_long'] = config('theme_cache.ttl.long', 3600);

        if ($this->diagnostics['config_cached']) {
            $this->notes[] = 'Config está cacheado. Rode "php artisan config:clear" após mudanças.';
        }
    }

    /**
     * Seção B: Storage Link
     */
    private function checkStorageLink(bool $fix): void
    {
        $publicStoragePath = public_path('storage');
        $isLink = is_link($publicStoragePath);
        $exists = file_exists($publicStoragePath);

        // Windows: is_link() pode falhar em junctions/symlinks criados pelo Git Bash
        // Verificar se aponta para o diretório correto como fallback
        $isValidLink = $isLink;
        if (! $isLink && $exists) {
            // Verificar se o diretório themes existe via link (indica symlink/junction válido)
            $themesViaLink = public_path('storage/themes');
            $themesReal = storage_path('app/public/themes');

            if (is_dir($themesViaLink) && is_dir($themesReal)) {
                $isValidLink = true;
            }
        }

        $this->diagnostics['storage_link_exists'] = $isLink;
        $this->diagnostics['storage_link_valid'] = $isValidLink;
        $this->diagnostics['storage_path_exists'] = $exists;

        if (! $exists) {
            $this->diagnostics['storage_link_ok'] = false;
            $this->notes[] = 'Storage link não existe! Previews não funcionarão.';

            if ($fix) {
                $this->tryFixStorageLink();
            } else {
                $this->notes[] = 'Execute: php artisan storage:link';
            }
        } elseif ($isValidLink || $isLink) {
            $this->diagnostics['storage_link_ok'] = true;
        } else {
            // Existe mas não parece ser um link válido
            $this->diagnostics['storage_link_ok'] = false;
            $this->notes[] = 'public/storage existe mas pode não ser um symlink válido. Verifique manualmente.';
        }
    }

    /**
     * Tenta criar o storage link.
     */
    private function tryFixStorageLink(): void
    {
        try {
            Artisan::call('storage:link');
            $this->diagnostics['storage_link_fixed'] = true;
            $this->notes[] = '[FIX] Storage link criado com sucesso!';
        } catch (\Throwable $e) {
            $this->diagnostics['storage_link_fixed'] = false;
            $this->notes[] = '[FIX FAILED] Não foi possível criar storage link: '.$e->getMessage();
        }
    }

    /**
     * Seção C: Temas (catálogo)
     */
    private function checkThemes(): void
    {
        $themesPath = storage_path('app/public/themes');
        $themes = [];
        $themesWithPreview = 0;

        if (! File::isDirectory($themesPath)) {
            $this->diagnostics['themes_dir_exists'] = false;
            $this->diagnostics['themes_found'] = 0;
            $this->diagnostics['themes_with_preview'] = 0;
            $this->diagnostics['themes'] = [];
            $this->notes[] = 'Diretório de temas não existe: storage/app/public/themes/';

            return;
        }

        $this->diagnostics['themes_dir_exists'] = true;

        // Buscar todos os theme.json
        $themeDirs = File::directories($themesPath);

        foreach ($themeDirs as $dir) {
            $slug = basename($dir);
            $jsonPath = $dir.DIRECTORY_SEPARATOR.'theme.json';

            if (! File::exists($jsonPath)) {
                continue;
            }

            $themeData = json_decode(File::get($jsonPath), true);

            if (! is_array($themeData)) {
                continue;
            }

            $previewFile = $themeData['preview'] ?? null;
            $previewExists = false;
            $previewPath = null;
            $previewUrlAbsolute = null;

            if ($previewFile) {
                $previewFullPath = $dir.DIRECTORY_SEPARATOR.$previewFile;
                $previewExists = File::exists($previewFullPath);
                // Path relativo (nao depende de APP_URL)
                $previewPath = "/storage/themes/{$slug}/{$previewFile}";
                // URL absoluta (informativa, pode ter porta errada)
                $previewUrlAbsolute = asset("storage/themes/{$slug}/{$previewFile}");

                if ($previewExists) {
                    $themesWithPreview++;
                }
            }

            $themes[] = [
                'slug'               => $slug,
                'name'               => $themeData['name'] ?? $slug,
                'has_preview'        => ! empty($previewFile),
                'preview_file'       => $previewFile,
                'preview_exists'     => $previewExists,
                'preview_path'       => $previewPath,
                'preview_url_absolute' => $previewUrlAbsolute,
            ];
        }

        $this->diagnostics['themes_found'] = count($themes);
        $this->diagnostics['themes_with_preview'] = $themesWithPreview;
        $this->diagnostics['themes'] = $themes;

        // Notas sobre problemas
        foreach ($themes as $theme) {
            if ($theme['has_preview'] && ! $theme['preview_exists']) {
                $this->notes[] = "Tema '{$theme['slug']}': preview declarado mas arquivo não existe.";
            }
        }
    }

    /**
     * Seção D: Database / ThemeConfig
     */
    private function checkDatabase(): void
    {
        $modelClass = 'Webkul\ThemeManager\Models\ThemeConfig';

        if (! class_exists($modelClass)) {
            $this->diagnostics['db_model_exists'] = false;
            $this->diagnostics['db_themeconfig'] = null;
            $this->notes[] = 'Model ThemeConfig não encontrado (pacote não instalado?).';

            return;
        }

        $this->diagnostics['db_model_exists'] = true;

        try {
            $config = $modelClass::query()->first();

            if (! $config) {
                $this->diagnostics['db_themeconfig'] = null;
                $this->notes[] = 'Nenhum registro ThemeConfig encontrado no banco.';

                return;
            }

            $this->diagnostics['db_themeconfig'] = [
                'id'             => $config->id ?? null,
                'is_active'      => $config->is_active ?? false,
                'selected_theme' => $config->selected_theme ?? null,
                'color_primary'  => $config->color_primary ?? null,
                'updated_at'     => $config->updated_at?->toIso8601String() ?? null,
            ];
        } catch (\Throwable $e) {
            $this->diagnostics['db_themeconfig'] = null;
            $this->diagnostics['db_error'] = $e->getMessage();
            $this->notes[] = 'Erro ao acessar ThemeConfig: '.$e->getMessage();
        }
    }

    /**
     * Output em formato humano (terminal).
     */
    private function outputHuman(): void
    {
        $this->newLine();
        $this->line('╔══════════════════════════════════════════════════════╗');
        $this->line('║            Theme Doctor - Diagnóstico                ║');
        $this->line('╚══════════════════════════════════════════════════════╝');

        // Seção A: Ambiente
        $this->newLine();
        $this->info('▶ AMBIENTE');
        $this->line('  Environment:    '.$this->diagnostics['env']);
        $this->line('  Cache Driver:   '.$this->diagnostics['cache_driver']);
        $this->line('  Config Cached:  '.($this->diagnostics['config_cached'] ? 'Sim' : 'Não'));
        $this->line('  Routes Cached:  '.($this->diagnostics['routes_cached'] ? 'Sim' : 'Não'));
        $this->line('  Views Cached:   '.$this->diagnostics['views_cached_count'].' arquivos');
        $this->line('  Theme Version:  '.$this->diagnostics['theme_cache_version']);

        // Seção B: Storage
        $this->newLine();
        $this->info('▶ STORAGE LINK');
        if ($this->diagnostics['storage_link_ok'] ?? false) {
            $this->line('  Status: <fg=green>OK</> - Symlink existe');
        } else {
            $this->line('  Status: <fg=red>PROBLEMA</> - Symlink não existe ou inválido');
        }

        // Seção C: Temas
        $this->newLine();
        $this->info('▶ TEMAS DISPONÍVEIS');
        $this->line('  Diretório:      '.($this->diagnostics['themes_dir_exists'] ? 'Existe' : 'Não existe'));
        $this->line('  Temas:          '.$this->diagnostics['themes_found']);
        $this->line('  Com preview:    '.$this->diagnostics['themes_with_preview']);

        if (! empty($this->diagnostics['themes'])) {
            $this->newLine();
            $this->line('  Detalhes (primeiros 5):');

            $count = 0;
            foreach ($this->diagnostics['themes'] as $theme) {
                if ($count >= 5) {
                    break;
                }

                $previewStatus = 'sem preview';
                if ($theme['has_preview']) {
                    $previewStatus = $theme['preview_exists']
                        ? '<fg=green>preview OK</>'
                        : '<fg=red>preview MISSING</>';
                }

                $this->line("    - {$theme['slug']}: {$previewStatus}");

                if ($theme['preview_path']) {
                    // Mostrar path relativo primeiro (sempre correto)
                    $this->line("      Path: {$theme['preview_path']}");
                }

                $count++;
            }
        }

        // Seção D: Database
        $this->newLine();
        $this->info('▶ DATABASE (ThemeConfig)');

        if (! ($this->diagnostics['db_model_exists'] ?? false)) {
            $this->line('  Model: <fg=yellow>Não encontrado</>');
        } elseif ($this->diagnostics['db_themeconfig'] === null) {
            $this->line('  Registro: <fg=yellow>Nenhum</>');
        } else {
            $db = $this->diagnostics['db_themeconfig'];
            $activeStatus = ($db['is_active'] ?? false) ? '<fg=green>Ativo</>' : '<fg=yellow>Inativo</>';
            $this->line("  Status:         {$activeStatus}");
            $this->line('  Tema:           '.($db['selected_theme'] ?? 'N/A'));
            $this->line('  Cor primária:   '.($db['color_primary'] ?? 'N/A'));
            $this->line('  Atualizado:     '.($db['updated_at'] ?? 'N/A'));
        }

        // Notas/Avisos
        if (! empty($this->notes)) {
            $this->newLine();
            $this->info('▶ NOTAS E AVISOS');
            foreach ($this->notes as $note) {
                if (str_starts_with($note, '[FIX]')) {
                    $this->line("  <fg=green>{$note}</>");
                } elseif (str_starts_with($note, '[FIX FAILED]')) {
                    $this->line("  <fg=red>{$note}</>");
                } else {
                    $this->line("  <fg=yellow>⚠</> {$note}");
                }
            }
        }

        // Comandos úteis
        $this->newLine();
        $this->info('▶ COMANDOS ÚTEIS');
        $this->line('  php artisan storage:link          # Criar symlink');
        $this->line('  php artisan theme:cache:clean     # Limpar cache de temas');
        $this->line('  php artisan config:clear          # Limpar config cache');
        $this->line('  php artisan view:clear            # Limpar view cache');

        $this->newLine();
    }

    /**
     * Output em formato JSON.
     */
    private function outputJson(): void
    {
        $output = [
            'env'                     => $this->diagnostics['env'],
            'cache_driver'            => $this->diagnostics['cache_driver'],
            'config_cached'           => $this->diagnostics['config_cached'],
            'routes_cached'           => $this->diagnostics['routes_cached'],
            'views_cached_count'      => $this->diagnostics['views_cached_count'],
            'theme_cache_version'     => $this->diagnostics['theme_cache_version'],
            'storage_link_ok'         => $this->diagnostics['storage_link_ok'] ?? false,
            'storage_link_fixed'      => $this->diagnostics['storage_link_fixed'] ?? null,
            'themes_dir_exists'       => $this->diagnostics['themes_dir_exists'] ?? false,
            'themes_found'            => $this->diagnostics['themes_found'] ?? 0,
            'themes_with_preview'     => $this->diagnostics['themes_with_preview'] ?? 0,
            'themes'                  => $this->diagnostics['themes'] ?? [],
            'db_model_exists'         => $this->diagnostics['db_model_exists'] ?? false,
            'db_themeconfig'          => $this->diagnostics['db_themeconfig'] ?? null,
            'notes'                   => $this->notes,
        ];

        $this->line(json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
}
