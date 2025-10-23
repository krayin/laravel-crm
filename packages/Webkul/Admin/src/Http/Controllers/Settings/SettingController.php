<?php

namespace Webkul\Admin\Http\Controllers\Settings;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Core\Menu\MenuItem;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin::settings.index');
    }

    /**
     * Search for settings.
     */
    public function search(): ?JsonResponse
    {
        $query = strtolower(request()->query('query'));

        if (empty($query)) {
            return response()->json(['data' => []]);
        }

        $results = $this->searchMenuItems($this->getSettingsConfig(), $query);

        return response()->json([
            'data' => $results->values(),
        ]);
    }

    /**
     * Recursively search through menu items and children.
     *
     * @param  Collection<int, MenuItem>  $menuItems
     * @return Collection<int, array<string, mixed>>
     */
    protected function searchMenuItems(Collection $menuItems, string $query): Collection
    {
        $results = collect();

        foreach ($menuItems as $item) {
            if ($this->matchesQuery($item, $query)) {
                $results->push([
                    'name' => $item->getName(),
                    'url'  => $item->getUrl(),
                    'icon' => $item->getIcon(),
                    'key'  => $item->getKey(),
                ]);
            }

            if ($item->haveChildren()) {
                $childResults = $this->searchMenuItems($item->getChildren(), $query);

                $results = $results->merge($childResults);
            }
        }

        return $results;
    }

    /**
     * Determine if the menu item matches the query.
     */
    protected function matchesQuery(MenuItem $item, string $query): bool
    {
        $query = strtolower($query);
        $url = strtolower($item->getUrl());

        if (
            ! $url
            || ! str_contains($url, $query)
        ) {
            return false;
        }

        return true;
    }

    /**
     * Get the settings configuration.
     */
    protected function getSettingsConfig(): Collection
    {
        return menu()
            ->getItems('admin')
            ->filter(fn (MenuItem $item) => $item->getKey() === 'settings');
    }
}
