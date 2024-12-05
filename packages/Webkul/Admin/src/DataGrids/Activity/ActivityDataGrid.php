<?php

namespace Webkul\Admin\DataGrids\Activity;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\Admin\Traits\ProvideDropdownOptions;
use Webkul\DataGrid\DataGrid;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\User\Repositories\UserRepository;

class ActivityDataGrid extends DataGrid
{
    use ProvideDropdownOptions;

    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
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
            ->whereIn('type', ['call', 'meeting', 'lunch'])
            ->where(function ($query) {
                if ($userIds = bouncer()->getAuthorizedUserIds()) {
                    $query->whereIn('activities.user_id', $userIds)
                        ->orWhereIn('activity_participants.user_id', $userIds);
                }
            })->groupBy('activities.id', 'leads.id', 'users.id');

        $this->addFilter('id', 'activities.id');
        $this->addFilter('title', 'activities.title');
        $this->addFilter('schedule_from', 'activities.schedule_from');
        $this->addFilter('created_by', 'users.name');
        $this->addFilter('created_by_id', 'users.name');
        $this->addFilter('created_at', 'activities.created_at');
        $this->addFilter('lead_title', 'leads.title');

        return $queryBuilder;
    }

    /**
     * Prepare columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('admin::app.activities.index.datagrid.id'),
            'type'       => 'integer',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'            => 'is_done',
            'label'            => trans('admin::app.activities.index.datagrid.is_done'),
            'type'             => 'string',
            'dropdown_options' => $this->getBooleanDropdownOptions('yes_no'),
            'searchable'       => false,
            'closure'          => fn ($row) => view('admin::activities.datagrid.is-done', compact('row'))->render(),
        ]);

        $this->addColumn([
            'index'      => 'title',
            'label'      => trans('admin::app.activities.index.datagrid.title'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'              => 'created_by_id',
            'label'              => trans('admin::app.activities.index.datagrid.created_by'),
            'type'               => 'string',
            'searchable'         => false,
            'sortable'           => true,
            'filterable'         => true,
            'filterable_type'    => 'searchable_dropdown',
            'filterable_options' => [
                'repository' => UserRepository::class,
                'column'     => [
                    'label' => 'name',
                    'value' => 'name',
                ],
            ],
            'closure'    => function ($row) {
                $route = urldecode(route('admin.settings.users.index', ['id[eq]' => $row->created_by_id]));

                return "<a class='text-brandColor hover:underline' href='".$route."'>".$row->created_by.'</a>';
            },
        ]);

        $this->addColumn([
            'index'   => 'comment',
            'label'   => trans('admin::app.activities.index.datagrid.comment'),
            'type'    => 'string',
        ]);

        $this->addColumn([
            'index'              => 'lead_title',
            'label'              => trans('admin::app.activities.index.datagrid.lead'),
            'type'               => 'string',
            'searchable'         => true,
            'sortable'           => true,
            'filterable'         => true,
            'filterable_type'    => 'searchable_dropdown',
            'filterable_options' => [
                'repository' => LeadRepository::class,
                'column'     => [
                    'label' => 'title',
                    'value' => 'title',
                ],
            ],
            'closure'    => function ($row) {
                if ($row->lead_title == null) {
                    return "<span class='text-gray-800 dark:text-gray-300'>N/A</span>";
                }

                $route = urldecode(route('admin.leads.index', ['pipeline_id' => $row->lead_pipeline_id, 'view_type' => 'table', 'id[eq]' => $row->lead_id]));

                return "<a class='text-brandColor hover:underline' href='".$route."'>".$row->lead_title.'</a>';
            },
        ]);

        $this->addColumn([
            'index'      => 'type',
            'label'      => trans('admin::app.activities.index.datagrid.type'),
            'type'       => 'string',
            'searchable' => false,
            'filterable' => false,
            'sortable'   => true,
            'closure'    => fn ($row) => trans('admin::app.activities.index.datagrid.'.$row->type),
        ]);

        $this->addColumn([
            'index'      => 'schedule_from',
            'label'      => trans('admin::app.activities.index.datagrid.schedule_from'),
            'type'       => 'date',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
            'closure'    => fn ($row) => core()->formatDate($row->schedule_from),
        ]);

        $this->addColumn([
            'index'      => 'schedule_to',
            'label'      => trans('admin::app.activities.index.datagrid.schedule_to'),
            'type'       => 'date',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
            'closure'    => fn ($row) => core()->formatDate($row->schedule_to),
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('admin::app.activities.index.datagrid.created_at'),
            'type'       => 'date',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
            'closure'    => fn ($row) => core()->formatDate($row->created_at),
        ]);
    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        if (bouncer()->hasPermission('activities.edit')) {
            $this->addAction([
                'index'  => 'edit',
                'icon'   => 'icon-edit',
                'title'  => trans('admin::app.activities.index.datagrid.edit'),
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.activities.edit', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('activities.delete')) {
            $this->addAction([
                'index'  => 'delete',
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.activities.index.datagrid.update'),
                'method' => 'DELETE',
                'url'    => fn ($row) => route('admin.activities.delete', $row->id),
            ]);
        }
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {

        $this->addMassAction([
            'icon'   => 'icon-delete',
            'title'  => trans('admin::app.activities.index.datagrid.mass-delete'),
            'method' => 'POST',
            'url'    => route('admin.activities.mass_delete'),
        ]);

        $this->addMassAction([
            'title'   => trans('admin::app.activities.index.datagrid.mass-update'),
            'url'     => route('admin.activities.mass_update'),
            'method'  => 'POST',
            'options' => [
                [
                    'label' => trans('admin::app.activities.index.datagrid.done'),
                    'value' => 1,
                ], [
                    'label' => trans('admin::app.activities.index.datagrid.not-done'),
                    'value' => 0,
                ],
            ],
        ]);
    }
}
