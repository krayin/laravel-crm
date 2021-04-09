<?php

namespace Webkul\Contact\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Attribute\Traits\CustomAttribute;
use Webkul\Attribute\Models\AttributeValueProxy;
use Webkul\Contact\Contracts\Organization as OrganizationContract;

class Organization extends Model implements OrganizationContract
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
        return $this->hasMany(AttributeValueProxy::modelClass(), 'entity_id')->where('entity_type', 'organizations');
    }
}
