@props([
    'allowEdit' => true,
])

<v-inline-email-edit
    {{ $attributes->except('value') }}
    :value={{ json_encode($attributes->get('value')) }}
    :allow-edit="{{ $allowEdit ? 'true' : 'false' }}"
>
    <div class="group w-full max-w-full hover:rounded-sm">
        <div class="rounded-xs flex h-[34px] items-center ltr:pl-2.5 ltr:text-left rtl:pr-2.5 rtl:text-right">
            <div class="shimmer h-5 w-48 rounded border border-transparent"></div>
        </div>
    </div>
</v-inline-email-edit>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-inline-email-edit-template"
    >
        <div class="group w-full max-w-full hover:rounded-sm">
            <!-- Non-editing view -->
            <div
                class="flex h-[34px] items-center rounded border border-transparent transition-all"
                :class="allowEdit ? 'hover:bg-gray-100 dark:hover:bg-gray-800' : ''"
            >
                <div 
                    class="group relative !w-full pl-2.5"
                    :style="{ 'text-align': position }"
                >
                    <span class="cursor-pointer truncate rounded">
                        @{{ valueLabel ? valueLabel : inputValue?.map(item => `${item.value}(${item.label})`).join(', ').length > 20 ? inputValue?.map(item => `${item.value}(${item.label})`).join(', ').substring(0, 20) + '...' : inputValue?.map(item => `${item.value}(${item.label})`).join(', ') }}
                    </span>

                    <!-- Tooltip -->
                    <div
                        class="absolute bottom-0 mb-5 hidden flex-col group-hover:flex"
                        v-if="inputValue?.map(item => `${item.value}(${item.label})`).join(', ').length > 20"
                    >
                        <span class="whitespace-no-wrap relative z-10 rounded-md bg-black px-4 py-2 text-xs leading-none text-white shadow-lg dark:bg-white dark:text-gray-900">
                            @{{ inputValue?.map(item => `${item.value}(${item.label})`).join(', \n') }}
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

            <Teleport to="body">
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
                                    @lang("admin::app.common.custom-attributes.update-emails-title")
                                </p>
                            </x-slot>

                            <!-- Modal Content -->
                            <x-slot:content>
                                <template v-for="(email, index) in emails">
                                    <div class="mb-2 flex items-center">
                                        <x-admin::form.control-group.control
                                            type="text"
                                            ::id="`${name}[${index}].value`"
                                            ::name="`${name}[${index}].value`"
                                            class="!rounded-r-none"
                                            ::rules="getValidation"
                                            v-model="email.value"
                                            :label="trans('admin::app.common.custom-attributes.email')"
                                        />

                                        <div class="relative">
                                            <x-admin::form.control-group.control
                                                type="select"
                                                ::id="`${name}[${index}].label`"
                                                ::name="`${name}[${index}].label`"
                                                class="!w-24 !rounded-l-none"
                                                ::value="email.label"
                                            >
                                                <option value="work">@lang('admin::app.common.custom-attributes.work')</option>
                                                <option value="home">@lang('admin::app.common.custom-attributes.home')</option>
                                            </x-admin::form.control-group.control>
                                        </div>

                                        <i
                                            v-if="emails.length > 1"
                                            class="icon-delete ml-1 cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-950"
                                            @click="remove(email)"
                                        ></i>
                                    </div>
                        
                                    <x-admin::form.control-group.error ::name="`${name}[${index}].value`"/>
                                </template>
                        
                                <button
                                    type="button"
                                    class="flex max-w-max items-center gap-2 text-brandColor"
                                    @click="add"
                                >
                                    <i class="icon-add text-md !text-brandColor"></i>

                                    @lang("admin::app.common.custom-attributes.add-more")
                                </button>
                            </x-slot>

                            <!-- Modal Footer -->
                            <x-slot:footer>
                                <!-- Save Button -->
                                <x-admin::button
                                    button-type="submit"
                                    class="primary-button justify-center"
                                    :title="trans('admin::app.common.custom-attributes.save')"
                                    ::loading="isProcessing"
                                    ::disabled="isProcessing"
                                />
                            </x-slot>
                        </x-admin::modal>
                    </form>
                </x-admin::form>
            </Teleport>
        </div>
    </script>

    <script type="module">
        app.component('v-inline-email-edit', {
            template: '#v-inline-email-edit-template',

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

                    emails: JSON.parse(JSON.stringify(this.value || [{'value': '', 'label': 'work'}])),

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
                        this.emails = newValue || [{'value': '', 'label': 'work'}];
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

                add() {
                    this.emails.push({
                        'value': '',
                        'label': 'work'
                    });
                },

                remove(contactEmail) {
                    this.emails = this.emails.filter(email => email !== contactEmail);
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
                            return 'This email is already used';
                        }

                        return true;
                    });
                },

                updateOrCreate(params) {
                    this.inputValue = params.contact_emails ?? params.emails;

                    if (this.url) {
                        this.isProcessing = true;

                        this.$axios.put(this.url, {
                                [this.name]: this.inputValue,
                            })
                            .then((response) => {
                                this.emails = response.data.data.emails || this.emails;

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            })
                            .catch((error) => {
                                this.inputValue = this.value;

                                this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                            })
                            .finally(() => {
                                this.isProcessing = false;
                            });
                    }

                    this.$emit('on-save', params);

                    this.$refs.emailModal.toggle();
                }
            },
        });
    </script>
@endPushOnce