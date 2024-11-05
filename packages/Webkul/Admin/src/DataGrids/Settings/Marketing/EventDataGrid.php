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
        $queryBuilder = DB::table('marketing_events')
            ->addSelect(
                'marketing_events.id',
                'marketing_events.name',
                'marketing_events.description',
                'marketing_events.date',
            );

        $this->addFilter('id', 'marketing_events.id');

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
        if (bouncer()->hasPermission('settings.automation.events.edit')) {
            $this->addAction([
                'index'  => 'edit',
                'icon'   => 'icon-edit',
                'title'  => trans('admin::app.settings.marketing.events.index.datagrid.edit'),
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.settings.marketing.events.edit', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('settings.automation.events.delete')) {
            $this->addAction([
                'index'          => 'delete',
                'icon'           => 'icon-delete',
                'title'          => trans('admin::app.settings.marketing.events.index.datagrid.delete'),
                'method'         => 'DELETE',
                'url'            => fn ($row) => route('admin.settings.marketing.events.delete', $row->id),
            ]);
        }
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        if (bouncer()->hasPermission('settings.automation.events.delete')) {
            $this->addMassAction([
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.settings.marketing.events.index.datagrid.delete'),
                'method' => 'POST',
                'url'    => route('admin.settings.marketing.events.mass_delete'),
            ]);
        }
    }
}
