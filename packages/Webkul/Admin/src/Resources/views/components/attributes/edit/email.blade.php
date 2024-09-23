@if (isset($attribute))
    <v-email-component
        :attribute="{{ json_encode($attribute) }}"
        :validations="'{{ $validations }}'"
        :value="{{ json_encode(old($attribute->code) ?? $value) }}"
    >
        <div class="mb-2 flex items-center">
            <input
                type="text"
                class="w-full rounded rounded-r-none border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
            >

            <div class="relative">
                <select class="custom-select w-full rounded rounded-l-none border bg-white px-2.5 py-2 text-sm font-normal text-gray-800 hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 ltr:mr-6 ltr:pr-8 rtl:ml-6 rtl:pl-8">
                    <option value="work" selected>@lang('admin::app.common.custom-attributes.work')</option>
                    <option value="home">@lang('admin::app.common.custom-attributes.home')</option>
                </select>
            </div>
        </div>

        <span class="flex cursor-pointer items-center gap-2 text-brandColor">
            <i class="icon-add text-md !text-brandColor"></i>

            @lang("admin::app.common.custom-attributes.add-more")
        </span>
    </v-email-component>
@endif

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-email-component-template"
    >
        <template v-for="(email, index) in emails">
            <div class="mb-2 flex items-center">
                <x-admin::form.control-group.control
                    type="text"
                    ::id="attribute.code"
                    ::name="`${attribute['code']}[${index}][value]`"
                    class="rounded-r-none"
                    ::rules="getValidation"
                    ::label="attribute.name"
                    v-model="email['value']"
                />

                <div class="relative">
                    <x-admin::form.control-group.control
                        type="select"
                        ::id="attribute.code"
                        ::name="`${attribute['code']}[${index}][label]`"
                        class="rounded-l-none ltr:mr-6 ltr:pr-8 rtl:ml-6 rtl:pl-8"
                        rules="required"
                        ::label="attribute.name"
                        v-model="email['label']"
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

            <x-admin::form.control-group.error ::name="`${attribute['code']}[${index}][value]`"/>

            <x-admin::form.control-group.error ::name="`${attribute['code']}[${index}].value`"/>
        </template>

        <span
            class="flex cursor-pointer items-center gap-2 text-brandColor"
            @click="add"
        >
            <i class="icon-add text-md !text-brandColor"></i>

            @lang("admin::app.common.custom-attributes.add-more")
        </span>
    </script>

    <script type="module">
        app.component('v-email-component', {
            template: '#v-email-component-template',

            props: ['validations', 'attribute', 'value'],

            data() {
                return {
                    emails: this.value || [{'value': '', 'label': 'work'}],
                };
            },

            watch: { 
                value(newValue, oldValue) {
                    if (
                        JSON.stringify(newValue)
                        !== JSON.stringify(oldValue)
                    ) {
                        this.emails = newValue || [{'value': '', 'label': 'work'}];
                    }
                },
            },

            computed: {
                getValidation() {
                    return {
                        email: true,
                        unique_email: this.emails ?? [],
                        ...(this.validations === 'required' ? { required: true } : {}),
                    };
                },
            },

            created() {
                this.extendValidations();
            },

            methods: {
                add() {
                    this.emails.push({
                        'value': '',
                        'label': 'work'
                    });
                },

                remove(email) {
                    this.emails = this.emails.filter(item => item !== email);
                },

                extendValidations() {
                    defineRule('unique_email', (value, emails) => {
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
            },
        });
    </script>
@endPushOnce