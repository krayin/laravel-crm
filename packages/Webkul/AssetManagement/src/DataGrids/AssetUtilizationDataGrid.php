<?php

namespace Webkul\AssetManagement\DataGrids;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class AssetUtilizationDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('asset_utilizations')->addSelect('id');

        return $queryBuilder;
    }

    /**
     * Prepare columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => 'ID',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);
        // asset name,asset id ,total hour used, utilization rate(%),score
        $this->addColumn([
            'index'      => 'asset_name',
            'label'      => 'Asset Name',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'asset_id',
            'label'      => 'Asset ID',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'total_hour_used',
            'label'      => 'Total Hour Used',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'utilization_rate',
            'label'      => 'Utilization Rate(%)',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'score',
            'label'      => 'Score',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);


    }

    public function prepareActions(): void
    {
        if (bouncer()->hasPermission('assetUtilization.view')) {
            $this->addAction([
                'icon'   => 'icon-eye',
                'title'  => 'View',
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.assetUtilization.view', $row->id),
            ]);
        }
        if (bouncer()->hasPermission('assetUtilization.edit')) {
            $this->addAction([
                'icon'   => 'icon-pencil',
                'title'  => 'Edit',
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.assetUtilization.edit', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('assetUtilization.delete')) {
            $this->addAction([
                'icon'   => 'icon-delete',
                'title'  => 'Delete',
                'method' => 'delete',
                'url'    => fn ($row) => route('admin.assetUtilization.delete', $row->id),
            ]);
        }
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        $this->addMassAction([
            'icon'   => 'icon-delete',
            'title'  => 'Delete',
            'method' => 'POST',
            'url'    => route('admin.assetUtilization.mass_delete'),
        ]);

    }
}
