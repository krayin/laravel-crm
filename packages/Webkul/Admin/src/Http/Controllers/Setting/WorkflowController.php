<?php

namespace Webkul\Admin\Http\Controllers\Setting;

use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Workflow\Repositories\WorkflowRepository;

class WorkflowController extends Controller
{
    /**
     * WorkflowRepository object
     *
     * @var \Webkul\User\Repositories\WorkflowRepository
     */
    protected $workflowRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Workflow\Repositories\WorkflowRepository  $workflowRepository
     * @return void
     */
    public function __construct(WorkflowRepository $workflowRepository)
    {
        $this->workflowRepository = $workflowRepository;
    }

    /**
     * Display a listing of the workflow.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(\Webkul\Admin\DataGrids\Setting\WorkflowDataGrid::class)->toJson();
        }

        return view('admin::settings.workflows.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin::settings.workflows.create');
    }

    /**
     * Store a newly created workflow in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'name' => 'required',
        ]);

        Event::dispatch('settings.workflow.create.before');

        $workflow = $this->workflowRepository->create(request()->all());

        Event::dispatch('settings.workflow.create.after', $workflow);

        session()->flash('success', trans('admin::app.settings.workflows.create-success'));

        return redirect()->route('admin.settings.workflows.index');
    }

    /**
     * Show the form for editing the specified workflow.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $workflow = $this->workflowRepository->findOrFail($id);

        return view('admin::settings.workflows.edit', compact('workflow'));
    }

    /**
     * Update the specified workflow in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $this->validate(request(), [
            'name' => 'required',
        ]);

        Event::dispatch('settings.workflow.update.before', $id);

        $workflow = $this->workflowRepository->update(request()->all(), $id);

        Event::dispatch('settings.workflow.update.after', $workflow);

        session()->flash('success', trans('admin::app.settings.workflows.update-success'));

        return redirect()->route('admin.settings.workflows.index');
    }

    /**
     * Remove the specified workflow from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $workflow = $this->workflowRepository->findOrFail($id);

        try {
            Event::dispatch('settings.workflow.delete.before', $id);

            $this->workflowRepository->delete($id);

            Event::dispatch('settings.workflow.delete.after', $id);

            return response()->json([
                'message' => trans('admin::app.settings.workflows.delete-success'),
            ], 200);
        } catch(\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.settings.workflows.delete-failed'),
            ], 400);
        }

        return response()->json([
            'message' => trans('admin::app.settings.workflows.delete-failed'),
        ], 400);
    }
}
