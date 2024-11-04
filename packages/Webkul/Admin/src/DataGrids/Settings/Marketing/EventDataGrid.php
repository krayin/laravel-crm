<?php

namespace Webkul\Admin\DataGrids\Settings\Marketing;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class EventDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('events')
            ->addSelect(
                'events.id',
                'events.name',
                'events.description',
                'events.date',
            );

        $this->addFilter('id', 'events.id');

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
            'label'      => trans('admin::app.settings.marketing.events.index.datagrid.id'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => trans('admin::app.settings.marketing.events.index.datagrid.name'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'    => 'description',
            'label'    => trans('admin::app.settings.marketing.events.index.datagrid.description'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'date',
            'label'    => trans('admin::app.settings.marketing.events.index.datagrid.date'),
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
        // TODO: Implement the bouncer for this actions.
        $this->addAction([
            'index'  => 'edit',
            'icon'   => 'icon-edit',
            'title'  => trans('admin::app.settings.marketing.events.index.datagrid.edit'),
            'method' => 'GET',
            'url'    => fn ($row) => route('admin.settings.marketing_events.edit', $row->id),
        ]);

        $this->addAction([
            'index'          => 'delete',
            'icon'           => 'icon-delete',
            'title'          => trans('admin::app.settings.marketing.events.index.datagrid.delete'),
            'method'         => 'DELETE',
            'url'            => fn ($row) => route('admin.settings.marketing_events.delete', $row->id),
        ]);
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        $this->addMassAction([
            'icon'   => 'icon-delete',
            'title'  => trans('admin::app.settings.marketing.events.index.datagrid.delete'),
            'method' => 'POST',
            'url'    => route('admin.settings.marketing_events.mass_delete'),
        ]);
    }
}
