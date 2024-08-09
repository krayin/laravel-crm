<v-inline-text-edit {{ $attributes }}></v-inline-text-edit>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-inline-text-edit-template"
    >
        <div class="group w-full max-w-full hover:rounded-sm">
            <!-- Non-editing view -->
            <div
                v-if="! isEditing"
                class="rounded-xs flex h-[38px] items-center space-x-2"
                :class="allowEdit ? 'cursor-pointer hover:bg-gray-50' : ''"
                :style="textPositionStyle"
            >
                <x-admin::form.control-group.control
                    type="hidden"
                    ::id="name"
                    ::name="name"
                    v-model="inputValue"
                />

                <span class="pl-[2px] text-sm font-normal">@{{ inputValue }}</span>

                <template v-if="allowEdit">
                    <i
                        @click="toggle"
                        class="icon-edit hidden pr-2 text-xl group-hover:block"
                    ></i>
                </template>
            </div>
        
            <!-- Editing view -->
            <div
                class="relative flex w-full flex-col"
                v-else
            >
                <div class="relative flex w-full flex-col">
                    <x-admin::form.control-group.control
                        type="text"
                        ::id="name"
                        ::name="name"
                        class="text-normal py-1 pr-16"
                        ::rules="rules"
                        ::label="label"
                        ::placeholder="placeholder"
                        ::style="inputPositionStyle"
                        v-model="inputValue"
                        ref="input"
                    />
                        
                    <!-- Action Buttons -->
                    <div class="absolute right-2 top-1/2 flex -translate-y-1/2 transform gap-[1px] bg-white">
                        <button
                            type="button"
                            class="flex items-center justify-center rounded-l-md bg-green-100 p-1 hover:bg-green-200"
                            @click="save"
                        >
                            <i class="icon-tick text-md cursor-pointer font-bold text-green-600" />
                        </button>
                    
                        <button
                            type="button"
                            class="ml-[1px] flex items-center justify-center rounded-r-md bg-red-100 p-1 hover:bg-red-200"
                            @click="cancel"
                        >
                            <i class="icon-cross-large text-md cursor-pointer font-bold text-red-600" />
                        </button>
                    </div>
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
            },

            data() {
                return {
                    inputValue: this.value,

                    isEditing: false,
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

                        this.$axios.post(this.url, formData)
                            .then((response) => {
                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            })
                            .catch((error) => {
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
            },
        });
    </script>
@endPushOnce