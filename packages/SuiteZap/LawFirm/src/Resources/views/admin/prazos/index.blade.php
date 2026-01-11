<x-admin::layouts>
    <x-slot:title>
        @lang('lawfirm::app.prazos.title')
        </x-slot>

        <div class="flex flex-col gap-4">
            <!-- Header -->
            <div
                class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <div class="text-xl font-bold dark:text-white">
                        @lang('lawfirm::app.prazos.title')
                    </div>
                </div>
                <!--
            <div class="flex items-center gap-x-2.5">
                <a href="{{ route('admin.processos.create') }}" class="primary-button">
                    @lang('lawfirm::app.processos.create')
                </a>
            </div>
            -->
            </div>

            <!-- DataGrid -->
            <x-admin::datagrid :src="route('admin.prazos.index')" />
        </div>
</x-admin::layouts>