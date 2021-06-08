<?php

namespace Webkul\Core;

class Tree {

    /**
     * Contains tree item
     *
     * @var array
     */
	public $items = [];

    /**
     * Contains acl roles
     *
     * @var array
     */
	public $roles = [];

	/**
	 * Shortcut method for create a Config with a callback.
	 * This will allow you to do things like fire an event on creation.
	 *
	 * @param  callable  $callback Callback to use after the Config creation
	 * @return object
	 */
	public static function create($callback = null)
	{
		$tree = new Tree();

		if ($callback) {
			$callback($tree);
		}

		return $tree;
	}

	/**
	 * Add a Config item to the item stack
	 *
	 * @param  string  $item
	 * @return void
	 */
	public function add($item, $type = '')
	{
		$item['children'] = [];
		
		$item['name'] = trans($item['name']);

		$children = str_replace('.', '.children.', $item['key']);

		if ($type == 'acl') {
			if (is_array($item['route'])) {
				foreach ($item['route'] as $route) {
					$this->roles[$route] = $item['key'];
				}
			} else {
				$this->roles[$item['route']] = $item['key'];
			}
		}

		core()->array_set($this->items, $children, $item);
	}
}
