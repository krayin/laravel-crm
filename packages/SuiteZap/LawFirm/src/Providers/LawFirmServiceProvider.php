<?php

namespace SuiteZap\LawFirm\Providers;

use Illuminate\Support\ServiceProvider;

class LawFirmServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // 1. Carregar Rotas
        $this->loadRoutesFrom(__DIR__ . '/../Routes/admin.php');

        if (file_exists(__DIR__ . '/../Routes/api.php')) {
            $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
        }

        // 2. Carregar Views
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'lawfirm');

        // 3. Carregar Migrações
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        // 4. Carregar Traduções
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'lawfirm');


        // 5. Registrar Observers (Mantendo funcionalidade crítica)
        $this->registerObservers();

        // 6. Registrar Event Listeners
        \Illuminate\Support\Facades\Event::listen('sales.lead.update.after', 'SuiteZap\LawFirm\Listeners\LeadUpdatedListener@handle');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Fazer o merge da configuração do menu
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/menu.php',
            'menu.admin'
        );
    }

    /**
     * Register model observers.
     *
     * @return void
     */
    protected function registerObservers()
    {
        \SuiteZap\LawFirm\Models\Processo::observe(
            \SuiteZap\LawFirm\Observers\ProcessoObserver::class
        );
    }
}