<?php

namespace Webkul\Contact\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Attribute\Traits\CustomAttribute;
use Webkul\Attribute\Models\AttributeValueProxy;
use Webkul\Contact\Contracts\Organization as OrganizationContract;

class Organization extends Model implements OrganizationContract
{
    use CustomAttribute;

    protected $casts = [
        'address' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'address',
    ];

    /**
     * Get the attribute values that owns the organization.
     */
    public function attribute_values()
    {
        return $this->morphMany(AttributeValueProxy::modelClass(), 'entity');
    }
}
