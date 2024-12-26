<?php

namespace Webkul\Transaction\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Transaction\Contracts\Transaction as TransactionContract;

class Transaction extends Model implements TransactionContract
{
    protected $fillable = [];
}