<?php

namespace Webkul\Admin\Http\Controllers\Settings;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Prettus\Repository\Criteria\RequestCriteria;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\AttributeForm;
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
     *
     * @return \Illuminate\Http\Response
     */
    public function search()
    {
        $results = $this->locationRepository
            ->pushCriteria(app(RequestCriteria::class))
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
            'message' => trans('admin::app.settings.warehouses.view.locations.create-success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id): JsonResponse
    {
        $this->locationRepository->findOrFail($id);

        try {
            Event::dispatch('settings.location.delete.before', $id);

            $this->locationRepository->delete($id);

            Event::dispatch('settings.location.delete.after', $id);

            return new JsonResponse([
                'message' => trans('admin::app.settings.warehouses.view.locations.delete-success'),
            ], 200);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'message' => trans('admin::app.settings.warehouses.view.locations.delete-failed'),
            ], 400);
        }
    }
}
