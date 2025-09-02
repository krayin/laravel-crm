<?php

namespace Webkul\Marketing\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Marketing\Contracts\Event as EventContract;

class Event extends Model implements EventContract
{
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

    public function campaigns()
    {
        return $this->hasMany(CampaignProxy::modelClass(), 'marketing_event_id');
    }
}
