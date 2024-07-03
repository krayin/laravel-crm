@php
    $value = system_config()->getConfigData($field->getNameKey()) ?? $field->getDefault();
@endphp

<input
    type="hidden"
    name="keys[]"
    value="{{ json_encode($child) }}"
/>

<div class="mb-4 last:!mb-0">
    <configurable
        name="{{ $field->getNameField() }}"
        value="{{ $value }}"
        label="{{ trans($field->getTitle()) }}"
        info="{{ trans($field->getInfo()) }}"
        validations="{{ $field->getValidations() }}"
        is-require="{{ $field->isRequired() }}"
        depend-name="{{ $field->getDependFieldName() }}"
        src="{{ Storage::url($value) }}"
        field-data="{{ json_encode($field) }}"
    ></configurable>
</div>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="configurable-template"
    >
        <div
            {{-- class="form-group {{ $field['type'] }}" --}}
            class="form-group"
            {{-- @if ($field['type'] == 'multiselect')
                :class="[errors.has('{{ $fieldName }}[]') ? 'has-error' : '']"
            @else
                :class="[errors.has('{{ $fieldName }}') ? 'has-error' : '']"
            @endif --}}
        >

            <label
                :for="name"
                {{-- {{ !isset($field['validations']) || preg_match('/\brequired\b/', $field['validations']) == false ? '' : 'class=required' }} --}}
            >
                @{{ label }}
            </label>

            <template v-if="field.type == 'password' || field.type== 'color' && field.is_visible">
                <input
                    :type="field.type"
                    :name="name"
                    class="control"
                    :id="name"
                    :value="value"
                    {{-- v-validate="'{{ $validations }}'" --}}
                    :data-vv-as="label"
                />
            </template>

            <template v-if="field.type == 'boolean' && field.is_visible">
                <input
                    type="hidden"
                    :name="name"
                    value="0"
                />

                <label class="switch">
                    <input
                        type="checkbox"
                        :name="name"
                        class="control"
                        :id="name"
                        :value="1"
                        :checked="parseInt(value || 0)"
                    />

                    <span class="slider round"></span>
                </label>
            </template>

            <template v-if="field.type == 'text' && field.is_visible">
                <input
                    :type="field.type"
                    :name="name"
                    class="control"
                    :id="name"
                    :value="value"
                    {{-- v-validate="'{{ $validations }}'" --}}
                    :data-vv-as="label"
                />
            </template>

            <template v-if="field.type == 'textarea' && field.is_visible">
                <textarea
                    :name="name"
                    class="control"
                    :id="name"
                    {{-- v-validate="'{{ $validations }}'" --}}
                    :data-vv-as="label"
                    :value="value"
                ></textarea>
            </template>

            <!-- Select input -->
            <template v-if="field.type == 'select' && field.is_visible">
                <select
                    :name="name"
                    class="control"
                    :id="name"
                    {{-- v-validate="'{{ $validations }}'" --}}
                    :value="value"
                    :data-vv-as="label"
                >
                    <option
                        v-for="option in field.options"
                        :value="option.value"
                        v-text="option.title"
                    >
                    </option>
                </select>
            </template>

            <!-- Multiselect Input -->
            <template v-if="field.type == 'multiselect' && field.is_visible">
                <select
                    :name="`${name}[]`"
                    multiple
                    class="control"
                    :id="`${name}[]`"
                    {{-- v-validate="'{{ $validations }}'" --}}
                    :data-vv-as="label"
                    v-model="value.split(',')"
                    multiple
                >
                    <option
                        v-for="option in field.options"
                        :key="option.value"
                        :value="option.value"
                        v-text="option.title"
                    >
                    </option>
                </select>
            </template>

            <template v-if="field.type == 'file' && field.is_visible">
                <a
                    v-if="value"
                    :href="`{{ route('admin.configuration.download', [request()->route('slug'), request()->route('slug2'), '']) }}/${value.split('/')[1]}`"
                >
                    <i class="icon sort-down-icon download"></i>
                </a>

                <input
                    :type="field.type"
                    :name="name"
                    class="control"
                    :id="name"
                    :value="value"
                    {{-- v-validate="'{{ $validations }}'" --}}
                    :data-vv-as="label"
                />

                <div
                    v-if="value"
                    class="form-group"
                >
                    <span class="checkbox">
                        <input
                            :name="`${name}[delete]`"
                            :id="`${name}[delete]`"
                            type="checkbox"
                            value="1"
                        />

                        <label
                            class="checkbox-view"
                            :for="`${name}[delete]`"
                        ></label>

                        {{ __('admin::app.configuration.delete') }}
                    </span>
                </div>
            </template>

            <template v-if="field.type == 'image' && field.is_visible">
                <a
                    v-if="value"
                    :href="src"
                    target="_blank"
                >
                    <img
                        :src="src"
                        :alt="name"
                        class="configuration-image"
                    />
                </a>

                <input
                    :name="name"
                    type="file"
                    class="control"
                    :id="name"
                    :value="value"
                    {{-- v-validate="'{{ $validations }}'" --}}
                    :data-vv-as="label"
                />

                <div
                    v-if="value"
                    class="form-group"
                >
                    <span class="checkbox">
                        <input
                            type="checkbox"
                            :name="`${name}[delete]`"
                            :id="`${name}[delete]`"
                            :value="1"
                        />

                        <label
                            class="checkbox-view"
                            :for="`${name}[delete]`"
                        ></label>

                        {{ __('admin::app.configuration.delete') }}
                    </span>
                </div>
            </template>

            <template v-if="field.type == 'state' && field.is_visible">
                <state
                    :name="name"
                    :state_code="value"
                    :validations="''"
                ></state>
            </template>

            <template v-if="field.type == 'country' && field.is_visible">
                <country
                    :name="name"
                    :country_code="value"
                    :validations="''"
                ></country>
            </template>
        </div>
    </script>

    <script type="text/x-template" id="country-template">
        <div>
            <select
                type="text"
                :name="name"
                class="control"
                :id="name"
                v-model="country"
                v-validate="validations"
                data-vv-as="&quot;{{ __('admin::app.customers.customers.country') }}&quot;"
                @change="sendCountryCode"
            >
                <option value=""></option>

                @foreach (core()->countries() as $country)

                    <option value="{{ $country->code }}">{{ $country->name }}</option>

                @endforeach
            </select>
        </div>

    </script>

    <script type="text/x-template" id="state-template">

        <div>
            <input
                type="text"
                :name="name"
                class="control"
                :id="name"
                v-model="state"
                v-validate="'required'"
                data-vv-as="&quot;{{ __('admin::app.customers.customers.state') }}&quot;"
                v-if="! haveStates()"
            />

            <select
                :name="name"
                class="control"
                :id="name"
                v-model="state"
                v-validate="'required'"
                data-vv-as="&quot;{{ __('admin::app.customers.customers.state') }}&quot;"
                v-if="haveStates()"
            >
                <option value="">{{ __('admin::app.customers.customers.select-state') }}</option>

                <option
                    v-for='(state, index) in countryStates[country]'
                    :value="state.code"
                    :text="state.name"
                ></option>
            </select>

        </div>

    </script>

    <script>
        Vue.component('configurable', {
            template: '#configurable-template',

            props: [
                'dependName',
                'fieldData',
                'info',
                'isRequire',
                'label',
                'name',
                'src',
                'validations',
                'value',
            ],

            data() {
                return {
                    field: JSON.parse(this.fieldData),
                };
            },

            mounted() {
                if (! this.dependName) {
                    return;
                }

                const dependElement = document.getElementById(this.dependName);

                if (! dependElement) {
                    return;
                }

                dependElement.addEventListener('change', (event) => {
                    this.field['is_visible'] = 
                        event.target.type === 'checkbox' 
                        ? event.target.checked
                        : this.validations.split(',').slice(1).includes(event.target.value);
                });

                dependElement.dispatchEvent(new Event('change'));
            },
        });

        Vue.component('country', {

            template: '#country-template',

            inject: ['$validator'],

            props: ['country_code', 'name', 'validations'],

            data: function () {
                return {
                    country: "",
                }
            },

            mounted: function () {
                this.country = this.country_code;
                this.sendCountryCode()
            },

            methods: {
                sendCountryCode: function () {
                    this.$root.$emit('countryCode', this.country)
                },
            }
        });

        Vue.component('state', {

            template: '#state-template',

            inject: ['$validator'],

            props: ['state_code', 'name', 'validations'],

            data: function () {
                return {
                    state: "",

                    country: "",

                    countryStates: @json(core()->groupedStatesByCountries())
                }
            },

            mounted: function () {
                this.state = this.state_code
            },

            methods: {
                haveStates: function () {
                    var this_this = this;

                    this_this.$root.$on('countryCode', function (country) {
                        this_this.country = country;
                    });

                    if (this.countryStates[this.country] && this.countryStates[this.country].length)
                        return true;

                    return false;
                },
            }
        });
    </script>
@endPushOnce