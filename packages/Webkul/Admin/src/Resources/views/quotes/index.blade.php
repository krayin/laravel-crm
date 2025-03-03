<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.quotes.index.title')
    </x-slot>

    <v-qoute>
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <!-- Bredcrumbs -->
                    <x-admin::breadcrumbs name="quotes" />
        
                    <div class="text-xl font-bold dark:text-white">
                        @lang('admin::app.quotes.index.title')
                    </div>
                </div>
        
                <div class="flex items-center gap-x-2.5">
                    <!-- Create button for Quote -->
                    <div class="flex items-center gap-x-2.5">
                        @if (bouncer()->hasPermission('quotes.create'))
                            <a 
                                href="{{ route('admin.quotes.create') }}"
                                class="primary-button"
                            >
                                @lang('admin::app.quotes.index.create-btn')
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        
            <!-- DataGrid Shimmer -->
            <x-admin::shimmer.datagrid />
        </div>
    </v-qoute>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-qoute-template"
        >
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                    <div class="flex flex-col gap-2">
                        <!-- Bredcrumbs -->
                        <x-admin::breadcrumbs name="quotes" />
        
                        <div class="text-xl font-bold dark:text-white">
                            @lang('admin::app.quotes.index.title')
                        </div>
                    </div>

                    <div class="flex items-center gap-x-2.5">
                        <!-- Create button for person -->
                        <div class="flex items-center gap-x-2.5">
                            {!! view_render_event('admin.quotes.index.create_button.before') !!}
                            
                            @if (bouncer()->hasPermission('quotes.create'))
                                <a 
                                    href="{{ route('admin.quotes.create') }}"
                                    class="primary-button"
                                >
                                    @lang('admin::app.quotes.index.create-btn')
                                </a>
                            @endif
            
                            {!! view_render_event('admin.quotes.index.create_button.after') !!}
                        </div>
                    </div>
                </div>
            
                {!! view_render_event('admin.quotes.index.datagrid.before') !!}
            
                <!-- DataGrid -->
                <x-admin::datagrid :src="route('admin.quotes.index')" />

                {!! view_render_event('admin.quotes.index.datagrid.after') !!}
            </div>
        </script>

        <script type="module">
            app.component('v-qoute', {
                template: '#v-qoute-template',
            });
        </script>
    @endPushOnce
</x-admin::layouts>
