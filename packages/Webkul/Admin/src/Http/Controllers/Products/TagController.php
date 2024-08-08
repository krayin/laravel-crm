<?php

namespace Webkul\Admin\Http\Controllers\Products;

use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Product\Repositories\ProductRepository;

class TagController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected ProductRepository $productRepository) {}

    /**
     * Store a newly created resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function attach($id)
    {
        Event::dispatch('products.tag.create.before', $id);

        $product = $this->productRepository->findOrFail($id);

        if (! $product->tags->contains(request()->input('tag_id'))) {
            $product->tags()->attach(request()->input('tag_id'));
        }

        Event::dispatch('products.tag.create.after', $product);

        return response()->json([
            'message' => trans('admin::app.leads.view.tags.create-success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $productId
     * @return \Illuminate\Http\Response
     */
    public function detach($productId)
    {
        Event::dispatch('products.tag.delete.before', $productId);

        $product = $this->productRepository->find($productId);

        $product->tags()->detach(request()->input('tag_id'));

        Event::dispatch('products.tag.delete.after', $product);

        return response()->json([
            'message' => trans('admin::app.leads.view.tags.destroy-success'),
        ]);
    }
}
