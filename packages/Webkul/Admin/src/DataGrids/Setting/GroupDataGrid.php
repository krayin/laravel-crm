<?php

namespace Webkul\Admin\DataGrids\Setting;

use Illuminate\Support\Facades\DB;
use Webkul\UI\DataGrid\DataGrid;

class GroupDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('groups')
            ->addSelect(
                'groups.id',
                'groups.name',
                'groups.description'
            );

        $this->addFilter('id', 'groups.id');

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
            'index'    => 'description',
            'label'    => trans('admin::app.datagrid.description'),
            'type'     => 'string',
            'sortable' => false,
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
}
