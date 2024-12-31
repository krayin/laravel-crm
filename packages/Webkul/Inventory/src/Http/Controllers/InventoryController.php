<?php

namespace Webkul\Inventory\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Webkul\Inventory\Repositories\InventoryRepository;
use Webkul\Admin\Http\Requests\AttributeForm;
use Webkul\Inventory\DataGrids\InventoryDataGrid;
use Illuminate\Support\Facades\Event;
use Illuminate\Http\JsonResponse;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Inventory\Exports\InventoryExport;
use Maatwebsite\Excel\Facades\Excel;


class InventoryController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct(protected InventoryRepository $inventoryRepository)
    {
        request()->request->add(['entity_type' => 'inventories']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(InventoryDataGrid::class)->toJson();
        }
        return view('inventory::index');
    }

    public function view($id)
    {
        $inventory = $this->inventoryRepository->findOrFail($id);

        return view('inventory::view', compact('inventory'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('inventory::create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(AttributeForm $request)
    {
        $this->inventoryRepository->create(request()->all());

        return redirect()->route('admin.inventory.index')->with('success',"Inventory created successfully");
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $inventory = $this->inventoryRepository->findOrFail($id);

        return view('inventory::edit', compact('inventory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, AttributeForm $request)
    {
        $this->inventoryRepository->update(request()->all(), $id);

        return redirect()->route('admin.inventory.index')->with('success',"Inventory updated successfully");

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id): JsonResponse
    {
        $inventory = $this->inventoryRepository->findOrFail($id);

        try {
            Event::dispatch('inventory.delete.before', $id);

            $inventory->delete($id);

            Event::dispatch('inventory.delete.after', $id);

            return response()->json([
                'message' => trans('inventory::app.inventories.index.delete-success'),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('inventory::app.inventories.index.delete-failed'),
            ], 400);
        }
    }

    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $indices = $massDestroyRequest->input('indices');

        foreach ($indices as $index) {
            Event::dispatch('inventory.delete.before', $index);

            $this->inventoryRepository->delete($index);

            Event::dispatch('inventory.delete.after', $index);
        }

        return new JsonResponse([
            'message' => trans('inventory::app.inventories.index.delete-success'),
        ]);
    }
    
    public function export()
    {
        return Excel::download(new InventoryExport, 'inventory.xlsx');
    }
}
