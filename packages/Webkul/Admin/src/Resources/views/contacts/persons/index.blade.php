<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.customers.customers.index.title')
    </x-slot>

    <div class="flex items-center justify-between pr-4 pl-2 py-2 border text-sm rounded-lg bg-white border-gray-200 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
        <div class="flex flex-col">
            <div class="flex items-center cursor-pointer">
                <i class="icon icon-arrow-right text-xl text-gray-800"></i>

                <a
                    href="{{ route('admin.contacts.persons.index') }}"
                    class="text-xs text-gray-800 dark:text-gray-300"
                >
                    Settings
                </a>
            </div>

            <div class="text-xl font-normal pl-3 dark:text-gray-300">
                Persons
            </div>
        </div>

        <div class="flex items-center gap-x-2.5">
            <!-- Create button for person -->
            <div class="flex items-center gap-x-2.5">
                <a
                    href="{{ route('admin.contacts.persons.create') }}"
                    class="primary-button"
                >
                    Create Person
                </a>
            </div>
        </div>
    </div>

    {!! view_render_event('krayin.admin.person.datagrid.index.before') !!}

    <x-admin::datagrid src="{{ route('admin.contacts.persons.index') }}" />

    {!! view_render_event('krayin.admin.person.datagrid.index.after') !!}
</x-admin::layouts>