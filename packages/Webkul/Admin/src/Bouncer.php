<?php

namespace Webkul\Admin;

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
}