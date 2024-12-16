<?php

namespace Webkul\Asset\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Asset\Contracts\Asset as AssetContract;

class Asset extends Model implements AssetContract
{
    protected $fillable = [
        'item_type',
        'item_brand',
        'item_name',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
