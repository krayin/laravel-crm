<?php

namespace Webkul\Admin\DataGrids\Settings;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Webkul\DataGrid\DataGrid;

class AttributeDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return void
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
     * Add columns.
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
            'index'    => 'code',
            'label'    => trans('admin::app.settings.attributes.index.datagrid.code'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'name',
            'label'    => trans('admin::app.settings.attributes.index.datagrid.name'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'      => 'entity_type',
            'label'      => trans('admin::app.settings.attributes.index.datagrid.entity-type'),
            'type'       => 'string',
            'searchable' => false,
            'filterable' => true,
            'closure'    => function ($row) {
                return ucfirst($row->entity_type);
            },
        ]);

        $this->addColumn([
            'index'    => 'type',
            'label'    => trans('admin::app.settings.attributes.index.datagrid.type'),
            'type'     => 'string',
            'sortable' => true,
            'filterable' => true,
        ]);


        $this->addColumn([
            'index'      => 'attribute_type',
            'label'      => trans('admin::app.settings.attributes.index.datagrid.is-default'),
            'type'       => 'boolean',
            'searchable' => true,
            'filterable' => false,
            'sortable'   => true,
            'closure'    => function ($value) {
                return trans('admin::app.settings.attributes.index.datagrid.' . ($value->attribute_type ? 'no' : 'yes'));
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
            'icon'   => 'icon-edit',
            'title'  => trans('admin::app.settings.attributes.index.datagrid.edit'),
            'method' => 'GET',
            'url'    => function ($row) {
                return route('admin.settings.attributes.edit', $row->id);
            },
        ]);

        $this->addAction([
            'icon'   => 'icon-delete',
            'title'  => trans('admin::app.settings.attributes.index.datagrid.delete'),
            'method' => 'DELETE',
            'url'    => function ($row) {
                return route('admin.settings.attributes.delete', $row->id);
            },
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
            'icon'   => 'icon-delete',
            'title'  => trans('admin::app.settings.attributes.index.datagrid.delete'),
            'method' => 'POST',
            'url'    => route('admin.settings.attributes.mass_delete'),
        ]);
    }
}
