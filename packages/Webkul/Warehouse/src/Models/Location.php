<?php

namespace Webkul\Warehouse\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Webkul\Warehouse\Contracts\Location as LocationContract;

class Location extends Model implements LocationContract
{
    use BelongsToTenant;

    /**
     * The table associated with the model.
     */
    protected $table = 'warehouse_locations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'warehouse_id',
    ];

    /**
     * Get the warehouse that owns the location.
     */
    public function warehouse()
    {
        return $this->belongsTo(WarehouseProxy::modelClass());
    }
}
