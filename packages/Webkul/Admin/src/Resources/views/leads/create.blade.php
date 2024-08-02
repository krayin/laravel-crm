<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.leads.create.title')
    </x-slot>

    <x-admin::form :action="route('admin.leads.store')">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    <x-admin::breadcrumbs 
                        name="leads.create" 
                    />
                </div>

                <div class="text-xl font-bold dark:text-gray-300">
                    @lang('admin::app.leads.create.title')
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                <!-- Save button for person -->
                <div class="flex items-center gap-x-2.5">
                    <button
                        type="submit"
                        class="primary-button"
                    >
                        @lang('admin::app.leads.create.save-btn')
                    </button>
                </div>
            </div>
        </div>

        <input type="hidden" id="lead_pipeline_stage_id" name="lead_pipeline_stage_id" value="{{ request('stage_id') }}" />

        <v-quote></v-quote>
    </x-admin::form>

    @pushOnce('scripts')
        <script 
            type="text/x-template"
            id="v-quote-template"
        >
            <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
                <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                    <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                        {!! view_render_event('admin.contacts.quotes.edit.form_controls.before') !!}
                       
                        <div class="border-b border-gray-200 text-center text-sm font-medium text-gray-500 dark:border-gray-700 dark:text-gray-400">
                            <ul class="flex flex-wrap">
                               <li class="me-2" v-for="tab in tabs" :key="tab.id">
                                    <a
                                        :href="'#' + tab.id"
                                        :class="[
                                            'inline-block p-4 rounded-t-lg border-b-2',
                                            activeTab === tab.id
                                            ? 'text-blue-600 border-blue-600 dark:text-blue-500 dark:border-blue-500'
                                            : 'text-gray-600 border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300'
                                        ]"
                                        @click="scrollToSection(tab.id)"
                                        :text="tab.label"
                                    ></a>
                                </li>
                            </ul>
                        </div>

                        <!-- Details section -->
                        <div 
                            class="mt-4"
                            id="lead-details"
                        >
                            <div class="mb-4 flex items-center justify-between gap-4">
                                <div class="flex flex-col gap-1">
                                    <p class="text-base font-semibold text-gray-800 dark:text-white">
                                        @lang('Details')
                                    </p>

                                    <p class="text-sm text-gray-600 dark:text-white">@lang('Put The Basic Information of the Lead')</p>
                                </div>
                            </div>

                            <div class="w-1/2">
                                @include('admin::common.custom-attributes.edit', [
                                    'customAttributes'  => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                        ['code', 'NOTIN', ['lead_value', 'lead_type_id', 'lead_source_id', 'expected_close_date', 'user_id']],
                                        'entity_type' => 'leads',
                                        'quick_add'   => 1
                                    ]),
                                    'customValidations' => [
                                        'expected_close_date' => [
                                            'date_format:yyyy-MM-dd',
                                            'after:' .  \Carbon\Carbon::yesterday()->format('Y-m-d')
                                        ],
                                    ],
                                ])

                                <div class="flex gap-4 max-sm:flex-wrap">
                                    <div class="mb-4 w-full">
                                        @include('admin::common.custom-attributes.edit', [
                                            'customAttributes'  => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                                ['code', 'IN', ['lead_value', 'lead_type_id', 'lead_source_id']],
                                                'entity_type' => 'leads',
                                                'quick_add'   => 1
                                            ]),
                                            'customValidations' => [
                                                {{-- 'expected_close_date' => [
                                                    'date_format:yyyy-MM-dd',
                                                    'after:' .  \Carbon\Carbon::yesterday()->format('Y-m-d')
                                                ], --}}
                                            ],
                                        ])
                                    </div>
                                        
                                    <div class="mb-4 w-full">
                                        @include('admin::common.custom-attributes.edit', [
                                            'customAttributes'  => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                                'entity_type' => 'leads',
                                                ['code', 'IN', ['expected_close_date', 'user_id']],
                                                'quick_add'   => 1
                                            ]),
                                            'customValidations' => [
                                                {{-- 'expected_close_date' => [
                                                    'date_format:yyyy-MM-dd',
                                                    'after:' .  \Carbon\Carbon::yesterday()->format('Y-m-d')
                                                ], --}}
                                            ],
                                        ])
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Person -->
                        <div
                            class="mt-4"
                            id="contact-person"
                        >
                            <div class="mb-4 flex items-center justify-between gap-4">
                                <div class="flex flex-col gap-1">
                                    <p class="text-base font-semibold text-gray-800 dark:text-white">
                                        @lang('Contact Person')
                                    </p>

                                    <p class="text-sm text-gray-600 dark:text-white">@lang('Information About the Contact Person')</p>
                                </div>
                            </div>

                            <div class="w-1/2">
                                @include('admin::leads.common.contact')
                            </div>
                        </div>

                        <!-- Product Section -->
                        <div
                            class="mt-4"
                            id="products"
                        >
                            <div class="mb-4 flex items-center justify-between gap-4">
                                <div class="flex flex-col gap-1">
                                    <p class="text-base font-semibold text-gray-800 dark:text-white">
                                        @lang('Products')
                                    </p>

                                    <p class="text-sm text-gray-600 dark:text-white">@lang('Information About the Products')</p>
                                </div>
                            </div>

                            <!-- Product Component -->
                            @include('admin::leads.common.products')
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
                        activeTab: 'lead-details',

                        tabs: [
                            { id: 'lead-details', label: '@lang('Deatils')' },
                            { id: 'contact-person', label: '@lang('Contact Person')' },
                            { id: 'products', label: '@lang('Products')' }
                        ],
                    };
                },

                methods: {
                    /**
                     * Scroll to the section.
                     * 
                     * @param {String} tabId
                     * 
                     * @returns {void}
                     */
                    scrollToSection(tabId) {
                        console.log(tabId);
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

    @pushOnce('styles')
        <style>
            html {
                scroll-behavior: smooth;
            }
        </style>
    @endPushOnce    
</x-admin::layouts>