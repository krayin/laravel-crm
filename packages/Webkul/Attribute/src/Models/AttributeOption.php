<?php

namespace Webkul\Attribute\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Attribute\Contracts\AttributeOption as AttributeOptionContract;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class AttributeOption extends Model implements AttributeOptionContract
{
    use BelongsToTenant;
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'sort_order',
        'attribute_id',
    ];

    /**
     * Get the attribute that owns the attribute option.
     */
    public function attribute()
    {
        return $this->belongsTo(AttributeProxy::modelClass());
    }
}
