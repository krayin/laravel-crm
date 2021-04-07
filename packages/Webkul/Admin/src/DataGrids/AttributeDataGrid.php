<?php

namespace Webkul\Admin\DataGrids;

use Webkul\UI\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;

class AttributeDataGrid extends DataGrid
{
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('users')
            ->addSelect(
                'users.id',
                'users.name',
                'users.email',
                'users.status'
            );

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('admin::app.datagrid.id'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => trans('admin::app.datagrid.name'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'email',
            'label'      => trans('admin::app.datagrid.email'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'     => 'status',
            'label'     => trans('admin::app.datagrid.status'),
            'type'      => 'boolean',
            'closure'   => function ($row) {
                if ($row->status == 1) {
                    return '<span class="badge badge-sm badge-pill badge-primary">' . trans('admin::app.datagrid.active') . '</span>';
                } else {
                    return '<span class="badge badge-sm badge-pill badge-danger">' . trans('admin::app.datagrid.inactive') . '</span>';
                }
            },
        ]);
    }

    public function prepareActions()
    {
        $this->addAction([
            'title'  => trans('ui::app.datagrid.edit'),
            'method' => 'GET',
            'route'  => 'admin.settings.users.edit',
            'icon'   => 'icon pencil-icon',
        ]);

        $this->addAction([
            'title'        => trans('ui::app.datagrid.delete'),
            'method'       => 'DELETE',
            'route'        => 'admin.settings.users.delete',
            'confirm_text' => trans('ui::app.datagrid.massaction.delete', ['resource' => 'user']),
            'icon'         => 'icon trash-icon',
        ]);
    }

    public function prepareMassActions()
    {
    }
}
