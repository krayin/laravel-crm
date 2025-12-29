<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\User\Models\User;

class BrandKitOverride extends Model
{
    /**
     * Sentinela para indicar "limpar este campo" (voltar ao default da camada inferior).
     * Usado quando is_active=true e value=null.
     */
    public const CLEAR_SENTINEL = '__CLEAR__';

    protected $table = 'brand_kit_overrides';

    protected $fillable = [
        'scope_key',
        'theme_slug',
        'override_key',
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
     * Retorna overrides ativos com valor real (não null/vazio).
     * ATENÇÃO: Este método ignora CLEARs. Use getActiveOverridesWithClears() para lógica completa.
     *
     * @deprecated Use getActiveOverridesWithClears() para suportar clear semantics
     *
     * @return array<string, string> [override_key => value]
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

    /**
     * Retorna overrides ativos incluindo CLEARs.
     *
     * Regras de Clear Semantics:
     * - is_active=true, value=null     → CLEAR (remove valor das camadas inferiores)
     * - is_active=true, value=""       → CLEAR (idem)
     * - is_active=true, value="X"      → Override com valor "X"
     * - is_active=false                → Ignorado (como se não existisse)
     *
     * @return array<string, string|null> [override_key => value|CLEAR_SENTINEL]
     */
    public static function getActiveOverridesWithClears(
        string $scopeKey,
        string $themeSlug,
    ): array {
        $overrides = static::query()
            ->where('scope_key', $scopeKey)
            ->where('theme_slug', $themeSlug)
            ->where('is_active', true)
            ->get(['override_key', 'value']);

        $result = [];

        foreach ($overrides as $override) {
            $key = $override->override_key;
            $value = $override->value;

            // null ou "" com is_active=true significa CLEAR
            if ($value === null || $value === '') {
                $result[$key] = self::CLEAR_SENTINEL;
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Define um override (com valor ou CLEAR).
     *
     * @param  string|null  $value  null = CLEAR
     * @param  int|null  $updatedBy  User ID
     */
    public static function setOverride(
        string $scopeKey,
        string $themeSlug,
        string $overrideKey,
        ?string $value,
        ?int $updatedBy = null,
    ): self {
        return static::updateOrCreate(
            [
                'scope_key'    => $scopeKey,
                'theme_slug'   => $themeSlug,
                'override_key' => $overrideKey,
            ],
            [
                'value'      => $value, // null = CLEAR
                'is_active'  => true,
                'updated_by' => $updatedBy,
            ],
        );
    }

    /**
     * Remove um override (desativa, não deleta).
     */
    public static function removeOverride(
        string $scopeKey,
        string $themeSlug,
        string $overrideKey,
    ): int {
        return static::query()
            ->where('scope_key', $scopeKey)
            ->where('theme_slug', $themeSlug)
            ->where('override_key', $overrideKey)
            ->update(['is_active' => false]);
    }

    /**
     * Verifica se um valor representa CLEAR.
     */
    public static function isClear(?string $value): bool
    {
        return $value === self::CLEAR_SENTINEL;
    }
}
