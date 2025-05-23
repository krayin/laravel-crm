<?php

namespace Webkul\RestApi\Http\Controllers\V1\Setting;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Webkul\Automation\Repositories\WorkflowRepository;
use Webkul\RestApi\Http\Controllers\V1\Controller;
use Webkul\RestApi\Http\Resources\V1\Setting\WorkflowResource;

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
    public function index(): JsonResource
    {
        $workflows = $this->allResources($this->workflowRepository);

        return WorkflowResource::collection($workflows);
    }

    /**
     * Show resource.
     */
    public function show(int $id): WorkflowResource
    {
        $resource = $this->workflowRepository->find($id);

        return new WorkflowResource($resource);
    }

    /**
     * Store a newly created workflow in storage.
     */
    public function store(): JsonResource
    {
        $this->validate(request(), [
            'name' => 'required',
        ]);

        Event::dispatch('settings.workflow.create.before');

        $workflow = $this->workflowRepository->create(request()->all());

        Event::dispatch('settings.workflow.create.after', $workflow);

        return new JsonResource([
            'data'    => new WorkflowResource($workflow),
            'message' => trans('rest-api::app.settings.workflows.create-success'),
        ]);
    }

    /**
     * Update the specified workflow in storage.
     */
    public function update($id): JsonResource
    {
        $this->validate(request(), [
            'name' => 'required',
        ]);

        Event::dispatch('settings.workflow.update.before', $id);

        $workflow = $this->workflowRepository->update(request()->all(), $id);

        Event::dispatch('settings.workflow.update.after', $workflow);

        return new JsonResource([
            'data'    => new WorkflowResource($workflow),
            'message' => trans('rest-api::app.settings.workflows.update-success'),
        ]);
    }

    /**
     * Remove the specified workflow from storage.
     */
    public function destroy(int $id): JsonResource
    {
        try {
            Event::dispatch('settings.workflow.delete.before', $id);

            $this->workflowRepository->delete($id);

            Event::dispatch('settings.workflow.delete.after', $id);

            return new JsonResource([
                'message' => trans('rest-api::app.settings.workflows.delete-success'),
            ]);
        } catch (\Exception $exception) {
            return new JsonResource([
                'message' => trans('rest-api::app.settings.workflows.delete-failed'),
            ], 500);
        }
    }
}
