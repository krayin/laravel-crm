<?php

namespace Webkul\Attribute\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Activity\Traits\LogsActivity;
use Webkul\Attribute\Contracts\AttributeValue as AttributeValueContract;

class AttributeValue extends Model implements AttributeValueContract
{
    use LogsActivity;

    public $timestamps = false;

    protected $casts = [
        'json_value' => 'array',
    ];

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
     * @var array
     */
    public static $attributeTypeFields = [
        'text'        => 'text_value',
        'textarea'    => 'text_value',
        'price'       => 'float_value',
        'boolean'     => 'boolean_value',
        'select'      => 'integer_value',
        'multiselect' => 'text_value',
        'checkbox'    => 'text_value',
        'email'       => 'json_value',
        'address'     => 'json_value',
        'phone'       => 'json_value',
        'lookup'      => 'integer_value',
        'datetime'    => 'datetime_value',
        'date'        => 'date_value',
        'file'        => 'text_value',
        'image'       => 'text_value',
    ];

    /**
     * Get the attribute that owns the attribute value.
     */
    public function attribute()
    {
        return $this->belongsTo(AttributeProxy::modelClass());
    }

    /**
     * Get the parent entity model (leads, products, persons or organizations).
     */
    public function entity()
    {
        return $this->morphTo();
    }
}
