<?php

namespace Webkul\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Inventory\Contracts\Inventory as InventoryContract;

class Inventory extends Model implements InventoryContract
{

    protected $table = 'inventory';
    
    protected $fillable = [
        'product_name',
        'sku',
        'amount',
        'stock',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
