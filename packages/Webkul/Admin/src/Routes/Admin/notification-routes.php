<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\NotificationController;

Route::controller(NotificationController::class)->prefix('notifications')->group(function () {
    Route::get('', 'index')->name('admin.notifications.index');

    Route::post('read/{id}', 'markAsRead')->name('admin.notifications.read');

    Route::post('read-all', 'markAllAsRead')->name('admin.notifications.read_all');
});
