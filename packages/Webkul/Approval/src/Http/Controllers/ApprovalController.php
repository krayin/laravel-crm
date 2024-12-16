<?php

namespace Webkul\Approval\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Webkul\Approval\DataGrids\ApprovalDataGrid;
use Webkul\Approval\Repositories\ApprovalRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Http\JsonResponse;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Admin\Http\Requests\AttributeForm;


class ApprovalController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct(protected ApprovalRepository $approvalRepository)
    {
        request()->request->add(['entity_type' => 'approvals']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(ApprovalDataGrid::class)->toJson();
        }

        return view('approval::index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $approval = $this->approvalRepository->findOrFail($id);

        return view('approval::edit', compact('approval'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AttributeForm $request, $id)
    {
        $this->approvalRepository->update(request()->all(), $id);

        return redirect()->route('admin.approval.index')->with('success',"Approval updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }

    /**
     * Mass delete the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $indices = $massDestroyRequest->input('indices');

        foreach ($indices as $index) {
            Event::dispatch('approval.delete.before', $index);

            $this->approvalRepository->delete($index);

            Event::dispatch('approval.delete.after', $index);
        }

        return new JsonResponse([
            'message' => trans('admin::app.approvals.index.delete-success'),
        ]);
    }
}
