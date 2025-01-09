<?php

use Illuminate\Support\Facades\Route;
use Webkul\OrderManagement\Http\Controllers\OrderManagementController;

// Route::middleware(['web','admin_locale','user'])->prefix(config('app.admin_path'))->group(function(){
//     Route::prefix('budget')->group(function () {
//         Route::get('', [BudgetController::class, 'index'])->name('admin.budget.index');
//         Route::get('create', [BudgetController::class, 'create'])->name('admin.budget.create');
//         Route::get('view/{id}', [BudgetController::class, 'view'])->name('admin.budget.view');
//         Route::post('store', [BudgetController::class, 'store'])->name('admin.budget.store');
//         Route::get('edit/{id}', [BudgetController::class, 'edit'])->name('admin.budget.edit');
//         Route::put('update/{id}', [BudgetController::class, 'update'])->name('admin.budget.update');
//         Route::delete('delete/{id}', [BudgetController::class, 'destroy'])->name('admin.budget.delete');
//         Route::post('mass_delete', [BudgetController::class, 'massDestroy'])->name('admin.budget.mass_delete');
//     });
// });


Route::middleware(['web','admin_locale','user'])->prefix(config('app.admin_path'))->group(function(){
    Route::prefix('orderManagement')->group(function () {
        Route::get('', [OrderManagementController::class, 'index'])->name('admin.ordermanagement.index');
        // regular booking
    Route::get('regular-booking', [OrderManagementController::class, 'regularBooking'])->name('admin.ordermanagement.regular_booking');
    Route::post('regular-booking', [OrderManagementController::class, 'storeRegularBooking'])->name('admin.ordermanagement.store_regular_booking');
    // special-price-request
    Route::get('special-price-request', [OrderManagementController::class, 'specialPriceRequest'])->name('admin.ordermanagement.special_price_request');
    Route::post('special-price-request', [OrderManagementController::class, 'storeSpecialPriceRequest'])->name('admin.ordermanagement.store_special_price_request');

    // complete order
    Route::get('complete-order', [OrderManagementController::class, 'completeOrder'])->name('admin.ordermanagement.complete_order');
    Route::post('complete-order', [OrderManagementController::class, 'storeCompleteOrder'])->name('admin.ordermanagement.store_complete_order');

    // cancel order
    Route::get('cancel-order', [OrderManagementController::class, 'cancelOrder'])->name('admin.ordermanagement.cancel_order');
    Route::post('cancel-order', [OrderManagementController::class, 'storeCancelOrder'])->name('admin.ordermanagement.store_cancel_order');

    //tracking
    Route::get('tracking', [OrderManagementController::class, 'tracking'])->name('admin.ordermanagement.tracking');
    Route::post('tracking', [OrderManagementController::class, 'storeTracking'])->name('admin.ordermanagement.store_tracking');

    });

    Route::prefix('createOrder')->group(function () {
        Route::get('', [OrderManagementController::class, 'create'])->name('admin.ordermanagement.create');
    });
});
