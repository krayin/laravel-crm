<?php

namespace Webkul\Email\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\User\Models\UserProxy;
use Webkul\Contact\Models\PersonProxy;
use Webkul\Email\Contracts\Email as EmailContract;

class Email extends Model implements EmailContract
{
    protected $table = 'emails';

    protected $casts = [
        'reference_ids' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject',
        'source',
        'message_id',
        'reference_ids',
        'is_trashed',
        'user_id',
        'person_id',
    ];

    /**
     * Get the threads.
     */
    public function threads()
    {
        return $this->hasMany(ThreadProxy::modelClass());
    }

    /**
     * Get the user that owns the thread.
     */
    public function user()
    {
        return $this->belongsTo(UserProxy::modelClass());
    }

    /**
     * Get the person that owns the thread.
     */
    public function person()
    {
        return $this->belongsTo(PersonProxy::modelClass());
    }
}
