@once
    @push('scripts')
        <script type="text/x-template" id="email-tags-component-template">
            <div class="tags-control control">
                <ul class="tags">
                    <li class="tag-choice" v-for="email in emails">
                        <input type="hidden" :name="controlName" :value="email"/>

                        @{{ email }}

                        <i class="icon close-icon" @click="removeTag(email)"></i>
                    </li>

                    <li class="tag-input">
                        <input
                            type="hidden"
                            :name="controlName"
                            v-validate="validations"
                            :data-vv-as="'&quot;' + controlLabel + '&quot;'"
                            v-if="! emails.length && email_term == ''"
                        />

                        <input
                            type="text"
                            :name="controlName"
                            v-model="email_term"
                            placeholder="{{ __('admin::app.leads.email-placeholder') }}"
                            v-validate="'email'"
                            :data-vv-as="'&quot;' + controlLabel + '&quot;'"
                            @keydown.enter.prevent="addTag"
                        />
                    </li>
                </ul>
            </div>
        </script>

        <script>
            Vue.component('email-tags-component', {
                template: '#email-tags-component-template',

                props: ['controlName', 'controlLabel', 'validations', 'data'],

                inject: ['$validator'],

                data: function () {
                    return {
                        emails: this.data ? this.data : [],

                        email_term: '',
                    }
                },

                methods: {
                    addTag: function() {
                        let sanitizedEmail = this.email_term.trim();

                        if (this.validateEmail(sanitizedEmail)) {
                            this.emails.push(sanitizedEmail);

                            this.email_term = '';
                        }
                    },

                    removeTag: function(email) {
                        const index = this.emails.indexOf(email);

                        Vue.delete(this.emails, index);
                    },

                    validateEmail: function (email) {
                        const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

                        return re.test(String(email).toLowerCase());
                    }
                }
            });
        </script>
    @endpush
@endonce
