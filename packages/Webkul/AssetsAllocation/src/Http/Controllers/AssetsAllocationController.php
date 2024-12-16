<?php

namespace Webkul\AssetsAllocation\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Webkul\AssetsAllocation\DataGrids\AssetsAllocationDataGrid;
use Webkul\AssetsAllocation\Repositories\AssetsAllocationRepository;
use Webkul\Admin\Http\Requests\AttributeForm;
use Illuminate\Support\Facades\Event;
use Illuminate\Http\JsonResponse;
use Webkul\Admin\Http\Requests\MassDestroyRequest;


class AssetsAllocationController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct(protected AssetsAllocationRepository $assetsAllocationRepository)
    {
        request()->request->add(['entity_type' => 'assetsAllocations']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(AssetsAllocationDataGrid::class)->toJson();
        }


        return view('assetsAllocation::index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('assetsAllocation::create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(AttributeForm $request)
    {
        $this->assetsAllocationRepository->create(request()->all());

        return redirect()->route('admin.assetsAllocation.index')->with('success',"AssetsAllocation created successfully");
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $assetsAllocation = $this->assetsAllocationRepository->findOrFail($id);

        return view('assetsAllocation::edit', compact('assetsAllocation'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AttributeForm $request, $id)
    {
        $this->assetsAllocationRepository->update($request->all(), $id);

        return redirect()->route('admin.assetsAllocation.index')->with('success',"AssetsAllocation updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id): JsonResponse
    {
        $assetsAllocation = $this->assetsAllocationRepository->findOrFail($id);

        try {
            Event::dispatch('assetsAllocation.delete.before', $id);

            $assetsAllocation->delete($id);

            Event::dispatch('assetsAllocation.delete.after', $id);

            return response()->json([
                'message' => trans('assetsAllocation::app.assetsAllocations.index.delete-success'),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('assetsAllocation::app.assetsAllocations.index.delete-failed'),
            ], 400);
        }
    }

    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $indices = $massDestroyRequest->input('indices');

        foreach ($indices as $index) {
            Event::dispatch('assetsAllocation.delete.before', $index);

            $this->assetsAllocationRepository->delete($index);

            Event::dispatch('assetsAllocation.delete.after', $index);
        }

        return new JsonResponse([
            'message' => trans('admin::app.assetsAllocations.index.delete-success'),
        ]);
    }
}
