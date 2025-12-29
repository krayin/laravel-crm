<?php

namespace App\Services;

use App\Support\BrandKitResolver;
use App\Support\ThemeCache;
use Illuminate\Support\Facades\Log;

/**
 * Centralizador de invalidação de cache do BrandKit.
 *
 * IMPORTANTE: Controllers e services NUNCA devem chamar Cache::forget() diretamente.
 * Sempre usar este serviço para garantir invalidação determinística e completa.
 *
 * Responsabilidades:
 * - Invalida cache do BrandKitResolver (brand_kit.resolved.*)
 * - Invalida cache do ThemeCache (theme.context.*, theme.config.*)
 * - Log de auditoria de invalidações
 */
class BrandKitCacheInvalidator
{
    public function __construct(
        private readonly BrandKitResolver $brandKitResolver,
    ) {}

    /**
     * Invalida cache após alteração no BrandKit.
     * Deve ser chamado após save/update de:
     * - brand_kit_overrides
     * - brand_kit_custom_css
     * - theme_configs
     * - Restore de snapshot
     *
     * @param  string  $scopeKey  Escopo (ex: 'global')
     * @param  string  $themeSlug  Tema específico ou '*' para todos
     */
    public function afterBrandKitChange(string $scopeKey, string $themeSlug): void
    {
        // 1. Invalida cache específico do BrandKitResolver
        if ($themeSlug === '*') {
            $this->brandKitResolver->invalidateAllGlobal();
        } else {
            $this->brandKitResolver->invalidate($scopeKey, $themeSlug);
        }

        // 2. Invalida cache global do ThemeCache
        // Isso é necessário porque ThemeContext depende do BrandKit
        ThemeCache::flush();

        Log::debug('[BrandKitCacheInvalidator] Cache invalidado', [
            'scope_key'  => $scopeKey,
            'theme_slug' => $themeSlug,
        ]);
    }

    /**
     * Invalida TODO o cache de tema e brandkit.
     * Usar após restauração de snapshot ou operações de bulk.
     */
    public function invalidateAll(): void
    {
        // Invalida todos os caches do BrandKitResolver
        $this->brandKitResolver->invalidateAllGlobal();

        // Invalida cache do ThemeCache
        ThemeCache::flush();

        Log::debug('[BrandKitCacheInvalidator] Full cache flush executado');
    }

    /**
     * Invalida cache após troca de tema selecionado.
     * Mais específico que afterBrandKitChange.
     */
    public function afterThemeSwitch(string $newThemeSlug): void
    {
        // O tema mudou, então precisamos invalidar tudo
        $this->invalidateAll();

        Log::info('[BrandKitCacheInvalidator] Tema alterado', [
            'new_theme' => $newThemeSlug,
        ]);
    }

    /**
     * Invalida cache após restore de snapshot.
     * Sempre faz flush completo por segurança.
     */
    public function afterSnapshotRestore(string $scopeKey, string $themeSlug): void
    {
        // Restore pode ter mudado muitos valores, então flush completo
        $this->invalidateAll();

        Log::info('[BrandKitCacheInvalidator] Cache invalidado após restore', [
            'scope_key'  => $scopeKey,
            'theme_slug' => $themeSlug,
        ]);
    }
}
