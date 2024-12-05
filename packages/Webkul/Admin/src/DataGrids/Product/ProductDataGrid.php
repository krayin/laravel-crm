<?php

namespace Webkul\Admin\DataGrids\Product;

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
        $tablePrefix = DB::getTablePrefix();

        $queryBuilder = DB::table('products')
            ->leftJoin('product_inventories', 'products.id', '=', 'product_inventories.product_id')
            ->select(
                'products.id',
                'products.sku',
                'products.name',
                'products.price',
            )
            ->addSelect(DB::raw('SUM('.$tablePrefix.'product_inventories.in_stock) as total_in_stock'))
            ->addSelect(DB::raw('SUM('.$tablePrefix.'product_inventories.allocated) as total_allocated'))
            ->addSelect(DB::raw('SUM('.$tablePrefix.'product_inventories.in_stock - '.$tablePrefix.'product_inventories.allocated) as total_on_hand'))
            ->groupBy('products.id');

        if (request()->route('id')) {
            $queryBuilder->where('product_inventories.warehouse_id', request()->route('id'));
        }

        $this->addFilter('id', 'products.id');
        $this->addFilter('total_in_stock', DB::raw('SUM('.$tablePrefix.'product_inventories.in_stock'));
        $this->addFilter('total_allocated', DB::raw('SUM('.$tablePrefix.'product_inventories.allocated'));
        $this->addFilter('total_on_hand', DB::raw('SUM('.$tablePrefix.'product_inventories.in_stock - '.$tablePrefix.'product_inventories.allocated'));

        return $queryBuilder;
    }

    /**
     * Add columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'sku',
            'label'      => trans('admin::app.products.index.datagrid.sku'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => trans('admin::app.products.index.datagrid.name'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'price',
            'label'      => trans('admin::app.products.index.datagrid.price'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
            'closure'    => fn ($row) => round($row->price, 2),
        ]);

        $this->addColumn([
            'index'    => 'total_in_stock',
            'label'    => trans('admin::app.products.index.datagrid.in-stock'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'total_allocated',
            'label'    => trans('admin::app.products.index.datagrid.allocated'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'total_on_hand',
            'label'    => trans('admin::app.products.index.datagrid.on-hand'),
            'type'     => 'string',
            'sortable' => true,
        ]);
    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        if (bouncer()->hasPermission('products.view')) {
            $this->addAction([
                'index'  => 'view',
                'icon'   => 'icon-eye',
                'title'  => trans('admin::app.products.index.datagrid.view'),
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.products.view', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('products.edit')) {
            $this->addAction([
                'index'  => 'edit',
                'icon'   => 'icon-edit',
                'title'  => trans('admin::app.products.index.datagrid.edit'),
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.products.edit', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('products.delete')) {
            $this->addAction([
                'index'  => 'delete',
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.products.index.datagrid.delete'),
                'method' => 'DELETE',
                'url'    => fn ($row) => route('admin.products.delete', $row->id),
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
            'title'  => trans('admin::app.products.index.datagrid.delete'),
            'method' => 'POST',
            'url'    => route('admin.products.mass_delete'),
        ]);
    }
}
