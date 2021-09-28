<?php

namespace Webkul\Admin\DataGrids\Activity;

use Illuminate\Support\Facades\DB;
use Webkul\UI\DataGrid\DataGrid;

class ActivityDataGrid extends DataGrid
{
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
                    $userIds = app('\Webkul\User\Repositories\UserRepository')->getCurrentUserGroupsUserIds();

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
            'index'      => 'title',
            'label'      => trans('admin::app.datagrid.title'),
            'type'       => 'string',
            'searchable' => true,
        ]);

        $this->addColumn([
            'index'            => 'created_by',
            'label'            => trans('admin::app.datagrid.created_by'),
            'type'             => 'dropdown',
            'dropdown_options' => app('\Webkul\User\Repositories\UserRepository')->get(['id as value', 'name as label'])->toArray(),
            'searchable'       => false,
            'sortable'         => true,
        ]);

        $this->addColumn([
            'index'      => 'comment',
            'label'      => trans('admin::app.datagrid.comment'),
            'type'       => 'string',
            'searchable' => true,
        ]);

        $this->addColumn([
            'index'      => 'lead_title',
            'label'      => trans('admin::app.datagrid.lead'),
            'type'       => 'string',
            'searchable' => false,
            'closure'    => function ($row) {
                $route = urldecode(route('admin.leads.index', ['view_type' => 'table', 'id[eq]' => $row->lead_id]));

                return "<a href='" . $route . "'>" . $row->lead_title . "</a>";
            },
        ]);

        $this->addColumn([
            'index'      => 'type',
            'label'      => trans('admin::app.datagrid.type'),
            'type'       => 'boolean',
            'searchable' => false,
        ]);

        $this->addColumn([
            'index'              => 'is_done',
            'label'              => trans('admin::app.datagrid.is_done'),
            'type'               => 'dropdown',
            'dropdown_options' => [
                [
                    'value' => 0,
                    'label' => __("admin::app.common.no"),
                ], [
                    'value' => 1,
                    'label' => __("admin::app.common.yes"),
                ]
            ],
            'searchable'         => false,
            'closure'            => function ($row) {
                if ($row->is_done) {
                    return '<span class="badge badge-round badge-success"></span>' . __("admin::app.common.yes");
                } else {
                    return '<span class="badge badge-round badge-danger"></span>' . __("admin::app.common.no");
                }
            },
        ]);

        $this->addColumn([
            'index'      => 'created_by',
            'label'      => trans('admin::app.datagrid.created_by'),
            'type'       => 'string',
            'searchable' => false,
            'closure'    => function ($row) {
                $route = urldecode(route('admin.settings.users.index', ['id[eq]' => $row->created_by_id]));

                return "<a href='" . $route . "'>" . $row->created_by . "</a>";
            },
        ]);

        $this->addColumn([
            'index'           => 'schedule_from',
            'label'           => trans('admin::app.datagrid.schedule_from'),
            'type'            => 'string',
            'searchable'      => false,
            'sortable'        => true,
            'closure'         => function ($row) {
                return core()->formatDate($row->schedule_from);
            },
        ]);

        $this->addColumn([
            'index'           => 'schedule_to',
            'label'           => trans('admin::app.datagrid.schedule_to'),
            'type'            => 'string',
            'searchable'      => false,
            'sortable'        => true,
            'closure'         => function ($row) {
                return core()->formatDate($row->schedule_to);
            },
        ]);

        $this->addColumn([
            'index'           => 'date_range',
            'label'           => trans('admin::app.datagrid.created_at'),
            'type'            => 'string',
            'searchable'      => false,
            'sortable'        => true,
            'closure'         => function ($row) {
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
            'type'      => 'pill',
            'key'       => 'type',
            'condition' => 'eq',
            'values'    => [
                [
                    'name'      => 'admin::app.leads.all',
                    'isActive'  => true,
                    'key'       => 'all',
                ], [
                    'name'      => 'admin::app.leads.call',
                    'isActive'  => false,
                    'key'       => 'call',
                ], [
                    'name'      => 'admin::app.leads.meeting',
                    'isActive'  => false,
                    'key'       => 'meeting',
                ], [
                    'name'      => 'admin::app.leads.lunch',
                    'isActive'  => false,
                    'key'       => 'lunch',
                ]
            ]
        ]);

        $this->addTabFilter([
            'type'      => 'group',
            'key'       => 'scheduled',
            'condition' => 'eq',
            'values'    => [
                [
                    'name'      => 'admin::app.datagrid.filters.yesterday',
                    'isActive'  => false,
                    'key'       => 'yesterday',
                ], [
                    'name'      => 'admin::app.datagrid.filters.today',
                    'isActive'  => false,
                    'key'       => 'today',
                ], [
                    'name'      => 'admin::app.datagrid.filters.tomorrow',
                    'isActive'  => false,
                    'key'       => 'tomorrow',
                ], [
                    'name'      => 'admin::app.datagrid.filters.this-week',
                    'isActive'  => false,
                    'key'       => 'this_week',
                ], [
                    'name'      => 'admin::app.datagrid.filters.this-month',
                    'isActive'  => true,
                    'key'       => 'this_month',
                ], [
                    'name'      => 'admin::app.datagrid.filters.custom',
                    'isActive'  => false,
                    'key'       => 'custom',
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
            'type'   => 'delete',
            'label'  => trans('ui::app.datagrid.delete'),
            'action' => route('admin.activities.mass_delete'),
            'method' => 'PUT',
        ]);
    }
}
