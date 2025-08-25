
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
    >
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <!-- Breadcrumbs -->
                    <x-admin::breadcrumbs
                        name="products.edit"
                        :entity="$product"
                     />

                    <div class="text-xl font-bold dark:text-white">
                        @lang('admin::app.products.edit.title')
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.products.edit.create_button.before', ['product' => $product]) !!}
                        
                        <!-- Edit button for Product -->
                        <button
                            type="submit"
                            class="primary-button"
                        >
                            @lang('admin::app.products.create.save-btn')
                        </button>

                        {!! view_render_event('admin.products.edit.create_button.after', ['product' => $product]) !!}
                    </div>
                </div>
            </div>

            <div class="flex gap-2.5 max-xl:flex-wrap">
                <!-- Left sub-component -->
                <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('admin::app.products.create.general')
                        </p>

                        {!! view_render_event('admin.products.edit.attributes.before', ['product' => $product]) !!}

                        <x-admin::attributes
                            :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                'entity_type' => 'products',
                                ['code', 'NOTIN', ['price', 'quantity']],
                            ])"
                            :entity="$product"
                        />

                        {!! view_render_event('admin.products.edit.attributes.after', ['product' => $product]) !!}
                    </div>
                </div>

                <!-- Right sub-component -->
                <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">
                    {!! view_render_event('admin.products.edit.accordion.before', ['product' => $product]) !!}

                    <x-admin::accordion >
                        <x-slot:header>
                            {!! view_render_event('admin.products.edit.accordion.header.before', ['product' => $product]) !!}

                            <div class="flex items-center justify-between">
                                <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('admin::app.products.create.price')
                                </p>
                            </div>

                            {!! view_render_event('admin.products.edit.accordion.header.after', ['product' => $product]) !!}
                        </x-slot>

                        <x-slot:content>
                            {!! view_render_event('admin.products.edit.accordion.content.attributes.before', ['product' => $product]) !!}

                            <x-admin::attributes
                                :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                    'entity_type' => 'products',
                                    ['code', 'IN', ['price', 'quantity']],
                                ])"
                                :entity="$product"
                            />

                            {!! view_render_event('admin.products.edit.accordion.content.attributes.after', ['product' => $product]) !!}
                        </x-slot>
                    </x-admin::accordion>

                    {!! view_render_event('admin.products.edit.accordion.after', ['product' => $product]) !!}
                </div>
            </div>
        </div>
    </x-admin::form>

    {!! view_render_event('admin.products.edit.form.after') !!}
</x-admin::layouts>
