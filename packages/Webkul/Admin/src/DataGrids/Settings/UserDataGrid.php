<?php

namespace Webkul\Admin\DataGrids\Settings;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Webkul\DataGrid\DataGrid;

class UserDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $tenantId = tenant('id');

        $queryBuilder = DB::table('users')
            ->distinct()
            ->addSelect(
                'users.id',
                'users.name',
                'users.email',
                'users.image',
                'user_tenants.status',
                'users.created_at'
            )
            ->join('user_tenants', function ($join) use ($tenantId) {
                $join->on('users.id', '=', 'user_tenants.user_id')
                    ->where('user_tenants.tenant_id', '=', $tenantId);
            })
            ->leftJoin('user_groups', 'user_tenants.id', '=', 'user_groups.user_tenant_id');

        if ($userIds = bouncer()->getAuthorizedUserIds()) {
            $queryBuilder->whereIn('users.id', $userIds);
        }

        return $queryBuilder;
    }

    /**
     * Add columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'    => 'id',
            'label'    => trans('admin::app.settings.users.index.datagrid.id'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => trans('admin::app.settings.users.index.datagrid.name'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
            'closure'    => function ($row) {
                return [
                    'image' => $row->image ? Storage::url($row->image) : null,
                    'name'  => $row->name,
                ];
            },
        ]);

        $this->addColumn([
            'index'      => 'users.email',
            'label'      => trans('admin::app.settings.users.index.datagrid.email'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'user_tenants.status',
            'label'      => trans('admin::app.settings.users.index.datagrid.status'),
            'type'       => 'boolean',
            'filterable' => true,
            'sortable'   => true,
            'searchable' => true,
        ]);

        $this->addColumn([
            'index'           => 'users.created_at',
            'label'           => trans('admin::app.settings.users.index.datagrid.created-at'),
            'type'            => 'date',
            'sortable'        => true,
            'searchable'      => true,
            'filterable_type' => 'date_range',
            'filterable'      => true,
        ]);
    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        if (bouncer()->hasPermission('settings.user.users.edit')) {
            $this->addAction([
                'index'  => 'edit',
                'icon'   => 'icon-edit',
                'title'  => trans('admin::app.settings.users.index.datagrid.edit'),
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.settings.users.edit', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('settings.user.users.delete')) {
            $this->addAction([
                'index'  => 'delete',
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.settings.users.index.datagrid.delete'),
                'method' => 'DELETE',
                'url'    => fn ($row) => route('admin.settings.users.delete', $row->id),
            ]);
        }
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        $this->addMassAction([
            'icon'   => 'icon-delete',
            'title'  => trans('admin::app.settings.users.index.datagrid.delete'),
            'method' => 'POST',
            'url'    => route('admin.settings.users.mass_delete'),
        ]);

        $this->addMassAction([
            'title'   => trans('admin::app.settings.users.index.datagrid.update-status'),
            'method'  => 'POST',
            'url'     => route('admin.settings.users.mass_update'),
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
