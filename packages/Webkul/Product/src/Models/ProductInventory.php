<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Product\Contracts\ProductInventory as ProductInventoryContract;
use Webkul\Warehouse\Models\LocationProxy;
use Webkul\Warehouse\Models\WarehouseProxy;

class ProductInventory extends Model implements ProductInventoryContract
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'in_stock',
        'allocated',
        'product_id',
        'warehouse_id',
        'warehouse_location_id',
    ];

    /**
     * Interact with the name.
     */
    protected function onHand(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->in_stock - $this->allocated,
            set: fn ($value) => $this->in_stock - $this->allocated
        );
    }

    /**
     * Get the product that owns the product inventory.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductProxy::modelClass());
    }

    /**
     * Get the product attribute family that owns the product.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(WarehouseProxy::modelClass());
    }

    /**
     * Get the product attribute family that owns the product.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(LocationProxy::modelClass(), 'warehouse_location_id');
    }
}
