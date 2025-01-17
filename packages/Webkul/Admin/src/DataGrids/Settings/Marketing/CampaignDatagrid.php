<?php

namespace Webkul\Admin\DataGrids\Settings\Marketing;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class CampaignDatagrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('marketing_campaigns')
            ->addSelect(
                'marketing_campaigns.id',
                'marketing_campaigns.name',
                'marketing_campaigns.subject',
                'marketing_campaigns.status',
            );

        $this->addFilter('id', 'marketing_campaigns.id');

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
            'label'      => trans('admin::app.settings.marketing.campaigns.index.datagrid.id'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => trans('admin::app.settings.marketing.campaigns.index.datagrid.name'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'    => 'subject',
            'label'    => trans('admin::app.settings.marketing.campaigns.index.datagrid.subject'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'status',
            'label'    => trans('admin::app.settings.marketing.campaigns.index.datagrid.status'),
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
        if (bouncer()->hasPermission('settings.automation.campaigns.edit')) {
            $this->addAction([
                'index'  => 'edit',
                'icon'   => 'icon-edit',
                'title'  => trans('admin::app.settings.marketing.campaigns.index.datagrid.edit'),
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.settings.marketing.campaigns.edit', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('settings.automation.campaigns.delete')) {
            $this->addAction([
                'index'          => 'delete',
                'icon'           => 'icon-delete',
                'title'          => trans('admin::app.settings.marketing.campaigns.index.datagrid.delete'),
                'method'         => 'DELETE',
                'url'            => fn ($row) => route('admin.settings.marketing.campaigns.delete', $row->id),
            ]);
        }
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        if (bouncer()->hasPermission('settings.automation.campaigns.mass_delete')) {
            $this->addMassAction([
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.settings.marketing.campaigns.index.datagrid.delete'),
                'method' => 'POST',
                'url'    => route('admin.settings.marketing.campaigns.mass_delete'),
            ]);
        }
    }
}
