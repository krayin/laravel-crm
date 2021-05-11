<contact-component></contact-component>

@push('scripts')
    <script type="text/x-template" id="contact-component-template">
        <div class="contact-controls">
            
            <div class="form-group" :class="[errors.has('name') ? 'has-error' : '']">
                <label for="name" class="required">{{ __('admin::app.leads.name') }}</label>

                <input type="text" v-validate="'required'" class="control" id="name" name="name" v-model="person.name" v-on:keyup="search" data-vv-as="&quot;{{ __('admin::app.leads.name') }}&quot;" autocomplete="off"/>

                <input type="hidden" v-validate="'required'" name="name" data-vv-as="&quot;{{ __('admin::app.leads.name') }}&quot;" v-model="person.id"/>

                <div class="lookup-results" v-if="state == ''">
                    <ul>
                        <li v-for='(person, index) in persons' @click="addPerson(person)">
                            <span>@{{ person.name }}</span>
                        </li>

                        <li v-if="! persons.length && person['name'].length && ! is_searching">
                            <span>{{ __('admin::app.common.no-result-found') }}</span>
                        </li>
                    </ul>
                </div>

                <span class="control-error" v-if="errors.has('name')">@{{ errors.first('name') }}</span>
            </div>

            <div class="form-group email">
                <label for="emails" class="required">{{ __('admin::app.leads.email') }}</label>

                @include('admin::common.field-types.email')
                    
                <email-component :attribute="{'code': 'email', 'name': 'Email'}" validations="required|email" :data="person.emails"></email-component>
            </div>

            <div class="form-group contact-numbers">
                <label for="contact_numbers">{{ __('admin::app.leads.contact-numbers') }}</label>

                @include('admin::common.field-types.phone')
                    
                <phone-component :attribute="{'code': 'contact_numbers', 'name': 'Contact Numbers'}" :data="person.contact_numbers"></phone-component>
            </div>

            <div class="form-group organization">
                <label for="address">{{ __('admin::app.leads.organization') }}</label>

                @php
                    $organizationAttribute = app('Webkul\Attribute\Repositories\AttributeRepository')->findOneWhere([
                        'entity_type' => 'persons',
                        'code'        => 'organization_id'
                    ]);
                @endphp

                @include('admin::common.field-types.lookup')

                <lookup-component :attribute='@json($organizationAttribute)' :data="person.organization"></lookup-component>
            </div>
        </div>
    </script>

    <script>
        Vue.component('contact-component', {

            template: '#contact-component-template',

            inject: ['$validator'],

            data: function () {
                return {
                    is_searching: false,

                    state: '',

                    person: {
                        'name': ''
                    },

                    persons: [],
                }
            },

            methods: {
                search: debounce(function () {
                    this.state = '';

                    this.person = {
                        'name': this.person['name']
                    };

                    this.is_searching = true;

                    if (this.person['name'].length < 2) {
                        this.persons = [];

                        this.is_searching = false;

                        return;
                    }

                    var self = this;
                    
                    this.$http.get("{{ route('admin.contacts.persons.search') }}", {params: {query: this.person['name']}})
                        .then (function(response) {
                            self.persons = response.data;

                            self.is_searching = false;
                        })
                        .catch (function (error) {
                            self.is_searching = false;
                        })
                }, 500),

                addPerson: function(result) {
                    this.state = 'old';

                    this.person = result;
                }
            }
            });
    </script>
@endpush