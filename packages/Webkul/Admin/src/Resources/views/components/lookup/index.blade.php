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
            <x-admin::form.control-group.control
                type="text"
                id="name"
                ::name="name"
                class="w-full cursor-pointer pr-10 text-gray-800"
                ::placeholder="selectedItem.name ?? placeholder"
                v-model="selectedItem.name"
                @click="toggle"
                readonly
            />

            <!-- Hidden Input Box -->
            <input 
                type="hidden"
                :name="name"
                :value="selectedItem?.id"
            />

            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                <div class="flex items-center justify-center space-x-1">
                    <div
                        class="relative"
                        v-if="isSearching"
                    >
                        <svg
                            class="h-5 w-5 animate-spin"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            aria-hidden="true"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            ></circle>
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            ></path>
                        </svg>
                    </div>
                    
                    <i 
                        class="text-2xl"
                        :class="showPopup ? 'icon-up-arrow': 'icon-down-arrow'"
                    ></i>
                </div>
            </span>

            <!-- Popup Box -->
            <div 
                v-if="showPopup" 
                class="absolute top-full z-10 mt-1 w-full origin-top transform rounded-lg border bg-white p-2 shadow-lg transition-transform"
            >
                <!-- Search Bar -->
                <input
                    type="text"
                    v-model.lazy="searchTerm"
                    v-debounce="500"
                    class="!mb-2 w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                    placeholder="Search..."
                    ref="searchInput"
                    @keyup="search"
                />
        
                <!-- Results List -->
                <ul class="max-h-40 divide-y divide-gray-100 overflow-y-auto">
                    <li 
                        v-for="item in filteredResults" 
                        :key="item.id"
                        class="cursor-pointer px-4 py-2 text-gray-800 transition-colors hover:bg-blue-100"
                        @click="selectItem(item)"
                    >
                        @{{ item.name }}
                    </li>

                    <li v-if="filteredResults.length === 0" class="px-4 py-2 text-center text-gray-500">
                        @lang('No results found')
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
                }
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
                 * Initialize the items.
                 * 
                 * @return {void}
                 */
                search() {
                    if (this.searchTerm.length <= 2) {
                        this.searchedResults = [];

                        this.isSearching = false;

                        return;
                    }

                    this.isSearching = true;

                    if (this.cancelToken) {
                        this.cancelToken.cancel();
                    }

                    this.cancelToken = this.$axios.CancelToken.source();

                    this.$axios.get(this.src, {
                            params: { 
                                ...this.params,
                                query: this.searchTerm
                            },
                            cancelToken: this.cancelToken.token, 
                        })
                        .then(response => {
                            this.searchedResults = response.data;
                        })
                        .catch(error => {
                            if (! this.$axios.isCancel(error)) {
                                console.error("Search request failed:", error);
                            }
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
            },
        });
    </script>
@endPushOnce