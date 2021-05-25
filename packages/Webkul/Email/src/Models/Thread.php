<?php

namespace Webkul\Email\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\User\Models\UserProxy;
use Webkul\Contact\Models\PersonProxy;
use Webkul\Email\Contracts\Thread as ThreadContract;

class Thread extends Model implements ThreadContract
{
    protected $table = 'email_threads';

    protected $casts = [
        'reply_to' => 'array',
        'cc'       => 'array',
        'bcc'      => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source',
        'type',
        'user_type',
        'name',
        'reply_to',
        'cc',
        'bcc',
        'message_id',
        'reply',
        'email_id',
        'user_id',
        'person_id',
    ];

    /**
     * Get the email that owns the thread.
     */
    public function email()
    {
        return $this->belongsTo(EmailProxy::modelClass());
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
