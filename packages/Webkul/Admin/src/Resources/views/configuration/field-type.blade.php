@php   
    $validations = [];

    if (isset($field['validations'])) {
        array_push($validations, $field['validations']);
    }

    $validations = implode('|', array_filter($validations));

    $key = explode(".", $item['key']);

    $firstField = current($key);

    $secondField = next($key);

    $name = $item['key'] . '.' . $field['name'];

    if (isset($field['data_source'])) {
        $temp = explode("@", $field['data_source']);

        $value = app(current($temp))->{end($temp)}();
    }

    $fieldName = $firstField . "[" . $secondField . "][" . $field['name'] . "]";
@endphp

@if ($field['type'] == 'depends')
    @php        
        $depends = explode(":", $field['depend']);

        $dependField = current($depends);

        $dependValue = end($depends);

        if (isset($value) && $value) {
            $i = 0;

            foreach ($value as $key => $result) {
                $data['title'] = $result;

                $data['value'] = $key;

                $options[$i] = $data;

                $i++;
            }

            $field['options'] = $options;
        }

        if (! isset($field['options'])) {
            $field['options'] = '';
        }

        $selectedOption = core()->getConfigData($name) ?? '';
    @endphp

    <depends
        :name="'{{ $fieldName }}'"
        :value="'{{ $dependValue }}'"
        :result="'{{ $selectedOption }}'"
        :options='@json($field['options'])'
        :validations="'{{ $validations }}'"
        :field_name="'{{ trans($field['title']) }}'"
        :depend="'{{ $firstField }}[{{ $secondField }}][{{ $dependField }}]'"
    ></depends>

@else
    <div
        class="form-group {{ $field['type'] }}"
        @if ($field['type'] == 'multiselect')
            :class="[errors.has('{{ $fieldName }}[]') ? 'has-error' : '']"
        @else
            :class="[errors.has('{{ $fieldName }}') ? 'has-error' : '']"
        @endif
    >

        <label
            for="{{ $name }}"
            {{ !isset($field['validations']) || preg_match('/\brequired\b/', $field['validations']) == false ? '' : 'class=required' }}
        >
            {{ __($field['title']) }}

        </label>

        @if ($field['type'] == "password" || $field['type'] == "color")
            @include('admin::configuration.fields.input')
        @else
            @include('admin::configuration.fields.' . $field['type'])
        @endif

        @if (isset($field['info']))
            <span class="control-info mt-10">{{ trans($field['info']) }}</span>
        @endif

        <span
            class="control-error"
            @if ($field['type'] == 'multiselect')
                v-if="errors.has('{{ $fieldName }}[]')"
            @else
                v-if="errors.has('{{ $fieldName }}')"
            @endif
        >
            @if ($field['type'] == 'multiselect')
                @{{ errors.first('{!! $firstField !!}[{!! $secondField !!}][{!! $field['name'] !!}][]') }}
            @else
                @{{ errors.first('{!! $firstField !!}[{!! $secondField !!}][{!! $field['name'] !!}]') }}
            @endif
        </span>

    </div>

@endif

@push('scripts')
    @if ($field['type'] == 'country')
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

                    <option v-for='(state, index) in countryStates[country]' :value="state.code">
                        @{{ state.default_name }}
                    </option>

                </select>

            </div>

        </script>

        <script>
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
    @endif

    <script type="text/x-template" id="depends-template">

        <div class="form-group"  :class="[errors.has(name) ? 'has-error' : '']" v-if="this.isVisible">
            <label :for="name" :class="[ isRequire ? 'required' : '']">
                @{{ field_name }}
            </label>

            <select
                :id="name"
                :name="name"
                class="control"
                v-model="savedValue"
                v-validate="validations"
                :data-vv-as="field_name"
                v-if="this.options.length"
            >
                <option v-for='(option, index) in this.options' :value="option.value">
                    @{{ option.title }}
                </option>
            </select>

            <input
                :id="name"
                :name="name"
                class="control"
                v-else type="text"
                v-model="savedValue"
                v-validate="validations"
                :data-vv-as="field_name"
            >

            <span class="control-error" v-if="errors.has(name)">
                @{{ errors.first(name) }}
            </span>
        </div>

    </script>

    <script>
        Vue.component('depends', {

            template: '#depends-template',

            inject: ['$validator'],

            props: [
                'name',
                'depend',
                'value',
                'result',
                'options',
                'data_source',
                'field_name',
                'validations',
            ],

            data: function() {
                return {
                    isRequire: false,
                    isVisible: false,
                    savedValue: "",
                }
            },

            mounted: function () {
                var this_this = this;

                this_this.savedValue = this_this.result;

                if (this_this.validations || (this_this.validations.indexOf("required") != -1)) {
                    this_this.isRequire = true;
                }

                $(document).ready(function(){
                    var dependentElement = document.getElementById(this_this.depend);
                    var dependValue = this_this.value;

                    if (dependValue == 'true') {
                        dependValue = 1;
                    } else if (dependValue == 'false') {
                        dependValue = 0;
                    }

                    $(document).on("change", "select.control", function() {
                        if (this_this.depend == this.name) {
                            if (this_this.value == this.value) {
                                this_this.isVisible = true;
                            } else {
                                this_this.isVisible = false;
                            }
                        }
                    })

                    if (dependentElement && dependentElement.value == dependValue) {
                        this_this.isVisible = true;
                    } else {
                        this_this.isVisible = false;
                    }

                    if (this_this.result) {
                        if (dependentElement.value == this_this.value) {
                            this_this.isVisible = true;
                        } else {
                            this_this.isVisible = false;
                        }
                    }
                });
            }
        });
    </script>

@endpush