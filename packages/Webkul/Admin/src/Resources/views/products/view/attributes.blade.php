{!! view_render_event('admin.products.view.attributes.before', ['product' => $product]) !!}

<div class="flex w-full flex-col gap-4 border-b border-gray-200 p-4">
    <h4 class="font-semibold">About Product</h4>

    <x-admin::attributes.view
        :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
            'entity_type' => 'products',
            ['code', 'IN', ['SKU', 'price', 'quantity', 'status']]
        ])->sortBy('sort_order')"
        :entity="$product"
    />
</div>

{!! view_render_event('admin.products.view.attributes.before', ['product' => $product]) !!}