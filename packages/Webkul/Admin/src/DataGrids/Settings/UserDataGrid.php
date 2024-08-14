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
     *
     * @return void
     */
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('users')
            ->distinct()
            ->addSelect(
                'users.id',
                'users.name',
                'users.email',
                'users.image',
                'users.status',
                'users.created_at'
            )
            ->leftJoin('user_groups', 'users.id', '=', 'user_groups.user_id');
        
        if ($userIds = bouncer()->getAuthorizedUserIds()) {
            $queryBuilder->whereIn('users.user_id', $userIds);
        }

        $this->addFilter('id', 'users.id');

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
            'closure'    => function ($row) {
                return [
                    'image' => $row->image ? Storage::url($row->image) : null,
                    'name'  => $row->name,
                ];
            },
        ]);

        $this->addColumn([
            'index'    => 'email',
            'label'    => trans('admin::app.settings.users.index.datagrid.email'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'      => 'status',
            'label'      => trans('admin::app.settings.users.index.datagrid.status'),
            'type'       => 'boolean',
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'           => 'created_at',
            'label'           => trans('admin::app.settings.users.index.datagrid.created-at'),
            'type'            => 'date',
            'searchable'      => true,
            'filterable'      => true,
            'filterable_type' => 'date_range',
            'sortable'        => true,
            'closure'         => function ($row) {
                return core()->formatDate($row->created_at);
            },
        ]);
    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        $this->addAction([
            'index'  => 'edit',
            'icon'   => 'icon-edit',
            'title'  => trans('admin::app.settings.users.index.datagrid.edit'),
            'method' => 'GET',
            'url'    => function ($row) {
                return route('admin.settings.users.edit', $row->id);
            },
        ]);

        $this->addAction([
            'index'  => 'delete',
            'icon'   => 'icon-delete',
            'title'  => trans('admin::app.settings.users.index.datagrid.delete'),
            'method' => 'DELETE',
            'url'    => function ($row) {
                return route('admin.settings.users.delete', $row->id);
            },
        ]);
    }

    /**
     * Prepare mass actions.
     *
     * @return void
     */
    public function prepareMassActions()
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
