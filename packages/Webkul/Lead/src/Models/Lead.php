<?php

namespace Webkul\Lead\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Attribute\Traits\CustomAttribute;
use Webkul\Attribute\Models\AttributeValueProxy;
use Webkul\Lead\Contracts\Lead as LeadContract;

class Lead extends Model implements LeadContract
{
    use CustomAttribute;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    ];

    /**
     * Get the product attribute values that owns the product.
     */
    public function attribute_values()
    {
        return $this->hasMany(AttributeValueProxy::modelClass(), 'entity_id')->where('entity_type', 'leads');
    }
}
