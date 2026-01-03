<?php

namespace App\Console\Commands;

use App\Support\Theme\InvalidateThemeCaches;
use Illuminate\Console\Command;

/**
 * ThemeCacheCleanCommand - Limpa (e opcionalmente aquece) cache de temas/BrandKit.
 *
 * Uso:
 *   php artisan theme:cache:clean
 *   php artisan theme:cache:clean --reason=post-deploy --warm
 *   php artisan theme:cache:clean --quiet
 *
 * Seguro para produção: não deleta nada fora do escopo tema/brandkit.
 * Compatível com CACHE_DRIVER=file (não usa Cache::tags).
 */
class ThemeCacheCleanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:cache:clean
                            {--reason=manual : Motivo da limpeza (registrado no log)}
                            {--warm : Aquecer cache após limpeza}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpa cache de temas e BrandKit (upgrade-safe)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $reason = $this->option('reason');
        $warm = $this->option('warm');
        $quiet = $this->option('quiet'); // Opção nativa do Artisan

        // Header
        if (! $quiet) {
            $this->printHeader();
        }

        // Limpar cache
        if (! $quiet) {
            $this->info('Limpando cache de temas...');
        }

        try {
            InvalidateThemeCaches::all($reason);

            if (! $quiet) {
                $this->info('  [OK] Cache invalidado');
                $this->line("  Reason: {$reason}");
            }
        } catch (\Throwable $e) {
            $this->error('  [ERRO] Falha ao invalidar cache: '.$e->getMessage());

            return 1;
        }

        // Warming (opcional)
        if ($warm) {
            if (! $quiet) {
                $this->newLine();
                $this->info('Aquecendo cache...');
            }

            $this->warmCache($quiet);
        }

        // Finalização
        if (! $quiet) {
            $this->newLine();
            $this->info('Concluído! Verifique: storage/logs/laravel.log');
        }

        return 0;
    }

    /**
     * Imprime header com informações do ambiente e config.
     */
    protected function printHeader(): void
    {
        $driver = config('cache.default', 'file');
        $env = app()->environment();

        // Info do config centralizado
        $configInfo = InvalidateThemeCaches::getConfigInfo();
        $version = $configInfo['version'];
        $ttlShort = $configInfo['ttl_short'].'s';
        $ttlLong = $configInfo['ttl_long'].'s';
        $keysCount = $configInfo['keys_count'];

        $this->newLine();
        $this->line('╔══════════════════════════════════════════╗');
        $this->line('║       Theme Cache Clean Command          ║');
        $this->line('╠══════════════════════════════════════════╣');
        $this->line("║  Driver:      {$this->padRight($driver, 26)}║");
        $this->line("║  Environment: {$this->padRight($env, 26)}║");
        $this->line('╠──────────────────────────────────────────╣');
        $this->line("║  Cache Version: {$this->padRight($version, 24)}║");
        $this->line("║  TTL Short:     {$this->padRight($ttlShort, 24)}║");
        $this->line("║  TTL Long:      {$this->padRight($ttlLong, 24)}║");
        $this->line("║  Keys Count:    {$this->padRight((string) $keysCount, 24)}║");
        $this->line('╚══════════════════════════════════════════╝');
        $this->newLine();
    }

    /**
     * Pad string à direita para alinhamento.
     */
    protected function padRight(string $str, int $length): string
    {
        return str_pad($str, $length);
    }

    /**
     * Aquece o cache de temas (opcional, nunca falha o comando).
     */
    protected function warmCache(bool $quiet): void
    {
        // 1. ThemeSelectionResolver - força recálculo do slug selecionado
        $this->warmThemeSelection($quiet);

        // 2. ThemeHelper - força recálculo do config
        $this->warmThemeConfig($quiet);

        // 3. BrandKitResolver - força recálculo do tema ativo
        $this->warmBrandKit($quiet);
    }

    /**
     * Aquece cache de seleção de tema.
     */
    protected function warmThemeSelection(bool $quiet): void
    {
        try {
            if (class_exists(\App\Support\ThemeSelectionResolver::class)) {
                $resolver = app(\App\Support\ThemeSelectionResolver::class);

                if (method_exists($resolver, 'getSelectedThemeSlug')) {
                    $slug = $resolver->getSelectedThemeSlug();

                    if (! $quiet) {
                        $this->line("  [OK] ThemeSelectionResolver: {$slug}");
                    }

                    return;
                }
            }

            if (! $quiet) {
                $this->line('  [SKIP] ThemeSelectionResolver não disponível');
            }
        } catch (\Throwable $e) {
            if (! $quiet) {
                $this->warn('  [WARN] ThemeSelectionResolver: '.$e->getMessage());
            }
        }
    }

    /**
     * Aquece cache de configuração do tema (ThemeHelper).
     */
    protected function warmThemeConfig(bool $quiet): void
    {
        try {
            // Tenta via app('theme') que é o singleton do ThemeHelper
            if (app()->bound('theme')) {
                $helper = app('theme');

                if (method_exists($helper, 'getConfig')) {
                    $config = $helper->getConfig();

                    if (! $quiet) {
                        $active = $config->is_active ?? false;
                        $status = $active ? 'ativo' : 'inativo';
                        $this->line("  [OK] ThemeHelper: tema {$status}");
                    }

                    return;
                }
            }

            if (! $quiet) {
                $this->line('  [SKIP] ThemeHelper não disponível');
            }
        } catch (\Throwable $e) {
            if (! $quiet) {
                $this->warn('  [WARN] ThemeHelper: '.$e->getMessage());
            }
        }
    }

    /**
     * Aquece cache do BrandKit para o tema selecionado.
     */
    protected function warmBrandKit(bool $quiet): void
    {
        try {
            if (class_exists(\App\Support\BrandKitResolver::class)) {
                $resolver = app(\App\Support\BrandKitResolver::class);

                if (method_exists($resolver, 'resolve')) {
                    // Pega o slug atual
                    $slug = 'default';

                    if (class_exists(\App\Support\ThemeSelectionResolver::class)) {
                        $selResolver = app(\App\Support\ThemeSelectionResolver::class);
                        $slug = $selResolver->getSelectedThemeSlug();
                    }

                    // Resolve para popular o cache
                    $resolver->resolve('global', $slug);

                    if (! $quiet) {
                        $this->line("  [OK] BrandKitResolver: global/{$slug}");
                    }

                    return;
                }
            }

            if (! $quiet) {
                $this->line('  [SKIP] BrandKitResolver não disponível');
            }
        } catch (\Throwable $e) {
            if (! $quiet) {
                $this->warn('  [WARN] BrandKitResolver: '.$e->getMessage());
            }
        }
    }
}
