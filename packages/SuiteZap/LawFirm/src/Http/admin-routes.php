<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use SuiteZap\LawFirm\Http\Controllers\Admin\ProcessoController;
use SuiteZap\LawFirm\Http\Controllers\Admin\PrazoController;
use SuiteZap\LawFirm\Http\Controllers\FinancialController;

/*
|--------------------------------------------------------------------------
| LawFirm Package - Admin Routes
|--------------------------------------------------------------------------
|
| Rotas administrativas do pacote LawFirm.
| Namespace de views: 'lawfirm'
|
*/

// ============================================================================
// ROTA DE DEBUG - Verificação do Status do Pacote
// Acesse: http://localhost/admin/lawfirm/debug-status
// ============================================================================
Route::middleware(['web'])
    ->prefix(config('app.admin_path', 'admin') . '/lawfirm')
    ->group(function () {

        // Rota de diagnóstico simples (sem autenticação para facilitar debug)
        Route::get('debug-status', function () {
            Log::info('LawFirm: Rota debug-status acessada com sucesso!');
            return response('LawFirm Package is ACTIVE', 200)
                ->header('Content-Type', 'text/plain');
        })->name('admin.lawfirm.debug_status');

    });

// ============================================================================
// ROTAS PRINCIPAIS DO JURÍDICO - Com Autenticação Completa
// ============================================================================
Route::middleware(['web', 'admin_locale', 'user'])
    ->prefix(config('app.admin_path', 'admin') . '/juridico')
    ->group(function () {

        // -----------------------------------------------
        // Processos Routes
        // -----------------------------------------------
        Route::prefix('processos')->controller(ProcessoController::class)->group(function () {
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

            // Rotas para abas específicas (DataGrids filtrados)
            Route::get('leads/processos/{id}', 'leadProcessos')->name('admin.leads.processos');
            Route::get('contacts/persons/processos/{id}', 'personProcessos')->name('admin.contacts.persons.processos');
            Route::get('contacts/organizations/processos/{id}', 'organizationProcessos')->name('admin.contacts.organizations.processos');
        });

        // -----------------------------------------------
        // Prazos Routes
        // -----------------------------------------------
        Route::prefix('prazos')->controller(PrazoController::class)->group(function () {
            Route::get('', 'index')->name('admin.prazos.index');
            Route::post('store', 'store')->name('admin.prazos.store');
            Route::get('{id}/edit', 'edit')->name('admin.prazos.edit');
            Route::put('{id}', 'update')->name('admin.prazos.update');
            Route::put('{id}/concluir', 'concluir')->name('admin.prazos.concluir');
            Route::delete('{id}', 'destroy')->name('admin.prazos.destroy');
        });
    });

// ============================================================================
// ROTAS LEGADO / COMPATIBILIDADE - /admin/lawfirm
// ============================================================================
Route::middleware(['web', 'user'])
    ->prefix('admin/lawfirm')
    ->group(function () {

        // Dashboard LawFirm
        Route::get('/', function () {
            return view('lawfirm::admin.index');
        })->name('admin.lawfirm.index');

        // Financial Dashboard
        Route::get('/financial', [FinancialController::class, 'index'])
            ->name('admin.lawfirm.financial.index');

        // Quick Pay (Baixa Rápida)
        Route::post('/financial/quick-pay/{id}', [FinancialController::class, 'quickPay'])
            ->name('admin.lawfirm.financial.quick_pay');

        // PDF Receipt (Recibo)
        Route::get('/financial/receipt/{id}', [FinancialController::class, 'downloadReceipt'])
            ->name('admin.lawfirm.financial.receipt');

        // DEBUG - Verifica se as views estão registradas
        Route::get('debug-view', function () {
            $viewName = 'lawfirm::contacts.persons.edit-extension';

            $results = [
                'message' => 'SUCESSO: O arquivo admin-routes.php foi carregado corretamente!',
                'view_name' => $viewName,
                'exists' => \Illuminate\Support\Facades\View::exists($viewName),
                'hints' => app('view')->getFinder()->getHints(),
            ];

            if ($results['exists']) {
                try {
                    $person = new \Webkul\Contact\Models\Person();
                    $person->id = 0;
                    $results['render_test'] = 'Ready to render';
                } catch (\Exception $e) {
                    $results['render_error'] = $e->getMessage();
                }
            }

            return response()->json($results, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        })->name('admin.lawfirm.debug_view');

        // DEBUG - Verificação de Permissões
        Route::get('debug-permissions', function () {
            $user = auth()->guard('user')->user();

            if (!$user)
                return "Usuário não logado.";

            return [
                'user_name' => $user->name,
                'role_name' => $user->role->name,
                'all_permissions_in_db' => $user->role->permissions,
                'menu_keys_required' => [
                    'lawfirm',
                    'lawfirm.processos'
                ]
            ];
        });
    });
