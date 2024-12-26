<?php

namespace Webkul\AssetManagement\DataGrids;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class InstrumentDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('instruments')->addSelect('id');

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
        // partner name, partner id,date ,instrument id , instrument name,quntity
        $this->addColumn([
            'index'      => 'partner_name',
            'label'      => 'Partner Name',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);
        $this->addColumn([
            'index'      => 'partner_id',
            'label'      => 'Partner ID',
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
            'index'      => 'instrument_id',
            'label'      => 'Instrument ID',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);
        $this->addColumn([
            'index'      => 'instrument_name',
            'label'      => 'Instrument Name',
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

    }

    public function prepareActions(): void
    {
        if (bouncer()->hasPermission('instrument.view')) {
            $this->addAction([
                'icon'   => 'icon-eye',
                'title'  => 'View',
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.instrument.view', $row->id),
            ]);
        }
        if (bouncer()->hasPermission('instrument.edit')) {
            $this->addAction([
                'icon'   => 'icon-pencil',
                'title'  => 'Edit',
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.instrument.edit', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('instrument.delete')) {
            $this->addAction([
                'icon'   => 'icon-delete',
                'title'  => 'Delete',
                'method' => 'delete',
                'url'    => fn ($row) => route('admin.instrument.delete', $row->id),
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
            'url'    => route('admin.instrument.mass_delete'),
        ]);

    }
}
