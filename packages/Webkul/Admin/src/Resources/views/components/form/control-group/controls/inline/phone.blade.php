<v-inline-phone-edit
    {{ $attributes->except('value') }}
    :value={{ json_encode($attributes->get('value')) }}
></v-inline-phone-edit>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-inline-phone-edit-template"
    >
        <div class="group w-full max-w-full hover:rounded-sm">
            <!-- Non-editing view -->
            <div
                class="flex items-center rounded-xs h-[38px] space-x-2"
                :class="allowEdit ? 'cursor-pointer hover:bg-gray-50' : ''"
                :style="textPositionStyle"
            >
                <span class="font-normal text-sm pl-2">@{{ inputValue.map(item => `${item.value}(${item.label})`).join(', ') }}</span>

                <template v-if="allowEdit">
                    <i
                        @click="toggle"
                        class="icon-edit hidden text-xl pr-2 group-hover:block"
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
                                            class="!rounded-l-none"
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
                    
                                <x-admin::form.control-group.error ::name="`${name}[${index}][value]`"/>
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
                value(newValue) {
                    if (JSON.stringify(newVal) !== JSON.stringify(oldVal)) {
                        this.contactNumbers = newVal || [{'value': '', 'label': 'work'}];
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

                    this.$emit('on-save', params);

                    this.$refs.phoneNumberModal.toggle();
                }
            },
        });
    </script>
@endPushOnce