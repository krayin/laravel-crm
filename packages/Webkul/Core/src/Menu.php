<?php

namespace Webkul\Core;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Webkul\Core\Menu\MenuItem;

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
                name: trans($menuItem['name']),
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
                    name: trans($subMenuItem['name']),
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
