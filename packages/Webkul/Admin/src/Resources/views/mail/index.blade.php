

<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.mail.index.' . request('route'))
    </x-slot>

    <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
        <div class="flex flex-col gap-2">
            <div class="flex cursor-pointer items-center">
                <!-- breadcrumbs -->
                <x-admin::breadcrumbs :name="'mail.route'" :entity="request('route')" />
            </div>

            <div class="text-xl font-bold dark:text-gray-300">
                <!-- title -->
                @lang('admin::app.mail.index.' . request('route'))
            </div>
        </div>
    </div>

    {!! view_render_event('krayin.admin.settings.roles.index.datagrid.before') !!}

    <!-- DataGrid -->
    <x-admin::datagrid src="{{ route('admin.mail.index', request('route')) }}" />

    {!! view_render_event('krayin.admin.settings.roles.index.datagrid.after') !!}
</x-admin::layouts>
