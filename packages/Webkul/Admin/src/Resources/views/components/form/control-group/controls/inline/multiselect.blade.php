@props([
    'allowEdit' => true,
    'data'      => [],
])

<v-inline-multi-select-edit
    {{ $attributes->except('data') }}
    :data="{{ json_encode($data) }}"
    :allow-edit="{{ $allowEdit ? 'true' : 'false' }}"
>
    <div class="group w-full max-w-full hover:rounded-sm">
        <div class="rounded-xs flex h-[34px] items-center pl-2.5 text-left">
            <div class="shimmer h-5 w-48 rounded border border-transparent"></div>
        </div>
    </div>
</v-inline-multi-select-edit>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-inline-multi-select-edit-template"
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
                        @{{ valueLabel ? valueLabel : selectedValue?.length > 20 ? selectedValue.substring(0, 20) + '...' : selectedValue }}
                    </span>
                    
                    <!-- Tooltip -->
                    <div
                        class="absolute bottom-0 mb-5 hidden flex-col group-hover:flex"
                        v-if="selectedValue?.length > 20"
                    >
                        <span class="whitespace-no-wrap relative z-10 rounded-md bg-black px-4 py-2 text-xs leading-none text-white shadow-lg dark:bg-white dark:text-gray-900">
                            @{{ selectedValue }}
                        </span>

                        <div class="-mt-2 ml-4 h-3 w-3 rotate-45 bg-black dark:bg-white"></div>
                    </div>
                </div>

                <template v-if="allowEdit">
                    <i
                        @click="toggle"
                        class="icon-edit phttp://192.168.15.43/test/-0.5 cursor-pointer rounded text-2xl opacity-0 hover:bg-gray-200 group-hover:opacity-100 dark:hover:bg-gray-950 ltr:mr-1 rtl:ml-1"
                    ></i>
                </template>
            </div>

            <x-admin::form.control-group.error ::name="name"/>
        </div>

        <!-- Editing view -->
        <div
            class="relative flex w-full flex-col"
            ref="dropdownContainer"
            v-if="isEditing"
        >
            <div class="flex min-h-[38px] w-full items-center rounded border border-gray-200 px-2.5 py-1.5 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400 ltr:pr-16 rtl:pl-16">
                <ul class="flex flex-wrap items-center gap-1">
                    <li
                        v-for="option in tempOptions"
                        :key="option.id"
                        class="flex items-center gap-1 rounded-md bg-slate-100 pl-2 dark:bg-gray-800 dark:text-white"
                    >
                        <input
                            type="hidden"
                            :name="name"
                            :value="option"
                        />
                
                        <div
                            class="relative h-[18px] pl-2.5"
                            :style="{ 'text-align': position }"
                        >
                            <!-- Wrap the text and tooltip in a group class for hover tracking -->
                            <div class="group">
                                <!-- Truncated text -->
                                <p class="max-w-[110px] cursor-pointer truncate">@{{ option.name }}</p>
                
                                <!-- Tooltip that shows on hover over the truncated text -->
                                <div
                                    class="absolute bottom-0 mb-5 hidden flex-col group-hover:flex"
                                    v-if="option.name?.length > 20"
                                >
                                    <span
                                        class="whitespace-no-wrap relative z-20 rounded-md bg-black px-4 py-2 text-xs leading-none text-white shadow-lg dark:bg-white dark:text-gray-900"
                                    >
                                        @{{ option.name }}
                                    </span>
                
                                    <div class="-mt-2 ml-4 h-3 w-3 rotate-45 bg-black dark:bg-white"></div>
                                </div>
                            </div>
                        </div>
                
                        <!-- Cross icon for removing option -->
                        <span
                            class="icon-cross-large cursor-pointer p-0.5 text-xl"
                            @click="removeOption(option)"
                        ></span>
                    </li>
                </ul>
            </div>

            <!-- Dropdown (position dynamic based on space) -->
            <div
                class="absolute z-10 w-full origin-top transform rounded-lg border bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800"
                :class="dropdownPosition === 'bottom' ? 'top-full mt-1' : 'bottom-full mb-1'"
                v-if="options.length > 0"
            >
                <!-- Results List -->
                <ul class="max-h-40 divide-gray-100 overflow-y-auto p-0.5">
                    <li 
                        v-for="option in options" 
                        :key="option.id"
                        class="cursor-pointer rounded px-4 py-2 text-gray-800 transition-colors hover:bg-blue-100 dark:text-white dark:hover:bg-gray-950 ltr:pr-16 rtl:pl-16"
                        @click="addOption(option)"
                    >
                        @{{ option.name }}
                    </li>
                </ul>
            </div>
                
            <!-- Action Buttons -->
            <div class="absolute top-1/2 flex -translate-y-1/2 transform gap-0.5 ltr:right-2 rtl:left-2">
                <button
                    type="button"
                    class="flex items-center justify-center bg-green-100 p-1 hover:bg-green-200 ltr:rounded-l-md rtl:rounded-r-md"
                    @click="save"
                >
                    <i class="icon-tick text-md cursor-pointer font-bold text-green-600 dark:!text-green-600" />
                </button>
            
                <button
                    type="button"
                    class="flex items-center justify-center bg-red-100 p-1 hover:bg-red-200 ltr:rounded-r-md rtl:rounded-l-md"
                    @click="cancel"
                >
                    <i class="icon-cross-large text-md cursor-pointer font-bold text-red-600 dark:!text-red-600" />
                </button>
            </div>
        </div>

    </script>

    <script type="module">
        app.component('v-inline-multi-select-edit', {
            template: '#v-inline-multi-select-edit-template',

            emits: ['options-updated'],

            props: {
                name: {
                    type: String,
                    required: true,
                },

                value: {
                    required: true,
                },

                rules: {
                    type: String,
                    default: '',
                },

                label: {
                    type: String,
                    default: '',
                },

                placeholder: {
                    type: String,
                    default: '',
                },

                position: {
                    type: String,
                    default: 'right',
                },

                allowEdit: {
                    type: Boolean,
                    default: true,
                },

                errors: {
                    type: Object,
                    default: {},
                },

                data: {
                    type: Array,
                    required: true,
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
                    inputValue: this.value,

                    isEditing: false,

                    options: this.data ?? [],

                    tempOptions: [],

                    isRTL: document.documentElement.dir === 'rtl',

                    isDropdownOpen: false,

                    dropdownPosition: "bottom",
                };
            },

            mounted() {
                this.tempOptions = this.options.filter((data) => this.value.includes(data.id));

                this.options = this.options.filter((data) => !this.value.includes(data.id));

                window.addEventListener("resize", this.setDropdownPosition);
            },

            computed: {
                /**
                 * Get the selected value.
                 * 
                 * @return {Object}
                 */
                selectedValue() {                    
                    if (this.tempOptions.length === 0) {
                        return null;
                    }

                    return this.tempOptions.map((data) => data.name).join(', ');
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

                    this.isDropdownOpen = ! this.isDropdownOpen;

                    if (this.isDropdownOpen) {
                        this.setDropdownPosition();
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

                    if (this.url) {
                        this.$axios.put(this.url, {
                                [this.name]: this.tempOptions.map((data) => data.id),
                            })
                            .then((response) => {
                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            })
                            .catch((error) => {
                                this.inputValue = this.value;

                                this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                            });                        
                    }

                    this.$emit('options-updated', {
                        name: this.name,
                        value: this.tempOptions.map((data) => data.id),
                    });
                },

                /**
                 * Cancel the input value.
                 * 
                 * @return {void}
                 */
                cancel() {
                    this.isEditing = false;

                    this.$emit('options-updated', {
                        name: this.name,
                        value: this.tempOptions.map((data) => data.id),
                    });
                },

                addOption(option) {
                    if (!this.tempOptions.some((data) => data.id === option.id)) {
                        this.tempOptions.push(option);

                        this.options = this.options.filter((data) => data.id !== option.id);

                        this.input = '';
                    }
                },

                removeOption(option) {
                    if (!this.options.some((data) => data.id === option.id)) {
                        this.options.push(option);

                        this.tempOptions = this.tempOptions.filter((data) => data.id !== option.id);
                    }
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