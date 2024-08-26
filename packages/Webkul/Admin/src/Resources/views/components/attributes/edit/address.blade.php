@if (isset($attribute))
    <v-address-component
        :attribute='@json($attribute)'
        :data='@json(old($attribute->code) ?: $value)'
    >
        <!-- Addresses Shimmer -->    
        <x-admin::shimmer.common.address />
    </v-address-component>
@endif

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-address-component-template"
    >
        <div class="flex gap-4">
            <div class="w-full">
                <!-- Address (Textarea field) -->
                <x-admin::form.control-group>
                    <x-admin::form.control-group.control
                        type="textarea"
                        ::name="attribute['code'] + '[address]'"
                        rows="10"
                        ::value="data ? data['address'] : ''"
                        :label="trans('admin::app.common.custom-attributes.address')"
                        ::rules="attribute.is_required ? 'required' : ''"
                    />

                    <x-admin::form.control-group.error ::name="attribute['code'] + '[address]'" />

                    <x-admin::form.control-group.error ::name="attribute['code'] + '.address'" />
                </x-admin::form.control-group>
            </div>

            <div class="grid w-full">
                <!-- Country Field -->
                <x-admin::form.control-group>
                    <x-admin::form.control-group.control
                        type="select"
                        ::name="attribute['code'] + '[country]'"
                        ::rules="attribute.is_required ? 'required' : ''"
                        :label="trans('admin::app.common.custom-attributes.country')"
                        v-model="country"
                    >
                        <option value="">@lang('admin::app.common.custom-attributes.select-country')</option>
                        
                        @foreach (core()->countries() as $country)
                            <option value="{{ $country->code }}">{{ $country->name }}</option>
                        @endforeach
                    </x-admin::form.control-group.control>

                    <x-admin::form.control-group.error ::name="attribute['code'] + '[country]'" />

                    <x-admin::form.control-group.error ::name="attribute['code'] + '.country'" />
                </x-admin::form.control-group>

                <!-- State Field -->
                <template v-if="haveStates()">
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.control
                            type="select"
                            ::name="attribute['code'] + '[state]'"
                            v-model="state"
                            :label="trans('admin::app.common.custom-attributes.state')"
                            ::rules="attribute.is_required ? 'required' : ''"
                        >
                            <option value="">@lang('admin::app.common.custom-attributes.select-state')</option>
                            
                            <option 
                                v-for='(state, index) in countryStates[country]' 
                                :value="state.code"
                            >
                                @{{ state.name }}
                            </option>
                        </x-admin::form.control-group.control>

                        <x-admin::form.control-group.error ::name="attribute['code'] + '[state]'" />

                        <x-admin::form.control-group.error ::name="attribute['code'] + '.state'" />
                    </x-admin::form.control-group>
                </template>

                <template v-else>
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.control
                            type="text"
                            ::name="attribute['code'] + '[state]'"
                            :placeholder="trans('admin::app.common.custom-attributes.state')"
                            :label="trans('admin::app.common.custom-attributes.state')"
                            ::rules="attribute.is_required ? 'required' : ''"
                            v-model="state"
                        >
                        </x-admin::form.control-group.control>
                        
                        <x-admin::form.control-group.error ::name="attribute['code'] + '[state]'" />

                        <x-admin::form.control-group.error ::name="attribute['code'] + '.state'" />
                    </x-admin::form.control-group>
                </template>

                <!-- City Field -->
                <x-admin::form.control-group>
                    <x-admin::form.control-group.control
                        type="text"
                        ::name="attribute['code'] + '[city]'"
                        ::value="data && data['city'] ? data['city'] : ''"
                        :placeholder="trans('admin::app.common.custom-attributes.city')"
                        :label="trans('admin::app.common.custom-attributes.city')"
                        ::rules="attribute.is_required ? 'required' : ''"
                    />

                    <x-admin::form.control-group.error ::name="attribute['code'] + '[city]'"/>

                    <x-admin::form.control-group.error ::name="attribute['code'] + '.city'" />
                </x-admin::form.control-group>

                <!-- Postcode Field -->
                <x-admin::form.control-group>
                    <x-admin::form.control-group.control
                        type="text"
                        ::name="attribute['code'] + '[postcode]'"
                        ::value="data &&  data['postcode'] ? data['postcode'] : ''"
                        :placeholder="trans('admin::app.common.custom-attributes.postcode')"
                        :label="trans('admin::app.common.custom-attributes.postcode')"
                        ::rules="attribute.is_required ? 'required' : ''"
                    />

                    <x-admin::form.control-group.error ::name="attribute['code'] + '[postcode]'" />

                    <x-admin::form.control-group.error ::name="attribute['code'] + '.postcode'" />
                </x-admin::form.control-group>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-address-component', {
            template: '#v-address-component-template',

            props: ['attribute', 'data'],

            data() {
                return {
                    country: this.data?.country || '',

                    state: this.data?.state || '',

                    countryStates: @json(core()->groupedStatesByCountries()),
                };
            },
            
            methods: {
                haveStates() {
                    /*
                    * The double negation operator is used to convert the value to a boolean.
                    * It ensures that the final result is a boolean value,
                    * true if the array has a length greater than 0, and otherwise false.
                    */
                    return !!this.countryStates[this.country]?.length;
                },
            }
        });
    </script>
@endPushOnce