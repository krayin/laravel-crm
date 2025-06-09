<?php

namespace Webkul\Admin\Http\Controllers\User;

use Webkul\Admin\Http\Controllers\Controller;

class SuperAdminController extends Controller
{
    public function index()
    {
        // aqui você pode buscar tenants, users, etc.
        return view('admin::user.superAdmin.index');
    }
}
