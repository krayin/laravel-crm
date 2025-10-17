<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Contact\OrganizationController;
use Webkul\Admin\Http\Controllers\Contact\Persons\ActivityController;
use Webkul\Admin\Http\Controllers\Contact\Persons\PersonController;
use Webkul\Admin\Http\Controllers\Contact\Persons\TagController;
use Webkul\Admin\Http\Controllers\Contact\Related\RelatedContactController;

Route::prefix('contacts')->group(function () {

    /**
     * Route to get the next CRM CODE (max persons id + 1)
     */
    Route::get('persons/next-crm-code',[PersonController::class,'nextCrmCode'] )->name('admin.contacts.persons.next-crm-code');


    // Related Contacts CRUD (AJAX)
    Route::controller(RelatedContactController::class)->prefix('related-contacts')->group(function () {
        Route::get('/', 'index')->name('admin.contacts.related-contacts.index');
        Route::post('/', 'store')->name('admin.contacts.related-persons.store');
        Route::patch('{relatedContact}', 'update')->name('admin.contacts.related-persons.update');
        Route::delete('{relatedContact}', 'destroy')->name('admin.contacts.related-persons.destroy');

        Route::get('create', 'create')->name('admin.contacts.related-contacts.create');
        Route::post('create', 'store')->name('admin.contacts.related-contacts.store');

        Route::get('view/{id}', 'show')->name('admin.contacts.related-contacts.view');

        Route::get('edit/{id}', 'edit')->name('admin.contacts.related-contacts.edit');

        Route::put('edit/{relatedContact}', 'update')->name('admin.contacts.related-contacts.update');

        Route::middleware(['throttle:100,60'])->delete('{id}', 'destroy')->name('admin.contacts.related-contacts.delete');

    });

    /**
     * Persons routes.
     */
    Route::controller(PersonController::class)->prefix('companies')->group(function () {
        Route::get('', 'index')->name('admin.contacts.persons.index');

        Route::get('create', 'create')->name('admin.contacts.persons.create');

        Route::post('create', 'store')->name('admin.contacts.persons.store');

        Route::get('view/{id}', 'show')->name('admin.contacts.persons.view');

        Route::get('edit/{id}', 'edit')->name('admin.contacts.persons.edit');

        Route::put('edit/{id}', 'update')->name('admin.contacts.persons.update');

        Route::get('search', 'search')->name('admin.contacts.persons.search');

        Route::middleware(['throttle:100,60'])->delete('{id}', 'destroy')->name('admin.contacts.persons.delete');

        Route::post('mass-destroy', 'massDestroy')->name('admin.contacts.persons.mass_delete');


        Route::post('duplicate/{id}', 'duplicate')->name('admin.contacts.persons.duplicate');


        /**
         * Tag routes.
         */
        Route::controller(TagController::class)->prefix('{id}/tags')->group(function () {
            Route::post('', 'attach')->name('admin.contacts.persons.tags.attach');

            Route::delete('', 'detach')->name('admin.contacts.persons.tags.detach');
        });

        /**
         * Activity routes.
         */
        Route::controller(ActivityController::class)->prefix('{id}/activities')->group(function () {
            Route::get('', 'index')->name('admin.contacts.persons.activities.index');
        });
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
