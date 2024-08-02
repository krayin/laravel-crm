<?php

namespace Webkul\Admin\DataGrids\Quote;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;
use Webkul\User\Repositories\UserRepository;

class QuoteDataGrid extends DataGrid
{
    public function __construct(protected UserRepository $userRepository) {}

    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
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
                'quotes.created_at',
                'users.id as user_id',
                'users.name as sales_person',
                'persons.id as person_id',
                'persons.name as person_name',
                'quotes.expired_at as expired_quotes'
            )
            ->leftJoin('users', 'quotes.user_id', '=', 'users.id')
            ->leftJoin('persons', 'quotes.person_id', '=', 'persons.id');

        $currentUser = auth()->guard('user')->user();

        if ($currentUser->view_permission != 'global') {
            if ($currentUser->view_permission == 'group') {
                $queryBuilder->whereIn('quotes.user_id', $this->userRepository->getCurrentUserGroupsUserIds());
            } else {
                $queryBuilder->where('quotes.user_id', $currentUser->id);
            }
        }

        $this->addFilter('id', 'quotes.id');
        $this->addFilter('user', 'quotes.user_id');
        $this->addFilter('sales_person', 'quotes.user_id');
        $this->addFilter('person_name', 'persons.name');
        $this->addFilter('expired_at', 'quotes.expired_at');
        $this->addFilter('created_at', 'quotes.created_at');

        if (request()->input('expired_quotes.in') == 1) {
            $this->addFilter('expired_quotes', DB::raw('DATEDIFF(NOW(), '.DB::getTablePrefix().'quotes.expired_at) >= '.DB::getTablePrefix().'NOW()'));
        } else {
            $this->addFilter('expired_quotes', DB::raw('DATEDIFF(NOW(), '.DB::getTablePrefix().'quotes.expired_at) < '.DB::getTablePrefix().'NOW()'));
        }

        return $queryBuilder;
    }

    /**
     * Prepare columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'    => 'subject',
            'label'    => trans('admin::app.quotes.index.datagrid.subject'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'sales_person',
            'label'    => trans('admin::app.quotes.index.datagrid.sales-person'),
            'type'     => 'string',
            'sortable' => true,
            'closure'  => function ($row) {
                $route = urldecode(route('admin.settings.users.index', ['id[eq]' => $row->user_id]));

                return "<a href='".$route."'>".$row->sales_person.'</a>';
            },
        ]);

        $this->addColumn([
            'index'      => 'expired_quotes',
            'label'      => trans('admin::app.quotes.index.datagrid.expired-quotes'),
            'type'       => 'string',
            'searchable' => false,
        ]);

        $this->addColumn([
            'index'    => 'person_name',
            'label'    => trans('admin::app.quotes.index.datagrid.person'),
            'type'     => 'string',
            'sortable' => true,
            'closure'  => function ($row) {
                $route = urldecode(route('admin.contacts.persons.index', ['id[eq]' => $row->person_id]));

                return "<a href='".$route."'>".$row->person_name.'</a>';
            },
        ]);

        $this->addColumn([
            'index'    => 'sub_total',
            'label'    => trans('admin::app.quotes.index.datagrid.subtotal'),
            'type'     => 'string',
            'sortable' => true,
            'closure'  => function ($row) {
                return core()->formatBasePrice($row->sub_total, 2);
            },
        ]);

        $this->addColumn([
            'index'    => 'discount_amount',
            'label'    => trans('admin::app.quotes.index.datagrid.discount'),
            'type'     => 'string',
            'sortable' => true,
            'closure'  => function ($row) {
                return core()->formatBasePrice($row->discount_amount, 2);
            },
        ]);

        $this->addColumn([
            'index'    => 'tax_amount',
            'label'    => trans('admin::app.quotes.index.datagrid.tax'),
            'type'     => 'string',
            'sortable' => true,
            'closure'  => function ($row) {
                return core()->formatBasePrice($row->tax_amount, 2);
            },
        ]);

        $this->addColumn([
            'index'    => 'adjustment_amount',
            'label'    => trans('admin::app.quotes.index.datagrid.adjustment'),
            'type'     => 'string',
            'sortable' => true,
            'closure'  => function ($row) {
                return core()->formatBasePrice($row->adjustment_amount, 2);
            },
        ]);

        $this->addColumn([
            'index'    => 'grand_total',
            'label'    => trans('admin::app.quotes.index.datagrid.grand-total'),
            'type'     => 'string',
            'sortable' => true,
            'closure'  => function ($row) {
                return core()->formatBasePrice($row->grand_total, 2);
            },
        ]);

        $this->addColumn([
            'index'      => 'expired_at',
            'label'      => trans('admin::app.quotes.index.datagrid.expired-at'),
            'type'       => 'date',
            'searchable' => false,
            'sortable'   => true,
            'closure'    => function ($row) {
                return core()->formatDate($row->expired_at, 'd M Y');
            },
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('admin::app.quotes.index.datagrid.created-at'),
            'type'       => 'date',
            'searchable' => false,
            'sortable'   => true,
            'closure'    => function ($row) {
                return core()->formatDate($row->created_at);
            },
        ]);
    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        $this->addAction([
            'index'  => 'edit',
            'icon'   => 'icon-edit',
            'title'  => trans('admin::app.quotes.index.datagrid.edit'),
            'method' => 'GET',
            'url'    => fn ($row) => route('admin.quotes.edit', $row->id),
        ]);

        $this->addAction([
            'index'  => 'print',
            'icon'   => 'icon-edit',
            'title'  => trans('admin::app.quotes.index.datagrid.print'),
            'method' => 'GET',
            'url'    => fn ($row) => route('admin.quotes.print', $row->id),
        ]);

        $this->addAction([
            'index'  => 'delete',
            'icon'   => 'icon-delete',
            'title'  => trans('admin::app.quotes.index.datagrid.delete'),
            'method' => 'DELETE',
            'url'    => fn ($row) => route('admin.quotes.delete', $row->id),
        ]);
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        $this->addMassAction([
            'icon'   => 'icon-delete',
            'title'  => trans('admin::app.quotes.index.datagrid.delete'),
            'method' => 'POST',
            'url'    => route('admin.quotes.mass_delete'),
        ]);

        $this->addMassAction([
            'icon'   => 'icon-delete',
            'title'  => trans('admin::app.quotes.index.datagrid.delete'),
            'method' => 'POST',
            'url'    => route('admin.quotes.mass_delete'),
        ]);
    }
}
