<?php

namespace Webkul\Activity\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Activity\Contracts\Participant as ParticipantContract;
use Webkul\Contact\Models\PersonProxy;
use Webkul\User\Models\UserProxy;

class Participant extends Model implements ParticipantContract
{
    public $timestamps = false;

    protected $table = 'activity_participants';

    protected $with = ['user', 'person'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'activity_id',
        'user_id',
        'person_id',
    ];

    /**
     * Get the activity that owns the participant.
     */
    public function activity()
    {
        return $this->belongsTo(ActivityProxy::modelClass());
    }

    /**
     * Get the user that owns the participant.
     */
    public function user()
    {
        return $this->belongsTo(UserProxy::modelClass());
    }

    /**
     * Get the person that owns the participant.
     */
    public function person()
    {
        return $this->belongsTo(PersonProxy::modelClass());
    }
}
