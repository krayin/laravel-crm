<?php

namespace Webkul\Admin\DataGrids\Mail;

use Webkul\UI\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;

class EmailDataGrid extends DataGrid
{
    public function prepareQueryBuilder()
    {
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
            'title'  => request('route') == 'draft'
                        ? trans('ui::app.datagrid.edit')
                        : trans('ui::app.datagrid.view'),
            'method' => 'GET',
            'route'  => 'admin.mail.view',
            'params' => ['route' => request('route')],
            'icon'   => request('route') == 'draft'
                        ? 'icon pencil-icon'
                        : 'icon eye-icon'
        ]);

        $this->addAction([
            'title'        => trans('ui::app.datagrid.delete'),
            'method'       => 'DELETE',
            'route'        => 'admin.mail.delete',
            'confirm_text' => trans('ui::app.datagrid.massaction.delete', ['resource' => 'user']),
            'icon'         => 'icon trash-icon',
        ]);
    }

    public function prepareMassActions()
    {
    }
}
