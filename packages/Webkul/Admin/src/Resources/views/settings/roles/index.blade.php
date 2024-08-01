<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.settings.roles.index.title')
    </x-slot>

    <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
        <div class="flex flex-col gap-2">
            <div class="flex cursor-pointer items-center">
                <!-- breadcrumbs -->
                <x-admin::breadcrumbs name="settings.roles" />
            </div>

            <div class="text-xl font-bold dark:text-gray-300">
                <!-- title -->
                @lang('admin::app.settings.roles.index.title')
            </div>
        </div>

        <div class="flex items-center gap-x-2.5">
            <!-- Create button Roles -->
            <div class="flex items-center gap-x-2.5">
                {!! view_render_event('krayin.admin.settings.roles.index.create_button.before') !!}
                    @if (bouncer()->hasPermission('settings.user.roles.create'))
                        <a
                            href="{{ route('admin.settings.roles.create') }}"
                            class="primary-button"
                        >
                            @lang('admin::app.products.index.create-btn')
                        </a>
                    @endif
                {!! view_render_event('krayin.admin.settings.roles.index.create_button.after') !!}
            </div>
        </div>
    </div>

    {!! view_render_event('krayin.admin.settings.roles.index.datagrid.before') !!}

    <!-- DataGrid -->
    <x-admin::datagrid src="{{ route('admin.settings.roles.index') }}" />

    {!! view_render_event('krayin.admin.settings.roles.index.datagrid.after') !!}
</x-admin::layouts>
