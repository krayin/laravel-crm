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

        foreach (config('menu.admin') as $index => $item) {
            if (! bouncer()->hasPermission($item['key'])) {
                continue;
            }

            $item['url'] = route($item['route'], $item['params'] ?? []);

			if (strpos($this->current, $item['url']) !== false) {
                $this->currentKey = $item['key'];
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