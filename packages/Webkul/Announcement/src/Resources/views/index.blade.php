<x-admin::layouts>
    <x-slot:title>
        @lang('announcement::app.announcements.index.title')
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    <!-- Breadcrumbs -->
                    <x-admin::breadcrumbs name="announcement" />
                </div>

                <div class="text-xl font-bold dark:text-white">
                    @lang('announcement::app.announcements.index.title')
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                {!! view_render_event('admin.announcements.index.create_button.before') !!}

                <!-- Create button for Product -->
                @if (bouncer()->hasPermission('announcement.create'))
                    <div class="flex items-center gap-x-2.5">
                        <a
                            href="{{ route('admin.announcement.create') }}"
                            class="primary-button"
                        >
                            @lang('announcement::app.announcements.index.create-btn')
                        </a>
                    </div>
                @endif

                {!! view_render_event('admin.announcements.index.create_button.after') !!}
            </div>
        </div>

        {!! view_render_event('admin.announcements.index.datagrid.before') !!}

        <x-admin::datagrid :src="route('admin.announcement.index')">
            <!-- DataGrid Shimmer -->
            <x-admin::shimmer.datagrid />
        </x-admin::datagrid>

        {!! view_render_event('admin.announcements.index.datagrid.after') !!}
    </div>
</x-admin::layouts>
