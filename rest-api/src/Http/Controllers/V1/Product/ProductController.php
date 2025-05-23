<?php

namespace Webkul\RestApi\Http\Controllers\V1\Product;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Prettus\Repository\Criteria\RequestCriteria;
use Webkul\Admin\Http\Requests\AttributeForm;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\RestApi\Http\Controllers\V1\Controller;
use Webkul\RestApi\Http\Request\MassDestroyRequest;
use Webkul\RestApi\Http\Resources\V1\Product\ProductResource;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected ProductRepository $productRepository)
    {
        $this->addEntityTypeInRequest('products');
    }

    /**
     * Display a listing of the products.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = $this->allResources($this->productRepository);

        return ProductResource::collection($products);
    }

    /**
     * Show resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $resource = $this->productRepository->findOrFail($id);

        return new ProductResource($resource);
    }

    /**
     * Search product results
     */
    public function search(): JsonResource
    {
        $products = $this->productRepository
            ->pushCriteria(app(RequestCriteria::class))
            ->limit(request()->input('limit') ?? 10)
            ->all();

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeInventories(int $id, ?int $warehouseId = null): JsonResponse
    {
        $this->validate(request(), [
            'inventories'                         => 'array',
            'inventories.*.warehouse_location_id' => 'required',
            'inventories.*.warehouse_id'          => 'required',
            'inventories.*.in_stock'              => 'required|integer|min:0',
            'inventories.*.allocated'             => 'required|integer|min:0',
        ]);

        $product = $this->productRepository->findOrFail($id);

        Event::dispatch('product.update.before', $id);

        $this->productRepository->saveInventories(request()->all(), $id, $warehouseId);

        Event::dispatch('product.update.after', $product);

        return new JsonResponse([
            'data'    => $product->inventories,
            'message' => trans('rest-api::app.products.inventory-create-success'),
        ], 200);
    }

    /**
     * Returns product inventories grouped by warehouse.
     */
    public function warehouses(int $id): JsonResponse
    {
        $warehouses = $this->productRepository->getInventoriesGroupedByWarehouse($id);

        return response()->json(array_values($warehouses));
    }

    /**
     * Store a newly created product in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(AttributeForm $request)
    {
        Event::dispatch('product.create.before');

        $product = $this->productRepository->create(request()->all());

        Event::dispatch('product.create.after', $product);

        return new JsonResource([
            'data'    => new ProductResource($product),
            'message' => trans('rest-api::app.products.create-success'),
        ]);
    }

    /**
     * Update the product in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AttributeForm $request, $id)
    {
        Event::dispatch('product.update.before', $id);

        $product = $this->productRepository->update($request->all(), $id);

        Event::dispatch('product.update.after', $product);

        return new JsonResource([
            'data'    => new ProductResource($product),
            'message' => trans('rest-api::app.products.updated-success'),
        ]);
    }

    /**
     * Remove the product from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Event::dispatch('settings.products.delete.before', $id);

            $this->productRepository->delete($id);

            Event::dispatch('settings.products.delete.after', $id);

            return new JsonResource([
                'message' => trans('rest-api::app.products.delete-success'),
            ]);
        } catch (\Exception $exception) {
            return new JsonResource([
                'message' => trans('rest-api::app.products.delete-failed'),
            ], 500);
        }
    }

    /**
     * Mass delete the products.
     *
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest)
    {
        $productIds = $massDestroyRequest->input('indices', []);

        foreach ($productIds as $productId) {
            $product = $this->productRepository->find($productId);

            if (! $product) {
                continue;
            }

            Event::dispatch('product.delete.before', $productId);

            $product->delete($productId);

            Event::dispatch('product.delete.after', $productId);
        }

        return new JsonResource([
            'message' => trans('rest-api::app.products.delete-success'),
        ]);
    }
}
