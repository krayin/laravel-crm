<?php

namespace Webkul\Admin\DataGrids\Settings;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class SourceDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('lead_sources')
            ->addSelect(
                'lead_sources.id',
                'lead_sources.name'
            );

        $this->addFilter('id', 'lead_sources.id');

        return $queryBuilder;
    }

    /**
     * Prepare Columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'    => 'id',
            'label'    => trans('admin::app.settings.sources.index.datagrid.id'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => trans('admin::app.settings.sources.index.datagrid.name'),
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
        if (bouncer()->hasPermission('settings.lead.sources.edit')) {
            $this->addAction([
                'index'  => 'edit',
                'icon'   => 'icon-edit',
                'title'  => trans('admin::app.settings.sources.index.datagrid.edit'),
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.settings.sources.edit', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('settings.lead.sources.delete')) {
            $this->addAction([
                'index'  => 'delete',
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.settings.sources.index.datagrid.delete'),
                'method' => 'DELETE',
                'url'    => fn ($row) => route('admin.settings.sources.delete', $row->id),
            ]);
        }
    }
}
