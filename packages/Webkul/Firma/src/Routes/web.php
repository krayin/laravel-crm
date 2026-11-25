<?php

use Illuminate\Support\Facades\Route;
use Webkul\Firma\Http\Controllers\WebhookController;

/**
 * Firma webhook deliveries. Outside the web middleware group on purpose: Firma signs each
 * delivery (verified in the controller), and a session or a CSRF token does not apply.
 */
Route::post('firma/webhook', WebhookController::class)->name('firma.webhook');
