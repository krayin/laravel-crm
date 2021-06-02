<?php

namespace Webkul\Admin\DataGrids\Contact;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Webkul\UI\DataGrid\DataGrid;

class PersonDataGrid extends DataGrid
{
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('persons')
            ->addSelect(
                'persons.id',
                'persons.name',
                'persons.emails',
                'persons.contact_numbers',
                'organizations.name as organization_name'
            )
            ->leftJoin('organizations', 'persons.organization_id', '=', 'organizations.id');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'             => 'name',
            'label'             => trans('admin::app.datagrid.name'),
            'type'              => 'string',
            'searchable'        => true,
            'sortable'          => true,
            'filterable_type'   => 'add'
        ]);

        $this->addColumn([
            'index'      => 'emails',
            'label'      => trans('admin::app.datagrid.emails'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => false,
            'closure'    => function ($row) {
                $emails = Arr::pluck(json_decode($row->emails, true), 'value');

                return implode(', ', $emails);
            },
        ]);

        $this->addColumn([
            'index'      => 'contact_numbers',
            'label'      => trans('admin::app.datagrid.contact_numbers'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => false,
            'closure'    => function ($row) {
                $response = "";
                $contactNumbers = json_decode($row->contact_numbers, true);

                foreach ($contactNumbers as $index => $contactNumber) {
                    $response .= $contactNumber['value'];

                    if (sizeof($contactNumbers) != $index + 1) {
                        $response .= ',';
                    }
                }
                
                return $response;
            },
        ]);

        $this->addColumn([
            'index'             => 'organization_name',
            'label'             => trans('admin::app.datagrid.organization_name'),
            'type'              => 'string',
            'searchable'        => true,
            'sortable'          => true,
            'filterable_type'   => 'add'
        ]);
    }

    public function prepareActions()
    {
        $this->addAction([
            'title'  => trans('ui::app.datagrid.edit'),
            'method' => 'GET',
            'route'  => 'admin.contacts.persons.edit',
            'icon'   => 'icon pencil-icon',
        ]);

        $this->addAction([
            'title'        => trans('ui::app.datagrid.delete'),
            'method'       => 'DELETE',
            'route'        => 'admin.contacts.persons.delete',
            'confirm_text' => trans('ui::app.datagrid.massaction.delete', ['resource' => trans('admin::app.contacts.persons.person')]),
            'icon'         => 'icon trash-icon',
        ]);
    }

    public function prepareMassActions()
    {
        $this->addMassAction([
            'type'   => 'delete',
            'label'  => trans('ui::app.datagrid.delete'),
            'action' => route('admin.contacts.persons.mass-delete'),
            'method' => 'PUT',
        ]);
    }
}
