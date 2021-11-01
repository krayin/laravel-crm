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
                <div
                    class="form-group input-group"
                    v-for="(contactNumber, index) in contactNumbers"
                    :class="[errors.has('{!! $formScope ?? '' !!}' + attribute['code'] + '[' + index + '][value]') ? 'has-error' : '']"
                >

                    <input
                        type="text"
                        :name="attribute['code'] + '[' + index + '][value]'"
                        class="control"
                        v-model="contactNumber['value']"
                        v-validate="validations ? validations + '|unique_contact_number' : 'unique_contact_number'"
                        :data-vv-as="attribute['name']"
                    />

                    <div class="input-group-append">
                        <select
                            :name="attribute['code'] + '[' + index + '][label]'"
                            class="control"
                            v-model="contactNumber['label']"
                        >
                            <option value="work">{{ __('admin::app.common.work') }}</option>
                            <option value="home">{{ __('admin::app.common.home') }}</option>
                        </select>
                    </div>

                    <i class="icon trash-icon" v-if="contactNumbers.length > 1" @click="removePhone(contactNumber)"></i>

                    <span class="control-error" v-if="errors.has('{!! $formScope ?? '' !!}' + attribute['code'] + '[' + index + '][value]')">
                        @{{ errors.first('{!! $formScope ?? '' !!}' + attribute['code'] + '[' + index + '][value]') }}
                    </span>
                </div>

                <a class="add-more-link" href @click.prevent="addPhone">+ {{ __('admin::app.common.add_more') }}</a>
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
                        if (JSON.stringify(newVal) !== JSON.stringify(oldVal)) {
                            this.contactNumbers = newVal || [{'value': '', 'label': 'work'}];
                        }
                    }
                },

                created: function() {
                    this.extendValidator();

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
                    },

                    extendValidator: function () {
                        this.$validator.extend('unique_contact_number', {
                            getMessage: (field) => '{!! __('admin::app.common.duplicate-value') !!}',

                            validate: (value) => {
                                let filteredContactNumbers = this.contactNumbers.filter((contactNumber) => {
                                    return contactNumber.value == value;
                                });

                                if (filteredContactNumbers.length > 1) {
                                    return false;
                                }

                                this.removeUniqueErrors();

                                return true;
                            }
                        });
                    },

                    isDuplicateExists: function () {
                        let contactNumbers = this.contactNumbers.map((contactNumber) => contactNumber.value);

                        return contactNumbers.some((contactNumber, index) => contactNumbers.indexOf(contactNumber) != index);
                    },

                    removeUniqueErrors: function () {
                        if (! this.isDuplicateExists()) {
                            this.errors
                                .items
                                .filter(error => error.rule === 'unique')
                                .map(error => error.id)
                                .forEach((id) => {
                                    this.errors.removeById(id);
                                });
                        }
                    }
                }
            });
        </script>

    @endpush
@endonce
