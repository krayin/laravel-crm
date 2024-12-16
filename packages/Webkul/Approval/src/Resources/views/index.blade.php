<x-admin::layouts>
    <x-slot:title>
        @lang('approval::app.approvals.index.title')
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    <!-- Breadcrumbs -->
                    <x-admin::breadcrumbs name="approval" />
                </div>

                <div class="text-xl font-bold dark:text-white">
                    @lang('approval::app.approvals.index.title')
                </div>
            </div>

        </div>

        {!! view_render_event('admin.approvals.index.datagrid.before') !!}

        <x-admin::datagrid :src="route('admin.approval.index')">
            <!-- DataGrid Shimmer -->
            <x-admin::shimmer.datagrid />
        </x-admin::datagrid>

        {!! view_render_event('admin.approvals.index.datagrid.after') !!}
    </div>
</x-admin::layouts>
