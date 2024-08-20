<?php

namespace Webkul\Admin\DataGrids\Settings;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class EmailTemplateDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
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

        return $queryBuilder;
    }

    /**
     * Add columns.
     *
     * @return void
     */
    public function prepareColumns()
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('admin::app.settings.email-template.index.datagrid.id'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => trans('admin::app.settings.email-template.index.datagrid.name'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'    => 'subject',
            'label'    => trans('admin::app.settings.email-template.index.datagrid.subject'),
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
        if (bouncer()->hasPermission('settings.automation.email_templates.edit')) {
            $this->addAction([
                'index'  => 'delete',
                'icon'   => 'icon-edit',
                'title'  => trans('admin::app.settings.email-template.index.datagrid.edit'),
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.settings.email_templates.edit', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('settings.automation.email_templates.delete')) {
            $this->addAction([
                'index'          => 'delete',
                'icon'           => 'icon-delete',
                'title'          => trans('admin::app.settings.email-template.index.datagrid.delete'),
                'method'         => 'DELETE',
                'url'            => fn ($row) => route('admin.settings.email_templates.delete', $row->id),
            ]);
        }
    }
}
