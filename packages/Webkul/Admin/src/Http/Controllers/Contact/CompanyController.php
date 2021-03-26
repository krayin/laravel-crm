<?php

namespace Webkul\Admin\Http\Controllers\Contact;

use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin::contacts.companies.index');
    }
}