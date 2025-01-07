<?php

namespace Webkul\ProductManagement\DataGrids;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class ProductDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('product_management')->orderBy('id', 'asc');

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
            'index'      => 'name',
            'label'      => 'Name',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);
    }

    public function prepareActions(): void
    {

        // if (bouncer()->hasPermission('productmanagement.view')) {
        //     $this->addAction([
        //         'icon'   => 'icon-eye',
        //         'title'  => 'View',
        //         'method' => 'GET',
        //         'url'    => function ($row) {
        //             return route('admin.productmanagement.view', $row->id);
        //         },
        //     ]);
        // }


        if (bouncer()->hasPermission('productmanagement.edit')) {
            $this->addAction([
                'index'  => 'edit',
                'icon'   => 'icon-edit',
                'title'  => 'Edit',
                'method' => 'GET',
                'url'    => function ($row) {
                    return route('admin.productmanagement.edit', $row->id);
                },
            ]);
        }

        if (bouncer()->hasPermission('productmanagement.delete')) {
            $this->addAction([
                'index'  => 'delete',
                'icon'   => 'icon-delete',
                'title'  => 'Delete',
                'method' => 'DELETE',
                'url'    => function ($row) {
                    return route('admin.productmanagement.delete', $row->id);
                },
            ]);
        }
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        if (bouncer()->hasPermission('productmanagement.mass_delete')) {
            $this->addMassAction([
                'icon'   => 'icon-delete',
                'title'  => 'Delete',
                'method' => 'POST',
                'url'    => route('admin.productmanagement.mass_delete'),
            ]);
        }
    }
}
