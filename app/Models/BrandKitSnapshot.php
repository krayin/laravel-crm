<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\User\Models\User;

class BrandKitSnapshot extends Model
{
    protected $table = "brand_kit_snapshots";

    protected $fillable = [
        "scope_key",
        "theme_slug",
        "name",
        "snapshot_version",
        "overrides_data",
        "custom_css_data",
        "is_auto",
        "created_by",
    ];

    protected $casts = [
        "overrides_data" => "array",
        "custom_css_data" => "array",
        "is_auto" => "boolean",
        "snapshot_version" => "integer",
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "created_by");
    }
}
