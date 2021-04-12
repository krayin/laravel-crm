<?php

namespace Webkul\Admin\Http\Controllers\Product;

use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Attribute\Http\Requests\AttributeForm;
use Webkul\Product\Repositories\ProductRepository;

class ProductController extends Controller
{
    /**
     * ProductRepository object
     *
     * @var \Webkul\Product\Repositories\ProductRepository
     */
    protected $productRepository;

    /**
     * Create a new controller instance.
     *
     * @param \Webkul\Product\Repositories\ProductRepository  $productRepository
     *
     * @return void
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;

        request()->request->add(['entity_type' => 'Webkul\Product\Models\Product']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin::products.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Webkul\Attribute\Http\Requests\AttributeForm $request
     * @return \Illuminate\Http\Response
     */
    public function store(AttributeForm $request)
    {
        Event::dispatch('product.create.before');

        $product = $this->productRepository->create(request()->all());

        Event::dispatch('product.create.after', $product);
        
        session()->flash('success', trans('admin::app.products.create-success'));

        return redirect()->back();
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

        return view('admin::products.edit', compact('product'));
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
        Event::dispatch('product.update.before');

        $product = $this->productRepository->update(request()->all(), $id);

        Event::dispatch('product.update.after', $product);
        
        session()->flash('success', trans('admin::app.products.update-success'));

        return redirect()->route('admin.products.index');
    }
}