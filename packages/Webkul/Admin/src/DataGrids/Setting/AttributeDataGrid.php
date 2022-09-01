<?php

namespace Webkul\Admin\DataGrids\Setting;

use Illuminate\Support\Facades\DB;
use Webkul\UI\DataGrid\DataGrid;

class AttributeDataGrid extends DataGrid
{
    /**
     * Create datagrid instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->tabFilters = [
            [
                'type'      => 'pill',
                'key'       => 'entity_type',
                'condition' => 'eq',
                'values'    => [
                    [
                        'name'      => trans('admin::app.leads.all'),
                        'isActive'  => true,
                        'key'       => 'all',
                    ], [
                        'name'      => trans('admin::app.leads.title'),
                        'isActive'  => false,
                        'key'       => 'leads',
                    ], [
                        'name'      => trans('admin::app.contacts.persons.title'),
                        'isActive'  => false,
                        'key'       => 'persons',
                    ], [
                        'name'      => trans('admin::app.contacts.organizations.title'),
                        'isActive'  => false,
                        'key'       => 'organizations',
                    ], [
                        'name'      => trans('admin::app.products.title'),
                        'isActive'  => false,
                        'key'       => 'products',
                    ], [
                        'name'      => trans('admin::app.quotes.title'),
                        'isActive'  => false,
                        'key'       => 'quotes',
                    ]
                ]
            ]
        ];
    }

    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('attributes')
            ->addSelect(
                'attributes.id',
                'attributes.code',
                'attributes.name',
                'attributes.type',
                'attributes.entity_type',
                'attributes.is_user_defined as attribute_type'
            );

        $this->addFilter('id', 'attributes.id');
        $this->addFilter('type', 'attributes.type');
        $this->addFilter('attribute_type', 'attributes.is_user_defined');

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
            'index'    => 'code',
            'label'    => trans('admin::app.datagrid.code'),
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
            'index'      => 'entity_type',
            'label'      => trans('admin::app.datagrid.entity_type'),
            'type'       => 'string',
            'searchable' => false,
            'closure'    => function ($row) {
                return ucfirst($row->entity_type);
            },
        ]);

        $this->addColumn([
            'index'    => 'type',
            'label'    => trans('admin::app.datagrid.type'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'             => 'attribute_type',
            'label'             => trans('admin::app.datagrid.attribute_type'),
            'type'              => 'dropdown',
            'dropdown_options'  => $this->getAttributeTypeDropdownOptions(),
            'sortable'          => true,
            'closure'           => function ($row) {
                return $row->attribute_type ? trans('admin::app.common.custom_attribute') : trans('admin::app.common.system_attribute');
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
            'route'  => 'admin.settings.attributes.edit',
            'icon'   => 'pencil-icon',
        ]);

        $this->addAction([
            'title'        => trans('ui::app.datagrid.delete'),
            'method'       => 'DELETE',
            'route'        => 'admin.settings.attributes.delete',
            'confirm_text' => trans('ui::app.datagrid.massaction.delete', ['resource' => 'attributes']),
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
            'action' => route('admin.settings.attributes.mass_delete'),
            'method' => 'PUT',
        ]);
    }
}
