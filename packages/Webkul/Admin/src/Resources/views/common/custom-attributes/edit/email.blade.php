@if (isset($attribute))
    <email-component
        :attribute='@json($attribute)'
        :validations="'{{$validations}}'"
        :data='@json(old($attribute->code) ?: $value)'
    ></email-component>
@endif

@once
    @push('scripts')

        <script type="text/x-template" id="email-component-template">
            <div class="emails-control">
                <div
                    class="form-group input-group"
                    v-for="(email, index) in emails"
                    :class="[errors.has('{!! $formScope ?? '' !!}' + attribute['code'] + '[' + index + '][value]') ? 'has-error' : '']"
                >
                    <input
                        type="text"
                        :name="attribute['code'] + '[' + index + '][value]'"
                        class="control"
                        v-model="email['value']"
                        v-validate="validations"
                        :data-vv-as="attribute['name']"
                    >

                    <div class="input-group-append">
                        <select :name="attribute['code'] + '[' + index + '][label]'" class="control" v-model="email['label']">
                            <option value="work">{{ __('admin::app.common.work') }}</option>
                            <option value="home">{{ __('admin::app.common.home') }}</option>
                        </select>
                    </div>

                    <i class="icon trash-icon" v-if="emails.length > 1" @click="removeEmail(email)"></i>

                    <span class="control-error" v-if="errors.has('{!! $formScope ?? '' !!}' + attribute['code'] + '[' + index + '][value]')">
                        @{{ errors.first('{!! $formScope ?? '' !!}' + attribute['code'] + '[' + index + '][value]') }}
                    </span>
                </div>

                <a class="add-more-link" href @click.prevent="addEmail">+ {{ __('admin::app.common.add_more') }}</a>
            </div>
        </script>

        <script>
            Vue.component('email-component', {
    
                template: '#email-component-template',
    
                props: ['validations', 'attribute', 'data'],
    
                inject: ['$validator'],
    
                data: function () {
                    return {
                        emails: this.data,
                    }
                },

                watch: { 
                    data: function(newVal, oldVal) {
                        if (JSON.stringify(newVal) !== JSON.stringify(oldVal)) {
                            this.emails = newVal || [{'value': '', 'label': 'work'}];
                        }
                    }
                },
    
                created: function() {
                    if (! this.emails || ! this.emails.length) {
                        this.emails = [{
                            'value': '',
                            'label': 'work'
                        }];
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
@endonce