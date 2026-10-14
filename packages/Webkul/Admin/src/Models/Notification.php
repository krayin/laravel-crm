<?php

namespace Webkul\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Admin\Contracts\Notification as NotificationContract;
use Webkul\User\Models\UserProxy;

class Notification extends Model implements NotificationContract
{
    protected $table = 'notifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entity_type',
        'action',
        'entity_id',
        'user_id',
        'causer_id',
        'title',
        'description',
        'route',
        'is_read',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * The user the notification is addressed to.
     */
    public function user()
    {
        return $this->belongsTo(UserProxy::modelClass(), 'user_id');
    }

    /**
     * The user whose action produced the notification.
     */
    public function causer()
    {
        return $this->belongsTo(UserProxy::modelClass(), 'causer_id');
    }
}
