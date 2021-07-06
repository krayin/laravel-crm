<?php

namespace Webkul\Admin\DataGrids\Setting;

use Webkul\User\Models\User;
use Webkul\UI\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;

class UserDataGrid extends DataGrid
{
    protected $redirectRow = [
        "id"    => "id",
        "route" => "admin.settings.users.edit",
    ];

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

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'             => 'id',
            'head_style'        => 'width: 50px',
            'label'             => trans('admin::app.datagrid.id'),
            'type'              => 'string',
            'searchable'        => true,
            'sortable'          => true,
            'filterable_type'   => 'add'
        ]);

        $this->addColumn([
            'index'             => 'name',
            'label'             => trans('admin::app.datagrid.name'),
            'type'              => 'string',
            'searchable'        => true,
            'sortable'          => true,
            'filterable_type'   => 'add'
        ]);

        $this->addColumn([
            'index'             => 'email',
            'label'             => trans('admin::app.datagrid.email'),
            'type'              => 'string',
            'searchable'        => true,
            'sortable'          => true,
            'filterable_type'   => 'add'
        ]);

        $this->addColumn([
            'index'              => 'status',
            'label'              => trans('admin::app.datagrid.status'),
            'type'               => 'boolean',
            'filterable_type'    => 'dropdown',
            'filterable_options' => [
                [
                    'label' => trans('admin::app.datagrid.active'),
                    'value' => 1,
                ], [
                    'label' => trans('admin::app.datagrid.inactive'),
                    'value' => 0,
                ],
            ],
            'closure'            => function ($row) {
                if ($row->status == 1) {
                    return '<span class="badge badge-round badge-primary"></span>' . trans('admin::app.datagrid.active');
                } else {
                    return '<span class="badge badge-round badge-danger"></span>' . trans('admin::app.datagrid.inactive');
                }
            },
        ]);

        $this->addColumn([
            'index'             => 'created_at',
            'label'             => trans('admin::app.datagrid.created_at'),
            'type'              => 'string',
            'sortable'          => true,
            'filterable_type'   => 'date_range',
            'closure'           => function ($row) {
                return core()->formatDate($row->created_at);
            },
        ]);
    }

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

    public function prepareMassActions()
    {
        $this->addMassAction([
            'type'   => 'delete',
            'label'  => trans('ui::app.datagrid.delete'),
            'action' => route('admin.settings.users.mass-delete'),
            'method' => 'PUT',
        ]);

        $this->addMassAction([
            'type'    => 'update',
            'label'   => trans('ui::app.datagrid.update-status'),
            'action'  => route('admin.settings.users.mass-update'),
            'method'  => 'PUT',
            'options' => [
                trans('admin::app.datagrid.active')   => 1,
                trans('admin::app.datagrid.inactive') => 0,
            ],
        ]);
    }
}
