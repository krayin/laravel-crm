<?php

namespace Webkul\Admin\DataGrids\Mail;

use Webkul\UI\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;

class EmailDataGrid extends DataGrid
{
    public function prepareQueryBuilder()
    {
        // dd(request('route'));
        
        $queryBuilder = DB::table('emails')
            ->addSelect('*')
            ->where('folders', 'like', '%"' . request('route') . '"%');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'             => 'name',
            'label'             => trans('admin::app.datagrid.name'),
            'type'              => 'string',
            'searchable'        => true,
            'sortable'          => true,
            'filterable_type'   => 'add'
        ]);
    }

    public function prepareActions()
    {
        $this->addAction([
            'title'  => trans('ui::app.datagrid.edit'),
            'method' => 'GET',
            'route'  => 'admin.products.edit',
            'icon'   => 'icon pencil-icon',
        ]);

        $this->addAction([
            'title'        => trans('ui::app.datagrid.delete'),
            'method'       => 'DELETE',
            'route'        => 'admin.products.delete',
            'confirm_text' => trans('ui::app.datagrid.massaction.delete', ['resource' => 'user']),
            'icon'         => 'icon trash-icon',
        ]);
    }

    public function prepareMassActions()
    {
    }
}
