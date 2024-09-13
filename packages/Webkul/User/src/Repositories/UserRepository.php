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
        'role_id',
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
    public function getGroupsUserIds(array $groupIds)
    {
        return $this->scopeQuery(fn ($query) => $query->select('users.*')
            ->leftJoin('user_groups', 'users.id', '=', 'user_groups.user_id')
            ->leftJoin('groups', 'user_groups.group_id', 'groups.id')
            ->whereIn('groups.id', $groupIds)
        )
            ->get()
            ->pluck('id')
            ->toArray();
    }
}
