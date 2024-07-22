<?php

namespace Webkul\Admin\DataGrids\Setting;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class WorkflowDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('workflows')
            ->addSelect(
                'workflows.id',
                'workflows.name'
            );

        $this->addFilter('id', 'workflows.id');

        return $queryBuilder;
    }

    /**
     * Prepare Columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('admin::app.datagrid.id'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => trans('admin::app.datagrid.name'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);
    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        $this->addAction([
            'index'  => 'edit',
            'icon'   => 'icon-edit',
            'title'  => trans('ui::app.datagrid.edit'),
            'method' => 'GET',
            'url'    => fn ($row) => route('admin.settings.workflows.edit', $row->id),
        ]);

        $this->addAction([
            'index'        => 'delete',
            'icon'         => 'icon-delete',
            'title'        => trans('ui::app.datagrid.delete'),
            'method'       => 'DELETE',
            'url'          => fn ($row) => route('admin.settings.workflows.delete', $row->id),
        ]);
    }
}
