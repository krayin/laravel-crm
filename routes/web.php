<?php

use App\Http\Controllers\Admin\ThemeController;
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

Route::get("/", function () {
    return view("welcome");
});

/*
|--------------------------------------------------------------------------
| Theme Manager Routes (Upgrade-Safe)
|--------------------------------------------------------------------------
|
| Rotas adicionais para gerenciamento de tema que complementam o package
| ThemeManager sem modificá-lo. Protegidas por middleware de autenticação
| e permissão.
|
*/
Route::prefix(config("app.admin_path", "admin"))
    ->middleware(["web", "user"])
    ->group(function () {
        Route::prefix("settings/theme")
            ->name("admin.settings.theme.")
            ->middleware(\App\Http\Middleware\ThemePermission::class)
            ->group(function () {
                // Restaurar tema padrão
                Route::post("/restore", [
                    ThemeController::class,
                    "restore",
                ])->name("restore");

                // Rollback para tema anterior
                Route::post("/rollback", [
                    ThemeController::class,
                    "rollback",
                ])->name("rollback");
            });
    });
