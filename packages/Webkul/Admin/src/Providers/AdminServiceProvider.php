<?php

namespace Webkul\Admin\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Debug\ExceptionHandler;

use Webkul\Core\Tree;
use Webkul\Admin\Menu;
use Webkul\Admin\Bouncer;
use Webkul\Admin\Exceptions\Handler;
use Webkul\Admin\Facades\Menu as MenuFacade;
use Webkul\Admin\Facades\Bouncer as BouncerFacade;
use Illuminate\Database\Eloquent\Relations\Relation;
use Webkul\Admin\Http\Middleware\Bouncer as BouncerMiddleware;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        include __DIR__ . '/../Http/helpers.php';

        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'admin');

        $this->publishes([
            __DIR__ . '/../../publishable/assets' => public_path('vendor/webkul/admin/assets'),
        ], 'public');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'admin');

        $this->app->bind(ExceptionHandler::class, Handler::class);

        $router->aliasMiddleware('user', BouncerMiddleware::class);

        Relation::morphMap([
            'leads'         => 'Webkul\Lead\Models\Lead',
            'products'      => 'Webkul\Product\Models\Product',
            'organizations' => 'Webkul\Contact\Models\Organization',
            'persons'       => 'Webkul\Contact\Models\Person',
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerFacades();

        $this->registerConfig();

        $this->registerACL();
    }

    /**
     * Register Bouncer as a singleton.
     *
     * @return void
     */
    protected function registerFacades()
    {
        $loader = AliasLoader::getInstance();

        $loader->alias('Bouncer', BouncerFacade::class);
        $loader->alias('Menu', MenuFacade::class);

        $this->app->singleton('bouncer', function () {
            return new Bouncer();
        });

        $this->app->singleton('menu', function () {
            return new Menu();
        });
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/menu.php', 'menu.admin'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/acl.php', 'acl'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/entity_types.php', 'attribute_entity_types'
        );
    }
    
    /**
     * Registers acl to entire application
     *
     * @return void
     */
    public function registerACL()
    {
        $this->app->singleton('acl', function () {
            return $this->createACL();
        });
    }

    /**
     * Create acl tree
     *
     * @return mixed
     */
    public function createACL()
    {
        static $tree;

        if ($tree) {
            return $tree;
        }

        $tree = Tree::create();

        foreach (config('acl') as $item) {
            $tree->add($item, 'acl');
        }

        $tree->items = core()->sortItems($tree->items);

        return $tree;
    }
}