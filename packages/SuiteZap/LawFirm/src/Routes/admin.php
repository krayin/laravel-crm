<?php

use Illuminate\Support\Facades\Route;
use SuiteZap\LawFirm\Http\Controllers\Admin\ProcessoController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for the LawFirm module.
| These routes are loaded by the LawFirmServiceProvider.
|
*/

Route::group([
    'prefix' => 'admin/juridico',
    'middleware' => ['web', 'user'],
], function () {

    // Processos Routes
    Route::controller(ProcessoController::class)->prefix('processos')->group(function () {
        Route::get('', 'index')->name('admin.processos.index');
        Route::get('create', 'create')->name('admin.processos.create');
        Route::post('create', 'store')->name('admin.processos.store');
        Route::get('search-person', 'searchPerson')->name('admin.processos.search_person');
        Route::get('search-lead', 'searchLead')->name('admin.processos.search_lead');
        Route::get('{id}', 'show')->name('admin.processos.show');
        Route::get('{id}/edit', 'edit')->name('admin.processos.edit');
        Route::put('{id}', 'update')->name('admin.processos.update');
        Route::delete('{id}', 'destroy')->name('admin.processos.destroy');
        Route::delete('anexo/{id}', 'destroyAnexo')->name('admin.processos.delete_attachment');
        Route::post('mass-delete', 'massDestroy')->name('admin.processos.mass_delete');

        // Rotas para abas específicas
        // Rotas para abas específicas
        Route::get('leads/processos/{id}', 'leadProcessos')->name('admin.leads.processos');
        Route::get('contacts/persons/processos/{id}', 'personProcessos')->name('admin.contacts.persons.processos');
        Route::get('contacts/organizations/processos/{id}', 'organizationProcessos')->name('admin.contacts.organizations.processos');
    });

    // Prazos Routes
    Route::controller(\SuiteZap\LawFirm\Http\Controllers\Admin\PrazoController::class)->prefix('prazos')->group(function () {
        Route::get('', 'index')->name('admin.prazos.index');
        Route::post('store', 'store')->name('admin.prazos.store');
        Route::get('{id}/edit', 'edit')->name('admin.prazos.edit');
        Route::put('{id}', 'update')->name('admin.prazos.update');
        Route::put('{id}/concluir', 'concluir')->name('admin.prazos.concluir');
        Route::delete('{id}', 'destroy')->name('admin.prazos.destroy');
    });
});



// Legacy route (keeping for backward compatibility)
Route::group([
    'prefix' => 'admin/lawfirm',
    'middleware' => ['web', 'user'],
], function () {

    // Dashboard
    Route::get('/', function () {
        return view('lawfirm::admin.index');
    })->name('admin.lawfirm.index');

    // Financial Dashboard
    Route::get('/financial', [\SuiteZap\LawFirm\Http\Controllers\FinancialController::class, 'index'])
        ->name('admin.lawfirm.financial.index');

    // DEBUG ROUTE
    Route::get('/debug-view', function () {
        $viewName = 'lawfirm::contacts.persons.edit-extension';

        $results = [
            'view_name' => $viewName,
            'exists' => \Illuminate\Support\Facades\View::exists($viewName),
            'hints' => app('view')->getFinder()->getHints(),
        ];

        if ($results['exists']) {
            try {
                // Fake person for rendering
                $person = new \Webkul\Contact\Models\Person();
                $person->id = 0;
                return view($viewName, compact('person'));
            } catch (\Exception $e) {
                return [
                    'status' => 'Render Error',
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ];
            }
        }

        return $results;
    })->name('admin.lawfirm.debug');

    // Route::get('/cases', [CaseController::class, 'index'])->name('admin.lawfirm.cases.index');
    // Route::get('/cases/create', [CaseController::class, 'create'])->name('admin.lawfirm.cases.create');
    // Route::post('/cases', [CaseController::class, 'store'])->name('admin.lawfirm.cases.store');
    // Route::get('/cases/{id}', [CaseController::class, 'show'])->name('admin.lawfirm.cases.show');
    // Route::get('/cases/{id}/edit', [CaseController::class, 'edit'])->name('admin.lawfirm.cases.edit');
    // Route::put('/cases/{id}', [CaseController::class, 'update'])->name('admin.lawfirm.cases.update');
    // Route::delete('/cases/{id}', [CaseController::class, 'destroy'])->name('admin.lawfirm.cases.destroy');

    // Hearings Routes (Audiências)
    // Route::resource('hearings', HearingController::class)->names('admin.lawfirm.hearings');

    // Documents Routes (Documentos)
    // Route::resource('documents', DocumentController::class)->names('admin.lawfirm.documents');
});
