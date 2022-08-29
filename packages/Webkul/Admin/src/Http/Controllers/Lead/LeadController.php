<?php

namespace Webkul\Admin\Http\Controllers\Lead;

use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\LeadForm;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Lead\Repositories\PipelineRepository;
use Webkul\Lead\Repositories\StageRepository;

class LeadController extends Controller
{
    /**
     * Lead repository instance.
     *
     * @var \Webkul\Lead\Repositories\LeadRepository
     */
    protected $leadRepository;

    /**
     * Pipeline repository instance.
     *
     * @var \Webkul\Lead\Repositories\PipelineRepository
     */
    protected $pipelineRepository;

    /**
     * Stage repository instance.
     *
     * @var \Webkul\Lead\Repositories\StageRepository
     */
    protected $stageRepository;

    /**
     * Create a new controller instance.
     *
     * @param \Webkul\Lead\Repositories\LeadRepository  $leadRepository
     * @param \Webkul\Lead\Repositories\PipelineRepository  $pipelineRepository
     * @param \Webkul\Lead\Repositories\StageRepository  $stageRepository
     *
     * @return void
     */
    public function __construct(
        LeadRepository $leadRepository,
        PipelineRepository $pipelineRepository,
        StageRepository $stageRepository
    ) {
        $this->leadRepository = $leadRepository;

        $this->pipelineRepository = $pipelineRepository;

        $this->stageRepository = $stageRepository;

        request()->request->add(['entity_type' => 'leads']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request('pipeline_id')) {
            $pipeline = $this->pipelineRepository->find(request('pipeline_id'));
        } else {
            $pipeline = $this->pipelineRepository->getDefaultPipeline();
        }

        return view('admin::leads.index', compact('pipeline'));
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get()
    {
        if (request('view_type')) {
            return app(\Webkul\Admin\DataGrids\Lead\LeadDataGrid::class)->toJson();
        } else {
            $createdAt = request('created_at') ?? null;

            if ($createdAt && isset($createdAt["bw"])) {
                $createdAt = explode(",", $createdAt["bw"]);

                $createdAt[0] .= ' 00:01';

                $createdAt[1] = $createdAt[1]
                    ? $createdAt[1] . ' 23:59'
                    : Carbon::now()->format('Y-m-d 23:59');
            } else {
                $createdAt = null;
            }

            if (request('pipeline_id')) {
                $pipeline = $this->pipelineRepository->find(request('pipeline_id'));
            } else {
                $pipeline = $this->pipelineRepository->getDefaultPipeline();
            }

            $data = [];

            if ($stageId = request('pipeline_stage_id')) {
                $query = $this->leadRepository->getLeadsQuery($pipeline->id, $stageId, request('search') ?? '', $createdAt);

                $paginator = $query->paginate(10);
                
                $data[$stageId] = [
                    'leads' => [],
                    'pagination' => [
                        'current' => $current = $paginator->currentPage(),
                        'last' => $last = $paginator->lastPage(),
                        'next' => $current < $last ? $current + 1 : null,
                    ],
                    'total' => core()->formatBasePrice($query->getModel()->paginate(request('page') ? request('page') * 10 : 10, ['lead_value'], 'page', 1)->sum('lead_value')),
                ];

                foreach ($paginator as $lead) {
                    $data[$stageId]['leads'][] =  array_merge($lead->toArray(), [
                        'lead_value' => core()->formatBasePrice($lead->lead_value),
                    ]);
                }
            } else {
                foreach ($pipeline->stages as $stage) {
                    $query = $this->leadRepository->getLeadsQuery($pipeline->id, $stage->id, request('search') ?? '', $createdAt);

                    $paginator = $query->paginate(10);

                    $data[$stage->id] = [
                        'leads' => [],
                        'pagination' => [
                            'current' => $current = $paginator->currentPage(),
                            'last' => $last = $paginator->lastPage(),
                            'next' => $current < $last ? $current + 1 : null,
                        ],
                        'total' => core()->formatBasePrice($query->paginate(10)->sum('lead_value')),
                    ];

                    foreach ($paginator as $lead) {
                        $data[$stage->id]['leads'][] =  array_merge($lead->toArray(), [
                            'lead_value' => core()->formatBasePrice($lead->lead_value),
                        ]);
                    }
                }
            }

            return response()->json($data);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin::leads.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Webkul\Admin\Http\Requests\LeadForm $request
     * @return \Illuminate\Http\Response
     */
    public function store(LeadForm $request)
    {
        Event::dispatch('lead.create.before');

        $data = request()->all();

        $data['status'] = 1;

        if ($data['lead_pipeline_stage_id']) {
            $stage = $this->stageRepository->findOrFail($data['lead_pipeline_stage_id']);

            $data['lead_pipeline_id'] = $stage->lead_pipeline_id;
        } else {
            $pipeline = $this->pipelineRepository->getDefaultPipeline();

            $stage = $pipeline->stages()->first();

            $data['lead_pipeline_id'] = $pipeline->id;

            $data['lead_pipeline_stage_id'] = $stage->id;
        }

        if (in_array($stage->code, ['won', 'lost'])) {
            $data['closed_at'] = Carbon::now();
        }

        $lead = $this->leadRepository->create($data);

        Event::dispatch('lead.create.after', $lead);

        session()->flash('success', trans('admin::app.leads.create-success'));

        return redirect()->route('admin.leads.index', $data['lead_pipeline_id']);
    }

    /**
     * Display a resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function view($id)
    {
        $lead = $this->leadRepository->findOrFail($id);

        $currentUser = auth()->guard('user')->user();

        if ($currentUser->view_permission != 'global') {
            if ($currentUser->view_permission == 'group') {
                $userIds = app('\Webkul\User\Repositories\UserRepository')->getCurrentUserGroupsUserIds();

                if (! in_array($lead->user_id, $userIds)) {
                    return redirect()->route('admin.leads.index');
                }
            } else {
                if ($lead->user_id != $currentUser->id) {
                    return redirect()->route('admin.leads.index');
                }
            }
        }

        return view('admin::leads.view', compact('lead'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Webkul\Admin\Http\Requests\LeadForm $request
     * @param int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(LeadForm $request, $id)
    {
        Event::dispatch('lead.update.before', $id);
        $data = request()->all();

        if ($data['lead_pipeline_stage_id']) {
            $stage = $this->stageRepository->findOrFail($data['lead_pipeline_stage_id']);

            $data['lead_pipeline_id'] = $stage->lead_pipeline_id;
        } else {
            $pipeline = $this->pipelineRepository->getDefaultPipeline();

            $stage = $pipeline->stages()->first();

            $data['lead_pipeline_id'] = $pipeline->id;

            $data['lead_pipeline_stage_id'] = $stage->id;
        }

        $lead = $this->leadRepository->update($data, $id);        

        Event::dispatch('lead.update.after', $lead);

        if (request()->ajax()) {
            return response()->json([
                'message' => trans('admin::app.leads.update-success'),
            ]);
        } else {
            session()->flash('success', trans('admin::app.leads.update-success'));

            if (request()->has('closed_at')) {
                return redirect()->back();
            } else {
               return redirect()->route('admin.leads.index', $data['lead_pipeline_id']);
            }
        }
    }

    /**
     * Search person results.
     *
     * @return \Illuminate\Http\Response
     */
    public function search()
    {
        $results = $this->leadRepository->findWhere([
            ['title', 'like', '%' . urldecode(request()->input('query')) . '%']
        ]);

        return response()->json($results);
    }

    /*
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->leadRepository->findOrFail($id);

        try {
            Event::dispatch('lead.delete.before', $id);

            $this->leadRepository->delete($id);

            Event::dispatch('lead.delete.after', $id);

            return response()->json([
                'message' => trans('admin::app.response.destroy-success', ['name' => trans('admin::app.leads.lead')]),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.response.destroy-failed', ['name' => trans('admin::app.leads.lead')]),
            ], 400);
        }
    }

    /**
     * Mass Update the specified resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function massUpdate()
    {
        $data = request()->all();

        foreach ($data['rows'] as $leadId) {
            $lead = $this->leadRepository->find($leadId);

            Event::dispatch('lead.update.before', $leadId);

            $lead->update(['lead_pipeline_stage_id' => $data['value']]);

            Event::dispatch('lead.update.before', $leadId);
        }

        return response()->json([
            'message' => trans('admin::app.response.update-success', ['name' => trans('admin::app.leads.title')])
        ]);
    }

    /**
     * Mass Delete the specified resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function massDestroy()
    {
        foreach (request('rows') as $leadId) {
            Event::dispatch('lead.delete.before', $leadId);

            $this->leadRepository->delete($leadId);

            Event::dispatch('lead.delete.after', $leadId);
        }

        return response()->json([
            'message' => trans('admin::app.response.destroy-success', ['name' => trans('admin::app.leads.title')]),
        ]);
    }
}
