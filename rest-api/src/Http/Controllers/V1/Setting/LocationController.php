<?php

namespace Webkul\RestApi\Http\Controllers\V1\Setting;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Prettus\Repository\Criteria\RequestCriteria;
use Webkul\Admin\Http\Requests\AttributeForm;
use Webkul\RestApi\Http\Controllers\V1\Controller;
use Webkul\Warehouse\Repositories\LocationRepository;

class LocationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected LocationRepository $locationRepository) {}

    /**
     * Search location results
     */
    public function search(): JsonResponse
    {
        $results = $this->locationRepository
            ->pushCriteria(app(RequestCriteria::class))
            ->limit(request()->input('limit') ?? 10)
            ->all();

        return response()->json([
            'data' => $results,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttributeForm $request): JsonResponse
    {
        Event::dispatch('settings.location.create.before');

        $location = $this->locationRepository->create(request()->all());

        Event::dispatch('settings.location.create.after', $location);

        return new JsonResponse([
            'data'    => $location,
            'message' => trans('rest-api::app.settings.warehouses.view.locations.create-success'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id)
    {
        Event::dispatch('settings.location.update.before', $id);

        $location = $this->locationRepository->update(request()->all(), $id);

        Event::dispatch('settings.location.update.after', $location);

        return new JsonResponse([
            'data'    => $location,
            'message' => trans('rest-api::app.settings.warehouses.view.locations.create-success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->locationRepository->findOrFail($id);

        try {
            Event::dispatch('settings.location.delete.before', $id);

            $this->locationRepository->delete($id);

            Event::dispatch('settings.location.delete.after', $id);

            return new JsonResponse([
                'message' => trans('rest-api::app.settings.warehouses.view.locations.delete-success'),
            ], 200);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'message' => trans('rest-api::app.settings.warehouses.view.locations.delete-failed'),
            ], 400);
        }
    }
}
