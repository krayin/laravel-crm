<?php

namespace Webkul\RestApi\Http\Controllers\V1\Setting\Warehouses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Prettus\Repository\Criteria\RequestCriteria;
use Webkul\Admin\Http\Requests\AttributeForm;
use Webkul\RestApi\Http\Controllers\V1\Controller;
use Webkul\RestApi\Http\Resources\V1\Setting\WarehouseResource;
use Webkul\Warehouse\Repositories\WarehouseRepository;

class WarehouseController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected WarehouseRepository $warehouseRepository)
    {
        request()->request->add(['entity_type' => 'warehouses']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResource
    {
        $warehouses = $this->allResources($this->warehouseRepository);

        return WarehouseResource::collection($warehouses);
    }

    /**
     *  Display the specified resource.
     */
    public function show(int $id): WarehouseResource
    {
        $warehouse = $this->warehouseRepository->findOrFail($id);

        return new WarehouseResource($warehouse);
    }

    /**
     * Search location results
     */
    public function search(): JsonResponse
    {
        $results = $this->warehouseRepository
            ->pushCriteria(app(RequestCriteria::class))
            ->limit(request()->input('limit') ?? 10)
            ->all();

        return response()->json($results);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttributeForm $request): JsonResponse
    {
        Event::dispatch('settings.warehouse.create.before');

        $warehouse = $this->warehouseRepository->create($request->all());

        Event::dispatch('settings.warehouse.create.after', $warehouse);

        return response()->json([
            'data'    => $warehouse,
            'message' => trans('rest-api::app.settings.warehouses.create-success'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AttributeForm $request, int $id)
    {
        Event::dispatch('settings.warehouse.update.before', $id);

        $warehouse = $this->warehouseRepository->update($request->all(), $id);

        Event::dispatch('settings.warehouse.update.after', $warehouse);

        return response()->json([
            'data'    => $warehouse,
            'message' => trans('rest-api::app.settings.warehouses.update-success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->warehouseRepository->findOrFail($id);

        try {
            Event::dispatch('settings.warehouse.delete.before', $id);

            $this->warehouseRepository->delete($id);

            Event::dispatch('settings.warehouse.delete.after', $id);

            return response()->json([
                'message' => trans('rest-api::app.settings.warehouses.delete-success'),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('rest-api::app.settings.warehouses.delete-success'),
            ], 400);
        }
    }
}
