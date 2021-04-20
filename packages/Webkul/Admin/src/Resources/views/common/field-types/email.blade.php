<email-component></email-component>

@push('scripts')

    <script type="text/x-template" id="email-component-template">
        <div class="emails-control">
            <div class="form-group input-group" v-for="(email, index) in emails" :class="[errors.has('{{ $attribute->code }}[' + index + '][value]') ? 'has-error' : '']">
                <input type="text" v-validate="'{{$validations}}'" class="control" :name="'{{ $attribute->code }}[' + index + '][value]'" v-model="email['value']" data-vv-as="&quot;{{ $attribute->name }}&quot;">

                <div class="input-group-append">
                    <select :name="'{{ $attribute->code }}[' + index + '][label]'" v-model="email['label']" class="control">
                        <option value="work">{{ __('admin::app.common.work') }}</option>
                        <option value="home">{{ __('admin::app.common.home') }}</option>
                    </select>
                </div>

                <i class="icon trash-icon" @click="removeEmail(email)"></i>

                <span class="control-error" v-if="errors.has('{{ $attribute->code }}[' + index + '][value]')">
                    @{{ errors.first('{!! $attribute->code !!}[' + index + '][value]') }}
                </span>
            </div>

            <a href @click.prevent="addEmail">+ add more</a>
        </div>
    </script>

    <script>
        Vue.component('email-component', {

            template: '#email-component-template',

            inject: ['$validator'],

            data: function () {
                return {
                    emails: [{
                        'value': '',
                        'label': 'work'
                    }],
                }
            },

            methods: {
                addEmail: function() {
                    this.emails.push({
                        'value': '',
                        'label': 'work'
                    })
                },

                removeEmail: function(email) {
                    const index = this.emails.indexOf(email);

                    Vue.delete(this.emails, index);
                }
            }
        });
    </script>
@endpush