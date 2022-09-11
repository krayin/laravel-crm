<?php

namespace Webkul\Admin;

use Illuminate\Support\Facades\Request;
use Webkul\Core\Tree;

class Menu
{
    /**
     * Contains current item route
     *
     * @var string
     */
	public $current;

    /**
     * Contains current item key
     *
     * @var string
     */
	public $currentKey;

    /**
     * Create a new instance.
     *
     * @return void
     */
	public function __construct()
	{
		$this->current = Request::url();
    }
    
    /**
     * Prepare menu.
     *
     * @return void
     */
    public function prepare()
    {
        $tree = Tree::create();

        $userRole = auth()->guard('user')->user()->role;

        $adminMenus = config('menu.admin');

        $currentUserRole = auth()->guard('user')->user()->role;

        foreach ($adminMenus as $index => $item) {
            if (! bouncer()->hasPermission($item['key'])) {
                continue;
            }

            if (isset($item['route'])) {
                $item['url'] = route($item['route'], $item['params'] ?? []);

                if (strpos($this->current, $item['url']) !== false) {
                    $this->currentKey = $item['key'];
                }
            }

            if ($item['key'] != 'settings'
                && $index + 1 < count(config('menu.admin'))
                && $currentUserRole->permission_type == 'custom'
            ) {
                $permission = config('menu.admin')[$index + 1];

                if (substr_count($permission['key'], '.') == 1
                    && substr_count($item['key'], '.') == 0
                ) {
                    foreach ($currentUserRole->permissions as $key => $value) {
                        if ($item['key'] != $value) {
                            continue;
                        }

                        $neededItem = $currentUserRole->permissions[$key + 1];

                        foreach (config('menu.admin') as $key1 => $findMatched) {
                            if ($findMatched['key'] != $neededItem) {
                                continue;
                            }

                            $item['route'] = $findMatched['route'];

                            $item['url'] = route($findMatched['route'], $findMatched['params'] ?? []);
                        }
                    }
                }
            }

            $tree->add($item, 'menu');
        }


        $tree->items = core()->sortItems($tree->items);

        return $tree;
    }

	/**
	 * Method to find the active links
	 *
	 * @param  array  $item
	 * @return string|void
	 */
	public function getActive($item)
	{
		$url = trim($item['url'], '/');

		if ((strpos($this->current, $url) !== false) || (strpos($this->currentKey, $item['key']) === 0)) {
			return 'active';
		}
	}
}