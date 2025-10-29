<?php

namespace Webkul\Product\Contracts;

interface ProductCategory
{
    /**
     * Get the parent category.
     */
    public function parent();

    /**
     * Get all children categories.
     */
    public function children();

    /**
     * Get all descendant categories recursively.
     */
    public function allChildren();

    /**
     * Get the products in this category.
     */
    public function products();

    /**
     * Check if this category is an ancestor of the given category.
     */
    public function isAncestorOf(ProductCategory $category): bool;

    /**
     * Check if this category is a descendant of the given category.
     */
    public function isDescendantOf(ProductCategory $category): bool;

    /**
     * Get the depth level of this category.
     */
    public function getDepth(): int;

    /**
     * Check if this category is a root category.
     */
    public function isRoot(): bool;

    /**
     * Check if this category has children.
     */
    public function hasChildren(): bool;

    /**
     * Get all ancestor categories.
     */
    public function getAncestors();

    /**
     * Get all descendant categories.
     */
    public function getDescendants();

    /**
     * Update the parent path for hierarchy navigation.
     */
    public function updateParentPath(): void;

    /**
     * Scope to filter only active categories.
     */
    public function scopeActive($query);

    /**
     * Scope to filter only root categories.
     */
    public function scopeRoots($query);
}