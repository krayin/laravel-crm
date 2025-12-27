<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\User\Models\User;

class BrandKitCustomCss extends Model
{
    protected $table = "brand_kit_custom_css";

    protected $fillable = [
        "scope_key",
        "theme_slug",
        "name",
        "css_content",
        "target", // string: admin|login|both
        "is_enabled",
        "priority",
        "created_by",
    ];

    protected $casts = [
        "is_enabled" => "boolean",
        "priority" => "integer",
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "created_by");
    }

    /**
     * Retorna CSS concatenado para um target especifico (admin/login)
     * e tambem aplica "both".
     */
    public static function getEnabledCss(
        string $scopeKey,
        string $themeSlug,
        string $target,
    ): string {
        return static::query()
            ->where("scope_key", $scopeKey)
            ->where("theme_slug", $themeSlug)
            ->where("is_enabled", true)
            ->where(function ($q) use ($target) {
                $q->where("target", $target)->orWhere("target", "both");
            })
            ->orderBy("priority")
            ->pluck("css_content")
            ->implode("\n\n");
    }
}
