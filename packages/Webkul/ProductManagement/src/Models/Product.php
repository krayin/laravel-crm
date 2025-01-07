<?php

namespace Webkul\ProductManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\ProductManagement\Contracts\Product as ProductContract;

class Product extends Model implements ProductContract
{

    protected $table = 'product_management';

    protected $fillable = [
        'name',
    ];
}
