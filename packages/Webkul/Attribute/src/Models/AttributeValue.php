<?php

namespace Webkul\Attribute\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Attribute\Contracts\AttributeValue as AttributeValueContract;

class AttributeValue extends Model implements AttributeValueContract
{
    public $timestamps = false;

    protected $fillable = [
        'attribute_id',
        'text_value',
        'boolean_value',
        'integer_value',
        'float_value',
        'datetime_value',
        'date_value',
        'json_value',
        'entity_id',
        'entity_type',
    ];

    /**
     * Get the attribute that owns the attribute value.
     */
    public function attribute()
    {
        return $this->belongsTo(AttributeProxy::modelClass());
    }
}