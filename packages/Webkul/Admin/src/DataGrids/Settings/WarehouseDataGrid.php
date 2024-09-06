<?php

namespace Webkul\Admin\DataGrids\Settings;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class WarehouseDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder(): Builder
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
            ->addSelect(DB::raw('count(DISTINCT '.DB::getTablePrefix().'product_inventories.product_id) as products'))
            ->groupBy('warehouses.id');

        $this->addFilter('id', 'warehouses.id');
        $this->addFilter('created_at', 'warehouses.created_at');

        return $queryBuilder;
    }

    /**
     * Add columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('admin::app.settings.warehouses.index.datagrid.id'),
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => trans('admin::app.settings.warehouses.index.datagrid.name'),
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'contact_name',
            'label'      => trans('admin::app.settings.warehouses.index.datagrid.contact-name'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'contact_emails',
            'label'      => trans('admin::app.settings.warehouses.index.datagrid.contact-emails'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
            'closure'    => function ($row) {
                $emails = json_decode($row->contact_emails, true);

                if ($emails) {
                    return collect($emails)->pluck('value')->join(', ');
                }
            },
        ]);

        $this->addColumn([
            'index'    => 'contact_numbers',
            'label'    => trans('admin::app.settings.warehouses.index.datagrid.contact-numbers'),
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
            'label'      => trans('admin::app.settings.warehouses.index.datagrid.products'),
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index'           => 'created_at',
            'label'           => trans('admin::app.settings.warehouses.index.datagrid.created-at'),
            'type'            => 'date',
            'searchable'      => true,
            'filterable'      => true,
            'filterable_type' => 'date_range',
            'sortable'        => true,
            'closure'         => function ($row) {
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
            'icon'   => 'icon-eye',
            'title'  => trans('admin::app.settings.warehouses.index.datagrid.view'),
            'method' => 'GET',
            'url'    => function ($row) {
                return route('admin.settings.warehouses.view', $row->id);
            },
        ]);

        $this->addAction([
            'icon'   => 'icon-edit',
            'title'  => trans('admin::app.settings.warehouses.index.datagrid.edit'),
            'method' => 'GET',
            'url'    => function ($row) {
                return route('admin.settings.warehouses.edit', $row->id);
            },
        ]);

        $this->addAction([
            'icon'   => 'icon-delete',
            'title'  => trans('admin::app.settings.warehouses.index.datagrid.delete'),
            'method' => 'DELETE',
            'url'    => function ($row) {
                return route('admin.settings.warehouses.delete', $row->id);
            },
        ]);
    }
}
