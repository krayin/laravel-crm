<?php

namespace Webkul\Admin\DataGrids\Product;

use Webkul\UI\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;

class ProductDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('products')
            ->leftJoin('product_inventories', 'products.id', '=', 'product_inventories.product_id')
            ->select(
                'products.id',
                'products.sku',
                'products.name',
                'products.price',
            )
            ->addSelect(DB::raw('SUM(product_inventories.in_stock) as total_in_stock'))
            ->addSelect(DB::raw('SUM(product_inventories.allocated) as total_allocated'))
            ->addSelect(DB::raw('SUM(product_inventories.in_stock - product_inventories.allocated) as total_on_hand'))
            ->groupBy('products.id');

        if (request()->route('id')) {
            $queryBuilder->where('product_inventories.warehouse_id', request()->route('id'));
        }

        $this->addFilter('id', 'products.id');
        $this->addFilter('total_in_stock', DB::raw('SUM(product_inventories.in_stock'));
        $this->addFilter('total_allocated', DB::raw('SUM(product_inventories.allocated'));
        $this->addFilter('total_on_hand', DB::raw('SUM(product_inventories.in_stock - product_inventories.allocated'));

        $this->setQueryBuilder($queryBuilder);
    }

    /**
     * Add columns.
     *
     * @return void
     */
    public function addColumns()
    {
        $this->addColumn([
            'index'    => 'sku',
            'label'    => trans('admin::app.datagrid.sku'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'name',
            'label'    => trans('admin::app.datagrid.name'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'price',
            'label'    => trans('admin::app.datagrid.price'),
            'type'     => 'string',
            'sortable' => true,
            'closure'  => function ($row) {
                return round($row->price, 2);
            },
        ]);

        $this->addColumn([
            'index'    => 'total_in_stock',
            'label'    => trans('admin::app.datagrid.in-stock'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'total_allocated',
            'label'    => trans('admin::app.datagrid.allocated'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'total_on_hand',
            'label'    => trans('admin::app.datagrid.on-hand'),
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
        $this->addAction([
            'title'  => trans('ui::app.datagrid.view'),
            'method' => 'GET',
            'route'  => 'admin.products.view',
            'icon'   => 'eye-icon',
        ]);

        $this->addAction([
            'title'  => trans('ui::app.datagrid.edit'),
            'method' => 'GET',
            'route'  => 'admin.products.edit',
            'icon'   => 'pencil-icon',
        ]);

        $this->addAction([
            'title'        => trans('ui::app.datagrid.delete'),
            'method'       => 'DELETE',
            'route'        => 'admin.products.delete',
            'confirm_text' => trans('ui::app.datagrid.mass-action.delete', ['resource' => 'user']),
            'icon'         => 'trash-icon',
        ]);
    }

    /**
     * Prepare mass actions.
     *
     * @return void
     */
    public function prepareMassActions()
    {
        $this->addMassAction([
            'type'   => 'delete',
            'label'  => trans('ui::app.datagrid.delete'),
            'action' => route('admin.products.mass_delete'),
            'method' => 'PUT',
        ]);
    }
}
