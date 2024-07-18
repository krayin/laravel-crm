<?php

namespace Webkul\Admin\DataGrids\Contact;

use Illuminate\Support\Facades\DB;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\DataGrid\DataGrid;
use Illuminate\Database\Query\Builder;

class OrganizationDataGrid extends DataGrid
{
    /**
     * Create datagrid instance.
     *
     * @return void
     */
    public function __construct(protected PersonRepository $personRepository)
    {
    }

    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        return DB::table('organizations')
            ->addSelect(
                'organizations.id',
                'organizations.name',
                'organizations.address',
                'organizations.created_at'
            );

        $this->addFilter('id', 'organizations.id');
        $this->addFilter('organization', 'organizations.name');
    }

    /**
     * Add columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('admin::app.datagrid.id'),
            'type'       => 'integer',
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => trans('admin::app.datagrid.name'),
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
        ]);
    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        $this->addAction([
            'icon'   => 'icon-edit',
            'title'  => trans('admin::app.catalog.attributes.index.datagrid.edit'),
            'method' => 'GET',
            'url'    => function ($row) {
                return route('admin.contacts.organizations.edit', $row->id);
            },
        ]);

        $this->addAction([
            'icon'   => 'icon-delete',
            'title'  => trans('admin::app.catalog.attributes.index.datagrid.delete'),
            'method' => 'DELETE',
            'url'    => function ($row) {
                return route('admin.contacts.organizations.delete', $row->id);
            },
        ]);
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        $this->addMassAction([
            'icon'   => 'icon-delete',
            'title'  => trans('ui::app.datagrid.delete'),
            'method' => 'PUT',
            'url'    => route('admin.contacts.organizations.mass_delete'),
        ]);
    }
}
