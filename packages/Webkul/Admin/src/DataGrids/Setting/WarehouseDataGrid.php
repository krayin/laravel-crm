<?php

namespace Webkul\Admin\DataGrids\Setting;

use Illuminate\Support\Facades\DB;
use Webkul\UI\DataGrid\DataGrid;

class WarehouseDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('warehouses')
            ->leftJoin('product_inventories', 'warehouses.id', '=', 'product_inventories.warehouse_id')
            ->select(
                'warehouses.id',
                'warehouses.name',
                'warehouses.contact_name',
                'warehouses.contact_emails',
                'warehouses.contact_numbers',
                'warehouses.created_at',
            )
            ->addSelect(DB::raw('count(DISTINCT product_inventories.product_id) as products'))
            ->groupBy('warehouses.id');

        $this->addFilter('id', 'warehouses.id');
        $this->addFilter('created_at', 'warehouses.created_at');

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
            'index'    => 'id',
            'label'    => trans('admin::app.datagrid.id'),
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
            'index'    => 'contact_name',
            'label'    => trans('admin::app.datagrid.contact-name'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'contact_emails',
            'label'    => trans('admin::app.datagrid.contact-emails'),
            'type'     => 'string',
            'sortable' => false,
            'closure'  => function ($row) {
                $emails = json_decode($row->contact_emails, true);

                if ($emails) {
                    return collect($emails)->pluck('value')->join(', ');
                }
            },
        ]);

        $this->addColumn([
            'index'    => 'contact_numbers',
            'label'    => trans('admin::app.datagrid.contact-numbers'),
            'type'     => 'string',
            'sortable' => false,
            'closure'  => function ($row) {
                $numbers = json_decode($row->contact_numbers, true);

                if ($numbers) {
                    return collect($numbers)->pluck('value')->join(', ');
                }
            },
        ]);

        $this->addColumn([
            'index'      => 'products',
            'label'      => trans('admin::app.datagrid.products'),
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => false,
            'closure'    => function ($row) {
                return "<a href='" . route('admin.settings.warehouses.products.index', $row->id) . "'>" . $row->products . "</a>";
            },
        ]);

        $this->addColumn([
            'index'    => 'created_at',
            'label'    => trans('admin::app.datagrid.created_at'),
            'type'     => 'date_range',
            'sortable' => true,
            'closure'  => function ($row) {
                return core()->formatDate($row->created_at);
            },
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
            'route'  => 'admin.settings.warehouses.view',
            'icon'   => 'eye-icon',
        ]);

        $this->addAction([
            'title'  => trans('ui::app.datagrid.edit'),
            'method' => 'GET',
            'route'  => 'admin.settings.warehouses.edit',
            'icon'   => 'pencil-icon',
        ]);

        $this->addAction([
            'title'        => trans('ui::app.datagrid.delete'),
            'method'       => 'DELETE',
            'route'        => 'admin.settings.warehouses.delete',
            'confirm_text' => trans('ui::app.datagrid.mass-action.delete', ['resource' => 'user']),
            'icon'         => 'trash-icon',
        ]);
    }
}
