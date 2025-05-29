<?php

namespace Webkul\User\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\User\Contracts\Role as RoleContract;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Role extends Model implements RoleContract
{
    use BelongsToTenant;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'permission_type',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    /**
     * Get the users.
     */
    public function users()
    {
        return $this->hasMany(UserProxy::modelClass());
    }
}
