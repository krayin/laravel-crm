{!! view_render_event('admin.leads.create.contact_person.form_controls.before') !!}

<v-contact-component :data="person"></v-contact-component>

{!! view_render_event('admin.leads.create.contact_person.form_controls.after') !!}

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-contact-component-template"
    >
        <!-- Person Search Lookup -->
        <x-admin::form.control-group>
            <x-admin::form.control-group.label class="required">
                @lang('admin::app.leads.common.contact.name')
            </x-admin::form.control-group.label>

            <x-admin::lookup
                ::src="src"
                name="person[id]"
                ::params="params"
                ::rules="nameValidationRule"
                :label="trans('admin::app.leads.common.contact.name')"
                ::value="{id: person.id, name: person.name}"
                :placeholder="trans('admin::app.leads.common.contact.name')"
                @on-selected="addPerson"
                :can-add-new="true"
            />

            <x-admin::form.control-group.control
                type="hidden"
                name="person[name]"
                v-model="person.name"
                v-if="person.name"
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
                :attribute="{'id': person?.id, 'code': 'person[emails]', 'name': 'Email'}"
                validations="required"
                :value="person.emails"
                :is-disabled="person?.id ? true : false"
            ></v-email-component>
        </x-admin::form.control-group>

        <!-- Person Contact Numbers -->
        <x-admin::form.control-group>
            <x-admin::form.control-group.label>
                @lang('admin::app.leads.common.contact.contact-number')
            </x-admin::form.control-group.label>

            <x-admin::attributes.edit.phone />

            <v-phone-component
                :attribute="{'id': person?.id, 'code': 'person[contact_numbers]', 'name': 'Contact Numbers'}"
                :value="person.contact_numbers"
                :is-disabled="person?.id ? true : false"
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
                :key="person.organization?.id"
                :attribute='@json($organizationAttribute)'
                :value="person.organization"
                :is-disabled="person?.id ? true : false"
                can-add-new="true"
            ></v-lookup-component>
        </x-admin::form.control-group>
    </script>

    <script type="module">
        app.component('v-contact-component', {
            template: '#v-contact-component-template',

            props: ['data'],

            data () {
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
                },

                nameValidationRule() {
                    return this.person.name ? '' : 'required';
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