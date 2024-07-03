<?php

namespace Webkul\Admin\Http\Controllers\Setting;

use Illuminate\Support\Facades\Event;
use Webkul\Attribute\Http\Requests\AttributeForm;
use Webkul\Admin\DataGrids\Setting\WarehouseDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Warehouse\Repositories\WarehouseRepository;
use Webkul\Warehouse\Repositories\LocationRepository;

class WarehouseController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected WarehouseRepository $warehouseRepository,
        protected LocationRepository $locationRepository
    )
    {
        request()->request->add(['entity_type' => 'warehouses']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(WarehouseDataGrid::class)->toJson();
        }

        return view('admin::settings.warehouses.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function products($id)
    {
        if (request()->ajax()) {
            return app(\Webkul\Admin\DataGrids\Product\ProductDataGrid::class)->toJson();
        }

        $warehouse = $this->warehouseRepository->findOrFail($id);

        return view('admin::settings.warehouses.products', compact('warehouse'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin::settings.warehouses.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Webkul\Attribute\Http\Requests\AttributeForm $request
     * @return \Illuminate\Http\Response
     */
    public function store(AttributeForm $request)
    {
        Event::dispatch('settings.warehouse.create.before');

        $warehouse = $this->warehouseRepository->create(request()->all());

        Event::dispatch('settings.warehouse.create.after', $warehouse);

        session()->flash('success', trans('admin::app.warehouses.create-success'));

        return redirect()->route('admin.settings.warehouses.index');
    }

    /**
     * Show the form for viewing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function view($id)
    {
        $warehouse = $this->warehouseRepository->findOrFail($id);

        return view('admin::settings.warehouses.view', compact('warehouse'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $warehouse = $this->warehouseRepository->findOrFail($id);

        return view('admin::settings.warehouses.edit', compact('warehouse'));
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
        Event::dispatch('settings.warehouse.update.before', $id);

        $warehouse = $this->warehouseRepository->update(request()->all(), $id);

        Event::dispatch('settings.warehouse.update.after', $warehouse);

        session()->flash('success', trans('admin::app.warehouses.update-success'));

        return redirect()->route('admin.settings.warehouses.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->warehouseRepository->findOrFail($id);

        try {
            Event::dispatch('settings.warehouse.delete.before', $id);

            $this->warehouseRepository->delete($id);

            Event::dispatch('settings.warehouse.delete.after', $id);

            return response()->json([
                'message' => trans('admin::app.response.destroy-success', ['name' => trans('admin::app.warehouses.warehouse')]),
            ], 200);
        } catch(\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.response.destroy-failed', ['name' => trans('admin::app.warehouses.warehouse')]),
            ], 400);
        }
    }

    /**
     * Search warehouse results
     *
     * @return \Illuminate\Http\Response
     */
    public function search()
    {
        $results = $this->warehouseRepository->findWhere([
            ['name', 'like', '%' . urldecode(request()->input('query')) . '%']
        ]);

        return response()->json($results);
    }

    /**
     * Returns warehouse locations.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($id = null)
    {
        $results = $this->locationRepository
            ->leftJoin('warehouses', 'warehouse_locations.warehouse_id', '=', 'warehouses.id')
            ->where('warehouse_locations.name', 'like', urldecode(request()->input('query')) . '%')
            ->select('warehouse_locations.*')
            ->addSelect('warehouse_locations.id as id')
            ->addSelect('warehouses.name as warehouse_name')
            ->get();

        $locations = [];

        foreach ($results as $result) {
            if (
                $id
                && $result->warehouse_id != $id
            ) {
                continue;
            }

            if (! isset($locations[$result->warehouse_id])) {
                $locations[$result->warehouse_id] = [
                    'id'  => $result->warehouse_id,
                    'name' => $result->warehouse_name,
                ];
            }

            $locations[$result->warehouse_id]['locations'][] = $result;
        }

        return response()->json(array_values($locations));
    }
}
