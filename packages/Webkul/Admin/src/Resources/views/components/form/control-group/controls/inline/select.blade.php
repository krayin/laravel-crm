@props([
    'allowEdit' => true,
    'options'   => [],
])

<v-inline-select-edit
    {{ $attributes->except('options') }}
    :options="{{ json_encode($options) }}"
    :allow-edit="{{ $allowEdit ? 'true' : 'false' }}"
>
    <div class="group w-full max-w-full hover:rounded-sm">
        <div class="rounded-xs flex h-[34px] items-center pl-2.5 text-left">
            <div class="shimmer h-5 w-48 rounded border border-transparent"></div>
        </div>
    </div>
</v-inline-select-edit>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-inline-select-edit-template"
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
                    class="group relative !w-full pl-2.5"
                    :style="{ 'text-align': position }"
                >
                    <span class="cursor-pointer truncate rounded">
                        @{{ valueLabel ? valueLabel : selectedValue?.name.length > 20 ? selectedValue?.name.substring(0, 20) + '...' : selectedValue?.name }}
                    </span>
                    
                    <!-- Tooltip -->
                    <div
                        class="absolute bottom-0 mb-5 hidden flex-col group-hover:flex"
                        v-if="selectedValue?.name.length > 20"
                    >
                        <span class="whitespace-no-wrap relative z-10 rounded-md bg-black px-4 py-2 text-xs leading-none text-white shadow-lg dark:bg-white dark:text-gray-900">
                            @{{ selectedValue?.name }}
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
                class="relative w-full"
                v-else
            >
                <x-admin::form.control-group.control
                    type="select"
                    ::id="name"
                    ::name="name"
                    ::rules="rules"
                    ::label="label"
                    class="!h-[34px] !py-0 ltr:pr-20 rtl:pl-20"
                    ::placeholder="placeholder"
                    v-model="inputValue"
                >
                    <option
                        v-for="(option, index) in options"
                        :key="option.id"
                        :value="option.id"
                    >
                        @{{ option.name }}
                    </option>
                </x-admin::form.control-group.control>
                    
                <!-- Action Buttons -->
                <div class="absolute top-1/2 flex -translate-y-1/2 transform items-center gap-0.5 ltr:right-2 rtl:left-2">
                    <i class="icon-down-arrow text-2xl" />

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

            <x-admin::form.control-group.error ::name="name"/>
        </div>
    </script>

    <script type="module">
        app.component('v-inline-select-edit', {
            template: '#v-inline-select-edit-template',

            emits: ['on-change', 'on-cancelled'],

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

                options: {
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
            },

            computed: {
                /**
                 * Get the selected value.
                 * 
                 * @return {Object}
                 */
                selectedValue() {
                    return this.options.find((option, index) => option.id == this.inputValue);
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
                                [this.name]: this.inputValue,
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
                        value: this.inputValue,
                    });
                },

                /**
                 * Cancel the input value.
                 * 
                 * @return {void}
                 */
                cancel() {
                    this.inputValue = this.value;

                    this.isEditing = false;

                    this.$emit('on-cancelled', {
                        name: this.name,
                        value: this.inputValue,
                    });
                },
            },
        });
    </script>
@endPushOnce