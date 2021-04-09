<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Attribute\Traits\CustomAttribute;
use Webkul\Attribute\Models\AttributeValueProxy;
use Webkul\Product\Contracts\Product as ProductContract;

class Product extends Model implements ProductContract
{
    use CustomAttribute;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Get the product attribute values that owns the product.
     */
    public function attribute_values()
    {
        return $this->hasMany(AttributeValueProxy::modelClass(), 'entity_id')->where('entity_type', 'products');
    }
}
