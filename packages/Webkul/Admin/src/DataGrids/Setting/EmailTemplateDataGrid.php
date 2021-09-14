<?php

namespace Webkul\Admin\DataGrids\Setting;

use Webkul\UI\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;

class EmailTemplateDataGrid extends DataGrid
{
    protected $redirectRow = [
        "id"    => "id",
        "route" => "admin.settings.email_templates.edit",
    ];

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('email_templates')
            ->addSelect(
                'email_templates.id',
                'email_templates.name',
                'email_templates.subject',
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
            'index'           => 'subject',
            'label'           => trans('admin::app.datagrid.subject'),
            'type'            => 'string',
            'searchable'      => true,
            'sortable'        => true,
            'filterable_type' => 'add'
        ]);
    }

    public function prepareActions()
    {
        $this->addAction([
            'title'  => trans('ui::app.datagrid.edit'),
            'method' => 'GET',
            'route'  => 'admin.settings.email_templates.edit',
            'icon'   => 'pencil-icon',
        ]);

        $this->addAction([
            'title'        => trans('ui::app.datagrid.delete'),
            'method'       => 'DELETE',
            'route'        => 'admin.settings.email_templates.delete',
            'confirm_text' => trans('ui::app.datagrid.massaction.delete', ['resource' => 'type']),
            'icon'         => 'trash-icon',
        ]);
    }
}
