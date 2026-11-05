<?php

namespace Webkul\Admin;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Webkul\User\Contracts\User as UserContract;
use Webkul\User\Repositories\UserRepository;

class Bouncer
{
    /**
     * Checks if user allowed or not for certain action
     *
     * @param  string  $permission
     * @return void
     */
    public function hasPermission($permission)
    {
        if (auth()->guard('user')->check() && auth()->guard('user')->user()->role->permission_type == 'all') {
            return true;
        } else {
            if (! auth()->guard('user')->check() || ! auth()->guard('user')->user()->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if user allowed or not for certain action
     *
     * @param  string  $permission
     * @return void
     */
    public static function allow($permission)
    {
        if (! auth()->guard('user')->check() || ! auth()->guard('user')->user()->hasPermission($permission)) {
            abort(401, 'This action is unauthorized');
        }
    }

    /**
     * This function will return user ids of current user's groups
     *
     * @return array|null
     */
    public function getAuthorizedUserIds()
    {
        $user = auth()->guard('user')->user();

        if ($user->view_permission == 'global') {
            return null;
        }

        if ($user->view_permission == 'group') {
            return array_values(array_unique(array_merge(
                [$user->id],
                app(UserRepository::class)->getCurrentUserGroupsUserIds()
            )));
        } else {
            return [$user->id];
        }
    }

    /**
     * Limit a query to the records inside the current user's data scope.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeToAuthorizedUsers($query)
    {
        $userIds = $this->getAuthorizedUserIds();

        if ($userIds === null) {
            return $query;
        }

        $model = $query->getModel();

        if ($model instanceof UserContract) {
            return $query->whereIn($model->getQualifiedKeyName(), $userIds);
        }

        static $ownedTables = [];

        $ownedTables[$model->getTable()] ??= Schema::hasColumn($model->getTable(), 'user_id');

        if ($ownedTables[$model->getTable()]) {
            return $query->whereIn($model->qualifyColumn('user_id'), $userIds);
        }

        return $query;
    }
}
