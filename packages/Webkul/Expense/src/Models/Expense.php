<?php

namespace Webkul\Expense\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Expense\Contracts\Expense as ExpenseContract;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model implements ExpenseContract
{

    use softDeletes;

    protected $fillable = [

        'expense_type',
        'expense_head',
        'expense_date',
        'description',
        'attachments',
        'mode',
        'expense_by',
        'payment_mode',
        'is_reimburse',
        'is_verified',
        'created_by',
        'updated_by',
        'deleted_by',

    ];
}
