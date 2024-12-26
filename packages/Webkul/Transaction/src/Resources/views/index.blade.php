<x-admin::layouts>
    <x-slot:title>
        @lang('transaction::app.transactions.index.title')
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    <!-- Breadcrumbs -->
                    <x-admin::breadcrumbs name="transaction" />
                </div>

                <div class="text-xl font-bold dark:text-white">
                    @lang('transaction::app.transactions.index.title')
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                {!! view_render_event('admin.transactions.index.create_button.before') !!}

                <!-- Create button for Product -->
                @if (bouncer()->hasPermission('transaction.create'))
                    <div class="flex items-center gap-x-2.5">
                        <a
                            href="{{ route('admin.transaction.create') }}"
                            class="primary-button"
                        >
                            @lang('transaction::app.transactions.index.create-btn')
                        </a>
                    </div>
                @endif

                {!! view_render_event('admin.transactions.index.create_button.after') !!}
            </div>
        </div>

        {!! view_render_event('admin.transactions.index.datagrid.before') !!}

        <x-admin::datagrid :src="route('admin.transaction.index')">
            <!-- DataGrid Shimmer -->
            <x-admin::shimmer.datagrid />
        </x-admin::datagrid>

        {!! view_render_event('admin.transactions.index.datagrid.after') !!}
    </div>
</x-admin::layouts>
