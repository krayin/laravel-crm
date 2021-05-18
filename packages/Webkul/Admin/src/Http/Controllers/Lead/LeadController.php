<?php

namespace Webkul\Admin\Http\Controllers\Lead;

use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Attribute\Http\Requests\AttributeForm;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Lead\Repositories\FileRepository;

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
     * Create a new controller instance.
     *
     * @param \Webkul\Lead\Repositories\LeadRepository  $leadRepository
     * @param \Webkul\Lead\Repositories\FileRepository  $fileRepository
     *
     * @return void
     */
    public function __construct(
        LeadRepository $leadRepository,
        FileRepository $fileRepository
    )
    {
        $this->leadRepository = $leadRepository;

        $this->fileRepository = $fileRepository;

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

        $lead = $this->leadRepository->create(request()->all());

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
}