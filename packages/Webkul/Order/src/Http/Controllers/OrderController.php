<?php

namespace Webkul\Order\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Webkul\Order\DataGrids\OrderDataGrid;
use Webkul\Order\Repositories\OrderRepository;
use Webkul\Admin\Http\Requests\AttributeForm;
use Illuminate\Support\Facades\Event;
use Illuminate\Http\JsonResponse;
use Webkul\Admin\Http\Requests\MassDestroyRequest;

class OrderController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct(protected OrderRepository $orderRepository)
    {
        request()->request->add(['entity_type' => 'orders']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {

        if (request()->ajax()) {
            return app(OrderDataGrid::class)->toJson();
        }


        return view('order::index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('order::create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(AttributeForm $request)
    {
        $this->orderRepository->create(request()->all());

        return redirect()->route('admin.order.index')->with('success',"Order created successfully");
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $order = $this->orderRepository->findOrFail($id);

        return view('order::edit', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AttributeForm $request, $id)
    {
        $this->orderRepository->update(request()->all(), $id);

        return redirect()->route('admin.order.index')->with('success',"Order updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = $this->orderRepository->findOrFail($id);

        try {
            Event::dispatch('order.delete.before', $id);

            $order->delete($id);

            Event::dispatch('order.delete.after', $id);

            return response()->json([
                'message' => trans('order::app.orders.index.delete-success'),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('order::app.orders.index.delete-failed'),
            ], 400);
        }
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
            Event::dispatch('order.delete.before', $index);

            $this->orderRepository->delete($index);

            Event::dispatch('order.delete.after', $index);
        }

        return new JsonResponse([
            'message' => trans('admin::app.orders.index.delete-success'),
        ]);
    }

}
