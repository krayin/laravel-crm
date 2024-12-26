<?php

namespace Webkul\Consignment\DataGrids;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class ConsignmentDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('consignments_stock')->addSelect('id');

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

    
        $this->addColumn([
            'index'      => 'product_name',
            'label'      => 'Product Name',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'quantity_on_consignment',
            'label'      => 'Quantity on Consignment',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'consumed_quantity',
            'label'      => 'Consumed Quantity',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'remaining_quantity',
            'label'      => 'Remaining Quantity',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'return_quantity',
            'label'      => 'Return Quantity',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'invoice_due_date',
            'label'      => 'Invoice Due Date',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'status',
            'label'      => 'Status',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);


    }

    public function prepareActions(): void
    {

        if (bouncer()->hasPermission('consignment.view')) {
            $this->addAction([
                'icon'   => 'icon-eye',
                'title'  => 'View',
                'method' => 'GET',
                'url'    => function ($row) {
                    return route('admin.consignment.view', $row->id);
                },
            ]);
        }


        if (bouncer()->hasPermission('consignment.edit')) {
            $this->addAction([
                'index'  => 'edit',
                'icon'   => 'icon-edit',
                'title'  => 'Edit',
                'method' => 'GET',
                'url'    => function ($row) {
                    return route('admin.consignment.edit', $row->id);
                },
            ]);
        }

        if (bouncer()->hasPermission('consignment.delete')) {
            $this->addAction([
                'index'  => 'delete',
                'icon'   => 'icon-delete',
                'title'  => 'Delete',
                'method' => 'DELETE',
                'url'    => function ($row) {
                    return route('admin.consignment.delete', $row->id);
                },
            ]);
        }
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        if (bouncer()->hasPermission('consignment.mass_delete')) {
            $this->addMassAction([
                'icon'   => 'icon-delete',
                'title'  => 'Delete',
                'method' => 'POST',
                'url'    => route('admin.consignment.mass_delete'),
            ]);
        }
    }
}
