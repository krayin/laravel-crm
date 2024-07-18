<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.products.index.title')
    </x-slot>

    <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white py-2 pl-2 pr-4 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
        <div class="flex flex-col">
            <div class="flex cursor-pointer items-center">
                <i class="icon-left-arrow text-2xl text-gray-800"></i>

                <a
                    href="{{ route('admin.products.index') }}"
                    class="text-xs text-gray-800 dark:text-gray-300"
                >
                    Settings
                </a>
            </div>

            <div class="pl-3 text-xl font-normal dark:text-gray-300">
                @lang('admin::app.products.index.title')
            </div>
        </div>

        <div class="flex items-center gap-x-2.5">
            <!-- Create button for person -->
            <div class="flex items-center gap-x-2.5">
                {!! view_render_event('krayin.admin.product.index.create-button.before') !!}

                <a
                    href="{{ route('admin.products.create') }}"
                    class="primary-button"
                >
                    @lang('admin::app.products.index.create-btn')
                </a>

                {!! view_render_event('krayin.admin.product.index.create-button.after') !!}
            </div>
        </div>
    </div>

    {!! view_render_event('krayin.admin.product.index.datagrid.before') !!}

    <x-admin::datagrid src="{{ route('admin.products.index') }}" />

    {!! view_render_event('krayin.admin.product.index.datagrid.after') !!}
</x-admin::layouts>
