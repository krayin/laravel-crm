<?php

namespace Webkul\Admin\Traits;

trait AuthorizesUserResource
{
    /**
     * Authorize user for the resource.
     */
    protected function authorizeUserResource($resource): void
    {
        $userIds = bouncer()->getAuthorizedUserIds();

        if ($userIds && ! in_array($resource->user_id, $userIds)) {
            abort(403);
        }
    }
}
