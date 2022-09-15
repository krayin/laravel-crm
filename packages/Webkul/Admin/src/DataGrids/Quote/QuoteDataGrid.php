<?php

namespace Webkul\Admin\DataGrids\Quote;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Webkul\Admin\Traits\ProvideDropdownOptions;
use Webkul\UI\DataGrid\DataGrid;
use Webkul\User\Repositories\UserRepository;

class QuoteDataGrid extends DataGrid
{
    use ProvideDropdownOptions;

    /**
     * User repository instance.
     *
     * @var \Webkul\User\Repositories\UserRepository
     */
    protected $userRepository;

    /**
     * Create datagrid instance.
     *
     * @param \Webkul\User\Repositories\UserRepository  $userRepository
     * @return void
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

        parent::__construct();
    }

    /**
     * Place your datagrid extra settings here.
     *
     * @return void
     */
    public function init()
    {
        $this->setRowProperties([
            'backgroundColor' => '#ffd0d6',
            'condition' => function ($row) {
                if (Carbon::createFromFormat('Y-m-d H:i:s',  $row->expired_at)->endOfDay() < Carbon::now()) {
                    return true;
                }

                return false;
            }
        ]);
    }

    /**
     * Prepare query builder.
     *
     * @return void
     */
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
            $this->addFilter('expired_quotes', DB::raw('DATEDIFF(NOW(), ' . DB::getTablePrefix() . 'quotes.expired_at) >= ' . DB::getTablePrefix() . 'NOW()'));
        } else {
            $this->addFilter('expired_quotes', DB::raw('DATEDIFF(NOW(), ' . DB::getTablePrefix() . 'quotes.expired_at) < ' . DB::getTablePrefix() . 'NOW()'));
        }

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
            'index'    => 'subject',
            'label'    => trans('admin::app.datagrid.subject'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'            => 'sales_person',
            'label'            => trans('admin::app.datagrid.sales-person'),
            'type'             => 'dropdown',
            'dropdown_options' => $this->getUserDropdownOptions(),
            'sortable'         => true,
            'closure'          => function ($row) {
                $route = urldecode(route('admin.settings.users.index', ['id[eq]' => $row->user_id]));

                return "<a href='" . $route . "'>" . $row->sales_person . "</a>";
            },
        ]);

        $this->addColumn([
            'index'            => 'expired_quotes',
            'label'            => trans('admin::app.datagrid.expired_quotes'),
            'type'             => 'single_dropdown',
            'dropdown_options' => $this->getBooleanDropdownOptions('yes_no'),
            'searchable'       => false,
        ]);

        $this->addColumn([
            'index'    => 'person_name',
            'label'    => trans('admin::app.datagrid.person'),
            'type'     => 'string',
            'sortable' => true,
            'closure'  => function ($row) {
                $route = urldecode(route('admin.contacts.persons.index', ['id[eq]' => $row->person_id]));

                return "<a href='" . $route . "'>" . $row->person_name . "</a>";
            },
        ]);

        $this->addColumn([
            'index'    => 'sub_total',
            'label'    => trans('admin::app.datagrid.sub-total'),
            'type'     => 'string',
            'sortable' => true,
            'closure'  => function ($row) {
                return core()->formatBasePrice($row->sub_total, 2);
            },
        ]);

        $this->addColumn([
            'index'    => 'discount_amount',
            'label'    => trans('admin::app.datagrid.discount'),
            'type'     => 'string',
            'sortable' => true,
            'closure'  => function ($row) {
                return core()->formatBasePrice($row->discount_amount, 2);
            },
        ]);

        $this->addColumn([
            'index'    => 'tax_amount',
            'label'    => trans('admin::app.datagrid.tax'),
            'type'     => 'string',
            'sortable' => true,
            'closure'  => function ($row) {
                return core()->formatBasePrice($row->tax_amount, 2);
            },
        ]);

        $this->addColumn([
            'index'    => 'adjustment_amount',
            'label'    => trans('admin::app.datagrid.adjustment'),
            'type'     => 'string',
            'sortable' => true,
            'closure'  => function ($row) {
                return core()->formatBasePrice($row->adjustment_amount, 2);
            },
        ]);

        $this->addColumn([
            'index'    => 'grand_total',
            'label'    => trans('admin::app.datagrid.grand-total'),
            'type'     => 'string',
            'sortable' => true,
            'closure'  => function ($row) {
                return core()->formatBasePrice($row->grand_total, 2);
            },
        ]);

        $this->addColumn([
            'index'      => 'expired_at',
            'label'      => trans('admin::app.leads.expired-at'),
            'type'       => 'date_range',
            'searchable' => false,
            'sortable'   => true,
            'closure'    => function ($row) {
                return core()->formatDate($row->expired_at, 'd M Y');
            },
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('admin::app.datagrid.created_at'),
            'type'       => 'date_range',
            'searchable' => false,
            'sortable'   => true,
            'closure'    => function ($row) {
                return core()->formatDate($row->created_at);
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
            'action' => route('admin.quotes.mass_delete'),
            'method' => 'PUT',
        ]);
    }
}
