<?php

namespace Webkul\Admin\DataGrids\Setting;

use Webkul\UI\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;

class GroupDataGrid extends DataGrid
{
    protected $redirectRow = [
        "id"    => "id",
        "route" => "admin.settings.groups.edit",
    ];

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('groups')
            ->addSelect(
                'groups.id',
                'groups.name',
                'groups.description',
            );

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'           => 'id',
            'head_style'      => 'width: 50px',
            'label'           => trans('admin::app.datagrid.id'),
            'type'            => 'string',
            'searchable'      => true,
            'sortable'        => true,
            'filterable_type' => 'add'
        ]);

        $this->addColumn([
            'index'           => 'name',
            'label'           => trans('admin::app.datagrid.name'),
            'type'            => 'string',
            'searchable'      => true,
            'sortable'        => true,
            'filterable_type' => 'add'
        ]);

        $this->addColumn([
            'index'      => 'description',
            'label'      => trans('admin::app.datagrid.description'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => false,
        ]);
    }

    public function prepareActions()
    {
        $this->addAction([
            'title'  => trans('ui::app.datagrid.edit'),
            'method' => 'GET',
            'route'  => 'admin.settings.groups.edit',
            'icon'   => 'pencil-icon',
        ]);

        $this->addAction([
            'title'        => trans('ui::app.datagrid.delete'),
            'method'       => 'DELETE',
            'route'        => 'admin.settings.groups.delete',
            'confirm_text' => trans('ui::app.datagrid.massaction.delete', ['resource' => 'group']),
            'icon'         => 'trash-icon',
        ]);
    }

    public function prepareMassActions()
    {
    }
}
