<?php

namespace Webkul\Marketing\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Webkul\Marketing\Contracts\Event as EventContract;

class Event extends Model implements EventContract
{
    use BelongsToTenant;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'marketing_events';

    /**
     * The attributes that are fillable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'date',
    ];
}
