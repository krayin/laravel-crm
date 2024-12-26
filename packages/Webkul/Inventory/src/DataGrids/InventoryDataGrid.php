<?php

namespace Webkul\Inventory\DataGrids;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class InventoryDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('inventory')->where('deleted_at', null)->orderBy('id', 'asc');

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

        // product , sku, available stock, reserved, on hold, price
        $this->addColumn([
            'index'      => 'product_name',
            'label'      => 'Product',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'sku',
            'label'      => 'SKU',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);


        $this->addColumn([
            'index'      => 'stock',
            'label'      => 'Available Stock',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'reserved',
            'label'      => 'Reserved',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'on_hold',
            'label'      => 'On Hold',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);


        $this->addColumn([
            'index'      => 'amount',
            'label'      => 'Amount',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

    }

    public function prepareActions(): void
    {

        if (bouncer()->hasPermission('inventory.view')) {
            $this->addAction([
                'icon'   => 'icon-eye',
                'title'  => 'View',
                'method' => 'GET',
                'url'    => function ($row) {
                    return route('admin.inventory.view', $row->id);
                },
            ]);
        }


        if (bouncer()->hasPermission('inventory.edit')) {
            $this->addAction([
                'index'  => 'edit',
                'icon'   => 'icon-edit',
                'title'  => 'Edit',
                'method' => 'GET',
                'url'    => function ($row) {
                    return route('admin.inventory.edit', $row->id);
                },
            ]);
        }

        if (bouncer()->hasPermission('inventory.delete')) {
            $this->addAction([
                'index'  => 'delete',
                'icon'   => 'icon-delete',
                'title'  => 'Delete',
                'method' => 'DELETE',
                'url'    => function ($row) {
                    return route('admin.inventory.delete', $row->id);
                },
            ]);
        }
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        if (bouncer()->hasPermission('inventory.mass_delete')) {
            $this->addMassAction([
                'icon'   => 'icon-delete',
                'title'  => 'Delete',
                'method' => 'POST',
                'url'    => route('admin.inventory.mass_delete'),
            ]);
        }
    }
}
