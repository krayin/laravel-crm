<?php

namespace Webkul\Admin\DataGrids\Activity;

use Webkul\UI\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;

class ActivityDataGrid extends DataGrid
{
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('lead_activities')
            ->addSelect('lead_activities.*');

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
                return core()->formatDate($row->scheduled_from);
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
}
