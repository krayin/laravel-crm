{!! view_render_event('admin.products.view.attributes.before', ['product' => $product]) !!}

<div class="flex w-full flex-col gap-4 border-b border-gray-200 p-4 dark:border-gray-800 dark:text-white">
    <h4 class="font-semibold">
        @lang('admin::app.products.view.attributes.about-product')
    </h4>

    {!! view_render_event('admin.products.view.attributes.view.before', ['product' => $product]) !!}

    <x-admin::attributes.view
        :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
            'entity_type' => 'products',
            ['code', 'IN', ['SKU', 'price', 'quantity', 'status']]
        ])->sortBy('sort_order')"
        :entity="$product"
        :url="route('admin.products.update', $product->id)"   
        :allow-edit="true"
    />

    {!! view_render_event('admin.products.view.attributes.view.after', ['product' => $product]) !!}
</div>

{!! view_render_event('admin.products.view.attributes.before', ['product' => $product]) !!}