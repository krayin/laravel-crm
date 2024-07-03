@php($value = system_config()->getConfigData($field->getNameKey()))

<input
    type="hidden"
    name="keys[]"
    value="{{ json_encode($child) }}"
/>

<configurable
    name="{{ $field->getNameField() }}"
    value="{{ $value }}"
    title="{{ trans($field->getTitle()) }}"
    validations="{{ $field->getValidations() }}"
    is-require="{{ $field->isRequired() }}"
    depend-name="{{ $field->getDependFieldName() }}"
    src="{{ Storage::url($value) }}"
    field-data="{{ json_encode($field) }}"
></configurable>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="configurable-template"
    >
        <div :class="['form-group', field.type, { 'has-error': hasError }]">
            <label
                :for="name"
                :class="isRequire"
            >
                @{{ title }}
            </label>

            <template v-if="field.type == 'password' || field.type== 'color' && field.is_visible">
                <input
                    :type="field.type"
                    :name="name"
                    class="control"
                    :id="name"
                    v-model="value"
                    v-validate="validations"
                    :data-vv-as="formattedTitle"
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
                        v-model="value"
                    />

                    <span class="slider round"></span>
                </label>
            </template>

            <template v-if="field.type == 'number' && field.is_visible">
                <input
                    :type="field.type"
                    :name="name"
                    class="control"
                    :id="name"
                    v-model="value"
                    v-validate="validations"
                    :data-vv-as="formattedTitle"
                />
            </template>

            <template v-if="field.type == 'text' && field.is_visible">
                <input
                    :type="field.type"
                    :name="name"
                    class="control"
                    :id="name"
                    v-model="value"
                    v-validate="validations"
                    :data-vv-as="formattedTitle"
                />
            </template>

            <template v-if="field.type == 'textarea' && field.is_visible">
                <textarea
                    :name="name"
                    class="control"
                    :id="name"
                    v-validate="validations"
                    :data-vv-as="formattedTitle"
                    v-model="value"
                ></textarea>
            </template>

            <!-- Select input -->
            <template v-if="field.type == 'select' && field.is_visible">
                <select
                    :name="name"
                    class="control"
                    :id="name"
                    v-validate="validations"
                    v-model="value"
                    :data-vv-as="formattedTitle"
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
                    v-validate="validations"
                    :data-vv-as="formattedTitle"
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
                    :href="`{{ route('admin.configuration.download', [request()->route('slug'), '']) }}/${value.split('/')[1]}`"
                    target="_blank"
                >
                    <i class="icon download-icon"></i>
                </a>

                <input
                    :type="field.type"
                    :name="name"
                    class="control"
                    :id="name"
                    v-validate="validations"
                    :data-vv-as="formattedTitle"
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
                        height="33"
                        width="33"
                    />
                </a>

                <input
                    :name="name"
                    type="file"
                    class="control"
                    :id="name"
                    v-validate="validations"
                    :data-vv-as="formattedTitle"
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

            <span
                class="control-error"
                v-if="errors.has(name)"
            >
                @{{ errors.first(name) }}
            </span>
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
                {{-- v-validate="validations" --}}
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
            <template v-if="! haveStates()">
                <input
                    type="text"
                    :name="name"
                    class="control"
                    :id="name"
                    v-model="state"
                    v-validate="'required'"
                    data-vv-as="&quot;{{ __('admin::app.customers.customers.state') }}&quot;"
                />
            </template>

           <template v-else>
                <select
                    :name="name"
                    class="control"
                    :id="name"
                    v-model="state"
                    v-validate="'required'"
                    data-vv-as="&quot;{{ __('admin::app.customers.customers.state') }}&quot;"
                >
                    <option value="">{{ __('admin::app.customers.customers.select-state') }}</option>

                    <option
                        v-for='(state, index) in countryStates[country]'
                        :value="state.code"
                    >
                        @{{ state.name }}
                    </option>
                </select>
           </template>
        </div>
    </script>

    <script>
        Vue.component('configurable', {
            template: '#configurable-template',

            inject: ['$validator'],

            props: [
                'dependName',
                'fieldData',
                'isRequire',
                'title',
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

            computed: {
                hasError() {
                    if (this.field.type == 'multiselect') {
                        return this.errors.has(`${this.name}[]`);
                    } else {
                        return this.errors.has(this.name);
                    }
                },

                formattedTitle() {
                    return `"${this.title}"`;
                },
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