<?php

namespace Webkul\Admin\DataGrids\Setting;

use Illuminate\Support\Facades\DB;
use Webkul\Admin\Traits\ProvideDropdownOptions;
use Webkul\UI\DataGrid\DataGrid;

class LocationDataGrid extends DataGrid
{
    use ProvideDropdownOptions;

    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('warehouse_locations')
            ->addSelect(
                'warehouse_locations.id',
                'warehouse_locations.aisle',
                'warehouse_locations.bay',
                'warehouse_locations.shelf',
                'warehouse_locations.bin',
                'warehouse_locations.created_at',
                'warehouse_id',
                'warehouses.name as warehouse',
                'warehouses.id as warehouse_id'
            )
            ->leftJoin('warehouses', 'warehouse_locations.warehouse_id', '=', 'warehouses.id');

        if (request()->query('warehouse_id')) {
            $this->enableSearch = false;

            $this->enableFilters = false;

            $queryBuilder->where('warehouse_locations.warehouse_id', request()->query('warehouse_id'));
        }

        $this->addFilter('id', 'warehouse_locations.id');
        $this->addFilter('warehouse', 'warehouses.id');

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
            'index'      => 'id',
            'label'      => trans('admin::app.datagrid.id'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'aisle',
            'label'      => trans('admin::app.datagrid.aisle'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'bay',
            'label'      => trans('admin::app.datagrid.bay'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
        ]);

        $this->addColumn([
            'index'      => 'shelf',
            'label'      => trans('admin::app.datagrid.shelf'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
        ]);

        $this->addColumn([
            'index'      => 'bin',
            'label'      => trans('admin::app.datagrid.bin'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
        ]);

        if (! request()->query('warehouse_id')) {
            $this->addColumn([
                'index'            => 'warehouse',
                'label'            => trans('admin::app.datagrid.name'),
                'type'             => 'dropdown',
                'dropdown_options' => $this->getWarehouseDropdownOptions(),
                'searchable'       => false,
                'sortable'         => false,
                'closure'  => function ($row) {
                    return "<a href='" . route('admin.settings.warehouses.view', $row->warehouse_id) . "' target='_blank'>" . $row->warehouse . "</a>";
                },
            ]);
        }

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('admin::app.datagrid.created_at'),
            'type'       => 'date_range',
            'searchable' => false,
            'sortable'   => true,
            'closure'    => function ($row) {
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
            'title'  => trans('ui::app.datagrid.edit'),
            'method' => 'GET',
            'route'  => 'admin.settings.locations.edit',
            'icon'   => 'pencil-icon',
        ]);

        $this->addAction([
            'title'        => trans('ui::app.datagrid.delete'),
            'method'       => 'DELETE',
            'route'        => 'admin.settings.locations.delete',
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
            'action' => route('admin.settings.locations.mass_delete'),
            'method' => 'PUT',
        ]);
    }
}
