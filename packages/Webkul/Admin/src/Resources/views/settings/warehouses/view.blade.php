<x-admin::layouts>
    <x-slot:title>
        @lang ($warehouse->name)
    </x-slot>

    <!-- Content -->
    <div class="flex gap-4">
        <!-- Left Panel -->
        {!! view_render_event('admin.leads.view.left.before', ['product' => $warehouse]) !!}

        <div class="flex min-w-[394px] max-w-[394px] flex-col self-start rounded-lg border border-gray-200 bg-white">
            <!-- Product Information -->
            <div class="flex w-full flex-col gap-2 border-b border-gray-200 p-4">
                <!-- Breadcrums -->
                <div class="flex items-center justify-between">
                    <x-admin::breadcrumbs name="settings.warehouses.view" :entity="$warehouse" />

                    <div class="flex gap-1">
                        <button class="icon-left-arrow rtl:icon-right-arrow rounded-md p-1 text-2xl transition-all hover:bg-gray-100"></button>
                        <button class="icon-right-arrow rtl:icon-right-arrow rounded-md p-1 text-2xl transition-all hover:bg-gray-100"></button>
                    </div>
                </div>

                <!-- Tags -->
                <x-admin::tags
                    :attach-endpoint="route('admin.settings.warehouses.tags.attach', $warehouse->id)"
                    :detach-endpoint="route('admin.settings.warehouses.tags.detach', $warehouse->id)"
                    :added-tags="$warehouse->tags"
                />

                <!-- Title -->
                <h3 class="text-lg font-bold">
                    {{ $warehouse->name }}
                </h3>

                <!-- Activity Actions -->
                <div class="flex flex-wrap gap-2">
                    <!-- File Activity Action -->
                    <x-admin::activities.actions.file
                        :entity="$warehouse"
                        entity-control-name="warehouse_id"
                    />

                    <!-- Note Activity Action -->
                    <x-admin::activities.actions.note
                        :entity="$warehouse"
                        entity-control-name="warehouse_id"
                    />

                    <!-- Activity Action -->
                    <x-admin::activities.actions.activity
                        :entity="$warehouse"
                        entity-control-name="warehouse_id"
                    />
                </div>
            </div>
            
            <!-- Product Attributes -->
            {{-- @include ('admin::products.view.attributes') --}}
        </div>

        {!! view_render_event('admin.products.view.left.after', ['product' => $warehouse]) !!}

        {!! view_render_event('admin.products.view.right.before', ['product' => $warehouse]) !!}
        
        <!-- Right Panel -->
        <div class="flex w-full flex-col gap-4 rounded-lg">
            <!-- Activity Navigation -->
            <x-admin::activities
                :endpoint="route('admin.settings.warehouse.activities.index', $warehouse->id)" 
                :types="[
                    ['name' => 'all', 'label' => trans('admin::app.products.view.all')],
                    ['name' => 'note', 'label' => trans('admin::app.products.view.notes')],
                    ['name' => 'file', 'label' => trans('admin::app.products.view.files')],
                ]"
                :extra-types="[
                    ['name' => 'location', 'label' => 'Location'],
                ]"
            >
                <x-slot:location>
                    <div class="flex gap-2">
                        <button class="icon-left-arrow rtl:icon-right-arrow rounded-md p-1 text-2xl transition-all hover:bg-gray-100"></button>
                        <button class="icon-right-arrow rtl:icon-right-arrow rounded-md p-1 text-2xl transition-all hover:bg-gray-100"></button>
                    </div>
                </x-slot>
            </x-admin::activities>
        </div>

        {!! view_render_event('admin.warehouse.view.right.after', ['warehouse' => $warehouse]) !!}
    </div>    
</x-admin::layouts>