<?php

namespace Webkul\Approval\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Approval\Contracts\Approval as ApprovalContract;

class Approval extends Model implements ApprovalContract
{
    protected $fillable = [

        'expense_id',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
