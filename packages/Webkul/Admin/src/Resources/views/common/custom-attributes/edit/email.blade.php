<v-email-component
    :attribute="{{ json_encode($attribute) }}"
    :validations="'{{ $validations }}'"
    :value="{{ json_encode(old($attribute->code) ?? $value) }}"
>
    <div class="flex items-center mt-2">
        <input
            type="text"
            class="w-full border px-3 py-2.5 text-sm transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400 rounded-none ltr:!rounded-l-lg rtl:!rounded-r-lg text-gray-700"
        >

        <div class="relative">
            <select class="custom-select w-full border bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 rounded-none ltr:!rounded-r-lg rtl:!rounded-l-lg ltr:mr-6 rtl:ml-6">
                <option value="work" selected>Work</option>
                <option value="home">Home</option>
            </select>
        </div>
    </div>

    <span class="cursor-pointer">
        + @lang("admin::app.common.add_more")
    </span>
</v-email-component>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-email-component-template"
    >
        <template v-for="(email, index) in emails">
            <div class="flex items-center mt-2">
                <x-admin::form.control-group.control
                    type="text"
                    ::id="attribute.code"
                    ::name="`${attribute['code']}[${index}][value]`"
                    class="rounded-none ltr:!rounded-l-lg rtl:!rounded-r-lg text-gray-700"
                    ::rules="getValidation"
                    ::label="attribute.name"
                    v-model="email['value']"
                />

                <div class="relative">
                    <x-admin::form.control-group.control
                        type="select"
                        ::id="attribute.code"
                        ::name="`${attribute['code']}[${index}][label]`"
                        class="rounded-none ltr:!rounded-r-lg rtl:!rounded-l-lg ltr:mr-6 rtl:ml-6"
                        rules="required"
                        ::label="attribute.name"
                        v-model="email['label']"
                    >
                        <option value="work">@lang('admin::app.common.work')</option>
                        <option value="home">@lang('admin::app.common.home')</option>
                    </x-admin::form.control-group.control>
                </div>

                <i
                    v-if="emails.length > 1"
                    class="cursor-pointer rounded-md p-1.5 ml-1 text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-950 icon-delete"
                    @click="remove(email)"
                ></i>
            </div>

            <x-admin::form.control-group.error ::name="`${attribute['code']}[${index}][value]`"/>
        </template>

        <span
            class="cursor-pointer"
            @click="add"
        >
            + @lang("admin::app.common.add_more")
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
                data(newValue, oldValue) {
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
