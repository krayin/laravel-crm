<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.quotes.create.title')
    </x-slot>

    <x-admin::form
        :action="route('admin.quotes.store')"
        method="PUT"
    >
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    <x-admin::breadcrumbs 
                        name="quotes.create" 
                    />
                </div>

                <div class="text-xl font-bold dark:text-gray-300">
                    @lang('admin::app.quotes.create.title')
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                <!-- Save button for person -->
                <div class="flex items-center gap-x-2.5">
                    <button
                        type="submit"
                        class="primary-button"
                    >
                        @lang('admin::app.quotes.create.save-btn')
                    </button>
                </div>
            </div>
        </div>

        <v-quote></v-quote>
    </x-admin::form>

    @pushOnce('scripts')
        <script 
            type="text/x-template"
            id="v-quote-template"
        >
            <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
                <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                    <div class="box-shadow rounded bg-white p-2 dark:bg-gray-900">
                        {!! view_render_event('admin.contacts.quotes.edit.form_controls.before') !!}
                       
                        <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">
                            <ul class="flex flex-wrap">
                               <li class="me-2" v-for="tab in tabs" :key="tab.id">
                                    <a
                                        href="javascript:void(0);"
                                        @click="scrollToSection(tab.id)"
                                        :class="[
                                            'inline-block p-4 rounded-t-lg border-b-2',
                                            activeTab === tab.id
                                            ? 'text-blue-600 border-blue-600 dark:text-blue-500 dark:border-blue-500'
                                            : 'text-gray-600 border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300'
                                        ]"
                                    >
                                        @{{ tab.label }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                        {!! view_render_event('admin.contacts.quotes.edit.form_controls.after') !!}
                    </div>
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-quote', {
                template: '#v-quote-template',
                data() {
                    return {
                        activeTab: 'quote-info',
                        tabs: [
                            { id: 'quote-info', label: 'Quote Information' },
                            { id: 'address-info', label: 'Address Information' },
                            { id: 'quote-items', label: 'Quote Items' }
                        ]
                    };
                },
                methods: {
                    scrollToSection(tabId) {
                        this.activeTab = tabId;

                        const section = document.getElementById(tabId);

                        if (section) {
                            section.scrollIntoView({ behavior: 'smooth' });
                        }
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>