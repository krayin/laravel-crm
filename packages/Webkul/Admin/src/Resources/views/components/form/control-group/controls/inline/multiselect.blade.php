<v-inline-multi-select-edit
    {{ $attributes->except('data') }}
    :data="{{ json_encode($data) }}"
></v-inline-multi-select-edit>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-inline-multi-select-edit-template"
    >
        <div class="group w-full max-w-full hover:rounded-sm">
            <!-- Non-editing view -->
            <div
                v-if="! isEditing"
                class="flex items-center rounded-xs h-[38px] space-x-2"
                :class="allowEdit ? 'cursor-pointer hover:bg-gray-50' : ''"
                :style="textPositionStyle"
            >
                <x-admin::form.control-group.control
                    type="hidden"
                    ::id="name"
                    ::name="name"
                    v-model="inputValue"
                />

                <span class="font-normal text-sm pl-[2px]">@{{ selectedValue }}</span>

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
                    <div class="flex min-h-[38px] w-full items-center rounded border border-gray-200 px-2.5 py-1.5 text-sm font-normal text-gray-800 transition-all hover:border-gray-400">
                        <ul class="flex flex-wrap items-center gap-1">
                            <li
                                class="flex items-center gap-1 rounded-md bg-slate-100 pl-2"
                                v-for="option in tempOptions"
                            >
                                <input type="hidden" :name="name" :value="option"/>
            
                                @{{ option.name }}
            
                                <span
                                    class="icon-cross-large cursor-pointer p-0.5 text-xl"
                                    @click="removeOption(option)"
                                ></span>
                            </li>
                        </ul>
                    </div>

                    <div
                        class="absolute top-full z-10 mt-1 w-full origin-top transform rounded-lg border bg-white p-2 shadow-lg transition-transform"
                        v-if="options.length > 0"
                    >
                        <!-- Results List -->
                        <ul class="max-h-40 divide-y divide-gray-100 overflow-y-auto">
                            <li 
                                v-for="option in options" 
                                :key="option.id"
                                class="cursor-pointer px-4 py-2 text-gray-800 transition-colors hover:bg-blue-100"
                                @click="addOption(option)"
                            >
                                @{{ option.name }}
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
            },

            data() {
                return {
                    inputValue: this.value,

                    isEditing: false,

                    options: this.data ?? [],

                    tempOptions: [],
                };
            },

            mounted() {
                this.tempOptions = this.options.filter((data) => this.value.includes(data.id));

                this.options = this.options.filter((data) => !this.value.includes(data.id));
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
            },
        });
    </script>
@endPushOnce