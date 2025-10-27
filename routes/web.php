<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\SSOController;

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

Route::get('/login/sso', [SSOController::class, 'redirectToSSO'])->name('ssologin');
Route::get('/oauth/callback', [SSOController::class, 'handleCallback'])->name('ssocalback');
