<?php

use Illuminate\Support\Facades\Route;
use Webkul\RestApi\Http\Controllers\V1\Setting\AttributeController;
use Webkul\RestApi\Http\Controllers\V1\Setting\EmailTemplateController;
use Webkul\RestApi\Http\Controllers\V1\Setting\GroupController;
use Webkul\RestApi\Http\Controllers\V1\Setting\PipelineController;
use Webkul\RestApi\Http\Controllers\V1\Setting\RoleController;
use Webkul\RestApi\Http\Controllers\V1\Setting\SourceController;
use Webkul\RestApi\Http\Controllers\V1\Setting\TagController;
use Webkul\RestApi\Http\Controllers\V1\Setting\TypeController;
use Webkul\RestApi\Http\Controllers\V1\Setting\UserController;
use Webkul\RestApi\Http\Controllers\V1\Setting\WebFormController;
use Webkul\RestApi\Http\Controllers\V1\Setting\WorkflowController;

Route::group([
    'prefix'     => 'settings',
    'middleware' => 'auth:sanctum',
], function () {
    /**
     * Group routes.
     */
    Route::controller(GroupController::class)->prefix('groups')->group(function () {
        Route::get('', 'index');

        Route::post('', 'store');

        Route::get('{id}', 'show')->where('id', '[0-9]+');

        Route::put('{id}', 'update');

        Route::delete('{id}', 'destroy');
    });

    /**
     * Role routes.
     */
    Route::controller(RoleController::class)->prefix('roles')->group(function () {
        Route::get('', 'index');

        Route::post('', 'store');

        Route::get('{id}', 'show')->where('id', '[0-9]+');

        Route::put('{id}', 'update');

        Route::delete('{id}', 'destroy');
    });

    /**
     * User routes.
     */
    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('', 'index');

        Route::post('', 'store');

        Route::get('{id}', 'show')->where('id', '[0-9]+');

        Route::put('{id}', 'update');

        Route::delete('{id}', 'destroy');

        Route::get('search', 'search');

        Route::post('mass-update', 'massUpdate');

        Route::post('mass-destroy', 'massDestroy');
    });

    /**
     * Lead pipeline routes.
     */
    Route::get('pipelines', [PipelineController::class, 'index']);

    Route::get('pipelines/{id}', [PipelineController::class, 'show']);

    Route::post('pipelines', [PipelineController::class, 'store']);

    Route::put('pipelines/{id}', [PipelineController::class, 'update']);

    Route::delete('pipelines/{id}', [PipelineController::class, 'destroy']);

    /**
     * Lead source routes.
     */
    Route::get('sources', [SourceController::class, 'index']);

    Route::get('sources/{id}', [SourceController::class, 'show']);

    Route::post('sources', [SourceController::class, 'store']);

    Route::put('sources/{id}', [SourceController::class, 'update']);

    Route::delete('sources/{id}', [SourceController::class, 'destroy']);

    /**
     * Lead type routes.
     */
    Route::get('types', [TypeController::class, 'index']);

    Route::get('types/{id}', [TypeController::class, 'show']);

    Route::post('types', [TypeController::class, 'store']);

    Route::put('types/{id}', [TypeController::class, 'update']);

    Route::delete('types/{id}', [TypeController::class, 'destroy']);

    /**
     * Attribute routes.
     */
    Route::get('attributes', [AttributeController::class, 'index']);

    Route::get('attributes/{id}', [AttributeController::class, 'show']);

    Route::get('attributes/lookup/{lookup?}', [AttributeController::class, 'lookup']);

    Route::post('attributes', [AttributeController::class, 'store']);

    Route::put('attributes/{id}', [AttributeController::class, 'update']);

    Route::delete('attributes/{id}', [AttributeController::class, 'destroy']);

    Route::post('attributes/mass-destroy', [AttributeController::class, 'massDestroy']);

    /**
     * Email template routes.
     */
    Route::get('email-templates', [EmailTemplateController::class, 'index']);

    Route::get('email-templates/{id}', [EmailTemplateController::class, 'show']);

    Route::post('email-templates', [EmailTemplateController::class, 'store']);

    Route::put('email-templates/{id}', [EmailTemplateController::class, 'update']);

    Route::delete('email-templates/{id}', [EmailTemplateController::class, 'destroy']);

    /**
     * Workflow routes.
     */
    Route::get('workflows', [WorkflowController::class, 'index']);

    Route::get('workflows/{id}', [WorkflowController::class, 'show']);

    Route::post('workflows', [WorkflowController::class, 'store']);

    Route::put('workflows/{id}', [WorkflowController::class, 'update']);

    Route::delete('workflows/{id}', [WorkflowController::class, 'destroy']);

    /**
     * Tag routes.
     */
    Route::controller(TagController::class)->prefix('tags')->group(function () {
        Route::get('', 'index');

        Route::post('', 'store');

        Route::get('{id}', 'show')->where('id', '[0-9]+');

        Route::put('{id}', 'update');

        Route::get('search', 'search');

        Route::delete('{id}', 'destroy');

        Route::post('mass-destroy', 'massDestroy');
    });

    /**
     * WebForms routes.
     */
    Route::controller(WebFormController::class)->prefix('web-forms')->group(function () {
        Route::get('', 'index');

        Route::post('', 'store');

        Route::get('{id}', 'show')->where('id', '[0-9]+');

        Route::put('{id}', 'update');

        Route::delete('{id}', 'destroy');
    });
});
