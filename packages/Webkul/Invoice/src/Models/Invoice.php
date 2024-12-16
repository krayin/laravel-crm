<?php

namespace Webkul\Invoice\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Invoice\Contracts\Invoice as InvoiceContract;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model implements InvoiceContract
{

    use softDeletes;

    protected $fillable = [
        'client_id',
        'invoice_date',
        'invoice_number',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
