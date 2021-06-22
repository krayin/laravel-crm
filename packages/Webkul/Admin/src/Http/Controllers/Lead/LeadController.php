<?php

namespace Webkul\Admin\Http\Controllers\Lead;

use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Lead\Repositories\FileRepository;
use Webkul\Lead\Repositories\StageRepository;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Attribute\Http\Requests\AttributeForm;

class LeadController extends Controller
{
    /**
     * LeadRepository object
     *
     * @var \Webkul\Lead\Repositories\LeadRepository
     */
    protected $leadRepository;

    /**
     * FileRepository object
     *
     * @var \Webkul\Lead\Repositories\FileRepository
     */
    protected $fileRepository;

    /**
     * StageRepository object
     *
     * @var \Webkul\Lead\Repositories\StageRepository
     */
    protected $stageRepository;

    /**
     * Create a new controller instance.
     *
     * @param \Webkul\Lead\Repositories\LeadRepository  $leadRepository
     * @param \Webkul\Lead\Repositories\FileRepository  $fileRepository
     * @param \Webkul\Lead\Repositories\StageRepository  $stageRepository
     *
     * @return void
     */
    public function __construct(
        LeadRepository $leadRepository,
        FileRepository $fileRepository,
        StageRepository $stageRepository
    ) {
        $this->leadRepository = $leadRepository;

        $this->fileRepository = $fileRepository;

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
        return view('admin::leads.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Webkul\Attribute\Http\Requests\AttributeForm $request
     * @return \Illuminate\Http\Response
     */
    public function store(AttributeForm $request)
    {
        Event::dispatch('lead.create.before');
        
        $data = request()->all();

        $data['user_id'] = $data['status'] = $data['lead_pipeline_id'] = 1;

        $lead = $this->leadRepository->create($data);

        Event::dispatch('lead.create.after', $lead);
        
        session()->flash('success', trans('admin::app.leads.create-success'));

        return redirect()->back();
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

        return view('admin::leads.view', compact('lead'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Webkul\Attribute\Http\Requests\AttributeForm $request
     * @param int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AttributeForm $request, $id)
    {
        Event::dispatch('lead.update.before');

        $lead = $this->leadRepository->update(request()->all(), $id);

        Event::dispatch('lead.update.after', $lead);

        if (request()->ajax()) {
            return response()->json([
                'status'  => true,
                'message' => trans('admin::app.leads.update-success'),
            ]);
        } else {
            session()->flash('success', trans('admin::app.leads.update-success'));

            if (request()->has('closed_at')) {
                return redirect()->back();
            } else {
                return redirect()->route('admin.leads.index');
            }
        }
    }

    /**
     * Upload files to storage
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function upload($id)
    {
        $this->validate(request(), [
            'file' => 'required',
        ]);

        Event::dispatch('leads.file.create.before');

        $file = $this->fileRepository->upload(request()->all(), $id);

        if ($file) {
            Event::dispatch('leads.file.create.after', $file);
            
            session()->flash('success', trans('admin::app.leads.file-upload-success'));
        } else {
            session()->flash('error', trans('admin::app.leads.file-upload-error'));
        }

        return redirect()->back();
    }

    /**
     * Download file from storage
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function download($id)
    {
        $file = $this->fileRepository->findOrFail($id);

        return Storage::download($file->path);
    }

    /**
     * Returns json format data of leads for kanban
     *
     * @return \Illuminate\Http\Response
     */
    public function fetchLeads()
    {
        $totalCount = [];
        $searchedKeyword = request('search') ?? '';
        $createdAt = request('created_at') ?? null;
        $currencySymbol = core()->currencySymbol(config('app.currency'));

        if ($createdAt) {
            $createdAt = explode(",", $createdAt["bw"]);

            $createdAt[0] = $createdAt[0] . ' ' . Carbon::parse('00:01')->format('H:i');
            $createdAt[1] = ($createdAt[1] ? $createdAt[1] : Carbon::now()->format('Y-m-d')) . ' ' . Carbon::parse('23:59')->format('H:i');
        }

        $leads = $this->leadRepository->getLeads($searchedKeyword, $createdAt)->toArray();
                    
        $stages = $this->stageRepository->get();

        foreach ($leads as $key => $lead) {
            $totalCount[$lead['status']] = ($totalCount[$lead['status']] ?? 0) + (float) $lead['lead_value'];

            $leads[$key]['view_url'] = route('admin.leads.view', ["id" => $lead['id']]);
        }

        $totalCount = array_map(function ($count) use ($currencySymbol) {
            return $currencySymbol . number_format($count);
        }, $totalCount);

        $stages = \Arr::pluck($stages, "name", "id");

        return response()->json([
            'blocks'          => $leads,
            'stages'          => $stages,
            'total_count'     => $totalCount,
            'currency_symbol' => $currencySymbol,
        ]);
    }

    /**
     * Update status of a lead
     *
     * @return \Illuminate\Http\Response
     */
    public function updateLeadStage()
    {
        $requestParams = request()->all();

        $stages = $this->stageRepository->findOneWhere(['name' => $requestParams['status']]);

        $this->leadRepository
            ->update([
                "lead_stage_id" => $stages->id,
                "entity_type"   => $requestParams["entity_type"],
            ], $requestParams['id']);

        return response()->json([
            'status'    => true,
            'message'   => __("admin::app.leads.lead_stage_updated"),
        ]);
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
                'status'    => true,
                'message'   => trans('admin::app.response.destroy-success', ['name' => trans('admin::app.leads.lead')]),
            ], 200);
        } catch(\Exception $exception) {
            return response()->json([
                'status'  => false,
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

            $lead->update(['lead_stage_id' => $data['value']]);
        }

        return response()->json([
            'status'  => true,
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
        $data = request()->all();

        $this->leadRepository->destroy($data['rows']);

        return response()->json([
            'status'  => true,
            'message' => trans('admin::app.response.destroy-success', ['name' => trans('admin::app.leads.title')]),
        ]);
    }
}