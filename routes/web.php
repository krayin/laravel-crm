<?php

use App\Http\Controllers\Admin\BrandKit\BrandKitController;
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

        /*
        |--------------------------------------------------------------------------
        | Brand Kit Routes
        |--------------------------------------------------------------------------
        |
        | Rotas para gerenciamento do Brand Kit (overrides, custom CSS, snapshots).
        | Protegidas por middleware de autenticação do admin panel.
        |
        */
        Route::prefix("settings/brand-kit")
            ->name("admin.brandkit.")
            // ->middleware(\App\Http\Middleware\BrandKitPermission::class) // MVP: desligado
            ->group(function () {
                Route::get("/", [BrandKitController::class, "index"])->name(
                    "index",
                );

                // Overrides
                Route::post("/override", [
                    BrandKitController::class,
                    "setOverride",
                ])->name("override.set");
                Route::post("/override/reset", [
                    BrandKitController::class,
                    "resetOverride",
                ])->name("override.reset");
                Route::post("/override/reset-all", [
                    BrandKitController::class,
                    "resetAllOverrides",
                ])->name("override.reset_all");

                // Custom CSS
                Route::post("/css", [
                    BrandKitController::class,
                    "addCustomCss",
                ])->name("css.add");
                Route::post("/css/{id}/toggle", [
                    BrandKitController::class,
                    "toggleCustomCss",
                ])
                    ->whereNumber("id")
                    ->name("css.toggle");
                Route::delete("/css/{id}", [
                    BrandKitController::class,
                    "deleteCustomCss",
                ])
                    ->whereNumber("id")
                    ->name("css.delete");

                // Snapshots
                Route::post("/snapshots", [
                    BrandKitController::class,
                    "createSnapshot",
                ])->name("snapshot.create");
                Route::post("/snapshots/{id}/restore", [
                    BrandKitController::class,
                    "restoreSnapshot",
                ])
                    ->whereNumber("id")
                    ->name("snapshot.restore");
            });
    });
