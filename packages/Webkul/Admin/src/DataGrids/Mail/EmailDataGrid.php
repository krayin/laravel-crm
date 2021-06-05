<?php

namespace Webkul\Admin\DataGrids\Mail;

use Illuminate\Support\Facades\DB;
use Webkul\UI\DataGrid\DataGrid;

class EmailDataGrid extends DataGrid
{
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('emails')
            ->addSelect('*')
            ->where('folders', 'like', '%"' . request('route') . '"%')
            ->whereNull('parent_id');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'             => 'name',
            'label'             => trans('admin::app.datagrid.from'),
            'type'              => 'string',
            'searchable'        => true,
            'sortable'          => true,
            'filterable_type'   => 'add',
        ]);

        $this->addColumn([
            'index'             => 'subject',
            'label'             => trans('admin::app.datagrid.subject'),
            'type'              => 'string',
            'searchable'        => true,
            'sortable'          => true,
            'filterable_type'   => 'add',
            'class'             => 'subject',
            'closure'           => function ($row) {
                return '<div class="subject-wrapper"><span class="subject-content">' . $row->subject . '</span><span class="reply"> - ' . substr(strip_tags($row->reply), 0, 225) . '<span></div>';
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
