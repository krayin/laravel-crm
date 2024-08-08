@props([
    'attribute' => [],
])

<v-inline-look-edit
    {{ $attributes }}
    :attribute="{{ json_encode($attribute) }}"
>
    <div class="group w-full max-w-full hover:rounded-sm">
        <div class="flex items-center rounded-xs text-left pl-2.5 h-[34px] space-x-2">
            <div class="shimmer h-5 w-48"></div>
        </div>
    </div>
</v-inline-look-edit>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-inline-look-edit-template"
    >
        <div class="group w-full max-w-full hover:rounded-sm">
            <!-- Non-editing view -->
            <div
                v-if="! isEditing"
                class="flex items-center rounded-xs h-[34px] space-x-2"
                :class="allowEdit ? 'cursor-pointer hover:bg-gray-50' : ''"
                :style="textPositionStyle"
            >
                <x-admin::form.control-group.control
                    type="hidden"
                    ::id="name"
                    ::name="name"
                    v-model="inputValue"
                />

                <span class="font-normal text-sm pl-[2px]">@{{ inputValue }}</span>

                <template v-if="allowEdit">
                    <i
                        @click="toggle"
                        class="icon-edit hidden text-xl pr-2 group-hover:block"
                    ></i>
                </template>
            </div>
        
            <!-- Editing view -->
            <div
                class="relative flex flex-col w-full"
                v-else
            >
                <div class="relative flex flex-col w-full">
                    <x-admin::form.control-group.control
                        type="text"
                        ::name="name"
                        class="w-full pr-10 cursor-pointer text-gray-800"
                        ::placeholder="placeholder"
                        v-model="selectedItem.name"
                        @click="toggleEditor"   
                        readonly
                    />

                    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-14">
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
                            v-debounce="200"
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
                        
                    <!-- Action Buttons -->
                    <div class="absolute right-2 top-1/2 transform -translate-y-1/2 flex gap-[1px] bg-white">
                        <button
                            type="button"
                            class="flex items-center justify-center rounded-l-md p-1 bg-green-100 hover:bg-green-200"
                            @click="save"
                        >
                            <i class="icon-tick text-md font-bold cursor-pointer text-green-600" />
                        </button>
                    
                        <button
                            type="button"
                            class="flex items-center justify-center rounded-r-md p-1 ml-[1px] bg-red-100 hover:bg-red-200"
                            @click="cancel"
                        >
                            <i class="icon-cross-large text-md font-bold cursor-pointer text-red-600" />
                        </button>
                    </div>
                </div>

                <x-admin::form.control-group.error ::name="name"/>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-inline-look-edit', {
            template: '#v-inline-look-edit-template',

            emits: ['on-change', 'on-cancelled'],

            props: {
                name: {
                    type: String,
                    required: true,
                },

                value: {
                    required: true,
                },

                position: {
                    type: String,
                    default: 'right',
                },

                errors: {
                    type: Object,
                    default: {},
                },

                attribute: {
                    type: Object,
                    default: () => ({}),
                },

                allowEdit: {
                    type: Boolean,
                    default: true,
                },

                placeholder: {
                    type: String,
                    default: 'Search...',
                },

                url: {
                    type: String,
                    default: '',
                },
            },

            data() {
                return {
                    inputValue: this.value,

                    isEditing: false,

                    showPopup: false,

                    searchTerm: '',

                    selectedItem: {},

                    searchedResults: [],

                    isSearching: false,

                    cancelToken: null,
                };
            },

            watch: {
                /**
                 * Watch the value prop.
                 * 
                 * @param {String} newValue 
                 */
                value(newValue) {
                    this.inputValue = newValue;
                },
            },

            computed: {
                /**
                 * Get the input position style.
                 * 
                 * @return {String}
                 */
                inputPositionStyle() {
                    return this.position === 'left' ? 'text-align: left; padding-left: 9px' : 'text-align: right;';
                },

                /**
                 * Get the text position style.
                 * 
                 * @return {String}
                 */
                textPositionStyle() {
                    return this.position === 'left' ? 'justify-content: space-between' : 'justify-content: end';
                },

                src() {
                    return `{{ route('admin.settings.attributes.lookup') }}/${this.attribute.lookup_type}`;
                },

                /**
                 * Filter the searchedResults based on the search query.
                 * 
                 * @return {Array}
                 */
                filteredResults() {
                    return this.searchedResults.filter(item => 
                        item.name.toLowerCase().includes(this.searchTerm.toLowerCase())
                    );
                },
            },

            methods: {
                /**
                 * Toggle the input.
                 * 
                 * @return {void}
                 */
                toggle() {
                    this.isEditing = true;

                    this.searchTerm = '';

                    this.selectedItem.name = this.inputValue;
                },

                toggleEditor() {
                    this.showPopup = ! this.showPopup;

                    if (this.showPopup) {
                        this.$nextTick(() => this.$refs.searchInput.focus());
                    }
                },

                /**
                 * Save the input value.
                 * 
                 * @return {void}
                 */
                save() {
                    if (this.errors[this.name]) {
                        return;
                    }

                    this.isEditing = false;

                    if (this.selectedItem.id === undefined) {
                        return;
                    }

                    this.inputValue = this.selectedItem.name;

                    if (this.url) {
                        this.$axios.put(this.url, {
                                [this.name]: this.selectedItem.id,
                            })
                            .then((response) => {
                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            })
                            .catch((error) => {
                                this.inputValue = this.value;
                            });                        
                    }

                    this.$emit('on-change', {
                        name: this.name,
                        value: this.selectedItem.id,
                    });
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
                },

                /**
                 * Cancel the input value.
                 * 
                 * @return {void}
                 */
                cancel() {
                    if (this.selectItem) {
                        this.inputValue = this.selectedItem.name;
                    }

                    this.isEditing = false;

                    this.$emit('on-cancelled', this.inputValue);
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

                            this.isSearching = false;
                        })
                        .finally(() => this.isSearching = false);
                },
            },
        });
    </script>
@endPushOnce