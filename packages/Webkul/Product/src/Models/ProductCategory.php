<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Activity\Traits\LogsActivity;
use Webkul\Attribute\Traits\CustomAttribute;
use Webkul\Product\Contracts\ProductCategory as ProductCategoryContract;
use Webkul\Product\Models\ProductProxy;
use Webkul\User\Models\UserProxy;

class ProductCategory extends Model implements ProductCategoryContract
{
    use CustomAttribute, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'full_name',
        'parent_path',
        'description',
        'image',
        'is_active',
        'sort_order',
        'parent_id',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the parent category.
     */
    public function parent()
    {
        return $this->belongsTo(ProductCategoryProxy::modelClass(), 'parent_id');
    }

    /**
     * Get the active children categories.
     */
    public function children()
    {
        return $this->hasMany(ProductCategoryProxy::modelClass(), 'parent_id')
                    ->where('is_active', true)
                    ->orderBy('sort_order');
    }

    /**
     * Get all children categories (including inactive).
     */
    public function allChildren()
    {
        return $this->hasMany(ProductCategoryProxy::modelClass(), 'parent_id');
    }

    /**
     * Get the products in this category.
     */
    public function products()
    {
        return $this->hasMany(ProductProxy::modelClass(), 'category_id');
    }

    /**
     * Get the user who created this category.
     */
    public function creator()
    {
        return $this->belongsTo(UserProxy::modelClass(), 'created_by');
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include root categories.
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get the full_name attribute.
     */
    public function getFullNameAttribute($value)
    {
        if ($value) {
            return $value;
        }

        // Auto-generate full name from hierarchy
        $names = collect();
        $category = $this;
        
        while ($category) {
            $names->prepend($category->name);
            $category = $category->parent;
        }
        
        return $names->implode(' > ');
    }

    /**
     * Update parent path for this category and its children.
     */
    public function updateParentPath(): void
    {
        $path = collect();
        $category = $this->parent;
        
        while ($category) {
            $path->prepend($category->id);
            $category = $category->parent;
        }
        
        $this->parent_path = $path->implode('/');
        $this->save();
        
        // Update children paths recursively
        $this->allChildren->each(function ($child) {
            $child->updateParentPath();
        });
    }

    /**
     * Get all ancestor categories.
     */
    public function getAncestors()
    {
        $ancestors = collect();
        $category = $this->parent;
        
        while ($category) {
            $ancestors->prepend($category);
            $category = $category->parent;
        }
        
        return $ancestors;
    }

    /**
     * Get all descendant categories.
     */
    public function getDescendants()
    {
        $descendants = collect();
        
        $this->allChildren->each(function ($child) use ($descendants) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getDescendants());
        });
        
        return $descendants;
    }

    /**
     * Check if this category is an ancestor of the given category.
     */
    public function isAncestorOf(ProductCategoryContract $category): bool
    {
        return $category->getAncestors()->contains('id', $this->id);
    }

    /**
     * Check if this category is a descendant of the given category.
     */
    public function isDescendantOf(ProductCategoryContract $category): bool
    {
        return $this->getAncestors()->contains('id', $category->id);
    }

    /**
     * Get the depth level of this category.
     */
    public function getDepth(): int
    {
        return $this->getAncestors()->count();
    }

    /**
     * Check if this category is a root category.
     */
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Check if this category has children.
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }
}