<?php

namespace App\Support\Theme;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * InvalidateThemeCaches - Helper central para invalidar TODOS os caches de tema.
 *
 * Este helper garante invalidação completa independente do caminho de código:
 * - UI (ThemeController)
 * - CLI (artisan tinker, commands)
 * - API (endpoints futuros)
 * - Observers (ThemeConfigObserver)
 *
 * IMPORTANTE:
 * - NÃO usa Cache::tags (compatível com CACHE_DRIVER=file)
 * - NÃO depende de pacotes externos
 * - NÃO edita vendor/ ou packages/ (upgrade-safe)
 * - Usa config/theme_cache.php como fonte de verdade para keys
 * - Fallback seguro se classes não existirem
 */
final class InvalidateThemeCaches
{
    /**
     * Invalida TODOS os caches relacionados a temas.
     *
     * Chama os invalidadores existentes se disponíveis,
     * com fallback para Cache::forget direto nas keys do config.
     *
     * @param  string|null  $reason  Motivo da invalidação (para logs)
     */
    public static function all(?string $reason = null): void
    {
        $invalidated = [];
        $errors = [];
        $fallbackKeys = [];

        // 1) ThemeHelper do pacote (theme_config)
        try {
            if (self::classMethodExists('Webkul\ThemeManager\Helpers\ThemeHelper', 'clearCache')) {
                $helper = app('theme');
                $helper->clearCache();
                $invalidated[] = 'ThemeHelper::clearCache()';
            }
        } catch (\Throwable $e) {
            $errors[] = 'ThemeHelper: '.$e->getMessage();
        }

        // 2) ThemeCache do app (context, config, available)
        try {
            if (self::classMethodExists(\App\Support\ThemeCache::class, 'flush')) {
                \App\Support\ThemeCache::flush();
                $invalidated[] = 'ThemeCache::flush()';
            }
        } catch (\Throwable $e) {
            $errors[] = 'ThemeCache: '.$e->getMessage();
        }

        // 3) ThemeSelectionResolver do app (selected_slug)
        try {
            if (self::classMethodExists(\App\Support\ThemeSelectionResolver::class, 'invalidate')) {
                $resolver = app(\App\Support\ThemeSelectionResolver::class);
                $resolver->invalidate();
                $invalidated[] = 'ThemeSelectionResolver::invalidate()';
            }
        } catch (\Throwable $e) {
            $errors[] = 'ThemeSelectionResolver: '.$e->getMessage();
        }

        // 4) BrandKitResolver do app (brand_kit.resolved.*)
        try {
            if (self::classMethodExists(\App\Support\BrandKitResolver::class, 'invalidateAllGlobal')) {
                $resolver = app(\App\Support\BrandKitResolver::class);
                $resolver->invalidateAllGlobal();
                $invalidated[] = 'BrandKitResolver::invalidateAllGlobal()';
            }
        } catch (\Throwable $e) {
            $errors[] = 'BrandKitResolver: '.$e->getMessage();
        }

        // 5) Fallback: limpa keys do config diretamente
        // (garante limpeza mesmo se classes não existirem ou falharem)
        $configKeys = self::buildKeysFromConfig();

        foreach ($configKeys as $key) {
            try {
                Cache::forget($key);
                $fallbackKeys[] = $key;
            } catch (\Throwable $e) {
                // Ignora erros de keys individuais
            }
        }

        // 6) Log estruturado
        Log::info('theme.cache.invalidated', [
            'reason'        => $reason ?? 'manual',
            'driver'        => config('cache.default', 'file'),
            'version'       => config('theme_cache.version', 'v2'),
            'invalidated'   => $invalidated,
            'fallback_keys' => $fallbackKeys,
            'errors'        => $errors,
            'timestamp'     => now()->toIso8601String(),
        ]);
    }

    /**
     * Constrói lista de cache keys concretas a partir do config.
     *
     * @return array<string>
     */
    private static function buildKeysFromConfig(): array
    {
        $keys = [];

        // Keys do registry
        $registry = config('theme_cache.keys', []);

        foreach ($registry as $baseName => $config) {
            if (isset($config['version']) && $config['version']) {
                // Key versionada: base.version
                $keys[] = "{$baseName}.{$config['version']}";
            } else {
                // Key exata (sem versão)
                $keys[] = $baseName;
            }
        }

        // Legacy keys
        $legacyKeys = config('theme_cache.legacy_keys', []);
        foreach ($legacyKeys as $legacyKey) {
            $keys[] = $legacyKey;
        }

        return array_unique($keys);
    }

    /**
     * Retorna informações do config para exibição.
     *
     * @return array{version: string, ttl_short: int, ttl_long: int, keys_count: int}
     */
    public static function getConfigInfo(): array
    {
        return [
            'version'    => config('theme_cache.version', 'v2'),
            'ttl_short'  => config('theme_cache.ttl.short', 300),
            'ttl_long'   => config('theme_cache.ttl.long', 3600),
            'keys_count' => count(config('theme_cache.keys', [])),
        ];
    }

    /**
     * Verifica se uma classe e método existem de forma segura.
     */
    private static function classMethodExists(string $class, string $method): bool
    {
        return class_exists($class) && method_exists($class, $method);
    }
}
