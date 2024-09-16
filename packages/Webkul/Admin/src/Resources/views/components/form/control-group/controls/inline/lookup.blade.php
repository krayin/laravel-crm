@props([
    'allowEdit' => true,
    'attribute' => [],
])

<v-inline-look-edit
    {{ $attributes }}
    :attribute="{{ json_encode($attribute) }}"
    :allow-edit="{{ $allowEdit ? 'true' : 'false' }}"
>
    <div class="group w-full max-w-full hover:rounded-sm">
        <div class="rounded-xs flex h-[34px] items-center pl-2.5 text-left">
            <div class="shimmer h-5 w-48 rounded border border-transparent"></div>
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
                class="flex h-[34px] items-center rounded border border-transparent transition-all"
                :class="allowEdit ? 'hover:bg-gray-100 dark:hover:bg-gray-800' : ''"
            >
                <x-admin::form.control-group.control
                    type="hidden"
                    ::id="name"
                    ::name="name"
                    v-model="inputValue"
                />

                <div
                    class="group relative h-[18px] !w-full pl-2.5"
                    :style="{ 'text-align': position }"
                >
                    <span class="cursor-pointer truncate rounded">
                        @{{ valueLabel ? valueLabel : inputValue.length > 20 ? inputValue.substring(0, 20) + '...' : inputValue }}
                    </span>

                    <!-- Tooltip -->
                    <div
                        class="absolute bottom-0 mb-5 hidden flex-col group-hover:flex"
                        v-if="inputValue.length > 20"
                    >
                        <span class="whitespace-no-wrap relative z-10 rounded-md bg-black px-4 py-2 text-xs leading-none text-white shadow-lg dark:bg-white dark:text-gray-900">
                            @{{ inputValue }}
                        </span>

                        <div class="-mt-2 ml-4 h-3 w-3 rotate-45 bg-black dark:bg-white"></div>
                    </div>
                </div>

                <template v-if="allowEdit">
                    <i
                        @click="toggle"
                        class="icon-edit cursor-pointer rounded p-0.5 text-2xl opacity-0 hover:bg-gray-200 group-hover:opacity-100 dark:hover:bg-gray-950 ltr:mr-1 rtl:ml-1"
                    ></i>
                </template>
            </div>
        
            <!-- Editing view -->
            <div
                class="relative flex w-full flex-col"
                ref="dropdownContainer"
                v-else
            >
                <x-admin::form.control-group.control
                    type="text"
                    ::name="name"
                    class="!h-[34px] w-full cursor-pointer !py-0 text-gray-800 dark:text-white ltr:pr-20 rtl:pl-20"
                    ::placeholder="placeholder"
                    v-model="selectedItem.name"
                    @click="toggleEditor"   
                    readonly
                />

                <span class="pointer-events-none absolute inset-y-0 flex items-center ltr:right-0 ltr:pr-14 rtl:left-0 rtl:pl-14">
                    <div class="flex items-center justify-center space-x-1">
                        <div
                            class="relative"
                            v-if="isSearching"
                        >
                            <x-admin::spinner />
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
                    class="absolute z-10 mt-1 w-full origin-top transform rounded-lg border border-gray-200 bg-white p-2 shadow-lg transition-transform dark:border-gray-800 dark:bg-gray-800"
                    :class="dropdownPosition === 'bottom' ? 'top-full mt-1' : 'bottom-full mb-1'"
                >
                    <!-- Search Bar -->
                    <input
                        type="text"
                        v-model.lazy="searchTerm"
                        v-debounce="200"
                        class="!mb-2 w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                        placeholder="@lang('admin::app.components.lookup.search')"
                        ref="searchInput"
                    />
            
                    <!-- Results List -->
                    <ul class="max-h-40 divide-y divide-gray-100 overflow-y-auto">
                        <li 
                            v-for="item in filteredResults" 
                            :key="item.id"
                            class="cursor-pointer px-4 py-2 text-gray-800 transition-colors hover:bg-blue-100 dark:text-white dark:hover:bg-gray-950"
                            @click="selectItem(item)"
                        >
                            @{{ item.name }}
                        </li>
    
                        <li v-if="filteredResults.length === 0" class="px-4 py-2 text-center text-gray-500 dark:text-gray-300">
                            @lang('admin::app.components.lookup.no-results')
                        </li>
                    </ul>
                </div>
                    
                <!-- Action Buttons -->
                <div class="absolute top-1/2 flex -translate-y-1/2 transform gap-0.5 bg-white dark:bg-gray-900 ltr:right-2 rtl:left-2">
                    <button
                        type="button"
                        class="flex items-center justify-center bg-green-100 p-1 hover:bg-green-200 ltr:rounded-l-md rtl:rounded-r-md"
                        @click="save"
                    >
                        <i class="icon-tick text-md cursor-pointer font-bold text-green-600 dark:!text-green-600" />
                    </button>
                
                    <button
                        type="button"
                        class="item-center flex justify-center bg-red-100 p-1 hover:bg-red-200 ltr:rounded-r-md rtl:rounded-l-md"
                        @click="cancel"
                    >
                        <i class="icon-cross-large text-md cursor-pointer font-bold text-red-600 dark:!text-red-600" />
                    </button>
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
                
                valueLabel: {
                    type: String,
                    default: '',
                },
            },

            data() {
                return {
                    inputValue: this.value ?? '',

                    isEditing: false,

                    showPopup: false,

                    searchTerm: '',

                    selectedItem: {},

                    searchedResults: [],

                    isSearching: false,

                    cancelToken: null,

                    isDropdownOpen: false,

                    dropdownPosition: "bottom",

                    isRTL: document.documentElement.dir === 'rtl',
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

                searchTerm(newVal, oldVal) {
                    this.search();
                },
            },

            mounted() {
                window.addEventListener("resize", this.setDropdownPosition);
            },

            computed: {
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

                    this.isDropdownOpen = ! this.isDropdownOpen;

                    if (this.isDropdownOpen) {
                        this.setDropdownPosition();
                    }
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

                                this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
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

                setDropdownPosition() {
                    this.$nextTick(() => {
                        const dropdownContainer = this.$refs.dropdownContainer;

                        if (! dropdownContainer) {
                            return;     
                        }

                        const dropdownRect = dropdownContainer.getBoundingClientRect();
                        const viewportHeight = window.innerHeight;

                        if (dropdownRect.bottom + 250 > viewportHeight) {
                            this.dropdownPosition = "top";
                        } else {
                            this.dropdownPosition = "bottom";
                        }
                    });
                },
            },
        });
    </script>
@endPushOnce