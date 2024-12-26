<?php

namespace Webkul\Consignment\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Webkul\Consignment\Repositories\ConsignmentRepository;
use Webkul\Admin\Http\Requests\AttributeForm;
use Webkul\Consignment\DataGrids\ConsignmentDataGrid;
use Illuminate\Support\Facades\Event;
use Illuminate\Http\JsonResponse;
use Webkul\Admin\Http\Requests\MassDestroyRequest;

class ConsignmentController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct(protected ConsignmentRepository $consignmentRepository)
    {
        request()->request->add(['entity_type' => 'consignments']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(ConsignmentDataGrid::class)->toJson();
        }
        return view('consignment::index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('consignment::create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(AttributeForm $request)
    {
        $this->consignmentRepository->create(request()->all());

        return redirect()->route('admin.consignment.index')->with('success',"consignment created successfully");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $consignment = $this->consignmentRepository->findOrFail($id);

        return view('consignment::edit', compact('consignment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, AttributeForm $request)
    {
        $this->consignmentRepository->update(request()->all(), $id);

        return redirect()->route('admin.consignment.index')->with('success',"consignment updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id): JsonResponse
    {
        $consignment = $this->consignmentRepository->findOrFail($id);

        try {
            Event::dispatch('consignment.delete.before', $id);

            $consignment->delete($id);

            Event::dispatch('consignment.delete.after', $id);

            return response()->json([
                'message' => trans('consignment::app.consignments.index.delete-success'),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('consignment::app.consignments.index.delete-failed'),
            ], 400);
        }
    }

    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $indices = $massDestroyRequest->input('indices');

        foreach ($indices as $index) {
            Event::dispatch('consignment.delete.before', $index);

            $this->consignmentRepository->delete($index);

            Event::dispatch('consignment.delete.after', $index);
        }

        return new JsonResponse([
            'message' => trans('consignment::app.consignments.index.delete-success'),
        ]);
    }

}
