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
        <div
            class="relative flex w-[550px] max-w-[550px] items-center max-lg:w-[400px] ltr:ml-2.5 rtl:mr-2.5"
            ref="headerSearch"
        >
            <i class="icon-search absolute top-2 flex items-center text-2xl ltr:left-3 rtl:right-3"></i>

            <input
                type="text"
                class="peer block w-full rounded-3xl border border-gray-300 bg-white px-10 py-1.5 leading-6 text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                :class="{'border-gray-400': isDropdownOpen}"
                placeholder="@lang('admin::app.components.layouts.header.mega-search.title')"
                v-model.lazy="searchTerm"
                @click="searchTerm.length >= 2 ? isDropdownOpen = true : {}"
                ref="searchInput"
                v-debounce="500"
            >

            <kbd
                class="pointer-events-none absolute select-none rounded border border-gray-300 bg-gray-100 px-1.5 py-0.5 text-xs font-semibold text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 ltr:right-3 rtl:left-3"
                v-if="! searchTerm"
                v-text="shortcutLabel"
            ></kbd>

            <div
                class="absolute top-10 z-10 w-full rounded-lg border border-gray-300 bg-white shadow-[0px_0px_0px_0px_rgba(0,0,0,0.10),0px_1px_3px_0px_rgba(0,0,0,0.10),0px_5px_5px_0px_rgba(0,0,0,0.09),0px_12px_7px_0px_rgba(0,0,0,0.05),0px_22px_9px_0px_rgba(0,0,0,0.01),0px_34px_9px_0px_rgba(0,0,0,0.00)] dark:border-gray-800 dark:bg-gray-900"
                v-if="isDropdownOpen && ! isModalOpen"
            >
                <!-- Search Tabs -->
                <div class="flex overflow-x-auto border-b text-sm text-gray-600 dark:border-gray-800 dark:text-gray-300">
                    <div
                        class="flex cursor-pointer items-center gap-2 whitespace-nowrap px-4 py-3.5 hover:bg-gray-100 dark:hover:bg-gray-950"
                        :class="{ 'border-b-2 border-brandColor font-semibold text-gray-800 dark:text-white': activeTab == tab.key }"
                        v-for="tab in tabs"
                        @click="activeTab = tab.key; search();"
                    >
                        @{{ tab.title }}
                        <span
                            class="inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full px-1.5 text-xs font-semibold leading-none transition-colors"
                            :class="resultCounts[tab.key]
                                ? (activeTab == tab.key
                                    ? 'bg-brandColor text-white'
                                    : 'bg-sky-100 text-sky-600 dark:bg-brandColor dark:text-white')
                                : 'bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500'"
                            v-if="resultCounts[tab.key] !== null"
                            :data-section-count="tab.key"
                            v-text="resultCounts[tab.key] > 99 ? '99+' : resultCounts[tab.key]"
                        ></span>
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
                                    :href="'{{ route('admin.products.index') }}?search=:query'.replace(':query', encodeURIComponent(searchTerm))"
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
                                    {{--
                                        `view_type=table` is requested explicitly because the leads
                                        listing defaults to the kanban board, and only the datagrid
                                        reads `?search=` — following this link to the default view
                                        landed on the board showing every lead, which contradicted
                                        the count the link itself advertises.
                                    --}}
                                    :href="'{{ route('admin.leads.index') }}?view_type=table&search=:query'.replace(':query', encodeURIComponent(searchTerm))"
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
                                    :href="'{{ route('admin.contacts.persons.index') }}?search=:query'.replace(':query', encodeURIComponent(searchTerm))"
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
                                    :href="'{{ route('admin.quotes.index') }}?search=:query'.replace(':query', encodeURIComponent(searchTerm))"
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

        <Teleport to="body">
            <div
                class="fixed inset-0 z-[10004] flex items-start justify-center bg-gray-500 bg-opacity-50 p-4 pt-[10vh]"
                style="backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
                v-if="isModalOpen"
                @click.self="closeModal"
            >
                <div class="w-full max-w-[640px] overflow-hidden rounded-xl bg-white shadow-xl dark:bg-gray-900">
                    <div class="relative flex items-center border-b border-gray-200 dark:border-gray-800">
                        <i class="icon-search absolute flex items-center text-2xl text-gray-400 ltr:left-4 rtl:right-4"></i>

                        <input
                            type="text"
                            class="w-full bg-transparent px-12 py-4 text-base leading-6 text-gray-700 outline-none placeholder:text-gray-400 dark:text-gray-200"
                            placeholder="@lang('admin::app.components.layouts.header.mega-search.title')"
                            v-model.lazy="searchTerm"
                            ref="modalSearchInput"
                            v-debounce="500"
                        >

                        <kbd
                            class="pointer-events-none absolute select-none rounded border border-gray-300 bg-gray-50 px-1.5 py-0.5 font-sans text-xs font-medium text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 ltr:right-4 rtl:left-4"
                            @click="closeModal"
                        >
                            Esc
                        </kbd>
                    </div>

                    <!-- Section tabs, carrying the same counts as the header dropdown. -->
                    <div class="flex overflow-x-auto border-b border-gray-200 text-sm text-gray-600 dark:border-gray-800 dark:text-gray-300">
                        <div
                            class="flex cursor-pointer items-center gap-2 whitespace-nowrap px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-950"
                            :class="{ 'border-b-2 border-brandColor font-semibold text-gray-800 dark:text-white': activeTab == tab.key }"
                            v-for="tab in tabs"
                            @click="activeTab = tab.key; search();"
                        >
                            @{{ tab.title }}

                            <span
                                class="inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full px-1.5 text-xs font-semibold leading-none transition-colors"
                                :class="resultCounts[tab.key]
                                    ? (activeTab == tab.key
                                        ? 'bg-brandColor text-white'
                                        : 'bg-sky-100 text-sky-600 dark:bg-brandColor dark:text-white')
                                    : 'bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500'"
                                v-if="resultCounts[tab.key] !== null"
                                :data-modal-section-count="tab.key"
                                v-text="resultCounts[tab.key] > 99 ? '99+' : resultCounts[tab.key]"
                            ></span>
                        </div>
                    </div>

                    <!-- Results for the active section. -->
                    <div class="max-h-[50vh] overflow-y-auto">
                        <template v-if="searchTerm.length <= 1">
                            <p class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                @lang('admin::app.components.layouts.header.mega-search.shortcut-hint')
                            </p>
                        </template>

                        <template v-else-if="! (searchedResults[activeTab] || []).length">
                            <p class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                @lang('admin::app.components.layouts.header.mega-search.empty-results')
                            </p>
                        </template>

                        <template v-else>
                            <a
                                class="flex items-center gap-2.5 border-b border-gray-100 px-4 py-3 text-sm text-gray-700 last:border-b-0 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-950"
                                v-for="record in searchedResults[activeTab]"
                                :href="recordUrl(record)"
                                :data-modal-result="activeTab"
                            >
                                <span v-text="record.name || record.title || record.subject || record.id"></span>
                            </a>
                        </template>
                    </div>
                </div>
            </div>
        </Teleport>
    </script>

    <script type="module">
        app.component('v-mega-search', {
            template: '#v-mega-search-template',

            data() {
                return  {
                    activeTab: 'leads',

                    isDropdownOpen: false,

                    /**
                     * Whether the search is open as a modal, which is what Ctrl/⌘ K opens. The
                     * header field stays where it is; the modal is a second way in, so the search
                     * is reachable from any page without first finding the header.
                     */
                    isModalOpen: false,

                    tabs: {
                        leads: {
                            key: 'leads',
                            title: "@lang('admin::app.components.layouts.header.mega-search.tabs.leads')",
                            is_active: true,
                            endpoint: "{{ route('admin.leads.search') }}",
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
                            title: "@lang('admin::app.components.layouts.header.mega-search.tabs.quotes')",
                            is_active: false,
                            endpoint: "{{ route('admin.quotes.search') }}",
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
                            title: "@lang('admin::app.components.layouts.header.mega-search.tabs.products')",
                            is_active: false,
                            endpoint: "{{ route('admin.products.search') }}",
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
                            title: "@lang('admin::app.components.layouts.header.mega-search.tabs.persons')",
                            is_active: false,
                            endpoint: "{{ route('admin.contacts.persons.search') }}",
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
                            title: "@lang('admin::app.components.layouts.header.mega-search.tabs.settings')",
                            is_active: false,
                            endpoint: "{{ route('admin.settings.search') }}",
                            query: '',
                        },

                        configurations: {
                            key: 'configurations',
                            title: "@lang('admin::app.components.layouts.header.mega-search.tabs.configurations')",
                            is_active: false,
                            endpoint: "{{ route('admin.configuration.search') }}",
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

                    /**
                     * How many records each section holds for the current term.
                     *
                     * `null` means "not counted yet" and is deliberately distinct from `0`: the
                     * badge is hidden while a count is unknown, so an in-flight section never reads
                     * as an empty one.
                     */
                    resultCounts: {
                        leads: null,
                        quotes: null,
                        products: null,
                        persons: null,
                        settings: null,
                        configurations: null,
                    },

                    params: {
                        search: '',
                        searchFields: '',
                    },
                };
            },

            watch: {
                searchTerm(...args) {
                    this.updateSearchParams(...args);

                    /**
                     * Counting is driven by the term alone. Switching tabs re-runs the active
                     * section's own search, but it does not change how many records any section
                     * holds, so the counts are not refetched for it.
                     */
                    this.countAllSections();
                },

                activeTab: 'updateSearchParams',
            },

            computed: {
                /**
                 * The shortcut as shown in the field. `Ctrl + K` is written out in full rather than
                 * abbreviated, so it reads the same way it is spoken.
                 */
                shortcutLabel() {
                    return 'Ctrl + K';
                },
            },

            created() {
                window.addEventListener('click', this.handleFocusOut);

                window.addEventListener('keydown', this.handleShortcut);
            },

            beforeDestroy() {
                window.removeEventListener('click', this.handleFocusOut);

                window.removeEventListener('keydown', this.handleShortcut);
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

                    /**
                     * The modal runs the same search, so opening the dropdown here would put both
                     * on screen at once showing identical results. Only the header's own search
                     * opens it.
                     */
                    this.isDropdownOpen = ! this.isModalOpen;

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

                /**
                 * Count every section for the current term, not just the one on screen.
                 *
                 * The dropdown only ever fetched the active tab, so the other sections gave no sign
                 * of whether they held anything — a user had to open each one to find out. Each
                 * section is counted with the same query its own tab would issue, so a badge always
                 * agrees with the list that tab shows.
                 */
                countAllSections() {
                    const term = this.searchTerm;

                    if (term.length <= 1) {
                        this.resetResultCounts();

                        return;
                    }

                    Object.values(this.tabs).forEach((tab) => {
                        const request = this.buildSectionRequest(tab, term);

                        this.$axios.get(request.endpoint, { params: request.params })
                            .then((response) => {
                                /**
                                 * A slower reply for an earlier term would otherwise overwrite the
                                 * count for the term now in the box.
                                 */
                                if (this.searchTerm !== term) {
                                    return;
                                }

                                this.resultCounts[tab.key] = (response.data.data || []).length;
                            })
                            .catch(() => {
                                /**
                                 * A section that cannot be counted shows no badge rather than a
                                 * misleading zero.
                                 */
                                if (this.searchTerm === term) {
                                    this.resultCounts[tab.key] = null;
                                }
                            });
                    });
                },

                /**
                 * The endpoint and query one section would use for `term`, mirroring how
                 * `updateSearchParams` builds the active tab's request.
                 */
                buildSectionRequest(tab, term) {
                    if (
                        tab.key === 'settings'
                        || tab.key === 'configurations'
                    ) {
                        return {
                            endpoint: `${tab.endpoint}?query=${encodeURIComponent(term)}`,
                            params: null,
                        };
                    }

                    return {
                        endpoint: tab.endpoint,
                        params: {
                            search: tab.query_params
                                .map((param) => `${param.search}:${term};`)
                                .join(''),
                            searchFields: tab.query_params
                                .map((param) => `${param.searchFields};`)
                                .join(''),
                        },
                    };
                },

                resetResultCounts() {
                    Object.keys(this.resultCounts).forEach((key) => {
                        this.resultCounts[key] = null;
                    });
                },

                /**
                 * Open the search on Ctrl/⌘ K, and close it on Escape.
                 *
                 * The browser binds Ctrl/⌘ K to its own address bar, so the default is prevented —
                 * without that the page would never see the key. Escape is only handled while the
                 * modal is open, so it does not interfere with anything else on the page.
                 */
                handleShortcut(e) {
                    if (
                        (e.ctrlKey || e.metaKey)
                        && e.key?.toLowerCase() === 'k'
                    ) {
                        e.preventDefault();

                        this.openModal();

                        return;
                    }

                    if (
                        e.key === 'Escape'
                        && this.isModalOpen
                    ) {
                        this.closeModal();
                    }
                },

                /**
                 * Where a result links to, by the section it came from. The dropdown builds these
                 * hrefs inline per section; the modal renders one list, so the mapping lives here.
                 */
                recordUrl(record) {
                    const routes = {
                        leads: "{{ route('admin.leads.view', ':id') }}",
                        quotes: "{{ route('admin.quotes.edit', ':id') }}",
                        persons: "{{ route('admin.contacts.persons.view', ':id') }}",
                        products: "{{ route('admin.products.edit', ':id') }}",
                    };

                    /**
                     * Settings and configuration results already carry their own destination.
                     */
                    if (! routes[this.activeTab]) {
                        return record.url || record.route || '#';
                    }

                    return routes[this.activeTab].replace(':id', record.id);
                },

                openModal() {
                    this.isModalOpen = true;

                    /**
                     * Only one of the two is ever on screen. Closing the dropdown here means the
                     * shortcut can be pressed with the header search already open without leaving
                     * the same results showing twice.
                     */
                    this.isDropdownOpen = false;

                    /**
                     * Focus lands in the field so the shortcut leads straight into typing, which is
                     * the whole point of reaching for it.
                     */
                    this.$nextTick(() => this.$refs.modalSearchInput?.focus());
                },

                closeModal() {
                    this.isModalOpen = false;

                    this.isDropdownOpen = false;
                },

                handleFocusOut(e) {
                    /**
                     * The modal has its own dismissal (Escape, the close control, the backdrop), so
                     * a click inside it must not be treated as clicking away from the header field.
                     */
                    if (this.isModalOpen) {
                        return;
                    }

                    /**
                     * The component has two roots now — the header field and the modal — so `$el`
                     * no longer stands for the whole component and `$el.contains()` stopped
                     * matching clicks inside the dropdown, closing it on its own results. The
                     * header container is tested directly instead.
                     */
                    const header = this.$refs.headerSearch;

                    if (
                        header
                        && ! header.contains(e.target)
                    ) {
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
