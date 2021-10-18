<?php

namespace Webkul\Admin\DataGrids\Setting;

use Illuminate\Support\Facades\DB;
use Webkul\UI\DataGrid\DataGrid;

class EmailTemplateDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('email_templates')
            ->addSelect(
                'email_templates.id',
                'email_templates.name',
                'email_templates.subject',
            );

        $this->addFilter('id', 'email_templates.id');

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
            'index'    => 'id',
            'label'    => trans('admin::app.datagrid.id'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'name',
            'label'    => trans('admin::app.datagrid.name'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'subject',
            'label'    => trans('admin::app.datagrid.subject'),
            'type'     => 'string',
            'sortable' => true,
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
