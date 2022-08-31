<?php

namespace Webkul\Admin\DataGrids\Lead;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Webkul\Admin\Traits\ProvideDropdownOptions;
use Webkul\Lead\Repositories\PipelineRepository;
use Webkul\Lead\Repositories\StageRepository;
use Webkul\UI\DataGrid\DataGrid;
use Webkul\User\Repositories\UserRepository;

class LeadDataGrid extends DataGrid
{
    use ProvideDropdownOptions;

    /**
     * Pipeline repository instance.
     *
     * @var \Webkul\Lead\Repositories\PipelineRepository
     */
    protected $pipelineRepository;

    /**
     * Pipeline instance.
     *
     * @var \Webkul\Contract\Repositories\Pipeline
     */
    protected $pipeline;

    /**
     * Stage repository instance.
     *
     * @var \Webkul\Lead\Repositories\StageRepository
     */
    protected $stageRepository;

    /**
     * User repository instance.
     *
     * @var \Webkul\User\Repositories\UserRepository
     */
    protected $userRepository;

    /**
     * Create data grid instance.
     *
     * @param \Webkul\Lead\Repositories\PipelineRepository  $pipelineRepository
     * @param \Webkul\Lead\Repositories\StageRepository  $stageRepository
     * @param \Webkul\User\Repositories\UserRepository  $userRepository
     * @return void
     */
    public function __construct(
        PipelineRepository $pipelineRepository,
        StageRepository $stageRepository,
        UserRepository $userRepository
    ) {
        $this->pipelineRepository = $pipelineRepository;

        if (request('pipeline_id')) {
            $this->pipeline = $this->pipelineRepository->find(request('pipeline_id'));
        } else {
            $this->pipeline = $this->pipelineRepository->getDefaultPipeline();
        }

        $this->stageRepository = $stageRepository;

        $this->userRepository = $userRepository;

        parent::__construct();

        $this->export = bouncer()->hasPermission('leads.persons.export') ? true : false;
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
                if (in_array($row->stage_code, ['won', 'lost']) || ! $row->rotten_lead) {
                    return false;
                } else {
                    return true;
                }
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
        $queryBuilder = DB::table('leads')
            ->addSelect(
                'leads.id',
                'leads.title',
                'leads.status',
                'leads.lead_value',
                'leads.expected_close_date',
                'lead_sources.name as lead_source_name',
                'leads.created_at',
                'lead_pipeline_stages.name as stage',
                'lead_tags.tag_id as tag_id',
                'users.id as user_id',
                'users.name as sales_person',
                'persons.id as person_id',
                'persons.name as person_name',
                'tags.name as tag_name',
                'lead_pipelines.rotten_days as pipeline_rotten_days',
                'lead_pipeline_stages.code as stage_code',
                DB::raw('CASE WHEN DATEDIFF(NOW(), leads.created_at) >= lead_pipelines.rotten_days THEN 1 ELSE 0 END as rotten_lead'),
            )
            ->leftJoin('users', 'leads.user_id', '=', 'users.id')
            ->leftJoin('persons', 'leads.person_id', '=', 'persons.id')
            ->leftJoin('lead_types', 'leads.lead_type_id', '=', 'lead_types.id')
            ->leftJoin('lead_pipeline_stages', 'leads.lead_pipeline_stage_id', '=', 'lead_pipeline_stages.id')
            ->leftJoin('lead_sources', 'leads.lead_source_id', '=', 'lead_sources.id')
            ->leftJoin('lead_pipelines', 'leads.lead_pipeline_id', '=', 'lead_pipelines.id')
            ->leftJoin('lead_tags', 'leads.id', '=', 'lead_tags.lead_id')
            ->leftJoin('tags', 'tags.id', '=', 'lead_tags.tag_id')
            ->groupBy('leads.id')
            ->where('leads.lead_pipeline_id', $this->pipeline->id);

        $currentUser = auth()->guard('user')->user();

        if ($currentUser->view_permission != 'global') {
            if ($currentUser->view_permission == 'group') {
                $queryBuilder->whereIn('leads.user_id', $this->userRepository->getCurrentUserGroupsUserIds());
            } else {
                $queryBuilder->where('leads.user_id', $currentUser->id);
            }
        }

        if (! is_null(request()->input('rotten_lead.in'))) {
            $queryBuilder->havingRaw('rotten_lead = ' . request()->input('rotten_lead.in'));
        }

        $this->addFilter('id', 'leads.id');
        $this->addFilter('user', 'leads.user_id');
        $this->addFilter('sales_person', 'leads.user_id');
        $this->addFilter('lead_source_name', 'lead_sources.id');
        $this->addFilter('person_name', 'persons.name');
        $this->addFilter('type', 'lead_pipeline_stages.code');
        $this->addFilter('stage', 'lead_pipeline_stages.name');
        $this->addFilter('tag_name', 'tags.name');
        $this->addFilter('expected_close_date', 'leads.expected_close_date');
        $this->addFilter('created_at', 'leads.created_at');
        $this->addFilter('rotten_lead',  DB::raw('DATEDIFF(NOW(),' . DB::getTablePrefix() . 'leads.created_at) >= ' . DB::getTablePrefix() . 'lead_pipelines.rotten_days'));

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
            'index'    => 'id',
            'label'    => trans('admin::app.datagrid.id'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'            => 'sales_person',
            'label'            => trans('admin::app.datagrid.sales-person'),
            'type'             => 'dropdown',
            'dropdown_options' => $this->getUserDropdownOptions(),
            'searchable'       => false,
            'sortable'         => true,
            'closure'          => function ($row) {
                $route = urldecode(route('admin.settings.users.index', ['id[eq]' => $row->user_id]));

                return "<a href='" . $route . "'>" . $row->sales_person . "</a>";
            },
        ]);

        $this->addColumn([
            'index'    => 'title',
            'label'    => trans('admin::app.datagrid.subject'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'tag_name',
            'label'    => trans('admin::app.datagrid.tags'),
            'type'     => 'hidden',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'            => 'lead_source_name',
            'label'            => trans('admin::app.leads.lead-source-name'),
            'type'             => 'dropdown',
            'dropdown_options' => $this->getleadSourcesOptions(),
            'searchable'       => false,
            'sortable'         => true,
        ]);

        $this->addColumn([
            'index'    => 'lead_value',
            'label'    => trans('admin::app.datagrid.lead_value'),
            'type'     => 'string',
            'sortable' => true,
            'closure'  => function ($row) {
                return core()->formatBasePrice($row->lead_value, 2);
            },
        ]);

        $this->addColumn([
            'index'      => 'person_name',
            'label'      => trans('admin::app.datagrid.contact_person'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
            'closure'    => function ($row) {
                $route = urldecode(route('admin.contacts.persons.index', ['id[eq]' => $row->person_id]));

                return "<a href='" . $route . "'>" . $row->person_name . "</a>";
            },
        ]);

        $this->addColumn([
            'index'      => 'stage',
            'label'      => trans('admin::app.datagrid.stage'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
            'filterable' => false,
            'closure'    => function ($row) {
                if ($row->stage == 'Won') {
                    $badge = 'success';
                } else if ($row->stage == 'Lost') {
                    $badge = 'danger';
                } else {
                    $badge = 'primary';
                }

                return "<span class='badge badge-round badge-{$badge}'></span>" . $row->stage;
            },
        ]);

        $this->addColumn([
            'index'             => 'rotten_lead',
            'label'             => trans('admin::app.datagrid.rotten_lead'),
            'type'              => 'dropdown',
            'dropdown_options'  => $this->getRootenLeadDropdownOptions(),
            'sortable'          => true,
            'searchable'        => false,
            'closure'           => function ($row) {
                return ! $row->rotten_lead || in_array($row->stage_code, ['won', 'lost']) ? trans('admin::app.common.no') : trans('admin::app.common.yes');
            }
        ]);

        $this->addColumn([
            'index'      => 'expected_close_date',
            'label'      => trans('admin::app.datagrid.expected_close_date'),
            'type'       => 'date_range',
            'searchable' => false,
            'sortable'   => true,
            'closure'    => function ($row) {
                if (! $row->expected_close_date) {
                    return '--';
                }

                return core()->formatDate($row->expected_close_date);
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
        $values = $this->pipeline->stages()
            ->get(['name', 'code as key', DB::raw('false as isActive')])
            ->prepend([
                'isActive' => true,
                'key'      => 'all',
                'name'     => trans('admin::app.datagrid.all'),
            ])
            ->toArray();

        $this->addTabFilter([
            'key'        => 'type',
            'type'       => 'pill',
            'condition'  => 'eq',
            'value_type' => 'lookup',
            'values'     => $values,
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

        foreach ($this->pipeline->stages->toArray() as $stage) {
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
