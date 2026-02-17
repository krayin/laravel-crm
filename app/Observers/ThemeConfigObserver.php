<?php

namespace App\Observers;

use App\Support\Theme\InvalidateThemeCaches;

/**
 * ThemeConfigObserver - Garante invalidação de cache em QUALQUER atualização de ThemeConfig.
 *
 * Este observer captura:
 * - Updates via UI (ThemeController)
 * - Updates via CLI (artisan tinker, commands)
 * - Updates via API (endpoints futuros)
 * - Updates via scripts/migrations
 *
 * IMPORTANTE:
 * - Registrado dinamicamente no ThemeOverridesServiceProvider
 * - Só carrega se a classe ThemeConfig do pacote existir
 * - Upgrade-safe: não edita vendor/ ou packages/
 */
class ThemeConfigObserver
{
    /**
     * Handle the ThemeConfig "saved" event.
     * Disparado após create() ou update().
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     */
    public function saved($model): void
    {
        InvalidateThemeCaches::all('ThemeConfigObserver:saved');
    }

    /**
     * Handle the ThemeConfig "deleted" event.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     */
    public function deleted($model): void
    {
        InvalidateThemeCaches::all('ThemeConfigObserver:deleted');
    }

    /**
     * Handle the ThemeConfig "restored" event (soft deletes).
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     */
    public function restored($model): void
    {
        InvalidateThemeCaches::all('ThemeConfigObserver:restored');
    }
}
