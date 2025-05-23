<?php

namespace Webkul\RestApi\Http\Controllers\V1\Setting;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Requests\PipelineForm;
use Webkul\Lead\Repositories\PipelineRepository;
use Webkul\RestApi\Http\Controllers\V1\Controller;
use Webkul\RestApi\Http\Resources\V1\Setting\PipelineResource;

class PipelineController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected PipelineRepository $pipelineRepository) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResource
    {
        $pipelines = $this->allResources($this->pipelineRepository);

        return PipelineResource::collection($pipelines);
    }

    /**
     * Show resource.
     */
    public function show(int $id): PipelineResource
    {
        $resource = $this->pipelineRepository->find($id);

        return new PipelineResource($resource);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PipelineForm $request): JsonResource
    {
        $request->merge([
            'is_default' => request()->has('is_default') ? 1 : 0,
        ]);

        Event::dispatch('settings.pipeline.create.before');

        $pipeline = $this->pipelineRepository->create($request->all());

        Event::dispatch('settings.pipeline.create.after', $pipeline);

        return new JsonResource([
            'data'    => new PipelineResource($pipeline),
            'message' => trans('rest-api::app.settings.pipelines.create-success'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PipelineForm $request, int $id): JsonResource
    {
        $request->merge([
            'is_default' => request()->has('is_default') ? 1 : 0,
        ]);

        Event::dispatch('settings.pipeline.update.before', $id);

        $pipeline = $this->pipelineRepository->update($request->all(), $id);

        Event::dispatch('settings.pipeline.update.after', $pipeline);

        return new JsonResource([
            'data'    => new PipelineResource($pipeline),
            'message' => trans('rest-api::app.settings.pipelines.updated-success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResource
    {
        $pipeline = $this->pipelineRepository->findOrFail($id);

        if ($pipeline->is_default) {
            return new JsonResource([
                'message' => trans('rest-api::app.settings.pipelines.default-delete-error'),
            ], 400);
        } else {
            $defaultPipeline = $this->pipelineRepository->getDefaultPipeline();

            $pipeline->leads()->update([
                'lead_pipeline_id'       => $defaultPipeline->id,
                'lead_pipeline_stage_id' => $defaultPipeline->stages()->first()->id,
            ]);
        }

        try {
            Event::dispatch('settings.pipeline.delete.before', $id);

            $this->pipelineRepository->delete($id);

            Event::dispatch('settings.pipeline.delete.after', $id);

            return new JsonResource([
                'message' => trans('rest-api::app.settings.pipelines.delete-success'),
            ]);
        } catch (\Exception $exception) {
            return new JsonResource([
                'message' => trans('rest-api::app.settings.pipelines.delete-failed'),
            ], 500);
        }
    }
}
