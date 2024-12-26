<?php

namespace Webkul\PriceFinder\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\PriceFinder\Contracts\PriceFinder as PriceFinderContract;

class PriceFinder extends Model implements PriceFinderContract
{
    protected $fillable = [];
}