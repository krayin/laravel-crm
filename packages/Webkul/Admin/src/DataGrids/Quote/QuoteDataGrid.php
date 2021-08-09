<?php

namespace Webkul\Admin\DataGrids\Quote;

use Webkul\UI\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;

class QuoteDataGrid extends DataGrid
{
    protected $redirectRow = [
        "id"    => "id",
        "route" => "admin.quotes.edit",
    ];

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('quotes')
            ->addSelect(
                'quotes.id',
                'quotes.subject',
                'quotes.expired_at',
                'quotes.sub_total',
                'quotes.discount_amount',
                'quotes.tax_amount',
                'quotes.adjustment_amount',
                'quotes.grand_total',
                'users.id as user_id',
                'users.name as user_name',
                'persons.id as person_id',
                'persons.name as person_name'
            )
            ->leftJoin('users', 'quotes.user_id', '=', 'users.id')
            ->leftJoin('persons', 'quotes.person_id', '=', 'persons.id');

        $currentUser = auth()->guard('user')->user();

        if ($currentUser->view_permission != 'global') {
            if ($currentUser->view_permission == 'group') {
                $queryBuilder->whereIn('quotes.user_id', app('\Webkul\User\Repositories\UserRepository')->getCurrentUserGroupsUserIds());
            } else {
                $queryBuilder->where('quotes.user_id', $currentUser->id);
            }
        }

        $this->addFilter('id', 'quotes.id');
        $this->addFilter('user', 'quotes.user_id');
        $this->addFilter('user_name', 'quotes.user_id');
        $this->addFilter('person_name', 'persons.name');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'             => 'subject',
            'label'             => trans('admin::app.datagrid.subject'),
            'type'              => 'string',
            'searchable'        => true,
            'sortable'          => true,
            'filterable_type'   => 'add'
        ]);

        $this->addColumn([
            'index'              => 'user_name',
            'label'              => trans('admin::app.datagrid.sales-person'),
            'type'               => 'string',
            'sortable'           => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => app('\Webkul\User\Repositories\UserRepository')->get(['id as value', 'name as label'])->toArray(),
            'closure'            => function ($row) {
                $route = urldecode(route('admin.settings.users.index', ['id[eq]' => $row->user_id]));

                return "<a href='" . $route . "'>" . $row->user_name . "</a>";
            },
        ]);

        $this->addColumn([
            'index'           => 'person_name',
            'label'           => trans('admin::app.datagrid.person'),
            'type'            => 'string',
            'sortable'        => true,
            'filterable_type' => 'add',
            'closure'         => function ($row) {
                $route = urldecode(route('admin.contacts.persons.index', ['id[eq]' => $row->person_id]));

                return "<a href='" . $route . "'>" . $row->person_name . "</a>";
            },
        ]);

        $this->addColumn([
            'index'         => 'sub_total',
            'label'         => trans('admin::app.datagrid.sub-total'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'closure'       => function ($row) {
                return core()->formatBasePrice($row->sub_total, 2);
            },
        ]);

        $this->addColumn([
            'index'         => 'discount_amount',
            'label'         => trans('admin::app.datagrid.discount'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'closure'       => function ($row) {
                return core()->formatBasePrice($row->discount_amount, 2);
            },
        ]);

        $this->addColumn([
            'index'         => 'tax_amount',
            'label'         => trans('admin::app.datagrid.tax'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'closure'       => function ($row) {
                return core()->formatBasePrice($row->tax_amount, 2);
            },
        ]);

        $this->addColumn([
            'index'         => 'adjustment_amount',
            'label'         => trans('admin::app.datagrid.adjustment'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'closure'       => function ($row) {
                return core()->formatBasePrice($row->adjustment_amount, 2);
            },
        ]);

        $this->addColumn([
            'index'         => 'grand_total',
            'label'         => trans('admin::app.datagrid.grand-total'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'closure'       => function ($row) {
                return core()->formatBasePrice($row->grand_total, 2);
            },
        ]);
    }

    public function prepareActions()
    {
        $this->addAction([
            'title'  => trans('ui::app.datagrid.edit'),
            'method' => 'GET',
            'route'  => 'admin.quotes.edit',
            'icon'   => 'pencil-icon',
        ]);

        $this->addAction([
            'title'        => trans('ui::app.datagrid.delete'),
            'method'       => 'DELETE',
            'route'        => 'admin.quotes.delete',
            'confirm_text' => trans('ui::app.datagrid.massaction.delete', ['resource' => 'user']),
            'icon'         => 'trash-icon',
        ]);
    }

    public function prepareMassActions()
    {
        $this->addMassAction([
            'type'   => 'delete',
            'label'  => trans('ui::app.datagrid.delete'),
            'action' => route('admin.quotes.mass_delete'),
            'method' => 'PUT',
        ]);
    }
}
