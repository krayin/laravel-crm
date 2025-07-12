<?php

namespace Webkul\Admin\DataGrids\Contact;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\Contact\Repositories\RelatedContactRepository;
use Webkul\DataGrid\DataGrid;

class RelatedContactDataGrid extends DataGrid
{
    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct(protected RelatedContactRepository $repository) {}

    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('related_contacts')
            ->select('id', 'name','type', 'emails', 'mobile_numbers', 'eid_expiry')
            ->whereNotNull(['name'])
            ->whereNot('name','=','');

        $this->addFilter('id', 'related_contacts.id');
        $this->addFilter('name', 'related_contacts.name');

        $this->setQueryBuilder($queryBuilder);

        return $queryBuilder;
    }

    /**
     * Add columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => "ID",
            'type'       => 'integer',
            'filterable' => true,
            'sortable'   => true,
            'searchable' => true,
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => "Name",
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
            'searchable' => true,
        ]);

        $this->addColumn([
            'index'      => 'type',
            'label'      => "Type",
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
            'searchable' => true,
        ]);

        $this->addColumn([
            'index'      => 'emails',
            'label'      => "Emails",
            'type'       => 'string',
            'sortable'   => false,
            'filterable' => true,
            'searchable' => true,
            'closure'    => fn($row) => collect(json_decode(json_decode($row->emails, true) ?? '[]'))->join(', '),
            ]);

        $this->addColumn([
            'index'      => 'mobile_numbers',
            'label'      => "Contact Numbers",
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
            'searchable' => true,
            'closure'    => fn($row) => collect(json_decode(json_decode($row->mobile_numbers, true) ?? '[]'))->join(', '),

        ]);

    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {

      //  if (bouncer()->hasPermission('contacts.relatedContact.view')) {
            $this->addAction([
                'icon'   => 'icon-eye',
                'title'  => trans('admin::app.contacts.relatedContact.index.datagrid.view'),
                'method' => 'GET',
                'url'    => function ($row) {
                    return route('admin.contacts.related-contacts.view', $row->id);
                },
            ]);
     //   }

       // if (bouncer()->hasPermission('contacts.relatedContact.edit')) {
            $this->addAction([
                'icon'   => 'icon-edit',
                'title'  => trans('admin::app.contacts.persons.index.datagrid.edit'),
                'method' => 'GET',
                'url'    => function ($row) {
                    return route('admin.contacts.related-contacts.edit', $row->id);
                },
            ]);
       // }

      //  if (bouncer()->hasPermission('contacts.relatedContact.delete')) {
            $this->addAction([
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.contacts.persons.index.datagrid.delete'),
                'method' => 'DELETE',
                'url'    => function ($row) {
                    return route('admin.contacts.related-contacts.delete', $row->id);
                },
            ]);
       // }

    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        /*
        if (bouncer()->hasPermission('contacts.persons.delete')) {
            $this->addMassAction([
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.contacts.persons.index.datagrid.delete'),
                'method' => 'POST',
                'url'    => route('admin.contacts.persons.mass_delete'),
            ]);
        }
        */
    }
}
