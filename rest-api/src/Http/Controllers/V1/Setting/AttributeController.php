<?php

namespace Webkul\RestApi\Http\Controllers\V1\Setting;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Core\Contracts\Validations\Code;
use Webkul\RestApi\Http\Controllers\V1\Controller;
use Webkul\RestApi\Http\Request\MassDestroyRequest;
use Webkul\RestApi\Http\Resources\V1\Setting\AttributeResource;

class AttributeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected AttributeRepository $attributeRepository) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResource
    {
        $attributes = $this->allResources($this->attributeRepository);

        return AttributeResource::collection($attributes);
    }

    /**
     * Show resource.
     */
    public function show(int $id): AttributeResource
    {
        $resource = $this->attributeRepository->find($id);

        return new AttributeResource($resource);
    }

    /**
     * Search attribute lookup results.
     */
    public function lookup($lookup): JsonResource
    {
        $results = $this->attributeRepository->getLookUpOptions($lookup, request()->input('query'));

        return AttributeResource::collection($results);
    }

    /**
     * Search attribute lookup results
     */
    public function lookupEntity(string $lookup): JsonResponse
    {
        $result = $this->attributeRepository->getLookUpEntity($lookup, request()->input('query'));

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): JsonResource
    {
        $this->validate(request(), [
            'code' => ['required', 'unique:attributes,code,NULL,NULL,entity_type,'.request('entity_type'), new Code],
            'name' => 'required',
            'type' => 'required',
        ]);

        Event::dispatch('settings.attribute.create.before');

        request()->request->add(['quick_add' => 1]);

        $attribute = $this->attributeRepository->create(request()->all());

        Event::dispatch('settings.attribute.create.after', $attribute);

        return new JsonResource([
            'data'    => new AttributeResource($attribute),
            'message' => trans('rest-api::app.settings.attributes.create-success'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id): JsonResource
    {
        $this->validate(request(), [
            'code' => ['required', 'unique:attributes,code,NULL,NULL,entity_type,'.$id, new Code],
            'name' => 'required',
            'type' => 'required',
        ]);

        Event::dispatch('settings.attribute.update.before', $id);

        $attribute = $this->attributeRepository->update(request()->all(), $id);

        Event::dispatch('settings.attribute.update.after', $attribute);

        return new JsonResource([
            'data'    => new AttributeResource($attribute),
            'message' => trans('rest-api::app.settings.attributes.update-success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResource
    {
        $attribute = $this->attributeRepository->findOrFail($id);

        if (! $attribute->is_user_defined) {
            return new JsonResource([
                'message' => trans('rest-api::app.settings.attributes.user-define-error'),
            ], 400);
        }

        try {
            Event::dispatch('settings.attribute.delete.before', $id);

            $this->attributeRepository->delete($id);

            Event::dispatch('settings.attribute.delete.after', $id);

            return new JsonResource([
                'message' => trans('rest-api::app.settings.attributes.destroy-success'),
            ]);
        } catch (\Exception $exception) {
            return new JsonResource([
                'message' => trans('rest-api::app.settings.attributes.delete-failed'),
            ], 500);
        }
    }

    /**
     * Mass delete the specified resources.
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResource
    {
        $attributeIds = $massDestroyRequest->input('indices', []);

        $count = 0;

        foreach ($attributeIds as $attributeId) {
            $attribute = $this->attributeRepository->find($attributeId);

            if (! $attribute->is_user_defined) {
                continue;
            }

            Event::dispatch('settings.attribute.delete.before', $attributeId);

            $this->attributeRepository->delete($attributeId);

            Event::dispatch('settings.attribute.delete.after', $attributeId);

            $count++;
        }

        if (! $count) {
            return new JsonResource([
                'message' => trans('rest-api::app.settings.attributes.delete-failed'),
            ], 500);
        }

        return new JsonResource([
            'message' => trans('rest-api::app.settings.attributes.destroy-success', ['name' => trans('rest-api::app.settings.attributes.title')]),
        ]);
    }

    /**
     * Download image or file
     */
    public function download(): StreamedResponse
    {
        return Storage::download(request('path'));
    }
}
