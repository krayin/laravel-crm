<?php

namespace Webkul\AssetManagement\DataGrids;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class WarehouseDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('asset_warehouses')->addSelect('id');

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
        // asset name, asset id ,date ,category,quantity,location
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
            'index'      => 'date',
            'label'      => 'Date',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'category',
            'label'      => 'Category',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'quantity',
            'label'      => 'Quantity',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'location',
            'label'      => 'Location',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);


    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        if (bouncer()->hasPermission('warehouse.view')) {
            $this->addAction([
                'icon'   => 'icon-eye',
                'title'  => 'View',
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.warehouse.view', $row->id),
            ]);
        }
        if (bouncer()->hasPermission('warehouse.edit')) {
            $this->addAction([
                'icon'   => 'icon-pencil',
                'title'  => 'Edit',
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.warehouse.edit', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('warehouse.delete')) {
            $this->addAction([
                'icon'   => 'icon-delete',
                'title'  => 'Delete',
                'method' => 'delete',
                'url'    => fn ($row) => route('admin.warehouse.delete', $row->id),
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
            'url'    => route('admin.warehouse.mass_delete'),
        ]);

    }
}
