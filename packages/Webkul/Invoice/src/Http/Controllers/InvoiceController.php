<?php

namespace Webkul\Invoice\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Webkul\Invoice\DataGrids\InvoiceDataGrid;
use Webkul\Invoice\Repositories\InvoiceRepository;
use Webkul\Admin\Http\Requests\AttributeForm;
use Illuminate\Support\Facades\Event;
use Illuminate\Http\JsonResponse;
use Webkul\Admin\Http\Requests\MassDestroyRequest;

class InvoiceController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct(protected InvoiceRepository $invoiceRepository)
    {
        request()->request->add(['entity_type' => 'invoice']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(InvoiceDataGrid::class)->toJson();
        }

        return view('invoice::index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('invoice::create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(AttributeForm $request)
    {
        $this->invoiceRepository->create(request()->all());

        return redirect()->route('admin.invoice.index')->with('success',"Invoice created successfully");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $invoice = $this->invoiceRepository->findOrFail($id);

        return view('invoice::edit', compact('invoice'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AttributeForm $request, $id)
    {
        $this->invoiceRepository->update($request->all(), $id);

        return redirect()->route('admin.invoice.index')->with('success',"Invoice updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $invoice = $this->invoiceRepository->delete($id);

        return response()->json(['message' => 'Invoice deleted successfully']);

    }

    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $indices = $massDestroyRequest->input('indices');

        foreach ($indices as $index) {
            Event::dispatch('invoice.delete.before', $index);

            $this->invoiceRepository->delete($index);

            Event::dispatch('invoice.delete.after', $index);
        }

        return new JsonResponse([
            'message' => trans('admin::app.invoices.index.delete-success'),
        ]);
    }
}
