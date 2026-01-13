<?php

namespace SuiteZap\LawFirm\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LawFirmServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * Método executado após todos os Service Providers serem registrados.
     * Responsável por carregar rotas, views, migrações, traduções, observers e listeners.
     *
     * @return void
     */
    public function boot()
    {
        // ====================================================================
        // DEBUG: Início do Boot
        // ====================================================================
        Log::info('LawFirm: Iniciando Boot...');

        // ====================================================================
        // 1. CARREGAR ROTAS
        // ====================================================================
        $routesPath = __DIR__ . '/../Http/admin-routes.php';

        if (file_exists($routesPath)) {
            $this->loadRoutesFrom($routesPath);
            Log::info('LawFirm: Rotas carregadas.', ['path' => $routesPath]);
        } else {
            Log::error('LawFirm: ERRO - Arquivo de rotas não encontrado!', ['path' => $routesPath]);
        }

        // Rotas API (opcional)
        $apiRoutesPath = __DIR__ . '/../Routes/api.php';
        if (file_exists($apiRoutesPath)) {
            $this->loadRoutesFrom($apiRoutesPath);
            Log::info('LawFirm: Rotas API carregadas.', ['path' => $apiRoutesPath]);
        }

        // ====================================================================
        // 2. CARREGAR VIEWS
        // ====================================================================
        $viewsPath = __DIR__ . '/../Resources/views';

        if (is_dir($viewsPath)) {
            $this->loadViewsFrom($viewsPath, 'lawfirm');
            Log::info('LawFirm: Views carregadas.', ['path' => $viewsPath, 'namespace' => 'lawfirm']);
        } else {
            Log::error('LawFirm: ERRO - Diretório de views não encontrado!', ['path' => $viewsPath]);
        }

        // ====================================================================
        // 3. CARREGAR MIGRAÇÕES
        // ====================================================================
        $migrationsPath = __DIR__ . '/../Database/Migrations';

        if (is_dir($migrationsPath)) {
            $this->loadMigrationsFrom($migrationsPath);
            Log::info('LawFirm: Migrações carregadas.', ['path' => $migrationsPath]);
        }

        // ====================================================================
        // 4. CARREGAR TRADUÇÕES
        // ====================================================================
        $langPath = __DIR__ . '/../Resources/lang';

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'lawfirm');
            Log::info('LawFirm: Traduções carregadas.', ['path' => $langPath, 'namespace' => 'lawfirm']);
        }

        // ====================================================================
        // 5. REGISTRAR OBSERVERS
        // ====================================================================
        $this->registerObservers();
        Log::info('LawFirm: Observers registrados.');

        // ====================================================================
        // 6. EVENT LISTENERS - Injeções de Views
        // ====================================================================
        $this->registerEventListeners();
        Log::info('LawFirm: Event Listeners registrados.');

        // ====================================================================
        // 7. VIEW COMPOSERS
        // ====================================================================
        $this->registerViewComposers();
        Log::info('LawFirm: View Composers registrados.');

        // ====================================================================
        // 8. BREADCRUMBS
        // ====================================================================
        $breadcrumbsPath = __DIR__ . '/../Routes/breadcrumbs.php';
        if (file_exists($breadcrumbsPath)) {
            require $breadcrumbsPath;
            Log::info('LawFirm: Breadcrumbs carregados.', ['path' => $breadcrumbsPath]);
        }

        // ====================================================================
        // DEBUG: Fim do Boot
        // ====================================================================
        Log::info('LawFirm: Boot finalizado com sucesso!');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Registrar o Repository / Contract Binding
        $this->app->bind(
            \SuiteZap\LawFirm\Contracts\Processo::class,
            \SuiteZap\LawFirm\Models\Processo::class
        );

        // Merge Config (Menu)
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/menu.php',
            'menu.admin'
        );

        // Merge Config (ACL)
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/acl.php',
            'acl'
        );

        // Merge Config (System) - Configurações do Painel
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/system.php',
            'core_config'
        );
    }

    /**
     * Register model observers.
     *
     * @return void
     */
    protected function registerObservers()
    {
        \SuiteZap\LawFirm\Models\Processo::observe(\SuiteZap\LawFirm\Observers\ProcessoObserver::class);
        \SuiteZap\LawFirm\Models\Prazo::observe(\SuiteZap\LawFirm\Observers\PrazoObserver::class);
        // \Webkul\Contact\Models\PersonProxy::observe(\SuiteZap\LawFirm\Observers\PersonObserver::class);
        // \Webkul\Contact\Models\OrganizationProxy::observe(\SuiteZap\LawFirm\Observers\OrganizationObserver::class);
    }

    /**
     * Register event listeners for view injection.
     *
     * @return void
     */
    protected function registerEventListeners()
    {
        // ---------------------------------------------------------------------
        // Lead: Atualização pós-save
        // ---------------------------------------------------------------------
        Event::listen('sales.lead.update.after', 'SuiteZap\LawFirm\Listeners\LeadUpdatedListener@handle');

        // ---------------------------------------------------------------------
        // CONTATOS: Persistência de Dados (Substituindo Observers)
        // ---------------------------------------------------------------------

        // Pessoas (Create/Update) - VALIDAÇÃO NOS EVENTOS BEFORE
        Event::listen('contacts.person.create.before', function () {
            if (request()->has('law_details')) {
                $validator = Validator::make(request()->all(), [
                    'law_details.cpf' => ['nullable', new \SuiteZap\LawFirm\Rules\Cpf],
                ]);

                if ($validator->fails()) {
                    throw new ValidationException($validator);
                }
            }
        });

        Event::listen('contacts.person.update.before', function () {
            if (request()->has('law_details')) {
                $validator = Validator::make(request()->all(), [
                    'law_details.cpf' => ['nullable', new \SuiteZap\LawFirm\Rules\Cpf],
                ]);

                if ($validator->fails()) {
                    throw new ValidationException($validator);
                }
            }
        });

        // Pessoas - SALVAMENTO NOS EVENTOS AFTER (Validação já foi feita)
        Event::listen('contacts.person.create.after', function ($person) {
            if (request()->has('law_details')) {
                $data = request('law_details');
                $data['person_id'] = $person->id;
                if (!isset($data['type']))
                    $data['type'] = 'PF';

                \SuiteZap\LawFirm\Models\LawPersonDetail::updateOrCreate(['person_id' => $person->id], $data);
            }
        });

        Event::listen('contacts.person.update.after', function ($person) {
            if (request()->has('law_details')) {
                $data = request('law_details');
                $data['person_id'] = $person->id;

                \SuiteZap\LawFirm\Models\LawPersonDetail::updateOrCreate(['person_id' => $person->id], $data);
            }
        });

        // Organizações (Create/Update) - VALIDAÇÃO NOS EVENTOS BEFORE
        Event::listen('contacts.organization.create.before', function () {
            if (request()->has('law_org_details')) {
                $validator = Validator::make(request()->all(), [
                    'law_org_details.cnpj' => ['nullable', new \SuiteZap\LawFirm\Rules\Cnpj],
                ]);

                if ($validator->fails()) {
                    throw new ValidationException($validator);
                }
            }
        });

        Event::listen('contacts.organization.update.before', function () {
            if (request()->has('law_org_details')) {
                $validator = Validator::make(request()->all(), [
                    'law_org_details.cnpj' => ['nullable', new \SuiteZap\LawFirm\Rules\Cnpj],
                ]);

                if ($validator->fails()) {
                    throw new ValidationException($validator);
                }
            }
        });

        // Organizações - SALVAMENTO NOS EVENTOS AFTER (Validação já foi feita)
        Event::listen('contacts.organization.create.after', function ($organization) {
            if (request()->has('law_org_details')) {
                $data = request('law_org_details');
                $data['organization_id'] = $organization->id;
                \SuiteZap\LawFirm\Models\LawOrganizationDetail::updateOrCreate(['organization_id' => $organization->id], $data);
            }
        });

        Event::listen('contacts.organization.update.after', function ($organization) {
            if (request()->has('law_org_details')) {
                $data = request('law_org_details');
                $data['organization_id'] = $organization->id;
                \SuiteZap\LawFirm\Models\LawOrganizationDetail::updateOrCreate(['organization_id' => $organization->id], $data);
            }
        });

        // ---------------------------------------------------------------------
        // Lead: Aba Processos
        // ---------------------------------------------------------------------
        Event::listen('admin.leads.view.activities.after', function ($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('lawfirm::admin.leads.tab_processos');
            Log::debug('LawFirm: View injetada em admin.leads.view.activities.after');
        });

        // ---------------------------------------------------------------------
        // Pessoa: Aba Processos (View)
        // ---------------------------------------------------------------------
        // Event::listen('admin.contact.persons.view.right.after', function ($viewRenderEventManager) {
        //     $viewRenderEventManager->addTemplate('lawfirm::admin.contacts.persons.tab_processos');
        //     Log::debug('LawFirm: View injetada em admin.contact.persons.view.right.after');
        // });

        // ---------------------------------------------------------------------
        // Organização: Aba Processos (View - Show/Edit Tab if applicable)
        // ---------------------------------------------------------------------
        // Nota: Krayin às vezes usa nomes diferentes, ajustando se necessário.
        // Event::listen('admin.organizations.edit.form.after', function ($viewRenderEventManager) {
        //     // Este hook geralmente é fora do form, se fosse tab.
        //     // Mantendo se for aba de processos.
        //     $viewRenderEventManager->addTemplate('lawfirm::admin.contacts.organizations.tab_processos');
        //     Log::debug('LawFirm: View injetada em admin.organizations.edit.form.after');
        // });

        // ---------------------------------------------------------------------
        // PESSOA: Campos Avançados (PF) - EDIT/CREATE forms
        // ---------------------------------------------------------------------
        Event::listen('admin.contacts.persons.edit.form_controls.after', function ($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('lawfirm::contacts.persons.edit-extension');
        });

        Event::listen('admin.persons.create.form_controls.after', function ($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('lawfirm::contacts.persons.edit-extension');
        });

        // ---------------------------------------------------------------------
        // ORGANIZAÇÃO: Campos Avançados (PJ) - CREATE/EDIT forms
        // ---------------------------------------------------------------------
        Event::listen('admin.contacts.organizations.create.form_controls.after', function ($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('lawfirm::contacts.organizations.edit-extension');
        });

        Event::listen('admin.contacts.organizations.edit.form_controls.after', function ($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('lawfirm::contacts.organizations.edit-extension');
        });

        // ---------------------------------------------------------------------
        // Dashboard Widget
        // ---------------------------------------------------------------------
        Event::listen('admin.dashboard.index.open_leads_by_states.after', function ($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('lawfirm::admin.dashboard.widgets.law-firm-overview');
        });
    }

    /**
     * Register View Composers.
     *
     * @return void
     */
    protected function registerViewComposers()
    {
        // Dashboard Widget - Dados
        View::composer('lawfirm::admin.dashboard.widgets.law-firm-overview', function ($view) {
            $activeCount = \SuiteZap\LawFirm\Models\Processo::where('status', 'Ativo')->count();
            $totalValorCausa = \SuiteZap\LawFirm\Models\Processo::where('status', 'Ativo')->sum('valor_causa');
            $totalValorGanho = \SuiteZap\LawFirm\Models\Processo::whereIn('status', ['Encerrado', 'Arquivado', 'Concluído'])->sum('valor_causa');

            $upcomingHearings = \SuiteZap\LawFirm\Models\Processo::query()
                ->where('status', 'Ativo')
                ->whereNotNull('data_audiencia')
                ->whereBetween('data_audiencia', [now()->startOfDay(), now()->addDays(7)->endOfDay()])
                ->orderBy('data_audiencia', 'asc')
                ->limit(5)
                ->get();

            $view->with([
                'activeCount' => $activeCount,
                'totalValorCausa' => $totalValorCausa,
                'totalValorGanho' => $totalValorGanho,
                'upcomingHearings' => $upcomingHearings,
            ]);
        });
    }
}