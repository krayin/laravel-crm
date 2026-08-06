<?php

namespace Webkul\Admin\DataGrids\Settings;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Webkul\DataGrid\DataGrid;
use Webkul\User\Repositories\RoleRepository;

class UserDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('users')
            ->distinct()
            ->addSelect(
                'id',
                'name',
                'email',
                'image',
                'status',
                'created_at'
            )
            ->leftJoin('user_groups', 'id', '=', 'user_groups.user_id');

        $authUser = auth()->guard('user')->user();

        /**
         * A full administrator, and any user with the global data scope, sees every user. Everyone
         * else sees the users they can actually manage — themselves and any user whose role holds the
         * same or fewer permissions than their own — so the listing matches what the controller allows
         * them to edit or delete, and a user above them (an administrator or a broader role) is hidden.
         */
        if (
            $authUser?->role?->permission_type !== 'all'
            && bouncer()->getAuthorizedUserIds() !== null
        ) {
            $ownPermissions = $authUser?->role?->permissions ?? [];

            $manageableRoleIds = app(RoleRepository::class)->all()
                ->filter(fn ($role) => $role->permission_type !== 'all'
                    && empty(array_diff($role->permissions ?? [], $ownPermissions)))
                ->pluck('id')
                ->all();

            $queryBuilder->where(function ($query) use ($manageableRoleIds, $authUser) {
                $query->whereIn('users.role_id', $manageableRoleIds)
                    ->orWhere('users.id', $authUser?->id);
            });
        }

        return $queryBuilder;
    }

    /**
     * Add columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index' => 'id',
            'label' => trans('admin::app.settings.users.index.datagrid.id'),
            'type' => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'name',
            'label' => trans('admin::app.settings.users.index.datagrid.name'),
            'type' => 'string',
            'sortable' => true,
            'searchable' => true,
            'filterable' => true,
            'closure' => function ($row) {
                return [
                    'image' => $row->image ? Storage::url($row->image) : null,
                    'name' => $row->name,
                ];
            },
        ]);

        $this->addColumn([
            'index' => 'email',
            'label' => trans('admin::app.settings.users.index.datagrid.email'),
            'type' => 'string',
            'sortable' => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index' => 'status',
            'label' => trans('admin::app.settings.users.index.datagrid.status'),
            'type' => 'boolean',
            'filterable' => true,
            'sortable' => true,
            'searchable' => true,
        ]);

        $this->addColumn([
            'index' => 'created_at',
            'label' => trans('admin::app.settings.users.index.datagrid.created-at'),
            'type' => 'date',
            'sortable' => true,
            'searchable' => true,
            'filterable_type' => 'date_range',
            'filterable' => true,
        ]);
    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        if (bouncer()->hasPermission('settings.user.users.edit')) {
            $this->addAction([
                'index' => 'edit',
                'icon' => 'icon-edit',
                'title' => trans('admin::app.settings.users.index.datagrid.edit'),
                'method' => 'GET',
                'url' => fn ($row) => route('admin.settings.users.edit', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('settings.user.users.delete')) {
            $this->addAction([
                'index' => 'delete',
                'icon' => 'icon-delete',
                'title' => trans('admin::app.settings.users.index.datagrid.delete'),
                'method' => 'DELETE',
                'url' => fn ($row) => route('admin.settings.users.delete', $row->id),
            ]);
        }
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        $this->addMassAction([
            'icon' => 'icon-delete',
            'title' => trans('admin::app.settings.users.index.datagrid.delete'),
            'method' => 'POST',
            'url' => route('admin.settings.users.mass_delete'),
        ]);

        $this->addMassAction([
            'title' => trans('admin::app.settings.users.index.datagrid.update-status'),
            'method' => 'POST',
            'url' => route('admin.settings.users.mass_update'),
            'options' => [
                [
                    'label' => trans('admin::app.settings.users.index.datagrid.active'),
                    'value' => 1,
                ],
                [
                    'label' => trans('admin::app.settings.users.index.datagrid.inactive'),
                    'value' => 0,
                ],
            ],
        ]);
    }
}
