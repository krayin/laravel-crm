<?php

namespace Webkul\Core;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Webkul\Core\Menu\MenuItem;
use Webkul\Core\Models\CoreConfig;

class Menu
{
    /**
     * Menu items.
     */
    private array $items = [];

    /**
     * Config menu.
     */
    private array $configMenu = [];

    /**
     * Contains current item key.
     */
    private string $currentKey = '';

    /**
     * Per-request cache of the custom menu names configured under
     * `general.settings.menu`, keyed by menu key. Labels are read many times
     * per page (sidebar, breadcrumbs, page title), so the whole group is
     * loaded once instead of querying per key.
     */
    private ?array $configuredNames = null;

    /**
     * Menu area for admin.
     */
    const ADMIN = 'admin';

    /**
     * Menu area for customer.
     */
    const CUSTOMER = 'customer';

    /**
     * Add a new menu item.
     */
    public function addItem(MenuItem $menuItem): void
    {
        $this->items[] = $menuItem;
    }

    /**
     * Get all menu items.
     */
    public function getItems(?string $area = null, string $key = ''): Collection
    {
        if (! $area) {
            throw new \Exception('Area must be provided to get menu items.');
        }

        static $items;

        if ($items) {
            return $items;
        }

        $configMenu = collect(config("menu.$area"))->map(function ($item) {
            return Arr::except([
                ...$item,
                'url' => route($item['route'], $item['params'] ?? []),
            ], ['params']);
        });

        switch ($area) {
            case self::ADMIN:
                $this->configMenu = $configMenu
                    ->filter(fn ($item) => bouncer()->hasPermission($item['key']))
                    ->toArray();
                break;

            default:
                $this->configMenu = $configMenu->toArray();

                break;
        }

        if (! $this->items) {
            $this->prepareMenuItems();
        }

        $items = collect($this->items)->sortBy(fn ($item) => $item->getPosition());

        return $items;
    }

    /**
     * Get admin menu by key or keys.
     */
    public function getAdminMenuByKey(array|string $keys): mixed
    {
        $items = $this->getItems('admin');

        $keysArray = (array) $keys;

        $filteredItems = $items->filter(fn ($item) => in_array($item->getKey(), $keysArray));

        return is_array($keys) ? $filteredItems : $filteredItems->first();
    }

    /**
     * Resolve the display label for a menu key.
     *
     * Honours the rename configured under `general.settings.menu`, falling back
     * to the given translation key when no custom name has been set. Every
     * surface that shows a menu label must go through here so a rename applies
     * consistently, not just in the sidebar.
     */
    public function getLabel(string $key, ?string $fallbackTransKey = null): string
    {
        if ($this->configuredNames === null) {
            $this->configuredNames = $this->loadConfiguredNames();
        }

        if (filled($this->configuredNames[$key] ?? null)) {
            return $this->configuredNames[$key];
        }

        return $fallbackTransKey ? trans($fallbackTransKey) : '';
    }

    /**
     * Load every custom menu name in a single query, keyed by menu key.
     */
    private function loadConfiguredNames(): array
    {
        $prefix = 'general.settings.menu.';

        return CoreConfig::query()
            ->where('code', 'like', $prefix.'%')
            ->pluck('value', 'code')
            ->mapWithKeys(fn ($value, $code) => [Str::after($code, $prefix) => $value])
            ->all();
    }

    /**
     * Prepare menu items.
     */
    private function prepareMenuItems(): void
    {
        $menuWithDotNotation = [];

        foreach ($this->configMenu as $item) {
            if (strpos(request()->url(), route($item['route'])) !== false) {
                $this->currentKey = $item['key'];
            }

            $menuWithDotNotation[$item['key']] = $item;
        }

        $menu = Arr::undot(Arr::dot($menuWithDotNotation));

        foreach ($menu as $menuItemKey => $menuItem) {
            $this->addItem(new MenuItem(
                key: $menuItemKey,
                name: $this->getLabel($menuItemKey, $menuItem['name']),
                route: $menuItem['route'],
                url: $menuItem['url'],
                sort: $menuItem['sort'],
                icon: $menuItem['icon-class'],
                info: trans($menuItem['info'] ?? ''),
                children: $this->processSubMenuItems($menuItem),
            ));
        }
    }

    /**
     * Process sub menu items.
     */
    private function processSubMenuItems($menuItem): Collection
    {
        return collect($menuItem)
            ->sortBy('sort')
            ->filter(fn ($value) => is_array($value))
            ->map(function ($subMenuItem) {
                $subSubMenuItems = $this->processSubMenuItems($subMenuItem);

                return new MenuItem(
                    key: $subMenuItem['key'],
                    name: $this->getLabel($subMenuItem['key'], $subMenuItem['name']),
                    route: $subMenuItem['route'],
                    url: $subMenuItem['url'],
                    sort: $subMenuItem['sort'],
                    icon: $subMenuItem['icon-class'],
                    info: trans($subMenuItem['info'] ?? ''),
                    children: $subSubMenuItems,
                );
            });
    }

    /**
     * Get current active menu.
     */
    public function getCurrentActiveMenu(?string $area = null): ?MenuItem
    {
        $currentKey = implode('.', array_slice(explode('.', $this->currentKey), 0, 2));

        return $this->findMatchingItem($this->getItems($area), $currentKey);
    }

    /**
     * Finding the matching item.
     */
    private function findMatchingItem($items, $currentKey): ?MenuItem
    {
        foreach ($items as $item) {
            if ($item->key == $currentKey) {
                return $item;
            }

            if ($item->haveChildren()) {
                $matchingChild = $this->findMatchingItem($item->getChildren(), $currentKey);

                if ($matchingChild) {
                    return $matchingChild;
                }
            }
        }

        return null;
    }
}
