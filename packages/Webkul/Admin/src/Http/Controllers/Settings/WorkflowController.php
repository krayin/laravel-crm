<?php

namespace Webkul\Admin\Http\Controllers\Settings;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Settings\WorkflowDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Automation\Repositories\WorkflowRepository;

class WorkflowController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected WorkflowRepository $workflowRepository) {}

    /**
     * Display a listing of the workflow.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(WorkflowDataGrid::class)->process();
        }

        return view('admin::settings.workflows.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin::settings.workflows.create');
    }

    /**
     * Store a newly created workflow in storage.
     */
    public function store(): RedirectResponse
    {
        $this->validate(request(), [
            'name' => 'required',
        ]);

        Event::dispatch('settings.workflow.create.before');

        $workflow = $this->workflowRepository->create(request()->all());

        Event::dispatch('settings.workflow.create.after', $workflow);

        session()->flash('success', trans('admin::app.settings.workflows.index.create-success'));

        return redirect()->route('admin.settings.workflows.index');
    }

    /**
     * Show the form for editing the specified workflow.
     */
    public function edit(int $id): View
    {
        $workflow = $this->workflowRepository->findOrFail($id);

        return view('admin::settings.workflows.edit', compact('workflow'));
    }

    /**
     * Update the specified workflow in storage.
     */
    public function update(int $id): RedirectResponse
    {
        $this->validate(request(), [
            'name' => 'required',
        ]);

        Event::dispatch('settings.workflow.update.before', $id);

        $workflow = $this->workflowRepository->update(request()->all(), $id);

        Event::dispatch('settings.workflow.update.after', $workflow);

        session()->flash('success', trans('admin::app.settings.workflows.index.update-success'));

        return redirect()->route('admin.settings.workflows.index');
    }

    /**
     * Remove the specified workflow from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $workflow = $this->workflowRepository->findOrFail($id);

        try {
            Event::dispatch('settings.workflow.delete.before', $id);

            $workflow->delete($id);

            Event::dispatch('settings.workflow.delete.after', $id);

            return response()->json([
                'message' => trans('admin::app.settings.workflows.index.delete-success'),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.settings.workflows.index.delete-failed'),
            ], 400);
        }

        return response()->json([
            'message' => trans('admin::app.settings.workflows.index.delete-failed'),
        ], 400);
    }
}
