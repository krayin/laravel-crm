<?php

namespace Webkul\Admin\DataGrids\Setting;

use Illuminate\Support\Facades\DB;
use Webkul\UI\DataGrid\DataGrid;

class UserDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('users')
            ->addSelect(
                'users.id',
                'users.name',
                'users.email',
                'users.status',
                'users.created_at'
            );

        $this->addFilter('id', 'users.id');

        $this->setQueryBuilder($queryBuilder);
    }

    /**
     * Add columns.
     *
     * @return void
     */
    public function addColumns()
    {
        $this->addColumn([
            'index'           => 'id',
            'label'           => trans('admin::app.datagrid.id'),
            'type'            => 'string',
            'searchable'      => true,
            'sortable'        => true,
        ]);

        $this->addColumn([
            'index'           => 'name',
            'label'           => trans('admin::app.datagrid.name'),
            'type'            => 'string',
            'searchable'      => true,
            'sortable'        => true,
        ]);

        $this->addColumn([
            'index'           => 'email',
            'label'           => trans('admin::app.datagrid.email'),
            'type'            => 'string',
            'searchable'      => true,
            'sortable'        => true,
        ]);

        $this->addColumn([
            'index'              => 'status',
            'label'              => trans('admin::app.datagrid.status'),
            'type'               => 'dropdown',
            'dropdown_options' => [
                [
                    'label' => trans('admin::app.datagrid.active'),
                    'value' => 1,
                ], [
                    'label' => trans('admin::app.datagrid.inactive'),
                    'value' => 0,
                ],
            ],
            'closure'            => true,
            'wrapper'            => function ($row) {
                if ($row->status == 1) {
                    return '<span class="badge badge-round badge-primary"></span>' . trans('admin::app.datagrid.active');
                } else {
                    return '<span class="badge badge-round badge-danger"></span>' . trans('admin::app.datagrid.inactive');
                }
            },
        ]);

        $this->addColumn([
            'index'           => 'created_at',
            'label'           => trans('admin::app.datagrid.created_at'),
            'type'            => 'date_range',
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
            'title'  => trans('ui::app.datagrid.edit'),
            'method' => 'GET',
            'route'  => 'admin.settings.users.edit',
            'icon'   => 'pencil-icon',
        ]);

        $this->addAction([
            'title'        => trans('ui::app.datagrid.delete'),
            'method'       => 'DELETE',
            'route'        => 'admin.settings.users.delete',
            'confirm_text' => trans('ui::app.datagrid.massaction.delete', ['resource' => 'user']),
            'icon'         => 'trash-icon',
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
            'type'   => 'delete',
            'label'  => trans('ui::app.datagrid.delete'),
            'action' => route('admin.settings.users.mass_delete'),
            'method' => 'PUT',
        ]);

        $this->addMassAction([
            'type'    => 'update',
            'label'   => trans('ui::app.datagrid.update-status'),
            'action'  => route('admin.settings.users.mass_update'),
            'method'  => 'PUT',
            'options' => [
                trans('admin::app.datagrid.active')   => 1,
                trans('admin::app.datagrid.inactive') => 0,
            ],
        ]);
    }
}
