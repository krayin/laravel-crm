<?php

use Webkul\Core\Acl;
use Webkul\Core\Core;
use Webkul\Core\Menu;
use Webkul\Core\SystemConfig;
use Webkul\Core\ViewRenderEventManager;

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

if (! function_exists('system_config')) {
    /**
     * System Config helper.
     */
    function system_config(): SystemConfig
    {
        return app('system_config');
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
