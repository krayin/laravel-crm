<?php

namespace Webkul\Reporting\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Reporting\Contracts\Reporting as ReportingContract;

class Reporting extends Model implements ReportingContract
{
    protected $fillable = [];
}