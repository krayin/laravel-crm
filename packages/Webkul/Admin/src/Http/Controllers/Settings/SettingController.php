<?php

namespace Webkul\Admin\Http\Controllers\Settings;

use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        return view('admin::settings.index');
    }
}
