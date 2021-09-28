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

        $configurations = config('core_config');

        foreach ($configurations as $index => $configuration) {
            $configurations[$index]['key'] = "configuration." . $configuration['key'];
            $configurations[$index]['route'] = 'admin.configuration.index';
            $configurations[$index]['params'] = str_replace(".", "/", $configuration['key']);
        }

        // $adminMenus = array_merge(config('menu.admin'), $configurations);
        $adminMenus = config('menu.admin');

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