<?php

namespace Webkul\Admin\DataGrids\Activity;

use Carbon\Carbon;
use Webkul\UI\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;

class ActivityDataGrid extends DataGrid
{
    protected $tabFilters = [
        [
            'type'      => 'pill',
            'key'       => 'type',
            'condition' => 'eq',
            'values'    => [
                [
                    'name'      => 'All',
                    'isActive'  => true,
                    'key'       => 'all',
                ], [
                    'name'      => 'Call',
                    'isActive'  => false,
                    'key'       => 'call',
                ], [
                    'name'      => 'Mail',
                    'isActive'  => false,
                    'key'       => 'mail',
                ], [
                    'name'      => 'Meeting',
                    'isActive'  => false,
                    'key'       => 'meeting',
                ]
            ]
        ], [
            'type'      => 'group',
            'key'       => 'duration',
            'condition' => 'eq',
            'values'    => [
                [
                    'name'      => 'Yesterday',
                    'isActive'  => false,
                    'key'       => 'yesterday',
                ], [
                    'name'      => 'Today',
                    'isActive'  => false,
                    'key'       => 'today',
                ], [
                    'name'      => 'Tomorrow',
                    'isActive'  => false,
                    'key'       => 'tomorrow',
                ], [
                    'name'      => 'This week',
                    'isActive'  => false,
                    'key'       => 'this_week',
                ], [
                    'name'      => 'This month',
                    'isActive'  => true,
                    'key'       => 'this_month',
                ], [
                    'name'      => 'Custom',
                    'isActive'  => false,
                    'key'       => 'custom',
                ]
            ]
        ]
    ];

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('lead_activities');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'       => 'comment',
            'label'       => trans('admin::app.datagrid.comment'),
            'type'        => 'string',
            'searchable'  => true,
        ]);

        $this->addColumn([
            'index'              => 'type',
            'label'              => trans('admin::app.datagrid.type'),
            'type'               => 'boolean',
            'filterable_type'    => 'dropdown',
            'closure'            => function ($row) {
                return $row->type;
            },
        ]);

        $this->addColumn([
            'index'              => 'is_done',
            'label'              => trans('admin::app.datagrid.is_done'),
            'type'               => 'boolean',
            'filterable_type'    => 'dropdown',
            'filterable_options' => [[
                'value' => 0,
                'label' => __("admin::app.common.no"),
            ], [
                'value' => 1,
                'label' => __("admin::app.common.yes"),
            ]],
            'closure'            => function ($row) {
                if ($row->is_done) {
                    return '<span class="badge badge-round badge-success"></span>' . __("admin::app.common.yes");
                } else {
                    return '<span class="badge badge-round badge-danger"></span>' . __("admin::app.common.no");
                }
            },
        ]);

        $this->addColumn([
            'index'             => 'scheduled_from',
            'label'             => trans('admin::app.datagrid.schedule_from'),
            'type'              => 'string',
            'sortable'          => true,
            'filterable_type'   => 'date_range',
            'closure'           => function ($row) {
                return core()->formatDate($row->schedule_from);
            },
        ]);

        $this->addColumn([
            'index'             => 'schedule_to',
            'label'             => trans('admin::app.datagrid.schedule_to'),
            'type'              => 'string',
            'sortable'          => true,
            'filterable_type'   => 'date_range',
            'closure'           => function ($row) {
                return core()->formatDate($row->schedule_to);
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
            'title'        => trans('ui::app.datagrid.delete'),
            'method'       => 'DELETE',
            'route'        => 'admin.activities.delete',
            'confirm_text' => trans('ui::app.datagrid.massaction.delete', ['resource' => 'user']),
            'icon'         => 'icon trash-icon',
        ]);
    }

    private function applyDurationFilter($queryBuilder)
    {
        $key = "created_at";
        $duration = request()->duration['eq'] ?? "this_month";

        $endDate = Carbon::now()->format('Y-m-d');

        switch ($duration) {
            case 'yesterday':
                $queryBuilder->where(
                    $key,
                    Carbon::yesterday()
                );
                break;

            case 'today':
                $queryBuilder->where(
                    $key,
                    Carbon::today()
                );
                break;

            case 'tomorrow':
                $queryBuilder->where(
                    $key,
                    Carbon::tomorrow()
                );
                break;

            case 'this_week':
                $startDate = Carbon::now()->subDays(7)->format('Y-m-d');

                $queryBuilder->whereBetween(
                    $key,
                    [$startDate, $endDate]
                );
                break;

            case 'this_month':
                $startDate = Carbon::now()->subDays(30)->format('Y-m-d');

                $queryBuilder->whereBetween(
                    $key,
                    [$startDate, $endDate]
                );
                break;
        }

        return $queryBuilder;
    }
}
