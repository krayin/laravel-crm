<phone-component></phone-component>

@push('scripts')

    <script type="text/x-template" id="phone-component-template">
        <div class="emails-control">
            <div class="form-group input-group" v-for="(contactNumber, index) in contactNumbers" :class="[errors.has('{{ $attribute->code }}[' + index + '][value]') ? 'has-error' : '']">
                <input type="text" v-validate="'{{$validations}}'" class="control" :name="'{{ $attribute->code }}[' + index + '][value]'" v-model="contactNumber['value']" :data-vv-as="&quot;{{ $attribute->name }}&quot;">

                <div class="input-group-append">
                    <select :name="'{{ $attribute->code }}[' + index + '][label]'" v-model="contactNumber['label']" class="control">
                        <option value="work">{{ __('admin::app.common.work') }}</option>
                        <option value="home">{{ __('admin::app.common.home') }}</option>
                    </select>
                </div>

                <i class="icon trash-icon" @click="removePhone(contactNumber)"></i>

                <span class="control-error" v-if="errors.has('{{ $attribute->code }}[' + index + '][value]')">
                    @{{ errors.first('{!! $attribute->code !!}[' + index + '][value]') }}
                </span>
            </div>

            <a href @click.prevent="addPhone">+ add more</a>
        </div>
    </script>

    <script>
        Vue.component('phone-component', {

            template: '#phone-component-template',

            inject: ['$validator'],

            data: function () {
                return {
                    contactNumbers: [{
                        'value': '',
                        'label': 'work'
                    }],
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