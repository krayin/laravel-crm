<?php

namespace Webkul\Admin\Http\Controllers\Product;

use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin::products.index');
    }
}