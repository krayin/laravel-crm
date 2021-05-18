<?php

namespace Webkul\Lead\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\User\Models\UserProxy;
use Webkul\Lead\Contracts\Activity as ActivityContract;

class Activity extends Model implements ActivityContract
{
    protected $table = 'lead_activities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'comment',
        'additional',
        'schedule_from',
        'schedule_to',
        'is_done',
        'lead_id',
        'user_id',
    ];

    /**
     * Get the lead that owns the activity.
     */
    public function lead()
    {
        return $this->belongsTo(LeadProxy::modelClass());
    }

    /**
     * Get the user that owns the activity.
     */
    public function user()
    {
        return $this->belongsTo(UserProxy::modelClass());
    }
}
