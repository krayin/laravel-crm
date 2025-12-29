<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\User\Models\User;

class BrandKitOverride extends Model
{
    // tabela padrao seria brand_kit_overrides, entao e opcional declarar
    protected $table = 'brand_kit_overrides';

    protected $fillable = [
        'scope_key',
        'theme_slug',
        'override_key', // <- IMPORTANTE (coluna renomeada)
        'value',
        'is_active',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Retorna overrides ativos com valor real (nao null/vazio)
     * Regra: override so entra se is_active=1 AND value preenchido
     *
     * Retorno: [override_key => value]
     */
    public static function getActiveOverrides(
        string $scopeKey,
        string $themeSlug,
    ): array {
        return static::query()
            ->where('scope_key', $scopeKey)
            ->where('theme_slug', $themeSlug)
            ->where('is_active', true)
            ->whereNotNull('value')
            ->where('value', '!=', '')
            ->pluck('value', 'override_key')
            ->toArray();
    }
}
