<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.settings.webforms.index.title')
    </x-slot>

    <v-webform>
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <div class="flex cursor-pointer items-center">
                        <!-- Bredcrumbs -->
                        <x-admin::breadcrumbs name="settings.web_forms" />
                    </div>
        
                    <div class="text-xl font-bold dark:text-white">
                        @lang('admin::app.settings.webforms.index.title')
                    </div>
                </div>
        
                <div class="flex items-center gap-x-2.5">
                    <!-- Create button for person -->
                    <div class="flex items-center gap-x-2.5">
                        @if (bouncer()->hasPermission('admin.settings.web_forms.create'))
                            <button
                                type="button"
                                class="primary-button"
                            >
                                @lang('admin::app.settings.webforms.index.create-btn')
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        
            <!-- DataGrid Shimmer -->
            <x-admin::shimmer.datagrid />
        </div>
    </v-webform>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-webform-template"
        >
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                    <div class="flex flex-col gap-2">
                        <div class="flex cursor-pointer items-center">
                            <!-- Bredcrumbs -->
                            <x-admin::breadcrumbs name="settings.web_forms" />
                        </div>
            
                        <div class="text-xl font-bold dark:text-white">
                            @lang('admin::app.settings.webforms.index.title')
                        </div>
                    </div>

                    <div class="flex items-center gap-x-2.5">
                        <!-- Create button for person -->
                        <div class="flex items-center gap-x-2.5">
                            {!! view_render_event('admin.settings.web_forms.index.create_button.before') !!}
            
                            @if (bouncer()->hasPermission('admin.settings.web_forms.create'))
                                <a
                                    href="{{ route('admin.settings.web_forms.create') }}"
                                    class="primary-button"
                                >
                                    @lang('admin::app.settings.webforms.index.create-btn')
                                </a>
                            @endif

                            {!! view_render_event('admin.settings.web_forms.index.create_button.after') !!}
                        </div>
                    </div>
                </div>
            
                {!! view_render_event('admin.settings.web_forms.index.datagrid.before') !!}
            
                <!-- DataGrid -->
                <x-admin::datagrid :src="route('admin.settings.web_forms.index')" />

                {!! view_render_event('admin.settings.web_forms.index.datagrid.after') !!}
            </div>
        </script>

        <script type="module">
            app.component('v-webform', {
                template: '#v-webform-template',
                data() {
                    return {};
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
