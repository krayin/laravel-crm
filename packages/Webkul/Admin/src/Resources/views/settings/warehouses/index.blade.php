<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.settings.warehouses.index.title')
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    <!-- breadcrumbs -->
                    <x-admin::breadcrumbs name="settings.warehouses" />
                </div>

                <div class="text-xl font-bold dark:text-white">
                    <!-- title -->
                    @lang('admin::app.settings.warehouses.index.title')
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                <!-- Create button For Warehouses -->
                <div class="flex items-center gap-x-2.5">
                    {!! view_render_event('admin.settings.warehouses.index.create_button.before') !!}

                    @if (bouncer()->hasPermission('settings.automation.warehouses.create'))
                        <a
                            href="{{ route('admin.settings.warehouses.create') }}"
                            class="primary-button"
                        >
                            @lang('admin::app.settings.warehouses.index.create-btn')
                        </a>
                    @endif

                    {!! view_render_event('admin.settings.warehouses.index.create_button.after') !!}
                </div>
            </div>
        </div>

        {!! view_render_event('admin.settings.warehouses.index.datagrid.before') !!}

        <!-- DataGrid -->
        <x-admin::datagrid :src="route('admin.settings.warehouses.index')">
            <!-- DataGrid Shimmer -->
            <x-admin::shimmer.datagrid />
        </x-admin::datagrid>

        {!! view_render_event('admin.settings.warehouses.index.datagrid.after') !!}
    </div>
</x-admin::layouts>
