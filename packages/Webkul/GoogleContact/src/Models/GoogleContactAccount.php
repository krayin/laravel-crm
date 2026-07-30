<?php

namespace Webkul\GoogleContact\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\GoogleContact\Contracts\GoogleContactAccount as GoogleContactAccountContract;
use Webkul\User\Models\UserProxy;

class GoogleContactAccount extends Model implements GoogleContactAccountContract
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'google_contact_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'google_email',
        'access_token',
        'refresh_token',
        'expires_at',
        'scopes',
    ];

    /**
     * The attributes that are castable.
     *
     * @var array
     */
    protected $casts = [
        'access_token'  => 'encrypted',
        'refresh_token' => 'encrypted',
        'expires_at'    => 'datetime',
        'scopes'        => 'array',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    /**
     * Get the user that owns the Google account connection.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserProxy::modelClass());
    }
}
