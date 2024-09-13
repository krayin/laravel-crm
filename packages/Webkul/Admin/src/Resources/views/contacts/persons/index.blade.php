<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.contacts.persons.index.title')
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    <x-admin::breadcrumbs name="contacts.persons" />
                </div>

                <div class="text-xl font-bold dark:text-white">
                    @lang('admin::app.contacts.persons.index.title')
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                <!-- Create button for person -->
                <div class="flex items-center gap-x-2.5">
                    {!! view_render_event('admin.persons.index.create_button.before') !!}

                    @if (bouncer()->hasPermission('admin.contacts.persons.view'))
                        <a
                            href="{{ route('admin.contacts.persons.create') }}"
                            class="primary-button"
                        >
                            @lang('admin::app.contacts.persons.index.create-btn')
                        </a>
                    @endif

                    {!! view_render_event('admin.persons.index.create_button.after') !!}
                </div>
            </div>
        </div>

        {!! view_render_event('admin.persons.index.datagrid.before') !!}

        <x-admin::datagrid :src="route('admin.contacts.persons.index')">
            <!-- DataGrid Shimmer -->
            <x-admin::shimmer.datagrid />
        </x-admin::datagrid>

        {!! view_render_event('admin.persons.index.datagrid.after') !!}
    </div>
</x-admin::layouts>