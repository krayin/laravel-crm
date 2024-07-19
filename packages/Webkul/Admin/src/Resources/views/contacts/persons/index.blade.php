<x-admin::layouts>
    <x-slot:title>
        @lang('Persons')
    </x-slot>

    <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
        <div class="flex flex-col gap-2">
            <div class="flex cursor-pointer items-center">
                <a 
                    href="{{ route('admin.contacts.organizations.index') }}" 
                    class="flex items-center text-xs text-gray-600 dark:text-gray-300"
                >
                    <i class="icon-left-arrow text-2xl"></i>
                    
                    @lang('admin::app.settings.roles.index.settings')
                </a>
            </div>

            <div class="px-4 text-xl font-bold dark:text-gray-300">
                @lang('Persons')
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