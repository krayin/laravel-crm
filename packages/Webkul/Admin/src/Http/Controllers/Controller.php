<?php

namespace Webkul\Admin\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function redirectToLogin()
    {
        return redirect()->route('admin.session.create');
    }

    /**
     * Prevent access to a record that is not owned by (or shared with) the current user.
     *
     * @param  int|array|null  $ownerIds  The user id (or ids, e.g. owner + participants) tied to the record.
     * @return void
     */
    protected function preventUnauthorizedAccess($ownerIds)
    {
        $userIds = bouncer()->getAuthorizedUserIds();

        if ($userIds === null) {
            return;
        }

        if (empty(array_intersect((array) $ownerIds, $userIds))) {
            abort(401, trans('admin::app.errors.unauthorized'));
        }
    }

    /**
     * Filter a collection of records down to the ones the current user is authorized to manage.
     *
     * @param  Collection  $records
     * @param  callable|null  $ownerIdsResolver  Resolves the owning user id(s) for a record; defaults to `user_id`.
     * @return Collection
     */
    protected function filterAuthorizedRecords($records, ?callable $ownerIdsResolver = null)
    {
        $userIds = bouncer()->getAuthorizedUserIds();

        if ($userIds === null) {
            return $records;
        }

        return $records->filter(function ($record) use ($userIds, $ownerIdsResolver) {
            $ownerIds = $ownerIdsResolver
                ? (array) $ownerIdsResolver($record)
                : [$record->user_id];

            return ! empty(array_intersect($ownerIds, $userIds));
        })->values();
    }
}
