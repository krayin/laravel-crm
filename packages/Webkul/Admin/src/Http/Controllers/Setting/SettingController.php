<?php

namespace Webkul\Admin\Http\Controllers\Setting;

use Webkul\Admin\Http\Controllers\Controller;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin::settings.index');
    }
}