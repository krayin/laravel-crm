<?php

namespace Webkul\Admin\DataGrids\Lead;

use Illuminate\Support\Facades\DB;
use Webkul\Lead\Repositories\PipelineRepository;
use Webkul\Lead\Repositories\StageRepository;
use Webkul\DataGrid\DataGrid;
use Webkul\User\Repositories\UserRepository;
use Webkul\Lead\Repositories\SourceRepository;
use Webkul\Lead\Repositories\TypeRepository;

class LeadDataGrid extends DataGrid
{
    /**
     * Pipeline instance.
     *
     * @var \Webkul\Contract\Repositories\Pipeline
     */
    protected $pipeline;

    /**
     * Create data grid instance.
     *
     * @return void
     */
    public function __construct(
        protected PipelineRepository $pipelineRepository,
        protected StageRepository $stageRepository,
        protected SourceRepository $sourceRepository,
        protected TypeRepository $typeRepository,
        protected UserRepository $userRepository
    ) {
        if (request('pipeline_id')) {
            $this->pipeline = $this->pipelineRepository->find(request('pipeline_id'));
        } else {
            $this->pipeline = $this->pipelineRepository->getDefaultPipeline();
        }
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
                DB::raw('CASE WHEN DATEDIFF(NOW(),'.DB::getTablePrefix().'leads.created_at) >='.DB::getTablePrefix().'lead_pipelines.rotten_days THEN 1 ELSE 0 END as rotten_lead'),
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
            $queryBuilder->havingRaw(DB::getTablePrefix().'rotten_lead = '.request()->input('rotten_lead.in'));
        }

        $this->addFilter('id', 'leads.id');
        $this->addFilter('user', 'leads.user_id');
        $this->addFilter('sales_person', 'leads.user_id');
        $this->addFilter('lead_source_name', 'lead_sources.id');
        $this->addFilter('person_name', 'persons.name');
        $this->addFilter('type', 'lead_pipeline_stages.code');
        $this->addFilter('stage', 'lead_pipeline_stages.id');
        $this->addFilter('tag_name', 'tags.name');
        $this->addFilter('expected_close_date', 'leads.expected_close_date');
        $this->addFilter('created_at', 'leads.created_at');
        $this->addFilter('rotten_lead', DB::raw('DATEDIFF(NOW(), '.DB::getTablePrefix().'leads.created_at) >= '.DB::getTablePrefix().'lead_pipelines.rotten_days'));

        return $queryBuilder;
    }

    /**
     * Add columns.
     *
     * @return void
     */
    public function prepareColumns()
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('admin::app.leads.index.datagrid.id'),
            'type'       => 'integer',
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'              => 'sales_person',
            'label'              => trans('admin::app.leads.index.datagrid.sales-person'),
            'type'               => 'string',
            'searchable'         => false,
            'sortable'           => true,
            'filterable'         => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => $this->userRepository->all(['name as label', 'id as value'])->toArray(),
        ]);

        $this->addColumn([
            'index'      => 'title',
            'label'      => trans('admin::app.leads.index.datagrid.subject'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'              => 'lead_source_name',
            'label'              => trans('admin::app.leads.index.datagrid.source'),
            'type'               => 'string',
            'searchable'         => false,
            'sortable'           => true,
            'filterable'         => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => $this->sourceRepository->all(['name as label', 'id as value'])->toArray(),
        ]);

        $this->addColumn([
            'index'    => 'lead_value',
            'label'    => trans('admin::app.leads.index.datagrid.lead-value'),
            'type'     => 'string',
            'sortable' => true,
            'closure'  => function ($row) {
                return core()->formatBasePrice($row->lead_value, 2);
            },
        ]);

        $this->addColumn([
            'index'      => 'person_name',
            'label'      => trans('admin::app.leads.index.datagrid.contact-person'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'              => 'stage',
            'label'              => trans('admin::app.leads.index.datagrid.stage'),
            'type'               => 'string',
            'searchable'         => false,
            'sortable'           => true,
            'filterable'         => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => $this->pipeline->stages->pluck('name', 'id')
                ->map(function ($name, $id) {
                    return ['value' => $id, 'label' => $name];
                })
                ->values()
                ->all(),
        ]);

        $this->addColumn([
            'index'      => 'rotten_lead',
            'label'      => trans('admin::app.leads.index.datagrid.rotten-lead'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => false,
            'closure'    => function ($row) {
                if (! $row->rotten_lead) {
                    return trans('admin::app.leads.index.datagrid.no');
                }

                if (in_array($row->stage_code, ['won', 'lost'])) {
                    return trans('admin::app.leads.index.datagrid.no');
                }

                return trans('admin::app.leads.index.datagrid.yes');
            },
        ]);

        $this->addColumn([
            'index'           => 'expected_close_date',
            'label'           => trans('admin::app.leads.index.datagrid.expected-close-date'),
            'type'            => 'date',
            'searchable'      => false,
            'sortable'        => true,
            'filterable'      => true,
            'filterable_type' => 'date_range',
            'closure'         => function ($row) {
                if (! $row->expected_close_date) {
                    return '--';
                }

                return $row->expected_close_date;
            },
        ]);

        $this->addColumn([
            'index'           => 'created_at',
            'label'           => trans('admin::app.leads.index.datagrid.created-at'),
            'type'            => 'date',
            'searchable'      => false,
            'sortable'        => true,
            'filterable'      => true,
            'filterable_type' => 'date_range',
        ]);
    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        if (bouncer()->hasPermission('leads.view')) {
            $this->addAction([
                'icon'   => 'icon-eye',
                'title'  => trans('admin::app.leads.index.datagrid.view'),
                'method' => 'GET',
                'url'    => function ($row) {
                    return route('admin.leads.view', $row->id);
                },
            ]);
        }

        if (bouncer()->hasPermission('leads.delete')) {
            $this->addAction([
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.leads.index.datagrid.delete'),
                'method' => 'POST',
                'url'    => function ($row) {
                    return route('admin.leads.delete', $row->id);
                },
            ]);
        }
    }

    /**
     * Prepare mass actions.
     *
     * @return void
     */
    public function prepareMassActions()
    {
        // $stages = [];

        // foreach ($this->pipeline->stages->toArray() as $stage) {
        //     $stages[$stage['name']] = $stage['id'];
        // }

        // $this->addMassAction([
        //     'type'   => 'delete',
        //     'label'  => trans('ui::app.datagrid.delete'),
        //     'action' => route('admin.leads.mass_delete'),
        //     'method' => 'PUT',
        // ]);

        // $this->addMassAction([
        //     'type'    => 'update',
        //     'label'   => trans('admin::app.leads.index.datagrid.update_stage'),
        //     'action'  => route('admin.leads.mass_update'),
        //     'method'  => 'PUT',
        //     'options' => $stages,
        // ]);
    }
}
