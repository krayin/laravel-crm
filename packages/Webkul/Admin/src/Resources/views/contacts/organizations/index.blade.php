<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.contacts.organizations.index.title')
    </x-slot>

    <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
        <div class="flex flex-col gap-2">
            <div class="flex cursor-pointer items-center">
                <x-admin::breadcrumbs name="contacts.organizations" />
            </div>

            <div class="text-xl font-bold dark:text-gray-300">
                @lang('admin::app.contacts.organizations.index.title')
            </div>
        </div>

        <div class="flex items-center gap-x-2.5">
            <!-- Create button for person -->
            <div class="flex items-center gap-x-2.5">
                @if (bouncer()->hasPermission('contacts.organizations.create'))
                    <a
                        href="{{ route('admin.contacts.organizations.create') }}"
                        class="primary-button"
                    >
                        @lang('admin::app.contacts.organizations.index.create-btn')
                    </a>
                @endif
            </div>
        </div>
    </div>

    {!! view_render_event('krayin.admin.organizations.datagrid.index.before') !!}

    <x-admin::datagrid src="{{ route('admin.contacts.organizations.index') }}" />

    {!! view_render_event('krayin.admin.organizations.datagrid.index.after') !!}
</x-admin::layouts>
