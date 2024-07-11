<?php

namespace Webkul\Warehouse\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Webkul\Warehouse\Contracts\Location as LocationContract;

class Location extends Model implements LocationContract
{
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
        'aisle',
        'bay',
        'shelf',
        'bin',
        'warehouse_id',
    ];

    /**
     * Interact with the name.
     */
    protected function name(): Attribute
    {
        $attributes = array_filter([
            $this->attributes['aisle'],
            $this->attributes['bay'],
            $this->attributes['shelf'],
            $this->attributes['bin'],
        ], fn ($item) => null !== $item && '' !== $item);

        return Attribute::make(
            set: fn (string $value) => implode('.', $attributes)
        );
    }

    /**
     * Get the warehouse that owns the location.
     */
    public function warehouse()
    {
        return $this->belongsTo(WarehouseProxy::modelClass());
    }
}
