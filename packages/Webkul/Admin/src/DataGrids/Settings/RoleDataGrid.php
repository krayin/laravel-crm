<?php

namespace Webkul\Admin\DataGrids\Settings;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;
use Webkul\User\Repositories\RoleRepository;

class RoleDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('roles')
            ->addSelect(
                'roles.id',
                'roles.name',
                'roles.description',
                'roles.permission_type'
            );

        $authUser = auth()->guard('user')->user();

        /**
         * A full administrator, and any user with the global data scope, sees every role. A group or
         * individual scope instead sees only the roles they can manage and assign — their own role and
         * any role whose privileges are a subset of their own, never the administrator role or a role
         * holding permissions they do not personally have. This mirrors the "cannot manage or grant a
         * role above your own" model enforced in the controller.
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

            $queryBuilder->whereIn('roles.id', $manageableRoleIds);
        }

        $this->addFilter('id', 'roles.id');
        $this->addFilter('name', 'roles.name');

        return $queryBuilder;
    }

    /**
     * Prepare Columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index' => 'id',
            'label' => trans('admin::app.settings.roles.index.datagrid.id'),
            'type' => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'name',
            'label' => trans('admin::app.settings.roles.index.datagrid.name'),
            'type' => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'description',
            'label' => trans('admin::app.settings.roles.index.datagrid.description'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => false,
        ]);

        $this->addColumn([
            'index' => 'permission_type',
            'label' => trans('admin::app.settings.roles.index.datagrid.permission-type'),
            'type' => 'string',
            'searchable' => true,
            'filterable' => true,
            'filterable_type' => 'dropdown',
            'filterable_options' => [
                [
                    'label' => trans('admin::app.settings.roles.index.datagrid.custom'),
                    'value' => 'custom',
                ],
                [
                    'label' => trans('admin::app.settings.roles.index.datagrid.all'),
                    'value' => 'all',
                ],
            ],
            'sortable' => true,
        ]);
    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        if (bouncer()->hasPermission('settings.user.roles.edit')) {
            $this->addAction([
                'icon' => 'icon-edit',
                'title' => trans('admin::app.settings.roles.index.datagrid.edit'),
                'method' => 'GET',
                'url' => fn ($row) => route('admin.settings.roles.edit', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('settings.user.roles.delete')) {
            $this->addAction([
                'icon' => 'icon-delete',
                'title' => trans('admin::app.settings.roles.index.datagrid.delete'),
                'method' => 'DELETE',
                'url' => fn ($row) => route('admin.settings.roles.delete', $row->id),
            ]);
        }
    }
}
