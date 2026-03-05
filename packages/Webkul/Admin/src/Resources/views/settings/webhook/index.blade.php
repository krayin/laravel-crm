<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.settings.webhooks.index.title')
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                {!! view_render_event('admin.settings.webhooks.breadcrumbs.datagrid.before') !!}

                <x-admin::breadcrumbs name="settings.webhooks" />

                {!! view_render_event('admin.settings.webhooks.breadcrumbs.datagrid.after') !!}

                <div class="text-xl font-bold dark:text-white">
                    @lang('admin::app.settings.webhooks.index.title')
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                <div class="flex items-center gap-x-2.5">
                    {!! view_render_event('admin.settings.webhooks.create_button.datagrid.before') !!}

                    <!-- Create button for person -->
                    <a
                        href="{{ route('admin.settings.webhooks.create') }}"
                        class="primary-button"
                    >
                        @lang('admin::app.settings.webhooks.index.create-btn')
                    </a>

                    {!! view_render_event('admin.settings.webhooks.create_button.datagrid.after') !!}
                </div>
            </div>
        </div>

        {!! view_render_event('admin.settings.webhooks.index.datagrid.before') !!}

        <x-admin::datagrid :src="route('admin.settings.webhooks.index')">
            <!-- DataGrid Shimmer -->
            <x-admin::shimmer.datagrid />
        </x-admin::datagrid>

        {!! view_render_event('admin.settings.webhooks.index.datagrid.after') !!}
    </div>
</x-admin::layouts>
