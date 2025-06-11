<?php

namespace Webkul\User\Repositories;

use Webkul\Core\Eloquent\Repository;

class UserRepository extends Repository
{
    /**
     * Searchable fields
     */
    protected $fieldSearchable = [
        'name',
        'email',
        'status',
        'view_permission',
    ];

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Webkul\User\Contracts\User';
    }

    /**
     * This function will return user ids of current user's groups
     *
     * @return array
     */
    public function getCurrentUserGroupsUserIds()
    {
        $currentTenantId = tenant('id');

        $user = auth()->guard('user')->user();

        
        $currentUserTenant = $user->tenantPivots()->where('tenant_id', $currentTenantId)->first();
    
        if (! $currentUserTenant) {
            return [];
        }

        $groupIds = $currentUserTenant->groups()->pluck('groups.id');
        
        $userTenantIds = \Webkul\User\Models\UserTenant::whereHas('groups', function ($query) use ($groupIds) {
            $query->whereIn('groups.id', $groupIds);
        })->pluck('user_id');

        return $userTenantIds->unique()->toArray(); 
    }
}
