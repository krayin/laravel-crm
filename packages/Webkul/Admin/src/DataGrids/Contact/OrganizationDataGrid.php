<?php

namespace Webkul\Admin\DataGrids\Contact;

use Webkul\UI\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;
use Webkul\Contact\Repositories\PersonRepository;

class OrganizationDataGrid extends DataGrid
{
    protected $personRepository;

    protected $redirectRow = [
        "id"    => "id",
        "route" => "admin.contacts.organizations.edit",
    ];

    public function __construct(PersonRepository $personRepository)
    {
        $this->personRepository = $personRepository;

        parent::__construct();
    }

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('organizations')
            ->addSelect(
                'organizations.id',
                'organizations.name',
                'organizations.address',
                'organizations.created_at'
            );

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'             => 'id',
            'head_style'        => 'width: 50px',
            'label'             => trans('admin::app.datagrid.id'),
            'type'              => 'string',
            'searchable'        => true,
            'sortable'          => true,
            'filterable_type'   => 'add'
        ]);

        $this->addColumn([
            'index'             => 'name',
            'label'             => trans('admin::app.datagrid.name'),
            'type'              => 'string',
            'searchable'        => true,
            'sortable'          => true,
            'filterable_type'   => 'add'
        ]);

        $this->addColumn([
            'index'             => 'persons_count',
            'head_style'        => 'width: 100px',
            'label'             => trans('admin::app.datagrid.persons_count'),
            'type'              => 'string',
            'searchable'        => true,
            'closure'           => function ($row) {
                $personsCount = $this->personRepository
                                ->findWhere(['organization_id' => $row->id])
                                ->count();

                $route = urldecode(route('admin.contacts.persons.index', ['organization[in]' => $row->id]));

                return "<a href='" . $route . "'>" . $personsCount . "</a>";
            },
        ]);

        $this->addColumn([
            'index'             => 'created_at',
            'label'             => trans('admin::app.datagrid.created_at'),
            'type'              => 'string',
            'sortable'          => true,
            'filterable_type'   => 'date_range',
            'closure'           => function ($row) {
                return core()->formatDate($row->created_at);
            },
        ]);
    }

    public function prepareActions()
    {
        $this->addAction([
            'title'  => trans('ui::app.datagrid.edit'),
            'method' => 'GET',
            'route'  => 'admin.contacts.organizations.edit',
            'icon'   => 'pencil-icon',
        ]);

        $this->addAction([
            'title'        => trans('ui::app.datagrid.delete'),
            'method'       => 'DELETE',
            'route'        => 'admin.contacts.organizations.delete',
            'confirm_text' => trans('ui::app.datagrid.massaction.delete', ['resource' => 'user']),
            'icon'         => 'trash-icon',
        ]);
    }

    public function prepareMassActions()
    {
        $this->addMassAction([
            'type'   => 'delete',
            'label'  => trans('ui::app.datagrid.delete'),
            'action' => route('admin.contacts.organizations.mass-delete'),
            'method' => 'PUT',
        ]);
    }
}
