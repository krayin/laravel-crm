
<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.products.create.title')
    </x-slot>

    {!! view_render_event('admin.products.create.form.before') !!}

    <x-admin::form
        :action="route('admin.products.store')"
        method="POST"
        enctype="multipart/form-data"
        id="product-form"
    >
        <div class="flex flex-col gap-4">
            <!-- Enhanced Form Header -->
            <x-admin::products.form-header />

            <!-- Enhanced Tabbed Form Interface -->
            <div class="product-creation-wizard">
                <x-admin::products.form-tabs>
                    <!-- General Information Tab -->
                    <x-slot:general>
                        <x-admin::products.tabs.general />
                    </x-slot>

                    <!-- Categorization Tab -->
                    <x-slot:categorization>
                        <x-admin::products.tabs.categorization />
                    </x-slot>

                    <!-- Inventory & Pricing Tab -->
                    <x-slot:inventory>
                        <x-admin::products.tabs.inventory />
                    </x-slot>

                    <!-- Media & Attributes Tab -->
                    <x-slot:media>
                        <x-admin::products.tabs.media />
                    </x-slot>
                </x-admin::products.form-tabs>
            </div>
        </div>
    </x-admin::form>

    {!! view_render_event('admin.products.create.form.after') !!}
</x-admin::layouts>
