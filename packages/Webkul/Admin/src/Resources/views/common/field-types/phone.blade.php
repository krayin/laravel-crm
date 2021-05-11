@if (isset($attribute))
    <phone-component
        :attribute='@json($attribute)'
        :validations="'{{$validations}}'"
        :data='@json(old($attribute->code) ?: $value)'
    ></phone-component>
@endif

@once
    @push('scripts')

        <script type="text/x-template" id="phone-component-template">
            <div class="phone-control">
                <div class="form-group input-group" v-for="(contactNumber, index) in contactNumbers" :class="[errors.has(attribute['code'] + '[' + index + '][value]') ? 'has-error' : '']">
                    <input type="text" v-validate="validations" class="control" :name="attribute['code'] + '[' + index + '][value]'" v-model="contactNumber['value']" :data-vv-as="attribute['name']">

                    <div class="input-group-append">
                        <select :name="attribute['code'] + '[' + index + '][label]'" v-model="contactNumber['label']" class="control">
                            <option value="work">{{ __('admin::app.common.work') }}</option>
                            <option value="home">{{ __('admin::app.common.home') }}</option>
                        </select>
                    </div>

                    <i class="icon trash-icon" @click="removePhone(contactNumber)"></i>

                    <span class="control-error" v-if="errors.has(attribute['code'] + '[' + index + '][value]')">
                        @{{ errors.first(attribute['code'] + '[' + index + '][value]') }}
                    </span>
                </div>

                <a href @click.prevent="addPhone">+ add more</a>
            </div>
        </script>

        <script>
            Vue.component('phone-component', {

                template: '#phone-component-template',

                props: ['validations', 'attribute', 'data'],

                inject: ['$validator'],

                data: function () {
                    return {
                        contactNumbers: this.data,
                    }
                },

                watch: { 
                    data: function(newVal, oldVal) {
                        this.contactNumbers = newVal ? newVal : [{'value': '', 'label': 'work'}];
                    }
                },

                created: function() {
                    if (! this.contactNumbers || ! this.contactNumbers.length) {
                        this.contactNumbers = [{
                            'value': '',
                            'label': 'work'
                        }];
                    }
                },

                methods: {
                    addPhone: function() {
                        this.contactNumbers.push({
                            'value': '',
                            'label': 'work'
                        })
                    },

                    removePhone: function(contactNumber) {
                        const index = this.contactNumbers.indexOf(contactNumber);

                        Vue.delete(this.contactNumbers, index);
                    }
                }
            });
        </script>

    @endpush
@endonce
