<?php

namespace Webkul\Admin\DataGrids\Activity;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\Admin\Traits\ProvideDropdownOptions;
use Webkul\DataGrid\DataGrid;
use Webkul\User\Repositories\UserRepository;

class ActivityDataGrid extends DataGrid
{
    use ProvideDropdownOptions;

    /**
     * Create class instance.
     *
     * @return void
     */
    public function __construct(protected UserRepository $userRepository) {}

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
            ->whereIn('type', ['call', 'meeting', 'lunch']);

        $currentUser = auth()->guard('user')->user();

        if ($currentUser->view_permission != 'global') {
            if ($currentUser->view_permission == 'group') {
                $queryBuilder->where(function ($query) {
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

        return $queryBuilder;
    }

    /**
     * Prepare columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('ID'),
            'type'       => 'integer',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'            => 'is_done',
            'label'            => trans('admin::app.datagrid.is_done'),
            'type'             => 'string',
            'dropdown_options' => $this->getBooleanDropdownOptions('yes_no'),
            'searchable'       => false,
            'closure'          => function ($row) {
                return "
                    <label for='is_done_{$row->id}'>
                        <input
                            name='is_done'
                            type='checkbox'
                            id='is_done_{$row->id}'
                            value='1'
                            ".($row->is_done ? 'checked' : '')."
                            onchange='updateStatus(event, \"".route('admin.activities.update', $row->id)."\")'
                            class='peer hidden'
                        >
                        <span class='icon-checkbox-outline peer-checked:icon-checkbox-select cursor-pointer rounded-md text-2xl peer-checked:text-brandColor'></span>
                    </label>
                ";
            },
        ]);

        $this->addColumn([
            'index'      => 'title',
            'label'      => trans('Title'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'created_by_id',
            'label'      => trans('admin::app.datagrid.created_by'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'closure'    => function ($row) {
                $route = urldecode(route('admin.settings.users.index', ['id[eq]' => $row->created_by_id]));

                return "<a class='text-brandColor hover:underline' href='".$route."'>".$row->created_by.'</a>';
            },
        ]);

        $this->addColumn([
            'index'   => 'comment',
            'label'   => trans('admin::app.datagrid.comment'),
            'type'    => 'string',
        ]);

        $this->addColumn([
            'index'      => 'lead_title',
            'label'      => trans('admin::app.datagrid.lead'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
            'closure'    => function ($row) {
                if ($row->lead_title == null) {
                    return 'N/A';
                }

                $route = urldecode(route('admin.leads.index', ['pipeline_id' => $row->lead_pipeline_id, 'view_type' => 'table', 'id[eq]' => $row->lead_id]));

                return "<a class='text-brandColor hover:underline' href='".$route."'>".$row->lead_title.'</a>';
            },
        ]);

        $this->addColumn([
            'index'      => 'type',
            'label'      => trans('admin::app.datagrid.type'),
            'type'       => 'string',
            'searchable' => false,
            'filterable' => false,
            'closure'    => fn ($row) => trans('admin::app.activities.'.$row->type),
        ]);

        $this->addColumn([
            'index'      => 'schedule_from',
            'label'      => trans('admin::app.datagrid.schedule_from'),
            'type'       => 'date',
            'searchable' => false,
            'sortable'   => true,
            'closure'    => fn ($row) => core()->formatDate($row->schedule_from),
        ]);

        $this->addColumn([
            'index'      => 'schedule_to',
            'label'      => trans('admin::app.datagrid.schedule_to'),
            'type'       => 'date',
            'searchable' => false,
            'sortable'   => true,
            'closure'    => fn ($row) => core()->formatDate($row->schedule_to),
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('admin::app.datagrid.created_at'),
            'type'       => 'date',
            'searchable' => false,
            'sortable'   => true,
            'closure'    => fn ($row) => core()->formatDate($row->created_at),
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
            'title'  => trans('ui::app.datagrid.edit'),
            'method' => 'GET',
            'url'    => fn ($row) => route('admin.activities.edit', $row->id),
        ]);

        $this->addAction([
            'index'  => 'delete',
            'icon'   => 'icon-delete',
            'title'  => trans('admin::app.settings.groups.index.datagrid.delete'),
            'method' => 'DELETE',
            'url'    => fn ($row) => route('admin.activities.delete', $row->id),
        ]);
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {

        $this->addMassAction([
            'title'  => trans('Delete'),
            'url'    => route('admin.activities.mass_delete'),
            'method' => 'POST',
        ]);

        $this->addMassAction([
            'title'   => trans('Mass Update'),
            'url'     => route('admin.activities.mass_update'),
            'method'  => 'POST',
            'options' => [
                [
                    'label' => trans('admin::app.catalog.products.index.datagrid.active'),
                    'value' => 1,
                ],
                [
                    'label' => trans('admin::app.catalog.products.index.datagrid.disable'),
                    'value' => 0,
                ],
            ],
        ]);
    }
}
