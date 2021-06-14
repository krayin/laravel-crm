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
                        <input type="hidden" v-validate="validations" :name="controlName" :data-vv-as="'&quot;' + controlLabel + '&quot;'" v-if="! emails.length"/>
                        <input type="text" :name="controlName" v-validate="'email'" :data-vv-as="'&quot;' + controlLabel + '&quot;'" v-model="email_term" @keydown.enter.prevent="addTag" placeholder="{{ __('admin::app.leads.email-placeholder') }}">
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
                        this.emails.push(this.email_term)

                        this.email_term = '';
                    },

                    removeTag: function(email) {
                        const index = this.emails.indexOf(email);

                        Vue.delete(this.emails, index);
                    }
                }
            });
        </script>

    @endpush
@endonce
