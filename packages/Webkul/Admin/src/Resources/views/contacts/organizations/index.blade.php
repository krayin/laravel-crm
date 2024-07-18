<x-admin::layouts>
    <x-slot:title>
        @lang('Organizations')
    </x-slot>

    <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white py-2 pl-2 pr-4 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
        <div class="flex flex-col">
            <div class="flex cursor-pointer items-center">
                <i class="icon-left-arrow text-2xl text-gray-800"></i>

                <a
                    href="{{ route('admin.contacts.persons.index') }}"
                    class="text-xs text-gray-800 dark:text-gray-300"
                >
                    Settings
                </a>
            </div>

            <div class="pl-3 text-xl font-normal dark:text-gray-300">
                Organizations
            </div>
        </div>

        <div class="flex items-center gap-x-2.5">
            <!-- Create button for person -->
            <div class="flex items-center gap-x-2.5">
                <a
                    href="{{ route('admin.contacts.organizations.create') }}"
                    class="primary-button"
                >
                    Create Organization
                </a>
            </div>
        </div>
    </div>

    {!! view_render_event('krayin.admin.organizations.datagrid.index.before') !!}

    <x-admin::datagrid src="{{ route('admin.contacts.organizations.index') }}" />

    {!! view_render_event('krayin.admin.organizations.datagrid.index.after') !!}
</x-admin::layouts>
