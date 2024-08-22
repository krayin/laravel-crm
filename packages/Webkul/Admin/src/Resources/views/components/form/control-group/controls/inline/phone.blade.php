<v-inline-phone-edit
    {{ $attributes->except('value') }}
    :value={{ json_encode($attributes->get('value')) }}
>
    <div class="group w-full max-w-full hover:rounded-sm">
        <div class="rounded-xs flex h-[34px] items-center space-x-2 pl-2.5 text-left">
            <div class="shimmer h-5 w-48 rounded border border-transparent"></div>
        </div>
    </div>
</v-inline-phone-edit>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-inline-phone-edit-template"
    >
        <div class="group w-full max-w-full hover:rounded-sm">
            <!-- Non-editing view -->
            <div
                class="rounded-xs flex h-[34px] items-center space-x-2"
                :class="allowEdit ? 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800' : ''"
                :style="textPositionStyle"
            >
                <div class="group relative flex !w-full flex-col items-center">
                    <span class="w-40 truncate rounded border border-transparent pl-[2px]">@{{ inputValue.map(item => `${item.value}(${item.label})`).join(', ') }}</span>

                    <div
                        class="absolute bottom-0 mb-5 hidden flex-col items-center group-hover:flex"
                        v-if="inputValue.map(item => `${item.value}(${item.label})`).join(', ').length > 20"
                    >
                        <span class="whitespace-no-wrap relative z-10 max-w-60 rounded-md bg-black p-4 text-xs leading-none text-white shadow-lg">
                            @{{ inputValue.map(item => `${item.value}(${item.label})`).join(', \n') }}
                        </span>

                        <div class="-mt-2 h-3 w-3 rotate-45 bg-black"></div>
                    </div>
                </div>

                <template v-if="allowEdit">
                    <i
                        @click="toggle"
                        class="icon-edit hidden pr-2 text-xl group-hover:block"
                    ></i>
                </template>
            </div>

            <x-admin::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
                ref="modalForm"
            >
                <form @submit="handleSubmit($event, updateOrCreate)">
                    <!-- Editing view -->
                    <x-admin::modal ref="phoneNumberModal">
                        <!-- Modal Header -->
                        <x-slot:header>
                            <p class="text-lg font-bold text-gray-800 dark:text-white">
                                Update Contact Numbers
                            </p>
                        </x-slot>

                        <!-- Modal Content -->
                        <x-slot:content>
                            <template v-for="(contactNumber, index) in contactNumbers">
                                <div class="mb-2 flex items-center">
                                    <x-admin::form.control-group.control
                                        type="text"
                                        ::id="`${name}[${index}].value`"
                                        ::name="`${name}[${index}].value`"
                                        class="!rounded-r-none"
                                        ::rules="getValidation"
                                        v-model="contactNumber.value"
                                    />

                                    <div class="relative">
                                        <x-admin::form.control-group.control
                                            type="select"
                                            ::id="`${name}[${index}].label`"
                                            ::name="`${name}[${index}].label`"
                                            class="!rounded-l-none !w-24"
                                            ::value="contactNumber.label"
                                        >
                                            <option value="work">@lang('admin::app.common.custom-attributes.work')</option>
                                            <option value="home">@lang('admin::app.common.custom-attributes.home')</option>
                                        </x-admin::form.control-group.control>
                                    </div>

                                    <i
                                        v-if="contactNumbers.length > 1"
                                        class="icon-delete ml-1 cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-950"
                                        @click="remove(contactNumber)"
                                    ></i>
                                </div>
                    
                                <x-admin::form.control-group.error ::name="`${name}[${index}].value`"/>
                            </template>
                    
                            <span
                                class="cursor-pointer text-brandColor"
                                @click="add"
                            >
                                +@lang("admin::app.common.custom-attributes.add-more")
                            </span>
                        </x-slot>

                        <!-- Modal Footer -->
                        <x-slot:footer>
                            <!-- Save Button -->
                            <x-admin::button
                                button-type="submit"
                                class="primary-button justify-center"
                                :title="trans('Save')"
                                ::loading="isProcessing"
                                ::disabled="isProcessing"
                            />
                        </x-slot>
                    </x-admin::modal>
                </form>
            </x-admin::form>
        </div>
    </script>

    <script type="module">
        app.component('v-inline-phone-edit', {
            template: '#v-inline-phone-edit-template',

            emits: ['on-save'],

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
                }
            },

            data() {
                return {
                    inputValue: this.value,

                    isEditing: false,

                    contactNumbers: JSON.parse(JSON.stringify(this.value || [{'value': '', 'label': 'work'}])),

                    isProcessing: false,
                };
            },

            watch: {
                /**
                 * Watch the value prop.
                 * 
                 * @param {String} newValue 
                 */
                value(newValue, oldValue) {
                    if (JSON.stringify(newValue) !== JSON.stringify(oldValue)) {
                        this.contactNumbers = newValue || [{'value': '', 'label': 'work'}];
                    }
                },
            },

            created() {
                this.extendValidations();

                if (! this.contactNumbers || ! this.contactNumbers.length) {
                    this.contactNumbers = [{
                        'value': '',
                        'label': 'work'
                    }];
                }
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
                 * Get the validation rules.
                 * 
                 * @return {Object}
                 */
                getValidation() {
                    return {
                        numeric: true,
                        unique_contact_number: this.contactNumbers ?? [],
                        required: true,
                    };
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

                    this.$refs.phoneNumberModal.toggle();
                },

                add() {
                    this.contactNumbers.push({
                        'value': '',
                        'label': 'work'
                    });
                },

                remove(contactNumber) {
                    this.contactNumbers = this.contactNumbers.filter(number => number !== contactNumber);
                },

                extendValidations() {
                    defineRule('unique_contact_number', (value, contactNumbers) => {
                        if (
                            ! value
                            || ! value.length
                        ) {
                            return true;
                        }

                        const phoneOccurrences = contactNumbers.filter(contactNumber => contactNumber.value === value).length;

                        if (phoneOccurrences > 1) {
                            return 'This phone number is already used';
                        }

                        return true;
                    });
                },

                updateOrCreate(params) {
                    this.inputValue = params.contact_numbers;

                    if (this.url) {
                        this.$axios.put(this.url, {
                                [this.name]: this.inputValue,
                            })
                            .then((response) => {
                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            })
                            .catch((error) => {
                                this.inputValue = this.value;
                            });                        
                    }

                    this.$emit('on-save', params);

                    this.$refs.phoneNumberModal.toggle();
                }
            },
        });
    </script>
@endPushOnce