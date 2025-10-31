<?php

namespace Webkul\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Product\Models\ProductCategory;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'code' => 'electronics',
                'description' => 'Electronic devices and components',
                'is_active' => true,
                'sort_order' => 1,
                'children' => [
                    [
                        'name' => 'Smartphones',
                        'code' => 'smartphones',
                        'description' => 'Mobile phones and smartphones',
                        'is_active' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Laptops',
                        'code' => 'laptops',
                        'description' => 'Laptop computers and notebooks',
                        'is_active' => true,
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Tablets',
                        'code' => 'tablets',
                        'description' => 'Tablet computers',
                        'is_active' => true,
                        'sort_order' => 3,
                    ],
                ]
            ],
            [
                'name' => 'Clothing',
                'code' => 'clothing',
                'description' => 'Apparel and clothing items',
                'is_active' => true,
                'sort_order' => 2,
                'children' => [
                    [
                        'name' => 'Men\'s Clothing',
                        'code' => 'mens-clothing',
                        'description' => 'Clothing for men',
                        'is_active' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Women\'s Clothing',
                        'code' => 'womens-clothing',
                        'description' => 'Clothing for women',
                        'is_active' => true,
                        'sort_order' => 2,
                    ],
                ]
            ],
            [
                'name' => 'Home & Garden',
                'code' => 'home-garden',
                'description' => 'Home improvement and garden items',
                'is_active' => true,
                'sort_order' => 3,
                'children' => [
                    [
                        'name' => 'Furniture',
                        'code' => 'furniture',
                        'description' => 'Home and office furniture',
                        'is_active' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Kitchen',
                        'code' => 'kitchen',
                        'description' => 'Kitchen appliances and utensils',
                        'is_active' => true,
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Garden Tools',
                        'code' => 'garden-tools',
                        'description' => 'Tools and equipment for gardening',
                        'is_active' => true,
                        'sort_order' => 3,
                    ],
                ]
            ],
            [
                'name' => 'Sports & Recreation',
                'code' => 'sports-recreation',
                'description' => 'Sports equipment and recreational items',
                'is_active' => true,
                'sort_order' => 4,
                'children' => [
                    [
                        'name' => 'Fitness Equipment',
                        'code' => 'fitness-equipment',
                        'description' => 'Exercise and fitness equipment',
                        'is_active' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Outdoor Sports',
                        'code' => 'outdoor-sports',
                        'description' => 'Equipment for outdoor sports',
                        'is_active' => true,
                        'sort_order' => 2,
                    ],
                ]
            ],
        ];

        $this->createCategories($categories);
    }

    /**
     * Create categories recursively
     */
    private function createCategories(array $categories, ?int $parentId = null): void
    {
        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);
            
            $categoryData['parent_id'] = $parentId;
            
            $category = ProductCategory::create($categoryData);
            
            // Build the full name and parent path
            $this->updateCategoryPaths($category);
            
            if (!empty($children)) {
                $this->createCategories($children, $category->id);
            }
        }
    }

    /**
     * Update category full name and parent path
     */
    private function updateCategoryPaths(ProductCategory $category): void
    {
        if ($category->parent_id) {
            $parent = ProductCategory::find($category->parent_id);
            if ($parent) {
                $category->full_name = $parent->full_name . ' > ' . $category->name;
                $category->parent_path = $parent->parent_path . '/' . $parent->id;
            }
        } else {
            $category->full_name = $category->name;
            $category->parent_path = '';
        }
        
        $category->saveQuietly(); // Save without triggering events
    }
}