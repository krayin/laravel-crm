<?php

namespace Webkul\AssetsAllocation\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\AssetsAllocation\Contracts\AssetsAllocation as AssetsAllocationContract;

class AssetsAllocation extends Model implements AssetsAllocationContract
{
    protected $fillable = [
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
