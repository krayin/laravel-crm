<?php

namespace Webkul\WebForm\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\UI\DataGrid\DataGrid;

class WebFormDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('web_forms')
            ->addSelect(
                'web_forms.id',
                'web_forms.title',
            );

        $this->addFilter('id', 'web_forms.id');

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
            'index'    => 'title',
            'label'    => trans('admin::app.datagrid.title'),
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
            'title'  => trans('ui::app.datagrid.view'),
            'method' => 'GET',
            'route'  => 'admin.settings.web_forms.view',
            'icon'   => 'icon eye-icon',
        ]);

        $this->addAction([
            'title'  => trans('ui::app.datagrid.edit'),
            'method' => 'GET',
            'route'  => 'admin.settings.web_forms.edit',
            'icon'   => 'pencil-icon',
        ]);

        $this->addAction([
            'title'        => trans('ui::app.datagrid.delete'),
            'method'       => 'DELETE',
            'route'        => 'admin.settings.web_forms.delete',
            'confirm_text' => trans('ui::app.datagrid.massaction.delete', ['resource' => 'type']),
            'icon'         => 'trash-icon',
        ]);
    }
}
