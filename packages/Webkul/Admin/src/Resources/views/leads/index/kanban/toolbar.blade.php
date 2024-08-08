{!! view_render_event('admin.leads.index.kanban.toolbar.before') !!}

<div class="flex justify-between">
    <div class="flex w-full items-center gap-x-1.5">
        <!-- Search Panel -->
        <div class="relative flex max-w-[445px] items-center max-sm:w-full max-sm:max-w-full">
            <div class="icon-search absolute top-1.5 flex items-center text-2xl ltr:left-3 rtl:right-3"></div>
            
            <input
                type="text"
                name="search"
                class="block w-full rounded-lg border bg-white py-1.5 leading-6 text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400 ltr:pl-10 ltr:pr-3 rtl:pl-3 rtl:pr-10"
                placeholder="Search"
                autocomplete="off"
                value=""
            >
        </div>
        
        <!-- Filter Drawer -->
        <x-admin::drawer
            width="350px"
            ref="updateCreateSampleDrawer"
        >
            <!-- Drawer Header -->
            <x-slot:toggle>
                <div class="relative inline-flex w-full max-w-max cursor-pointer select-none appearance-none items-center justify-between gap-x-1 rounded-md bg-sky-100 px-1 py-1.5 text-center text-base text-gray-900 transition-all marker:shadow hover:border-gray-400 hover:text-gray-800 focus:outline-none focus:ring-2 dark:border-gray-800 dark:bg-brandColor dark:text-white dark:hover:border-gray-400 ltr:pl-3 ltr:pr-3 rtl:pl-3 rtl:pr-3">
                    <span>
                        @lang('admin::app.leads.index.kanban.toolbar.filter')
                    </span>
                </div>
            </x-slot>

            <!-- Drawer Header -->
            <x-slot:header class="p-3.5">
                <div class="grid gap-3">
                    <div class="flex items-center justify-between">
                        <p class="text-xl font-semibold dark:text-white">
                            @lang('admin::app.leads.index.kanban.toolbar.filters')
                        </p>
                    </div>
                </div>
            </x-slot>

            <!-- Drawer Content -->
            <x-slot:content class="!p-0">
            </x-slot:content>
        </x-admin::drawer>
        
        <div class="z-10 hidden w-full divide-y divide-gray-100 rounded bg-white shadow dark:bg-gray-900"></div>
    </div>

    @include('admin::leads.index.view-switcher')
</div>
                
{!! view_render_event('admin.leads.index.kanban.toolbar.after') !!}