<?php

namespace Webkul\Consignment\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Consignment\Contracts\Consignment as ConsignmentContract;

class Consignment extends Model implements ConsignmentContract
{
    protected $fillable = [];
}