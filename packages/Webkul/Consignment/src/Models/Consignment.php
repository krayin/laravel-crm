<?php

namespace Webkul\Consignment\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Consignment\Contracts\Consignment as ConsignmentContract;

class Consignment extends Model implements ConsignmentContract
{
    protected $table = 'consignments_stock';
    protected $fillable = [
        'consignment_id',
        'product_id',
        'quantity',
        'amount',
        'date'
    ];
}
