@if (isset($attribute))
    <address-component
        :attribute='@json($attribute)'
        :validations="'{{$validations}}'"
        :data='@json(old($attribute->code) ?: $value)'
    ></address-component>
@endif

@push('scripts')

    <script type="text/x-template" id="address-component-template">
        <div class="form-group" :class="[errors.has(attribute['code'] + '[address]') || errors.has(attribute['code'] + '[country]') || errors.has(attribute['code'] + '[state]') || errors.has(attribute['code'] + '[city]') || errors.has(attribute['code'] + '[postcode]') ? 'has-error' : '']">
            <div class="address-left">
                <textarea
                    :name="attribute['code'] + '[address]'"
                    class="control"
                    v-validate="validations"
                    data-vv-as="&quot;{{ __('admin::app.common.address') }}&quot;"
                >@{{ data ? data['address'] : '' }}</textarea>
            </div>
    
            <div class="address-right">

                <select
                    :name="attribute['code'] + '[country]'"
                    class="control"
                    v-model="country"
                    v-validate="validations"
                    data-vv-as="&quot;{{ __('admin::app.common.country') }}&quot;"
                >
                    <option value="">{{ __('admin::app.common.select-country') }}</option>

                    @foreach (core()->countries() as $country)

                        <option value="{{ $country->code }}">{{ $country->name }}</option>

                    @endforeach
                </select>

                <select
                    :name="attribute['code'] + '[state]'"
                    class="control"
                    v-model="state"
                    v-validate="validations"
                    data-vv-as="&quot;{{ __('admin::app.common.state') }}&quot;"
                    v-if="haveStates()"
                >

                    <option value="">{{ __('admin::app.common.select-state') }}</option>

                    <option v-for='(state, index) in countryStates[country]' :value="state.code">
                        @{{ state.name }}
                    </option>

                </select>

                <input
                    type="text"
                    :name="attribute['code'] + '[state]'"
                    class="control"
                    v-model="state"
                    placeholder="{{ __('admin::app.common.state') }}"
                    v-validate="validations"
                    data-vv-as="&quot;{{ __('admin::app.common.state') }}&quot;"
                    v-else
                />

                
                <input
                    type="text"
                    :name="attribute['code'] + '[city]'"
                    class="control"
                    :value="data['city']"
                    placeholder="{{ __('admin::app.common.city') }}"
                    v-validate="validations"
                    data-vv-as="&quot;{{ __('admin::app.common.city') }}&quot;"
                    v-if="data && data['city']"
                />

                <input
                    type="text"
                    :name="attribute['code'] + '[city]'"
                    class="control"
                    placeholder="{{ __('admin::app.common.city') }}"
                    v-validate="validations"
                    data-vv-as="&quot;{{ __('admin::app.common.city') }}&quot;"
                    v-else
                />
                
                <input
                    type="text"
                    :name="attribute['code'] + '[postcode]'"
                    class="control"
                    :value="data['postcode']"
                    placeholder="{{ __('admin::app.common.postcode') }}"
                    v-validate="validations"
                    data-vv-as="&quot;{{ __('admin::app.common.postcode') }}&quot;"
                    v-if="data && data['postcode']"
                />

                <input
                    type="text"
                    :name="attribute['code'] + '[postcode]'"
                    class="control"
                    placeholder="{{ __('admin::app.common.postcode') }}"
                    v-validate="validations"
                    data-vv-as="&quot;{{ __('admin::app.common.postcode') }}&quot;"
                    v-else
                />
            </div>

            <span class="control-error" v-if="errors.has(attribute['code'] + '[address]') || errors.has(attribute['code'] + '[country]') || errors.has(attribute['code'] + '[state]') || errors.has(attribute['code'] + '[city]') || errors.has(attribute['code'] + '[postcode]')">
                {{ __('admin::app.common.address-validation') }}
            </span>
        </div>
    </script>

    <script>
        Vue.component('address-component', {

            template: '#address-component-template',
    
            props: ['validations', 'attribute', 'data'],

            inject: ['$validator'],

            data: function () {
                return {
                    country: this.data ? this.data['country'] : '',

                    state: this.data ? this.data['state'] : '',

                    countryStates: @json(core()->groupedStatesByCountries())
                }
            },

            methods: {
                haveStates: function () {
                    if (this.countryStates[this.country] && this.countryStates[this.country].length) {
                        return true;
                    }

                    return false;
                },
            }
        });
    </script>
@endpush