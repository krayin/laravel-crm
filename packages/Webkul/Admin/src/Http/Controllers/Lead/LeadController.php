<?php

namespace Webkul\Admin\Http\Controllers\Lead;

use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin::leads.index');
    }
}