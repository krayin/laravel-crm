@props([
    'endPoint'    => null,
    'params'      => [],
    'name'        => null,
    'placeholder' => null,
])

<v-lookup 
    endpoint="{{ $endPoint }}"
    :params="{{ json_encode($params) }}"
    name="{{ $name }}"
    placeholder="{{ $placeholder }}"
></v-lookup>

@pushOnce('scripts')
    <script 
        type="text/x-template"
        id="v-lookup-template"
    >
        <div
            class="relative"
            ref="lookup"
        >
            <!-- Hidden Input Box -->
            <input 
                type="hidden"
                :name="name"
                :value="selectedItem?.id"
            />
        
            <!-- Input Box (Button) -->
            <x-admin::form.control-group.control
                type="text"
                ::id="name"
                ::name="name"
                ::placeholder="selectedItem ? selectedItem?.name : placeholder"
                ::value="selectedItem ? selectedItem.name : ''"
                readonly
                @click="toggle"
                class="w-full pr-10 cursor-pointer"
            />

            <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
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
                class="absolute top-full mt-1 w-full border bg-white rounded-lg shadow-lg z-10 transition-transform transform origin-top p-2"
            >
                <!-- Search Bar -->
                <x-admin::form.control-group.control
                    type="text"
                    v-model="searchQuery"
                    class="!mb-2"
                    placeholder="Search..."
                    ref="searchInput"
                    @keyup="search"
                />
        
                <!-- Results List -->
                <ul class="max-h-40 overflow-y-auto divide-y divide-gray-100">
                    <li 
                        v-for="item in filteredResults" 
                        :key="item.id"
                        class="px-4 py-2 cursor-pointer hover:bg-blue-100 transition-colors"
                        @click="selectItem(item)"
                    >
                        @{{ item.name }}
                    </li>
                    <li v-if="filteredResults.length === 0" class="px-4 py-2 text-gray-500 text-center">
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
                endpoint: {
                    type: String,
                    required: true,
                },

                params: {
                    type: Object,
                    required: true,
                },

                name: {
                    type: String,
                    required: true,
                },

                placeholder: {
                    type: String,
                    required: true,
                },
            },

            emits: ['on-selected'],

            data() {
                return {
                    showPopup: false,

                    searchQuery: '',

                    selectedItem: null,

                    searchedResults: [],

                    isSearching: false,
                };
            },

            created() {
                window.addEventListener('click', this.handleFocusOut);
            },

            beforeDestroy() {
                window.removeEventListener('click', this.handleFocusOut);
            },

            computed: {
                /**
                 * Filter the searchedResults based on the search query.
                 * 
                 * @return {Array}
                 */
                filteredResults() {
                    return this.searchedResults.filter(item => 
                        item.name.toLowerCase().includes(this.searchQuery.toLowerCase())
                    );
                }
            },
            
            methods: {
                toggle() {
                    this.showPopup = !this.showPopup;

                    if (this.showPopup) {
                        this.$nextTick(() => this.$refs.searchInput.focus());
                    }
                },

                /**
                 * Select an item from the list.
                 * 
                 * @param {Object} item
                 * @return {void}
                 */
                selectItem(item) {
                    this.showPopup = false;

                    this.searchQuery = '';

                    this.selectedItem = item;

                    this.$emit('on-selected', item);
                },

                /**
                 * Initialize the items.
                 * 
                 * @return {void}
                 */
                search(event) {
                    const searchTerm = event.target.value;

                    if (searchTerm.length <= 2) {
                        this.searchedResults = [];

                        this.isSearching = false;

                        return;
                    }

                    this.isSearching = true;

                    this.$axios.get(this.endpoint, {
                            params: { 
                                ...this.params,
                                query: searchTerm
                            }
                        })
                        .then (response => {
                            this.searchedResults = response.data;

                            this.isSearching = false;
                        })
                        .catch (error => this.isSearching = false);
                },

                /**
                 * Handle the focus out event.
                 * 
                 * @param {Event} event
                 * @return {void}
                 */
                handleFocusOut(event) {
                    const lookup = this.$refs.lookup;

                    if (
                        lookup && 
                        !lookup.contains(event.target)
                    ) {
                        this.showPopup = false;
                    }
                },
            },
        });
    </script>
@endPushOnce