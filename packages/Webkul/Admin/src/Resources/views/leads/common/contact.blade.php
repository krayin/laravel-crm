{!! view_render_event('admin.leads.create.contact_person.form_controls.before') !!}

<v-contact-component :data="person"></v-contact-component>

{!! view_render_event('admin.leads.create.contact_person.form_controls.after') !!}

@pushOnce('scripts')
    <script 
        type="text/x-template" 
        id="v-contact-component-template"
    >
        <!-- Person Search lookup -->
        <x-admin::form.control-group>
            <x-admin::form.control-group.label class="required">
                @lang('admin::app.leads.common.contact.name')
            </x-admin::form.control-group.label>
            
            <x-admin::lookup
                ::src="src"
                name="person[id]"
                ::params="params"
                @on-selected="addPerson"
                :placeholder="trans('admin::app.leads.common.contact.name')"
                ::value="{id: person.id, name: person.name}"
                :can-add-new="true"
            />
        
            <x-admin::form.control-group.control
                type="hidden"
                name="person[name]"
                v-model="person.name"
                v-if="person.name"
                rules="required"
            />
        
            <x-admin::form.control-group.error control-name="person[id]" />
        </x-admin::form.control-group>

        <!-- Person Email -->
        <x-admin::form.control-group>
            <x-admin::form.control-group.label class="required">
                @lang('admin::app.leads.common.contact.email')
            </x-admin::form.control-group.label>

            <x-admin::attributes.edit.email />
            
            <v-email-component
                :attribute="{'code': 'person[emails]', 'name': 'Email'}"
                validations="required"
                :value="person.emails"
            ></v-email-component>

        </x-admin::form.control-group>
            
        <!-- Person Contact Numbers -->
        <x-admin::form.control-group>
            <x-admin::form.control-group.label>
                @lang('admin::app.leads.common.contact.contact-number')
            </x-admin::form.control-group.label>

            <x-admin::attributes.edit.phone />

            <v-phone-component
                :attribute="{'code': 'person[contact_numbers]', 'name': 'Contact Numbers'}"
                :value="person.contact_numbers"
            ></v-phone-component>
        </x-admin::form.control-group>
        
        <!-- Person Organization -->
        <x-admin::form.control-group>
            <x-admin::form.control-group.label>
                @lang('admin::app.leads.common.contact.organization')
            </x-admin::form.control-group.label>
            
            @php
                $organizationAttribute = app('Webkul\Attribute\Repositories\AttributeRepository')->findOneWhere([
                    'entity_type' => 'persons',
                    'code'        => 'organization_id'
                ]);

                $organizationAttribute->code = 'person[' . $organizationAttribute->code . ']';
            @endphp

            <x-admin::attributes.edit.lookup />

            <v-lookup-component
                :attribute='@json($organizationAttribute)'
                :value="person.organization"
            ></v-lookup-component>
        </x-admin::form.control-group>
    </script>

    <script type="module">
        app.component('v-contact-component', {
            template: '#v-contact-component-template',
            
            props: ['data'],

            data: function () {
                return {
                    is_searching: false,

                    person: this.data ? this.data : {
                        'name': ''
                    },

                    persons: [],
                }
            },

            computed: {
                src() {
                    return "{{ route('admin.contacts.persons.search') }}";
                },

                params() {
                    return {
                        params: {
                            query: this.person['name']
                        }
                    }
                }
            },

            methods: {
                addPerson (person) {
                    this.person = person;
                },
            }
        });
    </script>
@endPushOnce