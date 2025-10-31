<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Activity\Models\ActivityProxy;
use Webkul\Activity\Traits\LogsActivity;
use Webkul\Attribute\Traits\CustomAttribute;
use Webkul\Product\Contracts\Product as ProductContract;
use Webkul\Product\Models\ProductCategoryProxy;
use Webkul\Tag\Models\TagProxy;
use Webkul\Warehouse\Models\LocationProxy;
use Webkul\Warehouse\Models\WarehouseProxy;

class Product extends Model implements ProductContract
{
    use CustomAttribute, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'sku',
        'description',
        'quantity',
        'price',
        // New enhanced fields
        'type',
        'reference',
        'barcode',
        'cost',
        'volume',
        'weight',
        'enable_sales',
        'enable_purchase',
        'is_favorite',
        'images',
        'description_purchase',
        'description_sale',
        'status',
        'category_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:4',
        'cost' => 'decimal:4',
        'volume' => 'decimal:4',
        'weight' => 'decimal:4',
        'enable_sales' => 'boolean',
        'enable_purchase' => 'boolean',
        'is_favorite' => 'boolean',
        'images' => 'array',
    ];

    /**
     * Set the price attribute.
     * Handle empty strings by converting them to null.
     */
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = ($value === '' || $value === null) ? null : $value;
    }

    /**
     * Set the cost attribute.
     * Handle empty strings by converting them to null.
     */
    public function setCostAttribute($value)
    {
        $this->attributes['cost'] = ($value === '' || $value === null) ? null : $value;
    }

    /**
     * Set the volume attribute.
     * Handle empty strings by converting them to null.
     */
    public function setVolumeAttribute($value)
    {
        $this->attributes['volume'] = ($value === '' || $value === null) ? null : $value;
    }

    /**
     * Set the weight attribute.
     * Handle empty strings by converting them to null.
     */
    public function setWeightAttribute($value)
    {
        $this->attributes['weight'] = ($value === '' || $value === null) ? null : $value;
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include saleable products.
     */
    public function scopeSaleable($query)
    {
        return $query->where('enable_sales', true)->where('status', 'active');
    }

    /**
     * Scope a query to only include purchaseable products.
     */
    public function scopePurchaseable($query)
    {
        return $query->where('enable_purchase', true)->where('status', 'active');
    }

    /**
     * Scope a query to only include favorite products.
     */
    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }

    /**
     * Get the is_available attribute.
     */
    public function getIsAvailableAttribute(): bool
    {
        return $this->status === 'active' && $this->getTotalStockAttribute() > 0;
    }

    /**
     * Get the total_stock attribute.
     */
    public function getTotalStockAttribute(): int
    {
        return $this->inventories()->sum('in_stock');
    }

    /**
     * Get the main_image attribute.
     */
    public function getMainImageAttribute(): ?string
    {
        return $this->images[0] ?? null;
    }

    /**
     * Check if product is a service.
     */
    public function isService(): bool
    {
        return $this->type === 'service';
    }

    /**
     * Check if product is digital.
     */
    public function isDigital(): bool
    {
        return $this->type === 'digital';
    }

    /**
     * Check if product has physical properties.
     */
    public function hasPhysicalProperties(): bool
    {
        return $this->type === 'product';
    }

    /**
     * Get the category that the product belongs to.
     */
    public function category()
    {
        return $this->belongsTo(ProductCategoryProxy::modelClass());
    }

    /**
     * Get the product warehouses that owns the product.
     */
    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(WarehouseProxy::modelClass(), 'product_inventories');
    }

    /**
     * Get the product locations that owns the product.
     */
    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(LocationProxy::modelClass(), 'product_inventories', 'product_id', 'warehouse_location_id');
    }

    /**
     * Get the product inventories that owns the product.
     */
    public function inventories(): HasMany
    {
        return $this->hasMany(ProductInventoryProxy::modelClass());
    }

    /**
     * The tags that belong to the Products.
     */
    public function tags()
    {
        return $this->belongsToMany(TagProxy::modelClass(), 'product_tags');
    }

    /**
     * Get the activities.
     */
    public function activities()
    {
        return $this->belongsToMany(ActivityProxy::modelClass(), 'product_activities');
    }
}
