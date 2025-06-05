<?php

namespace Webkul\User\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Webkul\User\Models\User;
use Webkul\User\Models\RoleProxy;
use Webkul\User\Models\GroupProxy; 
use Webkul\User\Contracts\UserTenant as UserTenantContract;

class UserTenant extends Model implements UserTenantContract
{
    use BelongsToTenant;

    protected $table = 'user_tenants';

    protected $fillable = [
        'user_id',
        'tenant_id',
        'role_id',
        'status',
        'view_permission',
    ];

    /**
     * User that owns this pivot.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Role assigned in this tenant.
     */
    public function role()
    {
        return $this->belongsTo(RoleProxy::modelClass());
    }
    
    /**
     * Groups the user belongs to in this tenant.
     */
    public function groups()
    {
        return $this->belongsToMany(GroupProxy::modelClass(), 'user_groups', 'user_tenant_id', 'group_id');
    }
}
