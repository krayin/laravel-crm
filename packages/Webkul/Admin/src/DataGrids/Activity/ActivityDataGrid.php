<?php

namespace Webkul\Admin\DataGrids\Activity;

use Illuminate\Support\Facades\DB;
use Webkul\Admin\Traits\ProvideDropdownOptions;
use Webkul\UI\DataGrid\DataGrid;
use Webkul\User\Repositories\UserRepository;

class ActivityDataGrid extends DataGrid
{
    use ProvideDropdownOptions;

    /**
     * User repository instance.
     *
     * @var \Webkul\User\Repositories\UserRepository
     */
    protected $userRepository;

    /**
     * Create data grid instance.
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
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('activities')
            ->distinct()
            ->select(
                'activities.*',
                'leads.id as lead_id',
                'leads.title as lead_title',
                'leads.lead_pipeline_id',
                'users.id as created_by_id',
                'users.name as created_by',
            )
            ->leftJoin('activity_participants', 'activities.id', '=', 'activity_participants.activity_id')
            ->leftJoin('lead_activities', 'activities.id', '=', 'lead_activities.activity_id')
            ->leftJoin('leads', 'lead_activities.lead_id', '=', 'leads.id')
            ->leftJoin('users', 'activities.user_id', '=', 'users.id')
            ->whereIn('type', ['call', 'meeting', 'lunch']);

        $currentUser = auth()->guard('user')->user();

        if ($currentUser->view_permission != 'global') {
            if ($currentUser->view_permission == 'group') {
                $queryBuilder->where(function ($query) use ($currentUser) {
                    $userIds = $this->userRepository->getCurrentUserGroupsUserIds();

                    $query->whereIn('activities.user_id', $userIds)
                        ->orWhereIn('activity_participants.user_id', $userIds);

                    return $query;
                });
            } else {
                $queryBuilder->where(function ($query) use ($currentUser) {
                    $query->where('activities.user_id', $currentUser->id)
                        ->orWhere('activity_participants.user_id', $currentUser->id);

                    return $query;
                });
            }
        }

        $this->addFilter('id', 'activities.id');
        $this->addFilter('title', 'activities.title');
        $this->addFilter('schedule_from', 'activities.schedule_from');
        $this->addFilter('created_by', 'users.name');
        $this->addFilter('created_by_id', 'activities.user_id');
        $this->addFilter('created_at', 'activities.created_at');
        $this->addFilter('lead_title', 'leads.title');

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
            'index'            => 'is_done',
            'label'            => trans('admin::app.datagrid.is_done'),
            'type'             => 'dropdown',
            'dropdown_options' => $this->getBooleanDropdownOptions('yes_no'),
            'searchable'       => false,
            'closure'          => function ($row) {
                return view('admin::activities.datagrid.is-done', compact('row'))->render();
            },
        ]);

        $this->addColumn([
            'index' => 'title',
            'label' => trans('admin::app.datagrid.title'),
            'type'  => 'string',
        ]);

        $this->addColumn([
            'index'            => 'created_by_id',
            'label'            => trans('admin::app.datagrid.created_by'),
            'type'             => 'dropdown',
            'dropdown_options' => $this->getUserDropdownOptions(),
            'searchable'       => false,
            'sortable'         => true,
            'closure'          => function ($row) {
                $route = urldecode(route('admin.settings.users.index', ['id[eq]' => $row->created_by_id]));

                return "<a href='" . $route . "'>" . $row->created_by . "</a>";
            },
        ]);

        $this->addColumn([
            'index'   => 'comment',
            'label'   => trans('admin::app.datagrid.comment'),
            'type'    => 'string',
            'closure' => function ($row) {
                return $row->comment;
            },
        ]);

        $this->addColumn([
            'index'      => 'lead_title',
            'label'      => trans('admin::app.datagrid.lead'),
            'type'       => 'string',
            'searchable' => false,
            'closure'    => function ($row) {
                $route = urldecode(route('admin.leads.index', ['pipeline_id' => $row->lead_pipeline_id, 'view_type' => 'table', 'id[eq]' => $row->lead_id]));

                return "<a href='" . $route . "'>" . $row->lead_title . "</a>";
            },
        ]);

        $this->addColumn([
            'index'      => 'type',
            'label'      => trans('admin::app.datagrid.type'),
            'type'       => 'dropdown',
            'dropdown_options' => $this->getActivityTypeDropdownOptions(),
            'searchable' => false,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index'      => 'schedule_from',
            'label'      => trans('admin::app.datagrid.schedule_from'),
            'type'       => 'date_range',
            'searchable' => false,
            'sortable'   => true,
            'closure'    => function ($row) {
                return core()->formatDate($row->schedule_from);
            },
        ]);

        $this->addColumn([
            'index'      => 'schedule_to',
            'label'      => trans('admin::app.datagrid.schedule_to'),
            'type'       => 'date_range',
            'searchable' => false,
            'sortable'   => true,
            'closure'    => function ($row) {
                return core()->formatDate($row->schedule_to);
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
     * Prepare tab filters.
     *
     * @return array
     */
    public function prepareTabFilters()
    {
        $this->addTabFilter([
            'key'       => 'type',
            'type'      => 'pill',
            'condition' => 'eq',
            'values'    => [
                [
                    'name'     => 'admin::app.leads.all',
                    'isActive' => true,
                    'key'      => 'all',
                ], [
                    'name'     => 'admin::app.leads.call',
                    'isActive' => false,
                    'key'      => 'call',
                ], [
                    'name'     => 'admin::app.leads.meeting',
                    'isActive' => false,
                    'key'      => 'meeting',
                ], [
                    'name'     => 'admin::app.leads.lunch',
                    'isActive' => false,
                    'key'      => 'lunch',
                ]
            ]
        ]);

        $this->addTabFilter([
            'key'       => 'scheduled',
            'type'      => 'group',
            'condition' => 'eq',
            'values'    => [
                [
                    'name'     => 'admin::app.datagrid.filters.yesterday',
                    'isActive' => false,
                    'key'      => 'yesterday',
                ], [
                    'name'     => 'admin::app.datagrid.filters.today',
                    'isActive' => false,
                    'key'      => 'today',
                ], [
                    'name'     => 'admin::app.datagrid.filters.tomorrow',
                    'isActive' => false,
                    'key'      => 'tomorrow',
                ], [
                    'name'     => 'admin::app.datagrid.filters.this-week',
                    'isActive' => false,
                    'key'      => 'this_week',
                ], [
                    'name'     => 'admin::app.datagrid.filters.this-month',
                    'isActive' => false,
                    'key'      => 'this_month',
                ], [
                    'name'     => 'admin::app.datagrid.filters.custom',
                    'isActive' => false,
                    'key'      => 'custom',
                ]
            ]
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
            'route'  => 'admin.activities.edit',
            'icon'   => 'pencil-icon',
        ]);

        $this->addAction([
            'title'        => trans('ui::app.datagrid.delete'),
            'method'       => 'DELETE',
            'route'        => 'admin.activities.delete',
            'confirm_text' => trans('ui::app.datagrid.massaction.delete'),
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
            'type'    => 'update',
            'label'   => trans('ui::app.datagrid.is-done'),
            'action'  => route('admin.activities.mass_update'),
            'method'  => 'PUT',
            'options' => [
                trans('admin::app.datagrid.yes') => 1,
                trans('admin::app.datagrid.no')  => 0,
            ],
        ]);

        $this->addMassAction([
            'type'   => 'delete',
            'label'  => trans('ui::app.datagrid.delete'),
            'action' => route('admin.activities.mass_delete'),
            'method' => 'PUT',
        ]);
    }
}
