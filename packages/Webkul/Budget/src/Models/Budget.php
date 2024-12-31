<?php

namespace Webkul\Budget\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Budget\Contracts\Budget as BudgetContract;

class Budget extends Model implements BudgetContract
{
    protected $fillable = [];
}