<v-lookup {{ $attributes }}></v-lookup>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-lookup-template"
    >
        <div
            class="relative"
            ref="lookup"
        >
            <!-- Input Box (Button) -->
            <div
                class="relative inline-block w-full"
                @click="toggle"
            >
                <!-- Input Container -->
                <div class="relative flex cursor-pointer items-center justify-between rounded border border-gray-300 p-2 hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:text-gray-300">
                    <!-- Selected Item or Placeholder Text -->
                    <span
                        class="overflow-hidden text-ellipsis"
                        :title="selectedItem?.name"
                    >
                        @{{ selectedItem?.name !== "" ? selectedItem?.name : "@lang('admin::app.components.lookup.click-to-add')" }}
                    </span>

                    <!-- Icons Container -->
                    <div class="flex items-center gap-2">
                        <!-- Close Icon -->
                        <i
                            v-if="(selectedItem?.name) && ! isSearching"
                            class="icon-cross-large cursor-pointer text-xl text-gray-600"
                            @click="remove"
                        ></i>

                        <!-- Arrow Icon -->
                        <i
                            class="text-2xl text-gray-600"
                            :class="showPopup ? 'icon-up-arrow' : 'icon-down-arrow'"
                        ></i>
                    </div>
                </div>
            </div>

            <!-- Hidden Input Box -->
            <x-admin::form.control-group.control
                type="hidden"
                ::name="name"
                ::rules="rules"
                ::label="label"
                v-model="selectedItem.id"
            />

            <!-- Popup Box -->
            <div
                v-if="showPopup"
                class="absolute top-full z-10 mt-1 flex w-full origin-top transform flex-col gap-2 rounded-lg border border-gray-300 bg-white p-2 shadow-lg transition-transform dark:border-gray-900 dark:bg-gray-800"
            >
                <!-- Search Bar -->
                <div class="relative flex items-center">
                    <input
                        type="text"
                        v-model.lazy="searchTerm"
                        v-debounce="500"
                        class="w-full rounded border border-gray-300 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                        placeholder="@lang('admin::app.components.lookup.search')"
                        ref="searchInput"
                        @keyup="search"
                    />

                    <!-- Search Icon (absolute positioned) -->
                    <span class="absolute flex items-center ltr:right-2 rtl:left-2">
                        <!-- Loader (optional, based on condition) -->
                        <div
                            class="relative"
                            v-if="isSearching"
                        >
                            <x-admin::spinner />
                        </div>
                    </span>
                </div>

                <!-- Results List -->
                <ul class="max-h-40 divide-y divide-gray-100 overflow-y-auto">
                    <li
                        v-for="item in filteredResults"
                        :key="item.id"
                        class="cursor-pointer px-4 py-2 text-gray-800 transition-colors hover:bg-blue-100 dark:text-white dark:hover:bg-gray-900"
                        @click="selectItem(item)"
                    >
                        @{{ item.name }}
                    </li>

                    <template v-if="filteredResults.length === 0">
                        <li class="px-4 py-2 text-gray-500">
                            @lang('admin::app.components.lookup.no-results')
                        </li>
                    </template>

                    <li
                        v-if="canAddNew"
                        @click="selectItem({ id: '', name: searchTerm })"
                        class="cursor-pointer border-t border-gray-800 px-4 py-4 text-brandColor hover:bg-brandColor hover:text-white dark:border-gray-300 dark:text-gray-400 dark:hover:bg-gray-900 dark:hover:text-white"
                    >
                        <i class="icon-add text-md ltr:mr-2 rtl:ml-2"></i>

                        @lang('admin::app.components.lookup.add-as-new')
                    </li>
                </ul>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-lookup', {
            template: '#v-lookup-template',

            props: {
                src: {
                    type: String,
                    required: true,
                },

                params: {
                    type: Object,
                    default: () => ({}),
                },

                name: {
                    type: String,
                    required: true,
                },

                placeholder: {
                    type: String,
                    required: true,
                },

                value: {
                    type: Object,
                    default: () => ({}),
                },

                rules: {
                    type: String,
                    default: '',
                },

                label: {
                    type: String,
                    default: '',
                },

                canAddNew: {
                    type: Boolean,
                    default: false,
                },

                preload: {
                    type: Boolean,
                    default: false,
                }
            },

            emits: ['on-selected'],

            data() {
                return {
                    showPopup: false,

                    searchTerm: '',

                    selectedItem: {},

                    searchedResults: [],

                    isSearching: false,

                    cancelToken: null,
                };
            },

            mounted() {
                if (this.value) {
                    this.selectedItem = this.value;
                }
            },

            created() {
                window.addEventListener('click', this.handleFocusOut);
            },

            beforeDestroy() {
                window.removeEventListener('click', this.handleFocusOut);
            },

            watch: {
                searchTerm(newVal, oldVal) {
                    this.search();
                },
            },

            computed: {
                /**
                 * Filter the searchedResults based on the search query.
                 *
                 * @return {Array}
                 */
                filteredResults() {
                    return this.searchedResults.filter(item =>
                        item.name.toLowerCase().includes(this.searchTerm.toLowerCase())
                    );
                }
            },

            methods: {
                /**
                 * Toggle the popup.
                 *
                 * @return {void}
                 */
                toggle() {
                    this.showPopup = ! this.showPopup;

                    if (this.showPopup) {
                        if (! this.searchTerm.trim()) {
                            this.fetchResults('', 5);
                        }

                        this.$nextTick(() => this.$refs.searchInput.focus());
                    }
                },

                /**
                 * Select an item from the list.
                 *
                 * @param {Object} item
                 *
                 * @return {void}
                 */
                selectItem(item) {
                    this.showPopup = false;

                    this.searchTerm = '';

                    this.selectedItem = item;

                    this.$emit('on-selected', item);
                },

                /**
                 * Triggered on keyup — loads defaults when empty, searches when typed.
                 *
                 * @return {void}
                 */
                search() {
                    if (! this.showPopup) {
                        return;
                    }

                    const query = this.searchTerm.trim();

                    if (! query) {
                        this.fetchResults('', 5);

                        return;
                    }

                    this.fetchResults(query);
                },

                /**
                 * Fetch results from the server.
                 *
                 * @param {String} query
                 * @param {Number|null} limit
                 *
                 * @return {void}
                 */
                fetchResults(query = '', limit = null) {
                    this.isSearching = true;

                    if (this.cancelToken) {
                        this.cancelToken.cancel();
                    }

                    this.cancelToken = this.$axios.CancelToken.source();

                    const params = { ...this.params, query };

                    if (limit) {
                        params.limit = limit;
                    }

                    this.$axios.get(this.src, {
                            params,
                            cancelToken: this.cancelToken.token,
                        })
                        .then(response => {
                            const data = response.data?.data ?? response.data;
                            const results = Array.isArray(data) ? data : [];
                            this.searchedResults = limit ? results.slice(0, limit) : results;
                        })
                        .catch(error => {
                            if (! this.$axios.isCancel(error)) {
                                console.error("Search request failed:", error);
                            }

                            this.searchedResults = [];
                        })
                        .finally(() => this.isSearching = false);
                },

                /**
                 * Handle the focus out event.
                 *
                 * @param {Event} event
                 *
                 * @return {void}
                 */
                handleFocusOut(event) {
                    const lookup = this.$refs.lookup;

                    if (
                        lookup &&
                        ! lookup.contains(event.target)
                    ) {
                        this.showPopup = false;
                    }
                },

                /**
                 * Remove the selected item.
                 *
                 * @return {void}
                 */
                remove() {
                    this.selectedItem = {
                        id: '',
                        name: '',
                    };

                    this.$emit('on-selected', {});
                }
            },
        });
    </script>
@endPushOnce
