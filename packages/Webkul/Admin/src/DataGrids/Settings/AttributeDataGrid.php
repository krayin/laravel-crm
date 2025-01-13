<?php

namespace Webkul\Admin\DataGrids\Settings;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\DataGrid\DataGrid;

class AttributeDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('attributes')
            ->select(
                'attributes.id',
                'attributes.code',
                'attributes.name',
                'attributes.type',
                'attributes.entity_type',
                'attributes.is_user_defined as attribute_type'
            )
            ->where('entity_type', '<>', 'locations');

        $this->addFilter('id', 'attributes.id');
        $this->addFilter('type', 'attributes.type');
        $this->addFilter('attribute_type', 'attributes.is_user_defined');

        return $queryBuilder;
    }

    /**
     * Prepare columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('admin::app.settings.attributes.index.datagrid.id'),
            'type'       => 'string',
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'code',
            'label'      => trans('admin::app.settings.attributes.index.datagrid.code'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => trans('admin::app.settings.attributes.index.datagrid.name'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'entity_type',
            'label'      => trans('admin::app.settings.attributes.index.datagrid.entity-type'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => false,
            'filterable' => true,
            'closure'    => fn ($row) => ucfirst($row->entity_type),
        ]);

        $this->addColumn([
            'index'              => 'type',
            'label'              => trans('admin::app.settings.attributes.index.datagrid.type'),
            'type'               => 'string',
            'sortable'           => true,
            'filterable'         => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => app(AttributeRepository::class)
                ->select('type as label', 'type as value')
                ->distinct()
                ->get()
                ->toArray(),
        ]);

        $this->addColumn([
            'index'      => 'attribute_type',
            'label'      => trans('admin::app.settings.attributes.index.datagrid.is-default'),
            'type'       => 'boolean',
            'searchable' => true,
            'filterable' => false,
            'sortable'   => true,
            'closure'    => fn ($value) => trans('admin::app.settings.attributes.index.datagrid.'.($value->attribute_type ? 'no' : 'yes')),
        ]);
    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        if (bouncer()->hasPermission('settings.automation.attributes.edit')) {
            $this->addAction([
                'icon'   => 'icon-edit',
                'title'  => trans('admin::app.settings.attributes.index.datagrid.edit'),
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.settings.attributes.edit', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('settings.automation.attributes.delete')) {
            $this->addAction([
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.settings.attributes.index.datagrid.delete'),
                'method' => 'DELETE',
                'url'    => fn ($row) => route('admin.settings.attributes.delete', $row->id),
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
            'title'  => trans('admin::app.settings.attributes.index.datagrid.delete'),
            'method' => 'POST',
            'url'    => route('admin.settings.attributes.mass_delete'),
        ]);
    }
}
