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
                <textarea v-validate="validations" class="control" :name="attribute['code'] + '[address]'" data-vv-as="&quot;{{ __('admin::app.common.address') }}&quot;">@{{ data ? data['address'] : '' }}</textarea>
            </div>
    
            <div class="address-right">

                <select type="text" v-validate="validations" class="control" :name="attribute['code'] + '[country]'" v-model="country" data-vv-as="&quot;{{ __('admin::app.common.country') }}&quot;">
                    <option value="">{{ __('admin::app.common.select-country') }}</option>

                    @foreach (core()->countries() as $country)

                        <option value="{{ $country->code }}">{{ $country->name }}</option>

                    @endforeach
                </select>

                <select v-validate="validations" class="control" :name="attribute['code'] + '[state]'" v-model="state" v-if="haveStates()" data-vv-as="&quot;{{ __('admin::app.common.state') }}&quot;">

                    <option value="">{{ __('admin::app.common.select-state') }}</option>

                    <option v-for='(state, index) in countryStates[country]' :value="state.code">
                        @{{ state.name }}
                    </option>

                </select>

                <input
                    v-else
                    type="text"
                    v-model="state"
                    class="control"
                    v-validate="validations"
                    :name="attribute['code'] + '[state]'"
                    placeholder="{{ __('admin::app.common.state') }}"
                    data-vv-as="&quot;{{ __('admin::app.common.state') }}&quot;"
                />

                
                <input
                    type="text"
                    class="control"
                    :value="data['city']"
                    v-validate="validations"
                    v-if="data && data['city']"
                    :name="attribute['code'] + '[city]'"
                    placeholder="{{ __('admin::app.common.city') }}"
                    data-vv-as="&quot;{{ __('admin::app.common.city') }}&quot;"
                />

                <input
                    v-else
                    type="text"
                    class="control"
                    v-validate="validations"
                    :name="attribute['code'] + '[city]'"
                    placeholder="{{ __('admin::app.common.city') }}"
                    data-vv-as="&quot;{{ __('admin::app.common.city') }}&quot;"
                />
                
                <input
                    type="text"
                    class="control"
                    v-validate="validations"
                    :value="data['postcode']"
                    v-if="data && data['postcode']"
                    :name="attribute['code'] + '[postcode]'"
                    placeholder="{{ __('admin::app.common.postcode') }}"
                    data-vv-as="&quot;{{ __('admin::app.common.postcode') }}&quot;"
                />

                <input
                    v-else
                    type="text"
                    class="control"
                    v-validate="validations"
                    :name="attribute['code'] + '[postcode]'"
                    placeholder="{{ __('admin::app.common.postcode') }}"
                    data-vv-as="&quot;{{ __('admin::app.common.postcode') }}&quot;"
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