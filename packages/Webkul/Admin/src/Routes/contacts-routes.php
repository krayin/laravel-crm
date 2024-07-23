<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Contact\PersonController;
use Webkul\Admin\Http\Controllers\Contact\OrganizationController;

/**
 * Settings routes.
 */
Route::group(['middleware' => ['admin_locale'], 'prefix' => config('app.admin_path')], function () {
    Route::prefix('contacts')->group(function () {
        /**
         * Persons routes.
         */
        Route::controller(PersonController::class)->prefix('persons')->group(function () {
            Route::get('', 'index')->name('admin.contacts.persons.index');

            Route::get('create', 'create')->name('admin.contacts.persons.create');

            Route::post('create', 'store')->name('admin.contacts.persons.store');

            Route::get('edit/{id?}', 'edit')->name('admin.contacts.persons.edit');

            Route::put('edit/{id}', 'update')->name('admin.contacts.persons.update');

            Route::get('search', 'search')->name('admin.contacts.persons.search');

            Route::middleware(['throttle:100,60'])->delete('{id}', 'destroy')->name('admin.contacts.persons.delete');

            Route::post('mass-destroy', 'massDestroy')->name('admin.contacts.persons.mass_delete');
        });

        /**
         * Organization routes.
         */
        Route::controller(OrganizationController::class)->prefix('organizations')->group(function () {
            Route::get('', 'index')->name('admin.contacts.organizations.index');

            Route::get('create', 'create')->name('admin.contacts.organizations.create');

            Route::post('create', 'store')->name('admin.contacts.organizations.store');

            Route::get('edit/{id?}', 'edit')->name('admin.contacts.organizations.edit');

            Route::put('edit/{id}', 'update')->name('admin.contacts.organizations.update');

            Route::delete('{id}', 'destroy')->name('admin.contacts.organizations.delete');

            Route::put('mass-destroy', 'massDestroy')->name('admin.contacts.organizations.mass_delete');
        });
    });
});
