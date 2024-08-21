<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.settings.workflows.index.title')
    </x-slot>

    <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
        <div class="flex flex-col gap-2">
            <div class="flex cursor-pointer items-center">
                <x-admin::breadcrumbs name="settings.workflows" />
            </div>

            <div class="text-xl font-bold dark:text-white">
                @lang('admin::app.settings.workflows.index.title')
            </div>
        </div>

        <div class="flex items-center gap-x-2.5">
            <!-- Create button for person -->
            <div class="flex items-center gap-x-2.5">
                @if (bouncer()->hasPermission('settings.automation.workflows.create'))
                    <a
                        href="{{ route('admin.settings.workflows.create') }}"
                        class="primary-button"
                    >
                        @lang('admin::app.settings.workflows.index.create-btn')
                    </a>
                @endif
            </div>
        </div>
    </div>

    {!! view_render_event('krayin.admin.settings.workflows.index.datagrid.before') !!}

    <x-admin::datagrid :src="route('admin.settings.workflows.index')" />

    {!! view_render_event('krayin.admin.settings.workflows.index.datagrid.after') !!}
</x-admin::layouts>
