<?php

namespace Webkul\Admin\DataGrids\Setting;

use Webkul\UI\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;

class AttributeDataGrid extends DataGrid
{
    protected $tabFilters = [];
    
    protected $redirectRow = [
        "id"    => "id",
        "route" => "admin.settings.attributes.edit",
    ];

    public function __construct()
    {
        $this->tabFilters = [
            [
                'type'      => 'pill',
                'key'       => 'type',
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

        $this->addFilter('type', 'entity_type');

        parent::__construct();
    }

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('attributes')
            ->addSelect(
                'attributes.id',
                'attributes.code',
                'attributes.name',
                'attributes.type',
                'attributes.entity_type'
            );

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'      => 'id',
            'head_style' => 'width: 50px',
            'label'      => trans('admin::app.datagrid.id'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'code',
            'label'      => trans('admin::app.datagrid.code'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => trans('admin::app.datagrid.name'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'   => 'entity_type',
            'label'   => trans('admin::app.datagrid.entity_type'),
            'type'    => 'string',
            'closure' => function ($row) {
                return ucfirst($row->entity_type);
            },
        ]);

        $this->addColumn([
            'index'      => 'type',
            'label'      => trans('admin::app.datagrid.type'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
        ]);
    }

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
