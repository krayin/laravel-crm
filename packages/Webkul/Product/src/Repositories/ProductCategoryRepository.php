<?php

namespace Webkul\Product\Repositories;

use Illuminate\Container\Container;
use Webkul\Core\Eloquent\Repository;
use Webkul\Product\Contracts\ProductCategory;

class ProductCategoryRepository extends Repository
{
    /**
     * Searchable fields.
     */
    protected $fieldSearchable = [
        'name',
        'description',
    ];

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
    }

    /**
     * Specify model class name.
     *
     * @return mixed
     */
    public function model()
    {
        return ProductCategory::class;
    }

    /**
     * Create a new category.
     *
     * @param  array  $data
     * @return \Webkul\Product\Contracts\ProductCategory
     */
    public function create(array $data)
    {
        $category = parent::create($data);
        
        // Update full_name and parent_path
        $category->updateParentPath();
        $category->update(['full_name' => $category->getFullNameAttribute(null)]);
        
        return $category;
    }

    /**
     * Update an existing category.
     *
     * @param  array  $data
     * @param  int  $id
     * @param  array  $attributes
     * @return \Webkul\Product\Contracts\ProductCategory
     */
    public function update(array $data, $id, $attributes = [])
    {
        $category = parent::update($data, $id);
        
        // Update hierarchy paths if parent changed
        if (isset($data['parent_id'])) {
            $category->updateParentPath();
            $category->update(['full_name' => $category->getFullNameAttribute(null)]);
        }
        
        return $category;
    }

    /**
     * Get root categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRootCategories()
    {
        return $this->model->roots()->active()->orderBy('sort_order')->get();
    }

    /**
     * Get category tree structure.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCategoryTree()
    {
        return $this->getRootCategories()->map(function ($category) {
            return $this->buildCategoryTree($category);
        });
    }

    /**
     * Build category tree recursively.
     *
     * @param  \Webkul\Product\Contracts\ProductCategory  $category
     * @return array
     */
    protected function buildCategoryTree($category)
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'full_name' => $category->full_name,
            'product_count' => $category->products()->count(),
            'children' => $category->children->map(function ($child) {
                return $this->buildCategoryTree($child);
            })
        ];
    }

    /**
     * Move category to a new parent.
     *
     * @param  int  $categoryId
     * @param  int|null  $newParentId
     * @return \Webkul\Product\Contracts\ProductCategory
     * @throws \InvalidArgumentException
     */
    public function moveCategory($categoryId, $newParentId = null)
    {
        $category = $this->find($categoryId);
        
        if ($newParentId && $category->isAncestorOf($this->find($newParentId))) {
            throw new \InvalidArgumentException('Cannot move category to its own descendant');
        }
        
        $category->update(['parent_id' => $newParentId]);
        $category->updateParentPath();
        
        return $category;
    }

    /**
     * Get categories with product count.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCategoriesWithProductCount()
    {
        return $this->model->withCount('products')->get();
    }

    /**
     * Get category breadcrumbs.
     *
     * @param  int  $categoryId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBreadcrumbs($categoryId)
    {
        $category = $this->find($categoryId);
        
        if (!$category) {
            return collect();
        }
        
        $breadcrumbs = $category->getAncestors();
        $breadcrumbs->push($category);
        
        return $breadcrumbs;
    }

    /**
     * Get categories by depth level.
     *
     * @param  int  $depth
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCategoriesByDepth($depth = 0)
    {
        if ($depth === 0) {
            return $this->getRootCategories();
        }
        
        return $this->model->active()
            ->whereHas('parent', function ($query) use ($depth) {
                $this->addDepthCondition($query, $depth - 1);
            })
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Add depth condition to query recursively.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $depth
     * @return void
     */
    protected function addDepthCondition($query, $depth)
    {
        if ($depth === 0) {
            $query->whereNull('parent_id');
        } else {
            $query->whereHas('parent', function ($q) use ($depth) {
                $this->addDepthCondition($q, $depth - 1);
            });
        }
    }

    /**
     * Reorder categories within a parent.
     *
     * @param  array  $categoryIds
     * @param  int|null  $parentId
     * @return bool
     */
    public function reorderCategories(array $categoryIds, $parentId = null)
    {
        foreach ($categoryIds as $index => $categoryId) {
            $this->update([
                'sort_order' => $index + 1,
                'parent_id' => $parentId
            ], $categoryId);
        }
        
        return true;
    }

    /**
     * Get active categories for dropdown/select.
     *
     * @param  int|null  $excludeId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveCategoriesForSelect($excludeId = null)
    {
        $query = $this->model->active()->orderBy('full_name');
        
        if ($excludeId) {
            $excludeCategory = $this->find($excludeId);
            if ($excludeCategory) {
                $excludeIds = $excludeCategory->getDescendants()->pluck('id')->push($excludeId);
                $query->whereNotIn('id', $excludeIds);
            }
        }
        
        return $query->get();
    }

    /**
     * Search categories by name.
     *
     * @param  string  $searchTerm
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchCategories($searchTerm)
    {
        return $this->model->active()
            ->where('name', 'LIKE', "%{$searchTerm}%")
            ->orWhere('full_name', 'LIKE', "%{$searchTerm}%")
            ->orderBy('full_name')
            ->get();
    }

    /**
     * Delete category and handle children.
     *
     * @param  int  $id
     * @param  bool  $moveChildrenToParent
     * @return bool
     */
    public function deleteCategory($id, $moveChildrenToParent = true)
    {
        $category = $this->find($id);
        
        if (!$category) {
            return false;
        }
        
        if ($moveChildrenToParent) {
            // Move children to parent
            $category->allChildren()->update([
                'parent_id' => $category->parent_id
            ]);
            
            // Update paths for moved children
            $category->allChildren->each(function ($child) {
                $child->updateParentPath();
            });
        }
        
        return $category->delete();
    }
}