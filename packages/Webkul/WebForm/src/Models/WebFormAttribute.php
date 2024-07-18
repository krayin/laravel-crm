<?php

namespace Webkul\WebForm\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Attribute\Models\AttributeProxy;
use Webkul\WebForm\Contracts\WebFormAttribute as WebFormAttributeContract;

class WebFormAttribute extends Model implements WebFormAttributeContract
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'placeholder',
        'is_required',
        'is_hidden',
        'sort_order',
        'attribute_id',
        'web_form_id',
    ];

    /**
     * Get the attribute that owns the attribute.
     */
    public function attribute()
    {
        return $this->belongsTo(AttributeProxy::modelClass());
    }

    /**
     * Get the web_form that owns the attribute.
     */
    public function web_form()
    {
        return $this->belongsTo(WebFormProxy::modelClass());
    }
}
