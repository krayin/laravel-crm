<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Controller;

/**
 * Home routes.
 */

// Route::get('/', function () {
//     dd(Auth::check(), Auth::user(), session()->all());
//     return view('welcome');
// });
Route::middleware(['web'])->get('/', [Controller::class, 'redirectToLogin'])->name('krayin.home');
