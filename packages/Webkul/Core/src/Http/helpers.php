<?php
use Webkul\Core\ViewRenderEventManager;

use Webkul\Core\Core;
use Webkul\Core\Menu;
use Webkul\Core\Acl;

if (! function_exists('core')) {
    function core(): Core
    {
        return app('core');
    }
}

if (! function_exists('menu')) {
    function menu(): Menu
    {
        return app('menu');
    }
}

if (! function_exists('acl')) {
    /**
     * Acl helper.
     */
    function acl(): Acl
    {
        return app('acl');
    }
}

if (! function_exists('view_render_event')) {
    function view_render_event($eventName, $params = null)
    {
        app()->singleton(ViewRenderEventManager::class);

        $viewEventManager = app()->make(ViewRenderEventManager::class);

        $viewEventManager->handleRenderEvent($eventName, $params);

        return $viewEventManager->render();
    }
}
?>