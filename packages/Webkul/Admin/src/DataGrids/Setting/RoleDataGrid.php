<?php

namespace Webkul\Admin\DataGrids\Setting;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;
use Illuminate\Database\Query\Builder;

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

        $this->addFilter('id', 'roles.id');
        $this->addFilter('name', 'roles.name');

        return $queryBuilder;
    }

    /**
     * Add columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'    => 'id',
            'label'    => trans('admin::app.settings.roles.index.datagrid.id'),
            'type'     => 'string',
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'    => 'name',
            'label'    => trans('admin::app.settings.roles.index.datagrid.name'),
            'type'     => 'string',
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'    => 'description',
            'label'    => trans('admin::app.settings.roles.index.datagrid.description'),
            'type'     => 'string',
            'sortable' => false,
        ]);

        $this->addColumn([
            'index'              => 'permission_type',
            'label'              => trans('admin::app.settings.roles.index.datagrid.permission-type'),
            'type'               => 'string',
            'searchable'         => true,
            'filterable'         => true,
            'filterable_type'    => 'dropdown',
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
            'sortable'   => true,
        ]);
    }
   
    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        $this->addAction([
            'icon'   => 'icon-edit',
            'title'  => trans('admin::app.settings.roles.index.datagrid.edit'),
            'method' => 'GET',
            'url'    => function ($row) {
                return route('admin.settings.roles.edit', $row->id);
            },
        ]);

        $this->addAction([
            'icon'   => 'icon-delete',
            'title'  => trans('admin::app.settings.roles.index.datagrid.delete'),
            'method' => 'DELETE',
            'url'    => function ($row) {
                return route('admin.settings.roles.delete', $row->id);
            },
        ]);
    }
}
