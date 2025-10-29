<?php

namespace Webkul\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Attribute\Models\Attribute;
use Webkul\Attribute\Models\AttributeOption;

class ProductAttributesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $attributes = [
            [
                'code' => 'material',
                'name' => 'Material',
                'type' => 'select',
                'entity_type' => 'products',
                'is_required' => false,
                'is_unique' => false,
                'quick_add' => false,
                'validation' => null,
                'sort_order' => 100,
                'is_user_defined' => true,
                'options' => [
                    'Cotton',
                    'Polyester', 
                    'Leather',
                    'Metal',
                    'Plastic',
                    'Wood',
                    'Glass',
                    'Ceramic',
                    'Fabric',
                    'Other'
                ]
            ],
            [
                'code' => 'brand',
                'name' => 'Brand',
                'type' => 'text',
                'entity_type' => 'products',
                'is_required' => false,
                'is_unique' => false,
                'quick_add' => false,
                'validation' => 'max:255',
                'sort_order' => 101,
                'is_user_defined' => true,
            ],
            [
                'code' => 'model_number',
                'name' => 'Model Number',
                'type' => 'text',
                'entity_type' => 'products',
                'is_required' => false,
                'is_unique' => false,
                'quick_add' => false,
                'validation' => 'max:100',
                'sort_order' => 102,
                'is_user_defined' => true,
            ],
            [
                'code' => 'serial_number',
                'name' => 'Serial Number',
                'type' => 'text',
                'entity_type' => 'products',
                'is_required' => false,
                'is_unique' => true,
                'quick_add' => false,
                'validation' => 'max:100',
                'sort_order' => 103,
                'is_user_defined' => true,
            ],
            [
                'code' => 'warranty_period',
                'name' => 'Warranty Period (months)',
                'type' => 'number',
                'entity_type' => 'products',
                'is_required' => false,
                'is_unique' => false,
                'quick_add' => false,
                'validation' => 'numeric|min:0|max:240',
                'sort_order' => 104,
                'is_user_defined' => true,
            ],
            [
                'code' => 'color',
                'name' => 'Color',
                'type' => 'select',
                'entity_type' => 'products',
                'is_required' => false,
                'is_unique' => false,
                'quick_add' => false,
                'validation' => null,
                'sort_order' => 105,
                'is_user_defined' => true,
                'options' => [
                    'Red',
                    'Blue',
                    'Green',
                    'Yellow',
                    'Black',
                    'White',
                    'Gray',
                    'Brown',
                    'Orange',
                    'Purple',
                    'Pink',
                    'Multicolor'
                ]
            ],
            [
                'code' => 'size',
                'name' => 'Size',
                'type' => 'select',
                'entity_type' => 'products',
                'is_required' => false,
                'is_unique' => false,
                'quick_add' => false,
                'validation' => null,
                'sort_order' => 106,
                'is_user_defined' => true,
                'options' => [
                    'XS',
                    'S',
                    'M',
                    'L',
                    'XL',
                    'XXL',
                    'One Size',
                    'Custom'
                ]
            ],
            [
                'code' => 'country_of_origin',
                'name' => 'Country of Origin',
                'type' => 'text',
                'entity_type' => 'products',
                'is_required' => false,
                'is_unique' => false,
                'quick_add' => false,
                'validation' => 'max:100',
                'sort_order' => 107,
                'is_user_defined' => true,
            ],
            [
                'code' => 'certifications',
                'name' => 'Certifications',
                'type' => 'textarea',
                'entity_type' => 'products',
                'is_required' => false,
                'is_unique' => false,
                'quick_add' => false,
                'validation' => 'max:1000',
                'sort_order' => 108,
                'is_user_defined' => true,
            ],
            [
                'code' => 'eco_friendly',
                'name' => 'Eco-Friendly',
                'type' => 'boolean',
                'entity_type' => 'products',
                'is_required' => false,
                'is_unique' => false,
                'quick_add' => false,
                'validation' => null,
                'sort_order' => 109,
                'is_user_defined' => true,
            ],
            [
                'code' => 'hazardous_material',
                'name' => 'Hazardous Material',
                'type' => 'boolean',
                'entity_type' => 'products',
                'is_required' => false,
                'is_unique' => false,
                'quick_add' => false,
                'validation' => null,
                'sort_order' => 110,
                'is_user_defined' => true,
            ],
            [
                'code' => 'product_url',
                'name' => 'Product URL',
                'type' => 'url',
                'entity_type' => 'products',
                'is_required' => false,
                'is_unique' => false,
                'quick_add' => false,
                'validation' => 'url|max:500',
                'sort_order' => 111,
                'is_user_defined' => true,
            ],
            [
                'code' => 'tags',
                'name' => 'Tags',
                'type' => 'text',
                'entity_type' => 'products',
                'is_required' => false,
                'is_unique' => false,
                'quick_add' => false,
                'validation' => 'max:500',
                'sort_order' => 112,
                'is_user_defined' => true,
            ],
            [
                'code' => 'supplier_code',
                'name' => 'Supplier Code',
                'type' => 'text',
                'entity_type' => 'products',
                'is_required' => false,
                'is_unique' => false,
                'quick_add' => false,
                'validation' => 'max:100',
                'sort_order' => 113,
                'is_user_defined' => true,
            ],
            [
                'code' => 'lead_time_days',
                'name' => 'Lead Time (Days)',
                'type' => 'number',
                'entity_type' => 'products',
                'is_required' => false,
                'is_unique' => false,
                'quick_add' => false,
                'validation' => 'numeric|min:0|max:365',
                'sort_order' => 114,
                'is_user_defined' => true,
            ]
        ];

        foreach ($attributes as $attributeData) {
            // Check if attribute already exists
            $existingAttribute = Attribute::where('code', $attributeData['code'])
                ->where('entity_type', $attributeData['entity_type'])
                ->first();

            if ($existingAttribute) {
                continue; // Skip if already exists
            }

            $options = $attributeData['options'] ?? null;
            unset($attributeData['options']);

            $attribute = Attribute::create($attributeData);

            // Create options if provided
            if ($options && is_array($options)) {
                foreach ($options as $index => $optionName) {
                    AttributeOption::create([
                        'name' => $optionName,
                        'sort_order' => $index + 1,
                        'attribute_id' => $attribute->id,
                    ]);
                }
            }
        }
    }
}