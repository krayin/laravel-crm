<?php

Route::group([
        'prefix'        => 'admin/category',
        'middleware'    => ['web', 'user']
    ], function () {

        Route::get('', 'Webkul\Category\Http\Controllers\CategoryController@index')->name('admin.category.index');

});