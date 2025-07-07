<?php

namespace Webkul\Admin;

use Webkul\User\Repositories\UserRepository;
use Webkul\User\Models\UserTenant;
use Webkul\User\Models\Role;

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
            return app(UserRepository::class)->getCurrentUserGroupsUserIds();
        } else {
            return [$user->id];
        }
    }

    public function getUserTenants()
    {
        $user = auth()->guard('user')->user();

        if (! $user) {
            return collect(); // retorna uma coleção vazia se não estiver logado
        }

        return UserTenant::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->with('tenant.domains')
            ->get()
            ->map(function ($userTenant) {
                $tenant = $userTenant->tenant;
                $domain = optional($tenant->domains->first())->domain;

                return [
                    'id' => $tenant->id,
                    'domain' => $domain,
                    'name' => json_decode($tenant->data)->name ?? null,
                ];
            });
    }

    public function fetchRoles()
    {
        return Role::pluck('name', 'id')->toArray();
    }

}
