<?php

namespace Webkul\Attribute\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Attribute\Contracts\Attribute as AttributeContract;

class Attribute extends Model implements AttributeContract
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'type',
        'entity_type',
        'lookup_type',
        'is_required',
        'is_unique',
        'quick_add',
        'validation',
        'is_user_defined',
    ];

    /**
     * Get the options.
     */
    public function options()
    {
        return $this->hasMany(AttributeOptionProxy::modelClass());
    }
}
