<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Test route for Google Drive
Route::get('/test-drive/{id?}', function ($id = 1) {
    $lead = \Webkul\Lead\Repositories\LeadRepository::class;
    $lead = app($lead)->findOrFail($id);
    return view('test-drive', compact('lead'));
});
