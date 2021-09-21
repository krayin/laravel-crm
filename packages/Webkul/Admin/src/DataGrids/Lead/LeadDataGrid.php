<?php

namespace Webkul\Admin\DataGrids\Lead;

use Webkul\UI\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;

class LeadDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('leads')
            ->addSelect(
                'leads.id',
                'leads.title',
                'leads.status',
                'leads.lead_value',
                'leads.created_at',
                'users.id as user_id',
                'users.name as user_name',
                'lead_stages.name as stage'
            )
            ->leftJoin('users', 'leads.user_id', '=', 'users.id')
            ->leftJoin('lead_types', 'leads.lead_type_id', '=', 'lead_types.id')
            ->leftJoin('lead_stages', 'leads.lead_stage_id', '=', 'lead_stages.id')
            ->leftJoin('lead_sources', 'leads.lead_source_id', '=', 'lead_sources.id')
            ->leftJoin('lead_pipelines', 'leads.lead_pipeline_id', '=', 'lead_pipelines.id');

        $currentUser = auth()->guard('user')->user();

        if ($currentUser->view_permission != 'global') {
            if ($currentUser->view_permission == 'group') {
                $queryBuilder->whereIn('leads.user_id', app('\Webkul\User\Repositories\UserRepository')->getCurrentUserGroupsUserIds());
            } else {
                $queryBuilder->where('leads.user_id', $currentUser->id);
            }
        }

        $this->addFilter('id', 'leads.id');
        $this->addFilter('user', 'leads.user_id');
        $this->addFilter('type', 'lead_stages.code');
        $this->addFilter('stage', 'leads.lead_stage_id');
        $this->addFilter('created_at', 'leads.created_at');

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
            'index'      => 'id',
            'label'      => 'ID',
            'type'       => 'hidden',
            'searchable' => true,
        ]);

        $this->addColumn([
            'index'              => 'user',
            'label'              => trans('admin::app.datagrid.user'),
            'type'               => 'hidden',
            'sortable'           => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => app('\Webkul\User\Repositories\UserRepository')->get(['id as value', 'name as label'])->toArray(),
        ]);

        $this->addColumn([
            'index'      => 'title',
            'label'      => trans('admin::app.datagrid.subject'),
            'type'       => 'string',
            'searchable' => true,
        ]);

        $this->addColumn([
            'index'           => 'lead_value',
            'label'           => trans('admin::app.datagrid.lead_value'),
            'type'            => 'string',
            'searchable'      => true,
            'sortable'        => true,
            'filterable_type' => 'add',
            'wrapper'         => function ($row) {
                return core()->formatBasePrice($row->lead_value, 2);
            },
        ]);

        $this->addColumn([
            'index'   => 'user_name',
            'label'   => trans('admin::app.datagrid.contact_person'),
            'type'    => 'string',
            'closure' => true,
            'wrapper' => function ($row) {
                $route = urldecode(route('admin.contacts.persons.index', ['id[eq]' => $row->user_id]));

                return "<a href='" . $route . "'>" . $row->user_name . "</a>";
            },
        ]);

        $this->addColumn([
            'index'   => 'stage',
            'label'   => trans('admin::app.datagrid.stage'),
            'type'    => 'boolean',
            'closure' => true,
            'wrapper' => function ($row) {
                if ($row->stage == "Won") {
                    $badge = 'success';
                } else if ($row->stage == "Lost") {
                    $badge = 'danger';
                } else {
                    $badge = 'primary';
                }

                return "<span class='badge badge-round badge-$badge'></span>" . $row->stage;
            },
        ]);

        $this->addColumn([
            'index'           => 'created_at',
            'label'           => trans('admin::app.datagrid.created_at'),
            'type'            => 'string',
            'sortable'        => true,
            'wrapper'         => function ($row) {
                return core()->formatDate($row->created_at);
            },
            'filterable_type' => 'date_range',
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
            'type'              => 'pill',
            'key'               => 'type',
            'condition'         => 'eq',
            "value_type"        => "lookup",
            "repositoryClass"   => "\Webkul\Lead\Repositories\StageRepository",
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
            'route'  => 'admin.leads.view',
            'icon'   => 'eye-icon',
        ]);

        $this->addAction([
            'title'        => trans('ui::app.datagrid.delete'),
            'method'       => 'DELETE',
            'route'        => 'admin.leads.delete',
            'confirm_text' => trans('ui::app.datagrid.massaction.delete', ['resource' => trans('admin::app.contacts.persons.person')]),
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
        $stages = [];

        foreach (app("\Webkul\Lead\Repositories\StageRepository")->get(['id', 'name'])->toArray() as $stage) {
            $stages[$stage['name']] = $stage['id'];
        }

        $this->addMassAction([
            'type'   => 'delete',
            'label'  => trans('ui::app.datagrid.delete'),
            'action' => route('admin.leads.mass_delete'),
            'method' => 'PUT',
        ]);

        $this->addMassAction([
            'type'    => 'update',
            'label'   => trans('admin::app.datagrid.update_stage'),
            'action'  => route('admin.leads.mass_update'),
            'method'  => 'PUT',
            'options' => $stages,
        ]);
    }
}
