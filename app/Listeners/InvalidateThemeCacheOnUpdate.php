<?php

namespace App\Listeners;

use App\Support\BrandKitResolver;
use App\Support\ThemeSelectionResolver;
use Illuminate\Support\Facades\Log;

/**
 * Invalida caches de tema quando o ThemeManager atualiza configuracoes.
 *
 * Escuta: theme.update.after (disparado por ThemeController::update)
 *
 * Resolve o problema de cache de 5 minutos quando usuario troca
 * o tema selecionado - agora reflete imediatamente.
 */
class InvalidateThemeCacheOnUpdate
{
    public function __construct(
        private readonly ThemeSelectionResolver $themeSelectionResolver,
        private readonly BrandKitResolver $brandKitResolver,
    ) {}

    /**
     * Handle the event.
     *
     * @param mixed $config ThemeConfig model ou array
     */
    public function handle($config): void
    {
        // 1) Invalida cache do tema selecionado (ThemeSelectionResolver)
        $this->themeSelectionResolver->invalidate();

        // 2) Invalida cache do BrandKit para todos os temas (garante refresh)
        $this->brandKitResolver->invalidateAllGlobal();

        Log::info('[Theme] Cache invalidated after theme update');
    }
}
