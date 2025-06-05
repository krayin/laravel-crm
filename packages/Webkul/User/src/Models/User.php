<?php

namespace Webkul\User\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Webkul\User\Contracts\User as UserContract;
use Webkul\User\Models\UserTenant;

class User extends Authenticatable implements UserContract
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'image',
        'password',
        'api_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'api_token',
        'remember_token',
        'current_tenant_pivot',
    ];

    /**
     * The attributes that should be appended to the array form.
     *
     * @var array
     */
    protected $appends = [
        'image_url',
        'role_id',
        'status',
        'view_permission',
        'role',
        'groups',
    ];

    /**
     * Image URL accessor.
     */
    public function image_url()
    {
        if (! $this->image) {
            return null;
        }

        return Storage::url($this->image);
    }

    public function getImageUrlAttribute()
    {
        return $this->image_url();
    }

    public function toArray()
    {
        $array = parent::toArray();

        $array['image_url'] = $this->image_url;
        $array['groups'] = $this->groups;

        return $array;
    }

    /**
     * All tenant associations of this user.
     */
    public function tenantPivots()
    {
        return $this->hasMany(UserTenant::class);
    }

    /**
     * Current tenant pivot (based on the active tenant).
     */
    public function currentTenantPivot()
    {
        return $this->hasOne(UserTenant::class)
                    ->where('tenant_id', tenant('id'))
                    ->withDefault([
                        'role_id' => null,
                        'status' => 0,
                        'view_permission' => 'global',
                    ]);
    }

    /**
     * Get the currentTenantPivot model instance safely.
     */
    protected function getCurrentTenantPivotObject()
    {
        return $this->relationLoaded('currentTenantPivot')
            ? $this->getRelation('currentTenantPivot')
            : $this->currentTenantPivot()->getResults();
    }

    /**
     * Dynamically get the current tenant's role ID.
     */
    public function getRoleIdAttribute()
    {
        return $this->getCurrentTenantPivotObject()->role_id;
    }

    /**
     * Dynamically get the current tenant's status.
     */
    public function getStatusAttribute()
    {
        return $this->getCurrentTenantPivotObject()->status;
    }

    /**
     * Dynamically get the current tenant's view_permission.
     */
    public function getViewPermissionAttribute()
    {
        return $this->getCurrentTenantPivotObject()->view_permission;
    }

    /**
     * Proxy role relationship from the pivot.
     */
    public function role()
    {
        return $this->getCurrentTenantPivotObject()->role();
    }

    public function getRoleAttribute()
    {
        return $this->getCurrentTenantPivotObject()->role;
    }

    /**
     * Groups the user belongs to.
     */
    public function groups()
    {
        return $this->getCurrentTenantPivotObject()->groups();
    }

    public function getGroupsAttribute()
    {
        return $this->getCurrentTenantPivotObject()->groups;
    }

    /**
     * Check permission for a given action.
     */
    public function hasPermission($permission)
    {
        $role = $this->getCurrentTenantPivotObject()->role;

        if (! $role) {
            return false;
        }

        if ($role->permission_type === 'custom' && empty($role->permissions)) {
            return false;
        }

        return $role->permission_type === 'all' || in_array($permission, $role->permissions);
    }
}
