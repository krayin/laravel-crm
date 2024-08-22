
<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.settings.warehouses.create.title')
    </x-slot>

    {!! view_render_event('krayin.admin.settings.warehouses.create.form.before') !!}

    <x-admin::form
        :action="route('admin.settings.warehouses.store')"
        method="POST"
    >
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    <!-- Breadcrumbs -->
                    <x-admin::breadcrumbs name="settings.warehouses.create" />
                </div>

                <div class="text-xl font-bold dark:text-white">
                    @lang('admin::app.settings.warehouses.create.title')
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                <!-- Create button for person -->
                <div class="flex items-center gap-x-2.5">
                    <button
                        type="submit"
                        class="primary-button"
                    >
                        @lang('admin::app.settings.warehouses.create.save-btn')
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
            <!-- Left sub-component -->
            <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                    <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                        @lang('admin::app.settings.warehouses.create.contact-info')
                    </p>
                    
                    <x-admin::attributes
                        :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                            ['code', 'NOTIN', ['name', 'description']],
                            'entity_type' => 'warehouses',
                        ])->sortBy('sort_order')"
                    />
                </div>
            </div>

            <!-- Right sub-component -->
            <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">
                <x-admin::accordion>
                    <x-slot:header>
                        <div class="flex items-center justify-between">
                            <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                                @lang('admin::app.settings.roles.create.general')
                            </p>
                        </div>
                    </x-slot>

                    <x-slot:content>
                        <x-admin::attributes
                            :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                ['code', 'IN', ['name', 'description']],
                                'entity_type' => 'warehouses',
                            ])->sortBy('sort_order')"
                        />
                    </x-slot>
                </x-admin::accordion>
            </div>
        </div>
    </x-admin::form>

    {!! view_render_event('krayin.admin.settings.warehouses.create.form.after') !!}

</x-admin::layouts>
