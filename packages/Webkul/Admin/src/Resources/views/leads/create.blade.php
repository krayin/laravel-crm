<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.leads.create.title')
    </x-slot>

    {!! view_render_event('krayin.admin.leads.create.form.before') !!}

    <!-- Create Lead Form -->
    <x-admin::form :action="route('admin.leads.store')">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    <x-admin::breadcrumbs name="leads.create" />
                </div>

                <div class="text-xl font-bold dark:text-white">
                    @lang('admin::app.leads.create.title')
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                <!-- Save button for person -->
                <div class="flex items-center gap-x-2.5">
                    {!! view_render_event('krayin.admin.leads.create.form_buttons.before') !!}

                    <button
                        type="submit"
                        class="primary-button"
                    >
                        @lang('admin::app.leads.create.save-btn')
                    </button>

                    {!! view_render_event('krayin.admin.leads.create.form_buttons.after') !!}
                </div>
            </div>
        </div>

        <input type="hidden" id="lead_pipeline_stage_id" name="lead_pipeline_stage_id" value="{{ request('stage_id') }}" />

        <!-- Lead Create Component -->
        <v-lead-create></v-lead-create>
    </x-admin::form>

    {!! view_render_event('krayin.admin.leads.create.form.after') !!}

    @pushOnce('scripts')
        <script 
            type="text/x-template"
            id="v-lead-create-template"
        >
            <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
                <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                    {!! view_render_event('krayin.admin.leads.edit.form_controls.before') !!}
                    
                    <div class="flex flex-col gap-5">
                        <!-- Tabs -->
                        <div class="border-b border-gray-200 text-center text-sm font-medium dark:border-gray-700">
                            <ul class="flex flex-wrap">
                                <li v-for="tab in tabs" :key="tab.id">
                                    <a
                                        :href="'#' + tab.id"
                                        :class="[
                                            'inline-block px-4 py-2 rounded-t-lg border-b-2',
                                            activeTab === tab.id
                                            ? 'text-blue-600 border-blue-600 dark:text-blue-500 dark:border-blue-500'
                                            : 'text-gray-600 dark:text-gray-300  border-transparent hover:text-gray-800 hover:border-gray-400 dark:hover:border-gray-400  dark:hover:text-white'
                                        ]"
                                        @click="scrollToSection(tab.id)"
                                        :text="tab.label"
                                    ></a>
                                </li>
                            </ul>
                        </div>

                        <!-- Details section -->
                        <div class="flex flex-col gap-4" id="lead-details">
                            <div class="flex flex-col gap-1">
                                <p class="text-base font-semibold dark:text-white">
                                    @lang('admin::app.leads.create.details')
                                </p>

                                <p class="text-gray-600 dark:text-white">
                                    @lang('admin::app.leads.create.details-info')
                                </p>
                            </div>

                            <div class="w-1/2">
                                <!-- Lead Details Title and Description -->
                                <x-admin::attributes
                                    :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                        ['code', 'NOTIN', ['lead_value', 'lead_type_id', 'lead_source_id', 'expected_close_date', 'user_id']],
                                        'entity_type' => 'leads',
                                        'quick_add'   => 1
                                    ])"
                                    :custom-validations="[
                                        'expected_close_date' => [
                                            'date_format:yyyy-MM-dd',
                                            'after:' .  \Carbon\Carbon::yesterday()->format('Y-m-d')
                                        ],
                                    ]"
                                />

                                <!-- Lead Details Oter input fields -->
                                <div class="flex gap-4 max-sm:flex-wrap">
                                    <div class="w-full">
                                        <x-admin::attributes
                                            :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                                ['code', 'IN', ['lead_value', 'lead_type_id', 'lead_source_id']],
                                                'entity_type' => 'leads',
                                                'quick_add'   => 1
                                            ])"
                                            :custom-validations="[
                                                'expected_close_date' => [
                                                    'date_format:yyyy-MM-dd',
                                                    'after:' .  \Carbon\Carbon::yesterday()->format('Y-m-d')
                                                ],
                                            ]"
                                        />
                                    </div>
                                        
                                    <div class="w-full">
                                        <x-admin::attributes
                                            :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                                ['code', 'IN', ['expected_close_date', 'user_id']],
                                                'entity_type' => 'leads',
                                                'quick_add'   => 1
                                            ])"
                                            :custom-validations="[
                                                'expected_close_date' => [
                                                    'date_format:yyyy-MM-dd',
                                                    'after:' .  \Carbon\Carbon::yesterday()->format('Y-m-d')
                                                ],
                                            ]"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Person -->
                        <div class="flex flex-col gap-4" id="contact-person">
                            <div class="flex flex-col gap-1">
                                <p class="text-base font-semibold dark:text-white">
                                    @lang('admin::app.leads.create.contact-person')
                                </p>

                                <p class="text-gray-600 dark:text-white">
                                    @lang('admin::app.leads.create.contact-info')
                                </p>
                            </div>

                            <div class="w-1/2">
                                <!-- Contact Person Component -->
                                @include('admin::leads.common.contact')
                            </div>
                        </div>

                        <!-- Product Section -->
                        <div class="flex flex-col gap-4" id="products">
                            <div class="flex flex-col gap-1">
                                <p class="text-base font-semibold dark:text-white">
                                    @lang('admin::app.leads.create.products')
                                </p>

                                <p class="text-gray-600 dark:text-white">
                                    @lang('admin::app.leads.create.products-info')
                                </p>
                            </div>

                            <div>
                                <!-- Product Component -->
                                @include('admin::leads.common.products')
                            </div>
                        </div>
                    </div>
                    {!! view_render_event('krayin.admin.leads.form_controls.after') !!}
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-lead-create', {
                template: '#v-lead-create-template',

                data() {
                    return {
                        activeTab: 'lead-details',

                        tabs: [
                            { id: 'lead-details', label: '@lang('admin::app.leads.create.details')' },
                            { id: 'contact-person', label: '@lang('admin::app.leads.create.contact-person')' },
                            { id: 'products', label: '@lang('admin::app.leads.create.products')' }
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