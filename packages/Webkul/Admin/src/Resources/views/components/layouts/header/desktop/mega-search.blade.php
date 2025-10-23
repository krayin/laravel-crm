<v-mega-search>
    <div class="relative flex w-[550px] max-w-[550px] items-center max-lg:w-[400px] ltr:ml-2.5 rtl:mr-2.5">
        <i class="icon-search absolute top-2 flex items-center text-2xl ltr:left-3 rtl:right-3"></i>

        <input
            type="text"
            class="block w-full rounded-3xl border bg-white px-10 py-1.5 leading-6 text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
            placeholder="@lang('admin::app.components.layouts.header.mega-search.title')"
        >
    </div>
</v-mega-search>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-mega-search-template"
    >
        <div class="relative flex w-[550px] max-w-[550px] items-center max-lg:w-[400px] ltr:ml-2.5 rtl:mr-2.5">
            <i class="icon-search absolute top-2 flex items-center text-2xl ltr:left-3 rtl:right-3"></i>

            <input
                type="text"
                class="peer block w-full rounded-3xl border bg-white px-10 py-1.5 leading-6 text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                :class="{'border-gray-400': isDropdownOpen}"
                placeholder="@lang('admin::app.components.layouts.header.mega-search.title')"
                v-model.lazy="searchTerm"
                @click="searchTerm.length >= 2 ? isDropdownOpen = true : {}"
                v-debounce="500"
            >

            <div
                class="absolute top-10 z-10 w-full rounded-lg border bg-white shadow-[0px_0px_0px_0px_rgba(0,0,0,0.10),0px_1px_3px_0px_rgba(0,0,0,0.10),0px_5px_5px_0px_rgba(0,0,0,0.09),0px_12px_7px_0px_rgba(0,0,0,0.05),0px_22px_9px_0px_rgba(0,0,0,0.01),0px_34px_9px_0px_rgba(0,0,0,0.00)] dark:border-gray-800 dark:bg-gray-900"
                v-if="isDropdownOpen"
            >
                <!-- Search Tabs -->
                <div class="flex overflow-x-auto border-b text-sm text-gray-600 dark:border-gray-800 dark:text-gray-300">
                    <div
                        class="cursor-pointer p-4 hover:bg-gray-100 dark:hover:bg-gray-950"
                        :class="{ 'border-b-2 border-brandColor': activeTab == tab.key }"
                        v-for="tab in tabs"
                        @click="activeTab = tab.key; search();"
                    >
                        @{{ tab.title }}
                    </div>
                </div>

                <!-- Searched Results -->
                <template v-if="activeTab == 'products'">
                    <template v-if="isLoading">
                        <x-admin::shimmer.header.mega-search.products />
                    </template>

                    <template v-else>
                        <div class="grid max-h-[400px] overflow-y-auto">
                            <template v-for="product in searchedResults.products">
                                <a
                                    :href="'{{ route('admin.products.view', ':id') }}'.replace(':id', product.id)"
                                    class="flex cursor-pointer justify-between gap-2.5 border-b border-slate-300 p-4 last:border-b-0 hover:bg-gray-100 dark:border-gray-800 dark:hover:bg-gray-950"
                                >
                                    <!-- Left Information -->
                                    <div class="flex gap-2.5">
                                        <!-- Details -->
                                        <div class="grid place-content-start gap-1.5">
                                            <p class="text-base font-semibold text-gray-600 dark:text-gray-300">
                                                @{{ product.name }}
                                            </p>

                                            <p class="text-gray-500">
                                                @{{ "@lang(':sku')".replace(':sku', product.sku) }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Right Information -->
                                    <div class="grid place-content-center gap-1 text-right">
                                        <!-- Formatted Price -->
                                        <p class="font-semibold text-gray-600 dark:text-gray-300">
                                            @{{ $admin.formatPrice(product.price) }}
                                        </p>
                                    </div>
                                </a>
                            </template>

                        </div>

                        <div class="flex border-t p-3 dark:border-gray-800">
                            <template v-if="searchedResults.products.length">
                                <a
                                    :href="'{{ route('admin.products.index') }}?search=:query'.replace(':query', searchTerm)"
                                    class="cursor-pointer text-xs font-semibold text-brandColor transition-all hover:underline"
                                >

                                    @{{ `@lang('admin::app.components.layouts.header.mega-search.explore-all-matching-products')`.replace(':query', searchTerm).replace(':count', searchedResults.products.length) }}
                                </a>
                            </template>

                            <template v-else>
                                <a
                                    href="{{ route('admin.products.index') }}"
                                    class="cursor-pointer text-xs font-semibold text-brandColor transition-all hover:underline"
                                >
                                    @lang('admin::app.components.layouts.header.mega-search.explore-all-products')
                                </a>
                            </template>
                        </div>
                    </template>
                </template>

                <template v-if="activeTab == 'leads'">
                    <template v-if="isLoading">
                        <x-admin::shimmer.header.mega-search.leads />
                    </template>

                    <template v-else>
                        <div class="grid max-h-[400px] overflow-y-auto">
                            <template v-for="lead in searchedResults.leads">
                                <a
                                    :href="'{{ route('admin.leads.view', ':id') }}'.replace(':id', lead.id)"
                                    class="flex cursor-pointer justify-between gap-2.5 border-b border-slate-300 p-4 last:border-b-0 hover:bg-gray-100 dark:border-gray-800 dark:hover:bg-gray-950"
                                >
                                    <!-- Left Information -->
                                    <div class="flex gap-2.5">
                                        <!-- Details -->
                                        <div class="grid place-content-start gap-1.5">
                                            <p class="text-base font-semibold text-gray-600 dark:text-gray-300">
                                                @{{ lead.title }}
                                            </p>

                                            <p class="text-gray-500">
                                                @{{ lead.description }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Right Information -->
                                    <div class="grid place-content-center gap-1 text-right">
                                        <!-- Formatted Price -->
                                        <p class="font-semibold text-gray-600 dark:text-gray-300">
                                            @{{ $admin.formatPrice(lead.lead_value) }}
                                        </p>
                                    </div>
                                </a>
                            </template>
                        </div>

                        <div class="flex border-t p-3 dark:border-gray-800">
                            <template v-if="searchedResults.leads.length">
                                <a
                                    :href="'{{ route('admin.leads.index') }}?search=:query'.replace(':query', searchTerm)"
                                    class="cursor-pointer text-xs font-semibold text-brandColor transition-all hover:underline"
                                >
                                    @{{ `@lang('admin::app.components.layouts.header.mega-search.explore-all-matching-leads')`.replace(':query', searchTerm).replace(':count', searchedResults.leads.length) }}
                                </a>
                            </template>

                            <template v-else>
                                <a
                                    href="{{ route('admin.leads.index') }}"
                                    class="cursor-pointer text-xs font-semibold text-brandColor transition-all hover:underline"
                                >
                                    @lang('admin::app.components.layouts.header.mega-search.explore-all-leads')
                                </a>
                            </template>
                        </div>
                    </template>
                </template>

                <template v-if="activeTab == 'persons'">
                    <template v-if="isLoading">
                        <x-admin::shimmer.header.mega-search.persons />
                    </template>

                    <template v-else>
                        <div class="grid max-h-[400px] overflow-y-auto">
                            <template v-for="person in searchedResults.persons">
                                <a
                                    :href="'{{ route('admin.contacts.persons.view', ':id') }}'.replace(':id', person.id)"
                                    class="flex cursor-pointer justify-between gap-2.5 border-b border-slate-300 p-4 last:border-b-0 hover:bg-gray-100 dark:border-gray-800 dark:hover:bg-gray-950"
                                >
                                    <!-- Left Information -->
                                    <div class="flex gap-2.5">
                                        <!-- Details -->
                                        <div class="grid place-content-start gap-1.5">
                                            <p class="text-base font-semibold text-gray-600 dark:text-gray-300">
                                                @{{ person.name }}
                                            </p>

                                            <p class="text-gray-500">
                                                @{{ person.emails.map((item) => `${item.value}(${item.label})`).join(', ') }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </template>
                        </div>

                        <div class="flex border-t p-3 dark:border-gray-800">
                            <template v-if="searchedResults.persons.length">
                                <a
                                    :href="'{{ route('admin.contacts.persons.index') }}?search=:query'.replace(':query', searchTerm)"
                                    class="cursor-pointer text-xs font-semibold text-brandColor transition-all hover:underline"
                                >
                                    @{{ `@lang('admin::app.components.layouts.header.mega-search.explore-all-matching-contacts')`.replace(':query', searchTerm).replace(':count', searchedResults.persons.length) }}
                                </a>
                            </template>

                            <template v-else>
                                <a
                                    href="{{ route('admin.contacts.persons.index') }}"
                                    class="cursor-pointer text-xs font-semibold text-brandColor transition-all hover:underline"
                                >
                                    @lang('admin::app.components.layouts.header.mega-search.explore-all-contacts')
                                </a>
                            </template>
                        </div>
                    </template>
                </template>

                <template v-if="activeTab == 'quotes'">
                    <template v-if="isLoading">
                        <x-admin::shimmer.header.mega-search.quotes />
                    </template>

                    <template v-else>
                        <div class="grid max-h-[400px] overflow-y-auto">
                            <template v-for="quote in searchedResults.quotes">
                                <a
                                    :href="'{{ route('admin.quotes.edit', ':id') }}'.replace(':id', quote.id)"
                                    class="flex cursor-pointer justify-between gap-2.5 border-b border-slate-300 p-4 last:border-b-0 hover:bg-gray-100 dark:border-gray-800 dark:hover:bg-gray-950"
                                >
                                    <!-- Left Information -->
                                    <div class="flex gap-2.5">
                                        <!-- Details -->
                                        <div class="grid place-content-start gap-1.5">
                                            <p class="text-base font-semibold text-gray-600 dark:text-gray-300">
                                                @{{ quote.subject }}
                                            </p>

                                            <p class="text-gray-500">
                                                @{{ quote.description }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </template>
                        </div>

                        <div class="flex border-t p-3 dark:border-gray-800">
                            <template v-if="searchedResults.quotes.length">
                                <a
                                    :href="'{{ route('admin.quotes.index') }}?search=:query'.replace(':query', searchTerm)"
                                    class="cursor-pointer text-xs font-semibold text-brandColor transition-all hover:underline"
                                >
                                    @{{ `@lang('admin::app.components.layouts.header.mega-search.explore-all-matching-quotes')`.replace(':query', searchTerm).replace(':count', searchedResults.quotes.length) }}
                                </a>
                            </template>

                            <template v-else>
                                <a
                                    href="{{ route('admin.quotes.index') }}"
                                    class="cursor-pointer text-xs font-semibold text-brandColor transition-all hover:underline"
                                >
                                    @lang('admin::app.components.layouts.header.mega-search.explore-all-quotes')
                                </a>
                            </template>
                        </div>
                    </template>
                </template>

                <template v-if="activeTab == 'settings'">
                    <template v-if="isLoading">
                        <x-admin::shimmer.header.mega-search.settings />
                    </template>

                    <template v-else>
                        <div class="grid max-h-[400px] overflow-y-auto">
                            <template v-for="setting in searchedResults.settings">
                                <a
                                    :href="setting.url"
                                    class="flex cursor-pointer justify-between gap-2.5 border-b border-slate-300 p-4 last:border-b-0 hover:bg-gray-100 dark:border-gray-800 dark:hover:bg-gray-950"
                                >
                                    <!-- Left Information -->
                                    <div class="flex gap-2.5">
                                        <!-- Details -->
                                        <div class="grid place-content-start gap-1.5">
                                            <p class="text-base font-semibold text-gray-600 dark:text-gray-300">
                                                @{{ setting.name }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </template>
                        </div>

                        <template v-if="! searchedResults.settings.length">
                            <div class="flex border-t p-3 dark:border-gray-800">
                                <a
                                    href="{{ route('admin.settings.index') }}"
                                    class="cursor-pointer text-xs font-semibold text-brandColor transition-all hover:underline"
                                >
                                    @lang('admin::app.components.layouts.header.mega-search.explore-all-settings')
                                </a>
                            </div>
                        </template>
                    </template>
                </template>

                <template v-if="activeTab == 'configurations'">
                    <template v-if="isLoading">
                        <x-admin::shimmer.header.mega-search.configurations />
                    </template>

                    <template v-else>
                        <div class="grid max-h-[400px] overflow-y-auto">
                            <template v-for="configuration in searchedResults.configurations">
                                <a
                                    :href="configuration.url"
                                    class="flex cursor-pointer justify-between gap-2.5 border-b border-slate-300 p-4 last:border-b-0 hover:bg-gray-100 dark:border-gray-800 dark:hover:bg-gray-950"
                                >
                                    <!-- Left Information -->
                                    <div class="flex gap-2.5">
                                        <!-- Details -->
                                        <div class="grid place-content-start gap-1.5">
                                            <p class="text-base font-semibold text-gray-600 dark:text-gray-300">
                                                @{{ configuration.title }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </template>
                        </div>

                        <template v-if="! searchedResults.configurations.length">
                            <div class="flex border-t p-3 dark:border-gray-800">
                                <a
                                    href="{{ route('admin.configuration.index') }}"
                                    class="cursor-pointer text-xs font-semibold text-brandColor transition-all hover:underline"
                                >
                                    @lang('admin::app.components.layouts.header.mega-search.explore-all-configurations')
                                </a>
                            </div>
                        </template>
                    </template>
                </template>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-mega-search', {
            template: '#v-mega-search-template',

            data() {
                return  {
                    activeTab: 'leads',

                    isDropdownOpen: false,

                    tabs: {
                        leads: {
                            key: 'leads',
                            title: '@lang('admin::app.components.layouts.header.mega-search.tabs.leads')',
                            is_active: true,
                            endpoint: '{{ route('admin.leads.search') }}',
                            query_params: [
                                {
                                    search: 'title',
                                    searchFields: 'title:like',
                                },
                                {
                                    search: 'user.name',
                                    searchFields: 'user.name:like',
                                },
                                {
                                    search: 'person.name',
                                    searchFields: 'person.name:like',
                                },
                            ],
                        },

                        quotes: {
                            key: 'quotes',
                            title: '@lang('admin::app.components.layouts.header.mega-search.tabs.quotes')',
                            is_active: false,
                            endpoint: '{{ route('admin.quotes.search') }}',
                            query_params: [
                                {
                                    search: 'subject',
                                    searchFields: 'subject:like',
                                },
                                {
                                    search: 'description',
                                    searchFields: 'description:like',
                                },
                                {
                                    search: 'user.name',
                                    searchFields: 'user.name:like',
                                },
                                {
                                    search: 'person.name',
                                    searchFields: 'person.name:like',
                                },
                            ],
                        },

                        products: {
                            key: 'products',
                            title: '@lang('admin::app.components.layouts.header.mega-search.tabs.products')',
                            is_active: false,
                            endpoint: '{{ route('admin.products.search') }}',
                            query_params: [
                                {
                                    search: 'name',
                                    searchFields: 'name:like',
                                },
                                {
                                    search: 'sku',
                                    searchFields: 'sku:like',
                                },
                                {
                                    search: 'description',
                                    searchFields: 'description:like',
                                },
                            ],
                        },

                        persons: {
                            key: 'persons',
                            title: '@lang('admin::app.components.layouts.header.mega-search.tabs.persons')',
                            is_active: false,
                            endpoint: '{{ route('admin.contacts.persons.search') }}',
                            query_params: [
                                {
                                    search: 'name',
                                    searchFields: 'name:like',
                                },
                                {
                                    search: 'job_title',
                                    searchFields: 'job_title:like',
                                },
                                {
                                    search: 'user.name',
                                    searchFields: 'user.name:like',
                                },
                                {
                                    search: 'organization.name',
                                    searchFields: 'organization.name:like',
                                },
                            ],
                        },

                        settings: {
                            key: 'settings',
                            title: '@lang('Settings')',
                            is_active: false,
                            endpoint: '{{ route('admin.settings.search') }}',
                            query: '',
                        },

                        configurations: {
                            key: 'configurations',
                            title: '@lang('Configurations')',
                            is_active: false,
                            endpoint: '{{ route('admin.configuration.search') }}',
                            query: '',
                        },
                    },

                    isLoading: false,

                    searchTerm: '',

                    searchedResults: {
                        leads: [],
                        quotes: [],
                        products: [],
                        persons: [],
                        settings: [],
                        configurations: [],
                    },

                    params: {
                        search: '',
                        searchFields: '',
                    },
                };
            },

            watch: {
                searchTerm: 'updateSearchParams',

                activeTab: 'updateSearchParams',
            },

            created() {
                window.addEventListener('click', this.handleFocusOut);
            },

            beforeDestroy() {
                window.removeEventListener('click', this.handleFocusOut);
            },

            methods: {
                search(endpoint = null) {
                    if (! endpoint) {
                        return;
                    }

                    if (this.searchTerm.length <= 1) {
                        this.searchedResults[this.activeTab] = [];

                        this.isDropdownOpen = false;

                        return;
                    }

                    this.isDropdownOpen = true;

                    this.$axios.get(endpoint, {
                            params: {
                                ...this.params,
                            },
                        })
                        .then((response) => {
                            this.searchedResults[this.activeTab] = response.data.data;
                        })
                        .catch((error) => {})
                        .finally(() => this.isLoading = false);
                },

                handleFocusOut(e) {
                    if (! this.$el.contains(e.target)) {
                        this.isDropdownOpen = false;
                    }
                },

                updateSearchParams() {
                    const newTerm = this.searchTerm;

                    this.params = {
                        search: '',
                        searchFields: '',
                    };

                    const tab = this.tabs[this.activeTab];

                    if (
                        tab.key === 'settings'
                        || tab.key === 'configurations'
                    ) {
                        this.params = null;

                        this.search(`${tab.endpoint}?query=${newTerm}`);

                        return;
                    }

                    this.params.search += tab.query_params.map((param) => `${param.search}:${newTerm};`).join('');

                    this.params.searchFields += tab.query_params.map((param) => `${param.searchFields};`).join('');

                    this.search(tab.endpoint);
                },
            },
        });
    </script>
@endPushOnce
