@push('scripts')
    <script type="text/x-template" id="contact-component-template">
        <div class="contact-controls">
            
            <div class="form-group" :class="[errors.has('{!! $formScope ?? '' !!}person[name]') ? 'has-error' : '']">
                <label for="person[name]" class="required">{{ __('admin::app.leads.name') }}</label>

                <input
                    type="text"
                    name="person[name]"
                    class="control"
                    id="person[name]"
                    v-model="person.name"
                    autocomplete="off"
                    placeholder="{{ __('admin::app.common.start-typing') }}"
                    v-validate="'required'"
                    data-vv-as="&quot;{{ __('admin::app.leads.name') }}&quot;"
                    v-on:keyup="search"
                />

                <input
                    type="hidden"
                    name="person[id]"
                    v-model="person.id"
                    v-validate="'required'"
                    data-vv-as="&quot;{{ __('admin::app.leads.name') }}&quot;"
                    v-if="person.id"
                />

                <div class="lookup-results" v-if="state == ''">
                    <ul>
                        <li v-for='(person, index) in persons' @click="addPerson(person)">
                            <span>@{{ person.name }}</span>
                        </li>

                        <li v-if="! persons.length && person['name'].length && ! is_searching">
                            <span>{{ __('admin::app.common.no-result-found') }}</span>
                        </li>

                        <li class="action" v-if="person['name'].length && ! is_searching" @click="addAsNew()">
                            <span>
                                + {{ __('admin::app.common.add-as') }}
                            </span> 
                        </li>
                    </ul>
                </div>

                <span class="control-error" v-if="errors.has('{!! $formScope ?? '' !!}person[name]')">
                    @{{ errors.first('{!! $formScope ?? '' !!}person[name]') }}
                </span>
            </div>

            <div class="form-group email">
                <label for="person[emails]" class="required">{{ __('admin::app.leads.email') }}</label>

                @include('admin::common.custom-attributes.edit.email', ['formScope' => $formScope ?? ''])
                    
                <email-component
                    :attribute="{'code': 'person[emails]', 'name': 'Email'}"
                    :data="person.emails"
                    validations="required|email"
                ></email-component>
            </div>

            <div class="form-group contact-numbers">
                <label for="person[contact_numbers]">{{ __('admin::app.leads.contact-numbers') }}</label>

                @include('admin::common.custom-attributes.edit.phone', ['formScope' => $formScope ?? ''])
                    
                <phone-component
                    :attribute="{'code': 'person[contact_numbers]', 'name': 'Contact Numbers'}"
                    :data="person.contact_numbers"
                ></phone-component>
            </div>

            <div class="form-group organization">
                <label for="address">{{ __('admin::app.leads.organization') }}</label>

                @php
                    $organizationAttribute = app('Webkul\Attribute\Repositories\AttributeRepository')->findOneWhere([
                        'entity_type' => 'persons',
                        'code'        => 'organization_id'
                    ]);

                    $organizationAttribute->code = 'person[' . $organizationAttribute->code . ']';
                @endphp

                @include('admin::common.custom-attributes.edit.lookup')

                <lookup-component
                    :attribute='@json($organizationAttribute)'
                    :data="person.organization"
                ></lookup-component>
            </div>
        </div>
    </script>

    <script>
        Vue.component('contact-component', {

            template: '#contact-component-template',
    
            props: ['data'],

            inject: ['$validator'],

            data: function () {
                return {
                    is_searching: false,

                    state: this.data ? 'old': '',

                    person: this.data ? this.data : {
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
                },

                addAsNew: function() {
                    this.state = 'new';
                }
            }
        });
    </script>
@endpush