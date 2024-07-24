<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.settings.email-template.index.title')
    </x-slot>

    <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
        <div class="flex flex-col gap-2">
            <div class="flex cursor-pointer items-center">
                <x-admin::breadcrumbs name="settings.email_templates" />
            </div>

            <div class="text-xl font-bold dark:text-gray-300">
                @lang('admin::app.settings.email-template.index.title')
            </div>
        </div>

        <div class="flex items-center gap-x-2.5">
            <!-- Create button for person -->
            <div class="flex items-center gap-x-2.5">
                <a
                    href="{{ route('admin.settings.email_templates.create') }}"
                    class="primary-button"
                >
                    @lang('admin::app.settings.email-template.index.create-btn')
                </a>
            </div>
        </div>
    </div>

    <!-- DataGrid -->
    <x-admin::datagrid :src="route('admin.settings.email_templates.index')"/>
</x-admin::layouts>
