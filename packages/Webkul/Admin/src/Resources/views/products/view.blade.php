<x-admin::layouts>
    <x-slot:title>
        @lang ($product->name)
    </x-slot>

    <!-- Content -->
    <div class="flex gap-4">
        <!-- Left Panel -->
        {!! view_render_event('admin.leads.view.left.before', ['product' => $product]) !!}

        <div class="flex min-w-[394px] max-w-[394px] flex-col self-start rounded-lg border border-gray-200 bg-white">
            <!-- pRODUCT Information -->
            <div class="flex w-full flex-col gap-2 border-b border-gray-200 p-4">
                <!-- Breadcrums -->
                <div class="flex items-center justify-between">
                    <x-admin::breadcrumbs name="products.view" :entity="$product" />

                    <div class="flex gap-1">
                        <button class="icon-left-arrow rtl:icon-right-arrow rounded-md p-1 text-2xl transition-all hover:bg-gray-100"></button>
                        <button class="icon-right-arrow rtl:icon-right-arrow rounded-md p-1 text-2xl transition-all hover:bg-gray-100"></button>
                    </div>
                </div>

                <!-- Tags -->
                <x-admin::tags
                    :attach-endpoint="route('admin.products.tags.attach', $product->id)"
                    :detach-endpoint="route('admin.products.tags.detach', $product->id)"
                    :added-tags="$product->tags"
                />

                <!-- Title -->
                <h3 class="text-lg font-bold">
                    {{ $product->name }}
                </h3>
                
                <p class="text-sm font-normal">
                    SKU : {{ $product->sku }}
                </p>

                <!-- Activity Actions -->
                <div class="flex flex-wrap gap-2">
                    <!-- Note Activity Action -->
                    <x-admin::activities.actions.note
                        :entity="$product"
                        entity-control-name="product_id"
                    />
                </div>
            </div>
            
            <!-- Product Attributes -->
            @include ('admin::products.view.attributes')
        </div>

        {!! view_render_event('admin.products.view.left.after', ['product' => $product]) !!}

        {!! view_render_event('admin.products.view.right.before', ['product' => $product]) !!}
        
        <!-- Right Panel -->
        <div class="flex w-full flex-col gap-4 rounded-lg">
            <!-- Stages Navigation -->
            <x-admin::activities 
                :endpoint="route('admin.products.activities.index', $product->id)" 
                :types="[
                    ['name' => 'all', 'label' => trans('admin::app.components.activities.index.all')],
                    ['name' => 'note', 'label' => trans('admin::app.components.activities.index.notes')],
                    ['name' => 'inventory', 'label' => trans('Inventory')],
                ]"
            ></x-admin::activities>

            <!-- Inventory Component -->
            <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
                <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                    {!! view_render_event('krayin.admin.settings.roles.create.card.access_control.before') !!}
    
                    <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                        <!-- Inventory Section -->
                        @include('admin::products.view.inventory')
                    </div>
    
                    {!! view_render_event('krayin.admin.settings.roles.create.card.access_control.after') !!}
                </div>
            </div>
        </div>

        {!! view_render_event('admin.products.view.right.after', ['product' => $product]) !!}
    </div>    
</x-admin::layouts>