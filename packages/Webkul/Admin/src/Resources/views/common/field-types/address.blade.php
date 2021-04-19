<address-component></address-component>

@push('scripts')

    <script type="text/x-template" id="address-component-template">
        <div class="form-group" :class="[errors.has('{{ $attribute->code }}[address]') || errors.has('{{ $attribute->code }}[country]') || errors.has('{{ $attribute->code }}[state]') || errors.has('{{ $attribute->code }}[city]') || errors.has('{{ $attribute->code }}[postcode]') ? 'has-error' : '']">
            <div class="address-left">
                <textarea v-validate="'{{$validations}}'" class="control" name="{{ $attribute->code }}[address]" data-vv-as="&quot;{{ __('admin::app.common.address') }}&quot;">{{ old($attribute->code)['address'] ?? ($value['address'] ?? null) }}</textarea>
            </div>
    
            <div class="address-right">

                <select type="text" v-validate="'{{$validations}}'" class="control" name="{{ $attribute->code }}[country]" v-model="country" data-vv-as="&quot;{{ __('admin::app.common.country') }}&quot;">
                    <option value="">{{ __('admin::app.common.select-country') }}</option>

                    @foreach (core()->countries() as $country)

                        <option value="{{ $country->code }}">{{ $country->name }}</option>

                    @endforeach
                </select>

                <input type="text" v-validate="'{{$validations}}'" class="control" name="{{ $attribute->code }}[state]" v-model="state" v-if="! haveStates()" data-vv-as="&quot;{{ __('admin::app.common.state') }}&quot;" placeholder="{{ __('admin::app.common.state') }}"/>

                <select v-validate="'{{$validations}}'" class="control" name="{{ $attribute->code }}[state]" v-model="state" v-if="haveStates()" data-vv-as="&quot;{{ __('admin::app.common.state') }}&quot;">

                    <option value="">{{ __('admin::app.common.select-state') }}</option>

                    <option v-for='(state, index) in countryStates[country]' :value="state.code">
                        @{{ state.name }}
                    </option>

                </select>

                <input type="text" class="control" name="{{ $attribute->code }}[city]" value="{{ old($attribute->code)['city'] ?? ($value['city'] ?? null) }}" v-validate="'{{$validations}}'" data-vv-as="&quot;{{ __('admin::app.common.city') }}&quot;" placeholder="{{ __('admin::app.common.city') }}">
                
                <input type="text" class="control" name="{{ $attribute->code }}[postcode]" value="{{ old($attribute->code)['postcode'] ?? ($value['postcode'] ?? null) }}" v-validate="'{{$validations}}'" data-vv-as="&quot;{{ __('admin::app.common.postcode') }}&quot;" placeholder="{{ __('admin::app.common.postcode') }}">
            </div>

            <span class="control-error" v-if="errors.has('{{ $attribute->code }}[address]') || errors.has('{{ $attribute->code }}[country]') || errors.has('{{ $attribute->code }}[state]') || errors.has('{{ $attribute->code }}[city]') || errors.has('{{ $attribute->code }}[postcode]')">
                {{ __('admin::app.common.address-validation') }}
            </span>
        </div>
    </script>

    <script>
        Vue.component('address-component', {

            template: '#address-component-template',

            inject: ['$validator'],

            data: function () {
                return {
                    country: "{{ old($attribute->code)['country'] ?? ($value['country'] ?? null) }}",

                    state: "{{ old($attribute->code)['state'] ?? ($value['state'] ?? null) }}",

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