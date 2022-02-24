<?php
Route::group([
    'prefix'     => 'admin/web-forms',
    'namespace'  => 'Webkul\WebForm\Http\Controllers',
    'middleware' => ['web']
], function () {

    Route::get('forms/{id}/form.js', 'WebFormController@formJS')->name('admin.settings.web_forms.form_js');

    Route::get('forms/{id}/form.html', 'WebFormController@preview')->name('admin.settings.web_forms.preview');

    Route::post('forms/{id}', 'WebFormController@formStore')->name('admin.settings.web_forms.form_store');

    Route::group(['middleware' => ['user']], function () {
        Route::get('', 'WebFormController@index')->name('admin.settings.web_forms.index');

        Route::get('form/{id}/form.html', 'WebFormController@view')->name('admin.settings.web_forms.view');

        Route::get('create', 'WebFormController@create')->name('admin.settings.web_forms.create');

        Route::post('create', 'WebFormController@store')->name('admin.settings.web_forms.store');

        Route::get('edit/{id?}', 'WebFormController@edit')->name('admin.settings.web_forms.edit');

        Route::put('edit/{id}', 'WebFormController@update')->name('admin.settings.web_forms.update');

        Route::delete('{id}', 'WebFormController@destroy')->name('admin.settings.web_forms.delete');
    });
});