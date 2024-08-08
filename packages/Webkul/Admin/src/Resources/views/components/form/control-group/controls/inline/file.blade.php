<v-inline-file-edit {{ $attributes }}>
    <div class="group w-full max-w-full hover:rounded-sm">
        <div class="flex items-center rounded-xs text-left pl-2.5 h-[34px] space-x-2">
            <div class="shimmer h-5 w-48"></div>
        </div>
    </div>
</v-inline-file-edit>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-inline-file-edit-template"
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

                <a 
                    :href="inputValue" 
                    target="_blank"
                >
                    <span class="icon-down-arrow font-normal text-2xl pl-[2px]"></span>
                </a>

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
                    <input
                        type="file"
                        :name="name"
                        :id="name"
                        :class="[errors.length ? 'border !border-red-600 hover:border-red-600' : '']"
                        class="w-full rounded-md border px-3 py-2.5 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:file:bg-gray-800 dark:file:dark:text-white dark:hover:border-gray-400 dark:focus:border-gray-400"
                        @change="handleChange"
                        ref="input"
                    />
                        
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
        app.component('v-inline-file-edit', {
            template: '#v-inline-file-edit-template',

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
            },

            data() {
                return {
                    inputValue: this.value,

                    isEditing: false,

                    file: null,
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

                    let formData = new FormData();

                    formData.append(this.name, this.file);

                    formData.append('_method', 'PUT');

                    if (this.url) {
                        this.$axios.post(this.url, formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        })
                        .then((response) => {
                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                        })
                        .catch((error) => {
                            console.error(error);
                            this.inputValue = this.value;
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

                handleChange(event) {
                    this.file = event.target.files[0];

                    this.inputValue = URL.createObjectURL(this.file);
                },
            },
        });
    </script>
@endPushOnce