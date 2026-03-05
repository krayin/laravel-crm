@props([
    'allowEdit' => true,
])

<v-inline-text-edit
    {{ $attributes }}
    :allow-edit="{{ $allowEdit ? 'true' : 'false' }}"
>
    <div class="group w-full max-w-full hover:rounded-sm">
        <div class="rounded-xs flex h-[34px] items-center pl-2.5 text-left">
            <div class="shimmer h-5 w-48 rounded border border-transparent"></div>
        </div>
    </div>
</v-inline-text-edit>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-inline-text-edit-template"
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
                        <template v-if="isDirty">
                            @{{
                                inputValue.length > 20
                                    ? inputValue.substring(0, 20) + '...'
                                    : inputValue
                            }}
                        </template>

                        <template v-else>
                            @{{
                                (valueLabel || inputValue || '').length > 20
                                    ? (valueLabel || inputValue).substring(0, 20) + '...'
                                    : (valueLabel || inputValue)
                            }}
                        </template>
                    </span>

                    <!-- Tooltip -->
                    <div
                        class="absolute bottom-0 mb-5 hidden flex-col group-hover:flex"
                        v-if="inputValue?.length > 20"
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
                class="relative w-full"
                v-else
            >
                <x-admin::form.control-group.control
                    type="text"
                    ::id="name"
                    ::name="name"
                    class="!h-[34px] !py-0 ltr:pr-16 rtl:pl-16"
                    ::rules="rules"
                    ::label="label"
                    ::placeholder="placeholder"
                    v-model="inputValue"
                    ref="input"
                />

                <!-- Action Buttons -->
                <div class="absolute top-[6px] flex gap-0.5 ltr:right-2 rtl:left-2">
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

                <x-admin::form.control-group.error ::name="name"/>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-inline-text-edit', {
            template: '#v-inline-text-edit-template',

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

                url: {
                    type: String,
                    default: '',
                },

                params: {
                    type: Object,
                    default: () => ({}),
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

                    isDirty: false,

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

            methods: {
                /**
                 * Toggle the input.
                 *
                 * @return {void}
                 */
                toggle() {
                    this.isEditing = true;

                    this.$nextTick(() => this.$refs.input.focus());
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
                        let formData = new FormData();

                        formData.append(this.name, this.inputValue);

                        formData.append('_method', 'PUT');

                        this.isDirty = true;

                        this.$axios.post(this.url, {
                            ...this.params,
                            ...Object.fromEntries(formData),
                        })
                            .then((response) => {
                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            })
                            .catch((error) => {
                                this.isDirty = false;

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
