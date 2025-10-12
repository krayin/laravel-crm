<?php

namespace Famindo\AnalyticalCRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EngineeringOrder extends Model
{
    protected $table = 'engineering_orders';

    protected $guarded = [];

    public function items(): HasMany
    {
        return $this->hasMany(EngineeringOrderItem::class, 'order_id');
    }
}

