<?php

namespace Webkul\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Order\Contracts\Order as OrderContract;

class Order extends Model implements OrderContract
{
    protected $fillable = [

        'client_id',
        'order_name',
        'order_id',
        'po_number',
        'po_description',
        'order_amount',
        'order_tax',
        'total_order_amount',
        'order_date',
        'po_attachment',
        'created_by',
        'updated_by',
        'deleted_by',


    ];
}
