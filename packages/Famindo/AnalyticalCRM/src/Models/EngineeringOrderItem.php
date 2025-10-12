<?php

namespace Famindo\AnalyticalCRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EngineeringOrderItem extends Model
{
    protected $table = 'engineering_order_items';

    protected $guarded = [];

    public function order(): BelongsTo
    {
        return $this->belongsTo(EngineeringOrder::class, 'order_id');
    }
}

