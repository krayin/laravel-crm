<?php

use Illuminate\Support\Facades\Route;
use Webkul\UI\Http\Controllers\ExportController;
use Webkul\UI\Http\Controllers\ImportController;

Route::group(['middleware' => ['web', 'user']], function () {

    /**
     * DataGrid export.
     */
    Route::post('/export', [ExportController::class, 'export'])->name('ui.datagrid.export');

    /**
     * DataGrid import.
     */
    Route::post('/import', [ImportController::class, 'import'])->name('ui.datagrid.import');
});
