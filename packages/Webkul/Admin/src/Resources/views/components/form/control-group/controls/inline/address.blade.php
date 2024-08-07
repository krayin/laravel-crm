<v-inline-address-edit
    {{ $attributes->except('value') }}
    :value='@json($attributes->get('value'))'
></v-inline-address-edit>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-inline-address-edit-template"
    >
        <div class="group w-full max-w-full hover:rounded-sm">
            <!-- Non-editing view -->
            <div
                class="flex items-center rounded-xs h-[38px] space-x-2"
                :class="allowEdit ? 'cursor-pointer hover:bg-gray-50' : ''"
                :style="textPositionStyle"
            >
                <span class="font-normal text-sm pl-2">
                    @{{ inputValue?.address }}<br>
                    @{{ `${inputValue?.postcode} ${inputValue?.city}` }}<br>
                    @{{ `${inputValue?.state}, ${inputValue?.country}` }}<br>
                </span>

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
                    <x-admin::modal ref="emailModal">
                        <!-- Modal Header -->
                        <x-slot:header>
                            <p class="text-lg font-bold text-gray-800 dark:text-white">
                                Update Address
                            </p>
                        </x-slot>

                        <!-- Modal Content -->
                        <x-slot:content>
                            <div class="flex gap-4">
                                <div class="w-full">
                                    <!-- Address (Textarea field) -->
                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.control
                                            type="textarea"
                                            ::name="`${name}.address`"
                                            rows="10"
                                            ::value="inputValue?.address"
                                        />

                                        <x-admin::form.control-group.error ::name="name" />
                                    </x-admin::form.control-group>
                                </div>

                                <div class="grid w-full">
                                    <!-- Country Field -->
                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.control
                                            type="select"
                                            ::name="`${name}.country`"
                                            v-model="inputValue.country"
                                        >
                                            <option value="">@lang('admin::app.common.custom-attributes.select-country')</option>
                                            
                                            @foreach (core()->countries() as $country)
                                                <option value="{{ $country->code }}">{{ $country->name }}</option>
                                            @endforeach
                                        </x-admin::form.control-group.control>
                    
                                        <x-admin::form.control-group.error name="country" />
                    
                                    </x-admin::form.control-group>
                    
                                    <!-- State Field -->
                                    <template v-if="haveStates()">
                                        <x-admin::form.control-group>
                                            <x-admin::form.control-group.control
                                                type="select"
                                                ::name="`${name}.state`"
                                                v-model="inputValue.state"
                                            >
                                                <option value="">@lang('admin::app.common.custom-attributes.select-state')</option>
                                                
                                                <option v-for='(state, index) in countryStates[inputValue?.country]' :value="state.code">
                                                    @{{ state.name }}
                                                </option>
                                            </x-admin::form.control-group.control>
                    
                                            <x-admin::form.control-group.error name="country" />
                                        </x-admin::form.control-group>
                                    </template>
                    
                                    <template v-else>
                                        <x-admin::form.control-group>
                                            <x-admin::form.control-group.control
                                                type="text"
                                                ::name="`${name}.state`"
                                                v-model="inputValue.state"
                                            />
                                            
                                            <x-admin::form.control-group.error name="state" />
                                        </x-admin::form.control-group>
                                    </template>
                    
                                    <!-- City Field -->
                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.control
                                            type="text"
                                            ::name="`${name}.city`"
                                            ::value="inputValue?.city"
                                        />
                    
                                        <x-admin::form.control-group.error name="city" />
                                    </x-admin::form.control-group>
                    
                                    <!-- Postcode Field -->
                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.control
                                            type="text"
                                            ::name="`${name}.postcode`"
                                            ::value="inputValue?.postcode"
                                            :placeholder="trans('admin::app.common.custom-attributes.postcode')"
                                        />
                    
                                        <x-admin::form.control-group.error name="postcode" />
                                    </x-admin::form.control-group>
                                </div>
                            </div>
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
        app.component('v-inline-address-edit', {
            template: '#v-inline-address-edit-template',

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

                    emails: JSON.parse(JSON.stringify(this.value || [{'value': '', 'label': 'work'}])),

                    isProcessing: false,

                    countryStates: @json(core()->groupedStatesByCountries()),
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
                        this.emails = newVal || [{'value': '', 'label': 'work'}];
                    }
                },
            },

            created() {
                this.extendValidations();

                if (! this.emails || ! this.emails.length) {
                    this.emails = [{
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
                        email: true,
                        unique_contact_email: this.emails ?? [],
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

                    this.$refs.emailModal.toggle();
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
                    this.emails.push({
                        'value': '',
                        'label': 'work'
                    });
                },

                remove(email) {
                    this.emails = this.emails.filter(email => email !== email);
                },

                extendValidations() {
                    defineRule('unique_contact_email', (value, emails) => {
                        if (
                            ! value
                            || ! value.length
                        ) {
                            return true;
                        }

                        const emailOccurrences = emails.filter(email => email.value === value).length;

                        if (emailOccurrences > 1) {
                            return 'This email email is already used';
                        }

                        return true;
                    });
                },

                updateOrCreate(params) {
                    this.inputValue = params[this.name];

                    this.$emit('on-change', {
                        name: this.name,
                        value: this.inputValue,
                    });

                    this.$refs.emailModal.toggle();
                },

                haveStates() {
                    /*
                    * The double negation operator is used to convert the value to a boolean.
                    * It ensures that the final result is a boolean value,
                    * true if the array has a length greater than 0, and otherwise false.
                    */
                    return !!this.countryStates[this.inputValue.country]?.length;
                },
            },
        });
    </script>
@endPushOnce