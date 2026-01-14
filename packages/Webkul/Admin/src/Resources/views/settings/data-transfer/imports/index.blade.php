<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.settings.data-transfer.imports.index.title')
    </x-slot>

    <div class="flex flex-col gap-4"> 
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                {!! view_render_event('admin.settings.data_transfers.index.breadcrumbs.before') !!}

                <!-- Breadcrumbs -->
                <x-admin::breadcrumbs name="settings.data_transfers" />

                {!! view_render_event('admin.settings.data_transfers.index.breadcrumbs.after') !!}

                <div class="text-xl font-bold dark:text-white">
                    @lang('admin::app.settings.data-transfer.imports.index.title')
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                <!-- Create button for person -->
                <div class="flex items-center gap-x-2.5">
                    {!! view_render_event('admin.settings.data_transfers.index.create_button.before') !!}

                    @if (bouncer()->hasPermission('settings.automation.data_transfer.imports.create'))
                        <a 
                            href="{{ route('admin.settings.data_transfer.imports.create') }}" 
                            class="primary-button"
                        >
                            @lang('admin::app.settings.data-transfer.imports.index.button-title')
                        </a>
                    @endif

                    {!! view_render_event('admin.settings.data_transfers.index.create_button.after') !!}
                </div>
            </div>
        </div>

        {!! view_render_event('admin.settings.data_transfers.index.datagrid.before') !!}

        <!-- DataGrid -->
        <x-admin::datagrid :src="route('admin.settings.data_transfer.imports.index')">
            <!-- DataGrid Shimmer -->
            <x-admin::shimmer.datagrid />
        </x-admin::datagrid>

        {!! view_render_event('admin.settings.data_transfers.index.datagrid.after') !!}
    </div>
</x-admin::layouts>
