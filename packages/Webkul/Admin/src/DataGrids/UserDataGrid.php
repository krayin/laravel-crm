<?php

namespace Webkul\Admin\DataGrids;

use Webkul\UI\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;

class UserDataGrid extends DataGrid
{
    /**
     * @var string
     */
    public $index = 'id';


    /**
     * @var string
     */
    protected $sortOrder = 'desc';

    /**
     * Create a new datagrid instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('users')
            ->addSelect(
                'users.id',
                'users.name'
            );

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('admin::app.datagrid.id'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => trans('admin::app.datagrid.name'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);
    }

    public function prepareActions()
    {
        // $this->addAction([
        //     'title'  => trans('admin::app.datagrid.edit'),
        //     'method' => 'GET',
        //     'route'  => 'admin.customer.addresses.edit',
        //     'icon'   => 'icon pencil-lg-icon',
        // ]);

        // $this->addAction([
        //     'title'        => trans('admin::app.datagrid.delete'),
        //     'method'       => 'POST',
        //     'route'        => 'admin.customer.addresses.delete',
        //     'confirm_text' => trans('ui::app.datagrid.massaction.delete', ['resource' => 'address']),
        //     'icon'         => 'icon trash-icon',
        // ]);
    }

    public function prepareMassActions()
    {
    }
}
