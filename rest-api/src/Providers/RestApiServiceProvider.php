<?php

namespace Webkul\RestApi\Providers;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Webkul\RestApi\Exceptions\Handler;

class RestApiServiceProvider extends ServiceProvider
{
    /**
     * Register your middleware aliases here.
     *
     * @var array
     */
    protected $middlewareAliases = [
        'sanctum.admin' => \Webkul\RestApi\Middleware\AdminMiddleware::class,
    ];

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->activateMiddlewareAliases();

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'rest-api');

        $this->publishes([
            __DIR__.'/../Config/l5-swagger.php' => config_path('l5-swagger.php'),
        ], 'krayin-rest-api-swagger');

        $this->app->singleton(ExceptionHandler::class, Handler::class);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mapApiRoutes();

        $this->registerCommands();
    }

    /**
     * Define the "api" routes for the application.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->group(__DIR__.'/../Routes/api.php');
    }

    /**
     * Register commands.
     *
     * @return void
     */
    public function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Webkul\RestApi\Console\Commands\Install::class,
            ]);
        }
    }

    /**
     * Activate middleware aliases.
     *
     * @return void
     */
    protected function activateMiddlewareAliases()
    {
        collect($this->middlewareAliases)->each(function ($className, $alias) {
            $this->app['router']->aliasMiddleware($alias, $className);
        });
    }
}
