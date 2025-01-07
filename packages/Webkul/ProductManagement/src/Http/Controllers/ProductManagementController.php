<?php

namespace Webkul\ProductManagement\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Webkul\ProductManagement\Repositories\ProductRepository;
use Webkul\Admin\Http\Requests\AttributeForm;
use Webkul\ProductManagement\DataGrids\ProductDataGrid;
use Illuminate\Support\Facades\Event;
use Illuminate\Http\JsonResponse;
use Webkul\Admin\Http\Requests\MassDestroyRequest;


class ProductManagementController extends Controller
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct(protected ProductRepository $productRepository)
    {
        request()->request->add(['entity_type' => 'productManagement']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(ProductDataGrid::class)->toJson();
        }
        return view('productmanagement::index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('productmanagement::create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(AttributeForm $request)
    {
        $this->productRepository->create($request->all());

        return redirect()->route('admin.productmanagement.index')->with('success',"Product created successfully");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $product = $this->productRepository->findOrFail($id);

        return view('productmanagement::edit', compact('product'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, AttributeForm $request)
    {
        $this->productRepository->update($request->all(), $id);

        return redirect()->route('admin.productmanagement.index')->with('success',"Product updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

     public function destroy(int $id): JsonResponse
     {
         $product = $this->productRepository->findOrFail($id);

         try {
             Event::dispatch('product.delete.before', $id);

             $product->delete($id);

             Event::dispatch('product.delete.after', $id);

             return response()->json([
                 'message' => trans('product::app.products.index.delete-success'),
             ], 200);
         } catch (\Exception $exception) {
             return response()->json([
                 'message' => trans('product::app.products.index.delete-failed'),
             ], 400);
         }
     }

     public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
     {
         $indices = $massDestroyRequest->input('indices');

         foreach ($indices as $index) {
             Event::dispatch('product.delete.before', $index);

             $this->productRepository->delete($index);

             Event::dispatch('product.delete.after', $index);
         }

         return new JsonResponse([
             'message' => trans('product::app.products.index.delete-success'),
         ]);
     }
}
