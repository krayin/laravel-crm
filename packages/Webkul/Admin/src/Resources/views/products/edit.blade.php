
<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.products.edit.title')
    </x-slot>

    {!! view_render_event('admin.products.edit.form.before') !!}

    <x-admin::form
        :action="route('admin.products.update', $product->id)"
        encType="multipart/form-data"
        method="PUT"
        id="product-form"
    >
        <div class="flex flex-col gap-4">
            <!-- Enhanced Form Header -->
            <x-admin::products.form-header :product="$product" />

            <!-- Enhanced Tabbed Form Interface -->
            <div class="product-edit-wizard">
                <x-admin::products.form-tabs :product="$product">
                    <!-- General Information Tab -->
                    <x-slot:general>
                        <x-admin::products.tabs.general :product="$product" />
                    </x-slot>

                    <!-- Categorization Tab -->
                    <x-slot:categorization>
                        <x-admin::products.tabs.categorization :product="$product" />
                    </x-slot>

                    <!-- Inventory & Pricing Tab -->
                    <x-slot:inventory>
                        <x-admin::products.tabs.inventory :product="$product" />
                    </x-slot>

                    <!-- Media & Attributes Tab -->
                    <x-slot:media>
                        <x-admin::products.tabs.media :product="$product" />
                    </x-slot>
                </x-admin::products.form-tabs>
            </div>
        </div>
    </x-admin::form>

    {!! view_render_event('admin.products.edit.form.after') !!}
</x-admin::layouts>
