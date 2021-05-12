<?php

namespace Webkul\Lead\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Lead\Contracts\Product as ProductContract;

class Product extends Model implements ProductContract
{
    protected $table = 'lead_products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'quantity',
        'price',
        'amount',
        'product_id',
        'lead_id',
    ];

    /**
     * Get the lead that owns the product.
     */
    public function lead()
    {
        return $this->belongsTo(LeadProxy::modelClass());
    }
}
