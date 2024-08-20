<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.settings.attributes.index.title')
    </x-slot>

    <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
        <div class="flex flex-col gap-2">
            <div class="flex cursor-pointer items-center">
                <!-- breadcrumbs -->
                <x-admin::breadcrumbs name="settings.attributes" />
            </div>

            <div class="text-xl font-bold dark:text-white">
                <!-- Title -->
                @lang('admin::app.settings.attributes.index.title')
            </div>
        </div>

        <div class="flex items-center gap-x-2.5">
            <!-- Create Button for Attributes -->
            <div class="flex items-center gap-x-2.5">
                {!! view_render_event('krayin.admin.settings.attributes.index.create_button.before') !!}
                    @if (bouncer()->hasPermission('settings.automation.attributes.create'))
                        <a
                            href="{{ route('admin.settings.attributes.create') }}"
                            class="primary-button"
                        >
                            @lang('admin::app.settings.attributes.index.create-btn')
                        </a>
                    @endif
                {!! view_render_event('krayin.admin.settings.attributes.index.create_button.after') !!}
            </div>
        </div>
    </div>

    {!! view_render_event('krayin.admin.settings.attributes.index.datagrid.before') !!}

    <!-- DataGrid -->
    <x-admin::datagrid src="{{ route('admin.settings.attributes.index') }}" />

    {!! view_render_event('krayin.admin.settings.attributes.index.datagrid.after') !!}
</x-admin::layouts>
